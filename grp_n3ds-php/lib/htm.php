<?php
function printHeader() {
global $pagetitle;
global $bodyClass;
global $mysql;

if(!empty($_SESSION['pid'])) {
$lookup_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
$user_id = htmlspecialchars($lookup_user['user_id']); } else {
$user_id = null;
}


if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
header('X-PJAX-PATH: '.htmlspecialchars($_SERVER['REQUEST_URI'])); } else {

if(strpos($_SERVER['HTTP_USER_AGENT'], 'miiverse') !== false) { $js_file = 'https://rv3ds.rverse.club/js/complete.js'; }
  $js_file = 'https://rv3ds.rverse.club/js/complete.js';

print '<!DOCTYPE html>
<html lang="en">
  <head>
    <script>cave.toolbar_setVisible(true);</script>
    <meta charset="utf-8">
    <title>'.($pagetitle ?? 'grp.n3ds.page_title').'</title>
    <link rel="stylesheet" type="text/css" href="https://rv3ds.rverse.club/css/n3ds.css">
    <script src="'.$js_file.'"></script>
  </head>
  ';
print '<body ';

print 'data-user-id="'.$user_id.'"'.(empty($_SESSION['pid']) ? ' data-is-first-post="1" data-is-first-favorite="1"' : ' data-profile-url="/users/'.$user_id.'"');

  print '>
  ';     
}

print '<div id="body" class="'.($bodyClass ?? null).'">

';
	}

function topHeader($pagetitle) {
print '<div id="header">
  <div id="header-body">
    <h1 id="page-title"><span>'.$pagetitle.'</span></h1>
  </div>
</div>';
}

function truncate($text, $chars) {
$truncate_post_bodyp1 = mb_substr(($text), 0, $chars);
return (mb_strlen($text) >= $chars + 1 ? $truncate_post_bodyp1.'...' : $truncate_post_bodyp1);
}

function printFooter() {
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
print "\n\n    </div>"; } else {
print '

    </div>
  </body>
</html>';
} }

function plainErr($code, $message) {
http_response_code(!empty($code) ? $code : 403);
header('Content-Type: text/plain');
print !empty($message) ? $message."\n" : "403 Forbidden\n";
}

function noLogin() {
plainErr(403, '403 Forbidden');
}
if(isset($_SESSION["pid"])){
$lookup_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
if($lookup_user["status"] == 2 || $lookup_user["status"] == 3){
  $err_msgd = true;
  $err_msg["error_code"] = 9999999;
  $err_msg["message"] = "You are currently banned from rv3.<br><a href='https://n3ds.rverse.club/'>Go to rverse2</a>";
  $noheader = true;
  require("postform-err.php");
  exit();
}
}

function dserror($code, $text){
  $err_msgd = true;
  $err_msg["error_code"] = $code;
  $err_msg["message"] = $text;
  $noheader = true;
  require("postform-err.php");
  exit();
}
