<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$search_post = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.(isset($_GET['id']) ? $mysql->real_escape_string($_GET['id']) : 'a').'"');

if(isset($_GET['mode']) && $_GET['mode'] == 'empathies') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# Display 404 if method isn't POST
include_once '404alli.php'; }
# Method is POST.
require_once '../grplib-php/community-helper.php';

		$search_post_for_empathy = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.(isset($_GET['id']) ? $mysql->real_escape_string($_GET['id']) : 'a').'" AND posts.is_hidden != "1" LIMIT 1');
		if($search_post_for_empathy->num_rows == 0) { http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print
		json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit(); }

		if(empty($_SESSION['pid'])) {
		http_response_code(403); header('Content-Type: application/json; charset=utf-8');
		print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }

$post = $search_post->fetch_assoc();
if(!empty($_SESSION['pid'])) {		
if(!miitooCan($_SESSION['pid'], $post['id'], 'posts')) {
 		http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 400)); grpfinish($mysql); exit(); } }
$searchmyempathy = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'"');
if($searchmyempathy->num_rows != 0) {
# Remove empathy
$removemyempathy = $mysql->query('DELETE FROM empathies WHERE empathies.id = "'.$post['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'"');
if(!$removemyempathy) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
} else {
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 1));
}   }  else {
        $empathycreate = $mysql->query('INSERT INTO empathies(id, pid, created_from)
                VALUES ("'.$post['id'].'", "'.$_SESSION['pid'].'", "'.$_SERVER['REMOTE_ADDR'].'")');
	
        $getposter = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$post['pid'].'" LIMIT 1')->fetch_assoc();

	// If the user gave the same type of notification 8 seconds ago, then don't send this.
	$check_fastnews = $mysql->query('SELECT news.to_pid, news.created_at FROM grape.news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$getposter['pid'].'" AND news.news_context = "2" AND news.created_at > NOW() - 8 ORDER BY news.created_at DESC');
    if($check_fastnews->num_rows == 0) {
    $check_ownusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$getposter['pid'].'" AND news.news_context = "2" AND news.id = "'.$post['id'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
	$check_mergedusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$getposter['pid'].'" AND news.news_context = "2" AND news.id = "'.$post['id'].'" AND news.merged IS NOT NULL AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if($check_mergedusernews->num_rows != 0) {
	$result_update_mergedusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$check_mergedusernews['merged'].'"');	
	} elseif($check_ownusernews->num_rows != 0) {
	$result_update_ownusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$check_ownusernews->fetch_assoc()['news_id'].'"'); }
else {
$result_update_newsmergesearch = $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$getposter['pid'].'" AND news.id = "'.$post['id'].'" AND news.created_at > NOW() - 7200 AND news.news_context = "2" ORDER BY news.created_at DESC');	
if($result_update_newsmergesearch->num_rows != 0) {
$row_update_newsmergesearch = $result_update_newsmergesearch->fetch_assoc();
$result_newscreatemerge = $mysql->query('INSERT INTO grape.news(from_pid, to_pid, id, merged, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$getposter['pid'].'", "'.$post['id'].'", "'.$row_update_newsmergesearch['news_id'].'", "2", "0")');

$result_update_newsformerge = $mysql->query('UPDATE news SET has_read = "0", created_at = NOW() WHERE news.news_id = "'.$row_update_newsmergesearch['news_id'].'"');
		}
else {
        $result_newscreate = $mysql->query('INSERT INTO grape.news(from_pid, to_pid, id, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$getposter['pid'].'", "'.$post['id'].'", "2", "0")'); 	
	} }
	
        if(!$empathycreate)
        { json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); } else {
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => '1')); }	
   }
  } grpfinish($mysql);
exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'replies') {
require_once '../grplib-php/post-helper.php';
if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }

$ogpost_result = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$mysql->real_escape_string($_GET['id']).'" LIMIT 1');

if($ogpost_result->num_rows == 0) {
http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit();
}

$ogpost = $ogpost_result->fetch_assoc();

$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
$is_post_valid = postValid($user, 'upload');
$fastpost = ($mysql->query('SELECT replies.pid, replies.created_at FROM replies WHERE replies.pid = "'.$user['pid'].'" AND replies.created_at > NOW() - '.(isset($grp_config_max_postbuffertime) ? $grp_config_max_postbuffertime : '10').' ORDER BY replies.created_at DESC LIMIT 5')->num_rows != 0 ? true : false);
if($is_post_valid != 'ok' || $fastpost == true) {
if($fastpost == true) {
$error_message[] = 'Multiple posts cannot be made in such a short period of time. Please try posting again later.';
$error_code[] = '1515918'; }
if($is_post_valid == 'blank') {
$error_message[] = 'The content you have entered is blank.
Please enter content into your post.';
$error_code[] = 1515001; }
elseif($is_post_valid == 'max') {
$error_message[] = 'You have exceeded the amount of characters that you can send.';
$error_code[] = 1515002; }
}
if(!empty($error_code)) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [array(
'message' => $error_message[0],
'error_code' => $error_code[0]
)], 'code' => 400)); grpfinish($mysql); exit();
}

if(isset($_POST['screenshot']) && strlen($_POST['screenshot']) > 1) {
$ch_imgu = curl_init();
curl_setopt($ch_imgu, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
curl_setopt($ch_imgu, CURLOPT_POST, TRUE);
curl_setopt($ch_imgu, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch_imgu, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ef7417cca96d79f' ));
curl_setopt($ch_imgu, CURLOPT_POSTFIELDS, array( 'image' => $_POST['screenshot'] ));
$reply_imgu = curl_exec($ch_imgu);
curl_close($ch_imgu);

$reply_imgu2 = json_decode($reply_imgu, true);
if($reply_imgu2['success'] == false) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1511000 + $reply_imgu2['status'])], 'code' => 500));
grpfinish($mysql); exit();
} else {
$result_imgu = 'https://i.imgur.com/'.$reply_imgu2['data']['id'].'.png'; }
}

require_once '../grplib-php/olv-url-enc.php';
$gen_olive_url = genURL();

if(empty($_POST['feeling_id']) || strval($_POST['feeling_id']) >= 6) { $_POST['feeling_id'] = 0; }

$createpost = $mysql->query('INSERT INTO replies(id, reply_to_id, pid, feeling_id, platform_id, body, screenshot, is_spoiler) VALUES (
"'.$gen_olive_url.'", 
"'.$ogpost['id'].'",
"'.$_SESSION['pid'].'",
"'.(!empty($_POST['feeling_id']) && is_numeric($_POST['feeling_id']) ? $mysql->real_escape_string($_POST['feeling_id']) : 0).'",
"2",
"'.$mysql->real_escape_string($_POST['body']).'",
"'.(!empty($_POST['screenshot']) ? $result_imgu : null).'",
"'.(!empty($_POST['is_spoiler']) ? $mysql->real_escape_string($_POST['is_spoiler']) : 0).'"
)');

if(!$createpost) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
} else {
# Success, print post and send notification.
$search_post_created = $mysql->query('SELECT * FROM replies WHERE replies.id = "'.$gen_olive_url.'" LIMIT 1')->fetch_assoc();
require_once 'lib/htm.php';
require_once 'lib/htmPost.php';
require_once '../grplib-php/community-helper.php';
print displayReply($ogpost, $search_post_created);


		$get_empathy_poster = $mysql->query('SELECT * FROM grape.people WHERE people.pid = "'.$ogpost['pid'].'"')->fetch_assoc();

		if($_SESSION['pid'] == $get_empathy_poster['pid']) {
		# send notifications to all commenters	
		$sql_news_getcomments = 'SELECT replies.pid FROM grape.replies WHERE replies.reply_to_id = "'.$ogpost['id'].'" AND replies.pid != "'.$get_empathy_poster['pid'].'" AND replies.is_hidden = "0" GROUP BY replies.pid';
		$result_news_getcomments = $mysql->query($sql_news_getcomments);
		while($row_news_getcomments = mysqli_fetch_assoc($result_news_getcomments)) {
		$result_check_ownusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_news_getcomments['pid'].'" AND news.news_context = "5" AND news.id = "'.$ogpost['id'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if(mysqli_num_rows($result_check_ownusernews) != 0) {
	$result_update_ownusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.mysqli_fetch_assoc($result_check_ownusernews)['news_id'].'"');	
	}
		else {
		$result_news_send = $mysql->query('INSERT INTO grape.news(from_pid, to_pid, id, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_news_getcomments['pid'].'", "'.$mysql->real_escape_string($_GET['id']).'", "5", "0")'); }
		}
		}
		else {
	// If the user gave the same type of notification 8 seconds ago, then don't send this.
	$result_check_fastnews = $mysql->query('SELECT news.to_pid, news.created_at FROM grape.news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$get_empathy_poster['pid'].'" AND news.news_context = "4" AND news.created_at > NOW() - 1 ORDER BY news.created_at DESC');
    if($result_check_fastnews->num_rows == 0) {
    $result_check_ownusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$get_empathy_poster['pid'].'" AND news.news_context = "4" AND news.id = "'.$ogpost['id'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
$row_check_ownusernews = $result_check_ownusernews->fetch_assoc();
	$result_check_mergedusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$get_empathy_poster['pid'].'" AND news.id = "'.$ogpost['id'].'" AND news.news_context = "4" AND news.merged IS NOT NULL AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if($result_check_mergedusernews->num_rows != 0) {
	 $rfmgnws = $result_check_mergedusernews->fetch_assoc();
	$result_update_mergedusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$rfmgnws['merged'].'"');	
	}
	elseif($result_check_ownusernews->num_rows != 0) {
	$result_update_ownusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$row_check_ownusernews['news_id'].'"');
	}
else {
	$result_update_newsmergesearch = $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$get_empathy_poster['pid'].'" AND news.id = "'.$ogpost['id'].'" AND news.created_at > NOW() - 7200 AND news.news_context = "4" ORDER BY news.created_at DESC');	
	if($result_update_newsmergesearch->num_rows != 0) {
$row_update_newsmergesearch = $result_update_newsmergesearch->fetch_assoc();
	
	$result_newscreatemerge = $mysql->query('INSERT INTO grape.news(from_pid, to_pid, id, merged, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$get_empathy_poster['pid'].'", "'.$ogpost['id'].'", "'.$row_update_newsmergesearch['news_id'].'", "4", "0")');
$result_update_newsformerge = $mysql->query('UPDATE news SET has_read = "0", created_at = NOW() WHERE news.news_id = "'.$row_update_newsmergesearch['news_id'].'"');
		}
else {
        $result_newscreate = $mysql->query('INSERT INTO grape.news(from_pid, to_pid, id, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$get_empathy_poster['pid'].'", "'.$ogpost['id'].'", "4", "0")'); 	
	} }
		
		
	}			
		}

}
# Finished, clear sys resources!
grpfinish($mysql); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'set_spoiler') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404alli.php'; }
# Put checks + update post spoiler here.	
if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }

if($search_post->num_rows == 0) {
http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit();
}
$post = $search_post->fetch_assoc();

if($post['is_hidden'] == 1) { http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit(); }

if($post['pid'] != $_SESSION['pid']) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); 
}

if($post['is_spoiler'] == 1) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400)); grpfinish($mysql); exit(); 
}

$update_post = $mysql->query('UPDATE posts SET posts.is_spoiler = "1" WHERE posts.id = "'.$post['id'].'"');

        if(!$update_post) {
http_response_code(500); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
		}
else {
header('Content-Type: application/json; charset=utf-8'); print json_encode(array('is_spoiler' => 1,'success' => 1));
}

grpfinish($mysql); exit();	
}
if(isset($_GET['mode']) && $_GET['mode'] == 'delete') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404alli.php'; }
# Put checks + update post spoiler here.	
if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }

if($search_post->num_rows == 0) {
http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit();
}
$post = $search_post->fetch_assoc();

if($post['is_hidden'] == 1) { http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit(); }

if($post['pid'] != $_SESSION['pid']) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); 
}

$update_post = $mysql->query('UPDATE posts SET posts.is_hidden = "1", posts.hidden_resp = "1" WHERE posts.id = "'.$post['id'].'"');

        if(!$update_post) {
http_response_code(500); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
		}
else {
if($mysql->query('SELECT profiles.favorite_screenshot FROM grape.profiles WHERE profiles.pid = "'.$_SESSION['pid'].'" AND profiles.favorite_screenshot = "'.$post['id'].'"')->num_rows == 0); {
$delete_user_favoritepost = $mysql->query('UPDATE grape.profiles SET profiles.favorite_screenshot = "" WHERE profiles.pid = "'.$_SESSION['pid'].'"'); }	

require_once 'lib/htm.php';
$pagetitle = 'Error'; printHeader('old'); printMenu('old');
print '
<div id="main-body">

<div class="no-content track-error" data-track-error="404">
  <div>
    <p>Deleted by poster.</p>
  </div>
</div>

</div>
';
printFooter('old');
}

grpfinish($mysql); exit();	
}
if(isset($_GET['mode']) && $_GET['mode'] == 'screenshot.set_profile_post') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404alli.php'; }
# Put checks + update user's favorite post here.
if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }

if($search_post->num_rows == 0) {
http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit();
}
$post = $search_post->fetch_assoc();

if($post['is_hidden'] == 1) { http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit(); }

if($post['pid'] != $_SESSION['pid']) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); 
}
        $update_profile = $mysql->query('UPDATE profiles SET profiles.favorite_screenshot = "'.$post['id'].'" WHERE profiles.pid = "'.$_SESSION['pid'].'"');
        if(!$update_profile) {
http_response_code(500); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
		}
		else { 
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 1));
}

grpfinish($mysql); exit();
	
	}
if(isset($_GET['mode']) && $_GET['mode'] == 'violations') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404alli.php'; }
# Method is POST.

		if(empty($_SESSION['pid'])) { $error_code[] = 403; }
if(!empty($_SESSION['pid'])) {
if($search_post->num_rows == 0) { $error_code[] = 404;	} else {
$post = $search_post->fetch_assoc();
if($post['pid'] == $_SESSION['pid']) { $error_code[] = 400; }
} }
	    if(!empty($error_code) || !empty($error_message) ) {
		// JSON response.
			http_response_code($error_code[0]);
            header('Content-Type: application/json; charset=utf-8');
			json_encode(array('success' => 0, 'errors' => [], 'code' => $error_code[0])); }
    else {
$result_get_spamreports = $mysql->query('SELECT * FROM reports WHERE reports.source = "'.$_SESSION['pid'].'" AND reports.created_at > NOW() - 5');
if($result_get_spamreports->num_rows != 0) {
header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 1));
exit();
}
$reportcreate = $mysql->query('INSERT INTO reports (source, subject, type, reason, message) VALUES ("'.$_SESSION['pid'].'", "'.$post['id'].'", "0", "'.$mysql->real_escape_string($_POST['type']).'", "'.$mysql->real_escape_string($_POST['body']).'")');
        if(!$reportcreate) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); } else {
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 1)); }
		} grpfinish($mysql); 	exit(); }

if(isset($_GET['mode'])) { if($_GET['mode'] != 'empathies' || $_GET['mode'] != 'replies' || $_GET['mode'] != 'violations' || $_GET['mode'] != 'set_spoiler' || $_GET['mode'] != 'screenshot_set_profile_post' || $_GET['mode'] != 'delete') { 
# Display 404 if mode is undefined
include_once '404alli.php'; } }

if(!$search_post) {
$pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old'); grpfinish($mysql); exit(); } elseif($search_post->num_rows == 0) { $pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('posts', false); printFooter('old'); grpfinish($mysql); exit(); 
}
$post = $search_post->fetch_assoc();
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$post['pid'].'" LIMIT 1')->fetch_assoc();
$community = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$post['community_id'].'" LIMIT 1')->fetch_assoc();
if($post['is_hidden'] == '1') {
$pagetitle = 'Error'; printHeader('old'); printMenu('old');
if($post['hidden_resp'] == 0) {
require '../grplib-php/olv-url-enc.php';
notFound('Deleted by adminsistrator.</p>
<p>Post ID: '.getPostID($post['id']), true);  }
if($post['hidden_resp'] == '1') {
notFound ('Deleted by poster.', true); }
# Other deleted messages
printFooter('old'); grpfinish($mysql); exit();
}
# Success
require_once 'lib/htmCommunity.php';
require_once '../grplib-php/community-helper.php';
require_once 'lib/htmPost.php';
require_once '../grplib-php/olv-url-enc.php';
$bodyID = 'post-permlink';
$pagetitle = (!empty($_SESSION['pid']) && $_SESSION['pid'] == $post['pid'] ? 'Your Post' : htmlspecialchars($user['screen_name']).'\'s Post');
$mii = getMii($user, $post['feeling_id']);
printHeader('old'); printMenu('old');
print '<div id="main-body">
<div id="page-title">'.htmlspecialchars($mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$community['olive_title_id'].'" LIMIT 1')->fetch_assoc()['name']).'</div>
<div id="post-content" class="post '.($user['official_user'] == 1 ? 'official-user' : '').'">

  <a href="/users/'.htmlspecialchars($user['user_id']).'" class="icon-container'.($mii['official'] ? ' official-user' : '').'"><img src="'.$mii['output'].'" class="icon"></a>
  <p class="timestamp-container">
    <span class="spoiler-status'.($post['is_spoiler'] == 1 ? ' spoiler' : '').'">Spoilers·</span>
    <span class="timestamp">'.humanTiming(strtotime($post['created_at'])).'</span>
  </p>
  ';
  if($user['official_user'] == 1) {
print '<p class="user-organization">'.htmlspecialchars($user['organization']).'</p>'; } print '
  <p class="user-name"><a href="/users/'.htmlspecialchars($user['user_id']).'">'.htmlspecialchars($user['screen_name']).'</a><span class="user-id">'.htmlspecialchars($user['user_id']).'</span></p>
  <p class="community-container"><a'.($community['type'] == 5 ? '' : ' href="/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'"').'><img src="'.getIcon($community).'" class="community-icon">'.htmlspecialchars($community['name']).'</a></p>

  <div class="body">


    

    

';
if($post['_post_type'] == 'artwork') {
print '<p class="post-content-memo"><img src="'.htmlspecialchars($post['body']).'" class="post-memo"></p>'; 
} else {
print '	<p class="post-content-text">'.htmlspecialchars($post['body']).'</p>'; 
}
print '

';
if(!empty($post['screenshot'])) {
print '<div class="screenshot-container still-image"><img src="'.htmlspecialchars($post['screenshot']).'"></div>
'; }
if(!empty($post['url']) && strpos($post['url'], 'www.youtube.com/watch?v=') !== false) {
if(substr($post['url'], 0, 4) == "http" || substr($post['url'], 0, 5) == "https") {
$hvideopost = true;
print '<div class="screenshot-container video"><iframe class="youtube-player" type="text/html" width="490" height="276" src="https://www.youtube.com/embed/'.substr($post['url'], (substr($post['url'], 0, 5) == "https" ? 32 : 31), 11).'?rel=0&amp;modestbranding=1&amp;iv_load_policy=3" frameborder="0"></iframe></div>';
} }

if(!empty($post['url']) && !isset($hvideopost)) {
print '<p class="url-link"><a href="'.htmlspecialchars($post['url']).'" target="_blank">'.htmlspecialchars($post['url']).'</a></p>';
}

if(!empty($_SESSION['pid'])) {
$canmiitoo = miitooCan($_SESSION['pid'], $post['id'], 'posts'); 
$my_empathy_added = ($mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'" LIMIT 1')->num_rows == 1 ? true : false);
}

$empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'"');
$replies = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$post['id'].'" ORDER BY replies.created_at');
print '
    <div class="post-meta">
      <button type="button"'.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').' class="symbol submit empathy-button'.(isset($my_empathy_added) && $my_empathy_added == true ? ' empathy-added' : '').''.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').'" data-feeling="'.($mii['feeling'] ? $mii['feeling'] : 'normal').'" data-action="/posts/'.$post['id'].'/empathies"><span class="empathy-button-text">'.(isset($my_empathy_added) && $my_empathy_added == true ? 'Unyeah' : (!empty($usermii['miitoo']) ? $mii['miitoo'] : 'Yeah!')).'</span></button>
      <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count">'.$empathies->num_rows.'</span></div>
      <div class="reply symbol"><span class="symbol-label">Comments</span><span class="reply-count">'.$replies->num_rows.'</span></div>
    </div>
  </div>
</div>

';


# Empathy content
print '<div id="empathy-content'.($empathies->num_rows == 0 ? '" class="none"' : '"').'>
';
$empathies_display = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'"'.(!empty($_SESSION['pid']) ? ' AND empathies.pid != "'.$_SESSION['pid'].'"' : '').' ORDER BY empathies.created_at DESC LIMIT 15');
	  if(!empty($_SESSION['pid'])) {
print displayempathy($post, $post, true);
	  }
while($row_empathies = $empathies_display->fetch_assoc()) {
print displayempathy($row_empathies, $post, false);
}
print '</div>
';

# Buttons content
print '<div class="buttons-content">
<div class="social-buttons-content">

  </div>
  ';
if(!empty($_SESSION['pid']) && $_SESSION['pid'] != $post['pid'] && $user['official_user'] != 1) {
print '<div class="report-buttons-content">
      <button type="button" class="button" data-modal-open="#report-violation-page" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-support-text="'.getPostID($post['id']).'" data-action="/posts/'.$post['id'].'/violations" data-is-post="1" data-is-permalink="1" data-can-report-spoiler="'.($post['is_spoiler'] == 1 ? '1' : '0').'">Report Violation</button>
    </div>';
} if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $post['pid']) {
print '<div class="edit-buttons-content">
      <button type="button" class="symbol button edit-button edit-post-button" data-modal-open="#edit-post-page"><span class="symbol-label">Edit</span></button>
    </div>
	';
}
print '
</div>';

# Reply content
print '<div id="reply-content">
  <h2 class="label">Comment</h2>
  <div class="no-reply-content'.($replies->num_rows == 0 ? '' : ' none').'"><div>
    <p>This post has no comments.</p>
  </div></div>

  ';
$get_replies_for_view = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$post['id'].'" ORDER BY replies.created_at');

print '<ul class="list reply-list js-post-list">

';
if($get_replies_for_view->num_rows >= 1) {
while($display_replies = $get_replies_for_view->fetch_assoc()) {
print displayreply($post, $display_replies);	
} }

print '
</ul>';

print '

</div>
';
# Add a comment form // reply form //
print '<h2 class="label">Add a Comment</h2>
';
if(empty($_SESSION['pid'])) {
print '<div class="guest-message">
  <p>You must sign in to post a comment.
</p>
  
  <a href="/act/login" class="arrow-button"><span>Log in</span></a><a href="/act/create" class="arrow-button"><span>Create account</span></a>
</div>';
} else {
print '<form id="reply-form" method="post" class="folded'.($user['official_user'] == '1' || $user['privilege'] >= 1 || $user['image_perm'] == '1' ? ' for-identified-user' : '').'" action="/posts/'.$post['id'].'/replies">
  

  <div class="feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label>
  </div>


  <textarea name="body" class="textarea-text textarea" maxlength="1000" placeholder="Add a comment to this post here." data-open-folded-form="" data-required=""></textarea>
  ';
if($user['official_user'] == '1' || $user['privilege'] >= 1 || $user['image_perm'] == '1') {
print '
<label class="file-button-container">
      <span class="input-label">Screenshot <span>JPEG/PNG/BMP</span></span>
      <input type="file" class="file-button" accept="image/jpeg">
      <input type="hidden" name="screenshot" value="">
    </label>
'; }
print '
  <label class="spoiler-button symbol">
    <input type="checkbox" id="is_spoiler" name="is_spoiler" value="1">
    Spoilers
  </label>
  <div class="form-buttons">
    <input type="submit" class="black-button reply-button disabled" value="Send">
  </div>
</form>';
}
# </form>


print reportTemplate('posts');
print editTemplate('posts', $post);
print '
</div>';
printFooter('old');
grpfinish($mysql);
