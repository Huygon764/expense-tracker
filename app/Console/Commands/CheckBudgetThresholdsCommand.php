<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BudgetAlertService;
use Illuminate\Console\Command;

class CheckBudgetThresholdsCommand extends Command
{
    protected $signature = 'budget:check-thresholds';

    protected $description = 'Check budget thresholds (50%, 80%, 100%) and create notifications + dispatch email jobs';

    public function handle(BudgetAlertService $service): int
    {
        $users = User::whereHas('budgets')->get();

        $created = 0;

        /** @var \App\Models\User $user */
        foreach ($users as $user) {
            $before = $user->notifications()->count();
            $service->checkAndNotify($user->id);
            $created += $user->notifications()->count() - $before;
        }

        $this->info("Created {$created} notifications.");

        return self::SUCCESS;
    }
}
