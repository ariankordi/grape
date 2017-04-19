<?php
require_once '../grplib-php/init.php';
if($_SERVER['REQUEST_METHOD'] != 'GET') {
# If method isn't GET, display 404.
include_once '404.php'; }

else {
if(empty($_SESSION['pid'])) {
header('Location: http://' . $_SERVER['HTTP_HOST'] .'/guest_menu', true, 302); }
else {
 
$sql_profilecreate_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "' . $_SESSION['pid'] . '"';
$result_profilecreate_user_profile = mysqli_query($mysql, $sql_profilecreate_user_profile);
$row_profilecreate_user_profile = mysqli_fetch_assoc($result_profilecreate_user_profile); 
 
if(mysqli_num_rows($result_profilecreate_user_profile) == 0) {
$sql_profilecreate_user = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_profilecreate_user = mysqli_query($mysql, $sql_profilecreate_user);
$row_profilecreate_user = mysqli_fetch_assoc($result_profilecreate_user);
        $sql = "INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . mysqli_real_escape_string($mysql, $_SESSION['pid']) . "',
                       '" . mysqli_real_escape_string($mysql, $_SESSION['platform_id']) . "')";
                         
        $result = mysqli_query($mysql, $sql);
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
header('Location: http://' . $_SERVER['HTTP_HOST'] .'/users/'.$row_profilecreate_user['user_id'].'', true, 302); }

}


else {
header('Location: http://' . $_SERVER['HTTP_HOST'] .'/guest_menu', true, 302); }

}
}
