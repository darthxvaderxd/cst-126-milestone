<?php
/**
 *  Written by: Richard Williamson
 * Description: This file handles displaying admin functions about posts
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
require_once('./blog/utils.inc.php');

// load user from session
$user = getSessionUser();

// redirect to login
loginRequired();

if (!isAdmin($user)) {
    throw new Exception("Unauthorized user");
}


// get posts
$page = intval($_REQUEST['page'] ?? 1);
// TODO: update this to user selected and enfoce type
$sort = ['date_created', 'desc'];
$posts = getBlogPosts($page, 20, true, $debug);
?>

<html>
	<head>
		<title>Admin - Post Listing</title>
		<style>
		table { border-collapse: collapse; border: 1px solid #222; }
		td, th { padding: 0.4rem; text-align: center; border-right: 1px solid #222; }
		th { border-bottom: 1px solid #222; background-color: #555; color: #fff; }
		td { border-bottom: 1px dashed #222; }
		.odd { background-color: #ccc; }
		.even { background-color: #fff; }
		</style>
	</head>
	<body>
		<table>
			<tr>
				<th>ID</th>
				<th>Title</th>
				<th>Tags</th>
				<th>User</th>
				<th>Created</th>
				<th>Updated</th>
				<th>Published</th>
				<th>Actions</th>
			</tr>
			<?php 
			foreach($posts as $key => $post) {
			    $class = $key % 2 !== 0 ? 'odd' : 'even'; 
			    ?>
			    <tr class="<?php echo $class; ?>">
			    	<td><?php echo $post['blog_post_id']; ?></td>
			    	<td><?php echo $post['title']; ?></td>
			    	<td><?php echo join(',', $post['tags']); ?></td>
			    	<td><?php echo $post['user']['user_name']; ?></td>
			    	<td><?php echo $post['date_created']; ?></td>
			    	<td><?php echo $post['date_updated']; ?></td>
			    	<td><?php echo $post['live'] ? 'Yes' : 'No'; ?></td>
			    	<td>
			    		|&nbsp;
			    		<a href="./post.php?action=edit&id=<?php echo $post['blog_post_id']; ?>">Edit</a> |&nbsp;
			    		<a href="./post.php?action=delete&id=<?php echo $post['blog_post_id']; ?>">Delete</a> |&nbsp;
			    		<a href="./post.php?action=toggle&id=<?php echo $post['blog_post_id']; ?>">Toggle Published</a> |&nbsp;
			    	</td>
			    </tr>
			    <?php
			}
			?>
		</table>
	</body>
</html>