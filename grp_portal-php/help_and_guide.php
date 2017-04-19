<?php
//Guest menu screen
$pagetitle = 'Manual';
$body_id = 'main';
    
	require_once '../grplib-php/init.php';
	require_once 'lib/htm.php';
printHeader(false);
	printMenu();
	
	print $GLOBALS['div_body_head'];
	print '
<header id="header">
  
  <h1 id="page-title" class="">';
  print $pagetitle;
  print '</h1>

</header>


<div class="body-content" id="config-other-menu">
  <div>
    <a href="/help/" id="help-button" class="button">Manual</a>
    <a href="/faq/" id="guide-button" class="button">FAQ</a>
  </div>
</div>
    </div>';