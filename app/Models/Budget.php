<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
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

    /**
     * Sum of expenses in the current period (week or month) for this budget.
     */
    public function getSpentInCurrentPeriod(): float
    {
        $today = Carbon::today();
        if ($this->type === 'weekly') {
            $start = $today->copy()->startOfWeek();
            $end = $today->copy()->endOfWeek();
        } else {
            $start = $today->copy()->startOfMonth();
            $end = $today->copy()->endOfMonth();
        }

        $query = Expense::where('user_id', $this->user_id)
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')]);

        if ($this->category_id !== null) {
            $query->where('category_id', $this->category_id);
        }

        return (float) $query->sum('amount');
    }
}
