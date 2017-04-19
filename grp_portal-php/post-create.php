<?php
//Post creation endpoint
require_once '../grplib-php/init.php';

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	include_once '404.php';
	exit();
}
else {
	// Load communities so restrictions can be placed?
	$sql_communityrest = 'SELECT * FROM communities WHERE communities.community_id = "' . mysqli_real_escape_string($mysql, $_POST['community_id']) . '"';
    $result_communityrest = mysqli_query($mysql, $sql_communityrest);
if(mysqli_num_rows($result_communityrest) == 0) {
            $error_message[] = 'Community could not be found.';
			$error_code[] = '1512004';	
}
	
	$row_communityrest = mysqli_fetch_assoc($result_communityrest);
	
		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to create a post.';
			$error_code[] = '1512005';
        }
else {
$sql_post_getposter = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_post_getposter = mysqli_query($mysql, $sql_post_getposter);
$row_post_getposter = mysqli_fetch_assoc($result_post_getposter); 
if(!isset($grp_config_max_postbuffertime)) {
$grp_config_max_postbuffertime = 10; }

        if(mysqli_num_rows(mysqli_query($mysql, 'SELECT posts.pid, posts.created_at FROM posts WHERE posts.pid = "'.$row_post_getposter['pid'].'" AND posts.created_at > NOW() - '.$grp_config_max_postbuffertime.' ORDER BY posts.created_at DESC LIMIT 5')) != 0) {
			$error_message[] = 'Multiple posts cannot be made in such a short period of time. Please try posting again later.';
			$error_code[] = '1515918';	
		}
		
        if(strval($row_post_getposter['status'] >= 2) || isset($row_current_peopleban)) {
			$error_message[] = 'You are not permitted to post.';
			$error_code[] = '1512006';
		}

	if(strval($row_communityrest['min_perm'] > $row_post_getposter['privilege']) && !empty($row_communityrest['allowed_pids']) && !in_array($row_post_getposter['pid'], explode(', ', $row_communityrest['allowed_pids']))) {
			$error_message[] = 'You are not permitted to post here.';
			$error_code[] = '1512006';
			}
elseif(strval($row_communityrest['min_perm'] > $row_post_getposter['privilege']) && empty($row_communityrest['allowed_pids'])) {
			$error_message[] = 'You are not permitted to post here.';
			$error_code[] = '1512006';
}
	
	// Is the post body too long?
        if(strlen($_POST['body']) <= 0)
        {
            $error_message[] = 'The content you have entered is blank.\nPlease enter content into your post.';
			$error_code[] = '1515001';
        }
        if(preg_replace( '/[\x{200B}-\x{200D}]/u', '', $_POST['body'] ) == '') {
            $error_message[] = 'The content you have entered is blank.\nPlease enter content into your post.';
			$error_code[] = '1515001';
}
if(ctype_space(preg_replace( '/[\x{200B}-\x{200D}]/u', '', $_POST['body'] ))) {
            $error_message[] = 'The content you have entered is blank.\nPlease enter content into your post.';
			$error_code[] = '1515001';
}
		        if(strval($row_post_getposter['privilege']) <= 3 && strlen($_POST['body']) > 1000)
        {
            $error_message[] = 'You have exceeded the amount of characters that you can send.';
			$error_code[] = '1515002';
        }
		# start url checks
		if(isset($_POST['url'])) {
      	if(strlen($_POST['url']) >= 1) {
		if(strlen($_POST['url']) > 255)
        {
            $error_message[] = 'You have exceeded the amount of characters that you can send.';
			$error_code[] = '1515002';
        }
		if (substr($_POST['url'], 0, 4) != "http" && strlen($_POST['url']) >= 3) {
            $error_message[] = 'The URL you have specified is not of HTTP or HTTPS.';
			$error_code[] = '1515003';
        }
		if(strlen($_POST['url']) < 11 && strlen($_POST['url']) >= 3) {
		    $error_message[] = 'The URL you have specified is too short.';
			$error_code[] = '1515004';	
		}		
if (filter_var($_POST['url'], FILTER_VALIDATE_URL) === FALSE) {
		    $error_message[] = 'The URL you have specified is not valid.';
			$error_code[] = '1515005';			
        }
		}
}		

		if(isset($_POST['screenshot'])) {
      	if(strlen($_POST['screenshot']) >= 1) {
		if(strlen($_POST['screenshot']) > 255)
        {
            $error_message[] = 'You have exceeded the amount of characters that you can send.';
			$error_code[] = '1515002';
        }
		if (substr($_POST['screenshot'], 0, 5) != "https" && strlen($_POST['screenshot']) >= 3) {
            $error_message[] = 'The screenshot URL you have specified is not of HTTPS. Please use SSL!';
			$error_code[] = '1515003';
        }
		if(strlen($_POST['screenshot']) < 11 && strlen($_POST['screenshot']) >= 3) {
		    $error_message[] = 'The screenshot URL you have specified is too short.';
			$error_code[] = '1515004';	
		}
if (filter_var($_POST['screenshot'], FILTER_VALIDATE_URL) === FALSE) {
		    $error_message[] = 'The screenshot URL you have specified is not valid.';
			$error_code[] = '1515005';			
        }
		}
		}		

	if(!empty($_POST['screenshot'])) {
  $ch1 = curl_init();
  curl_setopt ($ch1, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($ch1, CURLOPT_URL, urldecode($_POST['screenshot']));
  curl_setopt ($ch1, CURLOPT_CONNECTTIMEOUT, 20);
  curl_setopt ($ch1, CURLOPT_USERAGENT, 'Nintendo Wii (http)');
  curl_setopt ($ch1, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch1, CURLOPT_HEADER, true); 
  curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, 'HEAD');
  curl_setopt($ch1, CURLOPT_NOBODY, true);
  $content1 = curl_exec($ch1);
  $contentType1 = curl_getinfo($ch1, CURLINFO_CONTENT_TYPE);
	if(substr($contentType1,0,5) != 'image' || $contentType1 == 'image/gif') {
		    $error_message[] = 'The screenshot you have specified is not valid.';
			$error_code[] = '1515005';	
}
	}		
		
		# end url checks
}
	    if(!empty($error_code) || !empty($error_message) ) /*Got errors?*/
    {
		// JSON response for errors.
			http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
			print '{"success":0,"errors":[{"message":"' . $error_message[0] . '","error_code":' . $error_code[0] . '}],"code":"400"}';
			print "\n";
    }
	else {
require 'lib/olv-url-enc.php';
$gen_olive_url = $b64url_data;

if(!isset($_POST['is_spoiler'])) {
$_POST['is_spoiler'] = false; }
if(empty($_POST['feeling_id']) || !is_numeric($_POST['feeling_id']) || strval($_POST['feeling_id']) >= 6) { $_POST['feeling_id'] = 0; } 
        $sql = "INSERT INTO
                    posts(id, pid, _post_type, feeling_id, platform_id, body, url, screenshot, community_id, is_spoiler)
                VALUES('" . $gen_olive_url  . "',
				       '" . mysqli_real_escape_string($mysql, $_SESSION['pid']) . "',
                       '" . htmlspecialchars(mysqli_real_escape_string($mysql, $_POST['_post_type'])) . "',
                       '" . htmlspecialchars(mysqli_real_escape_string($mysql, $_POST['feeling_id'])) . "',
                       '1',
                       '" . mysqli_real_escape_string($mysql, $_POST['body']) . "',
                       '" . (empty($_POST['url']) ? '' : mysqli_real_escape_string($mysql, $_POST['url'])) . "',
					   '" . (empty($_POST['screenshot']) ? '' : mysqli_real_escape_string($mysql, $_POST['screenshot'])) . "',
                       '" . mysqli_real_escape_string($mysql, $_POST['community_id']) . "',
                       '" . mysqli_real_escape_string($mysql, $_POST['is_spoiler']) . "')";
                         
        $result = mysqli_query($mysql, $sql);
        if(!$result)
        {
            //MySQL error; print jsON response.
			http_response_code(400);  
			header('Content-Type: application/json; charset=utf-8');
			
			// Enable in debug
			#print $sql;
			#print "\n\n";			
			
			print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"500"}';
			print "\n";
		}
		else {
			// HTML fragment success response.
			
			// Begins now.
	$sql_post_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $gen_olive_url . '"';
	$result_post_replies = mysqli_query($mysql, $sql_post_replies);
	$row_post_replies = mysqli_fetch_assoc($result_post_replies);
	
	$sql_post_empathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $gen_olive_url . '"';
	$result_post_empathies = mysqli_query($mysql, $sql_post_empathies);
	$row_post_empathies = mysqli_fetch_assoc($result_post_empathies);
	
	$sql_post_created = 'SELECT * FROM posts WHERE posts.id = "' . $gen_olive_url . '"';
	$result_post_created = mysqli_query($mysql, $sql_post_created);
	$row_post_created = mysqli_fetch_assoc($result_post_created);
	
    $sql_post_poster = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
    $result_post_poster = mysqli_query($mysql, $sql_post_poster);
    $row_post_poster = mysqli_fetch_assoc($result_post_poster); 
			
			# Define feeling IDs to be used later.

$template_creator_pid = $_SESSION['pid'];
$template_creator_user_id = htmlspecialchars($row_post_poster['user_id']);
$template_creator_screen_name = $row_post_poster['screen_name'];
$template_creator_mii_hash = $row_post_poster['mii_hash'];
$template_creator_user_face = $row_post_poster['user_face'];
$template_creator_official_user = $row_post_poster['official_user'];
$template_post_id = $row_post_created['id'];
$template_post_pid = $row_post_created['pid'];
$template_post_type = $row_post_created['_post_type'];
$template_post_is_hidden = $row_post_created['is_hidden'];
$template_post_body = htmlspecialchars($row_post_created['body']);
$template_post_url = htmlspecialchars($row_post_created['url']);
$template_post_screenshot = htmlspecialchars($row_post_created['screenshot']);
$template_post_created_at = $row_post_created['created_at'];
$template_post_spoiler = htmlspecialchars($row_post_created['is_spoiler']);
$template_post_feeling_id = htmlspecialchars($row_post_created['feeling_id']);

$template_result_post_empathies = $result_post_empathies;
$template_result_post_replies = $result_post_replies;
include 'lib/postlist-post-template.php';

// Ends here.

		
		}
		}

        }
