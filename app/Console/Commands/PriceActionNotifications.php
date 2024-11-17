<?php

namespace App\Console\Commands;

use App\Exceptions\MissingLatestPricesException;
use App\Scheduled\PriceActionNotificationGenerator;
use Illuminate\Console\Command;

class PriceActionNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:price-action-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Triggers the price action notifications';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            PriceActionNotificationGenerator::generateEvents();
        } catch (MissingLatestPricesException $e) {
            $this->error('Price action notifications failed: missing latest prices');
        }
    }
}
