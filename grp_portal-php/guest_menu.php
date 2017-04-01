<?php
//Guest menu screen
$pagetitle = 'Guest Menu';
$body_id = 'main';
    
	include 'lib/sql-connect.php';
	
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/my_menu', true, 302);
}
else {
    include 'lib/header.php';
	include 'lib/user-menu.php';
print $div_body_head;
print '
<header id="header">
  
  <h1 id="page-title" class="">';
  print $pagetitle;
  print '
  </h1>

</header>


<div class="body-content" id="my-menu">
  <div>
    
    
    <a href="/act/create" id="my-menu-miiverse-config" class="scroll big-button">Create Account</a>
	<a href="/act/login" id="my-menu-miiverse-config" class="scroll big-button">Log In</a>
<a href="/help_and_guide" data-pjax="#body" id="my-menu-help-and-guide" class="scroll big-button">Manual</a>
  </div>
</div>
    ';
    print $div_body_head_end;
	include 'lib/footer.php';
	}
	?>