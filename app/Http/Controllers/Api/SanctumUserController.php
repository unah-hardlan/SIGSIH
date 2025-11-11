<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SanctumUserController extends Controller
{
    public function show(Request $request)
    {
        return $request->user();
    }
}
