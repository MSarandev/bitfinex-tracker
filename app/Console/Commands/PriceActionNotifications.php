<?php

namespace App\Console\Commands;

use App\Exceptions\MissingLatestPricesException;
use App\Scheduled\PriceActionNotificationGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Command description';

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
