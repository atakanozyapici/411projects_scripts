<?php

/*
 * Following code will create a new product row
 * All product details are read from HTTP Post Request
 */

// array for JSON response
$response = array();

// check for required fields
if (isset($_GET['name']) && isset($_GET['id']) && isset($_GET['email'])) {

    $id = $_GET['id'];
    $price = $_GET['name'];
    $description = $_GET['email'];

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
    $result = mysqli_query($link, "INSERT INTO eatery(Eatery_ID, Eatery_Name, Email) VALUES('$id', '$price', '$description')");


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
