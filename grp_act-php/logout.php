<?php
require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php';

if(!empty($_SESSION['pid'])) {
require_once '../grplib-php/account-helper.php';
session_destroy();
unset($_COOKIE['grp_identity']);
session_destroy();
exit('Logged out.<script>setTimeout(function(){window.location.href="/";},1000);');
}

else {
printErr(1022597, 'You are not logged in.', '/act/logout'.(!empty($_GET['location']) ? '?location='.htmlspecialchars($_GET['location']) : ''));
}
 exit();