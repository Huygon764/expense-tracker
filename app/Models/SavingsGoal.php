<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class SavingsGoal extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'deadline',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'deadline' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Current amount (savings) from goal created_at to end of current month.
     * Uses pre-set value if set by controller; otherwise computes (may trigger queries).
     */
    public function getCurrentAmountAttribute(): float
    {
        if (array_key_exists('current_amount', $this->attributes)) {
            return (float) $this->attributes['current_amount'];
        }

        return $this->computeCurrentAmountFromMap($this->getExpensesByMonthForGoal());
    }

    /**
     * Compute current amount using pre-fetched expenses by month (key "Y-m", value sum).
     * Used by controller to avoid N+1.
     */
    public function computeCurrentAmountFromMap(Collection $expensesByMonth): float
    {
        $user = $this->user;
        $monthlyIncome = $user && $user->monthly_income !== null ? (float) $user->monthly_income : 0.0;
        $startMonth = $this->created_at->copy()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();
        $total = 0.0;
        $current = $startMonth->copy();

        while ($current <= $endMonth) {
            $key = $current->format('Y-n');
            $monthExpenses = (float) ($expensesByMonth->get($key)?->total ?? 0);
            $total += $monthlyIncome - $monthExpenses;
            $current->addMonth();
        }

        return round($total, 2);
    }

    /**
     * Get expenses grouped by year-month for this goal's range (for accessor when not pre-set).
     */
    private function getExpensesByMonthForGoal(): Collection
    {
        $userId = $this->user_id;
        $start = $this->created_at->copy()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        return Expense::where('user_id', $userId)
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->get()
            ->keyBy(fn ($e) => "{$e->year}-{$e->month}");
    }

    public function getMonthlyNeededAttribute(): ?float
    {
        $current = $this->current_amount;
        $target = (float) $this->target_amount;
        $remaining = $target - $current;
        if ($remaining <= 0) {
            return 0.0;
        }
        $monthsRemaining = max(0, (int) ceil(Carbon::now()->floatDiffInMonths($this->deadline, false)));
        if ($monthsRemaining <= 0) {
            return null;
        }

        return round($remaining / $monthsRemaining, 2);
    }

    public function getProgressPercentageAttribute(): float
    {
        $target = (float) $this->target_amount;
        if ($target <= 0) {
            return 0.0;
        }
        $current = $this->current_amount;

        return min(100.0, ($current / $target) * 100);
    }

    /**
     * Status: on_track, behind, achieved, expired.
     */
    public function getStatusAttribute(): string
    {
        $current = $this->current_amount;
        $target = (float) $this->target_amount;
        $today = Carbon::today();

        if ($current >= $target) {
            return 'achieved';
        }
        if ($this->deadline->lt($today)) {
            return 'expired';
        }

        $created = $this->created_at->copy()->startOfMonth();
        $deadlineStart = $this->deadline->copy()->startOfMonth();
        $totalMonths = max(1, $created->diffInMonths($deadlineStart) + 1);
        $elapsedMonths = $created->diffInMonths(Carbon::now()->copy()->endOfMonth()) + 1;
        $elapsedMonths = min($elapsedMonths, $totalMonths);
        $expectedByNow = $target * ($elapsedMonths / $totalMonths);

        return $current >= $expectedByNow ? 'on_track' : 'behind';
    }
}
