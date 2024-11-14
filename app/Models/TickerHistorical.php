<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TickerHistorical extends Model
{
    public float $bid;
    public float $ask;
    public int $mts;

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

    public function getBid(): float
    {
        return $this->bid;
    }

    public function setBid(float $bid): void
    {
        $this->bid = $bid;
    }

    public function getAsk(): float
    {
        return $this->ask;
    }

    public function setAsk(float $ask): void
    {
        $this->ask = $ask;
    }

    public function getMts(): int
    {
        return $this->mts;
    }

    public function setMts(int $mts): void
    {
        $this->mts = $mts;
    }
}
