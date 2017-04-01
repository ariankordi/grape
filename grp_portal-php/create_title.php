<?php
//Create title
include 'lib/sql-connect.php';

	if((strval($_SESSION['user_privilege']) <= 3) | (strval($_SESSION['user_status']) > 2)) {
	#The user can't create a title, redirect them.
    header('Location: http://' . $_SERVER['HTTP_HOST'] .'/communities', true, 302);
    header('X-Nintendo-Level: ' . $_SESSION['user_privilege'] . '');
    header('X-Nintendo-PID: ' . $_SESSION['pid'] . '');	 
    header('X-Nintendo-User-Status: ' . $_SESSION['user_status'] . '');	
    header('X-Nintendo-Login: ' . $_SESSION['signed_in'] . '');
	}
else
{
	#The user can create a title.
	if($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		#Display form.
$pagetitle = "Grape::Admin";
$act_template_subheader = 'Create Title';
$has_header_js = 'no';
$act_back_location = '/communities';
		$act_content = '
<div class="num7">
  <h2>Title Form</h2>
  <p>Create a title and a community in that title here.</p><br>
<form method="post" id="act_form">Name (255 characters): <input class="textbox" name="title_name" minlength="10" maxlength="255" type="text"><br>Unique ID (optional, decimal): <input class="textbox" name="title_unique_id" maxlength="255" type="text"><br>Icon: (Square icon, PNG/JPG): <input class="textbox" name="title_icon" maxlength="255" type="text"><br>
<h2>Community Form</h2><br>Name (255 characters): <input class="textbox" name="community.name" minlength="10" maxlength="255" type="text"><br>
Description (2200 characters): <textarea type="text" name="community.description" maxlength="2200" class="textarea"></textarea><br>Icon: (Square icon, PNG/JPG): <input class="textbox" name="community.icon" maxlength="255" type="text"><br>Banner: (1280x180, PNG/JPG): <input class="textbox" name="community.banner" maxlength="255" type="text"><br>3DS banner: (400x168, PNG/JPG): <input class="textbox" name="community.banner_3ds" maxlength="255" type="text"><br>
 		<input type="submit" value="Create" class="btn_001">

 	 
</form>    </div>';
        include 'lib/act_template.php';
	}
	else
	{
		$pidgen = mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.communities JOIN grape.titles on titles.created_at')).'' + 586437432;
		$pidgen2 = mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.communities JOIN grape.titles on titles.created_at')).'' + 586437432 + 1;
		//the form has been posted, so save it
		$sql_title = 'INSERT INTO titles(olive_title_id, olive_community_id, icon, name, platform_id, platform_type)
		   VALUES('."83955116433$pidgen".',
		          '."83955116433$pidgen2".',
				  "' . mysqli_real_escape_string($link, $_POST['title_icon']) . '",
				  "' . mysqli_real_escape_string($link, $_POST['title_name']) . '",
				  "' . (empty($_POST['title_platform_id']) ? '' : mysqli_real_escape_string($link, $_POST['title_platform_id'])) . '",
				  "' . (empty($_POST['title_platform_type']) ? NULL : mysqli_real_escape_string($link, $_POST['title_platform_type'])) . '")';
		$result_title = mysqli_query($link, $sql_title);
		if(!$result_title)
		{
			//something went wrong, display the error
			print "The SQL was: \n\n".$sql_title." And the error was: \n".mysqli_errno($link).", ".mysqli_error($link)."";
		}
		else
		{
			print 'success';
		}
	}
}

?>
