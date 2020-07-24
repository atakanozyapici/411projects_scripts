<?php

/*
 * Following code will create a new product row
 * All product details are read from HTTP Post Request
 */

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

    //$id = mysqli_query($link, "SELECT max(Eatery_ID) + 1 FROM Eatery");
    //$result = mysqli_query($link, "SELECT * FROM Eatery");
    $res = $decoded_input['result'];
    if ($stmt = $link->prepare("INSERT INTO eatery VALUES(?, ?,?,?,?,?,?,?,?,?,?,?,? )") ) {

    /* bind parameters for markers */
    $stmt->bind_param('issiissiissis', $id, $name, $email, $s_hour, $e_hour, $open_days, $address, $pricing, $coord, $phone, $reg_type, $type, $cuisine);
    $id = 10;
    $name = $res['name'];
    $email = NULL;
    $s_hour = $res['opening_hours']['periods'][0]['open']['time'];
    $e_hour = $res['opening_hours']['periods'][0]['close']['time'];
    $open_days = NULL;
    $address = $res['formatted_address'];
    $pricing = $res['price_level'];
    $coord = NULL;
    $phone = $res['formatted_phone_number'];
    $reg_type = NULL;
    $type = NULL;
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
    //$result = mysqli_query($link, "INSERT INTO eatery(Eatery_ID, Eatery_Name, Email) VALUES('$id', '$price', '$description')");


    // check if row inserted or not
    if ($result) {
        // successfully inserted into database
        $response["success"] = 1;
        // $response["message"] = "Product successfully created.";

        // echoing JSON response
        echo json_encode($response);
    // if (mysqli_num_rows($result) > 0) {
    //   // looping through all results
    //   // products node
    //   $response["products"] = array();
    //
    //   while ($row = mysqli_fetch_array($result)) {
    //       // temp user array
    //       $product = array();
    //       $product["Eatery_ID"] = $row["Eatery_ID"];
    //       $product["Eatery_Name"] = $row["Eatery_Name"];
    //       $product["Email"] = $row["Email"];
    //       // push single product into final response array
    //       array_push($response["products"], $product);
    //   }
    //   // success
    //   $response["success"] = 1;
    //
    //   // echoing JSON response
    //   echo json_encode($response);
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
