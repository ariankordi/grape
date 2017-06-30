<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if(empty($_GET['id'])) { include_once '404.php'; exit(); }
$search_post = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.(isset($_GET['id']) ? $mysql->real_escape_string($_GET['id']) : 'a').'"');
require_once '../grplib-php/user-helper.php';

if(isset($_GET['mode']) && $_GET['mode'] == 'empathies') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# Display 404 if method isn't POST
include_once '404.php'; }
# Method is POST.
require_once '../grplib-php/miitoo.php';
miitooAdd('posts'); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'empathies_delete') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# Display 404 if method isn't POST
include_once '404.php'; }
# Method is POST.
require_once '../grplib-php/miitoo.php';
miitooDelete('posts'); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'replies') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# Display comments
if(!$search_post || $search_post->num_rows == 0) {
include_once '404.php'; exit();
}
$num_replies = prepared('SELECT COUNT(id) AS num FROM replies WHERE replies.reply_to_id = ?', [$_GET['id']])->fetch_assoc()['num'];
$replies = prepared('SELECT * FROM replies WHERE replies.reply_to_id = ? ORDER BY created_at LIMIT '.($num_replies > 19 ? ($num_replies - 20) : 120), [$_GET['id']]);
print '
<ul class="post-permalink-reply">
';
require_once 'lib/htmPost.php';
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/olv-url-enc.php';
$post = $search_post->fetch_assoc();
while($display_replies = $replies->fetch_assoc()) {
displayReply($post, $display_replies);
	}
print '
</ul>';
	exit();
}
require_once '../grplib-php/post-helper.php';
if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); }

$ogpost_result = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$mysql->real_escape_string($_GET['id']).'" LIMIT 1');

if($ogpost_result->num_rows == 0) {
jsonErr(404);
}

$ogpost = $ogpost_result->fetch_assoc();
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $ogpost['pid'])) {
require '404.php'; exit(); }

if(!commentCan($_SESSION['pid'], $ogpost['id'])) {
 		jsonErr(403); }

$is_post_valid = postValid($me, 'url');
$fastpost = ($mysql->query('SELECT replies.pid, replies.created_at FROM replies WHERE replies.pid = "'.$me['pid'].'" AND replies.created_at > NOW() - '.(isset($grp_config_max_postbuffertime) ? $grp_config_max_postbuffertime : '10').' ORDER BY replies.created_at DESC LIMIT 5')->num_rows != 0 ? true : false);
if($is_post_valid != 'ok' || $fastpost == true) {
if($fastpost == true) {
$error_message[] = 'Multiple posts cannot be made in such a short period of time. Please try posting again later.';
$error_code[] = 1515918; }
if($is_post_valid == 'blank') {
$error_message[] = 'The content you have entered is blank.
Please enter content into your post.';
$error_code[] = 1515001; }
elseif($is_post_valid == 'max') {
$error_message[] = 'You have exceeded the amount of characters that you can send.';
$error_code[] = 1515002; }
elseif($is_post_valid == 'min') {
$error_message[] = 'The URL you have specified is too short.';
$error_code[] = 1515004; }
elseif($is_post_valid == 'nohttp') {
$error_message[] = 'The URL you have specified is not of HTTPS.';
$error_code[] = 1515003; }
elseif($is_post_valid == 'nossl') {
$error_message[] = 'The URL you have specified is not of HTTP or HTTPS.';
$error_code[] = 1515003; }
elseif($is_post_valid == 'invalid') {
$error_message[] = 'The URL you have specified is not valid.';
$error_code[] = 1515005; }
elseif($is_post_valid == 'invalid_screenshot') {
$error_message[] = 'The screenshot you have specified is not valid.';
$error_code[] = 1515005; }
}
if(!empty($error_code)) {
http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
'message' => $error_message[0],
'error_code' => $error_code[0]
)], 'code' => 400));  exit();
}


require_once '../grplib-php/olv-url-enc.php';
$gen_olive_url = genURL();

if(empty($_POST['feeling_id']) || strval($_POST['feeling_id']) >= 6) { $_POST['feeling_id'] = 0; }

$createpost = $mysql->query('INSERT INTO replies(id, reply_to_id, pid, feeling_id, platform_id, body, screenshot, is_spoiler, created_from) VALUES (
"'.$gen_olive_url.'", 
"'.$ogpost['id'].'",
"'.$_SESSION['pid'].'",
"'.(!empty($_POST['feeling_id']) && is_numeric($_POST['feeling_id']) ? $mysql->real_escape_string($_POST['feeling_id']) : 0).'",
"1",
"'.$mysql->real_escape_string($_POST['body']).'",
"'.(!empty($_POST['screenshot']) ? $mysql->real_escape_string($_POST['screenshot']) : null).'",
"'.(!empty($_POST['is_spoiler']) ? $mysql->real_escape_string($_POST['is_spoiler']) : 0).'",
"'.$mysql->real_escape_string($_SERVER['REMOTE_ADDR']).'"
)');

if(!$createpost) {
http_response_code(500);
header('Content-Type: application/json'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
} else {
# Success, print post and send notification.
$search_post_created = $mysql->query('SELECT * FROM replies WHERE replies.id = "'.$gen_olive_url.'" LIMIT 1')->fetch_assoc();
require_once 'lib/htm.php';
require_once 'lib/htmPost.php';
require_once '../grplib-php/community-helper.php';
print displayReply($ogpost, $search_post_created);


		$get_empathy_poster = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$ogpost['pid'].'"')->fetch_assoc();

		if($_SESSION['pid'] == $get_empathy_poster['pid']) {
		# send notifications to all commenters	
		# CHANGE SOON
		$sql_news_getcomments = 'SELECT replies.pid FROM replies WHERE replies.reply_to_id = "'.$ogpost['id'].'" AND replies.pid != "'.$get_empathy_poster['pid'].'" AND replies.is_hidden = "0" GROUP BY replies.pid';
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
	$result_check_fastnews = $mysql->query('SELECT news.to_pid, news.created_at FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$get_empathy_poster['pid'].'" AND news.news_context = "4" AND news.created_at > NOW() - 1 ORDER BY news.created_at DESC');
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
 exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'screenshot.set_profile_post') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404.php'; }
# Put checks + update user's favorite post here.
if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); }

if($search_post->num_rows == 0) {
jsonErr(404);
}
$post = $search_post->fetch_assoc();

if($post['is_hidden'] == 1) { http_response_code(404); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit(); }

if($post['pid'] != $_SESSION['pid']) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); 
}
        $update_profile = $mysql->query('UPDATE profiles SET profiles.favorite_screenshot = "'.$post['id'].'" WHERE profiles.pid = "'.$_SESSION['pid'].'"');
        if(!$update_profile) {
http_response_code(500); header('Content-Type: application/json'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
		}
		else { 
header('Content-Type: application/json'); print 
json_encode(array('success' => 1));
}

 exit();
	
	}
if(isset($_GET['mode']) && $_GET['mode'] == 'violations') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404.php'; }
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
            header('Content-Type: application/json');
			json_encode(array('success' => 0, 'errors' => [], 'code' => $error_code[0])); }
    else {
$result_get_spamreports = $mysql->query('SELECT * FROM reports WHERE reports.source = "'.$_SESSION['pid'].'" AND reports.created_at > NOW() - 5');
if($result_get_spamreports->num_rows != 0) {
header('Content-Type: application/json'); print json_encode(array('success' => 1));
exit();
}
$reportcreate = $mysql->query('INSERT INTO reports (source, subject, type, reason, message) VALUES ("'.$_SESSION['pid'].'", "'.$post['id'].'", "0", "'.$mysql->real_escape_string($_POST['type']).'", "'.$mysql->real_escape_string($_POST['body']).'")');
        if(!$reportcreate) {
http_response_code(500);
header('Content-Type: application/json'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); } else {
header('Content-Type: application/json'); print 
json_encode(array('success' => 1)); }
		}  	exit(); }
if(isset($_GET['mode']) && $_GET['mode'] == 'set_spoiler') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404.php'; }
# Put checks + update post spoiler here.	
if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); }

if($search_post->num_rows == 0) {
jsonErr(404);
}
$post = $search_post->fetch_assoc();

if($post['is_hidden'] == 1) { http_response_code(404); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit(); }

if($post['pid'] != $_SESSION['pid']) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); 
}

if($post['is_spoiler'] == 1) {
http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400));  exit(); 
}

$update_post = $mysql->query('UPDATE posts SET posts.is_spoiler = "1" WHERE posts.id = "'.$post['id'].'"');

        if(!$update_post) {
http_response_code(500); header('Content-Type: application/json'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
		}
else {
header('Content-Type: application/json'); print json_encode(array('is_spoiler' => 1,'success' => 1));
}

 exit();	
}
if(isset($_GET['mode']) && $_GET['mode'] == 'delete') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404.php'; }
# Put checks + update post spoiler here.	
if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); }

if($search_post->num_rows == 0) {
jsonErr(404);
}
$post = $search_post->fetch_assoc();

if($post['is_hidden'] == 1) { http_response_code(404); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit(); }

if($post['pid'] != $_SESSION['pid']) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); 
}

$update_post = $mysql->query('UPDATE posts SET posts.is_hidden = "1", posts.hidden_resp = "1" WHERE posts.id = "'.$post['id'].'"');

        if(!$update_post) {
http_response_code(500); header('Content-Type: application/json'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
		}
else {
if($mysql->query('SELECT profiles.favorite_screenshot FROM profiles WHERE profiles.pid = "'.$_SESSION['pid'].'" AND profiles.favorite_screenshot = "'.$post['id'].'"')->num_rows != 0) {
$delete_user_favoritepost = $mysql->query('UPDATE profiles SET profiles.favorite_screenshot = "" WHERE profiles.pid = "'.$_SESSION['pid'].'"'); 
}	

require_once 'lib/htm.php';
print '    <title>Your Post</title>
<header id="header">
  
  <h1 id="page-title">Your Post</h1>

</header>

<div class="body-content track-error" id="post-permalink" data-track-error="deleted">
';
noContentWindow('Deleted by poster.');
print '
  </div>
</div>';
}

 exit();	
}


if(isset($_GET['mode'])) { if($_GET['mode'] != 'empathies' || $_GET['mode'] != 'replies' || $_GET['mode'] != 'violations' || $_GET['mode'] != 'set_spoiler' || $_GET['mode'] != 'screenshot_set_profile_post' || $_GET['mode'] != 'delete' || $_GET['mode'] != 'empathies_delete') { 
# Display 404 if mode is undefined
include_once '404.php'; } }
  
# If normal use; /posts/*
if(!$search_post) {
generalError(404, 'The post could not be found.');  exit(); } elseif($search_post->num_rows == 0) { generalError(404, 'The post could not be found.');  exit(); }
$post = $search_post->fetch_assoc();
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $post['pid'])) {
require '404.php'; exit(); }
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$post['pid'].'" LIMIT 1')->fetch_assoc();
$community = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$post['community_id'].'" LIMIT 1')->fetch_assoc();
$title = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$community['olive_title_id'].'" LIMIT 1')->fetch_assoc();
$pagetitle = !empty($_SESSION['pid']) && $_SESSION['pid'] == $post['pid'] ? 'Your Post' : htmlspecialchars($user['screen_name']).'\'s Post';
$admin_del = $post['is_hidden'] == '1' && $post['hidden_resp'] == 0;
require_once '../grplib-php/olv-url-enc.php';
if($post['is_hidden'] == '1') {
if($post['hidden_resp'] == 0 && (empty($_SESSION['pid']) || $_SESSION['pid'] != $post['pid'])) {
generalError(404, 'Deleted by adminsistrator.</p>
<p>Post ID: '.getPostID($post['id']));  exit(); }
if($post['hidden_resp'] == '1') {
generalError(404, 'Deleted by poster.');  exit(); }
}
# Success
require_once 'lib/htmCommunity.php';
require_once 'lib/htmPost.php';
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/post-helper.php';
$mii = getMii($user, $post['feeling_id']);
$empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'"');
printHeader(false); printMenu();
$replies = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$post['id'].'" ORDER BY created_at');
print $GLOBALS['div_body_head'];

if(!empty($_SESSION['pid'])) {
$search_settings = $mysql->query('SELECT * FROM settings_title WHERE settings_title.pid = "'.$_SESSION['pid'].'" AND settings_title.olive_title_id = "'.$title['olive_title_id'].'" LIMIT 1');
$pref_id = $search_settings->num_rows != 0 ? $search_settings->fetch_assoc()['value'] : 0;
} else {
$pref_id = 0;
	}

$canReply = !empty($_SESSION['pid']) && commentCan($_SESSION['pid'], $post['id']);
	print '<header id="header">';
if(!$admin_del) {
print '  <a id="header-reply-button"'.($canReply == false ? ' disabled' : null).' class="header-button reply-button'.($canReply == false ? ' disabled' : null).'"'.($canReply == true ? 'href="#"' : '').' data-modal-open="#add-reply-page">Comment</a>
'; } print '
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';
# End this div after post-permalink-comments.
print '<div class="body-content post-subtype-default" id="post-permalink">
';

	print '<div id="post-permalink-content"'.($empathies->num_rows == 0 ? ' class="no-empathy"' : null).'>';
	# Get community & title data.

	print '<div id="post-permalink-header">
';
if($mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$title['olive_title_id'].'" AND communities.type != "5"')->num_rows >= 2) {			
print '
<a href="/titles/'.$title['olive_title_id'].'" data-pjax="#body" class="title"><span><img src="'.getIcon($title).'" class="title-icon"></span></a>
'; }
print '<a'.($community['type'] == '5' ? null : ' href="/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'"').' data-pjax="#body" class="community"><img src="'.getIcon($community).'" class="community-icon"> '.htmlspecialchars($community['name']).'</a>
	  </div>';

      print '<div id="post-permalink-body"'.($user['official_user'] == 1 ? ' class="official-user"' : null).'>
      <a href="/users/'.htmlspecialchars($user['user_id']).'" data-pjax="#body" class="user-icon-container'.($mii['official'] == true ? ' official-user' : null).'"><img src="'.$mii['output'].'" class="user-icon"></a>';
if($user['official_user'] == 1) {
	print '
	<p class="user-organization">'.htmlspecialchars($user['organization']).'</p>
	'; } 
	 print '<p class="user-name">'.htmlspecialchars($user['screen_name']).'<span class="user-id">'.htmlspecialchars($user['user_id']).'</span></p><p class="timestamp">'.humanTiming(strtotime($post['created_at'])).'
	 <span class="spoiler-status'.($post['is_spoiler'] == 1 ? ' spoiler' : null).'">Spoilers</span></p>
	 
	 <div class="post-content'.($post['_post_type'] == 'artwork' ? ' memo' : null).'">
	 ';
	 if($admin_del) {
	print '<p class="deleted-message">Deleted by administrator.</p>
	<p class="deleted-message">Post ID: '.getPostID($post['id']).'</p>';
	 }
if(!empty($post['screenshot'])) {
	print '
	<div class="capture-container">
           <img src="'.htmlspecialchars($post['screenshot']).'" class="capture">
	 </div>'; }
	 if($post['_post_type'] == 'artwork') {
print '
<p class="post-content-memo"><img src="'.htmlspecialchars($post['body']).'" class="post-memo"></p>

	 </div>'; } else {
	 print '

              <p class="post-content-text">'.htmlspecialchars($post['body']).'</p>
	 </div>'; }

if(!empty($post['url']) && strpos($post['url'], 'www.youtube.com/watch?v=') !== false) {
if(substr($post['url'], 0, 4) == "http" || substr($post['url'], 0, 5) == "https") {
$hvideopost = true;
$ytvidurl = substr($post['url'], (substr($post['url'], 0, 5) == "https" ? 32 : 31), 11);
print '<div id="post-video">
        <iframe id="post-video-player" data-video-id="'.$ytvidurl.'" frameborder="0" allowfullscreen="1" title="YouTube video player" width="900" height="504" src="https://www.youtube.com/embed/'.$ytvidurl.'?rel=0&amp;modestbranding=1&amp;iv_load_policy=3&amp;enablejsapi=1&amp;widgetid=1"></iframe>

      </div>';
} }
	
if(!$admin_del) {
        // Has the user given this post an empathy?
if(!empty($_SESSION['pid'])) {
$canmiitoo = miitooCan($_SESSION['pid'], $post['id'], 'posts'); 
$my_empathy_added = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'" LIMIT 1')->num_rows == 1;
}
	
	 print '<div class="post-meta">
	 ';
if(!empty($_SESSION['pid'])) {
if($_SESSION['pid'] == $post['pid']) {
print '<a href="#" role="button" class="edit-button edit-post-button" data-modal-open="#edit-post-page">Edit</a>';	}
else {
$is_report_disabled = $mii['official'] != true;
print '<a '.($is_report_disabled ? 'href="#" ' : null).'role="button"'.($is_report_disabled ? null : ' disabled').' class="report-button'.($is_report_disabled ? null : ' disabled').'" data-modal-open="#report-violation-page" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-support-text="'.getPostID($post['id']).'" data-action="/posts/'.$post['id'].'/violations" data-is-post="1" data-is-permalink="1" data-can-report-spoiler="'.($post['is_spoiler'] == 1 ? '1' : '0').'" data-community-id="'.$community['olive_community_id'].'" data-url-id="'.$post['id'].'" data-track-label="default" data-title-id="'.$title['olive_title_id'].'" data-track-action="openReportModal" data-track-category="reportViolation">Report Violation</a>'; }
}
else {
print '<a disabled role="button" class="report-button disabled">Report Violation</a>';	 
}

print '
        <div class="expression">
		';
        print '<button type="button" '.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : null).' 
		class="submit miitoo-button'.(!empty($_SESSION['pid']) && $my_empathy_added == true ? ' empathy-added' : null).'" 
		data-feeling="'.$mii['feeling'].'" 
		data-action="/posts/'.$post['id'].'/empathies" 
		data-other-empathy-count="'.(isset($my_empathy_added) && $my_empathy_added == true ? $empathies->num_rows - 1 : $empathies->num_rows).'" 
		data-sound="SE_WAVE_MII_'.(isset($my_empathy_added) && $my_empathy_added == true ? 'CANCEL' : 'ADD').'" 
		data-community-id="'.$community['olive_community_id'].'" 
		data-url-id="'.$post['id'].'" 
		data-track-label="default" 
		data-title-id="'.$title['olive_title_id'].'" 
		data-track-action="yeah" 
		data-track-category="empathy">'.(isset($my_empathy_added) && $my_empathy_added == true ? $mii['miitoo_delete'] : (!empty($mii['miitoo']) ? $mii['miitoo'] : 'Yeah!')).'</button>
        </div>';
      if(!isset($hvideopost) && !empty($post['url'])) {
	  print '
	  <a href="#" class="link-button" data-modal-open="#confirm-url-page"></a>'; }
	  print '

      </div>';
	  print '</div>
	  <div class="post-permalink-feeling">
      <p class="post-permalink-feeling-text"></p>
	  <div class="post-permalink-feeling-icon-container">
	  ';
$empathies_display = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'"'.(!empty($_SESSION['pid']) ? ' AND empathies.pid != "'.$_SESSION['pid'].'"' : '').' ORDER BY empathies.created_at DESC LIMIT 36');
	  if(!empty($_SESSION['pid'])) {
print displayempathy($post, $post, true, false);
	  }
$i = 1;
$numbr = $empathies_display->num_rows;
while($row_empathies = $empathies_display->fetch_assoc()) {
print displayempathy($row_empathies, $post, false, ($empathies_display->num_rows == 36 && $i > $numbr ? true: false));
$i++;
}
print '</div>
</div>
';
 	   
   # End of post_permalink_content
	print '</div>';

	#  Put comments here.	
    print '<div id="post-permalink-comments">';
if($replies->num_rows > 19) {
print '
<a href="/posts/'.$post['id'].'/replies" class="more-button all-replies-button" data-reply-count="'.$replies->num_rows.'"><span>Show all comments ('.$replies->num_rows.')</span></a>';
}
print '
<ul class="post-permalink-reply">

';
if($replies->num_rows > 19) {
$replies = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$post['id'].'" ORDER BY created_at LIMIT 20 OFFSET '.($replies->num_rows - 20));
}
    while($reply = $replies->fetch_assoc()) {
displayReply($post, $reply);
	}
print '
</ul>

  </div>
  ';
} print '
       </div>';
	# Add reply page
if($canReply) {
postForm('replies', $post, $me);
}
	# Posts footer, mandatory for a posts page.
postsFooter('posts', $post);
      if(!isset($hvideopost) && !empty($post['url'])) {
	print '<div id="confirm-url-page" class="window-page none" data-modal-types="confirm-url">
  <div class="window">
    <h1 class="window-title">Open Link</h1>
    <div class="window-body"><div class="window-body-inner">
      <p>This web page will be displayed in the Internet browser.<br>
<br>
Do you want to close Miiverse and view this web page?</p>
      <p class="link-url">'.htmlspecialchars($post['url']).'</p>
    </div></div>          <div class="window-bottom-buttons">
      <input type="button" class="olv-modal-close-button button" value="Back" data-sound="SE_WAVE_CANCEL">
      <input type="submit" class="post-button button" value="Open Link">';
	      }

print $GLOBALS['div_body_head_end'];
(empty($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');