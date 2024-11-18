<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TickerHistorical extends Model
{
    protected $fillable = [
        'bid',
        'ask',
        'mts',
    ];

    public static array $apiMarshaller = [
        1 => 'bid',
        3 => 'ask',
        12 => 'mts',
    ];

    public static array $apiUnMarshaller = [
        'bid' => 1,
        'ask' => 3,
        'mts' => 12,
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
