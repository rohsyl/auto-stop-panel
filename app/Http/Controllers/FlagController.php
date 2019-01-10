<?php

namespace App\Http\Controllers;

use App\Firebase\FirebaseUtils;
use Illuminate\Http\Request;

class FlagController extends AuthController
{
    public function __construct() {
        parent::__construct();
    }

    public function index(){

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database->getReference('plates');

        $plates = $references->getValue();

        $flagedPlates = [];

        foreach ($plates as $id => $plate){

            if(isset($plate['flaged']) && $plate['flaged']){
                $flagedPlates[$id] = $plate;
            }
        }

        return view('flaged.index')->with([
            'flagedPlates' => $flagedPlates,
        ]);
    }

    public function flag($plateNumber){

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database->getReference('plates')
            ->orderByChild("plateNumber")
            ->equalTo($plateNumber)
            ->limitToFirst(1);


        $plate = $references->getValue();
        $plate = reset($plate);


        $references = $database->getReference('plates/'.$plate['uid']);
        $plate = $references->getValue();
        $plate["flaged"] = true ;


        $references->set($plate);

        return redirect()->route('reports.index');
    }

    public function unflag($id){

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database->getReference('plates/'.$id);

        $plate = $references->getValue();
        $plate['flaged'] = false;
        $references->set($plate);

        return redirect()->route('flag.index');
    }


    public function flagFromTrip($id){

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database->getReference('trips/'.$id);
        $trip = $references->getValue();

        //if trip with no plate we don't do anything
        if(!isset($plate['flaged'])){
            return redirect()->route('alerts.index');
        }

        $plateUid = $trip['plateUid'];
        info($plateUid);
        $references = $database->getReference('plates/'.$plateUid);
        $plate = $references->getValue();
        info($plate);

        $plate['flaged'] = true;
        info($plate);
        $references->set($plate);

        return redirect()->route('flag.index');
    }

}
