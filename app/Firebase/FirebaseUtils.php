<?php
/**
 * Created by PhpStorm.
 * User: rohs
 * Date: 17.12.18
 * Time: 10:04
 */

namespace App\Firebase;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseUtils
{
    public static function get(){
        $serviceAccount = ServiceAccount::fromJsonFile(base_path() .'/auto-stop-8c24a-9c7a4a38e09e.json');

        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->create();

        return $firebase;
    }
}