<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if(empty($_SESSION['pid'])) {
plainErr(403, '403 Forbidden');  exit();
}

$pagetitle = 'Blocked Users';

$search_blacklists = prepared('SELECT target, type FROM blacklist WHERE blacklist.source = ?', [$_SESSION['pid'] ]);
printHeader(false); printMenu();
require_once 'lib/htmTemplates.php';
print $GLOBALS['div_body_head'];
print '
<header id="header">
  
  <h1 id="page-title" class="">'.$pagetitle.'</h1>

</header>

<div class="body-content" id="block-list-page">
';
if(!$search_blacklists || $search_blacklists->num_rows == 0) {
noContentWindow('You haven\'t blocked any users.<br>
    If you block users, you\'ll no longer receive friend requests from them and you\'ll be less likely to encounter them in games.');
} else {
print '  <ul class="list-content-with-icon-and-text">
';
require_once '../grplib-php/user-helper.php';
while($entries = $search_blacklists->fetch_assoc()) {
$person = $mysql->query('SELECT pid, user_id, screen_name, mii_hash, face, official_user FROM people WHERE people.pid = "'.$entries['target'].'"')->fetch_assoc();
$mii = getMii($person, false);
print '<li class="scroll">
      <a href="/users/'.htmlspecialchars($person['user_id']).'" class="scroll-focus icon-container'.($mii['official'] == 1 ? ' official-user' : '').'" data-pjax="#body"><img src="'.$mii['output'].'" class="icon"></a>
      <div>
        <button type="button" class="button unblock-button" data-modal-open="#unblock-confirm-page" data-user-id="'.htmlspecialchars($person['user_id']).'"'.($mii['official'] ? ' data-is-identified="1"' : '').' data-screen-name="'.htmlspecialchars($person['screen_name']).'" data-mii-face-url="'.$mii['output'].'" data-action="/users/'.htmlspecialchars($person['user_id']).'/blacklist.delete.json">Unblock</button>
        <button type="button" class="button block-button none" data-modal-open="#block-confirm-page" data-user-id="'.htmlspecialchars($person['user_id']).'"'.($mii['official'] ? ' data-is-identified="1"' : '').' data-screen-name="'.htmlspecialchars($person['screen_name']).'" data-mii-face-url="'.$mii['output'].'" data-action="/users/'.htmlspecialchars($person['user_id']).'/blacklist.create.json">Block</button>
      </div>
      <div class="body">
        <p class="title">
          <span class="nick-name">'.htmlspecialchars($person['screen_name']).'</span>
          <span class="id-name">'.htmlspecialchars($person['user_id']).'</span>
        </p>
        <p class="text">'.getProfileComment($person, false).'</p>
      </div>
    </li>';
}
print '  </ul>';
}
print '
</div>
';
blockConfirm();
unblockConfirm();
print $GLOBALS['div_body_head_end'];