<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceAction extends Model
{
    use SoftDeletes;

    public $table = 'price_actions';

    protected $fillable = [
        'id',
        'user_id',
        'trigger',
        'price',
        'active'
    ];

    protected $hidden = [
        'user_id',
    ];

    protected $dates = ['deleted_at'];
}
