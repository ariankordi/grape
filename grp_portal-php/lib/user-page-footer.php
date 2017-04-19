<?php if(isset($has_can_friend_request) && $has_can_friend_request = true) {?>
<div id="breakup-confirm-page" class="breakup-confirm-page window-page none" data-modal-types="confirm-relationship confirm-breakup" data-is-template="1">
  <div class="window user-window">
    <h1 class="window-title">Remove from Friends</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="user-container">
        <span class="icon-container"><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="icon"></span>
        <p class="nickname">
          <span class="screen-name"></span>
          <span class="id-name"></span>
        </p>
      </div>
      <div class="message">
        <p>Remove this user from your friends? You will be removed from this user's list as well.</p>
      </div>
    </div></div>
    <div class="window-bottom-buttons">
      <input type="button" class="cancel-button button" value="Back" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button button" value="Yes" data-action="/users/breakup.json" data-done-msgid="olv.portal.breakup_succeeded_to">
    </div>
  </div>
</div>
<div id="received-request-confirm-page" class="friend-request-confirm-page window-page none" data-modal-types="confirm-relationship confirm-received-request" data-is-template="1">
  <div class="window user-window">
    <h1 class="window-title">Friend Request</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="user-container">
        <span class="icon-container"><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="icon"></span>
        <p class="nickname">
          <span class="screen-name"></span>
          <span class="id-name"></span>
        </p>
      </div>
      <div class="message"><p class="message-inner"></p></div>
    </div></div>
    <div class="window-bottom-buttons">
      <input type="button" class="cancel-button button" value="Reject" data-action="/users/friend_request.delete.json" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button button" value="Accept" data-action="/users/friend_request.accept.json" data-done-msgid="olv.portal.friend_request.successed_with" data-track-category="friendRequest" data-track-action="acceptFriendRequest">
    </div>
  </div>
</div>
<?php } ?>
<div id="unfollow-confirm-page" class="unfollow-confirm-page window-page none" data-modal-types="confirm-relationship confirm-unfollow" data-is-template="1">
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
</div><?php
if(isset($has_can_friend_request) && $has_can_friend_request = true) {
if(isset($has_friends_relations) && $has_friends_relations = true) {
	?> <div id="received-request-confirm-page" class="friend-request-confirm-page window-page none" data-modal-types="confirm-relationship confirm-received-request" data-is-template="1">
  <div class="window user-window">
    <h1 class="window-title">Friend Request</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="user-container">
        <span class="icon-container"><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="icon"></span>
        <p class="nickname">
          <span class="screen-name"></span>
          <span class="id-name"></span>
        </p>
      </div>
      <div class="message"><p class="message-inner"></p></div>
    </div></div>
    <div class="window-bottom-buttons">
      <input type="button" class="cancel-button button" value="Reject" data-action="/users/friend_request.delete.json" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button button" value="Accept" data-action="/users/friend_request.accept.json" data-done-msgid="olv.portal.friend_request.successed_with" data-track-category="friendRequest" data-track-action="acceptFriendRequest">
    </div>
  </div>
</div> <?php
} print '
<div id="friend-request-post-page" class="window-page none" data-modal-types="post-friend-request preview-body">
  <div class="window">
    <h1 class="window-title">Send Friend Request to '.htmlspecialchars($row_userpage_user['screen_name']).'</h1>
    <form method="post" action="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/friend_request.create.json">
      <div class="window-body"><div class="window-body-inner">
        <p class="request-user">Friend Request:
          <span class="icon-container"><img src="'.$user_page_info_mii_face_output.'" class="user-icon"></span>
          <span class="screen-name">'.htmlspecialchars($row_userpage_user['screen_name']).'</span>
        </p>
          <input maxlength="255" name="body" class="textarea" placeholder="Write a message here." value="">
      </div></div>
      <div class="window-bottom-buttons">
        <input type="button" class="button olv-modal-close-button" value="Cancel" data-sound="SE_WAVE_CANCEL">
        <input type="button" class="post-button button" value="Send" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="sendFriendRequest" data-track-category="friendRequest">
      </div>
    </form>
  </div>
</div>'; }
?>
<div id="sent-request-confirm-page" class="cancel-request-confirm-page window-page none" data-modal-types="confirm-relationship confirm-sent-request" data-is-template="1">
  <div class="window user-window">
    <h1 class="window-title">Check Friend Request</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="user-container">
        <span class="icon-container"><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="icon"></span>
        <p class="nickname">
          <span class="screen-name"></span>
          <span class="id-name"></span>
        </p>
      </div>
      <div class="message">
        <div class="request-message">
          <p class="message-inner"></p>
          <p class="timestamp"></p>
        </div>
        <p>Cancel your friend request to this user?</p>
      </div>
    </div></div>
    <div class="window-bottom-buttons">
      <input type="button" class="cancel-button button" value="Back" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button button" value="Cancel Request" data-action="/users/friend_request.cancel.json" data-done-msgid="olv.portal.cancel_request_succeeded_to">
    </div>
  </div>
</div>