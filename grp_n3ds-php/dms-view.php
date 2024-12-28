<?php
require_once '../grplib-php/init.php';
$pagetitle = "Messages".($dev_server ? ' ('.CONFIG_SRV_ENV.')' : '');
require_once 'lib/htm.php';
require_once '../grplib-php/user-helper.php';
$bodyClass = "post-permalink";
printHeader();
$me = $mysql->query("SELECT * FROM `people` WHERE `pid` = '".$mysql->real_escape_string($_SESSION["pid"])."'")->fetch_assoc();
$miime = getMii($me, false);
if(!empty($_GET['user_id']) || !empty($_GET['conversation_id'])) {
    if(empty($_GET['conversation_id'])) {
    $search_person = $mysql->query('SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"');
    if($search_person->num_rows == 0) { require '404.php';  exit(); } else { $person = $search_person->fetch_assoc(); $mii = getMii($person, false); }
    $relationship = getFriendRelationship($_SESSION["pid"], $person['pid']);
    if(!$relationship) { plainErr(403, '403 Forbidden');  exit(); }
    if(!empty($relationship['conversation_id'])) {
    $conversation_id = $relationship['conversation_id']; }
    else {
    $create_conversation = $mysql->query('INSERT INTO conversations(sender, recipient) VALUES("'.$_SESSION["pid"].'", "'.$person['pid'].'")');
    if(!$create_conversation) {
    plainErr(500, '500 Internal Server Error');  exit(); }
    $conversation_id = $mysql->query('SELECT * FROM conversations WHERE conversations.conversation_id = "'.$mysql->insert_id.'" LIMIT 1')->fetch_assoc()['conversation_id'];
    }
    } else {
    $mode = 1;
    if($me['privilege'] <= 6) { plainErr(403, '403 Forbidden');  exit(); }
    $search_conversation = $mysql->query('SELECT * FROM conversations WHERE conversations.conversation_id = "'.$mysql->real_escape_string($_GET['conversation_id']).'" LIMIT 1');
    if(!$search_conversation || $search_conversation->num_rows == 0) { plainErr(404, '404 Not Found');  exit(); }
    $conversation_id = $search_conversation->fetch_assoc()['conversation_id'];
    }
} else {
    header("Location: /news/dms");
}
$mysql->query('UPDATE messages SET has_read = "1" WHERE messages.conversation_id = "'.$conversation_id.'" AND messages.pid != "'.$_SESSION["pid"].'"');
?>
    <div id="header">
		<div id="header-body">
			<h1 id="page-title"><span>Conversation with <?=htmlspecialchars($person["screen_name"])?> (<?=htmlspecialchars($person["user_id"])?>)</span></h1>
		</div>
	</div>
    <div id="body-content">
        <div class="post-buttons-content">
			<a href="/titles/-1/<?=$_GET["user_id"]?>/post" class="post-button message-button js-post-button" data-pjax="1"><span class="symbol">Send Message</span></a>
            <a href="/friend_messages/<?=$_GET["user_id"]?>" class="post-button message-button js-post-button" data-pjax="1">Reload</a>
		</div>
        <div id="post-permalink-comments">
        <div style="position:relative;left:38px;"><ul class="post-permalink-reply list reply-list" data-parent-post-id="0">
        <?php $messages = $mysql->query('SELECT * FROM messages WHERE messages.conversation_id = "'.$conversation_id.'" ORDER BY messages.created_at DESC LIMIT 20'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$_GET['offset'] : ''));
        while($message = $messages->fetch_assoc()){
        if($message["pid"] == $_SESSION["pid"]){
            $persond = $me;
            $miid = $miime;
        } else {
            $persond = $person;
            $miid = $mii;
        } ?>
        <li id="reply-<?=$message["id"]?>" class="test-reply scroll other  ">
			<a href="/users/<?=$persond["user_id"]?>" data-pjax="1" class="user-icon-container scroll-focus ">
									<img src="<?=$miid["output"]?>" class="user-icon" width="32" height="32">
								</a>
								<div class="reply-body">
									<header>
										<div class="header-inner">
											<p class="user-name"><a href="/users/<?=$persond["user_id"]?>" data-pjax="1"><?=$persond["screen_name"]?></a></p>
											<p class="timestamp-container">
												<?=humanTiming(strtotime($message["created_at"]))?>
																							</p>
										</div>
									</header>
                        <p class="reply-content-text">
                            <a class="to-permalink-button" data-pjax="1" tabindex="0"><?=htmlspecialchars($message["body"])?></a>
                        <?php if(!empty($message["screenshot"])){ ?>
							<a class="screenshot-container still-image"><img src="<?=htmlspecialchars($message["screenshot"])?>" height="96"></a>
						<?php } ?>
						</p>
                        <div class="reply-meta">
						    <div class="report-buttons-content">
								<a href="" role="button" class="button report-button" data-pjax="1">Report Violation</a>
							</div>
						</div>
                        </div>
                        </li>
                        <?php } ?>
                </ul>
            </div></div>
    </div>
</div>
<script>//setTimeout(function(){cave.toolbar_setMode(0);},100);</script>
<?php printFooter(); ?>