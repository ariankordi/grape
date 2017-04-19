<?php
$warning_page_grp = true;
include_once '../../../grplib-php/init.php';
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display form/warning.
http_response_code(403);
$pagetitle = 'Warning';
printHeader();

print $GLOBALS['div_body_head'];
print '<div class="window-page">
  <div class="window message-window with-button">
    <h1 class="window-title">'.$pagetitle.'</h1>
    <div class="window-body"><div class="window-body-inner message">
      <p>Account is in read-only mode.<br>';
	  if(isset($row_current_peopleban)) {
	  print 'A restriction is placed and set to expire '.$row_current_peopleban['expires_at'].'.'; } else {
	  print 'A restriction is not present. '; }
	  print '</p>
    </div></div>
    <div class="window-bottom-buttons single-button">
      <form action="/warning/readonly" method="post">
        <input type="hidden" name="location" value="'.(!empty($_GET['location']) ? htmlspecialchars(urldecode($_GET['location'])) : '/').'">
        <input type="submit" class="button" value="Close"/>
      </form>
    </div>
  </div>
</div>';
print $GLOBALS['div_body_head_end'];
printFooter(); } else {
# Posted, now redirect.
header('Set-Cookie: readonly_displayed=1; path=/');
header('Content-Type: application/json; charset=utf-8');
print '{"success":1'.(!empty($_POST['location']) ? ',"location":"'.htmlspecialchars(urldecode($_POST['location'])).'"' : '').'}';
}
?>