<?php

namespace App\Console\Commands;

use App\Services\PointService;
use Illuminate\Console\Command;

class ExpirePoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'points:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire old points based on expiration date';

    protected $pointService;

    public function __construct(PointService $pointService)
    {
        parent::__construct();
        $this->pointService = $pointService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting point expiration process...');

        $count = $this->pointService->expirePoints();

        $this->info("Successfully expired points for {$count} ledger entries.");

        return Command::SUCCESS;
    }
}
