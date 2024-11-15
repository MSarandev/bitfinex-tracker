<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PercentDelta extends Model
{
    use SoftDeletes;

    public $table = 'percent_deltas';

    protected $fillable = [
        'id',
        'user_id',
        'timeframe_flag',
        'timeframe_value',
        'percent_change',
        'active',
        'symbol'
    ];

    protected $hidden = [
        'user_id',
    ];

    protected $dates = ['deleted_at'];
}
