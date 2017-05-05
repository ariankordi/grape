<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if(empty($_SESSION['pid'])) {
notLoggedIn(); grpfinish($mysql); exit();
}

$me_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"')->fetch_assoc();

# If the method is POST, then post.
if($_SERVER['REQUEST_METHOD'] == 'POST') {
require_once '../grplib-php/user-helper.php';
if(!empty($_GET['conversation_id'])) {
if($me_user['privilege'] <= 6) { header(plainErr(403, '403 Forbidden'); grpfinish($mysql); exit(); } else {
$search_conversation = $mysql->query('SELECT * FROM conversations WHERE conversations.conversation_id = "'.$mysql->real_escape_string().'" LIMIT 1');
if(!$search_conversation || $search_conversation->num_rows == 0) { plainErr(404, '404 Not Found'); grpfinish($mysql); exit(); }
	}   
} else {
$user_id = userIDtoPID($mysql->real_escape_string($_GET['message_to_user_id']));
if(!$user_id) { plainErr(404, '404 Not Found'); grpfinish($mysql); exit(); }
$relationship = getFriendRelationship($_SESSION['pid'], $user_id);
if(!$relationship) { plainErr(403, '403 Forbidden'); grpfinish($mysql); exit(); }
}
require_once '../grplib-php/post-helper.php';
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
$is_post_valid = postValid($me_user, 'upload');
if($is_post_valid != 'ok') {
if($is_post_valid == 'blank') {
$error_message[] = 'The content you have entered is blank.
Please enter content into your post.';
$error_code[] = 1515001; }
elseif($is_post_valid == 'max') {
$error_message[] = 'You have exceeded the amount of characters that you can send.';
$error_code[] = 1515002; }
} if(!empty($error_code)) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [array(
'message' => $error_message[0],
'error_code' => $error_code[0]
)], 'code' => 400)); grpfinish($mysql); exit();
}

require_once '../grplib-php/olv-url-enc.php';
$gen_olive_url = genURL();

if(empty($_POST['feeling_id']) || strval($_POST['feeling_id']) >= 6 || !is_numeric($_POST['feeling_id'])) { $_POST['feeling_id'] = 0; }

if(empty($_GET['conversation_id'])) {
$conversation_id = $search_conversation->fetch_assoc()['conversation_id'];
}
else {
$conversation_id = $relationship['conversation_id'];
}

        $create_message = $mysql->query('INSERT INTO messages(conversation_id, id, pid, feeling_id, platform_id, body, screenshot, is_spoiler, has_read)
                VALUES("'.$conversation_id.'",
				       "'.$gen_olive_url .'",
				       "'.$_SESSION['pid'].'",
                       "'.$mysql->real_escape_string($_POST['feeling_id']).'",
                       "1",
                       "'.$mysql->real_escape_string($_POST['body']).'",
					   "'.(empty($_POST['screenshot']) ? NULL : $mysql->real_escape_string($_POST['screenshot'])).'",
                       "'.(empty($_POST['is_spoiler']) ? '0' : $mysql->real_escape_string($_POST['is_spoiler'])).'",
                       "0")');
		$updateFM = $mysql->query('UPDATE friend_relationships SET updated = NOW() WHERE relationship_id = "'.$relationship['relationship_id'].'"');
if(!$createpost) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
} else {
# Success, print post.
require_once 'lib/htmUser.php';
printMessage($createpost->fetch_assoc());
		}
grpfinish($mysql); exit();
}
# If not, then do everything else.

# If user isn't logged in, then 403 them.
if(empty($_SESSION['pid'])) {
notLoggedIn(); grpfinish($mysql); exit();
}
# If a user ID is specified, then display a special page for that, and if not, then display the message search.
if(!empty($_GET['user_id']) || !empty($_GET['conversation_id'])) {
if(!isset($_GET['conversation_id'])) {
$result_get_person_formessage = $mysql->query('SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"');
if(mysqli_num_rows($result_get_person_formessage) == 0) {
include_once '404.php'; exit();	} }

else {
$result_get_conversation = $mysql->query('SELECT * FROM conversations WHERE conversations.conversation_id = "'.$mysql->real_escape_string($_GET['conversation_id']).'"');
if(mysqli_num_rows($result_get_conversation) == 0) {
include_once '404.php'; exit();	} }
if(isset($_GET['user_id'])) {
$row_get_person_formessage = mysqli_fetch_assoc($result_get_person_formessage);
$result_search_person_relationship = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_get_person_formessage['pid'].'" AND friend_relationships.source = "'.$_SESSION['pid'].'" OR friend_relationships.target = "'.$_SESSION['pid'].'" AND friend_relationships.source = "'.$row_get_person_formessage['pid'].'" LIMIT 1');
if(mysqli_num_rows($result_search_person_relationship) == 0) {
header('Content-Type: text/plain; charset=UTF-8'); header("HTTP/1.1 403 Forbidden"); exit("403 Forbidden\n");   } }
else {
if(mysqli_fetch_assoc($mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"'))['privilege'] < 5) {
plainErr(403, '403 Forbidden'); grpfinish($mysql); exit();   } }
if(!isset($_GET['offset'])) {
if(isset($_GET['user_id'])) {
$pagetitle = 'Conversation with '.htmlspecialchars($row_get_person_formessage['screen_name']).' ('.htmlspecialchars($row_get_person_formessage['user_id']).')';
} else {
$pagetitle = 'ConversationID '.htmlspecialchars($_GET['conversation_id']); }
require_once 'lib/htm.php';
printHeader(false);
printMenu();
print $GLOBALS['div_body_head'];
print '<header id="header">
  <a id="header-message-button" class="header-button" href="#" data-modal-open="#add-message-page">Write Message</a>
  
  <h1 id="page-title" class="">'.$pagetitle.'</h1>

</header>';
}
if(isset($_GET['user_id'])) {
$find_conversation_for_friend = $mysql->query('SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" AND conversations.recipient = "'.$row_get_person_formessage['pid'].'" OR conversations.sender = "'.$row_get_person_formessage['pid'].'" AND conversations.recipient = "'.$_SESSION['pid'].'" LIMIT 1');
}
if(isset($_GET['offset']) && is_numeric($_GET['offset']) && strval($_GET['offset']) >= 1) {
$get_messages_for_friend = $mysql->query('SELECT * FROM messages WHERE messages.conversation_id = '.(!empty($_GET['conversation_id']) ? $mysql->real_escape_string($_GET['conversation_id']) : mysqli_fetch_assoc($find_conversation_for_friend)['conversation_id']).' ORDER BY messages.created_at DESC LIMIT 20 OFFSET '.'.'');	
}
else {
$get_messages_for_friend = $mysql->query('SELECT * FROM messages WHERE messages.conversation_id = '.(!empty($_GET['conversation_id']) ? $mysql->real_escape_string($_GET['conversation_id']) : mysqli_fetch_assoc($find_conversation_for_friend)['conversation_id']).' ORDER BY messages.created_at DESC LIMIT 20');
}

if(!empty($_GET['offset'])) { $my_new_offset = strval($_GET['offset']) + 20; } 
if($get_messages_for_friend) {
if(isset($_GET['offset']) && is_numeric($_GET['offset']) && strval($_GET['offset']) >= 1) {
# Modify as necessary
print '
<div class="body-content message-post-list" id="message-page" data-next-page-url="'.(mysqli_num_rows($get_messages_for_friend) > 19 ? '?offset='.$my_new_offset.'' : '').'">
'; } 
	else {
print '
<div class="body-content message-post-list" id="message-page" data-next-page-url="'.(mysqli_num_rows($get_messages_for_friend) > 19 ? '?offset=20' : '').'">
'; }

while($row_get_message_for_friend = mysqli_fetch_assoc($get_messages_for_friend)) {
$message_template_row = $row_get_message_for_friend;
include 'lib/messagelist-message-template.php';
}

print '
</div>
';
} else {
print '
<div class="body-content message-post-list" id="message-page" data-next-page-url="">

</div>
';

}

if(!isset($_GET['offset'])) {
# Add message form
$lookup_user = mysqli_fetch_assoc($mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"'));
print '<div id="add-message-page" class="add-post-page'.($lookup_user['privilege'] >= 2 ? 
' official-user-post' : '').' none" data-modal-types="add-entry add-message require-body preview-body" data-is-template="1">
  <header class="add-post-page-header">
  ';
if(empty($_GET['conversation_id'])) {
  print '
    <h1 class="page-title">Message to '.htmlspecialchars($row_get_person_formessage['screen_name']).' ('.htmlspecialchars($row_get_person_formessage['user_id']).')</h1>
  </header>
'; }
else {
  print '
    <h1 class="page-title">Message to ConversationID '.htmlspecialchars($_GET['conversation_id']).'</h1>
  </header>
';	}
if(empty($_GET['conversation_id'])) {
print '  <form method="post" action="/friend_messages">
<input type="hidden" name="message_to_user_id" value="'.htmlspecialchars($row_get_person_formessage['user_id']).'">'; } else {
print '  <form method="post" action="/friend_messages?conversation_id='.htmlspecialchars($_GET['conversation_id']).'">
<input type="hidden" name="conversation_id" value="'.htmlspecialchars($_GET['conversation_id']).'">';	
}
print '
	<input type="hidden" name="view_id" value="00000000000000000000000000000000">
	<input type="hidden" name="page_param" value="{&quot;upinfo&quot;:&quot;1400000000.00000,1400000000,1400000000.00000&quot;,&quot;reftime&quot;:&quot;+1400000000&quot;,&quot;order&quot;:&quot;desc&quot;,&quot;per_page&quot;:&quot;20&quot;}">
    <div class="add-post-page-content">
 ';
	if($lookup_user['mii_hash']) {
	print '<div class="feeling-selector expression">
  <img src="https://mii-secure.cdn.nintendo.net/'.htmlspecialchars($lookup_user['mii_hash']).'_normal_face.png" class="icon">
  <ul class="buttons"><li class="checked"><input type="radio" name="feeling_id" value="0" class="feeling-button-normal" data-mii-face-url="https://mii-secure.cdn.nintendo.net/'.htmlspecialchars($lookup_user['mii_hash']).'_normal_face.png" checked="" data-sound="SE_WAVE_MII_FACE_00"></li><li><input type="radio" name="feeling_id" value="1" class="feeling-button-happy" data-mii-face-url="https://mii-secure.cdn.nintendo.net/'.htmlspecialchars($lookup_user['mii_hash']).'_happy_face.png" data-sound="SE_WAVE_MII_FACE_01"></li><li><input type="radio" name="feeling_id" value="2" class="feeling-button-like" data-mii-face-url="https://mii-secure.cdn.nintendo.net/'.htmlspecialchars($lookup_user['mii_hash']).'_like_face.png" data-sound="SE_WAVE_MII_FACE_02"></li><li><input type="radio" name="feeling_id" value="3" class="feeling-button-surprised" data-mii-face-url="https://mii-secure.cdn.nintendo.net/'.htmlspecialchars($lookup_user['mii_hash']).'_surprised_face.png" data-sound="SE_WAVE_MII_FACE_03"></li><li><input type="radio" name="feeling_id" value="4" class="feeling-button-frustrated" data-mii-face-url="https://mii-secure.cdn.nintendo.net/'.htmlspecialchars($lookup_user['mii_hash']).'_frustrated_face.png" data-sound="SE_WAVE_MII_FACE_04"></li><li><input type="radio" name="feeling_id" value="5" class="feeling-button-puzzled" data-mii-face-url="https://mii-secure.cdn.nintendo.net/'.htmlspecialchars($lookup_user['mii_hash']).'_puzzled_face.png" data-sound="SE_WAVE_MII_FACE_05"></li>  </ul>
</div>';
	}
	if(isset($lookup_user['user_face'])) {
	if($lookup_user['user_face']) {	
	print '<div class="feeling-selector expression">
  <img src="'.htmlspecialchars($lookup_user['user_face']).'" class="icon">
  
</div>';
	}
   }
print '


      <div class="textarea-container textarea-with-menu active-text">
        <menu class="textarea-menu">
          <li><label class="textarea-menu-text checked">
              <input type="radio" name="_post_type" value="body" checked="" data-sound="">
          </label></li>
          <li><label class="textarea-menu-memo">
              <input type="radio" name="_post_type" value="painting" data-sound="">
          </label></li>
        </menu>
           <textarea name="body" class="textarea-text" value="" maxlength="1000" placeholder="Write a message to a friend here."></textarea>
        <div class="textarea-memo trigger" data-sound=""><div class="textarea-memo-preview"></div><input type="hidden" name="painting"></div>
      </div>
	';
	 print '<input type="text" class="textarea-line url-form" name="screenshot" placeholder="Screenshot URL" maxlength="255">';
print '
	</div>

      <input type="button" class="olv-modal-close-button fixed-bottom-button left" value="Cancel" data-sound="SE_WAVE_CANCEL">
      <input type="submit" class="post-button fixed-bottom-button" value="Send" data-track-category="message" data-track-action="sendMessage" data-post-content-type="text" data-post-with-screenshot="nodata">
  </form>
</div>';
print $GLOBALS['div_body_head_end'];

(isset($_SERVER['HTTP_X_PJAX']) ? '' : printFooter());
}
}

else {
$pagetitle = 'Messages';
printHeader(false); printMenu();
print $GLOBALS['div_body_head'];
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

$result_find_user_newstutorial = $mysql->query('SELECT * FROM settings_tutorial WHERE settings_tutorial.pid = "'.$_SESSION['pid'].'" AND settings_tutorial.friend_messages = "1"');

$result_get_friendexistence = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" OR friend_relationships.target = "'.$_SESSION['pid'].'" ORDER BY friend_relationships.updated DESC');

print '<div class="body-content" id="messages-list">
';
if(mysqli_num_rows($result_get_friendexistence) == 0) {
$no_content_message = 'In Messages, you can send messages to friends and view past messages. If you want to play a game with a friend or get some tips if you'."'".'re stuck, try sending a message!';
include 'lib/no-content-window.php'; }
else {
if(strval(mysqli_num_rows($result_find_user_newstutorial)) == 0) {
print '<div class="tutorial-window">
    <p>In Messages, you can send messages to friends and view past messages. If you want to play a game with a friend or get some tips if you'."'".'re stuck, try sending a message!</p>
    <a href="#" class="button tutorial-close-button" data-tutorial-name="messages">Close</a>
  </div>'; }
# Remember to put settings tutorial here

# Show users available to message	
print '<ul class="list-content-with-icon-and-text arrow-list">       

';      
while ($row_get_friendexistence = mysqli_fetch_assoc($result_get_friendexistence)) {
$row_get_friend = mysqli_fetch_assoc($mysql->query('SELECT * FROM people WHERE people.pid = "'.($row_get_friendexistence['target'] == $_SESSION['pid'] ? $row_get_friendexistence['source'] : $row_get_friendexistence['target']).'"'));
if($row_get_friend['pid'] != $_SESSION['pid']) {
if($row_get_friend['mii_hash']) {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$row_get_friend['mii_hash'].'_normal_face.png'; 
}
else {
if($row_get_friend['user_face']) {
$mii_face_output = htmlspecialchars($row_get_friend['user_face']);
} else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}
$find_conversation_for_friend1 = $mysql->query('SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" AND conversations.recipient = "'.$row_get_friend['pid'].'" OR conversations.sender = "'.$row_get_friend['pid'].'" AND conversations.recipient = "'.$_SESSION['pid'].'" LIMIT 1');
if(mysqli_num_rows($find_conversation_for_friend1) != 0) {
$get_message_search_recent_messg1 = mysqli_fetch_assoc($mysql->query('SELECT * FROM messages WHERE messages.conversation_id = "'.mysqli_fetch_assoc($find_conversation_for_friend1)['conversation_id'].'" ORDER BY messages.created_at DESC LIMIT 1')); }
else { $get_message_search_recent_messg1 = array(
'has_read'  => 1, 
'pid'  => 1); }
print '
      <li>
        <a href="/users/'.htmlspecialchars($row_get_friend['user_id']).'" data-pjax="#body" class="icon-container'.($row_get_friend['official_user'] == 1 ? ' official-user' : '').' trigger'.(isset($get_message_search_recent_messg1['has_read']) && $get_message_search_recent_messg1['pid'] != $_SESSION['pid'] && $get_message_search_recent_messg1['has_read'] == 
		0 ? ' notify' : '').'"><img src="'.$mii_face_output.'" class="icon"></a>
        <a href="/friend_messages/'.htmlspecialchars($row_get_friend['user_id']).'" data-pjax="#body" class="arrow-button"></a>
        <div class="body">
          <p class="title">
            <span class="nick-name">'.htmlspecialchars($row_get_friend['screen_name']).'</span>
            <span class="id-name">'.htmlspecialchars($row_get_friend['user_id']).'</span>
          </p>
          
          
		  ';
$find_conversation_for_friend = $mysql->query('SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" AND conversations.recipient = "'.$row_get_friend['pid'].'" OR conversations.sender = "'.$row_get_friend['pid'].'" AND conversations.recipient = "'.$_SESSION['pid'].'" LIMIT 1');
if(mysqli_num_rows($find_conversation_for_friend) == 0) {
print '          <p class="text placeholder">You haven'."'".'t exchanged messages with this user yet.</p>
';
} else {
$get_message_search_recent_messg = mysqli_fetch_assoc($mysql->query('SELECT * FROM messages WHERE messages.conversation_id = "'.mysqli_fetch_assoc($find_conversation_for_friend)['conversation_id'].'" ORDER BY messages.created_at DESC LIMIT 1'));
print '   <span class="timestamp">'.humanTiming(strtotime($get_message_search_recent_messg['created_at'])).'</span>
          <p class="text'.($get_message_search_recent_messg['pid'] == $_SESSION['pid'] ? ' my' : ' other').'">'.htmlspecialchars($get_message_search_recent_messg['body']).'</p>
		  ';
}
print '
          
        </div>
      </li>
	  ';
} }
# End ul
print '     
      
  </ul>';
}

# End body-content messages-list
print '
</div>';
print $GLOBALS['div_body_head_end'];
$result_get_all_conversation_ids = $mysql->query('SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" OR conversations.recipient = "'.$_SESSION['pid'].'"');
if(mysqli_num_rows($result_get_all_conversation_ids) != 0) {
while($row_get_all_conversation_ids = mysqli_fetch_assoc($result_get_all_conversation_ids)) {
$result_set_all_messages_unread = $mysql->query('UPDATE messages SET has_read = "1" WHERE messages.conversation_id = "'.$row_get_all_conversation_ids['conversation_id'].'" AND messages.pid != "'.$_SESSION['pid'].'"');
} }

(isset($_SERVER['HTTP_X_PJAX']) ? '' : printFooter());
}