<?php
//Not implemented page.
$pagetitle = 'Not Implemented';
$body_id = 'main';

include_once '../../lib/sql-connect.php';
include_once '../../lib/header.php';
include_once '../../lib/user-menu.php';

print '<div id="body">
<header id="header">
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
</header>';
print '<div class="body-content track-error" data-track-error="501">';
$no_content_message = ( 'This function is not implemented.' );
include_once '../../lib/no-content-window.php';

include_once '../../lib/footer.php';