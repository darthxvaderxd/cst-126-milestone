<?php
/**
	Written by: Richard Williamson
	This is the html for blog post viewing
 */ 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Richards Blog</title>
		<style>
		  input[type=text] { min-width: 450px; }
		  textarea { min-width: 500px; min-height: 250px; }
		  div { padding: 0.4rem; }
		  .post { background-color: #ccc; border: 1px solid #222; width: 550px; margin: auto; }
		  .error { color: #f00; }
		</style>
	</head>
	<body>
		<form action="post.php" method="POST">
    		<div align="center" class="post">
    			<h2>New Blog Post</h2>
    			<?php
    			if (!empty($error)) {
    			    ?>
    			    <div class="error">
    			    	<?php echo $error; ?>
    			    </div>
    			    <?php 
    			}
    			?>
    			<div>
    				<label for="title">Title: </label>
    				<?php echo $title ?? "None"; ?>
    			</div>
    			<div>
    				<?php echo $body ?? ""; ?>
    			</div>
    		</div>
		</form>
	</body>
</html>