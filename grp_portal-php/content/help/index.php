<?php
//Not implemented page.
$pagetitle = 'Not Implemented';
$body_id = 'main';

include '../../lib/sql-connect.php';
include '../../lib/header.php';
include '../../lib/user-menu.php';

print '<div id="body">
<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="501">';
$no_content_message = ( 'This function is not implemented.' );
include '../../lib/no-content-window.php';

include '../../lib/footer.php';