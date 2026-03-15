<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function deposits(): HasMany
    {
        return $this->hasMany(SavingsDeposit::class);
    }

    public function getCurrentAmountAttribute(): float
    {
        return (float) $this->deposits()->sum('amount');
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

        return min(100.0, max(0, ($this->current_amount / $target) * 100));
    }

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
