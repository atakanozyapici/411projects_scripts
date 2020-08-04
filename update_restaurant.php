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

    $query = "UPDATE eatery SET ";
    $where = " WHERE Eatery_ID = ";
    $where .= $decoded_input['Eatery_ID'];
    $flag = 0;
    $set = "";

    if($decoded_input['Name'] != ""){
      $set .= "Eatery_Name = '";
      $set .= $decoded_input['Name'];
      $set .= "'";
      $flag = 1;
    }
    if($decoded_input['Website'] != ""){
      if($flag == 1)
        $set .= ", ";
      $set .= "Website = '";
      $set .= $decoded_input['Website'];
      $set .= "'";
      $flag = 1;
    }
    if($decoded_input['Start_Hour'] != ""){
      if($flag == 1)
        $set .= ", ";
      $set .= "Start_Hour = ";
      $set .= $decoded_input['Start_Hour'];
      $flag = 1;
    }
    if($decoded_input['End_Hour'] != ""){
      if($flag == 1)
        $set .= ", ";
      $set .= "End_Hour = ";
      $set .= $decoded_input['End_Hour'];
      $flag = 1;
    }
    if($decoded_input['Open_Days'] != ""){
      if($flag == 1)
        $set .= ", ";
      $set .= "Open_Days = '";
      $set .= $decoded_input['Open_Days'];
      $set .= "'";
      $flag = 1;
    }
    if($decoded_input['Address'] != ""){
      if($flag == 1)
        $set .= ", ";
      $set .= "Address = '";
      $set .= $decoded_input['Address'];
      $set .= "'";
      $flag = 1;
    }
    if($decoded_input['Price_Level'] != ""){
      if($flag == 1)
        $set .= ", ";
      $set .= "Pricing = ";
      $set .= $decoded_input['Price_Level'];
      $flag = 1;
    }
    if($decoded_input['Phone'] != ""){
      if($flag == 1)
        $set .= ", ";
      $set .= "Phone_Num = '";
      $set .= $decoded_input['Phone'];
      $set .= "'";
      $flag = 1;
    }
    if($decoded_input['Regional_Type'] != ""){
      if($flag == 1)
        $set .= ", ";
      $set .= "Regional_Type = '";
      $set .= $decoded_input['Regional_Type'];
      $set .= "'";
      $flag = 1;
    }
    if($decoded_input['Eatery_Type'] != ""){
      if($flag == 1)
        $set .= ", ";
      $set .= "Eatery_Type = '";
      $set .= $decoded_input['Eatery_Type'];
      $set .= "'";
      $flag = 1;
    }
    if($decoded_input['Cuisine'] != ""){
      if($flag == 1)
        $set .= ", ";
      $set .= "Cuisine = '";
      $set .= $decoded_input['Cuisine'];
      $set .= "'";
      $flag = 1;
    }

    $query .= $set;
    $query .= $where;
    echo $query;

    $result = mysqli_query($link, $query);

    // check if row inserted or not
    if ($result) {
      // looping through all results
      // products node
      $response["products"] = array();
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
