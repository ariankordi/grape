<?php
require_once '../grplib-php/init.php';
if($_SERVER['REQUEST_METHOD'] != 'POST') {
	include_once '404.php';
    grpfinish($mysql); exit();
}

if(isset($_GET['user_id'])) {
$search_user = $mysql->query('SELECT * FROM people WHERE people.user_id = "'.(empty($_GET['user_id']) ? 'a' : $mysql->real_escape_string($_GET['user_id'])).'"'); } else {
$search_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.(empty($_POST['pid']) ? 'a' : $mysql->real_escape_string($_POST['pid'])).'"');	
}

if($search_user->num_rows == 0) { http_response_code(404); header('Content-Type: application/json; charset=utf-8');
print json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit(); }

if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }

$user = $search_user->fetch_assoc();

if($_SESSION['pid'] == $user['pid']) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 400)); grpfinish($mysql); exit(); }

if(isset($_GET['breakup'])) {
$result_friend_relationship = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" AND friend_relationships.target = "'.$user['pid'].'" OR friend_relationships.source = "'.$user['pid'].'" AND friend_relationships.target = "'.$_SESSION['pid'].'"');
if($result_friend_relationship->num_rows == 0) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 400)); grpfinish($mysql); exit(); }

# Breakup
$result_breakup = $mysql->query('DELETE FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" AND friend_relationships.target = "'.$user['pid'].'" OR friend_relationships.source = "'.$user['pid'].'" AND friend_relationships.target = "'.$_SESSION['pid'].'"');
if(!$result_breakup) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
	} else {
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 1)); }
grpfinish($mysql); exit();		
}

if(isset($_GET['create']) && substr($_SERVER['QUERY_STRING'], 0, 6) == 'create') {
if($mysql->query('SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.$user['pid'].'" AND friend_requests.finished = "0"')->num_rows != 0 || ($mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" AND friend_relationships.target = "'.$user['pid'].'"')->num_rows != 0)) {
			$error_message[] = 'You have either already sent a friend request to this user\n or are already friends with them.';
			$error_code[] = 1512013; }
		if(strlen($_POST['body']) > 255) {
            $error_message[] = 'You have exceeded the amount of characters that you can send.';
			$error_code[] = 1515002; }
	    if(!empty($error_code) || !empty($error_message) ) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [array( 'message' => $error_message[0], 'error_code' => $error_code[0])], 'code' => 400)); grpfinish($mysql); exit(); }
else {
$result_create_friendrequest = $mysql->query('INSERT INTO friend_requests (sender, recipient, `message`, `has_read`, `finished`) VALUES ("'.$_SESSION['pid'].'", "'.$user['pid'].'", "'.mysqli_real_escape_string($mysql, $_POST['body']).'", "0", "0")');
if(!$result_create_friendrequest) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
	} else {
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 1)); }
grpfinish($mysql); exit();
        }
}

else {
$sql_fr_ees1 = 'SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.mysqli_real_escape_string($mysql, $_POST['pid']).'" AND friend_requests.finished = "1"';
$sql_fr_ees2 = 'SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" AND friend_relationships.target = "'.mysqli_real_escape_string($mysql, $_POST['pid']).'"';

if($mysql->query($sql_fr_ees2)->num_rows != 0) {
			$error_message[] = 'You have either already sent a friend request to this user\n or are already friends with them.';
			$error_code[] = '1512013';	
}

if($_SERVER['QUERY_STRING'] == 'cancel' && $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.$user['pid'].'" AND friend_requests.finished = "0"')->num_rows == 0) {
			$error_message[] = 'You have not sent a friend request to this user.';
			$error_code[] = 1512014;  }
if($_SERVER['QUERY_STRING'] == 'delete' && $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$user['pid'].'" AND friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.finished = "0"')->num_rows == 0) {
			$error_message[] = 'You have not sent a friend request to this user.';
			$error_code[] = 1512014;  }

	    if(!empty($error_code) || !empty($error_message) ) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [array( 'message' => $error_message[0], 'error_code' => $error_code[0])], 'code' => 400)); grpfinish($mysql); exit(); }
else {

if($_SERVER['QUERY_STRING'] == 'delete' || $_SERVER['QUERY_STRING'] == 'cancel') {
	if($_SERVER['QUERY_STRING'] == 'cancel') {
		$sql_newscreate = 'UPDATE friend_requests SET finished="1" WHERE recipient="'.$user['pid'].'" AND sender="'.$_SESSION['pid'].'"';
	} else {
		$sql_newscreate = 'UPDATE friend_requests SET finished="1" WHERE sender="'.$user['pid'].'" AND recipient="'.$_SESSION['pid'].'"';	
	}
        $result_newscreate = $mysql->query($sql_newscreate);
        if(!$result_newscreate) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); }
        else {
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 1)); }
}
	
else {
# Create friend!	
$result_relationshipcreate = $mysql->query('INSERT INTO friend_relationships(source, target) VALUES ("' . $user['pid'] . '", "' . $_SESSION['pid'] . '")');
$result_newscreate = $mysql->query('UPDATE friend_requests SET finished="1" WHERE sender="'.$user['pid'].'" AND recipient="'.$_SESSION['pid'].'"');
        if(!$result_relationshipcreate) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); }
        else {
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 1)); }
}		
}

}
