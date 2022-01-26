<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StravaOverviewController extends Controller
{

    public function index()
    {
        return Inertia::render('Integrations/Strava/Index');
    }
}
