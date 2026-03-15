<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'user_id',
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

        return (float) Expense::where('user_id', $this->user_id)
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->sum('amount');
    }
}
