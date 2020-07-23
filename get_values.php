<?php

/*
 * Following code will create a new product row
 * All product details are read from HTTP Post Request
 */

// array for JSON response
$response = array();
$content = trim(file_get_contents("php://input"));
$decoded_input = json_decode($content,true);

// check for required fields
if (is_array($decoded_input)) {

    $from = $decoded_input['from'];
    $where1 = $decoded_input['where1'];
    $where2 = $decoded_input['where2'];

    // include db connect class
    require_once __DIR__ . '/db_config.php';

    // connecting to db
    $link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    $query = "SELECT * FROM ";
    //$from = "";
    $where = "";
    for($i = 0; $i < count($decoded_input); $i++){
      echo $decoded_input[$i];
    }
    $query .= $from;
    $query .= " WHERE ";
    $query .= $where1;
    $query .= "='";
    $query .= $where2;
    $query .= "'";
    // $query = "SELECT * FROM eatery WHERE Eatery_Name=Baban";


    //$id = mysqli_query($link, "SELECT max(Eatery_ID) + 1 FROM Eatery");
    //$result = mysqli_query($link, "SELECT * FROM Eatery");

    $result = mysqli_query($link, $query);


    // check if row inserted or not
    if (mysqli_num_rows($result) > 0) {
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
