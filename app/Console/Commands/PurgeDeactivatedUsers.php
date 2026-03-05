<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PurgeDeactivatedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:purge-deactivated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete deactivated users whose grace period has passed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deleted = 0;

        User::query()
            ->whereNotNull('deactivated_at')
            ->whereNotNull('scheduled_for_deletion_at')
            ->where('scheduled_for_deletion_at', '<=', now(config('app.timezone')))
            ->chunkById(100, function ($users) use (&$deleted): void {
                foreach ($users as $user) {
                    $user->delete();
                    $deleted++;
                }
            });

        $this->info("Purged {$deleted} deactivated user account(s).");

        return Command::SUCCESS;
    }
}
