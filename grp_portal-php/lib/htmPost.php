<?php

function displayempathy($row, $post, $my, $last) {
global $mysql;
if($my == true) { global $my_empathy_added; }
$empathies_person = $mysql->query('SELECT * FROM people WHERE people.pid = "'.($my == true ? $_SESSION['pid'] : $row['pid']).'" LIMIT 1')->fetch_assoc();
$empathies_person_mii = getMii($empathies_person, $post['feeling_id']);
print '<a href="/users/'.htmlspecialchars($empathies_person['user_id']).'" data-pjax="#body"  class="post-permalink-feeling-icon'.($empathies_person_mii['official'] == true ? ' official-user' : null).($my == true ? ' visitor' : '').($last == true ? ' extra' : '').'"'.($my == true ? 'style="'.($my_empathy_added == false ? 'display: none;' : '').'"' : '').'><img src="'.$empathies_person_mii['output'].'" class="user-icon"></a>';

}

function displayReply($ogpost, $reply) {
if($reply['hidden_resp'] != '1') {
global $mysql;
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$reply['pid'].'" LIMIT 1')->fetch_assoc();
$mii = getMii($user, $reply['feeling_id']);
$empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'"');

if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $reply['pid'])) {
return null; }

global $pref_id;
if(!isset($pref_id)) { 
$pref_id = 0; 
	}
$show_spoiler = (!empty($_SESSION['pid']) && $_SESSION['pid'] == $reply['pid']) || $pref_id == 1;
print '		   <li id="reply-'.$reply['id'].'" class="test-fresh-reply scroll'.($ogpost['pid'] == $reply['pid'] ? ' my' : ' other').($reply['is_spoiler'] == 1 ? ($show_spoiler ? '' : ' hidden') : '').($mii['official'] == true ? ' official-user' : null).(!empty($reply['screenshot']) ? ' with-image' : '').'">
  <a href="/users/'.htmlspecialchars($user['user_id']).'" data-pjax="#body" class="scroll-focus user-icon-container'.($mii['official'] == true ? ' official-user' : null).'"><img src="'.$mii['output'].'" class="user-icon"></a>
  ';
if($reply['is_hidden'] == 1 && $reply['hidden_resp'] == 0) {
	print '<div class="reply-content">
        <p class="deleted-message">Deleted by administrator.</p>
        <p class="deleted-message">Comment ID: '.getPostID($reply['id']).'</p>
';
if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $reply['pid']) {
print '<p class="reply-content-text">'.htmlspecialchars($reply['body']).'</p>';
}
print '
      </div>
	  </li>';	} else { 
print '
  <div class="reply-content">
    <header>
      <span class="user-name">'.htmlspecialchars($user['screen_name']).'</span>
      <span class="timestamp">'.humanTiming(strtotime($reply['created_at'])).'</span>
	  <span class="spoiler-status'.($reply['is_spoiler'] == 1 ? ' spoiler' : null).'">Spoilers</span>
		';
		
	# Can the user give an empathy? Used later.
		
print '    </header>


<p class="reply-content-text">'.htmlspecialchars($reply['body']).'</p>
';
	 if(!empty($reply['screenshot'])) {
	print '<a href="#" role="button" class="title-capture-container capture-container" data-modal-open="#capture-page" data-large-capture-url="'.htmlspecialchars($reply['screenshot']).'"><img src="'.htmlspecialchars($reply['screenshot']).'" class="title-capture"></a>'; }
if($reply['is_spoiler'] == 1) {
if(!$show_spoiler) {
print '<div class="hidden-content">
        <p>This comment contains spoilers.</p>
        <div><a href="#" class="hidden-content-button">View Post</a></div>
	</div>'; } }

if(!empty($_SESSION['pid'])) { $myempathy = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$reply['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'"')->num_rows == 1; $canmiitoo = miitooCan($_SESSION['pid'], $reply['id'], 'replies'); }

	print '


    <div class="reply-meta">
      <button type="button"'.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : null).'
              class="submit miitoo-button'.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').(!empty($_SESSION['pid']) && $myempathy ? ' empathy-added' : null).'"
              data-feeling="'.$mii['feeling'].'"
              data-action="/replies/'.$reply['id'].'/empathies"
              data-sound="SE_WAVE_MII_'.(!empty($_SESSION['pid']) && $myempathy ? 'CANCEL' : 'ADD').'"
              data-url-id="'.$reply['id'].'" data-track-label="reply" data-track-action="yeah" data-track-category="empathy"
      >'.(isset($myempathy) && $myempathy == true ? $mii['miitoo_delete'] : (!empty($mii['miitoo']) ? $mii['miitoo'] : 'Yeah!')).'</button>
      <a href="/replies/'.$reply['id'].'" class="to-permalink-button" data-pjax="#body">
        <span class="feeling">'.$empathies->num_rows.'</span>
      </a>
    </div>
  </div>
</li>';
}
} }

function postsFooter($type, $post) {
print '
<div id="report-violation-page"
     class="add-report-page window-page none"
     data-modal-types="report report-violation"
     data-is-template="1">
  <div class="window">
    <h1 class="window-title">Report Violation to Miiverse Administrators</h1>
    <form method="post" action="/'."{$type}/{$post['id']}".'/violations">
      <div class="window-body"><div class="window-body-inner message">
<!--       <p class="description">
            You are about to report a post with content which violates the Miiverse Code of Conduct. This report will be sent to Nintendo&#39;s Miiverse administrators and not to the creator of the post.        </p> 
!-->
        <div class="select-content">
          <span class="select-button-label">Violation Type: </span>
          <div class="select-button"><span class="select-button-content">Please make a selection.</span>
            <select name="type" class="cannot-report-spoiler">
              <option value="" selected>Please make a selection.</option>
              <option value="1" data-track-action="Personal">Personal Information</option>
              <option value="2" data-track-action="Violent">Violent Content</option>
              <option value="3" data-track-action="Inappropriate">Inappropriate/Harmful</option>
              <option value="4" data-track-action="Hateful">Hateful/Bullying</option>
              <option value="6" data-track-action="Advertising">Advertising</option>
              <option value="5" data-track-action="Sexual">Sexually Explicit</option>
              <option value="7" data-track-action="Other">Other</option>
            </select>
            <select name="type" class="can-report-spoiler">
              <option value="" selected>Please make a selection.</option>
              <option value="spoiler" data-body-required="1" data-track-action="Spoiler">Spoiler</option>
              <option value="1" data-track-action="Personal">Personal Information</option>
              <option value="2" data-track-action="Violent">Violent Content</option>
              <option value="3" data-track-action="Inappropriate">Inappropriate/Harmful</option>
              <option value="4" data-track-action="Hateful">Hateful/Bullying</option>
              <option value="6" data-track-action="Advertising">Advertising</option>
              <option value="5" data-track-action="Sexual">Sexually Explicit</option>
              <option value="7" data-track-action="Other">Other</option>
            </select>
          </div>
        </div>
        <textarea name="body" class="textarea" maxlength="100" placeholder="Enter a reason for the report."></textarea>
        <p class="post-id"></p>
      </div></div>
      <div class="window-bottom-buttons">
        <input type="button" class="olv-modal-close-button button" value="Cancel" data-sound="SE_WAVE_CANCEL">
        <input type="submit" class="post-button button" value="Submit Report"
                data-community-id="" data-url-id="'.$post['id'].'" data-track-label="default" data-title-id="" data-track-action="openReportModal" data-track-category="reportViolation">
      </div>
    </form>
  </div>
</div>
<div id="edit-post-page" class="window-page none" data-modal-types="edit-post">
  <div class="window">
    <h1 class="window-title">Edit Post</h1>
    <form method="post" class="edit-post-form" action="">
      <div class="window-body"><div class="window-body-inner message">
        <div class="select-content">
          <span class="select-button-label">Select an action:</span>
          <div class="select-button"><span class="select-button-content">Select an option.</span>
            <select name="edit-type">
                <option value="" data-action="" selected="">Select an option.</option>
';
if($type == 'posts' && !empty($post['screenshot'])) {
print '<option value="screenshot-profile-post" data-action="/posts/'.$post['id'].'/screenshot.set_profile_post">Set Screenshot as Favorite Post</option>'; }
print '
              <option value="spoiler"'.($post['is_spoiler'] == 1 ? ' disabled' : null).' data-action="/'."{$type}/{$post['id']}".'.set_spoiler">Set as Spoiler</option>
              <option value="delete" data-action="/'."{$type}/{$post['id']}".'.delete" data-track-label="default" data-track-action="deletePost" data-track-category="post">
                Delete
              </option>
            </select>
          </div>
        </div>
      </div></div>
      <div class="window-bottom-buttons">
        <input type="button" class="olv-modal-close-button button" value="Cancel" data-sound="SE_WAVE_CANCEL">
        <input type="submit" class="post-button button" value="Submit">
      </div>
    </form>
  </div>
</div>
<div id="disabled-report-violation-notice" class="window-page none">
  <div class="window">
    <h1 class="window-title">Report Violation</h1>
    <div class="window-body">
      <div class="window-body-inner">
        <p>You cannot report posts made automatically by a software title.</p>
      </div>
    </div>
    <div class="window-bottom-buttons single-button">
      <input class="olv-modal-close-button button" type="button" value="Close" data-sound="SE_WAVE_CANCEL">
    </div>
	</div>
	</div>';
}