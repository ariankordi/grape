<?php
//This page cannot be displayed page.
$pagetitle = 'Error';
$body_id = 'main';

http_response_code(404);

include 'sql-connect.php';
include 'header.php';
include 'user-menu.php';


print $div_body_head;
print '
<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The screen could not be displayed.' );
include 'no-content-window.php';

include 'footer.php';

?>