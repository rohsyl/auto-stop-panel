<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Firebase\FirebaseUtils;
use Illuminate\Support\Facades\Storage;

// This controller computes statistics related to the use of the Potostop app.
// It does so by downloading the whole Firebase JSON tree and analyzing it to compute the stats.
// Since the statistics are long to generate (about 12 seconds and counting), a caching system
// refreshes the stats only if they don't exist in the cache or if they are more than 48 hours old.
class StatsController extends AuthController
{
    // Constructing the controller
    public function __construct() {
        parent::__construct();
    }

    // The index page computes the statistics if necessary and displays them
    public function index()
    {
        // The stats will be two days old at worst
        $stats_cachefile = 'cached_stats.ser';
        $stats_ttl = 2 * (24*60*60);
        $refresh_stats = true;
        $stats = [];

        // If the stats exist in the cache...
        if ( Storage::disk('local')->exists( $stats_cachefile ) )
        {
            // ... they are unserialized
            $cached_stats = unserialize( Storage::disk('local')->get( $stats_cachefile ) );

            // If the cached stats are up-to-date enough,
            if ( time() - $cached_stats['timestamp'] < $stats_ttl )
            {
                // they are kept and we don't need to refresh the stats
                $stats = $cached_stats;
                $refresh_stats = false;
            }
        }

        // If the stats need to be refreshed, they are downloaded again from Firebase
        if ( $refresh_stats )
        {
            // Getting a Firebase reference
            $firebase = FirebaseUtils::get();
            $db = $firebase->getDatabase();

            // Getting the whole JSON tree from Firebase
            $root_ref = $db->getReference()->getSnapshot()->getValue();

            // Trips that are not finished are removed from the dataset
            foreach( $root_ref['trips'] as $tripId => $trip )
            {
                if ( $trip['status'] != 'finished')
                {
                    unset( $root_ref['trips'][$tripId] );
                }
            }

            // The stats are computed using the JSON tree and the controller's methods
            $stats = [
                'timestamp' => time(),
                'total_trips' => $this->getTotalTrips( $root_ref ),
                'total_persons' => $this->getTotalPersons( $root_ref ),
                'total_plates' => $this->getTotalPlates( $root_ref ),
                'avg_trips_per_person' => round( $this->getAverageTripsPerPerson( $root_ref ), 1),
                'avg_trips_per_plate' => round( $this->getAverageTripsPerPlate( $root_ref ), 1),
                'satisfaction_rate' => round( $this->getSatisfactionRate( $root_ref ), 3),
                'total_person_km' => $this->getTotalPersonKm( $root_ref )
            ];

            // Once the stats were recomputed, we save them in the cache for later use
            Storage::disk('local')->put( $stats_cachefile, serialize($stats) );
        }

        // The stats are sent to the statistics view
        return view('stats.index')->with( $stats );
    }

    // Returns the total number of trips found in the database
    private function getTotalTrips( $root_ref )
    {
        if ( !is_array($root_ref) ) return 0;

        return (array_key_exists('trips', $root_ref) ? count( $root_ref['trips'] ) : 0);
    }

    // Returns the total number of persons (aka passengers) found in the database
    private function getTotalPersons( $root_ref )
    {
        if ( !is_array($root_ref) ) return 0;

        return (array_key_exists('persons', $root_ref) ? count( $root_ref['persons'] ) : 0);
    }


    // Returns the total number of plates (aka drivers) found in the database
    // Approximately equal to the number of drivers
    private function getTotalPlates( $root_ref )
    {
        if ( !is_array($root_ref) ) return 0;

        return (array_key_exists('plates', $root_ref) ? count( $root_ref['plates'] ) : 0);
    }

    // Returns the average number of trips per person
    // Persons who are registered but who never traveled with the app are NOT counted
    private function getAverageTripsPerPerson( $root_ref )
    {
        if ( !is_array($root_ref) || !isset($root_ref['trips']) ) return 0;

        $total_trips_per_person = [];

        foreach( $root_ref['trips'] as $trip )
        {
            if( !isset($total_trips_per_person[$trip['ownerUid']]) )
                $total_trips_per_person[$trip['ownerUid']] = 1;
            else
                $total_trips_per_person[$trip['ownerUid']]++;
        }

        return array_sum( $total_trips_per_person ) / count( $total_trips_per_person );
    }

    // Only trips that are explicitly linked to a plate (plateUid property) are counted
    private function getAverageTripsPerPlate( $root_ref )
    {
        if ( !is_array($root_ref) || !isset($root_ref['trips']) ) return 0;

        $total_trips_per_plate = [];

        foreach( $root_ref['trips'] as $trip )
        {
            if ( array_key_exists('plateUid', $trip) )
            {
                if( !isset($total_trips_per_plate[$trip['plateUid']]) )
                    $total_trips_per_plate[$trip['plateUid']] = 1;
                else
                    $total_trips_per_plate[$trip['plateUid']]++;
            }
        }

        return array_sum( $total_trips_per_plate ) / count( $total_trips_per_plate );
    }

    // Returns a satisfaction rate for trips (the percentage of trips that were considered good).
    // If the rate is 1.0, all trips were good
    // If the rate is 0.0, all trips were bad
    private function getSatisfactionRate( $root_ref )
    {
        if ( !is_array($root_ref)
            || !isset($root_ref['trips'])
            || !isset($root_ref['reports'])
        ) return 0.0;

        // Since there is one report per bad trip, the number of reports is equal to
        // the number of bad trips
        $bad_trips = count( $root_ref['reports']);

        $total_trips = count( $root_ref['trips'] );
        $good_trips = $total_trips - $bad_trips;

        $satisfaction_rate = $good_trips / $total_trips;

        return $satisfaction_rate;
    }

    // Returns the total number of traveled kilometers for all passengers
    // Trips that have less than two GPS positions are ignored
    private function getTotalPersonKm( $root_ref )
    {
        if ( !is_array($root_ref) || !isset($root_ref['trips']) ) return 0;

        $total_person_km = 0;

        foreach ($root_ref['trips'] as $trip )
        {
            if( array_key_exists('positions', $trip) )
            {
                $positions = $trip['positions'];
                $positions_count = count( $positions );

                if ( $positions_count >= 2 )
                {
                    $from = $positions[0];
                    $to = $positions[ $positions_count - 1 ];

                    $distance_km = $this->dist(
                        [$from['latitude'], $from['longitude']],
                        [$to['latitude'], $to['longitude']]
                    );

                    $total_person_km += $distance_km;
                }
            }
        }

        return $total_person_km;
    }

    // Returns the distance between two GPS positions using Google's Distance Matrix API
    // Positions are 2-cells array (0 for latitude, 1 for longitude)
    // A. Cotting idea: manually compute the distances between all GPS positions of a trip (possible to use a GPX file and a library?)
    private function dist($from, $to)
    {
        // We make sure all parameters are 2-element arrays
        if ( !is_array($from) || !is_array($to) || count($from) != 2 || count($to) != 2 )
            return NAN;

        // Extracting latitude and longitude for origin and destination
        $from_lat = doubleval($from[0]);
        $from_lng = doubleval($from[1]);
        $to_lat = doubleval($to[0]);
        $to_lng = doubleval($to[1]);

        // Preparing the coordinates for the Google Maps Distance Matrix API
        $from_coords = $from_lat.','.$from_lng;
        $to_coords = $to_lat.','.$to_lng;

        // Preparing the URL parameters
        $params = [
            'key' => config('autostop.google.firebase.apiKey'),
            'origins' => $from_coords,
            'destinations' => $to_coords
        ];

        // Parameters keys and values are URL-escaped and temporarily stored again in $params
        foreach ($params as $key => &$value)
            $value = urlencode($key).'='.urlencode($value);

        // Building the Distance Matrix API URL...
        $dmapiUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json?';
        $dmapiUrl .= implode('&', $params);

        // cURL configuration
        $options = [
            // makes curl_exec() return the response body as a string
            CURLOPT_RETURNTRANSFER => true
        ];

        // Using cURL to retrieve the API results
        $c = curl_init( $dmapiUrl );
        curl_setopt_array( $c, $options);
        $response_body = curl_exec( $c );
        curl_close( $c );

        $json_object = json_decode( $response_body, true );

        if ( is_null($json_object) )
            return NAN;


        if ( isset($json_object['rows'][0]['elements'][0]['distance']['value']) )
        {
            return intval( $json_object['rows'][0]['elements'][0]['distance']['value'] );
        }

        else
        {
            // return 0; // If the API didn't reply, we return 0
            return rand(10,100); // For testing only!
        }
    }
}
