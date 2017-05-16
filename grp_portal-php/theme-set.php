<?php
if(!empty($_GET['theme'])) {

if($_GET['theme'] == 'blueberry' || $_GET['theme'] == 'grape' || $_GET['theme'] == 'cherry' || $_GET['theme'] == 'orange') {
# Set cookie theme to 'grape'
setcookie( "grp_theme", htmlspecialchars($_GET['theme']), strtotime('+10 days') );
}

else {
setcookie( "grp_theme", 'olive', strtotime('+10 days') );
}
    header("Location: ".(!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/communities'));
}
else {
include_once '404.php';
exit(); }

