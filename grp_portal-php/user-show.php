<?php
require_once '../grplib-php/init.php';
if(empty($_GET['pid']) || !is_numeric($_GET['pid'])) {
generalError(404, 'The user could not be found.'); grpfinish($mysql); exit();
}
$get_user = $mysql->query('SELECT user_id FROM people WHERE people.pid = "'.$mysql->real_escape_string($_GET['pid']).'"');
if($get_user->num_rows == 0 || $_GET['pid']) {
generalError(404, 'The user could not be found.'); grpfinish($mysql); exit();
}
else {
# Redir to profile
header('Location: '.$grp_config_default_redir_prot.''.$_SERVER['HTTP_HOST'] .'/users/'.htmlspecialchars($get_user->fetch_assoc()['user_id']), true, 302);
}

