<?php

//Make sure that it is a POST request.
if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
    throw new Exception('Request method must be POST!');
}

//Make sure that the content type of the POST request has been set to application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if(strcasecmp($contentType, 'application/json') != 0){
    throw new Exception('Content type must be: application/json');
}

// array for JSON response
$response = array();
$content = trim(file_get_contents("php://input"));
$decoded_input = json_decode($content,true);

// check if the json message exists
if (is_array($decoded_input)) {

    // include db connect class
    require_once __DIR__ . '/db_config.php';

    // connecting to db
    $link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    //formulize the query
    $query = "SELECT * FROM ";
    $from = "eatery ";
    $where = "WHERE Eatery_ID IN ";
    $where2 = "(SELECT Eatery_ID From menu_item NATURAL JOIN menu WHERE ";
    $flag = 0;
    if($decoded_input['Name'] != ""){
      $where2 .= "Item_Name LIKE '%";
      $where2 .= $decoded_input['Name'];
      $where2 .= "%'";
      $flag = 1;
    }
    if($x = $decoded_input['Type'] != ""){
      if($flag)
        $where2 .= " AND ";
      $where2 .= "Item_Type = '";
      $where2 .= $decoded_input['Type'];
      $where2 .= "'";
      $flag = 1;
    }
    if($x = $decoded_input['MaxPrice'] != ""){
      if($flag)
        $where2 .= " AND ";
      $where2 .= "Item_Price <= ";
      $where2 .= $decoded_input['MaxPrice'];
      $flag = 1;
    }
    if($x = $decoded_input['AvailableAfter'] != ""){
      if($flag)
        $where2 .= " AND ";
      $where2 .= "Start_Hour < ";
      $where2 .= $decoded_input['AvailableAfter'];
      $flag = 1;
    }
    if($x = $decoded_input['AvailableUntil'] != ""){
      if($flag)
        $where2 .= " AND ";
      $where2 .= "End_Hour > ";
      $where2 .= $decoded_input['AvailableUntil'];
      $flag = 1;
    }
    // $query .= $from;
    // $query .= " WHERE ";
    // $query .= $where1;
    // $query .= "='";
    // $query .= $where2;
    // $query .= "'";
    // $query = "SELECT * FROM eatery WHERE Eatery_Name=Baban";


    //$id = mysqli_query($link, "SELECT max(Eatery_ID) + 1 FROM Eatery");
    //$result = mysqli_query($link, "SELECT * FROM Eatery");

    $query .= $from;
    $query .= $where;
    $where2 .= ")";
    $query .= $where2;
    echo $query;
    echo "\n";

    $result = mysqli_query($link, $query);


    // check if row inserted or not
    if ($result) {
      // looping through all results
      // products node
      $response["products"] = array();

      while ($row = mysqli_fetch_array($result)) {
          // temp user array
          $product = array();
          $product["Eatery_ID"] = $row["Eatery_ID"];
          $product["Eatery_Name"] = $row["Eatery_Name"];
          $product["Email"] = $row["Email"];
          // push single product into final response array
          array_push($response["products"], $product);
      }
      // success
      $response["success"] = 1;

      // echoing JSON response
      echo json_encode($response);
    } else {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred.";

        // echoing JSON response
        echo json_encode($response);
    }

    mysqli_close($link);
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";

    // echoing JSON response
    echo json_encode($response);
}
?>