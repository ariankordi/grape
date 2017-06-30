<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if(empty($_SESSION['pid'])) {
notLoggedIn();  exit();
}

$me = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"')->fetch_assoc();

# If the method is POST, then post.
if($_SERVER['REQUEST_METHOD'] == 'POST') {
require_once '../grplib-php/user-helper.php';
if(!empty($_GET['conversation_id'])) {
if($me['privilege'] <= 6) { plainErr(403, '403 Forbidden');  exit(); } else    {
$search_conversation = $mysql->query('SELECT * FROM conversations WHERE conversations.conversation_id = "'.$mysql->real_escape_string($_GET['conversation_id']).'" LIMIT 1');
if(!$search_conversation || $search_conversation->num_rows == 0) { plainErr(404, '404 Not Found');  exit(); }
	}   
} else {
$user_id = userIDtoPID($mysql->real_escape_string($_POST['message_to_user_id']));
if(!$user_id) { plainErr(404, '404 Not Found');  exit(); }
$relationship = getFriendRelationship($_SESSION['pid'], $user_id);
if(!$relationship) { plainErr(403, '403 Forbidden');  exit(); }
}
require_once '../grplib-php/post-helper.php';
$is_post_valid = postValid($me, 'upload');
if($is_post_valid != 'ok') {
if($is_post_valid == 'blank') {
$error_message[] = 'The content you have entered is blank.
Please enter content into your post.';
$error_code[] = 1515001; }
elseif($is_post_valid == 'max') {
$error_message[] = 'You have exceeded the amount of characters that you can send.';
$error_code[] = 1515002; }
} if(!empty($error_code)) {
http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array('message'=>$error_message[0],'error_code' => $error_code[0])], 'code' => 400));  exit();
}

require_once '../grplib-php/olv-url-enc.php';
$gen_olive_url = genURL();

if(empty($_POST['feeling_id']) || strval($_POST['feeling_id']) >= 6 || !is_numeric($_POST['feeling_id'])) { $_POST['feeling_id'] = 0; }
if(!empty($_GET['conversation_id'])) {
$conversation_id = $search_conversation->fetch_assoc()['conversation_id'];
}
else {
if(!empty($relationship['conversation_id'])) {
$conversation_id = $relationship['conversation_id']; }
else {
$create_conversation = $mysql->query('INSERT INTO conversations(sender, recipient) VALUES("'.$_SESSION['pid'].'", "'.$user_id.'")');
if(!$create_conversation) {
plainErr(500, '500 Internal Server Error'); }
$conversation_id = $create_conversation->fetch_assoc()['conversation_id'];
} }

        $stmt_message = $mysql->prepare('INSERT INTO messages(conversation_id, id, pid, feeling_id, platform_id, body, screenshot, is_spoiler, has_read, created_from)
                VALUES(?, ?, ?, ?, "1", ?, ?, ?, "0", ?)');
		$scrnst = (empty($_POST['screenshot']) ? '' : $_POST['screenshot']); $isspr = (empty($_POST['is_spoiler']) ? 0 : $_POST['is_spoiler']);
		$stmt_message->bind_param('isiissis', $conversation_id, $gen_olive_url, $_SESSION['pid'], $_POST['feeling_id'], $_POST['body'], $scrnst, $isspr, $_SERVER['REMOTE_ADDR']); $exec_msg_stmt = $stmt_message->execute();

if(!$exec_msg_stmt) {
http_response_code(500);
header('Content-Type: application/json');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
} else {
	if(!empty($relationship)) {
		$updateFM = $mysql->query('UPDATE friend_relationships SET updated = NOW() WHERE relationship_id = "'.$relationship['relationship_id'].'"');
	}
		# Success, print post.
require_once 'lib/htmUser.php';
printMessage($mysql->query('SELECT * FROM messages WHERE messages.id = "'.$gen_olive_url.'" LIMIT 1')->fetch_assoc());
	   }
 exit();
}
# If not, then do everything else.

# If user isn't logged in, then 403 them.
# If a user ID is specified, then display a special page for that, and if not, then display the message search.
if(!empty($_GET['user_id']) || !empty($_GET['conversation_id'])) {
require_once '../grplib-php/user-helper.php';
if(empty($_GET['conversation_id'])) {
$mode = 0;
$search_person = $mysql->query('SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"');
if($search_person->num_rows == 0) { require '404.php';  exit(); } else { $person = $search_person->fetch_assoc(); }
$relationship = getFriendRelationship($_SESSION['pid'], $person['pid']);
if(!$relationship) { plainErr(403, '403 Forbidden');  exit(); }
if(!empty($relationship['conversation_id'])) {
$conversation_id = $relationship['conversation_id']; }
else {
$create_conversation = $mysql->query('INSERT INTO conversations(sender, recipient) VALUES("'.$_SESSION['pid'].'", "'.$person['pid'].'")');
if(!$create_conversation) {
plainErr(500, '500 Internal Server Error');  exit(); }
$conversation_id = $mysql->query('SELECT * FROM conversations WHERE conversations.conversation_id = "'.$mysql->insert_id.'" LIMIT 1')->fetch_assoc()['conversation_id'];
}
} else {
$mode = 1;
if($me['privilege'] <= 6) { plainErr(403, '403 Forbidden');  exit(); }
$search_conversation = $mysql->query('SELECT * FROM conversations WHERE conversations.conversation_id = "'.$mysql->real_escape_string($_GET['conversation_id']).'" LIMIT 1');
if(!$search_conversation || $search_conversation->num_rows == 0) { plainErr(404, '404 Not Found');  exit(); }
$conversation_id = $search_conversation->fetch_assoc()['conversation_id'];
}

if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
if(empty($_GET['conversation_id'])) {
$pagetitle = 'Conversation with '.htmlspecialchars($person['screen_name']).' ('.htmlspecialchars($person['user_id']).')';
} else {
$pagetitle = 'ConversationID '.htmlspecialchars($_GET['conversation_id']); 
}
printHeader(false); printMenu();
print $GLOBALS['div_body_head'];
print '<header id="header">
  <a id="header-message-button" class="header-button" href="#" data-modal-open="#add-message-page">Write Message</a>
  
  <h1 id="page-title" class="">'.$pagetitle.'</h1>

</header>
';
}
$messages = $mysql->query('SELECT * FROM messages WHERE messages.conversation_id = "'.$conversation_id.'" ORDER BY messages.created_at DESC LIMIT 20'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$_GET['offset'] : ''));
if(!empty($_GET['offset'])) { $my_new_offset = strval($_GET['offset']) + 20; }
print '
<div class="body-content message-post-list" id="message-page" data-next-page-url="'.($messages->num_rows > 19 ? '?offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? 20 + $_GET['offset'] : 20) : '').'">

';
require_once 'lib/htmUser.php';
while($message = $messages->fetch_assoc()) {
printMessage($message, (empty($_GET['conversation_id']) ? $person : false));
}

print '
</div>
';

if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
require_once 'lib/htmUser.php';
# Add message form
messageForm($me, (empty($_GET['conversation_id']) ? $person : false));
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

$has_tutorial = $mysql->query('SELECT * FROM settings_tutorial WHERE settings_tutorial.pid = "'.$_SESSION['pid'].'" AND settings_tutorial.friend_messages = "1"')->num_rows == 0;

$friends = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" OR friend_relationships.target = "'.$_SESSION['pid'].'" ORDER BY friend_relationships.updated DESC');
# seinfeld is better

print '<div class="body-content" id="messages-list">
';
if($friends->num_rows == 0) {
$no_content_message = noContentWindow('In Messages, you can send messages to friends and view past messages. If you want to play a game with a friend or get some tips if you\'re stuck, try sending a message!'); }
elseif($has_tutorial) {
print '<div class="tutorial-window">
    <p>In Messages, you can send messages to friends and view past messages. If you want to play a game with a friend or get some tips if you\'re stuck, try sending a message!</p>
    <a href="#" class="button tutorial-close-button" data-tutorial-name="messages">Close</a>
  </div>'; }

# Show users available to message
print '<ul class="list-content-with-icon-and-text arrow-list">       

';      
while($row_friends = $friends->fetch_assoc()) {
$friend = $mysql->query('SELECT * FROM people WHERE people.pid = "'.($row_friends['target'] == $_SESSION['pid'] ? $row_friends['source'] : $row_friends['target']).'"')->fetch_assoc();
if($friend['pid'] != $_SESSION['pid']) {
$mii = getMii($friend, false);
$conversation = $mysql->query('SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" AND conversations.recipient = "'.$friend['pid'].'" OR conversations.sender = "'.$friend['pid'].'" AND conversations.recipient = "'.$_SESSION['pid'].'"');
if($conversation->num_rows != 0) {
$recent_msg = $mysql->query('SELECT * FROM messages WHERE messages.conversation_id = "'.$conversation->fetch_assoc()['conversation_id'].'" ORDER BY messages.created_at DESC LIMIT 1')->fetch_assoc(); }
else { $recent_msg = array('has_read'=>1,'pid'=>0); }
print '
      <li>
        <a href="/users/'.htmlspecialchars($friend['user_id']).'" data-pjax="#body" class="icon-container'.($mii['official'] ? ' official-user' : '').' trigger'.(isset($recent_msg['has_read']) && $recent_msg['pid'] != $_SESSION['pid'] && $recent_msg['has_read'] == 
		0 ? ' notify' : '').'"><img src="'.$mii['output'].'" class="icon"></a>
        <a href="/friend_messages/'.htmlspecialchars($friend['user_id']).'" data-pjax="#body" class="arrow-button"></a>
        <div class="body">
          <p class="title">
            <span class="nick-name">'.htmlspecialchars($friend['screen_name']).'</span>
            <span class="id-name">'.htmlspecialchars($friend['user_id']).'</span>
          </p>
          
          
		  ';
if(empty($recent_msg['conversation_id'])) {
print '          <p class="text placeholder">You haven\'t exchanged messages with this user yet.</p>
';
} else {
print '   <span class="timestamp">'.humanTiming(strtotime($recent_msg['created_at'])).'</span>
          <p class="text'.($recent_msg['pid'] == $_SESSION['pid'] ? ' my' : ' other').'">'.htmlspecialchars($recent_msg['body']).'</p>
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
#Set all as read
$get_all_conversations = $mysql->query('SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" OR conversations.recipient = "'.$_SESSION['pid'].'"');
if($get_all_conversations->num_rows != 0) {
while($all_conversation_ids = $get_all_conversations->fetch_assoc()) {
$mysql->query('UPDATE messages SET has_read = "1" WHERE messages.conversation_id = "'.$all_conversation_ids['conversation_id'].'" AND messages.pid != "'.$_SESSION['pid'].'"');
} }

(isset($_SERVER['HTTP_X_PJAX']) ? '' : printFooter());