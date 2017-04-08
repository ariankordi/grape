<?php
include 'lib/sql-connect.php';

# If user isn't logged in, then 403 them.
if(empty($_SESSION['pid'])) {
if(isset($_SERVER['HTTP_X_PJAX'])) {
header('Content-Type: application/json; charset=UTF-8');
header("HTTP/1.1 401 Unauthorized");
			print '{"success":0,"errors":[{"message":"You have been logged out.\nPlease log back in.","error_code":1510110}],"code":"401"}';
			exit ("\n");
}
else {
header('Content-Type: text/plain; charset=UTF-8');
header("HTTP/1.1 403 Forbidden");
exit("403 Forbidden\n");
}
}

$pagetitle = 'Notifications';
if(empty($_SERVER['HTTP_X_PJAX_CONTAINER'])) { $_SERVER['HTTP_X_PJAX_CONTAINER'] = ''; }
if($_SERVER['HTTP_X_PJAX_CONTAINER'] != '.tab-body') {
include 'lib/header.php';
include 'lib/user-menu.php';
# Insert PJAX // same as user page
print $div_body_head;
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';
# Put checks for closing this notification
print '<div class="body-content tab2-content" id="news-page">
';

$sql_find_user_newstutorial = 'SELECT * FROM grape.settings_tutorial WHERE settings_tutorial.pid = "'.$_SESSION['pid'].'" AND settings_tutorial.my_news = "1"';
$result_find_user_newstutorial = mysqli_query($link, $sql_find_user_newstutorial);

if(mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.friend_requests WHERE friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.finished = "0" AND friend_requests.has_read = "0" LIMIT 1')) == 1) { $has_friend_request_new = ' notify'; } else { $has_friend_request_new = ''; }

if(strval(mysqli_num_rows($result_find_user_newstutorial)) == 0) {
print '<div class="tutorial-window">
  <p>Check Notifications to see how other people have reacted to your posts or comments and to see if you have any pending friend requests. If you have new notifications, an orange icon will appear to let you know.</p>
<a href="#" class="button tutorial-close-button" data-tutorial-name="my_news">Close</a>
</div>'; }
  
  print '<menu class="tab-header">
  <li id="tab-header-my-news" class="tab-button selected"><a href="/news/my_news" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB" class="tab-icon-my-news"><span>Updates</span></a></li>
  <li id="tab-header-friend-request" class="tab-button'.$has_friend_request_new.'"><a href="/news/friend_requests" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB" class="tab-icon-friend-request"><span>Friend Requests</span></a></li>
</menu>
';
}
$sql_find_user_news = 'SELECT * FROM grape.news WHERE news.to_pid = "'.$_SESSION['pid'].'" AND news.merged IS NULL ORDER BY news.created_at DESC LIMIT 65';
$result_find_user_news = mysqli_query($link, $sql_find_user_news);

function getScreenNamefromPID($pid) {
global $link;
return htmlspecialchars(mysqli_fetch_assoc(mysqli_query($link, 'SELECT screen_name FROM people WHERE people.pid = "'.$pid.'" LIMIT 1'))['screen_name']);	
}

print '
    <div class="tab-body">';

if(mysqli_num_rows($result_find_user_news) == 0) {
$no_content_message = 'No updates.';
include 'lib/no-content-window.php'; }
else {
// Found news, display it here inside a div, a ul, and close them.
// Types - 0, test, 1 admin message, 2, empathy, 3, comment empathy, 4, my comment, 5, poster comment, 6, follow

print '<ul class="list-content-with-icon-and-text arrow-list" id="news-list-content">';
while($row_act_find_user_news = mysqli_fetch_assoc($result_find_user_news)) {
print "<li>\n";
// If the news type is greater than 2, aka an empathy or higher, display the from_pid's icon.
$find_news_merged_with = mysqli_query($link, 'SELECT * FROM news WHERE news.merged = "'.$row_act_find_user_news['news_id'].'" ORDER BY news.created_at LIMIT 20');

$rows_find_news_merged_with = mysqli_fetch_all($find_news_merged_with, MYSQLI_ASSOC);

if(strval($row_act_find_user_news['news_context']) >=2) {

$sql_news_user_select = 'SELECT * FROM grape.people WHERE people.pid = "'.$row_act_find_user_news['from_pid'].'"';
$result_news_user_select = mysqli_query($link, $sql_news_user_select);
$row_news_user_select = mysqli_fetch_assoc($result_news_user_select);

if($row_news_user_select['mii_hash']) {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_news_user_select['mii_hash'] . '_normal_face.png'; 
}
else {
if($row_news_user_select['user_face']) {
$mii_face_output = htmlspecialchars($row_news_user_select['user_face']);
} else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

if($row_news_user_select['official_user'] == 1) {
$is_news_user_official = ' official-user';
}
else {
$is_news_user_official = ''; }

if(strval($row_act_find_user_news['news_context']) == 6) {
$sql_feed_my_following = 'SELECT * FROM grape.relationships WHERE relationships.source = "' . $_SESSION['pid'] . '" AND relationships.target = "'. $row_news_user_select['pid'].'" AND relationships.is_me2me = "0"';
$result_feed_my_following = mysqli_query($link, $sql_feed_my_following);
 
if(mysqli_num_rows($result_feed_my_following) == 0 && $row_news_user_select['pid'] != $_SESSION['pid']) {
$has_scroll_notif = ''; }

$news_subject_uri = '/users/'.htmlspecialchars($row_news_user_select['user_id']).'';  }
else {
$has_scroll_notif = 'arrow-button '; }

if(strval($row_act_find_user_news['news_context']) == 2) {
$news_subject_uri = '/posts/'.htmlspecialchars($row_act_find_user_news['id']).''; }
if(strval($row_act_find_user_news['news_context']) == 3) {
$news_subject_uri = '/replies/'.htmlspecialchars($row_act_find_user_news['id']).''; }
if(strval($row_act_find_user_news['news_context']) == 4) {
$news_subject_uri = '/posts/'.htmlspecialchars($row_act_find_user_news['id']).''; }
if(strval($row_act_find_user_news['news_context']) == 5) {
$news_subject_uri = '/posts/'.htmlspecialchars($row_act_find_user_news['id']).''; }

if(strval($row_act_find_user_news['news_context']) == 6) {
if(mysqli_num_rows($result_feed_my_following) >= 1 ) {
$has_scroll_notif = 'arrow-button '; } }

print '
<a href="/users/'.htmlspecialchars($row_news_user_select['user_id']).'" data-pjax="#body" class="icon-container'.$is_news_user_official.''.(strval($row_act_find_user_news['has_read']) == 0 ? ' notify' : '').'"><img src="'.$mii_face_output.'" class="icon"></a>
<a href="'.$news_subject_uri.'" data-pjax="#body" class="'.$has_scroll_notif.'scroll"></a>
';

if(strval($row_act_find_user_news['news_context']) == 6 && $row_news_user_select['pid'] != $_SESSION['pid']) {
if(mysqli_num_rows($result_feed_my_following) == 0) {
$has_scroll_notif = '';

print '<div class="toggle-button">
    <a class="follow-button button add-button" href="#" data-action="/users/'.htmlspecialchars($row_news_user_select['user_id']).'/follow" data-sound="SE_WAVE_FRIEND_ADD" data-track-label="user" data-track-action="follow" data-track-category="follow">Follow</a>
      <button class="button follow-done-button relationship-button done-button none">Follow</button>
</div>';
}
}

print '<div class="body">
';

if(strval($row_act_find_user_news['news_context']) == 4) {
# Get post body here
$sql_news_getpost = 'SELECT * FROM grape.posts WHERE posts.id = "'.$row_act_find_user_news['id'].'"';
$result_news_getpost = mysqli_query($link, $sql_news_getpost);
$row_news_getpost = mysqli_fetch_assoc($result_news_getpost);
# Truncate it
$truncate_post_bodyp1 = mb_substr(($row_news_getpost['body']), 0, 17, 'utf-8');
if(mb_strlen($row_news_getpost['body'], 'utf-8') >= 18) {
$truncate_post_body = "$truncate_post_bodyp1..."; }
else {
$truncate_post_body = $truncate_post_bodyp1; }

if(isset($rows_find_news_merged_with[0]['from_pid'])) {
if(count($rows_find_news_merged_with) == 1) {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span> and <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span> commented on <span class="link">your Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body))).')</span>.   ';
}
if(count($rows_find_news_merged_with) == 2) {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, and <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> commented on <span class="link">your Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body))).')</span>.   ';
}
if(count($rows_find_news_merged_with) == 3) {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> and one other person commented on <span class="link">your Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body))).')</span>.      ';
}
if(count($rows_find_news_merged_with) >= 4) {
$subtr_news_curr = count($rows_find_news_merged_with) - 2;
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> and '.$subtr_news_curr.' others commented on <span class="link">your Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body))).').';
}
}
 else {
 $news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span> commented on <span class="link">your Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars($truncate_post_body)).')</span>.   '; }
}

if(strval($row_act_find_user_news['news_context']) == 5) {
# Get post body here
$sql_news_getpost = 'SELECT * FROM grape.posts WHERE posts.id = "'.$row_act_find_user_news['id'].'"';
$result_news_getpost = mysqli_query($link, $sql_news_getpost);
if(mysqli_num_rows($result_news_getpost) == 0) {
$row_news_getpost = array(
'_post_type' => 'body',
'body' => 'not found'
);
} else {
$row_news_getpost = mysqli_fetch_assoc($result_news_getpost); }
# Truncate it
$truncate_post_bodyp1 = mb_substr(($row_news_getpost['body']), 0, 17, 'utf-8');
if(mb_strlen($row_news_getpost['body'], 'utf-8') >= 18) {
$truncate_post_body = "$truncate_post_bodyp1..."; }
else {
$truncate_post_body = $truncate_post_bodyp1; }

$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span> commented on <span class="link">'.htmlspecialchars($row_news_user_select['screen_name']).''."'s".' Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars($truncate_post_body)).')</span>.   '; 
}

if(strval($row_act_find_user_news['news_context']) == 3) {
# Get comment body here
$sql_news_getcomment = 'SELECT * FROM grape.replies WHERE replies.id = "'.$row_act_find_user_news['id'].'"';
$result_news_getcomment = mysqli_query($link, $sql_news_getcomment);
if(mysqli_num_rows($result_news_getcomment) == 0) {
$row_news_getcomment = array(
'_post_type' => 'body',
'body' => 'not found'
);
} else {
$row_news_getcomment = mysqli_fetch_assoc($result_news_getcomment); }
# Truncate it
$truncate_post_bodyp1 = mb_substr(($row_news_getcomment['body']), 0, 17, 'utf-8');
if(mb_strlen($row_news_getcomment['body'], 'utf-8') >= 17) {
$truncate_post_body = "$truncate_post_bodyp1..."; }
else {
$truncate_post_body = $truncate_post_bodyp1; }

if(isset($rows_find_news_merged_with[0]['from_pid'])) {
if(count($rows_find_news_merged_with) == 1) {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span> and <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span> gave <span class="link">your Comment ('.htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body)).')</span> a Yeah.   ';
}
if(count($rows_find_news_merged_with) == 2) {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, and <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> gave <span class="link">your Comment ('.htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body)).')</span> a Yeah.   ';
}
if(count($rows_find_news_merged_with) == 3) {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> and one other person gave <span class="link">your Comment ('.htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body)).')</span> a Yeah.   ';
}
if(count($rows_find_news_merged_with) >= 4) {
$subtr_news_curr = count($rows_find_news_merged_with) - 2;
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> and '.$subtr_news_curr.' others gave <span class="link">your Comment ('.htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body)).') a Yeah.';
}

}
else {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span> gave <span class="link">your Comment ('.htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body)).')</span> a Yeah.   '; 
} }

if(strval($row_act_find_user_news['news_context']) == 2) {
# Get post body here
$sql_news_getpost = 'SELECT * FROM grape.posts WHERE posts.id = "'.$row_act_find_user_news['id'].'"';
$result_news_getpost = mysqli_query($link, $sql_news_getpost);
if(mysqli_num_rows($result_news_getpost) == 0) {
$row_news_getpost = array(
'_post_type' => 'body',
'body' => 'not found'
);
} else {
$row_news_getpost = mysqli_fetch_assoc($result_news_getpost); }
# Truncate it
$truncate_post_bodyp1 = mb_substr(($row_news_getpost['body']), 0, 17, 'utf-8');
if(mb_strlen($row_news_getpost['body'], 'utf-8') >= 18) {
$truncate_post_body = "$truncate_post_bodyp1..."; }
else {
$truncate_post_body = $truncate_post_bodyp1; }

if(isset($rows_find_news_merged_with[0]['from_pid'])) {
if(count($rows_find_news_merged_with) == 1) {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span> and <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span> gave <span class="link">your Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body))).')</span> a Yeah.   ';
}
if(count($rows_find_news_merged_with) == 2) {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, and <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> gave <span class="link">your Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body))).')</span> a Yeah.   ';
}
if(count($rows_find_news_merged_with) == 3) {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> and one other person gave <span class="link">your Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body))).')</span> a Yeah.   ';
}
if(count($rows_find_news_merged_with) >= 4) {
$subtr_news_curr = count($rows_find_news_merged_with) - 2;
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> and '.$subtr_news_curr.' others gave <span class="link">your Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body))).') a Yeah.';
}

}
else {
$news_content_text = '<span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span> gave <span class="link">your Post ('.($row_news_getpost['_post_type'] == 'artwork' ? 'handwritten' : htmlspecialchars(preg_replace("/[\r\n]{2,}/", " ", $truncate_post_body))).')</span> a Yeah.   '; }
}
if(strval($row_act_find_user_news['news_context']) == 6) {

if(isset($rows_find_news_merged_with[0]['from_pid'])) {
if(count($rows_find_news_merged_with) == 1) {
$news_content_text = 'Followed by <span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span> and <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>.';
}
if(count($rows_find_news_merged_with) == 2) {
$news_content_text = 'Followed by <span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, and <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span>.';
}
if(count($rows_find_news_merged_with) == 3) {
$news_content_text = 'Followed by <span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> and one other person.';
}
if(count($rows_find_news_merged_with) >= 4) {
$subtr_news_curr = count($rows_find_news_merged_with) - 2;
$news_content_text = 'Followed by <span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[0]['from_pid']).'</span>, <span class="nick-name">'.getScreenNamefromPID($rows_find_news_merged_with[1]['from_pid']).'</span> and '.$subtr_news_curr.' others.';
}

}
else {
$news_content_text = 'Followed by <span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>.'; } }

print '<p class="text">'.$news_content_text.'    <span class="timestamp"> '.humanTiming(strtotime($row_act_find_user_news['created_at'])).'</span>
  </p>
</div>
  </li>';
// Mark all as read
$sql_update = 'UPDATE grape.news SET news.has_read = "1" WHERE news.news_id = "'.$row_act_find_user_news['news_id'].'"';
$result_update = mysqli_query($link, $sql_update);
}
		
else {
// Print test or admin notification here!
}

}
print '</ul>';
}
// End tab-content, body-content tab2-content.
print '</div>';

if($_SERVER['HTTP_X_PJAX_CONTAINER'] != '.tab-body') {
print '  </div>';




print $div_body_head_end;
include 'lib/footer.php';
}


?>