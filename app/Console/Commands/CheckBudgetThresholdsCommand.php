<?php

namespace App\Console\Commands;

use App\Jobs\SendBudgetAlertEmailJob;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckBudgetThresholdsCommand extends Command
{
    protected $signature = 'budget:check-thresholds';

    protected $description = 'Check budget thresholds (50%, 80%, 100%) and create notifications + dispatch email jobs';

    public function handle(): int
    {
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        $users = User::whereHas('budgets', function ($q) {
            $q->whereNull('category_id')->where('type', 'monthly');
        })->get();

        $thresholds = [
            'budget_50' => ['ratio' => 0.5, 'message' => 'Bạn đã dùng 50% ngân sách tháng này.'],
            'budget_80' => ['ratio' => 0.8, 'message' => 'Bạn đã dùng 80% ngân sách tháng này.'],
            'budget_100' => ['ratio' => 1.0, 'message' => 'Bạn đã vượt ngân sách tháng này.'],
        ];

        $created = 0;

        foreach ($users as $user) {
            $budget = Budget::where('user_id', $user->id)
                ->whereNull('category_id')
                ->where('type', 'monthly')
                ->first();

            if (! $budget || (float) $budget->amount <= 0) {
                continue;
            }

            $spent = (float) Expense::where('user_id', $user->id)
                ->whereBetween('date', [$startMonth, $endMonth])
                ->sum('amount');

            $ratio = $spent / (float) $budget->amount;

            foreach ($thresholds as $type => $config) {
                if ($ratio < $config['ratio']) {
                    continue;
                }

                $exists = Notification::where('user_id', $user->id)
                    ->where('type', $type)
                    ->whereBetween('created_at', [$startMonth, $endMonth])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'message' => $config['message'],
                    'is_read' => false,
                ]);

                SendBudgetAlertEmailJob::dispatch($notification);
                $created++;
            }
        }

        $this->info("Created {$created} notifications.");

        return self::SUCCESS;
    }
}
