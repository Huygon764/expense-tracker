<?php

namespace App\Services;

use App\Jobs\SendBudgetAlertEmailJob;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Notification;
use Carbon\Carbon;

class BudgetAlertService
{
    private const THRESHOLDS = [
        50 => 0.5,
        80 => 0.8,
        100 => 1.0,
    ];

    private const MESSAGES_MONTHLY = [
        50 => 'Bạn đã dùng 50% ngân sách tháng này.',
        80 => 'Bạn đã dùng 80% ngân sách tháng này.',
        100 => 'Bạn đã vượt ngân sách tháng này.',
    ];

    private const MESSAGES_WEEKLY = [
        50 => 'Bạn đã dùng 50% ngân sách tuần này.',
        80 => 'Bạn đã dùng 80% ngân sách tuần này.',
        100 => 'Bạn đã vượt ngân sách tuần này.',
    ];

    public function checkAndNotify(int $userId): void
    {
        $budgets = Budget::where('user_id', $userId)->get();

        /** @var Budget $budget */
        foreach ($budgets as $budget) {
            $this->checkBudget($budget);
        }
    }

    private function checkBudget(Budget $budget): void
    {
        $amount = (float) $budget->amount;
        if ($amount <= 0) {
            return;
        }

        $spent = $budget->getSpentInCurrentPeriod();
        $ratio = $spent / $amount;

        $isWeekly = $budget->type === 'weekly';
        $messages = $isWeekly ? self::MESSAGES_WEEKLY : self::MESSAGES_MONTHLY;

        if ($isWeekly) {
            $periodStart = Carbon::now()->startOfWeek();
            $periodEnd = Carbon::now()->endOfWeek();
        } else {
            $periodStart = Carbon::now()->startOfMonth();
            $periodEnd = Carbon::now()->endOfMonth();
        }

        foreach (self::THRESHOLDS as $level => $threshold) {
            if ($ratio < $threshold) {
                continue;
            }

            $type = $isWeekly ? "budget_weekly_{$level}" : "budget_{$level}";

            $exists = Notification::where('user_id', $budget->user_id)
                ->where('type', $type)
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->exists();

            if ($exists) {
                continue;
            }

            $notification = Notification::create([
                'user_id' => $budget->user_id,
                'type' => $type,
                'message' => $messages[$level],
                'is_read' => false,
            ]);

            SendBudgetAlertEmailJob::dispatch($notification);
        }
    }
}
