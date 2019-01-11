<?php

namespace App\Http\Controllers;

use App\Firebase\FirebaseUtils;
use Carbon\Carbon;
use Kreait\Firebase;

/**
 * Class AlertsController. Manage Alerts
 * @package App\Http\Controllers
 */
class AlertsController extends AuthController
{
    /**
     * AlertsController constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * This action will display the list of alerts
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){

        // query to fb
        $firebase = FirebaseUtils::get();
        $database = $firebase->getDatabase();
        $references = $database->getReference('alerts');
        $alerts = $references->getValue();

        $readedAlerts = [];
        $newAlerts = [];
        // split alert red by admin and the unred ones
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

    /**
     * This action will display the details of a given alert
     * @param $id int The id of the alert
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function details($id){

        // query to fb
        $firebase = FirebaseUtils::get();
        $database = $firebase->getDatabase();
        $references = $database->getReference('alerts/'.$id);
        $alert = $references->getValue();

        // load relationship data
        $this->fillAlertDetails($database, $alert);

        return view('alerts.details')->with([
            'alert' => $alert,
            'id' => $id
        ]);
    }

    /**
     * Set the given as read
     * @param $id int The if of the alert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function read($id){

        // query to fb
        $firebase = FirebaseUtils::get();
        $database = $firebase->getDatabase();
        $references = $database->getReference('alerts/'.$id);
        $alert = $references->getValue();

        // set as read
        $alert['readDate'] = Carbon::now()->timestamp;
        $alert['readByAdmin'] = true;

        // save change
        $references->set($alert);

        return redirect()->route('alerts.index');
    }

    /**
     * Fill the details of the alert
     * @param $database Firebase\Database The reference to the fb database
     * @param $alert array The alert to fill in
     */
    private function fillAlertDetails($database, &$alert){

        // load trip
        $references = $database->getReference('trips/'.$alert['tripUid']);
        $alert['trip'] = $references->getValue();

        // load owner
        $references = $database->getReference('persons/'.$alert['trip']['ownerUid']);
        $alert['trip']['owner'] = $references->getValue();

    }



}
