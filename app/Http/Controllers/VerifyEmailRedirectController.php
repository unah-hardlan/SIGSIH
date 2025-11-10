<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifyEmailRedirectController extends Controller
{
    public function redirect(Request $request)
    {
        return redirect()->route('verify.email.page', [
            'token' => $request->query('token'),
            'email' => $request->query('email'),
        ]);
    }
}
