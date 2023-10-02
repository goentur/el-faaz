<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    function sideBar(): JsonResponse
    {
        $user = Auth::user();
        if ($user->sidebar === 's') {
            $user->update(['sidebar' => 'h']);
            return response()->json(['status' => true], 200);
        } else {
            $user->update(['sidebar' => 's']);
            return response()->json(['status' => true], 200);
        }
    }
}
