<?php

namespace App\Http\Controllers;

use App\Firebase\FirebaseUtils;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class TripsController extends Controller
{
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

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database
            ->getReference('trips');

        $trips = $references->getValue();

        print_r($trips);
        die();

    }
}
