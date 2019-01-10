<?php

namespace App\Http\Controllers;

use App\Firebase\FirebaseUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlertsController extends AuthController
{
    public function __construct() {
        parent::__construct();
    }

    public function index(){

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database->getReference('alerts');

        $alerts = $references->getValue();

        $readedAlerts = [];
        $newAlerts = [];

        foreach ($alerts as $id => $alert){
            if($alert['readByAdmin']){
                $readedAlerts[$id] = $alert;
            }
            else{
                $newAlerts[$id] = $alert;
            }
        }

        return view('alerts.index')->with([
            'readedAlerts' => $readedAlerts,
            'newAlerts' => $newAlerts
        ]);
    }

    public function details($id){

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database->getReference('alerts/'.$id);

        $alert = $references->getValue();

        $this->fillAlertDetails($database, $alert);



        return view('alerts.details')->with([
            'alert' => $alert,
            'id' => $id
        ]);
    }

    public function read($id){

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database->getReference('alerts/'.$id);

        $alert = $references->getValue();

        $alert['readDate'] = Carbon::now()->timestamp;
        $alert['readByAdmin'] = true;

        $references->set($alert);

        return redirect()->route('alerts.index');
    }

    private function fillAlertDetails($database, &$alert){
        $references = $database->getReference('trips/'.$alert['tripUid']);

        $alert['trip'] = $references->getValue();

        $references = $database->getReference('persons/'.$alert['trip']['ownerUid']);

        $alert['trip']['owner'] = $references->getValue();

    }



}
