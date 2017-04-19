<?php
#$message_template_row = $row_message_show;

$row_message_user = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM people WHERE people.pid = "'.$message_template_row['pid'].'"'));

	if($row_message_user['mii_hash']) {
if(($message_template_row['feeling_id']) == '0') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_message_user['mii_hash'] . '_normal_face.png'; 
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
if(($message_template_row['feeling_id']) == '1') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_message_user['mii_hash'] . '_happy_face.png'; 
$mii_face_feeling = 'happy'; }
$mii_face_miitoo = htmlspecialchars('Yeah!');
if(($message_template_row['feeling_id']) == '2') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_message_user['mii_hash'] . '_like_face.png'; 
$mii_face_feeling = 'like';
$mii_face_miitoo = htmlspecialchars('Yeah♥'); }
if(($message_template_row['feeling_id']) == '3') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_message_user['mii_hash'] . '_surprised_face.png'; 
$mii_face_feeling = 'surprised';
$mii_face_miitoo = htmlspecialchars('Yeah!?'); }
if(($message_template_row['feeling_id']) == '4') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_message_user['mii_hash'] . '_frustrated_face.png'; 
$mii_face_feeling = 'frustrated';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
if(($message_template_row['feeling_id']) == '5') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_message_user['mii_hash'] . '_puzzled_face.png'; 
$mii_face_feeling = 'puzzled';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
}
else {
if($row_message_user['user_face']) {
$mii_face_output = htmlspecialchars($row_message_user['user_face']);
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!');
} else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
}
print '<div id="message-'.$message_template_row['id'].'" class="post scroll '.($row_message_user['pid'] == $_SESSION['pid'] ? 'my' : 'other').'-post">
  <a href="/users/'.htmlspecialchars($row_message_user['user_id']).'" data-pjax="#body" class="scroll-focus user-icon-container'.($row_message_user['official_user'] == 1 ? ' official-user' : '').'"><img src="'.$mii_face_output.'" class="user-icon"></a>
  <header>
    <span class="timestamp">'.humanTiming(strtotime($message_template_row['created_at'])).'</span>
    
  </header>
  <div class="post-body">


      <p class="post-content">'.htmlspecialchars($message_template_row['body']).'</p>

      ';
	  if(!empty($message_template_row['screenshot']) && strlen($message_template_row['screenshot']) > 3) {
	  print '<a href="#" role="button" class="title-capture-container capture-container" data-modal-open="#capture-page" data-large-capture-url="'.htmlspecialchars($message_template_row['screenshot']).'"><img src="'.htmlspecialchars($message_template_row['screenshot']).'" class="title-capture"></a>'; }
	  print '
        
      
  </div>
</div>';
?>