<?php

#$row_user_to_view = $row_relationships_users;

if($row_user_to_view['mii_hash']) {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_user_to_view['mii_hash'] . '_normal_face.png'; 
}
else {
if($row_user_to_view['user_face']) {
$mii_face_output = htmlspecialchars($row_user_to_view['user_face']);
} else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

if($row_user_to_view['official_user'] == 1) {
$is_user_official_user = ' official-user';
}
else {
$is_user_official_user = ''; }

if(isset($is_always_button_have)) {
$has_follow_scroll = ' arrow-button';
}
else {
$sql_userwho = 'SELECT * FROM people WHERE people.pid = "' . $row_user_to_view['pid'] . '"';
$result_userwho = mysqli_query($mysql, $sql_userwho);

if(isset($_SESSION['pid'])) {
$sql_search_relationship = 'SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$row_user_to_view['pid'].'"';
$result_search_relationship = mysqli_query($mysql, $sql_search_relationship); }

if(isset($_SESSION['pid']) && strval(mysqli_num_rows($result_search_relationship) == 0)) {
$has_follow_scroll = '';
}
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_user_to_view['pid']) {
$has_follow_scroll = ' arrow-button';
}
else {
$has_follow_scroll = ' arrow-button'; }
}

print '<li class="scroll test-user-'.htmlspecialchars($row_user_to_view['user_id']).'">
    <a href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" class="scroll-focus icon-container'.$is_user_official_user.'" data-pjax="#body"><img src="'.$mii_face_output.'" class="icon"></a>
    

	
	

';
if(isset($is_user_search)) {
print '<div>
    <a class="button" href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" data-pjax="#body">View Profile</a>
  </div>';
}

else {

if(isset($is_friends_added_list)) {
if($is_friends_added_list = true && isset($is_friends_pending_list)) {
$search_frienduserlistli1 = mysqli_query($mysql, 'SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.$row_user_to_view['pid'].'" AND friend_requests.finished = "0"');
$search_frienduserlistli = mysqli_fetch_assoc($search_frienduserlistli1);
print '<button type="button" class="button friend-requested-button relationship-button remove-button" data-modal-open="#sent-request-confirm-page" '.($row_user_to_view['official_user'] == 1 ? 'data-is-identified="1" ': '').'data-user-id="'.htmlspecialchars($row_user_to_view['user_id']).'" data-screen-name="'.htmlspecialchars($row_user_to_view['screen_name']).'" data-mii-face-url="'.$mii_face_output.'" data-pid="'.$search_frienduserlistli['recipient'].'" data-body="'.htmlspecialchars($search_frienduserlistli['message']).'" data-timestamp="'.date("m/d/Y g:i A",strtotime($search_frienduserlistli['created_at'])).'">Request Pending</button>';
} 

else {
print '<button type="button" class="button friend-button relationship-button remove-button" data-modal-open="#breakup-confirm-page" '.($row_user_to_view['official_user'] == 1 ? 'data-is-identified="1" ': '').'data-user-id="'.htmlspecialchars($row_user_to_view['user_id']).'" data-screen-name="'.htmlspecialchars($row_user_to_view['screen_name']).'" data-mii-face-url="'.$mii_face_output.'" data-pid="'.htmlspecialchars($row_user_to_view['pid']).'">Friends</button>'; }
}
	
else {
if(isset($is_always_button_have)) {
print '<a href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" class="scroll-focus arrow-button" data-pjax="#body"></a>'; } else {
print '<div class="toggle-button">';
if(isset($_SESSION['pid']) && $_SESSION['pid'] != $row_user_to_view['pid']) {
if(isset($_SESSION['pid']) && strval(mysqli_num_rows($result_search_relationship) == 0)) {
print '<a class="follow-button button add-button relationship-button" href="#" data-action="/users/'.htmlspecialchars($row_user_to_view['user_id']).'/follow" data-sound="SE_WAVE_FRIEND_ADD" data-track-label="user" data-track-action="follow" data-track-category="follow">Follow</a>
      <button class="button follow-done-button relationship-button done-button none" disabled>Follow</button>';
print '</div>
  <a href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" class="scroll-focus" data-pjax="#body"></a>

';
}
else {
print '<a href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" class="scroll-focus arrow-button" data-pjax="#body"></a>'; }
}
else {
print '<a href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" class="scroll-focus arrow-button" data-pjax="#body"></a>'; }
}

}


}
$sql_user_to_view_profile = 'SELECT * FROM profiles WHERE profiles.pid = "'.$row_user_to_view['pid'].'"';
$result_user_to_view_profile = mysqli_query($mysql, $sql_user_to_view_profile);
$row_user_to_view_profile = mysqli_fetch_assoc($result_user_to_view_profile);

if(empty($row_user_to_view_profile['comment'])) {
$row_user_to_view_profile['comment'] = null; }

print '
  <div class="body">
  ';
  if(isset($is_mutual_list) && isset($row_user_to_view_profile['favorite_screenshot']) && strlen($row_user_to_view_profile['favorite_screenshot']) > 3) {
$result_posts_getfavoritepost = mysqli_query($mysql, 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $row_user_to_view_profile['favorite_screenshot']).'"');
  print '<div class="user-profile-memo-content">
      <img src="'.htmlspecialchars(mysqli_fetch_assoc($result_posts_getfavoritepost)['screenshot']).'" class="user-profile-memo">
    </div>';	  
  }
  print '    <p class="title">
      <span class="nick-name">'.htmlspecialchars($row_user_to_view['screen_name']).'</span>
      <span class="id-name">'.htmlspecialchars($row_user_to_view['user_id']).'</span>
    </p>
    <p class="text">'.htmlspecialchars($row_user_to_view_profile['comment']).'</p>
  </div>
</li>';

?>