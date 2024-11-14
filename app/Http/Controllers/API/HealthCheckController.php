<?php

namespace app\Http\Controllers\API;

use App\Http\Controllers\Controller;

class HealthCheckController extends Controller
{
    public function checkHealth()
    {
        return response()->json(['status' => 'ok']);
    }
}
