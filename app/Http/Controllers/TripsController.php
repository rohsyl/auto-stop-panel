<?php

namespace App\Http\Controllers;

use App\Firebase\FirebaseUtils;

/**
 * Class TripsController
 * @package App\Http\Controllers
 */
class TripsController extends AuthController
{
    /**
     * TripsController constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * This action display the trip path on a map
     * @param null|int $id The id of the trop
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function map($id = null){

        return view('trips.map')->with([
            'tripId' => $id,
            'apikey' => config('autostop.google.maps.apikey'),
            'firebase' => [
                'apiKey' => config('autostop.google.firebase.apiKey'),
                'authDomain' => config('autostop.google.firebase.authDomain'),
                'databaseURL' => config('autostop.google.firebase.databaseURL'),
                'projectId' => config('autostop.google.firebase.projectId'),
                'storageBucket' => config('autostop.google.firebase.storageBucket'),
                'messagingSenderId' => config('autostop.google.firebase.messagingSenderId'),
            ],
        ]);
    }

    public function index(){

        $trips = FirebaseUtils::get()
            ->getDatabase()
            ->getReference('trips')
            ->getValue();

        print_r($trips);
        die();
    }
}
