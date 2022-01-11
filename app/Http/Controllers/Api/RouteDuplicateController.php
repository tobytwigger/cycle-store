<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\File;
use App\Services\File\FileUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteDuplicateController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('create', Route::class);

        $request->validate([
            'hash' => 'required|string'
        ]);
        $route = Route::where('user_id', Auth::id())->whereHas('routeFile',
            fn(Builder $query) => $query->where('hash', $request->input('hash'))->where('type', FileUploader::ROUTE_FILE)
        )->first();

        return [
            'is_duplicate' => $route !== null,
            'file' => $route?->routeFile,
            'route' => $route
        ];
    }

}
