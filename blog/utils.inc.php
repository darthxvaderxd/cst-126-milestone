<?php 
/**
 *  Written by: Richard Williamson
 * Description: This file houses utility functions for blog posts
 */
require_once './lib/db.php';

/**
 * Create a new blog post
 * @param array $user - this is the session user
 * @param string $title - title of the blog post
 * @param string $body - the meat of the post
 * @param boolean $debug
 */
function saveBlogPost($user, $title, $body, $debug = false) {
    $db = getDatabase($debug);
    if ($db) {
        $sql = "INSERT INTO blog_post (title, body, user_id, live) values (?, ?, ?, true)";
        $stmt = $db->prepare($sql);
        if ($stmt) {
            // Sanitize the post params to stop sql injection
            $stmt->bind_param("sss", $title, $body, $user['user_id']);
            $stmt->execute();
            $error =  mysqli_error($db);
            if ($error) {
                throw Exception($error);
            }
            // close the connection
            $db->close();
        } else {
            if ($debug) {
                echo "<!-- ".$db->errno . ' ' . $db->error. " -->";
            }
            throw Exception('Could not prepare statement');
        }
    } else {
        echo '<!-- no db -->';
        throw Exception('Could not connect to database');
    }
}
