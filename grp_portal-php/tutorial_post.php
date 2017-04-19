<?php
require_once '../grplib-php/init.php';

		if(empty($_SESSION['pid'])) {
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
            $error_message[] = 'You are not logged in.\nLog in to view notifications.';
			$error_code[] = '1512005';
		exit('{"success":0,"errors":[{"message":"' . $error_message[0] . '","error_code":' . $error_code[0] . '}],"code":"400"}');
        }

$sql_find_user_newstutorial = 'SELECT * FROM settings_tutorial WHERE settings_tutorial.pid = "'.$_SESSION['pid'].'" AND settings_tutorial.my_news = "1"';
$result_find_user_newstutorial = mysqli_query($mysql, $sql_find_user_newstutorial);

$sql_find_user_fmtutorial = 'SELECT * FROM settings_tutorial WHERE settings_tutorial.pid = "'.$_SESSION['pid'].'" AND settings_tutorial.friend_messages = "1"';
$result_find_user_fmtutorial = mysqli_query($mysql, $sql_find_user_fmtutorial);

if(strval(mysqli_num_rows($result_find_user_newstutorial)) >= 1) {
            $error_message[] = 'You have already accomplished this.';
			$error_code[] = '1512015';	
}
if(strval(mysqli_num_rows($result_find_user_fmtutorial)) >= 1) {
            $error_message[] = 'You have already accomplished this.';
			$error_code[] = '1512015';	
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
	if(isset($_POST['tutorial_name']) && $_POST['tutorial_name'] == 'my_news') {
    $sql_update = 'INSERT INTO settings_tutorial (pid, my_news) VALUES ("'.$_SESSION['pid'].'", "1")';
    }
	if(isset($_POST['tutorial_name']) && $_POST['tutorial_name'] == 'messages') {
    $sql_update = 'INSERT INTO settings_tutorial (pid, friend_messages) VALUES ("'.$_SESSION['pid'].'", "1")';
    }
	
	if(isset($sql_update)) {
	    $result = mysqli_query($mysql, $sql_update);
        if(!$result)
        {
            //MySQL error; print jsON response.
			http_response_code(400);  
			header('Content-Type: application/json; charset=utf-8');
			
			// Enable in debug
			#print $sql_update;
			#print "\n\n";			
			
			print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"500"}';
			print "\n";
		}
		else { 
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}'; 
}	
		
	}
	}
	
	