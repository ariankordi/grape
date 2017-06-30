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
require_once '../grplib-php/miitoo.php';
miitooAdd('replies'); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'empathies_delete') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# Display 404 if method isn't POST
include_once '404.php'; }
# Method is POST.
require_once '../grplib-php/miitoo.php';
miitooDelete('replies'); exit();
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
$reportcreate = $mysql->query('INSERT INTO reports (source, subject, type, reason, message) VALUES ("'.$_SESSION['pid'].'", "'.$post['id'].'", "1", "'.$mysql->real_escape_string($_POST['type']).'", "'.$mysql->real_escape_string($_POST['body']).'")');
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
http_response_code(404); header('Content-Type: application/json'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit();
}
$post = $search_post->fetch_assoc();

if($post['is_hidden'] == 1) { http_response_code(404); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit(); }

if($post['pid'] != $_SESSION['pid']) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); 
}

if($post['is_spoiler'] == 1) {
http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400));  exit(); 
}

$update_post = $mysql->query('UPDATE replies SET replies.is_spoiler = "1" WHERE replies.id = "'.$post['id'].'"');

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
http_response_code(404); header('Content-Type: application/json'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit();
}
$post = $search_post->fetch_assoc();

if($post['is_hidden'] == 1) { http_response_code(404); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit(); }

if($post['pid'] != $_SESSION['pid']) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); 
}

$update_post = $mysql->query('UPDATE replies SET replies.is_hidden = "1", replies.hidden_resp = "1" WHERE replies.id = "'.$post['id'].'"');

        if(!$update_post) {
http_response_code(500); header('Content-Type: application/json'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
		}
else {
require_once 'lib/htm.php';
print '    <title>Your Comment</title>
<header id="header">
  
  <h1 id="page-title">Your Comment</h1>

</header>

<div class="body-content track-error" id="post-permalink" data-track-error="deleted">
';
noContentWindow('Deleted by the author of the comment.');
print '
  </div>
</div>';
}

 exit();	
}

if(isset($_GET['mode'])) { if($_GET['mode'] != 'empathies' || $_GET['mode'] != 'violations' || $_GET['mode'] != 'set_spoiler' || $_GET['mode'] != 'delete' || $_GET['mode'] != 'empathies_delete') {
# Display 404 if mode is undefined
include_once '404.php'; } }

# Else, normal use, aka /replies/*. Display comment.
if(!$search_post) {
generalError(404, 'The post could not be found.');  exit(); } elseif($search_post->num_rows == 0) { generalError(404, 'The post could not be found.');  exit(); }
$reply = $search_post->fetch_assoc();
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$reply['pid'].'" LIMIT 1')->fetch_assoc();

$ogpost = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$reply['reply_to_id'].'" LIMIT 1')->fetch_assoc();
$ogpost_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$ogpost['pid'].'" LIMIT 1')->fetch_assoc();
$pagetitle = !empty($_SESSION['pid']) && $_SESSION['pid'] == $reply['pid'] ? 'Your Comment' : htmlspecialchars($user['screen_name']).'\'s Comment';
$admin_del = $reply['is_hidden'] == '1' && $reply['hidden_resp'] == 0;
require_once '../grplib-php/olv-url-enc.php';
if($reply['is_hidden'] == '1') {
if($reply['hidden_resp'] == 0 && (empty($_SESSION['pid']) || $_SESSION['pid'] != $reply['pid'])) {
generalError(404, 'Deleted by adminsistrator.</p>
<p>Comment ID: '.getPostID($reply['id']));  
 exit();
}
if($reply['hidden_resp'] == '1') {
generalError(404, 'Deleted by the author of the comment.'); 
 exit(); }
}
# Success
require_once 'lib/htmPost.php';
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/post-helper.php';
$mii = getMii($user, $reply['feeling_id']);
$ogpost_mii = getMii($ogpost_user, $reply['feeling_id']);
printHeader(false); printMenu();

    print $GLOBALS['div_body_head'];
	print '<header id="header">
	
	  <h1 id="page-title">'.$pagetitle.'</h1>
	
	</header><div class="body-content" id="reply-permalink">';
	
$truncate_post_bodyp1 = mb_substr((htmlspecialchars($ogpost['body'])), 0, 20);
$truncate_post_body = (mb_strlen($truncate_post_bodyp1) >= 20 ? "$truncate_post_bodyp1..." : $truncate_post_bodyp1);

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
';
if($admin_del) {
print '        <p class="deleted-message">Deleted by administrator.</p>
        <p class="deleted-message">Comment ID: '.getPostID($reply['id']).'</p>';
}
print '

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
		data-track-category="empathy">'.(isset($my_empathy_added) && $my_empathy_added == true ? $mii['miitoo_delete'] : (!empty($mii['miitoo']) ? $mii['miitoo'] : 'Yeah!')).'</button>
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
';
if(!$admin_del) {
print '	  <div class="post-permalink-feeling">
      <p class="post-permalink-feeling-text"></p>
	  <div class="post-permalink-feeling-icon-container">
	  ';
$empathies_display = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'"'.(!empty($_SESSION['pid']) ? ' AND empathies.pid != "'.$_SESSION['pid'].'"' : '').' ORDER BY empathies.created_at DESC LIMIT 36');
	  if(!empty($_SESSION['pid'])) {
print displayempathy($reply, $reply, true, false);
	  }
$i = 1;
$numbr = $empathies_display->num_rows;
while($row_empathies = $empathies_display->fetch_assoc()) {
print displayempathy($row_empathies, $reply, false, ($empathies_display->num_rows == 36 && $i > $numbr == $lastArrayKey ? true: false));
$i++;
}
print '
      </div>';
}
	# End ul
	print '</ul>
  </li>';
    print '</div>';
	print '</div>';
   postsFooter('replies', $reply);
    print $GLOBALS['div_body_head_end'];
(empty($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');