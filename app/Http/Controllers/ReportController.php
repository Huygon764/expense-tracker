<?php

namespace App\Http\Controllers;

use App\Exports\ExpensesReportExport;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    /**
     * @return array{total: float, by_category: array<array{category_name: string, total: float}>, expenses: \Illuminate\Database\Eloquent\Collection, date_from: string, date_to: string}
     */
    private function buildReportData(int $userId, string $dateFrom, string $dateTo): array
    {
        $expenses = Expense::where('user_id', $userId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->with('category')
            ->orderBy('date', 'desc')
            ->get();

        $total = (float) $expenses->sum('amount');

        $byCategory = $expenses->groupBy('category_id')->map(function ($items, $categoryId) {
            $name = $items->first()->category?->name ?? 'Khác';
            return [
                'category_name' => $name,
                'total' => (float) $items->sum('amount'),
            ];
        })->values()->sortByDesc('total')->values()->all();

        return [
            'total' => $total,
            'by_category' => $byCategory,
            'expenses' => $expenses,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];
    }

    /**
     * @return array{0: string, 1: string}|RedirectResponse
     */
    private function validateDateRange(Request $request): array|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
        ], [], [
            'date_from' => 'Từ ngày',
            'date_to' => 'Đến ngày',
        ]);

        if ($validator->fails()) {
            return redirect()->route('reports.index')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        return [$validated['date_from'], $validated['date_to']];
    }

    public function pdf(Request $request): Response|RedirectResponse
    {
        $range = $this->validateDateRange($request);
        if ($range instanceof RedirectResponse) {
            return $range;
        }
        [$dateFrom, $dateTo] = $range;
        $data = $this->buildReportData(Auth::id(), $dateFrom, $dateTo);

        $pdf = Pdf::loadView('reports.pdf', $data);

        $filename = 'report-' . $dateFrom . '-' . $dateTo . '.pdf';

        return $pdf->download($filename);
    }

    public function excel(Request $request): BinaryFileResponse|RedirectResponse
    {
        $range = $this->validateDateRange($request);
        if ($range instanceof RedirectResponse) {
            return $range;
        }
        [$dateFrom, $dateTo] = $range;
        $data = $this->buildReportData(Auth::id(), $dateFrom, $dateTo);

        $filename = 'report-' . $dateFrom . '-' . $dateTo . '.xlsx';

        return Excel::download(new ExpensesReportExport($data), $filename);
    }
}
