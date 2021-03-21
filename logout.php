<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles logging a user out
 */

session_start();
session_destroy();
header("Location: ". str_replace('logout.php', 'login.html', $_SERVER['REQUEST_URI']));