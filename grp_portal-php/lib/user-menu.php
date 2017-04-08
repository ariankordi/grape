<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    if ($_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
	print null;
	}
}
	else {
	if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
	print '<menu id="global-menu">
      <li id="global-menu-mymenu"><a href="/users/'.$_SESSION['user_id'].'" data-pjax="#body" data-sound="SE_WAVE_MENU"><span class="mii-icon"><img src="';
      $guformii = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM people WHERE pid = "'.$_SESSION['pid'].'" LIMIT 1'));
	  if($guformii['mii_hash']) {
print 'https://mii-secure.cdn.nintendo.net/' . $guformii['mii_hash'] . '_normal_face.png'; 
}
else {
if($guformii['user_face']) {
print htmlspecialchars($guformii['user_face']);
} else {
print '/img/mii/img_unknown_MiiIcon.png'; }
}
	  print '
	  " alt="User Menu"></span><span>User Page</span></a></li>
      <li id="global-menu-feed"><a href="/" data-pjax="#body" data-sound="SE_WAVE_MENU">Activity Feed</a></li>
      <li id="global-menu-community"><a href="/communities" data-pjax="#body" data-sound="SE_WAVE_MENU">Communities</a></li>
      <li id="global-menu-message"><a href="/friend_messages" data-pjax="#body" data-sound="SE_WAVE_MENU">Messages<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-news"><a href="/news/my_news" data-pjax="#body" data-sound="SE_WAVE_MENU">Notifications<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-exit"><a href="#" role="button" data-sound="SE_WAVE_EXIT">Close</a></li>
      <li id="global-menu-back" class="none"><a href="#" role="button" class="accesskey-B" data-sound="SE_WAVE_BACK">Back</a></li>
    </menu>
';
	}
	else {
	print '
    <menu id="global-menu">
      <li id="global-menu-mymenu"><a href="/guest_menu" data-pjax="#body" data-sound="SE_WAVE_MENU"><span class="mii-icon"><img src="/img/mii/img_unknown_MiiIcon.png" alt="Guest Menu"></span><span>Guest Menu</span></a></li>
      <li id="global-menu-feed"><a href="javascript:alert(\'An account is required to use this feature. Create one in Guest Menu.\');" data-pjax="#body" data-sound="SE_WAVE_MENU">Activity Feed</a></li>
      <li id="global-menu-community"><a href="/communities" data-pjax="#body" data-sound="SE_WAVE_MENU">Communities</a></li>
      <li id="global-menu-message"><a href="javascript:alert(\'An account is required to use this feature. Create one in Guest Menu.\');" data-pjax="#body" data-sound="SE_WAVE_MENU">Messages<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-news"><a href="javascript:alert(\'An account is required to use this feature. Create one in Guest Menu.\');" data-pjax="#body" data-sound="SE_WAVE_MENU">Notifications<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-exit"><a href="#" role="button" data-sound="SE_WAVE_EXIT">Close</a></li>
      <li id="global-menu-back" class="none"><a href="#" role="button" class="accesskey-B" data-sound="SE_WAVE_BACK">Back</a></li>
    </menu>
';
	}
	}
?>