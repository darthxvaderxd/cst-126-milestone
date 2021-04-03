<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles crud actions on posts
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

if (!isAdmin($user)) {
    throw new Exception("Unauthorized user");
}

$action = $_REQUEST['action'];
$approved_actions = ['edit', 'delete', 'toggle'];

if (!$action || !in_array($action, $approved_actions)) {
    throw new Exception("Action unknown");
}

$redirect = 'posts.php';

switch ($action) {
    case 'edit':
        return;
    case 'delete':
        break;
    case 'toggle':
        break;
}

header("Location: ". str_replace('post.php', $redirect, $_SERVER['REQUEST_URI']));