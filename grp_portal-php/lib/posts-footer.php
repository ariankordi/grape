<div id="report-violation-page"
     class="add-report-page window-page none"
     data-modal-types="report report-violation"
     data-is-template="1">
  <div class="window">
    <h1 class="window-title">Report Violation to Miiverse Administrators</h1>
    <form method="post" action="/<?php print $template_post_type_uri.'/'.$template_post_end_uri; ?>/violations">
      <div class="window-body"><div class="window-body-inner message">
<?php        #<p class="description">
            #You are about to report a post with content which violates the Miiverse Code of #Conduct. This report will be sent to Nintendo&#39;s Miiverse administrators and not to #the creator of the post.        </p>
			?>
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
                data-community-id="" data-url-id="<?php print $template_post_end_uri; ?>" data-track-label="default" data-title-id="" data-track-action="openReportModal" data-track-category="reportViolation">
      </div>
    </form>
  </div>
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
<?php
if($template_post_type_uri == 'posts') {
if(strlen($row_post['screenshot']) > 3) {
print '<option value="screenshot-profile-post" data-action="/posts/'.$template_post_end_uri.'/screenshot_set_profile_post">Set Screenshot as Favorite Post</option>'; } }
?>
              <option value="spoiler"<?php print $template_has_post_spoiler; ?> data-action="<?php print '/'; print $template_post_type_uri; print '/'; print $template_post_end_uri; ?>/set_spoiler">Set as Spoiler</option>
              <option value="delete" data-action="<?php print '/'; print $template_post_type_uri; print '/'; print $template_post_end_uri; ?>/delete" data-track-label="default" data-track-action="deletePost" data-track-category="post">
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
	</div>