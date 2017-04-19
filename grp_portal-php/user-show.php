<?php
require_once '../grplib-php/init.php';
if(empty($_GET['pid'])) { $_GET['pid'] = ''; }
$user_show_search_pid = mysqli_query($mysql, 'SELECT * FROM people WHERE people.pid = "'.mysqli_real_escape_string($mysql, $_GET['pid']).'"');
if(mysqli_num_rows($user_show_search_pid) == 0 || $_GET['pid'] == '') {

(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Error');
require_once 'lib/htm.php';
printHeader(false);
printMenu();
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
print '
</div>
';
print $GLOBALS['div_body_head_end'];
printFooter();

}
else {
# Redir to profile
header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/users/'.htmlspecialchars(mysqli_fetch_assoc($user_show_search_pid)['user_id']), true, 302);
}

