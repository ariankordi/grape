<?php

function breakupConfirm() { ?>
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
<?php }
function recievedRequest() { ?>
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
<?php }
function unfollowConfirm() { ?>
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
</div>
<?php }
function blockConfirm() { ?>
<div id="block-confirm-page" class="block-confirm-page window-page none" data-modal-types="confirm-relationship confirm-block" data-is-template="1">
  <div class="window user-window">
    <h1 class="window-title">Block</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="user-container">
        <span class="icon-container"><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="icon"></span>
        <p class="nickname">
          <span class="screen-name"></span>
          <span class="id-name"></span>
        </p>
      </div>
      <div class="message">
        <p>Are you sure you want to block this user? You will no longer receive friend requests from this user, and you will be less likely to encounter him or her in games.</p>
      </div>
    </div></div>
    <div class="window-bottom-buttons">
      <input type="button" class="cancel-button button" value="Back" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button button" value="Block" data-done-msgid="olv.portal.blocklist.block_successed_to" data-track-category="block" data-track-action="block">
    </div>
  </div>
</div>
<?php }
function unblockConfirm() { ?>
<div id="unblock-confirm-page" class="unblock-confirm-page window-page none" data-modal-types="confirm-relationship confirm-unblock" data-is-template="1">
  <div class="window user-window">
    <h1 class="window-title">Unblock</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="user-container">
        <span class="icon-container"><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="icon"></span>
        <p class="nickname">
          <span class="screen-name"></span>
          <span class="id-name"></span>
        </p>
      </div>
      <div class="message">
        <p>Remove this user from your blocked-user list?</p>
      </div>
    </div></div>
    <div class="window-bottom-buttons">
      <input type="button" class="cancel-button button" value="Back" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button button" value="Yes" data-done-msgid="olv.portal.blocklist.unblock_successed_to" data-track-category="block" data-track-action="unblock">
    </div>
  </div>
</div>
<?php }
function friendRequestConfirm() { ?>
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
<?php }
function friendRequestPost($user, $mii) {
print '
<div id="friend-request-post-page" class="window-page none" data-modal-types="post-friend-request preview-body">
  <div class="window">
    <h1 class="window-title">Send Friend Request to '.htmlspecialchars($user['screen_name']).'</h1>
    <form method="post" action="/users/'.htmlspecialchars($user['user_id']).'/friend_request.create.json">
      <div class="window-body"><div class="window-body-inner">
        <p class="request-user">Friend Request:
          <span class="icon-container"><img src="'.$mii['output'].'" class="user-icon"></span>
          <span class="screen-name">'.htmlspecialchars($user['screen_name']).'</span>
        </p>
          <input maxlength="255" name="body" class="textarea" placeholder="Write a message here." value="">
      </div></div>
      <div class="window-bottom-buttons">
        <input type="button" class="button olv-modal-close-button" value="Cancel" data-sound="SE_WAVE_CANCEL">
        <input type="button" class="post-button button" value="Send" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="sendFriendRequest" data-track-category="friendRequest">
      </div>
    </form>
  </div>
</div>
';
}
function sentRequestConfirm() { ?>
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
<?php }
// end

function userPageTemplate($user, $mii) {
// combine all into one nice package for a user page
breakupConfirm();
recievedRequest();
unfollowConfirm();
blockConfirm();
#unblockConfirm();
friendRequestConfirm();
friendRequestPost($user, $mii);
sentRequestConfirm();
}

function titleSettingsPages($title, $id) {
switch($id) {
case 0:
$text = 'Hide Spoilers';
break;
case 1:
$text = 'Show All';
break;
case 2:
$text = 'Hide All Posts';
break;
}
print '
<div id="title-settings-page" class="window-page none" data-modal-types="title-settings">
  <div class="window">
    <h1 class="window-title">Community Post Display Setting</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="title-container">
        <img src="'.getIcon($title).'" class="title-icon">
        <p class="title-name">'.htmlspecialchars($title['name']).'</p>
      </div>
      <ul class="settings-list">
<li data-name="viewable_post" class="scroll">
  <p class="settings-label">Which posts do you want to see for this community?</p>
  <a class="settings-button scroll-focus"
     href="#" data-modal-open=".settings-page[data-name=\'viewable_post\']"
     >'.$text.'
  </a>
  
</li>

      </ul>
    </div></div>
    <div class="window-bottom-buttons single-button">
      <input type="button" class="button close-button" value="Close">
    </div>
  </div>
</div>

<div class="settings-page window-page none"
     
     data-modal-types="select-settings"
     data-action="/settings/titles/'.$title['olive_title_id'].'"
     data-name="viewable_post">
  <div class="window">
    <h1 class="window-title">Community Post Display Setting</h1>
    <div class="window-body"><div class="window-body-inner message">
        Which posts do you want to see for this community?
    </div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus'.($id == 1 ? ' selected' : '').'"
              value="1" data-sound="SE_WAVE_TOGGLE_CHECK"
              >Show All</button>
      <button class="checkbox-button post-button scroll-focus'.($id == 0 ? ' selected' : '').'"
              value="0" data-sound="SE_WAVE_TOGGLE_CHECK"
              >Hide Spoilers</button>
      <button class="checkbox-button post-button scroll-focus'.($id == 2 ? ' selected' : '').'"
              value="2" data-sound="SE_WAVE_TOGGLE_CHECK"
              >Hide All Posts</button>
    </div>
  </div>
</div>



';
}