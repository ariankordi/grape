<?php
require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php';

if(!empty($_SESSION['pid'])) {
require_once '../grplib-php/account-helper.php';
setLoginVars($_SESSION, false);
unset($_COOKIE['grp_identity']);
setcookie('grp_identity', false, time() - 4, '/');
defaultRedir(false, false);
}

else {
printErr(1022597, 'You are not logged in.', '/act/logout'.(!empty($_GET['location']) ? '?location='.htmlspecialchars($_GET['location']) : ''));
}
 exit();