<?php

namespace App\Scheduled;

use App\Exceptions\ExternalApiCallNotSuccessfulException;
use App\Exceptions\ExternalApiNotHealthyException;
use App\Helpers\ApiWrapper;
use App\Models\Dtos\PercentDeltaEvaluationDto;
use App\Models\TickerHistorical;
use App\Models\User;
use App\Notifications\PercentDeltaNotification;
use App\Repositories\PercentDeltaRepo;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class PercentDeltaNotificationGenerator
{
    /**
     * @return void
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public static function generateEvents(): void
    {
        $allDeltas = self::getAllDeltas();

        if ($allDeltas->isEmpty()) {
            return;
        }

        Log::info(sprintf('Checking for percent change notifications for %d records', $allDeltas->count()));

        $dataPerLongestPeriod = self::getDataPerLongestPeriod($allDeltas);

        $notifiable = self::determineIfDeltaIsHit($dataPerLongestPeriod, $allDeltas);

        foreach ($notifiable as $percentDelta) {
            Log::info(sprintf(
                    'Percent change hit for symbol %s, user %d',
                    $percentDelta->symbol,
                    $percentDelta->user_id)
            );

            try {
                $user = self::findUser($percentDelta->user_id);
            } catch (Exception $e) {
                Log::error(sprintf('User with id %d not found', $percentDelta->user_id));
                continue;
            }

            $userConfig = sprintf(
                'Symbol: "%s", Change of: "%s", Timeframe: "%s%s"',
                $percentDelta->symbol,
                $percentDelta->percent_change,
                $percentDelta->timeframe_flag,
                $percentDelta->timeframe_value,
            );

            $user->notifyNow(new PercentDeltaNotification($userConfig));
        }
    }

    /**
     * @param  array  $allData
     * @param  Collection  $allDeltas
     * @return array
     */
    private static function determineIfDeltaIsHit(array $allData, Collection $allDeltas): array
    {
        $notifiable = [];

        foreach ($allDeltas->groupBy('symbol') as $symbol => $deltaPerSymbol) {
            $dataPerSymbol = array_reverse($allData[$symbol]); // start from the oldest record
            $dataPerSymbol = Collection::make($dataPerSymbol);


            foreach ($deltaPerSymbol as $deltaEntry) {
                // get the start-end timestamps for this delta entry
                $startOfPeriod = Date::now()->getTimestampMs();
                $endOfPeriod = self::modifyTimePeriod($deltaEntry->timeframe_flag, $deltaEntry->timeframe_value);

                $mostRecentPriceEntry = $dataPerSymbol->last();

                $priceData = $dataPerSymbol
                    ->whereBetween('mts', [$endOfPeriod, $startOfPeriod]);

                foreach ($priceData as $priceEntry) {
                    // check if the percent delta is satisfied
                    $mostRecentPrice = $mostRecentPriceEntry->bid;
                    $priceEntryPrice = $priceEntry->bid;
                    $expectedPercentChange = $deltaEntry->percent_change;

                    $totalChange = abs($mostRecentPrice - $priceEntryPrice) / (($mostRecentPrice + $priceEntryPrice) / 2) * 100;

                    if ($totalChange >= $expectedPercentChange) {
                        $notifiable[] = $deltaEntry;
                    }
                }
            }
        }

        return $notifiable;
    }

    /**
     * @param  Collection  $allDeltas
     * @return array
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    private static function getDataPerLongestPeriod(Collection $allDeltas): array
    {
        $timeFrames = [];

        $responseStack = [];

        foreach ($allDeltas->groupBy('symbol') as $deltaPerSymbol) {
            $timeFrames[$deltaPerSymbol->first()->symbol] = self::getLongestDelta($deltaPerSymbol);
        }

        foreach ($timeFrames as $symbol => $timeFrame) {
            $now = Date::now();

            $from = self::modifyTimePeriod($timeFrame->timeframe_flag, $timeFrame->timeframe_value);
            $to = $now->getTimestampMs();


            // get data from the api
            $response = self::getDataFromAPI($symbol, $from, $to);

            foreach (json_decode($response) as $data) {
                $entry = new TickerHistorical([
                    'bid' => $data[TickerHistorical::idFromKey('bid')],
                    'ask' => $data[TickerHistorical::idFromKey('ask')],
                    'mts' => $data[TickerHistorical::idFromKey('mts')],
                ]);

                $responseStack[$symbol][] = $entry;
            }
        }

        return $responseStack;
    }

    /**
     * @return Collection
     */
    private static function getAllDeltas(): Collection
    {
        return PercentDeltaRepo::getAllStatic();
    }

    /**
     * @param  Collection  $allDeltas
     * @return PercentDeltaEvaluationDto
     */
    private static function getLongestDelta(Collection $allDeltas): PercentDeltaEvaluationDto
    {
        $longestTimeFlag = "";
        $longestTimePeriod = 0;
        $longestTimeFlagIndex = 0;
        $lengthCalc = self::lengthCalculator();

        // find the longest time period flag
        foreach ($allDeltas as $delta) {
            $timeFlagIndex = $lengthCalc[$delta->timeframe_flag];

            if ($timeFlagIndex > $longestTimeFlagIndex) {
                $longestTimeFlagIndex = $timeFlagIndex;
                $longestTimeFlag = $delta->timeframe_flag;
            }
        }

        $withLongestTimeFlag = $allDeltas
            ->where('timeframe_flag', $longestTimeFlag);

        // find the longest time period in the filtered deltas
        foreach ($withLongestTimeFlag as $delta) {
            if ($delta->timeframe_value > $longestTimePeriod) {
                $longestTimePeriod = $delta->timeframe_value;
            }
        }

        // we now have the longest time flag + period. Get the data from the api
        return new PercentDeltaEvaluationDto([
            'timeframe_flag' => $longestTimeFlag,
            'timeframe_value' => $longestTimePeriod
        ]);
    }

    /**
     * @return Collection
     */
    private static function lengthCalculator(): Collection
    {
        return new Collection([
            "H" => 1,
            "D" => 2,
            "W" => 3,
            "M" => 4,
            "Y" => 4
        ]);
    }

    /**
     * @param  string  $symbol
     * @param  int  $from
     * @param  int  $to
     * @return string
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    private static function getDataFromAPI(string $symbol, int $from, int $to): string
    {
        try {
            return (new ApiWrapper())->getHistoricalTickerData($symbol, $from, $to, true);
        } catch (Exception $e) {
            Log::error(sprintf("Error while fetching historical data for symbol %s, %s", $symbol, $e->getMessage()));
            throw new ExternalApiCallNotSuccessfulException();
        }
    }

    /**
     * @param  string  $period
     * @param  int  $value
     * @return int
     */
    private static function modifyTimePeriod(string $period, int $value): int
    {
        $now = Date::now();

        return match ($period) {
            "H" => $now->subHours($value)->getTimestampMs(),
            "D" => $now->subDays($value)->getTimestampMs(),
            "W" => $now->subWeeks($value)->getTimestampMs(),
            "M" => $now->subMonths($value)->getTimestampMs(),
            "Y" => $now->subYears($value)->getTimestampMs(),
        };
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
