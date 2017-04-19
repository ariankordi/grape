<?php

function displayempathy($row, $post, $my) {
global $mysql;
if($my == true) { global $my_empathy_added; }
$empathies_person = $mysql->query('SELECT * FROM people WHERE people.pid = "'.($my == true ? $_SESSION['pid'] : $row['pid']).'" LIMIT 1')->fetch_assoc();
$empathies_person_mii = getMii($empathies_person, $post['feeling_id']);
print '<a href="/users/'.htmlspecialchars($empathies_person['user_id']).'" class="post-permalink-feeling-icon'.($my == true ? ' visitor' : '').'"'.($my == true ? 'style="'.($my_empathy_added == false ? 'display: none;' : '').'"' : '').'><img src="'.$empathies_person_mii['output'].'" class="user-icon"></a>';
}

function displayreply($ogpost, $reply) {
if($reply['hidden_resp'] != '1') {
global $mysql;
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$reply['pid'].'" LIMIT 1')->fetch_assoc();
$mii = getMii($user, $reply['feeling_id']);
print '<li id="reply-'.$reply['id'].'" data-href="/replies/'.$reply['id'].'" class="'.($reply['pid'] == $ogpost['pid'] ? 'my' : 'other').''.($reply['is_spoiler'] == 1 ? (!empty($_SESSION['pid']) && $_SESSION['pid'] == $reply['pid'] ? '' : ' hidden') : '').' trigger">
  <a href="/users/'.htmlspecialchars($user['user_id']).'" class="icon-container'.($mii['official'] ? ' official-user' : '').'"><img src="'.$mii['output'].'" class="icon"></a>
  <div class="body">
  ';
  if($reply['is_hidden'] == '1' && $reply['hidden_resp'] == '0') {
	  print '<p class="deleted-message">Deleted by administrator.</p>
	  <p class="deleted-message">Comment ID: '.getPostID($reply['id']).'</p>
  '; } else {
print '    <div class="header">
      <p class="user-name"><a href="/users/'.htmlspecialchars($user['user_id']).'">'.htmlspecialchars($user['screen_name']).'</a></p>
      <p class="timestamp-container">
        <span class="spoiler-status'.($reply['is_spoiler'] == 1 ? ' spoiler' : '').'">Spoilers ·</span>
        <a class="timestamp" href="/replies/'.$reply['id'].'">'.humanTiming(strtotime($reply['created_at'])).'</a>      </p>
    </div>

    <p class="reply-content-text">'.htmlspecialchars($reply['body']).'</p>

';
if(!empty($reply['screenshot'])) {
print '
<div class="screenshot-container still-image"><img src="'.htmlspecialchars($reply['screenshot']).'"></div>
'; }

if($reply['is_spoiler'] == 1) {
if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $reply['pid']) { } else {
print '<div class="hidden-content">
      <p>This comment contains spoilers.</p>
      <button type="button" class="hidden-content-button">View Comment</button>
</div>'; } }

$empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'"');
if(!empty($_SESSION['pid'])) { $myempathy = ($mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'"')->num_rows == 1 ? true : false); $canmiitoo = miitooCan($_SESSION['pid'], $reply['id'], 'replies'); }

print '
    <div class="reply-meta">
        <button type="button"'.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').' class="symbol submit empathy-button'.(isset($myempathy) && $myempathy == true ? ' empathy-added' : '').''.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').' reply" data-feeling="'.$mii['feeling'].'" data-action="/replies/'.$reply['id'].'/empathies"><span class="empathy-button-text">'.(isset($myempathy) && $myempathy == true ? 'Unyeah' : (!empty($mii['miitoo']) ? $mii['miitoo'] : 'Yeah!')).'</span></button>
        <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count">'.$empathies->num_rows.'</span></div>
    </div>
	  '; }
  print '</div>
</li>';

} }

function reportTemplate($type) {
print '<div id="report-violation-page" class="dialog none" data-modal-types="report report-violation" data-is-template="1">
<div class="dialog-inner">
  <div class="window">
    <h1 class="window-title">Report Violation</h1>
    <div class="window-body">
      <form method="post" action="">
        
        <p class="select-button-label">Violation Type: </p>
        <select name="type" class="cannot-report-spoiler">
          <option value="" selected="">Make a selection.</option>
          <option value="1">Intrusion of Privacy</option>
          <option value="2">Violence/Physical Harm</option>
          <option value="3">Abusive/Harassing/Bullying</option>
          <option value="4">Hateful/Discriminatory</option>
          <option value="6">Advertising/Spam</option>
          <option value="5">Sexually Explicit</option>
          <option value="7">Other Inappropriate Content</option>
        </select>
        <select name="type" class="can-report-spoiler">
          <option value="" selected="">Make a selection.</option>
          <option value="spoiler" data-body-required="1">Spoilers</option>
          <option value="1">Intrusion of Privacy</option>
          <option value="2">Violence/Physical Harm</option>
          <option value="3">Abusive/Harassing/Bullying</option>
          <option value="4">Hateful/Discriminatory</option>
          <option value="6">Advertising/Spam</option>
          <option value="5">Sexually Explicit</option>
          <option value="7">Other Inappropriate Content</option>
        </select>
        <textarea name="body" class="textarea" maxlength="100" data-placeholder="Enter a reason for the report here."></textarea>
        <p class="post-id">Post ID: </p>
        <div class="form-buttons">
          <input type="button" class="olv-modal-close-button gray-button" value="Cancel">
          <input type="submit" class="post-button black-button" value="Submit Report">
        </div>
      </form>
    </div>
  </div>
</div>
</div>';
}

function editTemplate($type, $post) {
print '<div id="edit-post-page" class="dialog none" data-modal-types="edit-post">
<div class="dialog-inner">
  <div class="window">
    <h1 class="window-title">Edit Post</h1>
    <div class="window-body">
      <form method="post" class="edit-post-form" action="">
        <p class="select-button-label">Select an action:</p>
        <select name="edit-type">
          <option value="" selected="">Select an option.</option>
		  ';
if($type == 'posts' && !empty($post['screenshot'])) {
print '<option value="screenshot-profile-post" data-action="/posts/'.$post['id'].'/screenshot.set_profile_post">Set Screenshot as Favorite Post</option>'; }
		  print '
          <option'.($post['is_spoiler'] == 1 ? ' disabled' : '').' value="spoiler" data-action="/'.$type.'/'.$post['id'].'.set_spoiler">Set as Spoiler</option>
          <option value="delete" data-action="/'.$type.'/'.$post['id'].'.delete">Delete</option>
        </select>
        <div class="form-buttons">
          <input type="button" class="olv-modal-close-button gray-button" value="Cancel">
          <input type="submit" class="post-button black-button" value="Confirm">
        </div>
      </form>
    </div>
  </div>
</div>
</div>';
}