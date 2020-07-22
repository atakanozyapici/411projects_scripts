<?php

/*
 * Following code will create a new product row
 * All product details are read from HTTP Post Request
 */

// array for JSON response
$response = array();

// check for required fields
if (isset($_GET['from']) && isset($_GET['where1']) && isset($_GET['where2'])) {

    $from = $_GET['from'];
    $where1 = $_GET['where1'];
    $where2 = $_GET['where2'];

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
    $result = mysqli_query($link, "SELECT * FROM eatery WHERE Eatery_Name = 'Baban' ");


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
