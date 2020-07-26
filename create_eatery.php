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

    $query = "SELECT MAX(Eatery_ID) as next FROM eatery";

    $result = mysqli_query($link, $query);

    $row = mysqli_fetch_array($result);
    $next = $row['next'] + 1;

    if ($stmt = $link->prepare("INSERT INTO eatery VALUES(?, ?,?,?,?,?,?,?,?,?,?,?,? )") ) {

    /* bind parameters for markers */
    $stmt->bind_param('issiissiissss', $id, $name, $website, $s_hour, $e_hour, $open_days, $address, $pricing, $coord, $phone, $reg_type, $type, $cuisine);
    $id = $next;
    $name = $decoded_input['Name'];
    $website = $decoded_input['Website'];
    $s_hour = $decoded_input['Start_Hour'];
    $e_hour = $decoded_input['End_Hour'];
    $open_days = $decoded_input['Open_Days'];
    $address = $decoded_input['Address'];
    $pricing = $decoded_input['Price_Level'];
    $coord = NULL;
    $phone = $decoded_input['Phone'];
    $reg_type = $decoded_input['Regional_Type'];
    $type = $decoded_input['Eatery_Type'];
    $cuisine = NULL;



    /* execute query */
    $result = $stmt->execute();

    $stmt->close();
    }

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
