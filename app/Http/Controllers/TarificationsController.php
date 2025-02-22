<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TarificationsController extends Controller
{
    //

    public function index(){

        return view('dashboard.tarifications');
    }
}
