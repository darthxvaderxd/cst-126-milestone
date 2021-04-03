<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles displaying admin functions available
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


?>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Richards Blog</title>
	</head>
	<body>
		<a href="login.html">Login</a>
		<a href="register.html">Register</a>
	</body>
</html>