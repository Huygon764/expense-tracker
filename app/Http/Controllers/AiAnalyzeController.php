<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Services\GeminiService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AiAnalyzeController extends Controller
{
    private const CACHE_TTL_SECONDS = 3600;

    private const FALLBACK_TIPS = 'Một số gợi ý chung: Theo dõi chi tiêu hàng ngày, set ngân sách cho từng danh mục, ưu tiên khoản cần thiết trước.';

    public function analyze(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $apiKey = config('services.gemini.api_key', '');
        if ($apiKey === '') {
            return response()->json([
                'error' => 'Chưa cấu hình API key.',
                'tips' => self::FALLBACK_TIPS,
            ], 400);
        }

        $aggregated = $this->buildAggregatedData($user->id);
        $prompt = $this->buildPrompt($aggregated);

        try {
            $cacheKey = 'ai_analysis_' . $user->id . '_' . now()->format('Y-m-d');
            $analysis = Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($prompt) {
                $service = GeminiService::fromConfig();
                return $service->analyze($prompt);
            });

            return response()->json(['analysis' => $analysis]);
        } catch (\Exception $e) {
            Log::error('Gemini API error', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Không thể phân tích lúc này.',
                'tips' => self::FALLBACK_TIPS,
            ], 500);
        }
    }

    /**
     * @return array{by_category: array<array{category: string, total: float}>, spent_this_month: float, spent_last_month: float, last_7_days: array<array{date: string, total: float}>, budget_month: float|null, budget_week: float|null, spent_this_week: float|null}
     */
    private function buildAggregatedData(int $userId): array
    {
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();
        $lastMonthStart = Carbon::now()->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->copy()->subMonth()->endOfMonth();

        $spentThisMonth = (float) Expense::where('user_id', $userId)
            ->whereBetween('date', [$startMonth, $endMonth])
            ->sum('amount');

        $spentLastMonth = (float) Expense::where('user_id', $userId)
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $pieRows = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startMonth, $endMonth])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->get();

        $categoryIds = $pieRows->pluck('category_id')->unique()->filter()->values()->all();
        $categories = $categoryIds ? Category::whereIn('id', $categoryIds)->get()->keyBy('id') : collect();

        $byCategory = $pieRows->map(function ($row) use ($categories) {
            return [
                'category' => $row->category_id
                    ? ($categories->get($row->category_id)?->name ?? 'Khác')
                    : 'Khác',
                'total' => (float) $row->total,
            ];
        })->values()->all();

        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $total = (float) Expense::where('user_id', $userId)
                ->where('date', $d->toDateString())
                ->sum('amount');
            $last7Days[] = ['date' => $d->format('Y-m-d'), 'total' => $total];
        }

        $budgetMonthly = Budget::where('user_id', $userId)
            ->whereNull('category_id')
            ->where('type', 'monthly')
            ->first();
        $budgetMonth = $budgetMonthly ? (float) $budgetMonthly->amount : null;

        $budgetWeekly = Budget::where('user_id', $userId)
            ->whereNull('category_id')
            ->where('type', 'weekly')
            ->first();
        $budgetWeek = $budgetWeekly ? (float) $budgetWeekly->amount : null;
        $spentThisWeek = null;
        if ($budgetWeekly) {
            $startWeek = Carbon::now()->startOfWeek();
            $endWeek = Carbon::now()->endOfWeek();
            $spentThisWeek = (float) Expense::where('user_id', $userId)
                ->whereBetween('date', [$startWeek, $endWeek])
                ->sum('amount');
        }

        return [
            'by_category' => $byCategory,
            'spent_this_month' => $spentThisMonth,
            'spent_last_month' => $spentLastMonth,
            'last_7_days' => $last7Days,
            'budget_month' => $budgetMonth,
            'budget_week' => $budgetWeek,
            'spent_this_week' => $spentThisWeek,
        ];
    }

    private function buildPrompt(array $data): string
    {
        $lines = [
            'Bạn là trợ lý phân tích tài chính cá nhân.',
            '',
            'Đây là dữ liệu chi tiêu tổng hợp (không có thông tin cá nhân):',
            '- Chi theo category (tháng này): ' . json_encode($data['by_category']),
            '- Tổng tháng này: ' . $data['spent_this_month'] . ', tổng tháng trước: ' . $data['spent_last_month'],
            '- 7 ngày gần nhất (theo ngày): ' . json_encode($data['last_7_days']),
        ];

        if ($data['budget_month'] !== null) {
            $pct = $data['budget_month'] > 0
                ? round($data['spent_this_month'] / $data['budget_month'] * 100)
                : 0;
            $lines[] = '- Ngân sách tháng: ' . $data['budget_month'] . ', đã chi: ' . $data['spent_this_month'] . ' (' . $pct . '%)';
        } else {
            $lines[] = '- Chưa set ngân sách tháng.';
        }

        if ($data['budget_week'] !== null && $data['spent_this_week'] !== null) {
            $weekPct = $data['budget_week'] > 0
                ? round($data['spent_this_week'] / $data['budget_week'] * 100)
                : 0;
            $lines[] = '- Ngân sách tuần: ' . $data['budget_week'] . ', đã chi tuần: ' . $data['spent_this_week'] . ' (' . $weekPct . '%)';
        }

        $lines[] = '';
        $lines[] = 'Nếu không đủ dữ liệu thì nói ngắn gọn và gợi ý thêm dữ liệu.';
        $lines[] = '';
        $lines[] = 'Trả lời **chỉ** theo format sau, bằng tiếng Việt:';
        $lines[] = '## Nhận xét';
        $lines[] = '[nhận xét thói quen chi tiêu]';
        $lines[] = '## Gợi ý tiết kiệm';
        $lines[] = '[gợi ý]';
        $lines[] = '## Dự đoán';
        $lines[] = '[dự đoán chi tiêu cuối tháng]';

        return implode("\n", $lines);
    }
}
