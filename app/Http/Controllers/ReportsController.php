<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends AuthController
{
    public function __construct() {
        parent::__construct();
    }

    public function index(){

        return view('reports.index');
    }
}
