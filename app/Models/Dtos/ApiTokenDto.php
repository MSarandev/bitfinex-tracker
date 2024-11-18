<?php

namespace App\Models\Dtos;

use Illuminate\Database\Eloquent\Model;

class ApiTokenDto extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tokenValue',
        'expiration',
    ];
}
