<?php
include 'lib/sql-connect.php';
if($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_GET['olive_community_id'])) {
	include 'lib/404.php';
    exit();
}

if(isset($_GET['delete'])) {
		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to configure settings.';
			$error_code[] = '1512005';
        }
else {
$search_for_community_by_uri = mysqli_query($link, 'SELECT * FROM communities WHERE communities.olive_community_id = "'.mysqli_real_escape_string($link, $_GET['olive_community_id']).'"');
$row_search_for_community_by_uri = mysqli_fetch_assoc($search_for_community_by_uri);
if(mysqli_num_rows($search_for_community_by_uri) == 0) {
			$error_message[] = 'The community could not be found.';
			$error_code[] = '1512012';	
			}
else {
$search_my_user_favorites_for_this = mysqli_query($link, 'SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" AND favorites.community_id = "'.$row_search_for_community_by_uri['community_id'].'"');
if(mysqli_num_rows($search_my_user_favorites_for_this) == 0) {
			$error_message[] = 'You have not favorited this community.';
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
$result_create_favorite = mysqli_query($link, 'DELETE FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" AND favorites.community_id = "'.$row_search_for_community_by_uri['community_id'].'"');
        if(!$result_create_favorite)
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

else {
		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to configure settings.';
			$error_code[] = '1512005';
        }
else {
$search_for_community_by_uri = mysqli_query($link, 'SELECT * FROM communities WHERE communities.olive_community_id = "'.mysqli_real_escape_string($link, $_GET['olive_community_id']).'"');
$row_search_for_community_by_uri = mysqli_fetch_assoc($search_for_community_by_uri);
if(mysqli_num_rows($search_for_community_by_uri) == 0) {
			$error_message[] = 'The community could not be found.';
			$error_code[] = '1512012';	
			}
else {
$search_my_user_favorites_for_this = mysqli_query($link, 'SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" AND favorites.community_id = "'.$row_search_for_community_by_uri['community_id'].'"');
if(mysqli_num_rows($search_my_user_favorites_for_this) != 0) {
			$error_message[] = 'You have already favorited this community.';
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
$result_create_favorite = mysqli_query($link, 'INSERT INTO favorites (pid, community_id) VALUES ("'.$_SESSION['pid'].'", "'.$row_search_for_community_by_uri['community_id'].'")');
        if(!$result_create_favorite)
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