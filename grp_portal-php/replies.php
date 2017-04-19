<?php
require_once '../grplib-php/init.php';

if(empty($_GET['id'])) { include_once '404.php'; exit(); }
# If /replies/*/empathies, /replies/*/violations, etc. is specified.
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
$sql_reply_getuser = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_reply_getuser = mysqli_query($mysql, $sql_reply_getuser);
$row_reply_getuser = mysqli_fetch_assoc($result_reply_getuser); 
		
        if(strval($row_reply_getuser['status'] >= 3) || strval($row_reply_getuser['empathy_restriction'] >= 1)) {
			$error_message[] = 'You are not permitted to give Yeahs.';
			$error_code[] = '1512006';
		}
}
$sql_empathywho = 'SELECT * FROM replies WHERE replies.id = "' . $_GET['id'] . '"';
$result_empathywho = mysqli_query($mysql, $sql_empathywho);

if(mysqli_num_rows($result_empathywho)==0) {
			$error_message[] = 'The reply could not be found.';
			$error_code[] = '1512007';	
        }
if(mysqli_fetch_assoc($result_empathywho)['pid']==$_SESSION['pid']) {
			$error_message[] = 'You cannot give a Yeah to your own reply.';
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
print 'remove';
print "\n";
} else {
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}';
print "\n";
}   }  else {
        $sql_empathycreate = 'INSERT INTO
                    empathies(id, pid, created_from)
                VALUES ("' . $_GET['id'] . '",
                        "' . $_SESSION['pid'] . '",
						"' . $_SERVER['REMOTE_ADDR'] . '")';
        $result_empathycreate = mysqli_query($mysql, $sql_empathycreate);
		
        $sql_get_empathy_post = 'SELECT * FROM replies WHERE replies.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';
        $result_get_empathy_post = mysqli_query($mysql, $sql_get_empathy_post);
        $row_get_empathy_post = mysqli_fetch_assoc($result_get_empathy_post);	
		
        $sql_get_empathy_poster = 'SELECT * FROM people WHERE people.pid = "'.$row_get_empathy_post['pid'].'"';
		$result_get_empathy_poster = mysqli_query($mysql, $sql_get_empathy_poster);
		$row_get_empathy_poster = mysqli_fetch_assoc($result_get_empathy_poster);

	// If the user gave the same type of notification 8 seconds ago, then don't send this.
	$result_check_fastnews = mysqli_query($mysql, 'SELECT news.to_pid, news.created_at FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.news_context = "3" AND news.created_at > NOW() - 8 ORDER BY news.created_at DESC');
    if(mysqli_num_rows($result_check_fastnews) == 0) {
    $result_check_ownusernews = mysqli_query($mysql, 'SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.news_context = "3" AND news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.id = "'.$row_get_empathy_post['id'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
$row_check_ownusernews = mysqli_fetch_assoc($result_check_ownusernews);
	$result_check_mergedusernews = mysqli_query($mysql, 'SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.news_context = "3" AND news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.id = "'.$row_get_empathy_post['id'].'" AND news.merged IS NOT NULL AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if(mysqli_num_rows($result_check_mergedusernews) != 0) {
	$result_update_mergedusernewsagain = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.mysqli_fetch_assoc($result_check_mergedusernews)['merged'].'"');	
	}
	elseif(mysqli_num_rows($result_check_ownusernews) != 0) {
	$result_update_ownusernewsagain = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$row_check_ownusernews['news_id'].'"');
	}
else {
	$result_update_newsmergesearch = mysqli_query($mysql, 'SELECT * FROM news WHERE news.to_pid = "'.$row_get_empathy_poster['pid'].'" AND news.id = "'.$row_get_empathy_post['id'].'" AND news.created_at > NOW() - 7200 AND news.news_context = "3" ORDER BY news.created_at DESC');	
	if(mysqli_num_rows($result_update_newsmergesearch) != 0) {
$row_update_newsmergesearch = mysqli_fetch_assoc($result_update_newsmergesearch);
	
	$result_newscreatemerge = mysqli_query($mysql, 'INSERT INTO news(from_pid, to_pid, id, merged, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_get_empathy_poster['pid'].'", "'.$row_get_empathy_post['id'].'", "'.$row_update_newsmergesearch['news_id'].'", "3", "0")');
$result_update_newsformerge = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = NOW() WHERE news.news_id = "'.$row_update_newsmergesearch['news_id'].'"');
		}
else {
        $result_newscreate = mysqli_query($mysql, 'INSERT INTO news(from_pid, to_pid, id, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_get_empathy_poster['pid'].'", "'.$row_get_empathy_post['id'].'", "3", "0")'); 	
	} }
		
		
	}
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

$result_whatpost = mysqli_query($mysql, 'SELECT * FROM replies WHERE replies.id = "' . mysqli_real_escape_string($mysql, $_GET['id']) . '"');

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

$result_reportcreate = mysqli_query($mysql, 'INSERT INTO reports (source, subject, type, reason, message) VALUES ("'.$_SESSION['pid'].'", "'.mysqli_real_escape_string($mysql, $_GET['id']).'", "1", "'.mysqli_real_escape_string($mysql, $_POST['type']).'", "'.mysqli_real_escape_string($mysql, $_POST['body']).'")');
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
	$sql_getposter_update = 'SELECT * FROM replies WHERE replies.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';
	$result_getposter_update = mysqli_query($mysql, $sql_getposter_update);
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


	if(!empty($error_code) || !empty($error_message) ) /*Got errors?*/
    {
		// JSON response for errors.
			http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
			print '{"success":0,"errors":[{"message":"' . $error_message[0] . '","error_code":' . $error_code[0] . '}],"code":"400"}';
			print "\n";
    }
else {
        $sql_update = 'UPDATE replies SET replies.is_spoiler = "1" WHERE replies.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';	
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
	$sql_getposter_update = 'SELECT * FROM replies WHERE replies.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';
	$result_getposter_update = mysqli_query($mysql, $sql_getposter_update);
	$row_getposter_update = mysqli_fetch_assoc($result_getposter_update);
	
	if(isset($_SESSION['pid']) && $row_getposter_update['pid'] != $_SESSION['pid']) {
	        $error_message[] = 'You are not the original poster of this post.';
			$error_code[] = '1512016';
	}
	if(strval($row_getposter_update['is_hidden']) == '1') {
	        $error_message[] = 'The post has already been deleted.';
			$error_code[] = '1512017';
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
        $sql_update = 'UPDATE replies SET replies.is_hidden = "1", replies.hidden_resp = "1" WHERE replies.id = "'.mysqli_real_escape_string($mysql, $_GET['id']).'"';	
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

print '    <title>Your Comment</title>
<header id="header">
  
  <h1 id="page-title">Your Comment</h1>

</header>

<div class="body-content track-error" id="post-permalink" data-track-error="deleted">
';
$no_content_message = 'Deleted by the author of the comment.';
include 'lib/no-content-window.php';
print '
  </div>
</div>';
		
}




}	
	
	
	}	
}


# Define other modes here.

#else {
#include_once '404.php'; }

}




# Else, normal use, aka /replies/*
else {
# Display comment.
$sql_reply = 'SELECT * FROM replies WHERE replies.id = "' . mysqli_real_escape_string($mysql, $_GET['id']) . '"';
$result_reply = mysqli_query($mysql, $sql_reply);
$row_reply = mysqli_fetch_assoc($result_reply);

$sql_reply_user = 'SELECT * FROM people WHERE people.pid = "' . $row_reply['pid'] . '"';
$result_reply_user = mysqli_query($mysql, $sql_reply_user);
$row_reply_user = mysqli_fetch_assoc($result_reply_user); 

// Who posesses the reply?
if($row_reply['pid']) {
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
if($_SESSION['pid'] == $row_reply['pid']) {
$pagetitle = 'Your Comment';
}
else {
$pagetitle = htmlspecialchars($row_reply_user['screen_name']) . "'s Comment"; 
}    } 
else {
$pagetitle = htmlspecialchars($row_reply_user['screen_name']) . "'s Comment"; }
}
else {
$pagetitle = 'Error'; }
require_once 'lib/htm.php';
printHeader(false);
printMenu();
// DB error.
if(!$result_reply)
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
	// Reply wasn't found.
    if($row_reply == 0)
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
    else {
if($row_reply['is_hidden'] == 1) {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" id="post-permalink" data-track-error="deleted">';
if($row_reply['hidden_resp'] == '1') {
if(!empty($_SESSION['pid']) && $row_reply['pid'] == $_SESSION['pid']) {
$no_content_message = ( 'Deleted by the author of the comment.' ); } else {
$no_content_message = ( 'Deleted by poster.' ); }
} if($row_reply['hidden_resp'] == '0') {
require_once 'lib/olv-url-enc.php';
$no_content_message = 'Deleted by administrator.</p>
<p>Comment ID: '.getPostID($row_reply['id']);
}
include 'lib/no-content-window.php';
print $GLOBALS['div_body_head_end'];
print '</div>';
}
else {	
// Display post using $row_reply!
	$sql_reply_user = 'SELECT * FROM people WHERE people.pid = "' . $row_reply['pid'] . '"';
	$result_reply_user = mysqli_query($mysql, $sql_reply_user);
	$row_reply_user = mysqli_fetch_assoc($result_reply_user);

	$sql_reply_ogpost = 'SELECT * FROM posts WHERE posts.id = "' . $row_reply['reply_to_id'] . '"';
	$result_reply_ogpost = mysqli_query($mysql, $sql_reply_ogpost);
	$row_reply_ogpost = mysqli_fetch_assoc($result_reply_ogpost);

	$sql_reply_ogpost_user = 'SELECT * FROM people WHERE people.pid = "' . $row_reply_ogpost['pid'] . '"';
	$result_reply_ogpost_user = mysqli_query($mysql, $sql_reply_ogpost_user);
	$row_reply_ogpost_user = mysqli_fetch_assoc($result_reply_ogpost_user);

# Original post Mii	
	if($row_reply_ogpost_user['mii_hash']) {
if(($row_reply_ogpost['feeling_id']) == '0') {
$mii_ogpost_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_ogpost_user['mii_hash'] . '_normal_face.png'; }
if(($row_reply_ogpost['feeling_id']) == '1') {
$mii_ogpost_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_ogpost_user['mii_hash'] . '_happy_face.png'; }
if(($row_reply_ogpost['feeling_id']) == '2') {
$mii_ogpost_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_ogpost_user['mii_hash'] . '_like_face.png'; }
if(($row_reply_ogpost['feeling_id']) == '3') {
$mii_ogpost_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_ogpost_user['mii_hash'] . '_surprised_face.png'; }
if(($row_reply_ogpost['feeling_id']) == '4') {
$mii_ogpost_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_ogpost_user['mii_hash'] . '_frustrated_face.png'; }
if(($row_reply_ogpost['feeling_id']) == '5') {
$mii_ogpost_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_ogpost_user['mii_hash'] . '_puzzled_face.png'; }
}
else {
if($row_reply_ogpost_user['user_face']) {
$mii_ogpost_face_output = htmlspecialchars($row_reply_ogpost_user['user_face']); }
else {
$mii_ogpost_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

# Root reply Mii
if($row_reply_user['mii_hash']) {
if(($row_reply['feeling_id']) == '0') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_user['mii_hash'] . '_normal_face.png'; 
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
if(($row_reply['feeling_id']) == '1') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_user['mii_hash'] . '_happy_face.png'; 
$mii_face_feeling = 'happy'; }
$mii_face_miitoo = htmlspecialchars('Yeah!');
if(($row_reply['feeling_id']) == '2') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_user['mii_hash'] . '_like_face.png'; 
$mii_face_feeling = 'like';
$mii_face_miitoo = htmlspecialchars('Yeah♥'); }
if(($row_reply['feeling_id']) == '3') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_user['mii_hash'] . '_surprised_face.png'; 
$mii_face_feeling = 'surprised';
$mii_face_miitoo = htmlspecialchars('Yeah!?'); }
if(($row_reply['feeling_id']) == '4') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_user['mii_hash'] . '_frustrated_face.png'; 
$mii_face_feeling = 'frustrated';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
if(($row_reply['feeling_id']) == '5') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_user['mii_hash'] . '_puzzled_face.png'; 
$mii_face_feeling = 'puzzled';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
}
else {
if($row_reply_user['user_face']) {
$mii_face_output = htmlspecialchars($row_reply_user['user_face']);
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!');
} else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
}
	
    print $GLOBALS['div_body_head'];
	print '<header id="header">
	
	  <h1 id="page-title">' . $pagetitle . '</h1>
	
	</header><div class="body-content" id="reply-permalink">';
	
	$truncate_post_bodyp1 = mb_substr((htmlspecialchars($row_reply_ogpost['body'])), 0, 20, 'utf-8');
$truncate_post_body = (mb_strlen($truncate_post_bodyp1, 'utf-8') >= 20 ? "$truncate_post_bodyp1..." : $truncate_post_bodyp1);

print '
  <a class="post-permalink-button info-ticker" href="/posts/' . $row_reply_ogpost['id'] . '" data-pjax="#body">
    <span>View <span class="post-user-description"><img src="' . $mii_ogpost_face_output . '" class="user-icon">';
	print htmlspecialchars($row_reply_ogpost_user['screen_name']);
	print "'s post (";
	print $row_reply_ogpost['_post_type'] == 'artwork' ? 'handwritten' : $truncate_post_body;
	print ')</span> for this comment.</span>';
	print '
  </a>
  <div id="post-permalink-comments">
  ';
  # Add no-empathy above
  print '<ul class="post-permalink-reply">
    <li>';
	
	if($row_reply_user['official_user'] == 1) {
$is_replier_official_user = ' official-user';
}
else {
$is_replier_official_user = ''; }

print '<a href="/users/' . htmlspecialchars($row_reply_user['user_id']) . '" data-pjax="#body" class="scroll-focus user-icon-container' . $is_replier_official_user . '"><img src="' . $mii_face_output . '" class="user-icon"></a>';
print '
<div class="reply-content">
        <header>
          <span class="user-name">' . htmlspecialchars($row_reply_user['screen_name']) . '</span>
          <span class="timestamp">' . humanTiming(strtotime($row_reply['created_at'])) . '</span>
		  <span class="spoiler-status">Spoilers</span>
		  ';
		  if($row_reply['is_spoiler'] == '1') { 
	 print '<span class="spoiler-status spoiler">Spoilers</span>'; }
	print '	  </header>



            <p class="reply-content-text">' . htmlspecialchars($row_reply['body']) . '</p>';
	 if(isset($row_reply['screenshot'])) {
     if(strlen($row_reply['screenshot']) > 3) {
	print '<div class="capture-container">
          <img src="' . htmlspecialchars($row_reply['screenshot']) . '" class="capture">
        </div>
		'; 
	 } }

if(isset($_SESSION['pid'])) {  
if($_SESSION['pid'] == $row_reply['pid']) {
$reply_meta_button_output = '<a href="#" role="button" class="edit-button edit-reply-button" data-modal-open="#edit-post-page">Edit</a>';	}
else {
require 'lib/olv-url-enc.php';
$is_report_disabled = (strval($row_reply_user['official_user']) == 1 || isset($row_current_peopleban) ? ' disabled' : '');
$reply_meta_button_output = '<a '.(strval($row_reply_user['official_user']) == 1 || isset($row_current_peopleban) ? '' : 'href="#" ').' role="button"'.$is_report_disabled.' class="report-button'.$is_report_disabled.'" data-modal-open="#report-violation-page" data-screen-name="'.htmlspecialchars($row_reply_user['screen_name']).'" data-support-text="'.getPostID($row_reply['id']).'" data-action="/replies/'.$row_reply['id'].'/violations" data-is-permalink="1" data-can-report-spoiler="'.(strval($row_reply['is_spoiler']) == 1 ? '1' : '0').'" data-community-id="" data-url-id="'.$row_reply['id'].'" data-track-label="reply" data-title-id="" data-track-action="openReportModal" data-track-category="reportViolation">Report Violation</a>'; }
 }
else {
$reply_meta_button_output = '<a disabled="" role="button" class="report-button disabled">Report Violation</a>';	 
 }
if(isset($row_current_peopleban)) {
$can_reply_user_miitoo = ' disabled'; }
    elseif(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && $row_reply_user['pid'] != $_SESSION['pid']) {
	$can_reply_user_miitoo = ''; }
	else {
	$can_reply_user_miitoo = ' disabled'; }

if(!empty($_SESSION['pid'])) {  		
$sql_hasempathy = 'SELECT * FROM empathies WHERE empathies.id = "' . mysqli_real_escape_string($mysql, $row_reply['id']) . '" AND empathies.pid = "' . $_SESSION['pid'] . '"';
$result_hasempathy = mysqli_query($mysql, $sql_hasempathy);
}

$sql_empathyamt = 'SELECT * FROM empathies WHERE empathies.id = "' . mysqli_real_escape_string($mysql, $row_reply['id']) . '"';
$result_empathyamt = mysqli_query($mysql, $sql_empathyamt);
if(!empty($_SESSION['pid']) && mysqli_num_rows($result_hasempathy)!=0) {
    $mii_face_miitoo = 'Unyeah';
	$has_reply_miitoo_given_v2 = ''; 
    $has_reply_miitoo_given = ' empathy-added';
	$has_reply_miitoo_given_snd = 'SE_WAVE_MII_CANCEL';
    $reply_miitoo_amt_other = mysqli_num_rows($result_empathyamt) - 1;	}
	else {
	$has_reply_miitoo_given_v2 = ' style="display: none;"';
	$has_reply_miitoo_given = '';
	$has_reply_miitoo_given_snd = 'SE_WAVE_MII_ADD';
    $reply_miitoo_amt_other = mysqli_num_rows($result_empathyamt);	} 
	
	
	 print '<div class="reply-meta">
        

        <div class="expression">
		';
        print '<button type="button" ' . $can_reply_user_miitoo . ' 
		class="submit miitoo-button' . $has_reply_miitoo_given . '" 
		data-feeling="' . $mii_face_feeling . '" 
		data-action="/replies/' . $row_reply['id'] . '/empathies" 
		data-other-empathy-count="' . $reply_miitoo_amt_other . '" 
		data-sound="' . $has_reply_miitoo_given_snd . '" 
		data-url-id="' . $row_reply['id'] . '" 
		data-track-label="default" 
		data-track-action="yeah" 
		data-track-category="empathy">' . $mii_face_miitoo . '</button>
        </div>';

print $reply_meta_button_output;		
		print '</div>
		';

print '        <div class="post-permalink-feeling">
<p class="post-permalink-feeling-text"></p>';

if(isset($_SESSION['pid'])) {	  
if($_SESSION['pid']) {
$sql_reply_me = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_reply_me = mysqli_query($mysql, $sql_reply_me);
$row_reply_me = mysqli_fetch_assoc($result_reply_me); 	}  }
	  
	  if(isset($_SESSION['signed_in'])) {
	  if($row_reply_me['mii_hash']) {
	  $my_mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_me['mii_hash'] . '_' . $mii_face_feeling . '_face.png';
	  }
	  else {
	  if($row_reply_me['user_face']) {
	  $my_mii_face_output = $row_reply_me['user_face'];
	  }
	  else {
	  $my_mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
	  }
	  }

	 # User's own empathy.

if($row_reply_me['official_user'] == 1) {
$is_yeaher_official_user = ' official-user';
}
else {
$is_yeaher_official_user = ''; }

	 print '<div class="post-permalink-feeling-icon-container"><a href="/users/' . htmlspecialchars($_SESSION['user_id']) . '" data-pjax="#body" class="post-permalink-feeling-icon visitor' . $is_yeaher_official_user . '"' . $has_reply_miitoo_given_v2 . '><img src="' . htmlspecialchars($my_mii_face_output) . '" class="user-icon"></a>'; 
	  }
	 # Put other users' empathies here.

	$sql_reply_empathies2 = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_reply['id'] . '" ORDER BY empathies.created_at DESC LIMIT 36';
	$result_reply_empathies2 = mysqli_query($mysql, $sql_reply_empathies2);	 
	while($row_reply_empathies2 = mysqli_fetch_assoc($result_reply_empathies2)) {	

$sql_reply_empathies_user = 'SELECT * FROM people WHERE people.pid = "' . $row_reply_empathies2['pid'] . '"';
$result_reply_empathies_user = mysqli_query($mysql, $sql_reply_empathies_user);
$row_reply_empathies_user = mysqli_fetch_assoc($result_reply_empathies_user);	
		# Don't display your own Mii!
		if(isset($_SESSION['pid']) && $row_reply_empathies_user['pid']==$_SESSION['pid']) {
		print null; }
		else {
			
if($row_reply_empathies_user['official_user'] == 1) {
$is_yeaher_official_user = ' official-user';
}
else {
$is_yeaher_official_user = ''; }
		
	  if($row_reply_empathies_user['mii_hash']) {
	  $our_mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_reply_empathies_user['mii_hash'] . '_' . $mii_face_feeling . '_face.png';
	  }
	  else {
	  if($row_reply_empathies_user['user_face']) {
	  $our_mii_face_output = $row_reply_empathies_user['user_face'];
	  }
	  else {
	  $our_mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
	  }
	  }
			
		print '<a href="/users/' . $row_reply_empathies_user['user_id'] . '" data-pjax="#body" class="post-permalink-feeling-icon' . $is_yeaher_official_user . '"><img src="' . $our_mii_face_output . '" class="user-icon"></a>';	
		
		}
}
       print ' </div>
      </div>';
	# End ul
	print '</ul>
  </li>';
  # End post-permalink-comments
    print '</div>';
	print '</div>';
	$template_post_type_uri = 'replies';
	$template_post_end_uri = $row_reply['id'];
	if($row_reply['is_spoiler'] == '1') {
	$template_has_post_spoiler = ' disabled'; }
	else {
	$template_has_post_spoiler = ''; }
	include 'lib/posts-footer.php';
    print $GLOBALS['div_body_head_end'];
(empty($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');
	  }
    }
}

}

