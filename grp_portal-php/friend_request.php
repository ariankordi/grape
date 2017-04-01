<?php
include 'lib/sql-connect.php';
if($_SERVER['REQUEST_METHOD'] != 'POST') {
	include 'lib/404.php';
    exit();
}

if(isset($_GET['breakup'])) {
		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to add friends';
			$error_code[] = '1512005';
        }
else {
if(mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.people WHERE people.pid = "'.mysqli_real_escape_string($link, $_POST['pid']).'"')) == 0) {
			$error_message[] = 'The user could not be found.';
			$error_code[] = '1512012';	
}
if($_SESSION['pid'] == mysqli_real_escape_string($link, $_POST['pid'])) {
			$error_message[] = 'You cannot friend yourself.';
			$error_code[] = '1512008';				  
}

$sql_friend_relationship = 'SELECT * FROM grape.friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" AND friend_relationships.target = "'.mysqli_real_escape_string($link, $_POST['pid']).'" OR friend_relationships.source = "'.mysqli_real_escape_string($link, $_POST['pid']).'" AND friend_relationships.target = "'.$_SESSION['pid'].'"';
$result_friend_relationship = mysqli_query($link, $sql_friend_relationship);
if(mysqli_num_rows($result_friend_relationship) == 0) {
			$error_message[] = 'You are not friends with this user.';
			$error_code[] = '1512013';	
}

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
# Breakup
		$sql_breakup = 'DELETE FROM grape.friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" AND friend_relationships.target = "'.mysqli_real_escape_string($link, $_POST['pid']).'" OR friend_relationships.source = "'.mysqli_real_escape_string($link, $_POST['pid']).'" AND friend_relationships.target = "'.$_SESSION['pid'].'"';
        $result_breakup = mysqli_query($link, $sql_breakup);
        if(!$result_breakup)
        {
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($link) . '}],"code":"400"}';
print "\n";
	}
        else
        {
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}';
print "\n";
        }
}
exit();		
}

if(isset($_GET['create']) && substr($_SERVER['QUERY_STRING'], 0, 6) == 'create') {
$get_user_for_friend_add = mysqli_query($link, 'SELECT people.pid FROM grape.people WHERE people.user_id = "'.mysqli_real_escape_string($link, $_GET['user_id']).'"');
$get_user_for_friend_add2 = mysqli_fetch_assoc($get_user_for_friend_add);

		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to add friends';
			$error_code[] = '1512005';
        }
else {
if(mysqli_num_rows($get_user_for_friend_add) == 0) {
			$error_message[] = 'The user could not be found.';
			$error_code[] = '1512012';	
}
else {
if(mysqli_fetch_assoc(mysqli_query($link, 'SELECT people.user_id FROM grape.people WHERE people.pid = "'.$_SESSION['pid'].'"'))['user_id'] == mysqli_real_escape_string($link, $_GET['user_id'])) {
			$error_message[] = 'You cannot friend yourself.';
			$error_code[] = '1512008';				  
}
	
if(mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.$get_user_for_friend_add2['pid'].'" AND friend_requests.finished = "0"')) != 0 || (mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" AND friend_relationships.target = "'.$get_user_for_friend_add2['pid'].'"')) != 0)) {
			$error_message[] = 'You have either already sent a friend request to this user\n or are already friends with them.';
			$error_code[] = '1512013';	
}
		if(strlen($_POST['body']) > 255) {
            $error_message[] = 'You have exceeded the amount of characters that you can send.';
			$error_code[] = '1515002';
        }
} }
	    if(!empty($error_code) || !empty($error_message) ) /*Got errors?*/
    {
		// JSON response for errors.
			http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
			print '{"success":0,"errors":[{"message":"' . $error_message[0] . '","error_code":' . $error_code[0] . '}],"code":"400"}';
			print "\n";
    }
else {
$sql_create_friendrequest = 'INSERT INTO grape.friend_requests (sender, recipient, `message`, `has_read`, `finished`) VALUES ("'.$_SESSION['pid'].'", "'.$get_user_for_friend_add2['pid'].'", "'.mysqli_real_escape_string($link, $_POST['body']).'", "0", "0")';
$result_create_friendrequest = mysqli_query($link, $sql_create_friendrequest);
        if(!$result_create_friendrequest)
        {
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($link) . '}],"code":"400"}';
print "\n";
	}
        else
        {
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}';
print "\n";
        }
}

}

else {
		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to add friends';
			$error_code[] = '1512005';
        }
else {
if(mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.people WHERE people.pid = "'.mysqli_real_escape_string($link, $_POST['pid']).'"')) == 0) {
			$error_message[] = 'The user could not be found.';
			$error_code[] = '1512012';	
}
if($_SESSION['pid'] == mysqli_real_escape_string($link, $_POST['pid'])) {
			$error_message[] = 'You cannot friend yourself.';
			$error_code[] = '1512008';				  
}
$sql_fr_ees1 = 'SELECT * FROM grape.friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.mysqli_real_escape_string($link, $_POST['pid']).'" AND friend_requests.finished = "1"';
$sql_fr_ees2 = 'SELECT * FROM grape.friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" AND friend_relationships.target = "'.mysqli_real_escape_string($link, $_POST['pid']).'"';

if(mysqli_num_rows(mysqli_query($link, $sql_fr_ees2)) != 0) {
			$error_message[] = 'You have either already sent a friend request to this user\n or are already friends with them.';
			$error_code[] = '1512013';	
}

if($_SERVER['QUERY_STRING'] == 'cancel' && mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.mysqli_real_escape_string($link, $_POST['pid']).'" AND friend_requests.finished = "0"')) == 0) {
			$error_message[] = 'You have not sent a friend request to this user.';
			$error_code[] = '1512014';	
}
if($_SERVER['QUERY_STRING'] == 'delete' && mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.friend_requests WHERE friend_requests.sender = "'.mysqli_real_escape_string($link, $_POST['pid']).'" AND friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.finished = "0"')) == 0) {
			$error_message[] = 'You have not sent a friend request to this user.';
			$error_code[] = '1512014';	
}

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

if($_SERVER['QUERY_STRING'] == 'delete' || $_SERVER['QUERY_STRING'] == 'cancel') {
	if($_SERVER['QUERY_STRING'] == 'cancel') {
		$sql_newscreate = 'UPDATE grape.friend_requests SET finished="1" WHERE recipient="'.mysqli_real_escape_string($link, $_POST['pid']).'" AND sender="'.$_SESSION['pid'].'"';
	} else {
		$sql_newscreate = 'UPDATE grape.friend_requests SET finished="1" WHERE sender="'.mysqli_real_escape_string($link, $_POST['pid']).'" AND recipient="'.$_SESSION['pid'].'"';	
	}
        $result_newscreate = mysqli_query($link, $sql_newscreate);
        if(!$result_newscreate)
        {
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($link) . '}],"code":"400"}';
print "\n";
	}
        else
        {
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}';
print "\n";
        }
}
	
else {
# Create friend!	
        $sql_relationshipcreate = 'INSERT INTO grape.friend_relationships(source, target) VALUES ("' . mysqli_real_escape_string($link, $_POST['pid']) . '", "' . $_SESSION['pid'] . '")';
		$sql_newscreate = 'UPDATE grape.friend_requests SET finished="1" WHERE sender="'.mysqli_real_escape_string($link, $_POST['pid']).'" AND recipient="'.$_SESSION['pid'].'"';
        $result_relationshipcreate = mysqli_query($link, $sql_relationshipcreate);
        $result_newscreate = mysqli_query($link, $sql_newscreate);
        if(!$result_relationshipcreate)
        {
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($link) . '}],"code":"400"}';
print "\n";
	}
        else
        {
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}';
print "\n";
        }
}		
}

}
?>