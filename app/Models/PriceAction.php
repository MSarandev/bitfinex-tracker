<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceAction extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'price_actions';

    protected $fillable = [
        'id',
        'user_id',
        'trigger',
        'price',
        'active',
        'symbol'
    ];

    protected $hidden = [
        'user_id',
    ];

    protected $dates = ['deleted_at'];

    /**
     * @param $price
     * @return bool
     */
    public function isHit($price): bool
    {
        $requestedPrice = $this->price;
        $trigger = $this->trigger;

        if ($trigger === 'above') {
            return $price > $requestedPrice;
        }

        return $price < $requestedPrice;
    }
}
