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
generalError(404, 'Deleted by poster.'); }
grpfinish($mysql); exit();
}
# Success
$mii = getMii($user, $reply['feeling_id']);
$ogpost_mii = getMii($user, $reply['feeling_id']);
    print $GLOBALS['div_body_head'];
	print '<header id="header">
	
	  <h1 id="page-title">'.$pagetitle.'</h1>
	
	</header><div class="body-content" id="reply-permalink">';
	
$truncate_post_bodyp1 = mb_substr((htmlspecialchars($ogpost['body'])), 0, 20, 'utf-8');
$truncate_post_body = (mb_strlen($truncate_post_bodyp1, 'utf-8') >= 20 ? "$truncate_post_bodyp1..." : $truncate_post_bodyp1);

print '
  <a class="post-permalink-button info-ticker" href="/posts/'.$row_reply_ogpost['id'].'" data-pjax="#body">
    <span>View <span class="post-user-description"><img src="'.$mii_ogpost_face_output.'" class="user-icon">';
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

print '<a href="/users/'.htmlspecialchars($row_reply_user['user_id']).'" data-pjax="#body" class="scroll-focus user-icon-container'.$is_replier_official_user.'"><img src="'.$mii_face_output.'" class="user-icon"></a>';
print '
<div class="reply-content">
        <header>
          <span class="user-name">'.htmlspecialchars($row_reply_user['screen_name']).'</span>
          <span class="timestamp">'.humanTiming(strtotime($row_reply['created_at'])).'</span>
		  <span class="spoiler-status">Spoilers</span>
		  ';
		  if($row_reply['is_spoiler'] == '1') { 
	 print '<span class="spoiler-status spoiler">Spoilers</span>'; }
	print '	  </header>



            <p class="reply-content-text">'.htmlspecialchars($row_reply['body']).'</p>';
	 if(isset($row_reply['screenshot'])) {
     if(strlen($row_reply['screenshot']) > 3) {
	print '<div class="capture-container">
          <img src="'.htmlspecialchars($row_reply['screenshot']).'" class="capture">
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
$sql_hasempathy = 'SELECT * FROM empathies WHERE empathies.id = "'.$mysql->real_escape_string($row_reply['id']).'" AND empathies.pid = "'.$_SESSION['pid'].'"';
$result_hasempathy = $mysql->query($sql_hasempathy);
}

$sql_empathyamt = 'SELECT * FROM empathies WHERE empathies.id = "'.$mysql->real_escape_string($row_reply['id']).'"';
$result_empathyamt = $mysql->query($sql_empathyamt);
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
        print '<button type="button" '.$can_reply_user_miitoo.' 
		class="submit miitoo-button'.$has_reply_miitoo_given.'" 
		data-feeling="'.$mii_face_feeling.'" 
		data-action="/replies/'.$row_reply['id'].'/empathies" 
		data-other-empathy-count="'.$reply_miitoo_amt_other.'" 
		data-sound="'.$has_reply_miitoo_given_snd.'" 
		data-url-id="'.$row_reply['id'].'" 
		data-track-label="default" 
		data-track-action="yeah" 
		data-track-category="empathy">'.$mii_face_miitoo.'</button>
        </div>';

print $reply_meta_button_output;		
		print '</div>
		';

print '        <div class="post-permalink-feeling">
<p class="post-permalink-feeling-text"></p>';

if(isset($_SESSION['pid'])) {	  
if($_SESSION['pid']) {
$sql_reply_me = 'SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"';
$result_reply_me = $mysql->query($sql_reply_me);
$row_reply_me = mysqli_fetch_assoc($result_reply_me); 	}  }
	  
	  if(isset($_SESSION['signed_in'])) {
	  if($row_reply_me['mii_hash']) {
	  $my_mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$row_reply_me['mii_hash'].'_'.$mii_face_feeling.'_face.png';
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

	 print '<div class="post-permalink-feeling-icon-container"><a href="/users/'.htmlspecialchars($_SESSION['user_id']).'" data-pjax="#body" class="post-permalink-feeling-icon visitor'.$is_yeaher_official_user.'"'.$has_reply_miitoo_given_v2.'><img src="'.htmlspecialchars($my_mii_face_output).'" class="user-icon"></a>'; 
	  }
	 # Put other users' empathies here.

	$sql_reply_empathies2 = 'SELECT * FROM empathies WHERE empathies.id = "'.$row_reply['id'].'" ORDER BY empathies.created_at DESC LIMIT 36';
	$result_reply_empathies2 = $mysql->query($sql_reply_empathies2);	 
	while($row_reply_empathies2 = mysqli_fetch_assoc($result_reply_empathies2)) {	

$sql_reply_empathies_user = 'SELECT * FROM people WHERE people.pid = "'.$row_reply_empathies2['pid'].'"';
$result_reply_empathies_user = $mysql->query($sql_reply_empathies_user);
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
	  $our_mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$row_reply_empathies_user['mii_hash'].'_'.$mii_face_feeling.'_face.png';
	  }
	  else {
	  if($row_reply_empathies_user['user_face']) {
	  $our_mii_face_output = $row_reply_empathies_user['user_face'];
	  }
	  else {
	  $our_mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
	  }
	  }
			
		print '<a href="/users/'.$row_reply_empathies_user['user_id'].'" data-pjax="#body" class="post-permalink-feeling-icon'.$is_yeaher_official_user.'"><img src="'.$our_mii_face_output.'" class="user-icon"></a>';	
		
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
require_once 'lib/htmPost.php';
   postsFooter('replies', $row_reply);
    print $GLOBALS['div_body_head_end'];
(empty($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');