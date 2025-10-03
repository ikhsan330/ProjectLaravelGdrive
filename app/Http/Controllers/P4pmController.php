<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class P4pmController extends Controller
{
    public function dashboard()
    {
        return view('p4pm.dashboard');
    }
}
