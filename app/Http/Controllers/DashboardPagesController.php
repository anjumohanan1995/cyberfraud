<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardPagesController extends Controller
{


    public function dashboard(){

        return view('dashboard.dashboard');
    }

}
