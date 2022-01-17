<?php

namespace App\Http\Controllers\Pages\Route;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteSearchController extends Controller
{

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'sometimes|nullable|string|min:1|max:255'
        ]);

        return Route::where('user_id', Auth::id())
            ->when($request->has('query') && $request->input('query'), function(Builder $query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->input('query') . '%')->get();
            })
            ->orderBy('updated_at', 'DESC')
            ->limit(15)
            ->get();
    }

}
