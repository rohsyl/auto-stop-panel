<?php

namespace App\Http\Controllers;

use App\Firebase\FirebaseUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends AuthController
{
    public function __construct() {
        parent::__construct();
    }

    public function index(){

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database->getReference('reports');

        $reports = $references->getValue();

        $readedReports = [];
        $newReports = [];

        foreach ($reports as $id => $report){
            if($report['readByAdmin']){
                $readedReports[$id] = $report;
            }
            else{
                $newReports[$id] = $report;
            }
        }

        return view('reports.index')->with([
            'readedReports' => $readedReports,
            'newReports' => $newReports
        ]);
    }

    public function details($id){

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database->getReference('reports/'.$id);

        $report = $references->getValue();

        $this->fillAlertDetails($database, $report);

        return view('reports.details')->with([
            'report' => $report,
            'id' => $id
        ]);
    }

    public function read($id){

        $firebase = FirebaseUtils::get();

        $database = $firebase->getDatabase();

        $references = $database->getReference('reports/'.$id);

        $report = $references->getValue();

        $report['readDate'] = Carbon::now()->timestamp;
        $report['readByAdmin'] = true;

        $references->set($report);

        return redirect()->route('reports.index');
    }

    private function fillAlertDetails($database, &$report){
        $references = $database->getReference('trips/'.$report['tripUid']);

        $report['trip'] = $references->getValue();

        $references = $database->getReference('persons/'.$report['trip']['ownerUid']);

        $report['trip']['owner'] = $references->getValue();
    }
}
