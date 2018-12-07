<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripsController extends Controller
{
    //

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
}
