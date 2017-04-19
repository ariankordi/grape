<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$search_post = $mysql->query('SELECT * FROM replies WHERE replies.id = "'.(isset($_GET['id']) ? $mysql->real_escape_string($_GET['id']) : 'a').'"');
if(isset($_GET['mode']) && $_GET['mode'] == 'empathies') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# Display 404 if method isn't POST
include_once '404alli.php'; }
# Method is POST.
require_once '../grplib-php/community-helper.php';

		$search_post_for_empathy = $mysql->query('SELECT * FROM replies WHERE replies.id = "'.(isset($_GET['id']) ? $mysql->real_escape_string($_GET['id']) : 'a').'" AND replies.is_hidden != "1" LIMIT 1');
		if($search_post_for_empathy->num_rows == 0) { http_response_code(404); header('Content-Type: application/json; charset=utf-8');
		json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit(); }

		if(empty($_SESSION['pid'])) {
		http_response_code(403); header('Content-Type: application/json; charset=utf-8');
		json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }

$post = $search_post->fetch_assoc();
if(!empty($_SESSION['pid'])) {		
if(!miitooCan($_SESSION['pid'], $post['id'], 'replies')) {
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
	$check_fastnews = $mysql->query('SELECT news.to_pid, news.created_at FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$getposter['pid'].'" AND news.news_context = "3" AND news.created_at > NOW() - 8 ORDER BY news.created_at DESC');
    if($check_fastnews->num_rows == 0) {
    $check_ownusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$getposter['pid'].'" AND news.news_context = "3" AND news.id = "'.$post['id'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
	$check_mergedusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$getposter['pid'].'" AND news.news_context = "3" AND news.id = "'.$post['id'].'" AND news.merged IS NOT NULL AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if($check_mergedusernews->num_rows != 0) {
	$result_update_mergedusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$check_mergedusernews['merged'].'"');	
	} elseif($check_ownusernews->num_rows != 0) {
	$result_update_ownusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$check_ownusernews->fetch_assoc()['news_id'].'"'); }
else {
$result_update_newsmergesearch = $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$getposter['pid'].'" AND news.id = "'.$post['id'].'" AND news.created_at > NOW() - 7200 AND news.news_context = "3" ORDER BY news.created_at DESC');	
if($result_update_newsmergesearch->num_rows != 0) {
$row_update_newsmergesearch = $result_update_newsmergesearch->fetch_assoc();
$result_newscreatemerge = $mysql->query('INSERT INTO grape.news(from_pid, to_pid, id, merged, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$getposter['pid'].'", "'.$post['id'].'", "'.$row_update_newsmergesearch['news_id'].'", "3", "0")');

$result_update_newsformerge = $mysql->query('UPDATE news SET has_read = "0", created_at = NOW() WHERE news.news_id = "'.$row_update_newsmergesearch['news_id'].'"');
		}
else {
        $result_newscreate = $mysql->query('INSERT INTO grape.news(from_pid, to_pid, id, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$getposter['pid'].'", "'.$post['id'].'", "3", "0")'); 	
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

$update_post = $mysql->query('UPDATE replies SET replies.is_spoiler = "1" WHERE replies.id = "'.$post['id'].'"');

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

$update_post = $mysql->query('UPDATE replies SET replies.is_hidden = "1", replies.hidden_resp = "1" WHERE replies.id = "'.$post['id'].'"');

        if(!$update_post) {
http_response_code(500); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
		}
else {
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
$reportcreate = $mysql->query('INSERT INTO reports (source, subject, type, reason, message) VALUES ("'.$_SESSION['pid'].'", "'.$post['id'].'", "1", "'.$mysql->real_escape_string($_POST['type']).'", "'.$mysql->real_escape_string($_POST['body']).'")');
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
$reply = $search_post->fetch_assoc();
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$reply['pid'].'" LIMIT 1')->fetch_assoc();
$ogpost = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$reply['reply_to_id'].'" LIMIT 1')->fetch_assoc();
if($reply['is_hidden'] == '1') {
$pagetitle = 'Error'; printHeader('old'); printMenu('old');
if($reply['hidden_resp'] == 0) {
require '../grplib-php/olv-url-enc.php';
notFound('Deleted by adminsistrator.</p>
<p>Comment ID: '.getPostID($reply['id']), true);  }
if($reply['hidden_resp'] == '1') {
notFound ('Deleted by poster.', true); }
# Other deleted messages
printFooter('old'); grpfinish($mysql); exit();
}

# Success
require_once 'lib/htmCommunity.php';
require_once '../grplib-php/community-helper.php';
require_once 'lib/htmPost.php';
require_once '../grplib-php/olv-url-enc.php';
$pagetitle = (!empty($_SESSION['pid']) && $_SESSION['pid'] == $reply['pid'] ? 'Your Comment' : htmlspecialchars($user['screen_name']).'\'s Comment');
$mii = getMii($user, $reply['feeling_id']);
printHeader('old'); printMenu('old');
print '<div id="main-body">

<div id="post-content" class="post reply-permalink-post">
';
$truncate_post_bodyp1 = mb_substr((htmlspecialchars($ogpost['body'])), 0, 20, 'utf-8');
$truncate_post_body = (mb_strlen($truncate_post_bodyp1, 'utf-8') >= 20 ? "$truncate_post_bodyp1..." : $truncate_post_bodyp1);

$ogpost_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$ogpost['pid'].'" LIMIT 1')->fetch_assoc();
$ogpost_community = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$ogpost['community_id'].'" LIMIT 1')->fetch_assoc();
$ogpostmii = getMii($ogpost_user, $ogpost['feeling_id']);
print '<a class="post-permalink-button info-ticker arrow-left-button" href="/posts/'.$ogpost['id'].'">
    <span><span class="post-user-description">View <img src="'.$ogpostmii['output'].'" class="user-icon">'.htmlspecialchars($ogpost_user['screen_name']).'\'s post (';
print $ogpost['_post_type'] == 'artwork' ? 'handwritten' : $truncate_post_body;
print ') for this comment.</span></span>
  </a>';

print '
<div id="reply-'.$reply['id'].'" class="my'.($mii['official'] == true ?  ' official-user' : '').'">
';
print '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="icon-container'.($mii['official'] == true ?  ' official-user' : '').'"><img src="'.$mii['output'].'" class="icon"></a>';
print '<p class="timestamp-container">
    <span class="spoiler-status'.($reply['is_spoiler'] == 1 ? ' spoiler' : '').'">Spoilers ·</span>
    <span class="timestamp">'.humanTiming(strtotime($reply['created_at'])).'</span>  </p>
	';

print '<p class="user-name"><a href="/users/'.htmlspecialchars($user['user_id']).'">'.htmlspecialchars($user['screen_name']).'</a></p>
<p class="community-container"><a'.($ogpost_community['type'] == 5 ? '' : ' href="/titles/'.$ogpost_community['olive_title_id'].'/'.$ogpost_community['olive_community_id'].'"').'><img src="'.getIcon($ogpost_community).'" class="community-icon">'.htmlspecialchars($ogpost_community['name']).'</a></p>';

print '<div class="body">
';
# drawing or body?
print '
        <p class="reply-content-text">'.htmlspecialchars($reply['body']).'</p>
';
if(!empty($reply['screenshot'])) {
	print '
<div class="screenshot-container still-image"><img src="'.htmlspecialchars($reply['screenshot']).'"></div>'; }
if(!empty($_SESSION['pid'])) {
$canmiitoo = miitooCan($_SESSION['pid'], $reply['id'], 'replies'); 
$my_empathy_added = ($mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'" LIMIT 1')->num_rows == 1 ? true : false);
}

$empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'"');
print '
    <div class="post-meta">
      <button type="button"'.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').' class="symbol submit empathy-button'.(isset($my_empathy_added) && $my_empathy_added == true ? ' empathy-added' : '').''.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').'" data-feeling="'.($mii['feeling'] ? $mii['feeling'] : 'normal').'" data-action="/replies/'.$reply['id'].'/empathies"><span class="empathy-button-text">'.(isset($my_empathy_added) && $my_empathy_added == true ? 'Unyeah' : (!empty($usermii['miitoo']) ? $mii['miitoo'] : 'Yeah!')).'</span></button>
      <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count">'.$empathies->num_rows.'</span></div>
    </div>
    <div id="empathy-content'.($empathies->num_rows == 0 ? '" class="none"' : '"').'>
';
$empathies_display = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'"'.(!empty($_SESSION['pid']) ? ' AND empathies.pid != "'.$_SESSION['pid'].'"' : '').' ORDER BY empathies.created_at DESC LIMIT 8');
	  if(!empty($_SESSION['pid'])) {
print displayempathy($reply, $reply, true);
	  }
while($row_empathies = $empathies_display->fetch_assoc()) {
print displayempathy($row_empathies, $reply, false);
}
print '
    </div>

    <div class="post-meta">
	';
if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $reply['pid']) {
print '<button type="button" class="symbol button edit-button edit-reply-button" data-modal-open="#edit-post-page"><span class="symbol-label">Edit</span></button>';
} elseif(!empty($_SESSION['pid']) && $user['official_user'] != 1) {
print '<div class="report-buttons-content">
      <button type="button" class="button" data-modal-open="#report-violation-page" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-support-text="'.getPostID($reply['id']).'" data-action="/replies/'.$reply['id'].'/violations" data-is-permalink="1" data-can-report-spoiler="'.($reply['is_spoiler'] == 1 ? '1' : '0').'">Report Violation</button>
    </div>';
}
print '
</div>
';
print reportTemplate('replies');
print editTemplate('replies', $reply);

print '
  </div>';

print '</div>';

print '
</div>
</div>';
printFooter('old');