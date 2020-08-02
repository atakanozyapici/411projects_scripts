<?php

$dir    = 'C:\Users\ataka\Downloads\Menus\Menu';
$files1 = scandir($dir);

require_once __DIR__ . '/db_config.php';

// connecting to db
$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


for($i = 2; $i < count($files1); $i++){
  $name = $dir . "\\" . $files1[$i];
  $output = file_get_contents($name);
  $decoded_input = json_decode($output, true);


  //$id = mysqli_query($link, "SELECT max(Eatery_ID) + 1 FROM Eatery");
  //$result = mysqli_query($link, "SELECT * FROM Eatery");
  // $inter = mysqli_query($link, "SELECT MAX(Eatery_ID) FROM `eatery`");
  // echo json_encode($inter);
  $res = $decoded_input['Menus'];
  for($j =0; $j < count($res); $j++){

    if ($stmt = $link->prepare("INSERT INTO menu VALUES(?, ?,?,?,? )") ) {

    /* bind parameters for markers */
    $stmt->bind_param('iiiis', $eatery, $menu, $start, $end, $type);
    $eatery = $res[$j]['Eatery_ID'];
    $menu = $res[$j]['Menu_ID'];
    $start = $res[$j]['Start_Hour'];
    $end = $res[$j]['End_Hour'];
    $type = $res[$j]['Menu_Type'];


    /* execute query */
    $result = $stmt->execute();

    // /* bind result variables */
    // $stmt->bind_result($district);
    //
    // /* fetch value */
    // $stmt->fetch();
    //
    // printf("%s is in district %s\n", $city, $district);

    /* close statement */
    $stmt->close();
    }
  }
}

?>