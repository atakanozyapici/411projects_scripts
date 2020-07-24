<?php
require_once __DIR__ . '/GooglePlacesQuery.php';

$test = new GooglePlacesQuery();

$test->TestPlacesApi();

$result = $test->findRestaurantsNearChampaign();

echo $result;
?>