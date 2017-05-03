<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if(empty($_GET['id'])) { include_once '404.php'; exit(); }
$search_post = $mysql->query('SELECT * FROM replies WHERE replies.id = "'.(isset($_GET['id']) ? $mysql->real_escape_string($_GET['id']) : 'a').'"');

# If /replies/*/empathies, /replies/*/violations, etc. is specified.
if(isset($_GET['mode']) && $_GET['mode'] == 'empathies') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# Display 404 if method isn't POST
include_once '404.php'; }
# Method is POST.
require_once '../grplib-php/community-helper.php';

		$search_post_for_empathy = $mysql->query('SELECT * FROM replies WHERE replies.id = "'.(isset($_GET['id']) ? $mysql->real_escape_string($_GET['id']) : 'a').'" AND replies.is_hidden != "1" LIMIT 1');
		if($search_post_for_empathy->num_rows == 0) { http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print
		json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit(); }

		if(empty($_SESSION['pid'])) {
		http_response_code(403); header('Content-Type: application/json; charset=utf-8');
		print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }

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
include_once '404.php'; }
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
if($mysql->query('SELECT profiles.favorite_screenshot FROM profiles WHERE profiles.pid = "'.$_SESSION['pid'].'" AND profiles.favorite_screenshot = "'.$post['id'].'"')->num_rows == 0); {
$delete_user_favoritepost = $mysql->query('UPDATE profiles SET profiles.favorite_screenshot = "" WHERE profiles.pid = "'.$_SESSION['pid'].'"'); }	

require_once 'lib/htm.php';
print '    <title>Your Comment</title>
<header id="header">
  
  <h1 id="page-title">Your PComment</h1>

</header>

<div class="body-content track-error" id="post-permalink" data-track-error="deleted">
';
noContentWindow('Deleted by the author of the comment.');
print '
  </div>
</div>';
}

grpfinish($mysql); exit();	
}

if(isset($_GET['mode'])) { if($_GET['mode'] != 'empathies' || $_GET['mode'] != 'violations' || $_GET['mode'] != 'set_spoiler' || $_GET['mode'] != 'delete') {
# Display 404 if mode is undefined
include_once '404.php'; } }

# Else, normal use, aka /replies/*. Display comment.
if(!$search_post) {
generalError(404, 'The post could not be found.'); grpfinish($mysql); exit(); } elseif($search_post->num_rows == 0) { generalError(404, 'The post could not be found.'); grpfinish($mysql); exit(); }
$reply = $search_post->fetch_assoc();
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$reply['pid'].'" LIMIT 1')->fetch_assoc();

$ogpost = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$reply['reply_to_id'].'" LIMIT 1')->fetch_assoc();
$ogpost_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$ogpost['pid'].'" LIMIT 1')->fetch_assoc();
if($reply['is_hidden'] == '1') {
if($reply['hidden_resp'] == 0) {
require '../grplib-php/olv-url-enc.php';
generalError(404, 'Deleted by adminsistrator.</p>
<p>Comment ID: '.getPostID($reply['id']));  }
if($reply['hidden_resp'] == '1') {
generalError(404, 'Deleted by the author of the comment.'); }
grpfinish($mysql); exit();
}
# Success
require_once 'lib/htmPost.php';
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/post-helper.php';
$mii = getMii($user, $reply['feeling_id']);
$ogpost_mii = getMii($user, $reply['feeling_id']);
$pagetitle = htmlspecialchars($user['screen_name']).'\'s Comment';
printHeader(false); printMenu();

    print $GLOBALS['div_body_head'];
	print '<header id="header">
	
	  <h1 id="page-title">'.$pagetitle.'</h1>
	
	</header><div class="body-content" id="reply-permalink">';
	
$truncate_post_bodyp1 = mb_substr((htmlspecialchars($ogpost['body'])), 0, 20, 'utf-8');
$truncate_post_body = (mb_strlen($truncate_post_bodyp1, 'utf-8') >= 20 ? "$truncate_post_bodyp1..." : $truncate_post_bodyp1);

print '
  <a class="post-permalink-button info-ticker" href="/posts/'.$ogpost['id'].'" data-pjax="#body">
    <span>View <span class="post-user-description"><img src="'.$ogpost_mii['output'].'" class="user-icon">'.htmlspecialchars($ogpost_user['screen_name']).'\'s post ('.($ogpost['_post_type'] == 'artwork' ? 'handwritten' : $truncate_post_body).')</span> for this comment.</span>';
	print '
  </a>
  <div id="post-permalink-comments">
  ';
  # Add no-empathy above
  print '<ul class="post-permalink-reply">
    <li>';

print '<a href="/users/'.htmlspecialchars($user['user_id']).'" data-pjax="#body" class="scroll-focus user-icon-container'.($mii['official'] ? ' official-user' : '').'"><img src="'.$mii['output'].'" class="user-icon"></a>';
print '
<div class="reply-content">
        <header>
          <span class="user-name">'.htmlspecialchars($user['screen_name']).'</span>
          <span class="timestamp">'.humanTiming(strtotime($reply['created_at'])).'</span>
		  <span class="spoiler-status'.($reply['is_spoiler'] == 1 ? ' spoiler' : null).'">Spoilers</span>
	    	  </header>



            <p class="reply-content-text">'.htmlspecialchars($reply['body']).'</p>';
	 if(!empty($reply['screenshot'])) {
	print '<div class="capture-container">
          <img src="'.htmlspecialchars($reply['screenshot']).'" class="capture">
        </div>
		'; 
	 }

        // Has the user given this post an empathy?
if(!empty($_SESSION['pid'])) {
$canmiitoo = miitooCan($_SESSION['pid'], $reply['id'], 'replies'); 
$my_empathy_added = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'" LIMIT 1')->num_rows == 1;
}
$empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'"');
require '../grplib-php/olv-url-enc.php';

	 print '<div class="reply-meta">
        

        <div class="expression">
		';
        print '<button type="button" '.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : null).' 
		class="submit miitoo-button'.(!empty($_SESSION['pid']) && $my_empathy_added == true ? ' empathy-added' : null).'" 
		data-feeling="'.$mii['feeling'].'" 
		data-action="/replies/'.$reply['id'].'/empathies" 
		data-other-empathy-count="'.(isset($my_empathy_added) && $my_empathy_added == true ? $empathies->num_rows - 1 : $empathies->num_rows).'" 
		data-sound="SE_WAVE_MII_'.(isset($my_empathy_added) && $my_empathy_added == true ? 'CANCEL' : 'ADD').'" 
		data-url-id="'.$reply['id'].'" 
		data-track-label="default" 
		data-track-action="yeah" 
		data-track-category="empathy">'.(isset($my_empathy_added) && $my_empathy_added == true ? 'Unyeah' : (!empty($usermii['miitoo']) ? $mii['miitoo'] : 'Yeah!')).'</button>
        </div>';

if(!empty($_SESSION['pid'])) {
if($_SESSION['pid'] == $reply['pid']) {
print '<a href="#" role="button" class="edit-button edit-reply-button" data-modal-open="#edit-post-page">Edit</a>';	}
else {
$is_report_disabled = $mii['official'] != true;
print '<a '.($is_report_disabled ? 'href="#" ' : null).'role="button"'.($is_report_disabled ? null : ' disabled').' class="report-button'.($is_report_disabled ? null : ' disabled').'" data-modal-open="#report-violation-page" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-support-text="'.getPostID($reply['id']).'" data-action="/posts/'.$reply['id'].'/violations" data-is-post="1" data-is-permalink="1" data-can-report-spoiler="'.($reply['is_spoiler'] == 1 ? '1' : '0').'" data-community-id="" data-url-id="'.$reply['id'].'" data-track-label="default" data-title-id="" data-track-action="openReportModal" data-track-category="reportViolation">Report Violation</a>'; }
}
else {
print '<a disabled role="button" class="report-button disabled">Report Violation</a>';	 
}

	  print '</div>
	  <div class="post-permalink-feeling">
      <p class="post-permalink-feeling-text"></p>
	  <div class="post-permalink-feeling-icon-container">
	  ';
$empathies_display = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'"'.(!empty($_SESSION['pid']) ? ' AND empathies.pid != "'.$_SESSION['pid'].'"' : '').' ORDER BY empathies.created_at DESC LIMIT 15');
	  if(!empty($_SESSION['pid'])) {
print displayempathy($reply, $reply, true);
	  }
while($row_empathies = $empathies_display->fetch_assoc()) {
print displayempathy($row_empathies, $reply, false);
}
print '
      </div>';
	# End ul
	print '</ul>
  </li>';
    print '</div>';
	print '</div>';
   postsFooter('replies', $reply);
    print $GLOBALS['div_body_head_end'];
(empty($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');