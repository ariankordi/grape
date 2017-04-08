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

if(mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.news WHERE news.to_pid = "'.$_SESSION['pid'].'" AND news.has_read = "0" AND news.merged IS NULL LIMIT 1')) == 1) { $has_news_new = ' notify'; } else { $has_news_new = ''; }

if(strval(mysqli_num_rows($result_find_user_newstutorial)) == 0) {
print '<div class="tutorial-window">
  <p>Check Notifications to see how other people have reacted to your posts or comments and to see if you have any pending friend requests. If you have new notifications, an orange icon will appear to let you know.</p>
<a href="#" class="button tutorial-close-button" data-tutorial-name="my_news">Close</a>
</div>'; }
  
  print '<menu class="tab-header">
  <li id="tab-header-my-news" class="tab-button'.$has_news_new.'"><a href="/news/my_news" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB" class="tab-icon-my-news"><span>Updates</span></a></li>
  <li id="tab-header-friend-request" class="tab-button selected"><a href="/news/friend_requests" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB" class="tab-icon-friend-request"><span>Friend Requests</span></a></li>
</menu>
';
}
print '
    <div class="tab-body">';

$sql_find_user_friend_requests = 'SELECT * FROM grape.friend_requests WHERE friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.finished = "0" ORDER BY news_id DESC LIMIT 100';
$result_find_user_friend_requests = mysqli_query($link, $sql_find_user_friend_requests);

if(mysqli_num_rows($result_find_user_friend_requests) == 0) {
$no_content_message = "You don't have any friend requests.";
include 'lib/no-content-window.php'; }
else {
// Found friend requests, display them here inside a div, an ul, close them then add the template.
print '<ul class="list-content-with-icon-and-text" id="friend-request-list-content">
';
while($row_find_user_friend_requests = mysqli_fetch_assoc($result_find_user_friend_requests)) {
print '<li>
';

$result_news_user_select = mysqli_query($link, 'SELECT * FROM grape.people WHERE people.pid = "'.$row_find_user_friend_requests['sender'].'"');
$row_news_user_select = mysqli_fetch_assoc($result_news_user_select);

if($row_news_user_select['mii_hash']) {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_news_user_select['mii_hash'] . '_normal_face.png'; }
else {
if($row_news_user_select['user_face']) {
$mii_face_output = htmlspecialchars($row_news_user_select['user_face']); } else { $mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

if($row_news_user_select['official_user'] == 1) { $is_news_user_official = ' official-user'; $is_identified_user_value = ' data-is-identified="1"'; }
else { $is_news_user_official = ''; $is_identified_user_value = ''; }
if(strval($row_find_user_friend_requests['has_read']) == 0) { $notification_is_new1 = ' notify'; }else { $notification_is_new1 = ''; }


print '<a href="/users/'.htmlspecialchars($row_news_user_select['user_id']).'" data-pjax="#body" class="icon-container'.$is_news_user_official.''.$notification_is_new1.'"><img src="'.$mii_face_output.'" class="icon"></a>'."\n".'';
print '<div class="friend-request-buttons">
        <a href="#" class="button received-request-button" data-modal-open="#received-request-confirm-page" data-user-id="'.htmlspecialchars($row_news_user_select['user_id']).'" data-screen-name="'.htmlspecialchars($row_news_user_select['screen_name']).'"'.$is_identified_user_value.' data-mii-face-url="'.$mii_face_output.'" data-pid="'.$row_news_user_select['pid'].'" data-body="'.$row_find_user_friend_requests['message'].'">View Friend Request</a>
        <span class="ok-message none"></span>
      </div>
<div class="body">
        <p class="title">';
		$get_user_profile_for_fr = mysqli_query($link, 'SELECT * FROM profiles WHERE profiles.pid = "'.$row_news_user_select['pid'].'" LIMIT 1');
		print '
          <span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>
        </p>
        <p class="text">'.(mysqli_num_rows($get_user_profile_for_fr) != 0 ? htmlspecialchars(mysqli_fetch_assoc($get_user_profile_for_fr)['comment']) : '
		').'</p>
      </div>';


print '
</li>';
$result_update = mysqli_query($link, 'UPDATE grape.friend_requests SET friend_requests.has_read = "1" WHERE friend_requests.news_id = "'.$row_find_user_friend_requests['news_id'].'"');	
}
print '
</ul>';
print '<div id="received-request-confirm-page" class="friend-request-confirm-page window-page none" data-modal-types="confirm-relationship confirm-received-request" data-is-template="1">
  <div class="window user-window">
    <h1 class="window-title">Friend Request</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="user-container">
        <span class="icon-container"><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="icon"></span>
        <p class="nickname">
          <span class="screen-name"></span>
          <span class="id-name"></span>
        </p>
      </div>
      <div class="message"><p class="message-inner"></p></div>
    </div></div>
    <div class="window-bottom-buttons">
      <input type="button" class="cancel-button button" value="Reject" data-action="/users/friend_request.delete.json" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button button" value="Accept" data-action="/users/friend_request.accept.json" data-done-msgid="olv.portal.friend_request.successed_with" data-track-category="friendRequest" data-track-action="acceptFriendRequest">
    </div>
  </div>
</div>';
}
	
# End tab-content, body-content tab2-content.
print '</div>';

if($_SERVER['HTTP_X_PJAX_CONTAINER'] != '.tab-body') {
print '  </div>';




print $div_body_head_end;
include 'lib/footer.php';
}


?>