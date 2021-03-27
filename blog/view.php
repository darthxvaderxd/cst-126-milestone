<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles creating a blogpost from a user
 */

$debug = true;
$error = "";

if ($debug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// whoopsies
try {
    // do this because of include issues
    chdir('../');
    require_once('./lib/user.php');
    
    // load user from session
    $user = getSessionUser();
    
    // redirect to loginS
    loginRequired();
} catch (Exception $e) {
    $error = "An error has occured";
    if ($debug) {
        echo "<!-- " .print_r($e, true). "-->";
    }
}

include 'display.php';