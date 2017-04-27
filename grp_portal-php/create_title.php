<?php
//Create title
require_once '../grplib-php/init.php';

	if((strval($_SESSION['user_privilege']) <= 3) | (strval($_SESSION['user_status']) > 2)) {
	#The user can't create a title, redirect them.
    header('Location: http://'.$_SERVER['HTTP_HOST'] .'/communities', true, 302);
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
        actTemplate($act_template_subheader, $act_back_location, $act_content);
	}
	else
	{
		$pidgen = mysqli_num_rows($mysql->query('SELECT * FROM communities JOIN titles on titles.created_at')).'' + 586437432;
		$pidgen2 = mysqli_num_rows($mysql->query('SELECT * FROM communities JOIN titles on titles.created_at')).'' + 586437432 + 1;
		//the form has been posted, so save it
		$sql_title = 'INSERT INTO titles(olive_title_id, olive_community_id, icon, name, platform_id, platform_type)
		   VALUES('."83955116433$pidgen".',
		          '."83955116433$pidgen2".',
				  "'.$mysql->real_escape_string($_POST['title_icon']).'",
				  "'.$mysql->real_escape_string($_POST['title_name']).'",
				  "'.(empty($_POST['title_platform_id']) ? '' : $mysql->real_escape_string($_POST['title_platform_id'])).'",
				  "'.(empty($_POST['title_platform_type']) ? NULL : $mysql->real_escape_string($_POST['title_platform_type'])).'")';
		$result_title = $mysql->query($sql_title);
		if(!$result_title)
		{
			//something went wrong, display the error
			print "The SQL was: \n\n".$sql_title." And the error was: \n".mysqli_errno($mysql).", ".mysqli_error($mysql)."";
		}
		else
		{
			print 'success';
		}
	}
}


