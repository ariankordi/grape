<?php
require_once '../grplib-php/init.php';

$pagetitle = 'Guest Menu';
require_once 'lib/htm.php';
printHeader(false);
	printMenu();
print $GLOBALS['div_body_head'];
print '
<header id="header">
  
  <h1 id="page-title" class="">'.$pagetitle.'</h1>

</header>


<div class="body-content" id="my-menu">
  <div>
    
    
    <a href="/act/create" id="my-menu-miiverse-config" class="scroll big-button">Create Account</a>
	<a href="/act/login" id="my-menu-miiverse-config" class="scroll big-button">Log In</a>
	
  </div>
</div>
    ';
    print $GLOBALS['div_body_head_end'];
	printFooter();