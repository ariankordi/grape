<?php
//This page cannot be displayed page.
$pagetitle = 'Error';
$body_id = 'main';

if (empty($error_code_http)) {
	$error_code_http = '400';
}
if ($error_code_http == 404){
  $http_error_message = "404 Not Found"; 
}
if ($error_code_http == 400){
  $http_error_message = "400 Bad Request"; 
}
if ($error_code_http == 401){
  $http_error_message = "401 Unauthorized"; 
}
if ($error_code_http == 403){
  $http_error_message = "403 Forbidden"; 
}
else {
  $http_error_message = "400 Bad Request";
}

http_response_code($code = $error_code_http);

include 'sql-connect.php';
include 'header.php';
include 'user-menu.php';


print $div_body_head;
print '
<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="' . $error_code_http . '">';
$no_content_message = ( 'The screen could not be displayed.' );
include 'no-content-window.php';

include 'footer.php';

?>