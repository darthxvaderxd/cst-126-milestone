<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles connecting to the database
 */

/**
 * returns database connection
 * @param boolean $debug
 * @return mysqli|null
 */
function getDatabase($debug = false) {
    try {
        $db = new mysqli('localhost', 'root', 'root', 'milestone');
        if (!mysqli_connect_errno()) {
            return $db;
        } else {
            if ($debug) {
                echo("<!-- Connect failed: %s\n". mysqli_connect_error()) ."-->";
            }
        }
    } catch (Exception $e) {
        if (debug) {
            echo "<!-- " .print_r($e, true). "-->";
        }
    }
    return null;
}