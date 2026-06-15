<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Models\SavingsDeposit;
use App\Models\SavingsGoal;
use App\Models\User;
use App\Services\BudgetAlertService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds a rich demo account (demo@gmail.com) with ~3 months of realistic
 * Vietnamese spending data so the dashboard, charts and AI analysis look
 * good during a demo. Run with: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    private const EMAIL = 'demo@gmail.com';

    private const MONTHS_BACK = 3;

    /**
     * Spending profile per category (amounts in VND).
     * chance = probability of at least one expense on a given day.
     */
    private const PROFILES = [
        'Food'          => ['chance' => 0.85, 'max_per_day' => 2, 'min' => 25000,  'max' => 150000],
        'Transport'     => ['chance' => 0.70, 'max_per_day' => 1, 'min' => 15000,  'max' => 70000],
        'Shopping'      => ['chance' => 0.15, 'max_per_day' => 1, 'min' => 150000, 'max' => 900000],
        'Entertainment' => ['chance' => 0.20, 'max_per_day' => 1, 'min' => 50000,  'max' => 350000],
        'Health'        => ['chance' => 0.08, 'max_per_day' => 1, 'min' => 80000,  'max' => 600000],
        'Other'         => ['chance' => 0.10, 'max_per_day' => 1, 'min' => 50000,  'max' => 400000],
    ];

    public function run(): void
    {
        // Deterministic randomness so the demo data is reproducible.
        mt_srand(42);

        // Re-running is idempotent: deleting the user cascades all their data.
        User::where('email', self::EMAIL)->delete();

        $user = User::create([
            'name' => 'Demo User',
            'email' => self::EMAIL,
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_notification' => true,
            'onboarding_completed_at' => now(),
        ]);

        $categories = $this->seedCategories($user->id);
        $this->seedBudgets($user->id);
        $this->seedRecurring($user->id, $categories);
        $this->seedExpenses($user->id, $categories);
        $this->seedSavings($user->id);

        // Generate authentic budget notifications via the real service.
        app(BudgetAlertService::class)->checkAndNotify($user->id);

        $this->command->info('Demo account ready: ' . self::EMAIL . ' / password');
    }

    /**
     * @return array<string, Category> keyed by category name
     */
    private function seedCategories(int $userId): array
    {
        $categories = [];
        foreach (config('onboarding.default_categories', []) as $item) {
            $categories[$item['name']] = Category::create([
                'user_id' => $userId,
                'name' => $item['name'],
                'icon' => $item['icon'] ?? null,
                'color' => $item['color'] ?? null,
            ]);
        }

        return $categories;
    }

    private function seedBudgets(int $userId): void
    {
        Budget::create(['user_id' => $userId, 'amount' => 12000000, 'type' => 'monthly']);
        Budget::create(['user_id' => $userId, 'amount' => 2000000,  'type' => 'weekly']);
    }

    /**
     * @param array<string, Category> $categories
     */
    private function seedRecurring(int $userId, array $categories): void
    {
        RecurringExpense::create([
            'user_id' => $userId,
            'category_id' => $categories['Bills']->id,
            'title' => 'Tiền thuê nhà',
            'amount' => 3500000,
            'type' => 'monthly',
            'day_of_month' => 1,
            'is_active' => true,
        ]);

        RecurringExpense::create([
            'user_id' => $userId,
            'category_id' => $categories['Entertainment']->id,
            'title' => 'Netflix',
            'amount' => 220000,
            'type' => 'monthly',
            'day_of_month' => 5,
            'is_active' => true,
        ]);
    }

    /**
     * @param array<string, Category> $categories
     */
    private function seedExpenses(int $userId, array $categories): void
    {
        $start = Carbon::today()->subMonths(self::MONTHS_BACK)->startOfDay();
        $end = Carbon::today();

        $notes = [
            'Food'          => ['Ăn trưa', 'Cà phê', 'Ăn tối', 'Trà sữa', 'Đi chợ'],
            'Transport'     => ['Xăng xe', 'Grab', 'Gửi xe', 'Vé bus'],
            'Shopping'      => ['Quần áo', 'Đồ gia dụng', 'Giày dép', 'Mỹ phẩm'],
            'Entertainment' => ['Xem phim', 'Game', 'Sách', 'Nhạc'],
            'Health'        => ['Thuốc', 'Khám bệnh', 'Vitamin'],
            'Other'         => ['Quà tặng', 'Linh tinh', 'Sửa đồ'],
        ];

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            foreach (self::PROFILES as $name => $p) {
                if (mt_rand(1, 100) > $p['chance'] * 100) {
                    continue;
                }

                $count = mt_rand(1, $p['max_per_day']);
                for ($i = 0; $i < $count; $i++) {
                    Expense::create([
                        'user_id' => $userId,
                        'category_id' => $categories[$name]->id,
                        'amount' => $this->roundAmount(mt_rand($p['min'], $p['max'])),
                        'note' => $notes[$name][array_rand($notes[$name])],
                        'date' => $day->toDateString(),
                    ]);
                }
            }
        }
    }

    private function seedSavings(int $userId): void
    {
        $laptop = SavingsGoal::create([
            'user_id' => $userId,
            'name' => 'Laptop mới',
            'target_amount' => 25000000,
            'deadline' => Carbon::today()->addMonths(4)->toDateString(),
        ]);
        foreach ([4000000, 3500000, 3000000] as $offset => $amount) {
            SavingsDeposit::create([
                'savings_goal_id' => $laptop->id,
                'amount' => $amount,
                'note' => 'Tiết kiệm hàng tháng',
                'date' => Carbon::today()->subMonths(2 - $offset)->toDateString(),
            ]);
        }

        $trip = SavingsGoal::create([
            'user_id' => $userId,
            'name' => 'Du lịch Đà Nẵng',
            'target_amount' => 8000000,
            'deadline' => Carbon::today()->addMonths(2)->toDateString(),
        ]);
        foreach ([2500000, 2500000] as $offset => $amount) {
            SavingsDeposit::create([
                'savings_goal_id' => $trip->id,
                'amount' => $amount,
                'note' => 'Góp quỹ du lịch',
                'date' => Carbon::today()->subMonths(1 - $offset)->toDateString(),
            ]);
        }
    }

    /**
     * Round to the nearest 1,000 VND so amounts look natural.
     */
    private function roundAmount(int $amount): int
    {
        return (int) (round($amount / 1000) * 1000);
    }
}
