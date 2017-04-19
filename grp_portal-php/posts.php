<?php
require_once '../grplib-php/init.php';

if(empty($_GET['id'])) { include_once '404.php'; exit(); }
# If /posts/*/empathies, /posts/*/replies, /posts/*/violations, etc. is specified.
if(isset($_GET['mode'])) {
if($_GET['mode'] == 'empathies') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404.php'; }
else {
# Method is POST.

		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to give Yeahs to posts or comments.';
			$error_code[] = '1512005';
        }

if($_SESSION['pid']) {		
$sql_post_getuser = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_post_getuser = mysqli_query($mysql, $sql_post_getuser);
$row_post_getuser = mysqli_fetch_assoc($result_post_getuser); 

 if(strval($row_post_getuser['status'] >= 3) || strval($row_post_getuser['empathy_restriction'] >= 1)) {
			$error_message[] = 'You are not permitted to give Yeahs.';
			$error_code[] = '1512006';
		}
}
$sql_empathywho = 'SELECT * FROM posts WHERE posts.id = "' . mysqli_real_escape_string($mysql, $_GET['id']) . '"';
$result_empathywho = mysqli_query($mysql, $sql_empathywho);

if(mysqli_num_rows($result_empathywho)==0) {
			$error_message[] = 'The post could not be found.';
			$error_code[] = '1512007';	
        }
if(mysqli_fetch_assoc($result_empathywho)['pid']==$_SESSION['pid']) {
			$error_message[] = 'You cannot give a Yeah to your own post.';
			$error_code[] = '1512008';				  
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
$sql_hasempathy = 'SELECT * FROM empathies WHERE empathies.id = "' . mysqli_real_escape_string($mysql, $_GET['id']) . '" AND empathies.pid = "' . $_SESSION['pid'] . '"';
$result_hasempathy = mysqli_query($mysql, $sql_hasempathy);
if(mysqli_num_rows($result_hasempathy)!=0) {
$sql_empathyremove = 'DELETE FROM empathies WHERE empathies.id = "' . mysqli_real_escape_string($mysql, $_GET['id']) . '" AND empathies.pid = "' . $_SESSION['pid'] . '"';
$result_empathyremove = mysqli_query($mysql, $sql_empathyremove);
if(!$result_empathyremove){
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"400"}';
print "\n";
} else {
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}';
print "\n";
}   }  else {
        $sql_empathycreate = 'INSERT INTO
                    empathies(id, pid, created_from)
                VALUES ("' . mysqli_real_escape_string($mysql, $_GET['id']) . '",
                        "' . $_SESSION['pid'] . '",
						"' . $_SERVER['REMOTE_ADDR'] . '")';
        $result_empathycreate = mysqli_query($mysql, $sql_empathycreate);
		
        $sql_get_empathy_post = 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';
        $result_get_empathy_post = mysqli_query($mysql, $sql_get_empathy_post);
        $row_get_empathy_post = mysqli_fetch_assoc($result_get_empathy_post);	
		
        $sql_get_empathy_poster = 'SELECT * FROM people WHERE people.pid = "'.$row_get_empathy_post['pid'].'"';
		$result_get_empathy_poster = mysqli_query($mysql, $sql_get_empathy_poster);
		$row_get_empathy_poster = mysqli_fetch_assoc($result_get_empathy_poster);

	// If the user gave the same type of notification 8 seconds ago, then don't send this.
	$result_check_fastnews = mysqli_query($mysql, 'SELECT news.to_pid, news.created_at FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.news_context = "2" AND news.created_at > NOW() - 8 ORDER BY news.created_at DESC');
    if(mysqli_num_rows($result_check_fastnews) == 0) {
    $result_check_ownusernews = mysqli_query($mysql, 'SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.news_context = "2" AND news.id = "'.$row_get_empathy_post['id'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
$row_check_ownusernews = mysqli_fetch_assoc($result_check_ownusernews);
	$result_check_mergedusernews = mysqli_query($mysql, 'SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.news_context = "2" AND news.id = "'.$row_get_empathy_post['id'].'" AND news.merged IS NOT NULL AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if(mysqli_num_rows($result_check_mergedusernews) != 0) {
	$result_update_mergedusernewsagain = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.mysqli_fetch_assoc($result_check_mergedusernews)['merged'].'"');	
	}
	elseif(mysqli_num_rows($result_check_ownusernews) != 0) {
	$result_update_ownusernewsagain = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$row_check_ownusernews['news_id'].'"');
	}
else {
	$result_update_newsmergesearch = mysqli_query($mysql, 'SELECT * FROM news WHERE news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.id = "'.$row_get_empathy_post['id'].'" AND news.created_at > NOW() - 7200 AND news.news_context = "2" ORDER BY news.created_at DESC');	
	if(mysqli_num_rows($result_update_newsmergesearch) != 0) {
$row_update_newsmergesearch = mysqli_fetch_assoc($result_update_newsmergesearch);
	
	$result_newscreatemerge = mysqli_query($mysql, 'INSERT INTO news(from_pid, to_pid, id, merged, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_get_empathy_poster['pid'].'", "'.$row_get_empathy_post['id'].'", "'.$row_update_newsmergesearch['news_id'].'", "2", "0")');
$result_update_newsformerge = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = NOW() WHERE news.news_id = "'.$row_update_newsmergesearch['news_id'].'"');
		}
else {
        $result_newscreate = mysqli_query($mysql, 'INSERT INTO news(from_pid, to_pid, id, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_get_empathy_poster['pid'].'", "'.$row_get_empathy_post['id'].'", "2", "0")'); 	
	} }
		
		
	
        if(!$result_empathycreate)
        {
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"400"}';
print "\n";
	}
        else
        {
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}';
print "\n";
        }
		
   }
  }
	}
}
} 
if($_GET['mode'] == 'replies') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404.php'; }
else { 
		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to create a post.';
			$error_code[] = '1512005';
        }
		
if($_SESSION['pid']) {		
$sql_post_getreplier = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_post_getreplier = mysqli_query($mysql, $sql_post_getreplier);
$row_post_getreplier = mysqli_fetch_assoc($result_post_getreplier); 
if(!isset($grp_config_max_postbuffertime)) {
$grp_config_max_postbuffertime = 10; }

        if(mysqli_num_rows(mysqli_query($mysql, 'SELECT replies.pid, replies.created_at FROM replies WHERE replies.pid = "'.$row_post_getreplier['pid'].'" AND replies.created_at > NOW() - '.$grp_config_max_postbuffertime.' ORDER BY replies.created_at DESC LIMIT 5')) != 0) {
			$error_message[] = 'Multiple posts cannot be made in such a short period of time. Please try posting again later.';
			$error_code[] = '1515918';	
		} 

        if(strval($row_post_getreplier['status'] >= 3) || isset($row_current_peopleban)) {
			$error_message[] = 'You are not permitted to reply to posts.';
			$error_code[] = '1512006';
		}
}
	
	// Is the post body too long?
        if(strlen($_POST['body']) <= 0 || empty($_POST['body']) )
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
		        if(strlen($_POST['body']) > 1000)
        {
            $error_message[] = 'You have exceeded the amount of characters that you can send.';
			$error_code[] = '1515002';
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
#print urldecode($_POST['screenshot']);
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
                    replies(id, reply_to_id, pid, feeling_id, platform_id, body, screenshot, is_spoiler)
                VALUES('" . $gen_olive_url  . "',
				       '" . mysqli_real_escape_string($mysql, $_GET['id']) . "',
				       '" . mysqli_real_escape_string($mysql, $_SESSION['pid']) . "',
                       '" . htmlspecialchars(mysqli_real_escape_string($mysql, $_POST['feeling_id'])) . "',
                       '1',
                       '" . mysqli_real_escape_string($mysql, $_POST['body']) . "',
					   '" . (isset($_POST['screenshot']) ? mysqli_real_escape_string($mysql, $_POST['screenshot']) : '') . "',
                       '" . mysqli_real_escape_string($mysql, $_POST['is_spoiler']) . "')";
                         
        $result = mysqli_query($mysql, $sql);
		
		$sql_get_empathy_post = 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';
        $result_get_empathy_post = mysqli_query($mysql, $sql_get_empathy_post);
        $row_get_empathy_post = mysqli_fetch_assoc($result_get_empathy_post);
		
        $sql_get_empathy_poster = 'SELECT * FROM people WHERE people.pid = "'.$row_get_empathy_post['pid'].'"';
		$result_get_empathy_poster = mysqli_query($mysql, $sql_get_empathy_poster);
		$row_get_empathy_poster = mysqli_fetch_assoc($result_get_empathy_poster);

		if($_SESSION['pid'] == $row_get_empathy_poster['pid']) {
		# send notifications to all commenters	
		$sql_news_getcomments = 'SELECT replies.pid FROM replies WHERE replies.reply_to_id = "'.$row_get_empathy_post['id'].'" AND replies.pid != "'.$row_get_empathy_poster['pid'].'" AND replies.is_hidden = "0" GROUP BY replies.pid';
		$result_news_getcomments = mysqli_query($mysql, $sql_news_getcomments);
		while($row_news_getcomments = mysqli_fetch_assoc($result_news_getcomments)) {
		$result_check_ownusernews = mysqli_query($mysql, 'SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_news_getcomments['pid'].'" AND news.news_context = "5" AND news.id = "'.$row_get_empathy_post['id'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if(mysqli_num_rows($result_check_ownusernews) != 0) {
	$result_update_ownusernewsagain = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.mysqli_fetch_assoc($result_check_ownusernews)['news_id'].'"');	
	}
		else {
		$result_news_send = mysqli_query($mysql, 'INSERT INTO news(from_pid, to_pid, id, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_news_getcomments['pid'].'", "'.mysqli_real_escape_string($mysql, $_GET['id']).'", "5", "0")'); }
		}
		}
		else {
	// If the user gave the same type of notification 8 seconds ago, then don't send this.
	$result_check_fastnews = mysqli_query($mysql, 'SELECT news.to_pid, news.created_at FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.news_context = "4" AND news.created_at > NOW() - 1 ORDER BY news.created_at DESC');
    if(mysqli_num_rows($result_check_fastnews) == 0) {
    $result_check_ownusernews = mysqli_query($mysql, 'SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.news_context = "4" AND news.id = "'.$row_get_empathy_post['id'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
$row_check_ownusernews = mysqli_fetch_assoc($result_check_ownusernews);
	$result_check_mergedusernews = mysqli_query($mysql, 'SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.id = "'.$row_get_empathy_post['id'].'" AND news.news_context = "4" AND news.merged IS NOT NULL AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if(mysqli_num_rows($result_check_mergedusernews) != 0) {
	$result_update_mergedusernewsagain = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.mysqli_fetch_assoc($result_check_mergedusernews)['merged'].'"');	
	}
	elseif(mysqli_num_rows($result_check_ownusernews) != 0) {
	$result_update_ownusernewsagain = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$row_check_ownusernews['news_id'].'"');
	}
else {
	$result_update_newsmergesearch = mysqli_query($mysql, 'SELECT * FROM news WHERE news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.id = "'.$row_get_empathy_post['id'].'" AND news.created_at > NOW() - 7200 AND news.news_context = "4" ORDER BY news.created_at DESC');	
	if(mysqli_num_rows($result_update_newsmergesearch) != 0) {
$row_update_newsmergesearch = mysqli_fetch_assoc($result_update_newsmergesearch);
	
	$result_newscreatemerge = mysqli_query($mysql, 'INSERT INTO news(from_pid, to_pid, id, merged, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_get_empathy_poster['pid'].'", "'.$row_get_empathy_post['id'].'", "'.$row_update_newsmergesearch['news_id'].'", "4", "0")');
$result_update_newsformerge = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = NOW() WHERE news.news_id = "'.$row_update_newsmergesearch['news_id'].'"');
		}
else {
        $result_newscreate = mysqli_query($mysql, 'INSERT INTO news(from_pid, to_pid, id, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_get_empathy_poster['pid'].'", "'.$row_get_empathy_post['id'].'", "4", "0")'); 	
	} }
		
		
	}			
		}

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
			// HTML fragment success response begins now.
	
	$sql_reply_empathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $gen_olive_url . '"';
	$result_reply_empathies = mysqli_query($mysql, $sql_reply_empathies);
	$row_reply_empathies = mysqli_fetch_assoc($result_reply_empathies);
	
	$sql_reply_created = 'SELECT * FROM replies WHERE replies.id = "' . $gen_olive_url . '"';
	$result_reply_created = mysqli_query($mysql, $sql_reply_created);
	$row_reply_created = mysqli_fetch_assoc($result_reply_created);
	
    $sql_reply_poster = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
    $result_reply_poster = mysqli_query($mysql, $sql_reply_poster);
    $row_reply_poster = mysqli_fetch_assoc($result_reply_poster); 
			
$template_creator_pid = $row_reply_poster['pid'];
$template_creator_user_id = $row_reply_poster['user_id'];
$template_creator_screen_name = $row_reply_poster['screen_name'];
$template_creator_mii_hash = $row_reply_poster['mii_hash'];
$template_creator_user_face = $row_reply_poster['user_face'];
$template_creator_official_user = $row_reply_poster['official_user'];
$template_reply_id = $row_reply_created['id'];
$template_reply_to_id = $row_reply_created['reply_to_id'];
$template_reply_pid = $row_reply_created['pid'];
$template_reply_body = $row_reply_created['body'];
$template_reply_screenshot = $row_reply_created['screenshot'];
$template_reply_is_hidden = $row_reply_created['is_hidden'];
$template_reply_created_at = $row_reply_created['created_at'];
$template_reply_spoiler = $row_reply_created['is_spoiler'];
$template_reply_feeling_id = $row_reply_created['feeling_id'];

$template_result_reply_empathies = $result_reply_empathies;
include 'lib/replylist-reply-template.php';
           

		   
			
		}

	
		
	}


   }
}
if($_GET['mode'] == 'violations') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404.php'; }
else {
# Method is POST.

		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.';
			$error_code[] = '1512005';
        }

if($_SESSION['pid']) {
$sql_post_getuser = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_post_getuser = mysqli_query($mysql, $sql_post_getuser);
$row_post_getuser = mysqli_fetch_assoc($result_post_getuser);

$result_whatpost = mysqli_query($mysql, 'SELECT * FROM posts WHERE posts.id = "' . mysqli_real_escape_string($mysql, $_GET['id']) . '"');

if(mysqli_num_rows($result_whatpost)==0) {
			$error_message[] = 'The post could not be found.';
			$error_code[] = '1512007';	
        }
if(mysqli_fetch_assoc($result_whatpost)['pid']==$_SESSION['pid']) {
			$error_message[] = 'You cannot report your own post.';
			$error_code[] = '1512008';				  
        }
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
$row_reportpost = mysqli_fetch_assoc($result_whatpost);

$result_get_spamreports = mysqli_query($mysql, 'SELECT * FROM reports WHERE reports.source = "'.$_SESSION['pid'].'" AND reports.created_at > NOW() - 5');
if(mysqli_num_rows($result_get_spamreports) != 0) {
header('Content-Type: application/json; charset=utf-8'); print '{"success":1}'; print "\n";
exit();
}

$result_reportcreate = mysqli_query($mysql, 'INSERT INTO reports (source, subject, type, reason, message) VALUES ("'.$_SESSION['pid'].'", "'.mysqli_real_escape_string($mysql, $_GET['id']).'", "0", "'.mysqli_real_escape_string($mysql, $_POST['type']).'", "'.mysqli_real_escape_string($mysql, $_POST['body']).'")');
        if(!$result_reportcreate)
        {
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"400"}';
print "\n";
	}
        else
        {
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}';
print "\n";
        }
		
}
} }
if($_GET['mode'] == 'set_spoiler') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404.php'; }
else {
# Put checks + update post spoiler here.	
		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to update posts.';
			$error_code[] = '1512005';
        }
	$sql_getposter_update = 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';
	$result_getposter_update = mysqli_query($mysql, $sql_getposter_update);
if(mysqli_num_rows($result_getposter_update) == 0) {
            $error_message[] = 'The post could not be found.';
$error_code[] = '1512019'; }
else {
	$row_getposter_update = mysqli_fetch_assoc($result_getposter_update);
	
	if(isset($_SESSION['pid']) && $row_getposter_update['pid'] != $_SESSION['pid']) {
	        $error_message[] = 'You are not the original poster of this post.';
			$error_code[] = '1512016';
	}
	if(strval($row_getposter_update['is_hidden']) == '1') {
	        $error_message[] = 'The post has been deleted.';
			$error_code[] = '1512017';
	}
	if($row_getposter_update['is_spoiler'] == '1') {
	        $error_message[] = 'The post is already a spoiler.';
			$error_code[] = '1512017';
	}

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
        $sql_update = 'UPDATE posts SET posts.is_spoiler = "1" WHERE posts.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';	
	    $result_update = mysqli_query($mysql, $sql_update);
        if(!$result_update)
        {
            //MySQL error; print jsON response.
			http_response_code(400);  
			header('Content-Type: application/json; charset=utf-8');
			
			// Enable in debug
			#print $sql_update;
			#print "\n\n";			
			
			print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"500"}';
			print "\n";
		}
		else { 
header('Content-Type: application/json; charset=utf-8');
print '{"is_spoiler":1,"success":1}'; 
}




}	
	
	
	}	
}
if($_GET['mode'] == 'screenshot_set_profile_post') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404.php'; }
else {
# Put checks + update user's favorite post here.
		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to update profiles.';
			$error_code[] = '1512005';
        }
	$sql_getposter_update = 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';
	$result_getposter_update = mysqli_query($mysql, $sql_getposter_update);
if(mysqli_num_rows($result_getposter_update) == 0) {
            $error_message[] = 'The post could not be found.';
$error_code[] = '1512019'; }
else {
	$row_getposter_update = mysqli_fetch_assoc($result_getposter_update);
	
	if(isset($_SESSION['pid']) && $row_getposter_update['pid'] != $_SESSION['pid']) {
	        $error_message[] = 'You are not the original poster of this post.';
			$error_code[] = '1512016';
	}
	if(strval($row_getposter_update['is_hidden']) == '1') {
	        $error_message[] = 'The post has been deleted.';
			$error_code[] = '1512017';
	}
	if(strlen($row_getposter_update['screenshot']) < 3) {
	        $error_message[] = 'This post does not contain a screenshot';
			$error_code[] = '1512018';
	}
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
        $sql_update_profile = 'UPDATE profiles SET profiles.favorite_screenshot = "'.mysqli_real_escape_string($mysql, $_GET['id']).'" WHERE profiles.pid = "'.$_SESSION['pid'].'"';	
	    $result_update_profile = mysqli_query($mysql, $sql_update_profile);
        if(!$result_update_profile)
        {
            //MySQL error; print jsON response.
			http_response_code(400);  
			header('Content-Type: application/json; charset=utf-8');
			
			// Enable in debug
			#print $sql_update;
			#print "\n\n";			
			
			print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"500"}';
			print "\n";
		}
		else { 
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}'; 
}




}	
}
	
	}
if($_GET['mode'] == 'delete') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404.php'; }
else {
# Put checks + update post deletion here.	
		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to delete posts.';
			$error_code[] = '1512005';
        }
	$sql_getposter_update = 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';
	$result_getposter_update = mysqli_query($mysql, $sql_getposter_update);
if(mysqli_num_rows($result_getposter_update) == 0) {
            $error_message[] = 'The post could not be found.';
$error_code[] = '1512019'; }
else {
	$row_getposter_update = mysqli_fetch_assoc($result_getposter_update);
	
	if(isset($_SESSION['pid']) && $row_getposter_update['pid'] != $_SESSION['pid']) {
	        $error_message[] = 'You are not the original poster of this post.';
			$error_code[] = '1512016';
	}
	if(strval($row_getposter_update['is_hidden']) == '1') {
	        $error_message[] = 'The post has already been deleted.';
			$error_code[] = '1512017';
	}

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
        $sql_update = 'UPDATE posts SET posts.is_hidden = "1", posts.hidden_resp = "1" WHERE posts.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';	
	    $result_update = mysqli_query($mysql, $sql_update);
        if(!$result_update)
        {
            //MySQL error; print jsON response.
			http_response_code(400);  
			header('Content-Type: application/json; charset=utf-8');
			
			// Enable in debug
			#print $sql_update;
			#print "\n\n";			
			
			print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"500"}';
			print "\n";
		}
		else {
$get_user_favoritepost = mysqli_query($mysql, 'SELECT profiles.favorite_screenshot FROM profiles WHERE profiles.pid = "'.$_SESSION['pid'].'" AND profiles.favorite_screenshot = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"');
		if(mysqli_num_rows($get_user_favoritepost) != 0) {
$delete_user_favoritepost = mysqli_query($mysql, 'UPDATE profiles SET profiles.favorite_screenshot = "" WHERE profiles.pid = "'.$_SESSION['pid'].'"');		
		}
print '    <title>Your Post</title>
<header id="header">
  
  <h1 id="page-title">Your Post</h1>

</header>

<div class="body-content track-error" id="post-permalink" data-track-error="deleted">
';
$no_content_message = 'Deleted by poster.';
include 'lib/no-content-window.php';
print '
  </div>
</div>';
		
}




}	
	
	
	}	
}


# Define other modes here; violations.

// Should I put this else in?
#else {
#include_once '404.php'; }
}  
  
# If normal use; /posts/*
else {
$sql_post = 'SELECT * FROM posts WHERE posts.id = "' . mysqli_real_escape_string($mysql, $_GET['id']) . '"';
$result_post = mysqli_query($mysql, $sql_post);
$row_post = mysqli_fetch_assoc($result_post);

$sql_post_user = 'SELECT * FROM people WHERE people.pid = "' . $row_post['pid'] . '"';
$result_post_user = mysqli_query($mysql, $sql_post_user);
$row_post_user = mysqli_fetch_assoc($result_post_user); 

// Who posesses the post?
if($row_post['pid']) {
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
if($_SESSION['pid'] == $row_post['pid']) {
$pagetitle = 'Your Post';
}
else {
$pagetitle = htmlspecialchars($row_post_user['screen_name']) . "'s post"; 
}    } 
else {
$pagetitle = htmlspecialchars($row_post_user['screen_name']) . "'s post"; }
}
else {
$pagetitle = 'Error'; }
require_once 'lib/htm.php';
printHeader(false);
printMenu();
// DB error.
if(!$result_post)
{
http_response_code(500);
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="500">';
$no_content_message = ( 'Server error.' );
include 'lib/no-content-window.php';
}
else
{
	// Post wasn't found.
    if($row_post == 0)
    {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The post could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
// Display post using $row_post!
    print $GLOBALS['div_body_head'];
# Is the post deleted?
if($row_post['is_hidden'] == 1) {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
print '<header id="header">
<h1 id="page-title">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" id="post-permalink" data-track-error="deleted">';
if($row_post['hidden_resp'] == 1) {
$no_content_message = ( 'Deleted by poster.' );
} if($row_post['hidden_resp'] == 0) {
require_once 'lib/olv-url-enc.php';
$no_content_message = 'Deleted by administrator.</p>
<p>Post ID: '.getPostID($row_post['id']);
}
include 'lib/no-content-window.php';
print $GLOBALS['div_body_head_end'];
print '</div>';
}
else {
if(empty($_SESSION['signed_in'])) {
$if_can_user_reply = ' disabled'; } else {
$row_my_replier1 = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_my_replier1 = mysqli_query($mysql, $row_my_replier1);
$row_my_replier1 = mysqli_fetch_assoc($result_my_replier1);
    if(isset($row_current_peopleban)) {
	$if_can_user_reply = ' disabled';		
	}
    elseif(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && 3 >= $row_my_replier1['status']) {
	$if_can_user_reply = ''; }
	else {
$if_can_user_reply = ' disabled'; } }
	print '<header id="header">
  <a id="header-reply-button"' . $if_can_user_reply . ' class="header-button reply-button' . $if_can_user_reply . '"'.($if_can_user_reply == '' ? 'href="#"' : '').' data-modal-open="#add-reply-page">Comment</a>
  
  <h1 id="page-title">' . $pagetitle . '</h1>

</header>';
# End this div after post-permalink-comments.
print '<div class="body-content post-subtype-default" id="post-permalink">';

	$sql_post_empathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_post['id'] . '" ORDER BY empathies.created_at DESC LIMIT 36';
	$result_post_empathies = mysqli_query($mysql, $sql_post_empathies);
	$row_post_empathies = mysqli_fetch_assoc($result_post_empathies);
if(mysqli_num_rows($result_post_empathies)!=0) {
$post_view_has_empathy = ''; }
else {
$post_view_has_empathy = ' class="no-empathy"'; }
	print '<div id="post-permalink-content"' . $post_view_has_empathy . '>';
	
	# Get community & title data.
	$sql_post_community = 'SELECT * FROM communities WHERE communities.community_id = "' . $row_post['community_id'] . '"';
	$result_post_community = mysqli_query($mysql, $sql_post_community);
	$row_post_community = mysqli_fetch_assoc($result_post_community);    
	$sql_post_title = 'SELECT * FROM titles WHERE titles.olive_title_id = "' . $row_post_community['olive_title_id'] . '"';
	$result_post_title = mysqli_query($mysql, $sql_post_title);
	$row_post_title = mysqli_fetch_assoc($result_post_title); 

	
	if(strlen($row_post_title['icon']) < 2) {
    $post_title_icon = 'https://miiverse.nintendo.net/img/title-icon-default.png';
	}
	else {
	$post_title_icon = $row_post_title['icon'];	
	}
	
	if(strlen($row_post_community['icon']) < 2) {
    $post_community_icon = 'https://miiverse.nintendo.net/img/title-icon-default.png';
	}
	else {
	$post_community_icon = $row_post_community['icon'];	
	}
	
if(strval($row_post_community['type']) == 5) {
print '<div id="post-permalink-header">

      <a data-pjax="#body" class="community"><img src="'.htmlspecialchars($post_community_icon).'" class="community-icon"> ' . htmlspecialchars($row_post_community['name']) . '</a>

      

    </div>';
}
else {
	print '<div id="post-permalink-header">
';
    $sql_communityscroll = 'SELECT * FROM communities WHERE communities.olive_title_id = "' . htmlspecialchars($row_post_title['olive_title_id']) . '"';
	$result_communityscroll = mysqli_query($mysql, $sql_communityscroll);
	$communityscroll_amt = mysqli_num_rows($result_communityscroll);
	if(($communityscroll_amt) >= 2) {				
print '
<a href="/titles/' . htmlspecialchars($row_post_community['olive_title_id']) . '" data-pjax="#body" class="title"><span><img src="' . htmlspecialchars($post_title_icon) . '" class="title-icon"></span></a>';
	}
print '
      <a href="/titles/' . htmlspecialchars($row_post_community['olive_title_id']) . '/' . $row_post_community['olive_community_id'] . '" data-pjax="#body" class="community"><img src="' . $post_community_icon . '" class="community-icon"> ' . htmlspecialchars($row_post_community['name']) . '</a>
	  </div>';
}
if($row_post_user['mii_hash']) {
if(($row_post['feeling_id']) == '0') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_user['mii_hash'] . '_normal_face.png'; 
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
if(($row_post['feeling_id']) == '1') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_user['mii_hash'] . '_happy_face.png'; 
$mii_face_feeling = 'happy'; }
$mii_face_miitoo = htmlspecialchars('Yeah!');
if(($row_post['feeling_id']) == '2') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_user['mii_hash'] . '_like_face.png'; 
$mii_face_feeling = 'like';
$mii_face_miitoo = htmlspecialchars('Yeah♥'); }
if(($row_post['feeling_id']) == '3') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_user['mii_hash'] . '_surprised_face.png'; 
$mii_face_feeling = 'surprised';
$mii_face_miitoo = htmlspecialchars('Yeah!?'); }
if(($row_post['feeling_id']) == '4') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_user['mii_hash'] . '_frustrated_face.png'; 
$mii_face_feeling = 'frustrated';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
if(($row_post['feeling_id']) == '5') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_user['mii_hash'] . '_puzzled_face.png'; 
$mii_face_feeling = 'puzzled';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
}
else {
if($row_post_user['user_face']) {
$mii_face_output = htmlspecialchars($row_post_user['user_face']);
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!');
} else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
}

if(isset($_SESSION['pid'])) {	  
if($_SESSION['pid']) {
$sql_post_me = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_post_me = mysqli_query($mysql, $sql_post_me);
$row_post_me = mysqli_fetch_assoc($result_post_me); 	}  }

if($row_post_user['official_user'] == 1) {
$is_poster_official_user = ' official-user';
}
else {
$is_poster_official_user = ''; }
	  
      print '<div id="post-permalink-body"';
if($row_post_user['official_user'] == 1) {
print ' class="official-user"';	 }
	  print '>
      <a href="/users/' . htmlspecialchars($row_post_user['user_id']) . '" data-pjax="#body" class="user-icon-container' . $is_poster_official_user . '"><img src="' . $mii_face_output . '" class="user-icon"></a>';
	 
if($row_post_user['official_user'] == 1) {
	print '<p class="user-organization">' . htmlspecialchars($row_post_user['organization']) . '</p>
	';
} 
	 print '<p class="user-name">' . htmlspecialchars($row_post_user['screen_name']) . '<span class="user-id">' . htmlspecialchars($row_post_user['user_id']) . '</span></p>';
     print '<p class="timestamp">';
	 print humanTiming(strtotime($row_post['created_at']));
     print '<span class="spoiler-status">Spoilers</span>';
	 if($row_post['is_spoiler'] == '1') { 
	 print '<span class="spoiler-status spoiler">Spoilers</span>'; }
	 print '</p>';
	 print '<div class="post-content'.($row_post['_post_type'] == 'artwork' ? ' memo' : '').'">';
	 if(isset($row_post['screenshot'])) {
     if(strlen($row_post['screenshot']) > 3) {
	print '<div class="capture-container">
           <img src="' . htmlspecialchars($row_post['screenshot']) . '" class="capture">
	 </div>'; } }
	 if($row_post['_post_type'] == 'artwork') {
print '<p class="post-content-memo"><img src="'.htmlspecialchars($row_post['body']).'" class="post-memo"></p>

	 </div>'; } else {
	 print '

              <p class="post-content-text">' . htmlspecialchars($row_post['body']) . '</p>
	 </div>'; }
	  if(isset($row_post['url'])) {
if (strpos( $row_post['url'], 'www.youtube.com/watch?v=') !== false) {
if(substr($row_post['url'], 0, 4) == "http") {
$post_yt_url = substr($row_post['url'], 31, 11);
}
if(substr($row_post['url'], 0, 5) == "https") {
$post_yt_url = substr($row_post['url'], 32, 11);
}
print '<div id="post-video">
        <iframe id="post-video-player" data-video-id="' . $post_yt_url . '" frameborder="0" allowfullscreen="1" title="YouTube video player" width="900" height="504" src="https://www.youtube.com/embed/' . $post_yt_url . '?rel=0&amp;modestbranding=1&amp;iv_load_policy=3&amp;enablejsapi=1&amp;widgetid=1"></iframe>

      </div>';
}
	else {
if(substr($row_post['url'], 0, 4) == "http" || substr($row_post['url'], 0, 5) == "https") {
$post_standard_url = true;
}
	  }
  }

if(isset($_SESSION['pid'])) {  
if($_SESSION['pid'] == $row_post['pid']) {
$post_meta_button_output = '<a href="#" role="button" class="edit-button edit-post-button" data-modal-open="#edit-post-page">Edit</a>';	}
else {
require_once 'lib/olv-url-enc.php';
$is_report_disabled = (strval($row_post_user['official_user']) == 1 || isset($row_current_peopleban) ? ' disabled' : '');
$post_meta_button_output = '<a '.(strval($row_post_user['official_user']) == 1 || isset($row_current_peopleban) ? '' : 'href="#" ').' role="button"'.$is_report_disabled.' class="report-button'.$is_report_disabled.'" data-modal-open="#report-violation-page" data-screen-name="'.htmlspecialchars($row_post_user['screen_name']).'" data-support-text="'.getPostID($row_post['id']).'" data-action="/posts/'.$row_post['id'].'/violations" data-is-post="1" data-is-permalink="1" data-can-report-spoiler="'.(strval($row_post['is_spoiler']) == 1 ? '1' : '0').'" data-community-id="'.$row_post_community['olive_community_id'].'" data-url-id="'.$row_post['id'].'" data-track-label="default" data-title-id="'.$row_post_community['olive_title_id'].'" data-track-action="openReportModal" data-track-category="reportViolation">Report Violation</a>'; }
 }
else {
$post_meta_button_output = '<a disabled="" role="button" class="report-button disabled">Report Violation</a>';	 
 }
    if(isset($row_current_peopleban)) {
	$can_post_user_miitoo = ' disabled';		
	}
    elseif(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && $row_post_user['pid'] != $_SESSION['pid']) {
	$can_post_user_miitoo = ''; }
	else {
	$can_post_user_miitoo = ' disabled'; }
	

        // Has the user given this post an empathy?
if(!empty($_SESSION['pid'])) {  		
$sql_hasempathy = 'SELECT * FROM empathies WHERE empathies.id = "' . mysqli_real_escape_string($mysql, $row_post['id']) . '" AND empathies.pid = "' . $_SESSION['pid'] . '"';
$result_hasempathy = mysqli_query($mysql, $sql_hasempathy);
}

$sql_empathyamt = 'SELECT * FROM empathies WHERE empathies.id = "' . mysqli_real_escape_string($mysql, $row_post['id']) . '"';
$result_empathyamt = mysqli_query($mysql, $sql_empathyamt);
if(!empty($_SESSION['pid']) && mysqli_num_rows($result_hasempathy)!=0) {
    $mii_face_miitoo = 'Unyeah';
	$has_post_miitoo_given_v2 = ''; 
    $has_post_miitoo_given = ' empathy-added';
	$has_post_miitoo_given_snd = 'SE_WAVE_MII_CANCEL';
    $post_miitoo_amt_other = mysqli_num_rows($result_empathyamt) - 1;	}
	else {
	$has_post_miitoo_given_v2 = ' style="display: none;"';
	$has_post_miitoo_given = '';
	$has_post_miitoo_given_snd = 'SE_WAVE_MII_ADD';
    $post_miitoo_amt_other = mysqli_num_rows($result_empathyamt);	} 
	
	
	 print '<div class="post-meta">
          ' . $post_meta_button_output . '

        <div class="expression">
		';
        print '<button type="button" ' . $can_post_user_miitoo . ' 
		class="submit miitoo-button' . $has_post_miitoo_given . '" 
		data-feeling="' . $mii_face_feeling . '" 
		data-action="/posts/' . $row_post['id'] . '/empathies" 
		data-other-empathy-count="' . $post_miitoo_amt_other . '" 
		data-sound="' . $has_post_miitoo_given_snd . '" 
		data-community-id="' . $row_post_community['olive_community_id'] . '" 
		data-url-id="' . $row_post['id'] . '" 
		data-track-label="default" 
		data-title-id="' . $row_post_community['olive_title_id'] . '" 
		data-track-action="yeah" 
		data-track-category="empathy">' . $mii_face_miitoo . '</button>
        </div>';
	  if(isset($post_standard_url)) {
      if($post_standard_url == true) {
	  print '<a href="#" class="link-button" data-modal-open="#confirm-url-page"></a>'; }
	  }
	  print '

      </div>';	  
	  # Please implement Miis representing empathies in posts.
	  print '</div>
	  <div class="post-permalink-feeling">
      <p class="post-permalink-feeling-text"></p>
	  ';
	  
	  if(!empty($_SESSION['pid'])) {
	  if($row_post_me['mii_hash']) {
	  $my_mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_me['mii_hash'] . '_' . $mii_face_feeling . '_face.png';
	  }
	  else {
	  if($row_post_me['user_face']) {
	  $my_mii_face_output = $row_post_me['user_face'];
	  }
	  else {
	  $my_mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
	  }
	  }

	 # User's own empathy.

if($row_post_me['official_user'] == 1) {
$is_yeaher_official_user = ' official-user';
}
else {
$is_yeaher_official_user = ''; }

	 print '<div class="post-permalink-feeling-icon-container"><a href="/users/' . htmlspecialchars($_SESSION['user_id']) . '" data-pjax="#body" class="post-permalink-feeling-icon visitor' . $is_yeaher_official_user . '"' . $has_post_miitoo_given_v2 . '><img src="' . htmlspecialchars($my_mii_face_output) . '" class="user-icon"></a>'; 
	  }
	 # Put other users' empathies here.

	$sql_post_empathies2 = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_post['id'] . '" ORDER BY empathies.created_at DESC LIMIT 36';
	$result_post_empathies2 = mysqli_query($mysql, $sql_post_empathies2);	 
	    while($row_post_empathies2 = mysqli_fetch_assoc($result_post_empathies2)) {	

$sql_post_empathies_user = 'SELECT * FROM people WHERE people.pid = "' . $row_post_empathies2['pid'] . '"';
$result_post_empathies_user = mysqli_query($mysql, $sql_post_empathies_user);
$row_post_empathies_user = mysqli_fetch_assoc($result_post_empathies_user);	
		# Don't display your own Mii!
		if(isset($_SESSION['pid']) && $row_post_empathies_user['pid']==$_SESSION['pid']) {
		print null; }
		else {
			
if($row_post_empathies_user['official_user'] == 1) {
$is_yeaher_official_user = ' official-user';
}
else {
$is_yeaher_official_user = ''; }
		
	  if($row_post_empathies_user['mii_hash']) {
	  $our_mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_empathies_user['mii_hash'] . '_' . $mii_face_feeling . '_face.png';
	  }
	  else {
	  if($row_post_empathies_user['user_face']) {
	  $our_mii_face_output = $row_post_empathies_user['user_face'];
	  }
	  else {
	  $our_mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
	  }
	  }
			
		print '<a href="/users/' . $row_post_empathies_user['user_id'] . '" data-pjax="#body" class="post-permalink-feeling-icon' . $is_yeaher_official_user . '"><img src="' . $our_mii_face_output . '" class="user-icon"></a>';	
		
		}
}
		
	 # End of empathies
     print '</div>'; 	   
   # End of post_permalink_content
	  if(isset($_SESSION['signed_in'])) {
   print '</div>';
	  }
	print '</div>';
# Put comments here.	
    print '<div id="post-permalink-comments">

<ul class="post-permalink-reply">
';

	$sql_post_reply = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . mysqli_real_escape_string($mysql, $_GET['id']) . '" ORDER BY replies.created_at';
	$result_post_reply = mysqli_query($mysql, $sql_post_reply);

    while($row_post_reply = mysqli_fetch_assoc($result_post_reply)) {
	
	$sql_reply_poster = 'SELECT * FROM people WHERE people.pid = "' . $row_post_reply['pid'] . '"';
    $result_reply_poster = mysqli_query($mysql, $sql_reply_poster);
    $row_reply_poster = mysqli_fetch_assoc($result_reply_poster);
	
	$sql_reply_empathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_post_reply['id'] . '"';
	$result_reply_empathies = mysqli_query($mysql, $sql_reply_empathies);
	$row_reply_empathies = mysqli_fetch_assoc($result_reply_empathies);
	
$template_creator_pid = $row_reply_poster['pid'];
$template_creator_user_id = $row_reply_poster['user_id'];
$template_creator_screen_name = $row_reply_poster['screen_name'];
$template_creator_mii_hash = $row_reply_poster['mii_hash'];
$template_creator_user_face = $row_reply_poster['user_face'];
$template_creator_official_user = $row_reply_poster['official_user'];
$template_reply_id = $row_post_reply['id'];
$template_reply_to_id = $row_post_reply['reply_to_id'];
$template_reply_pid = $row_post_reply['pid'];
$template_reply_body = $row_post_reply['body'];
$template_reply_screenshot = $row_post_reply['screenshot'];
$template_reply_is_hidden = $row_post_reply['is_hidden'];
$template_reply_hidden_resp = $row_post_reply['hidden_resp'];
$template_reply_created_at = $row_post_reply['created_at'];
$template_reply_spoiler = $row_post_reply['is_spoiler'];
$template_reply_feeling_id = $row_post_reply['feeling_id'];

$template_result_reply_empathies = $result_reply_empathies;
include 'lib/replylist-reply-template.php';
	}
print '</ul>

  </div>';	
	print '</div>';
	# Add reply page	
	
print '<div id="add-reply-page" class="add-post-page ';

if(isset($_SESSION['pid'])) {	
if(strval($lookup_user['image_perm']) >= 1) {
	print 'official-user-post ';
  }
}
print 'none" data-modal-types="add-entry add-reply require-body preview-body" data-is-template="1">
  <header class="add-post-page-header">
    <h1 class="page-title">Comment on ';
	print $pagetitle;
print '	
    </h1>
  </header>
  <form method="post" action="/posts/' . $row_post['id'] . '/replies" class="test-reply-form">

    <div class="add-post-page-content">
';
    if(isset($_SESSION['signed_in'])) {
	if($lookup_user['mii_hash']) {
	print '<div class="feeling-selector expression">
  <img src="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_normal_face.png" class="icon">
  <ul class="buttons"><li class="checked"><input type="radio" name="feeling_id" value="0" class="feeling-button-normal" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_normal_face.png" checked="" data-sound="SE_WAVE_MII_FACE_00"></li><li><input type="radio" name="feeling_id" value="1" class="feeling-button-happy" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_happy_face.png" data-sound="SE_WAVE_MII_FACE_01"></li><li><input type="radio" name="feeling_id" value="2" class="feeling-button-like" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_like_face.png" data-sound="SE_WAVE_MII_FACE_02"></li><li><input type="radio" name="feeling_id" value="3" class="feeling-button-surprised" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_surprised_face.png" data-sound="SE_WAVE_MII_FACE_03"></li><li><input type="radio" name="feeling_id" value="4" class="feeling-button-frustrated" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_frustrated_face.png" data-sound="SE_WAVE_MII_FACE_04"></li><li><input type="radio" name="feeling_id" value="5" class="feeling-button-puzzled" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_puzzled_face.png" data-sound="SE_WAVE_MII_FACE_05"></li>  </ul>
</div>';
	}
	if(isset($lookup_user['user_face'])) {
	if($lookup_user['user_face']) {	
	print '<div class="feeling-selector expression">
  <img src="' . htmlspecialchars($lookup_user['user_face']) . '" class="icon">
  
</div>';
	}
   }
  }

  # Re-add data-community-id, post ID, title ID at the bottom.
print '

      <div class="textarea-container textarea-with-menu active-text">
        <menu class="textarea-menu">
          <li><label class="textarea-menu-text  checked">
              <input type="radio" name="_post_type" value="body" checked="" data-sound="">
          </label></li>
          
            <li class="test-painting-tab"><label class="textarea-menu-memo">
                <input type="radio" name="_post_type" value="painting" data-sound="">
            </label></li>
          
        </menu>
        <textarea type="text" name="body" class="textarea-text" value="" maxlength="1000" placeholder="Add a comment here."></textarea>
        <div class="textarea-memo trigger" data-sound=""><div class="textarea-memo-preview"></div><input type="hidden" name="painting"></div>
      </div>
	  ';
	 if(strval($lookup_user['image_perm']) >= 1) {
	 print '<input type="text" class="textarea-line url-form" name="screenshot" placeholder="Screenshot URL" maxlength="255">';
	 }
print '
      <label class="spoiler-button checkbox-button">
        Spoilers
        <input type="checkbox" name="is_spoiler" value="1">
      </label>
    </div>
      <input type="button" class="olv-modal-close-button fixed-bottom-button left" value="Cancel" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button fixed-bottom-button" value="Post" data-track-label="default" data-track-action="sendReply" data-track-category="reply" data-post-content-type="text" data-post-with-screenshot="nodata">
  </form>
</div>';
	# Posts footer, mandatory for a posts page.
	$template_post_type_uri = 'posts';
	$template_post_end_uri = $row_post['id'];
	if($row_post['is_spoiler'] == '1') {
	$template_has_post_spoiler = ' disabled'; }
	else {
	$template_has_post_spoiler = ''; }
	include 'lib/posts-footer.php';
	  if(isset($post_standard_url)) {
      if($post_standard_url == true) {
	print '<div id="confirm-url-page" class="window-page none" data-modal-types="confirm-url">
  <div class="window">
    <h1 class="window-title">Open Link</h1>
    <div class="window-body"><div class="window-body-inner">
      <p>This web page will be displayed in the Internet browser.<br>
<br>
Do you want to close Miiverse and view this web page?</p>
      <p class="link-url">' . htmlspecialchars($row_post['url']) . '</p>
    </div></div>          <div class="window-bottom-buttons">
      <input type="button" class="olv-modal-close-button button" value="Back" data-sound="SE_WAVE_CANCEL">
      <input type="submit" class="post-button button" value="Open Link">';
	      }
	  }
				}				
}            
}
print $GLOBALS['div_body_head_end'];
(empty($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');
}
