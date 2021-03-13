<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    // Get our variables...
    $email1 = $_REQUEST['email1'];
    $email2 = $_REQUEST['email2'];
    $user_name = $_REQUEST['user_name'];
    $password1 = $_REQUEST['password1'];
    $password2 = $_REQUEST['password2'];
    $nick_name = $_REQUEST['nick_name'];
    $first_name = $_REQUEST['first_name'];
    $last_name = $_REQUEST['last_name'];
    $middle_name = $_REQUEST['middle_name'];
    $address1 = $_REQUEST['address1'];
    $address2 = $_REQUEST['address2'];
    $city = $_REQUEST['city'];
    $state = $_REQUEST['state'];
    $postal_code = $_REQUEST['postal_code'];
    $country = $_REQUEST['country'];

    // validate email
    if ($email1 !== $email2) {
        echo "Emails do not match";
        return;
    }

    // validate password
    if ($password1 !== $password2) {
        echo "Passwords do not match";
        return;
    }

    // validate required fields filled out
    if (empty($email1)) {
        echo "Email is required";
        return;
    }

    if (empty($user_name)) {
        echo "user_name is required";
        return;
    }

    if (empty($password1)) {
        echo "password1 is required";
        return;
    }

    if (empty($first_name)) {
        echo "first_name is required";
        return;
    }

    if (empty($last_name)) {
        echo "last_name is required";
        return;
    }

    if (empty($address1)) {
        echo "address1 is required";
        return;
    }

    if (empty($city)) {
        echo "city is required";
        return;
    }

    if (empty($state)) {
        echo "state is required";
        return;
    }

    if (empty($postal_code)) {
        echo "postal_code is required";
        return;
    }

    if (empty($country)) {
        echo "country is required";
        return;
    }

    $sql = "INSERT INTO users (user_name, password, nick_name, first_name, last_name, middle_name, email, address1, address2, city, state, postal_code, country, banned, deleted) "
         . "VALUES (?, ?, ?, ?, ?, ?, ? , ?, ?, ?, ?, ?, ?, false, false) ";
    
    $mysqli = new mysqli('localhost', 'root', 'root', 'milestone');
    if (mysqli_connect_errno()) {
        echo "There was an error creating account.";
        echo("<!-- Connect failed: %s\n". mysqli_connect_error()) ."-->";

    }

    $stmt = $mysqli->prepare($sql);
    
    if ($stmt) {
        // Sanitize the post params to stop sql injection
        $stmt->bind_param(
            "sssssssssssss",
            $user_name,
            $password1,
            $nick_name,
            $first_name,
            $last_name,
            $middle_name,
            $email1,
            $address1,
            $address2,
            $city,
            $state,
            $postal_code,
            $country,
        );
        $stmt->execute();
        
        // close the connection
        $mysqli->close();
        echo "Account created.";
    } else {
        echo "There was an error creating account.";
        echo "<!-- ".$mysqli->errno . ' ' . $mysqli->error. " -->";
    }
} catch (Exception $e) {
    echo "There was an error creating your account.";
    echo "<!-- " .print_r($e, true). "-->";
}