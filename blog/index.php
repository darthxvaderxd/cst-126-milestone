<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles displaying the blogpost form to a user
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

// load user from session
$user = getSessionUser();

// redirect to login
loginRequired();

// show the beatufiul form
include 'form.php';