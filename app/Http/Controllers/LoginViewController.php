<?php

namespace App\Http\Controllers;

class LoginViewController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }
}
