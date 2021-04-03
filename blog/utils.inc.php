<?php 
/**
 *  Written by: Richard Williamson
 * Description: This file houses utility functions for blog posts
 */
require_once './lib/db.php';
require_once('./lib/user.php');

/**
 * Create a new blog post
 * @param array $user - this is the session user
 * @param string $title - title of the blog post
 * @param string $body - the meat of the post
 * @param string[] $tags - the tags
 * @param boolean $debug
 */
function saveBlogPost($user, $title, $body, $tags = [], $debug = false) {
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
        
        // save the tags
        if ($tags) {
            $blog_id = getBlogIdFromPost($user, $title, $body);
            foreach($tags as $tagString) {
                $tag = getTagByName($tagString, $debug);
                if (!$tag) {
                    $tag = createNewTag($tagString, $debug);
                }
                if ($tag) { // this could still be false
                    if ($blog_id) {
                        saveBlogTag($tag['tag_id'], $blog_id);
                    }
                }
            }
        }
    } else {
        echo '<!-- no db -->';
        throw Exception('Could not connect to database');
    }
}

/**
 * Get all the blog posts
 * @param number $page
 * @param number $page_size
 * @param array $sort - how are we sorting
 * @param boolean $include_extras - include user and tag data
 * @param boolean $debug
 * @return unknown[]
 */
function getBlogPosts($page = 1, $page_size = 20, $sort = ['date_created', 'desc'], $include_extras = false, $debug = false) {
    $db = getDatabase($debug);
    
    // saftey first
    $page = intval($page);
    $page_size = intval($page_size);
    
    // our posts
    $posts = [];
    if ($db) {
        $sql = "SELECT blog_post_id, title, body, user_id, date_created, date_updated, live FROM blog_post WHERE 1 LIMIT $page, $page_size";
        echo "<!-- sql => " .$sql. " -->";
        $result = $db->query($sql);
        
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                if ($include_extras) {
                    $row['user'] = getUserById(getDatabase($debug), $row['user_id']);
                    $row['tags'] = getTagsForBlogId($row['blog_post_id']);
                }
                $posts[] = $row;
            }
        }
    }
    return $posts;
}

/**
 * Get the blog_id from a new blog post
 * @param array $user
 * @param string $title
 * @param string $body
 * @param boolean $debug
 * @return int|false
 */
function getBlogIdFromPost($user, $title, $body, $debug = false) {
    $db = getDatabase($debug);
    if ($db) {
        $sql = "SELECT blog_post_id FROM blog_post WHERE title like ? AND user_id = ? and body like ?";
        $stmt = $db->prepare($sql);
        if ($stmt) {
            // Sanitize the post params to stop sql injection
            $stmt->bind_param("sss", $title, $user['user_id'], $body);
            $stmt->execute();
            
            $error =  mysqli_error($db);
            if ($error) {
                throw Exception($error);
            }
            
            $stmt->bind_result($blog_id);
            $stmt->fetch();
            $db->close();
            
            if ($blog_id) {
                return $blog_id;
            }
        } else {
            echo "<!-- no stmt -->";
        }
    } else {
        echo "<!-- no db -->";
    }
    return false;
}

/**
 * get a tag by name if it exists, or return false
 * @param string $tag
 * @param boolean $debug
 * @return array|boolean
 */
function getTagByName($tag, $debug = false) {
    $db = getDatabase($debug);
    if ($db) {
        $sql = "SELECT * FROM tags WHERE tag LIKE ?";
        $stmt = $db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $tag);
            $stmt->execute();
            
            $error =  mysqli_error($db);
            if ($error) {
                throw Exception($error);
            }
            
            $stmt->bind_result($tag_id, $tag);
            $stmt->fetch();
            
            // close the connection
            $db->close();
            if ($tag_id) {
                return [
                    'tag_id' => $tag_id,
                    'tag'    => $tag,
                ];
            }
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
    return false;
}

/**
 * Create a new tag
 * @param string $tag
 * @param boolean $debug
 * @return array|boolean
 */
function createNewTag($tag, $debug = false) {
    $db = getDatabase($debug);
    if ($db) {
        $sql = "insert into tags (tag) values(?)";
        $stmt = $db->prepare($sql);
        if ($stmt) {
            $tag = strtolower($tag);
            $stmt->bind_param("s", $tag);
            $stmt->execute();
            
            $error =  mysqli_error($db);
            if ($error) {
                throw Exception($error);
            }
            return getTagByName($tag);
        }
    }
    return false;
}

/**
 * Does the blog post already have this tag
 * @param int $blog_id
 * @param int $tag_id
 * @param boolean $debug
 * @return boolean
 */
function blogHasTag($blog_id, $tag_id, $debug = false) {
    $db = getDatabase($debug);
    if ($db) {
        $sql = "SELECT blog_tag_id FROM blog_tag WHERE blog_id = ? AND tag_id = ?";
        $stmt = $db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $blog_id, $tag_id);
            $stmt->execute();
            
            $error =  mysqli_error($db);
            if ($error) {
                throw Exception($error);
            }
            
            $stmt->bind_result($blog_tag_id);
            $stmt->fetch();
            
            if ($blog_tag_id) {
                return true;
            }
        }
    }
    return false;
}

/**
 * save the tag for the blog if it doesn't exist
 * @param int $tag_id
 * @param int $blog_id
 */
function saveBlogTag($tag_id, $blog_id) {
    // verify that it doesn't exist
    $db = getDatabase();
    if (!blogHasTag($blog_id, $tag_id)) {
        $sql = "INSERT INTO blog_tag (blog_id, tag_id) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $blog_id, $tag_id);
            $stmt->execute();
        }
        $error =  mysqli_error($db);
        if ($error) {
            throw Exception($error);
        }
    }
}

/**
 * get the tags for a given blog id
 * @param int $blog_id
 */
function getTagsForBlogId($blog_id) {
    $db = getDatabase();
    $tags = [];
    if ($db) {
        $sql = "SELECT t.tag FROM blog_tag bt join tags t using (tag_id) WHERE blog_id = $blog_id";
        $result = $db->query($sql);
        
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $tags[] = $row['tag'];
            }
        }
    }
    
    return $tags;
}
