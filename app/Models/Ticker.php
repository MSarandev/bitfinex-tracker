<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bid',
        'ask'
    ];

    public static array $apiMarshaller = [
        0 => 'bid',
        1 => 'bidSize',
        2 => 'ask',
        3 => 'askSize',
        4 => 'dailyChange',
        5 => 'dailyChangeRelative',
        6 => 'lastPrice',
        7 => 'volume',
        8 => 'high',
        9 => 'low',
    ];

    public static array $apiUnMarshaller = [
        'bid' => 0,
        'bidSize' => 1,
        'ask' => 2,
        'askSize' => 3,
        'dailyChange' => 4,
        'dailyChangeRelative' => 5,
        'lastPrice' => 6,
        'volume' => 7,
        'high' => 8,
        'low' => 9,
    ];

    public static function idFromKey($key)
    {
        return self::$apiUnMarshaller[$key];
    }

    public static function keyFromId($id)
    {
        return self::$apiMarshaller[$id];
    }
}
