<?php
namespace App\Http\Controllers;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * AuthController constructor.
     * This controller enable tu auth middleware so each controller that extend from this one will be secured
     */
    public function __construct() {
        $this->middleware('auth');
    }
}
