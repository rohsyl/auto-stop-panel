<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Firebase\FirebaseUtils;
use Illuminate\Support\Facades\Storage;

class StatsController extends AuthController
{
    public function __construct() {
        parent::__construct();
    }

    public function index()
    {
        $firebase = FirebaseUtils::get();
        $db = $firebase->getDatabase();

        $root_ref = $db->getReference()->getSnapshot()->getValue();

        // Trips that are not finished are removed from the dataset
        foreach( $root_ref['trips'] as $tripId => $trip )
        {
            if ( $trip['status'] != 'finished')
            {
                unset( $root_ref['trips'][$tripId] );
            }
        }

        // The stats will be two days old at worst
        $stats_cachefile = 'cached_stats.ser';
        $stats_ttl = 2 * (24*60*60);
        $refresh_stats = true;
        $stats = [];

        if ( Storage::disk('local')->exists( $stats_cachefile ) )
        {
            $cached_stats = unserialize( Storage::disk('local')->get( $stats_cachefile ) );

            // We keep the cached state if we are not beyond the TTL
            if ( time() - $cached_stats['timestamp'] < $stats_ttl )
            {
                $stats = $cached_stats;
                $refresh_stats = false;
            }
        }

        if ( $refresh_stats )
        {
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

            Storage::disk('local')->put( $stats_cachefile, serialize($stats) );
        }


        return view('stats.index')->with( $stats );
    }


    private function getTotalTrips( $root_ref )
    {
        if ( !is_array($root_ref) ) return 0;

        return (array_key_exists('trips', $root_ref) ? count( $root_ref['trips'] ) : 0);
    }


    private function getTotalPersons( $root_ref )
    {
        if ( !is_array($root_ref) ) return 0;

        return (array_key_exists('persons', $root_ref) ? count( $root_ref['persons'] ) : 0);
    }


    // Approximately equal to the number of drivers
    private function getTotalPlates( $root_ref )
    {
        if ( !is_array($root_ref) ) return 0;

        return (array_key_exists('plates', $root_ref) ? count( $root_ref['plates'] ) : 0);
    }

    // Persons who are registered but who never traveled with the app are NOT counted
    private function getAverageTripsPerPerson( $root_ref )
    {
        if ( !is_array($root_ref) ) return 0;

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

    // Seuls sont comptés les voyages qui sont explicitement liés à une plaque
    private function getAverageTripsPerPlate( $root_ref )
    {
        if ( !is_array($root_ref) ) return 0;

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
        // Since there is one report per bad trip, the number of reports is equal to
        // the number of bad trips
        $bad_trips = count( $root_ref['reports']);

        $total_trips = count( $root_ref['trips'] );
        $good_trips = $total_trips - $bad_trips;

        $satisfaction_rate = $good_trips / $total_trips;

        return $satisfaction_rate;
    }

    // Trips that have less than two GPS positions are ignored
    private function getTotalPersonKm( $root_ref )
    {
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

        if ( isset($json_object['rows']['0']['elements']['0']['distance']['value']) )
        {
            return intval( $json_object['rows']['0']['elements']['0']['distance']['value'] );
        }

        else
        {
            // return 0;
            return rand(10,100); // For testing only!
        }
    }

    // OK Le nombre de trajets total réalisés => number of trips

    // NOPE Le nombre de personnes transportées => impossible à obtenir sans identifier chaque personne.
        // (chaque personne aura son propre trip même si elles prennent le même véhicule)
        // Si plusieurs passagers, risque de compter plusieurs fois la même personne:
        // 2 personnes, chacune saisit 2 dans nombre de personnes
        // => 2 personne => 2 trips différents => 2 * 2 personnes => 4 personnes !
        // À la rigueur OK si une personne seulement utilise l'app pour tout le monde, mais besoin de
        // discipline côté utilisateur et impossible à garantir. Et moins de sécurités car moins de
        // passagers identifiés par nom, prénom etc.

    // OK Le nombre total de « km-personne » réalisés => SUM(distance_voyage * nb_personnes_voyage)
        // distance_voyage => dist(1st position, destination) !! Peut être faux si arrêt avant arrivée !
        // distance_voyage => dist(1st position, last_position) ! plus précis et pas trop intensif niveau calcul
        // distance_voyage => dist(1st position, 2nd_position) + dist(2nd position, 3rd position) etc. Heavy to compute? More precise though

    // OK Le nombre d'utilisateurs de l'application auto-stoppeurs => number of person entities
    // OK Le nombre de trajets moyens par auto-stoppeur AVG( trips_by_person )
        // trips_by_person => count trips where ownerUid = xXXXX

    // OK Le nombre d'utilisateurs de l'application automobilistes => number_of_plates (davantage un nombre de véhicules que d'utilisateurs)
    // OK Le nombre de trajet moyens réalisés par automobilistes AVG( trips_by_plates )
        // trips_by_plate => count trips where plate = xXXXX

    // OK Le taux de satisfaction (trajets ok VS trajets pas ok)
        // trajets_ok = counf_of_trips where trip.plate.reports => null
        // trajets_PAS_ok = counf_of_trips where trip.plate.reports != null



    // NOPE Le détail des alertes (toutes information sur les trajets qui ont généré une alerte)
    // vague... partie de Gaétan?
}
