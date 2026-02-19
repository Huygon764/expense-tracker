<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\RecurringExpense;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateRecurringExpensesCommand extends Command
{
    protected $signature = 'recurring:create-expenses {--dry-run : List what would be created without writing to the database}';

    protected $description = 'Create expenses from active recurring expenses that match today\'s schedule';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $today = Carbon::today()->format('Y-m-d');

        $recurring = RecurringExpense::active()->get()->filter(fn (RecurringExpense $r) => $r->shouldRunToday());
        $created = 0;

        foreach ($recurring as $r) {
            if (Expense::where('recurring_expense_id', $r->id)->where('date', $today)->exists()) {
                continue;
            }
            if ($r->category_id === null) {
                $this->warn("Recurring \"{$r->title}\" (id {$r->id}) has no category; expense requires a category. Skipped.");
                continue;
            }
            if ($dryRun) {
                $this->line("Would create expense: {$r->title}, amount {$r->amount}, date {$today}");
                $created++;
                continue;
            }
            try {
                Expense::create([
                    'user_id' => $r->user_id,
                    'category_id' => $r->category_id,
                    'amount' => $r->amount,
                    'note' => $r->title,
                    'date' => $today,
                    'recurring_expense_id' => $r->id,
                ]);
                $created++;
            } catch (\Throwable $e) {
                $this->error("Failed to create expense for recurring \"{$r->title}\" (id {$r->id}): {$e->getMessage()}");
            }
        }

        if ($dryRun) {
            $this->info("[Dry run] Would create {$created} expense(s).");
        } else {
            $this->info("Created {$created} expense(s).");
        }

        return self::SUCCESS;
    }
}
