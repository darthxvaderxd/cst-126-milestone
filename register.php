<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles registering a new user
 */

require_once('./lib/db.php');
require_once('./lib/user.php');
$debug = true;

if ($debug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

try {
    // Get our variables...
    $email1 = $_REQUEST['email1'];
    $email2 = $_REQUEST['email2'];
    $user_name = $_REQUEST['user_name'];
    $password1 = $_REQUEST['password1'];
    $password2 = $_REQUEST['password2'];
    $first_name = $_REQUEST['first_name'];
    $last_name = $_REQUEST['last_name'];
    $address1 = $_REQUEST['address1'];
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
    
    $db = getDatabase($debug);

    if (registeredUser($db, $_REQUEST, $debug)) {
        echo "Account created.";
    } else {
        echo "There was an error creating account.";
    }
} catch (Exception $e) {
    echo "There was an error creating your account.";
    if ($debug) {
        echo "<!-- " .print_r($e, true). "-->";
    }
}