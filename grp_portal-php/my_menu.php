<?php
require_once '../grplib-php/init.php';

if(empty($_SESSION['pid'])) {
header('Location: '.LOCATION.'/guest_menu', true, 302);
}
else {
require_once 'lib/htm.php';
$pagetitle = 'User Menu';
printHeader(false);
	printMenu();
print $GLOBALS['div_body_head'];
print '
<header id="header">
  
  <h1 id="page-title" class="">'.$pagetitle.'</h1>

</header>


<div class="body-content" id="my-menu">
  <div>
    <a href="/settings/account" data-pjax="#body" id="my-menu-miiverse-config" class="scroll big-button">Account Settings</a>
	<a href="/my_blacklist" data-pjax="#body" id="my-menu-blocklist" class="scroll big-button">Blocked Users</a>
    <a href="/act/edit" id="my-menu-miiverse-config" class="scroll big-button">Edit Account</a>
	<a href="/act/logout" id="my-menu-miiverse-config" class="scroll big-button">Log Out</a>
  </div>
</div>
      ';
	  print $GLOBALS['div_body_head_end'];
	printFooter();
	}
	