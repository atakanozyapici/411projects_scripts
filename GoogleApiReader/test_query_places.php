<?php
require_once __DIR__ . '/GooglePlacesQuery.php';
require_once __DIR__ . '/FileIO.php';

$test = new GooglePlacesQuery();

// $test->TestPlacesApi();


$place_ids = $test->findRestaurantsNearChampaign("Burger");
$num_places = count($place_ids);

for($i = 0; $i < $num_places; $i++) {
    $result = $test->findDetailedInfo($place_ids[$i][0]);

    if($result) {
        $fileIO = new FileIO($place_ids[$i][1]);
        
        $fileIO->WriteFile($result);
    }
}

?>