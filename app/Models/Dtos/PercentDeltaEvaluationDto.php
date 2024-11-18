<?php

namespace App\Models\Dtos;

use Illuminate\Database\Eloquent\Model;

class PercentDeltaEvaluationDto extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'timeframe_flag',
        'timeframe_value',
    ];
}
