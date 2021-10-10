<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserViewController extends Controller
{
    public function showConfig()
    {
        return view('user.config');
    }
}
