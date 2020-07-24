<?php
require_once __DIR__ . '/GooglePlacesQuery.php';

$test = new GooglePlacesQuery();

// $test->TestPlacesApi();

$test->findRestaurant("Mia Za");

echo $test->findDetailedInfo("ChIJBUwndT_XDIgRq-dn6OFKEe0");
?>