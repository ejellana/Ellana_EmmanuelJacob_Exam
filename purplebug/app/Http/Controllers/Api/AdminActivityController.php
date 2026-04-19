<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class AdminActivityController extends Controller
{
    public function index()
    {
        $logs = Activity::with('causer')
            ->latest()
            ->get();

        return response()->json($logs);
    }
}
