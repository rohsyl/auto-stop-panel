<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatsController extends AuthController
{
    public function __construct() {
        parent::__construct();
    }

    public function index(){
        return view('stats.index');
    }
}
