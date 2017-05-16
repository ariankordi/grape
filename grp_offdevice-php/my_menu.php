<?php
require_once '../grplib-php/init.php';

require_once 'lib/htm.php';
if(empty($_SESSION['pid'])) {
noLogin(); grpfinish($mysql); exit(); }

$pagetitle = 'User Menu';
printHeader('old'); printMenu('old');
print '<div id="main-body">
<h2 class="headline">User Menu</h2><div class="list my-menu-list">

	<a href="/users/'.htmlspecialchars($_SESSION['user_id']).'" id="my-menu-profile" class="scroll big-button">Profile</a>    
    <a href="/settings/account" id="my-menu-settings-profile" class="scroll big-button">Account Settings</a>
    <a href="/act/edit" id="my-menu-settings-profile" class="scroll big-button">Edit Account</a>
	<form action="/act/logout" method="get" id="my-menu-logout" class="symbol">
	<input type="hidden" name="location" value="/">
    <input type="submit" value="Log Out">
   </form></div>

</div>';
printFooter('old');
grpfinish($mysql);