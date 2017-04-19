<?php
require_once '../grplib-php/init.php';

if(empty($_SESSION['pid'])) {
require 'lib/htm.php';
notLoggedIn(); grpfinish($mysql); exit();
}

# If the method is POST, then post.
if($_SERVER['REQUEST_METHOD'] == 'POST') {
if(empty($_GET['conversation_id'])) {
$result_people_messagesearch = mysqli_query($mysql, 'SELECT * FROM people WHERE people.user_id = "'.mysqli_real_escape_string($mysql, $_POST['message_to_user_id']).'"');
if(mysqli_num_rows($result_people_messagesearch) == 0) {
			$error_message[] = 'The user could not be found.';
			$error_code[] = '1512012';
} else {
$row_people_messagesearch = mysqli_fetch_assoc($result_people_messagesearch);
$result_relationshipsearch = mysqli_query($mysql, 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_people_messagesearch['pid'].'" AND friend_relationships.target = "'.$_SESSION['pid'].'" OR friend_relationships.source = "'.$_SESSION['pid'].'" OR friend_relationships.source = "'.$row_people_messagesearch['pid'].'" LIMIT 1');
if(mysqli_num_rows($result_relationshipsearch) == 0) {
			$error_message[] = 'You are not friends with this user.';
			$error_code[] = '1512013';	
}
} }
else {
$result_people_messagesearch = mysqli_query($mysql, 'SELECT * FROM conversations WHERE conversations.conversation_id = "'.mysqli_real_escape_string($mysql, $_GET['conversation_id']).'"');
if(mysqli_num_rows($result_people_messagesearch) == 0) {
			$error_message[] = 'The user could not be found.';
			$error_code[] = '1512012';
}

        if(strlen($_POST['body']) <= 0)
        {
            $error_message[] = 'The content you have entered is blank.\nPlease enter content into your post.';
			$error_code[] = '1515001';
        }
        if(preg_replace( '/[\x{200B}-\x{200D}]/u', '', $_POST['body'] ) == '') {
            $error_message[] = 'The content you have entered is blank.\nPlease enter content into your post.';
			$error_code[] = '1515001';
}
if(ctype_space(preg_replace( '/[\x{200B}-\x{200D}]/u', '', $_POST['body'] ))) {
            $error_message[] = 'The content you have entered is blank.\nPlease enter content into your post.';
			$error_code[] = '1515001';
}
		# start url checks
		if(isset($_POST['screenshot'])) {
      	if(strlen($_POST['screenshot']) >= 1) {
		if(strlen($_POST['screenshot']) > 255)
        {
            $error_message[] = 'You have exceeded the amount of characters that you can send.';
			$error_code[] = '1515002';
        }
		if (substr($_POST['screenshot'], 0, 4) != "http" && strlen($_POST['screenshot']) >= 3) {
            $error_message[] = 'The screenshot URL you have specified is not of HTTP or HTTPS.';
			$error_code[] = '1515003';
        }
		if(strlen($_POST['screenshot']) < 11 && strlen($_POST['screenshot']) >= 3) {
		    $error_message[] = 'The screenshot URL you have specified is too short.';
			$error_code[] = '1515004';	
		}
if (filter_var($_POST['screenshot'], FILTER_VALIDATE_URL) === FALSE) {
		    $error_message[] = 'The screenshot URL you have specified is not valid.';
			$error_code[] = '1515005';			
        }
		}
		}		

		# end url checks
}
	    if(!empty($error_code) || !empty($error_message) ) /*Got errors?*/
    {
		// JSON response for errors.
			http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
			print '{"success":0,"errors":[{"message":"' . $error_message[0] . '","error_code":' . $error_code[0] . '}],"code":"400"}';
			print "\n";
    }
	else {
require 'lib/olv-url-enc.php';
$gen_olive_url = $b64url_data;

if(!isset($_POST['is_spoiler'])) { $_POST['is_spoiler'] = false; } if(empty($_POST['feeling_id'])) {
$_POST['feeling_id'] = '0'; }
if(empty($_GET['conversation_id'])) {
$result_search_conversations = mysqli_query($mysql, 'SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" AND conversations.recipient = "'.$row_people_messagesearch['pid'].'" OR conversations.recipient = "'.$_SESSION['pid'].'" AND conversations.sender = "'.$row_people_messagesearch['pid'].'"');
if(mysqli_num_rows($result_search_conversations) == 0) {
	
$recipient_in_new_conversation1 = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" AND friend_relationships.target = "'.$row_people_messagesearch['pid'].'" OR friend_relationships.target = "'.$_SESSION['pid'].'" AND friend_relationships.source = "'.$row_people_messagesearch['pid'].'"'));

$recipient_in_new_conversation = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM people WHERE people.pid = "'.($recipient_in_new_conversation1['source'] == $_SESSION['pid'] ? $recipient_in_new_conversation1['target'] : $recipient_in_new_conversation1['source']).'"'));

$create_new_conversation = mysqli_query($mysql, 'INSERT INTO conversations (sender, recipient) VALUES ("'.$_SESSION['pid'].'", "'.$recipient_in_new_conversation['pid'].'")');

$new_message_conversation_id = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" AND conversations.recipient = "'.$recipient_in_new_conversation['pid'].'"'))['conversation_id'];
} 
else {
$new_message_conversation_id = mysqli_fetch_assoc($result_search_conversations)['conversation_id']; }
}
else {
$new_message_conversation_id = mysqli_real_escape_string($mysql, $_GET['conversation_id']);	
}

        $sql = 'INSERT INTO
                    messages(conversation_id, id, pid, feeling_id, platform_id, body, screenshot, is_spoiler, has_read)
                VALUES("' . $new_message_conversation_id . '",
				       "' . $gen_olive_url  . '",
				       "' . mysqli_real_escape_string($mysql, $_SESSION['pid']) . '",
                       "' . htmlspecialchars(mysqli_real_escape_string($mysql, $_POST['feeling_id'])) . '",
                       "' . mysqli_real_escape_string($mysql, $_SESSION['platform_id']) . '",
                       "' . mysqli_real_escape_string($mysql, $_POST['body']) . '",
					   "' . (empty($_POST['screenshot']) ? NULL : mysqli_real_escape_string($mysql, $_POST['screenshot'])) . '",
                       "' . (empty($_POST['is_spoiler']) ? '0' : mysqli_real_escape_string($mysql, $_POST['is_spoiler'])) . '",
                       "0")';
                         
        $result = mysqli_query($mysql, $sql);
		$resultUpdateFM = mysqli_query($mysql, 'UPDATE friend_relationships SET updated = CURRENT_TIMESTAMP WHERE relationship_id = "'.mysqli_fetch_assoc($result_relationshipsearch)['relationship_id'].'"');
        if(!$result)
        {
            //MySQL error; print jsON response.
			http_response_code(400);  
			header('Content-Type: application/json; charset=utf-8');
			
			// Enable in debug
			#print $sql;
			#print "\n\n";			
			
			print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"500"}';
			print "\n";
		}
		else {
			// HTML fragment success response.
$row_get_posted_message = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM messages WHERE messages.id = "'.$gen_olive_url.'"'));
$message_template_row = $row_get_posted_message;
include 'lib/messagelist-message-template.php';
// End here.
		
		}

	}
}

# If not, then do everything else.

else {
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
# If a user ID is specified, then display a special page for that, and if not, then display the message search.
if(isset($_GET['user_id']) || isset($_GET['conversation_id'])) {
if(!isset($_GET['conversation_id'])) {
$result_get_person_formessage = mysqli_query($mysql, 'SELECT * FROM people WHERE people.user_id = "'.mysqli_real_escape_string($mysql, $_GET['user_id']).'"');
if(mysqli_num_rows($result_get_person_formessage) == 0) {
include_once '404.php'; exit();	} }

else {
$result_get_conversation = mysqli_query($mysql, 'SELECT * FROM conversations WHERE conversations.conversation_id = "'.mysqli_real_escape_string($mysql, $_GET['conversation_id']).'"');
if(mysqli_num_rows($result_get_conversation) == 0) {
include_once '404.php'; exit();	} }
if(isset($_GET['user_id'])) {
$row_get_person_formessage = mysqli_fetch_assoc($result_get_person_formessage);
$result_search_person_relationship = mysqli_query($mysql, 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_get_person_formessage['pid'].'" AND friend_relationships.source = "'.$_SESSION['pid'].'" OR friend_relationships.target = "'.$_SESSION['pid'].'" AND friend_relationships.source = "'.$row_get_person_formessage['pid'].'" LIMIT 1');
if(mysqli_num_rows($result_search_person_relationship) == 0) {
header('Content-Type: text/plain; charset=UTF-8'); header("HTTP/1.1 403 Forbidden"); exit("403 Forbidden\n");   } }
else {
if(mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"'))['privilege'] < 5) {
header('Content-Type: text/plain; charset=UTF-8'); header("HTTP/1.1 403 Forbidden"); exit("403 Forbidden\n");   } }
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
$find_conversation_for_friend = mysqli_query($mysql, 'SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" AND conversations.recipient = "'.$row_get_person_formessage['pid'].'" OR conversations.sender = "'.$row_get_person_formessage['pid'].'" AND conversations.recipient = "'.$_SESSION['pid'].'" LIMIT 1');
}
if(isset($_GET['offset']) && is_numeric($_GET['offset']) && strval($_GET['offset']) >= 1) {
$get_messages_for_friend = mysqli_query($mysql, 'SELECT * FROM messages WHERE messages.conversation_id = '.(!empty($_GET['conversation_id']) ? mysqli_real_escape_string($mysql, $_GET['conversation_id']) : mysqli_fetch_assoc($find_conversation_for_friend)['conversation_id']).' ORDER BY messages.created_at DESC LIMIT 20 OFFSET '.mysqli_real_escape_string($mysql, $_GET['offset']).'');	
}
else {
$get_messages_for_friend = mysqli_query($mysql, 'SELECT * FROM messages WHERE messages.conversation_id = '.(!empty($_GET['conversation_id']) ? mysqli_real_escape_string($mysql, $_GET['conversation_id']) : mysqli_fetch_assoc($find_conversation_for_friend)['conversation_id']).' ORDER BY messages.created_at DESC LIMIT 20');
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
$lookup_user = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"'));
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
  <img src="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_normal_face.png" class="icon">
  <ul class="buttons"><li class="checked"><input type="radio" name="feeling_id" value="0" class="feeling-button-normal" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_normal_face.png" checked="" data-sound="SE_WAVE_MII_FACE_00"></li><li><input type="radio" name="feeling_id" value="1" class="feeling-button-happy" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_happy_face.png" data-sound="SE_WAVE_MII_FACE_01"></li><li><input type="radio" name="feeling_id" value="2" class="feeling-button-like" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_like_face.png" data-sound="SE_WAVE_MII_FACE_02"></li><li><input type="radio" name="feeling_id" value="3" class="feeling-button-surprised" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_surprised_face.png" data-sound="SE_WAVE_MII_FACE_03"></li><li><input type="radio" name="feeling_id" value="4" class="feeling-button-frustrated" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_frustrated_face.png" data-sound="SE_WAVE_MII_FACE_04"></li><li><input type="radio" name="feeling_id" value="5" class="feeling-button-puzzled" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_puzzled_face.png" data-sound="SE_WAVE_MII_FACE_05"></li>  </ul>
</div>';
	}
	if(isset($lookup_user['user_face'])) {
	if($lookup_user['user_face']) {	
	print '<div class="feeling-selector expression">
  <img src="' . htmlspecialchars($lookup_user['user_face']) . '" class="icon">
  
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

$result_get_all_conversation_ids = mysqli_query($mysql, 'SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" OR conversations.recipient = "'.$_SESSION['pid'].'"');
if(mysqli_num_rows($result_get_all_conversation_ids) != 0) {
while($row_get_all_conversation_ids = mysqli_fetch_assoc($result_get_all_conversation_ids)) {
$result_set_all_messages_unread = mysqli_query($mysql, 'UPDATE messages SET has_read = "1" WHERE messages.conversation_id = "'.$row_get_all_conversation_ids['conversation_id'].'" AND messages.pid != "'.$_SESSION['pid'].'"');
} }

(isset($_SERVER['HTTP_X_PJAX']) ? '' : printFooter());
}
}

else {
$pagetitle = 'Messages';
require_once 'lib/htm.php';
printHeader(false);
printMenu();

print $GLOBALS['div_body_head'];
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

$result_find_user_newstutorial = mysqli_query($mysql, 'SELECT * FROM settings_tutorial WHERE settings_tutorial.pid = "'.$_SESSION['pid'].'" AND settings_tutorial.friend_messages = "1"');

$result_get_friendexistence = mysqli_query($mysql, 'SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" OR friend_relationships.target = "'.$_SESSION['pid'].'" ORDER BY friend_relationships.updated DESC');

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
$row_get_friend = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM people WHERE people.pid = "'.($row_get_friendexistence['target'] == $_SESSION['pid'] ? $row_get_friendexistence['source'] : $row_get_friendexistence['target']).'"'));
if($row_get_friend['pid'] != $_SESSION['pid']) {
if($row_get_friend['mii_hash']) {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_get_friend['mii_hash'] . '_normal_face.png'; 
}
else {
if($row_get_friend['user_face']) {
$mii_face_output = htmlspecialchars($row_get_friend['user_face']);
} else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}
$find_conversation_for_friend1 = mysqli_query($mysql, 'SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" AND conversations.recipient = "'.$row_get_friend['pid'].'" OR conversations.sender = "'.$row_get_friend['pid'].'" AND conversations.recipient = "'.$_SESSION['pid'].'" LIMIT 1');
if(mysqli_num_rows($find_conversation_for_friend1) != 0) {
$get_message_search_recent_messg1 = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM messages WHERE messages.conversation_id = "'.mysqli_fetch_assoc($find_conversation_for_friend1)['conversation_id'].'" ORDER BY messages.created_at DESC LIMIT 1')); }
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
$find_conversation_for_friend = mysqli_query($mysql, 'SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" AND conversations.recipient = "'.$row_get_friend['pid'].'" OR conversations.sender = "'.$row_get_friend['pid'].'" AND conversations.recipient = "'.$_SESSION['pid'].'" LIMIT 1');
if(mysqli_num_rows($find_conversation_for_friend) == 0) {
print '          <p class="text placeholder">You haven'."'".'t exchanged messages with this user yet.</p>
';
} else {
$get_message_search_recent_messg = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM messages WHERE messages.conversation_id = "'.mysqli_fetch_assoc($find_conversation_for_friend)['conversation_id'].'" ORDER BY messages.created_at DESC LIMIT 1'));
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
$result_get_all_conversation_ids = mysqli_query($mysql, 'SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION['pid'].'" OR conversations.recipient = "'.$_SESSION['pid'].'"');
if(mysqli_num_rows($result_get_all_conversation_ids) != 0) {
while($row_get_all_conversation_ids = mysqli_fetch_assoc($result_get_all_conversation_ids)) {
$result_set_all_messages_unread = mysqli_query($mysql, 'UPDATE messages SET has_read = "1" WHERE messages.conversation_id = "'.$row_get_all_conversation_ids['conversation_id'].'" AND messages.pid != "'.$_SESSION['pid'].'"');
} }

(isset($_SERVER['HTTP_X_PJAX']) ? '' : printFooter());
} }

