<?php
require_once '../grplib-php/init.php';
$pagetitle = "Messages".($dev_server ? ' ('.CONFIG_SRV_ENV.')' : '');
require_once 'lib/htm.php';
require_once '../grplib-php/user-helper.php';
printHeader();
$friends = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION["pid"].'" OR friend_relationships.target = "'.$_SESSION["pid"].'" ORDER BY friend_relationships.updated DESC');
?>
<div id="body" class="messages-page" data-region-id="2">
    <div class="title-header text-header">
        <div class="header-banner-container">
            <div class="header-banner">
                <p>In Messages, you can send messages to friends and view past messages.</p>
            </div>
        </div>
        <h1 class="info-content">
            <span class="icon-container"><img src="/img/message-icon-selected.png" width="28" height="22" style="position:relative;top:20%;left:14%;"></span>
            <span class="title-container">
                <span class="title">Messages</span>
            </span>
        </h1>
    </div>
    <div class="body-content">
        <div class="tab-body">
            <div class="tab2">
				<a id="tab-header" href="/news/my_news" data-pjax="1" class="symbol"><span class="name">Notifications</span></a>
				<a id="tab-header" href="/news/dms" class="selected" data-pjax="1"><span class="name">Messages</span></a>
                <a id="tab-header" href="#" class="symbol" data-pjax="1"><span class="friend symbol">Friends</span></a>
			</div>
            <?php
            if($friends->num_rows == 0) { ?>
            <div id="news-tutorial" class="tutorial-window">
                <p>In Messages, you can send messages to friends and view past messages. If you want to play a game with a friend or get some tips if you're stuck, try sending a message!p>
                <!--<a href="#" class="button tutorial-close-button" data-tutorial-name="nthn">Close</a>-->
            </div>
            <?php } else { 
            ?>
            <ul class="list-content-with-icon-and-text arrow-list" id="news-list-content">
            <?php
            while($row_friends = $friends->fetch_assoc()) {
                $friend = $mysql->query('SELECT * FROM people WHERE people.pid = "'.($row_friends['target'] == $_SESSION["pid"] ? $row_friends['source'] : $row_friends['target']).'"')->fetch_assoc();
                if($friend['pid'] != $_SESSION["pid"]) {
                    $mii = getMii($friend, false);
                    $conversation = $mysql->query('SELECT * FROM conversations WHERE conversations.sender = "'.$_SESSION["pid"].'" AND conversations.recipient = "'.$friend['pid'].'" OR conversations.sender = "'.$friend['pid'].'" AND conversations.recipient = "'.$_SESSION["pid"].'"');
                    if($conversation->num_rows != 0) {
                    $recent_msg = $mysql->query('SELECT * FROM messages WHERE messages.conversation_id = "'.$conversation->fetch_assoc()['conversation_id'].'" ORDER BY messages.created_at DESC LIMIT 1')->fetch_assoc(); }
                    else { $recent_msg = array('has_read'=>1,'pid'=>0); }
                ?>
                <li>
                    <a href="/friend_messages/<?=$friend["user_id"]?>" data-pjax="#body" class="icon-container">
                        <img src="<?=$mii['output']?>" class="icon">
                    </a>
                    <a href="/friend_messages/<?=$friend["user_id"]?>" data-pjax="#body" class="arrow-button scroll"></a>
                    <div class="body">
                        <p class="text">
                            <b class="nick-name"><?=htmlspecialchars($friend["screen_name"])?></b>
                            <span class="id-name"><?=htmlspecialchars($friend["user_id"])?></span><br>
                            <span class="timestamp"><?=($recent_msg["pid"] == $_SESSION["pid"] ? 'You: ' : '')?><?=($recent_msg['has_read'] == 0 ? '<b>' : '')?><?=(empty($recent_msg['conversation_id']) ? 'You haven\'t exchanged messages with this user yet.' : (empty(htmlspecialchars($recent_msg['body'])) ? 'Artwork' : htmlspecialchars($recent_msg['body'])))?><?=($recent_msg['has_read'] == 0 ? '</b>' : '')?></span>
                        </p>
                    </div>
                </li>
            <?php } } } ?>
            </ul>
        </div>
    </div>
</div>