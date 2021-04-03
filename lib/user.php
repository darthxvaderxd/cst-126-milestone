<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles dealing with a user, creating, logging in and editing
 */

session_start();
require_once('./lib/db.php');

function registerUser($db, $request, $debug = false) {
    try {
        $sql = "INSERT INTO users (user_name, password, nick_name, first_name, last_name, middle_name, email, address1, address2, city, state, postal_code, country, banned, deleted) "
             . "VALUES (?, ?, ?, ?, ?, ?, ? , ?, ?, ?, ?, ?, ?, false, false) ";
        $stmt = $db->prepare($sql);
        
        if ($stmt) {
            // Sanitize the post params to stop sql injection
            $stmt->bind_param(
                "sssssssssssss",
                $request['user_name'],
                $request['password1'],
                $request['nick_name'],
                $request['first_name'],
                $request['last_name'],
                $request['middle_name'],
                $request['email1'],
                $request['address1'],
                $request['address2'],
                $request['city'],
                $request['state'],
                $request['postal_code'],
                $request['country'],
            );
            $stmt->execute();
            
            // close the connection
            $db->close();
        } else {
            if ($debug) {
                echo "<!-- ".$db->errno . ' ' . $db->error. " -->";
            }
        }
    } catch (Exception $e) {
        if ($debug) {
            echo "<!-- " .print_r($e, true). "-->";
        }
    }
    return false;
}

function isAdmin($user) {
    return true;
}

function getUserByUsername($db, $username, $debug = false) {
    try {
        $sql = "SELECT user_id, user_name, password, role_id, nick_name, first_name, last_name, middle_name, email, address1, address2, city, state, postal_code, country, banned, deleted "
             . "FROM users WHERE user_name = ? ";
       
        $stmt = $db->prepare($sql);
        if ($stmt) {
            // Sanitize the post params to stop sql injection
            $stmt->bind_param("s", $username);
            $stmt->execute();
            
            $stmt->bind_result($user_id, $user_name, $password, $role_id, $nick_name, $first_name, $last_name, $middle_name, $email, $address1, $adress2, $city, $state, $postal_code, $country, $banned, $deleted);
            $stmt->fetch();
            
            $db->close();
            // found user
            if ($user_id) {
                return [
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'password' => $password,
                    'role_id' => $role_id,
                    // set preferred name
                    'preferred_name' => $nick_name ? $nick_name : $first_name,
                    'nick_name' => $nick_name,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'middle_name' => $middle_name,
                    'email' => $email,
                    'address1' => $address1,
                    'adress2' => $adress2,
                    'city' => $city,
                    'state' => $state,
                    'postal_code' => $postal_code,
                    'country' => $country,
                    'banned' => $banned,
                    'deleted' => $deleted,
                ];
            }
        } else {
            if ($debug) {
                echo $db->errno . ' ' . $db->error;
            }
        }
    } catch (Exception $e) {
        if ($debug) {
            echo "<!-- " .print_r($e, true). "-->";
        }
    }

    return null;
}

function getUserById($db, $id, $debug = false) {
    try {
        $sql = "SELECT user_id, user_name, password, role_id, nick_name, first_name, last_name, middle_name, email, address1, address2, city, state, postal_code, country, banned, deleted "
            . "FROM users WHERE user_id = ? ";
            
            $stmt = $db->prepare($sql);
            if ($stmt) {
                // Sanitize the post params to stop sql injection
                $stmt->bind_param("i", $id);
                $stmt->execute();
                
                $stmt->bind_result($user_id, $user_name, $password, $role_id, $nick_name, $first_name, $last_name, $middle_name, $email, $address1, $adress2, $city, $state, $postal_code, $country, $banned, $deleted);
                $stmt->fetch();
                
                $db->close();
                // found user
                if ($user_id) {
                    return [
                        'user_id' => $user_id,
                        'user_name' => $user_name,
                        'password' => $password,
                        'role_id' => $role_id,
                        // set preferred name
                        'preferred_name' => $nick_name ? $nick_name : $first_name,
                        'nick_name' => $nick_name,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'middle_name' => $middle_name,
                        'email' => $email,
                        'address1' => $address1,
                        'adress2' => $adress2,
                        'city' => $city,
                        'state' => $state,
                        'postal_code' => $postal_code,
                        'country' => $country,
                        'banned' => $banned,
                        'deleted' => $deleted,
                    ];
                }
            } else {
                if ($debug) {
                    echo $db->errno . ' ' . $db->error;
                }
            }
    } catch (Exception $e) {
        if ($debug) {
            echo "<!-- " .print_r($e, true). "-->";
        }
    }
    
    return null;
}

function getLoginAttempts($db, $username, $debug = false) {
    try {
        $sql = "SELECT count(*) AS count FROM login_attempt la "
             . "JOIN users u on (u.user_id = la.user_id) "
             . " WHERE u.user_name = ? AND la.timestamp >=  (NOW() - INTERVAL SECOND(NOW()) SECOND - INTERVAL 30 MINUTE) ";

        // Sanitize the post params to stop sql injection
        $stmt = $db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            
            $stmt->bind_result($count);
            $stmt->fetch();
            
            if ($count) {
                return $count;
            }
        } else {
            if ($debug) {
                echo "<!-- ".$db->errno . ' ' . $db->error. " -->";
            }
        }
        $db->close();
    } catch (Exception $e) {
        if ($debug) {
            echo "<!-- " .print_r($e, true). "-->";
        }
    }
    return 0;
}

function addLoginAttempt($db, $user_id, $debug = false) {
    try  {
        $sql = "INSERT INTO login_attempt (user_id, timestamp) VALUES (?, NOW()) ";
        $stmt = $db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        } else {
            if ($debug) {
                echo "<!-- ".$db->errno . ' ' . $db->error. " -->";
            }
        }
            
        // close the connection
        $db->close();
    } catch (Exception $e) {
        if ($debug) {
            echo "<!-- " .print_r($e, true). "-->";
        }
    }
};

function validateLogin($db, $username, $password, $debug = false) {
    $user = getUserByUsername($db, $username, $debug);

    if ($user && $user['password'] === $password && $user['user_name'] === $username) {
        return $user;
    } else if ($user) { // capture login failures
        addLoginAttempt(getDatabase($debug), $user['user_id'], $debug);
    }
    return false;
}

function getSessionUser() {
    return !empty($_SESSION['user']) ? $_SESSION['user'] : false;
}

function loginRequired() {
    $user = getSessionUser();
    if (!$user) {
        header("Location: ../login.html");
        return; // not needed but you never know
    }
}