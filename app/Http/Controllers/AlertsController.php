<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlertsController extends AuthController
{
    public function __construct() {
        parent::__construct();
    }

    public function index(){

        return view('alerts.index');
    }
}
