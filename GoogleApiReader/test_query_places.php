<?php
require_once __DIR__ . '/GooglePlacesQuery.php';
require_once __DIR__ . '/FileIO.php';

$test = new GooglePlacesQuery();

// $test->TestPlacesApi();

$test->findRestaurant("Mia Za");

$result = $test->findDetailedInfo("ChIJBUwndT_XDIgRq-dn6OFKEe0");

$fileIO = new FileIO("MiaZaTest");

$fileIO->WriteFile($result);
?>