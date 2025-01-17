<?php

require_once __DIR__ . '\vendor\autoload.php';
use SKAgarwal\GoogleApi\PlacesApi;

class GooglePlacesQuery
{
    private $places;

    function __construct(){
        $this->places = new PlacesApi('AIzaSyC-RXf36R3F1uCFAMnAsEMdrn1BW9z24L8');
    }

    public function TestPlacesApi() {
        echo $this->places->getKey();
        $this->places->verifySSL(true);
        echo $this->places->getStatus();

    }

    public function findRestaurantsNearChampaign($keyword) {          
        $location = '40.1093,-88.2284';

        $params = [
            'types' => 'restaurant',
            'keyword' => $keyword
        ];
        $response = $this->places->nearbySearch($location, $radius = '5000', $params); # line 2
        $decoded = json_decode($response);

        // foreach()

        // echo $response;
        //var_dump($decoded);

        $num_places = count($decoded->results);
        $place_ids = array_fill(0, $num_places, 0);

        for ($i = 0; $i < $num_places; $i++) {
            $place_ids[$i] = array($decoded->results[$i]->place_id, $decoded->results[$i]->name);
        } 

        return $place_ids;
    }

    public function findRestaurant($name) {
        $params = [
            'types' => 'restaurant'
        ];

        $response = $this->places->textSearch($name, $params);
        return $response;
    }

    public function findDetailedInfo($place_id) {
        $response = $this->places->placeDetails($place_id);
        return $response;
    }
}

?>