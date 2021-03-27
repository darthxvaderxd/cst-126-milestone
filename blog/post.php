<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles creating a blogpost from a user
 */

$debug = true;

if ($debug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// do this because of include issues
chdir('../');
require_once('./lib/user.php');
require_once('./lib/blacklist.words.php');
require_once('./blog/utils.inc.php');

$error = "";

// whoopsies
try {
    // load user from session
    $user = getSessionUser();
    
    // redirect to loginS
    loginRequired();
    
    $title = $_REQUEST['title'];
    $body = $_REQUEST['body'];
    
    if (empty($title) || empty($body)) {
        $error = "You need a blog title and post to continue.";
    } else { // bad word check
        foreach($blacklisted_words as $bword) {
            if (strpos($title, "$bword ") !== false || strpos($title, " $bword") !== false ) {
                if ($error !== "") {
                    $error .= ", ";
                }
                $error .= "${bword} is in the title and this is not an acceptable word";
            }
            if (strpos($body, " $bword ") !== false || strpos($body, " $bword") !== false ) {
                if ($error !== "") {
                    $error .= ", ";
                }
                $error .= "${bword} is in the post and this is not an acceptable word";
            }
        }
    }

    saveBlogPost($user, $title, $body, $debug);
} catch (Exception $e) {
    $error = "There was an error that occured";
    if ($debug) {
        echo "<!-- " .print_r($e, true). "-->";
    }
}

if (!empty($error)) {
    include './blog/form.php';
    return;
}

include './blog/display.php';