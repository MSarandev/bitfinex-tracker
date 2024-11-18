<?php

namespace App\Scheduled;

use App\Exceptions\MissingLatestPricesException;
use App\Models\Ticker;
use App\Models\User;
use App\Notifications\PriceActionNotification;
use App\Repositories\PriceActionsRepo;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PriceActionNotificationGenerator
{
    /**
     * @return void
     * @throws MissingLatestPricesException
     */
    public static function generateEvents(): void
    {
        $latestPrices = self::getLatestPrices();

        if (!$latestPrices) {
            Log::error('No latest prices found');
            throw new MissingLatestPricesException();
        }

        $priceActions = self::getAllPriceActions();

        if ($priceActions->isEmpty()) {
            Log::info('No price actions found');
            return;
        }


        foreach ($priceActions as $priceAction) {
            $symbol = $priceAction->symbol;
            $price = $latestPrices[$symbol]->bid;

            if ($priceAction->isHit($price)) {
                Log::info(sprintf('Price action hit for symbol %s, user %d', $symbol, $priceAction->user_id));

                $userConfig = sprintf(
                    'Symbol: "%s", Trigger: "%s", Price: "%s"',
                    $priceAction->symbol,
                    $priceAction->trigger,
                    $priceAction->price
                );

                try {
                    $user = self::findUser($priceAction->user_id);
                } catch (Exception|QueryException $e) {
                    Log::error(sprintf('User with id %d not found', $priceAction->user_id));
                    continue;
                }

                $user->notifyNow(new PriceActionNotification($userConfig));
            }
        }
    }

    /**
     * @return array
     */
    private static function getLatestPrices(): array
    {
        $allowedSybmols = config('bitfinex.symbols');
        $cachePrefix = config('bitfinex.cache_prefix');
        $latestPrices = [];

        foreach ($allowedSybmols as $symbol) {
            $cacheKey = $cachePrefix.$symbol;

            $cacheValue = Cache::get($cacheKey);

            if ($cacheValue) {
                $decoded = json_decode($cacheValue, true);


                $latestPrices[$symbol] = new Ticker($decoded);
            }
        }

        return $latestPrices;
    }

    /**
     * @return Collection
     */
    private static function getAllPriceActions(): Collection
    {
        return PriceActionsRepo::getAllActiveStatic();
    }

    /**
     * @param  int  $userId
     * @return User
     */
    private static function findUser(int $userId): User
    {
        return User::find($userId);
    }
}
