<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles logging a user in
 */


require_once('./lib/db.php');
require_once('./lib/user.php');
$debug = true;

if ($debug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

function outputLoginForm($message) {
    $data = file_get_contents('./login.html');
    echo str_replace( 
        '<!-- message here -->',
        $message,
        $data
    );
}

try {
    // load user from session
    $user = !empty($_SESSION['user']) ? $_SESSION['user'] : false;
    
    if (!$user) {
        if (empty($_REQUEST['username'])) {
            outputLoginForm('<div class="error">user_name is required</div>');
            return;
        }
        
        if (!$user && empty($_REQUEST['password'])) {
            outputLoginForm('<div class="error">password is required</div>');
            return;
        }
        
        // get previous failed login attempts in last 30 minutes
        $failed_attempts = getLoginAttempts(getDatabase($debug), $_REQUEST['username'], $debug);
    
        if ($failed_attempts > 5) {
            outputLoginForm('<div class="error">You have had over five login failures in the last 30 minutes you are timed out</div>');
            return;
        }
    
        // validate the user login credentials
        $user = validateLogin(getDatabase($debug), $_REQUEST['username'], $_REQUEST['password'], $debug);
    }
    
    if (!$user) { // login failed
        outputLoginForm('<div class="error">Your login failed please try again</div>');
        return;
    } else { // login successful
        // store the user in the session
        $_SESSION['user'] = $user;
        echo "Welcome " .$user['preferred_name']. " you are logged in";
    }
} catch (Exception $e) {
    outputLoginForm('<div class="error">There was an error that caused login to fail</div>');
    if ($debug) {
        echo "<!-- " .print_r($e, true). "-->";
    }
}