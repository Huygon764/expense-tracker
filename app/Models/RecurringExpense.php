<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringExpense extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'amount',
        'type',
        'day_of_week',
        'day_of_month',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function shouldRunToday(): bool
    {
        $today = Carbon::today();
        if ($this->type === 'weekly') {
            return (int) $today->dayOfWeek === (int) $this->day_of_week;
        }
        if ($this->type === 'monthly') {
            return $today->day === (int) $this->day_of_month
                || ((int) $this->day_of_month > $today->daysInMonth && $today->day === $today->daysInMonth);
        }
        return false;
    }

    public function getNextRunDate(): Carbon
    {
        $today = Carbon::today();
        if ($this->type === 'weekly') {
            $dayOfWeek = (int) $this->day_of_week;
            $current = $today->copy();
            for ($i = 0; $i <= 7; $i++) {
                if ($current->dayOfWeek === $dayOfWeek) {
                    return $current;
                }
                $current->addDay();
            }
        }
        if ($this->type === 'monthly') {
            $day = (int) $this->day_of_month;
            $thisMonth = $today->copy()->startOfMonth();
            $runThisMonth = $day <= $thisMonth->daysInMonth
                ? $thisMonth->copy()->day($day)
                : $thisMonth->copy()->endOfMonth();
            if ($runThisMonth->gte($today)) {
                return $runThisMonth;
            }
            $nextMonth = $today->copy()->addMonth()->startOfMonth();
            return $day <= $nextMonth->daysInMonth
                ? $nextMonth->copy()->day($day)
                : $nextMonth->copy()->endOfMonth();
        }
        return $today;
    }
}
