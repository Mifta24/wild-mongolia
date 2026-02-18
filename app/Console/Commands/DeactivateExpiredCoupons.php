<?php

namespace App\Console\Commands;

use App\Services\CouponService;
use Illuminate\Console\Command;

class DeactivateExpiredCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate expired coupons';

    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        parent::__construct();
        $this->couponService = $couponService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Deactivating expired coupons...');

        $count = $this->couponService->deactivateExpiredCoupons();

        $this->info("Successfully deactivated {$count} expired coupons.");

        return Command::SUCCESS;
    }
}
