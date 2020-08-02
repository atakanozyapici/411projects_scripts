<?php

$dir    = 'C:\Users\ataka\Downloads\Temp';
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


  $id_result = mysqli_query($link, "SELECT max(Eatery_ID) + 1 FROM Eatery");
  $id_temp = mysqli_fetch_row($id_result)[0];
  //$result = mysqli_query($link, "SELECT * FROM Eatery");
  // $inter = mysqli_query($link, "SELECT MAX(Eatery_ID) FROM `eatery`");
  // echo json_encode($inter);
  $res = $decoded_input['result'];
  if ($stmt = $link->prepare("INSERT INTO eatery VALUES(?, ?,?,?,?,?,?,?,?,?,?,?,? )") ) {

  /* bind parameters for markers */
  $stmt->bind_param('issiissiissss', $id, $name, $website, $s_hour, $e_hour, $open_days, $address, $pricing, $coord, $phone, $reg_type, $type, $cuisine);
  $id = $id_temp;
  $name = $res['name'];
  $website = $res['website'];
  $s_hour = $res['opening_hours']['periods'][0]['open']['time'];
  $e_hour = $res['opening_hours']['periods'][0]['close']['time'];
  $days = "";
  for($j = 0; $j < count($res['opening_hours']['periods']); $j++){
    $days .= $res['opening_hours']['periods'][$j]['close']['day'];
  }
  $open_days = $days;
  $address = $res['formatted_address'];
  $pricing = $res['price_level'];
  $coord = NULL;
  $phone = $res['formatted_phone_number'];
  $reg_type = NULL;
  $type = $res['types'][0];
  $cuisine = NULL;



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

?>
