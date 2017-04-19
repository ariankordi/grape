<?php

function unfollowConfirm() {
print '<div id="unfollow-confirm-page" class="unfollow-confirm-page window-page none" data-modal-types="confirm-relationship confirm-unfollow" data-is-template="1">
  <div class="window user-window">
    <h1 class="window-title">Unfollow</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="user-container">
        <span class="icon-container"><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="icon"></span>
        <p class="nickname">
          <span class="screen-name"></span>
          <span class="id-name"></span>
        </p>
      </div>
      <div class="message">
        <p>Stop following this user?</p>
      </div>
    </div></div>
    <div class="window-bottom-buttons">
      <input type="button" class="cancel-button button" value="Back" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button button" value="Yes" data-done-msgid="olv.portal.unfollow_succeeded_to">
    </div>
  </div>
</div>';
}