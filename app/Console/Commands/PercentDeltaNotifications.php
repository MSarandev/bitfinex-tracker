<?php

namespace App\Console\Commands;

use App\Scheduled\PercentDeltaNotificationGenerator;
use Exception;
use Illuminate\Console\Command;

class PercentDeltaNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:percent-change-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Triggers the percent change notifications';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            PercentDeltaNotificationGenerator::generateEvents();
        } catch (Exception $e) {
            $this->error('Percent change notifications failed: missing latest prices');
        }
    }
}
