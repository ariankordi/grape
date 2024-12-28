<?php
require_once '../grplib-php/init.php';
require_once '../grplib-php/community-helper.php';
require_once 'lib/htm.php';
if(isset($_GET["moder"])){
    $parampack = explode("\\", base64_decode($_SERVER["HTTP_X_NINTENDO_PARAMPACK"]));
    $_GET["title_id"] = $parampack[2];
    $search_title = $mysql->prepare('SELECT * FROM titles WHERE titles.olive_title_id_usa = ? OR titles.olive_title_id_eur = ? OR titles.olive_title_id_jpn = ? AND titles.hidden != 1 LIMIT 1');
    $search_title->bind_param("sss", $_GET["title_id"], $_GET["title_id"], $_GET["title_id"]);
    $search_title->execute();
    $title_res = $search_title->get_result();
    if($title_res->num_rows == 0){
        header("Location: /communities");
        exit();
    }
    $title = $title_res->fetch_assoc();
    header("Location: /titles/".$title["olive_title_id"]."/".$title["olive_community_id"]);
    exit();
}
$search_title = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$mysql->real_escape_string($_GET['title_id'] ?? 'a').'" AND titles.hidden != 1 LIMIT 1');

if(!$search_title) {
dserror(1210500, "Server-side error."); }
elseif($search_title->num_rows == 0) {
dserror(1210404, "This title does not exist."); }

# Community listing.
if(isset($_GET['community_id'], $_GET['title_id'])) {

if(!empty($_GET['mode']) && $_GET['mode'] != 'hot' && $_GET['mode'] != 'reply') {
include '404.php';  exit(); }

$search_community = $mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$mysql->real_escape_string($_GET['title_id']).'" AND communities.olive_community_id = "'.$mysql->real_escape_string($_GET['community_id']).'" LIMIT 1');
}
if(!$search_community) {
    dserror(1210500, "Server-side error."); }
    elseif($search_community->num_rows == 0) {
    dserror(1210404, "This community does not exist."); }
    $community = $search_community->fetch_assoc();
    if($community['type'] == 5) {
    include '404.php';  exit(); }
$title = $search_title->fetch_assoc();
$pagetitle = htmlspecialchars($title["name"]).($dev_server ? ' ('.CONFIG_SRV_ENV.')' : '');
printHeader();
?>
<div id="body" class="community-post-list" data-region-id="2">
            <?php if(!empty($community["banner_3ds"])){ ?>
      			<div class="title-header with-header-banner">
									<div class="header-banner-container post-subtype-default-container">
						<img src="<?=$community["banner_3ds"]?>" height="168" width="400">
					</div>
				<h1 class="info-content">
					<span class="icon-container"><img src="<?=$community["icon"]?>" class="icon" width="48" height="48"></span>
					<span class="title-container">
						<span class="title"><?=htmlspecialchars($community["name"])?></span>
					</span>
				</h1>
                <?php } else { ?>
                    <div class="title-header with-header-banner">
                    <div class="header-banner-container post-subtype-default-container">
						<img src="#" height="168" width="400">
					</div>
				<h1 class="info-content">
					<span class="icon-container"><img src="<?=$community["icon"]?>" class="icon" width="48" height="48"></span>
					<span class="title-container">
						<span class="title"><?=htmlspecialchars($community["name"])?></span>
					</span>
				</h1>
                <?php } ?>
									<span class="platform-tag platform-tag-wiiu"></span>
							</div>
			<div id="header-meta" class="header-meta-with-description">
				<a href="#" class="favorite-button symbol button top-right-button checked" data-action-favorite="/titles/oaE8rzZL7PO2q514nvNp/qWNg64rE2AQ2y0VwKbJd/favorite.json" data-action-unfavorite="/titles/oaE8rzZL7PO2q514nvNp/qWNg64rE2AQ2y0VwKbJd/unfavorite.json"></a>
				<a href="#" class="button symbol setting-button" data-sound="SE_OLV_OK_SUB" data-pjax="1"></a>
			</div>
							<div class="community-info">
					<p class="text"><?=htmlspecialchars($community["description"])?></p>
				</div>
						<div class="tab2">
				<a id="tab-header-post" href="/titles/<?=$community["olive_title_id"]?>/<?=$community["olive_community_id"]?>" data-pjax="1" class="<?=($_GET["mode"] == "hot" ? 'symbol' : 'selected')?>"><span class="symbol new-posts">All</span></a>
				<a id="tab-header-hot-post" href="/titles/<?=$community["olive_title_id"]?>/<?=$community["olive_community_id"]?>/hot" class="<?=($_GET["mode"] == "hot" ? 'selected symbol' : 'symbol')?>" data-pjax="1"><span class="name">Popular</span></a>
			</div>
			<div class="body-content tab3-content">
				<div id="list-content" class="post-list main-content">
					<div class="tab-body">
						<div class="post-buttons-content"><!--with-memo-button-->
							<a href="/titles/<?=$community["olive_title_id"]?>/<?=$community["olive_community_id"]?>/post" class="post-button js-post-button" data-pjax="1"><span class="symbol">Post</span></a>
							<!--<a href="/titles/<?=$community["olive_title_id"]?>/<?=$community["olive_community_id"]?>/post_memo" class="memo-button" data-pjax="1"><span class="symbol">Saved Posts</span></a>-->
						</div>
						<div class="post-list list" data-olv-community-id="<?=$community["olive_community_id"]?>">
                        <?php if($_GET["mode"] == "hot"){
                            $get_posts = searchPopular($community, $_GET['date'] ?? date('Y-m-d'), 50, $_GET['offset'] ?? 0, true);
                        } else {
                            $get_posts = $mysql->query('SELECT * FROM posts WHERE posts.community_id = "'.$community['community_id'].'" AND is_hidden = 0 ORDER BY posts.created_at DESC LIMIT 50'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$mysql->real_escape_string($_GET['offset']) : null));	
                        }		
    if(!$get_posts || $get_posts->num_rows == 0) {
	exit("<center>There are no posts to display.</center>"); }
while($post = $get_posts->fetch_assoc()) { 
    $admin_del = $post['is_hidden'] == '1' && $post['hidden_resp'] == 0;
    $deleted = $post['is_hidden'] == '1';
    $search_people = $mysql->query('SELECT * FROM people WHERE pid = "'.$post["pid"].'"');

if(!$search_people) {
goto skippost; }
elseif($search_people->num_rows == 0) {
goto skippost; }
$person = $search_people->fetch_assoc();
$empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'"')->num_rows;
$replies = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$post['id'].'" ORDER BY replies.created_at')->num_rows;
$canmiitoo = miitooCan($_SESSION['pid'], $post['id'], 'posts'); 
$my_empathy_added = ($mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'" LIMIT 1')->num_rows == 1 ? true : false);
$mii = getMii($person, $post["feeling_id"]);
    ?>
					<div id="post-<?=$post["id"]?>" class="post scroll post-subtype-default  " data-href="/posts/<?=$post["id"]?>">
                <div class="body">
            <a href="/users/<?=$person["user_id"]?>" data-pjax="1" class="user-icon-container scroll-focus ">
                <img src="<?=$mii["output"]?>" class="user-icon" width="32" height="32">
            </a>
            <div class="post-container">
                <div class="user-container">
                    <p class="user-name"><a href="#" data-pjax="1"><?=$person["screen_name"]?></a></p>
                    <p class="timestamp-container">
                        <span class="timestamp"><?=humanTiming(strtotime($post["created_at"]))?></span>
                                            </p>
                </div>
                <div class="post-content">
                    <?php if($admin_del){ 
                        $canmiitoo = false;
                        ?>
                        <p class="post-content-text">
                        <p class="deleted-message">
        Deleted by administrator.<br>
        Post ID: <?=$post['id']?>
      </p></p></div>
                    <?php } else { 
                        if($post["_post_type"] !== "body"){ ?> 
                        <p class="post-content-memo">
                            <a href="/posts/<?=$post["id"]?>" class="to-permalink-button" data-pjax="1" tabindex="0">
                                <img src="<?=htmlspecialchars($post["body"])?>">
                            </a>
                        </p>
                        <?php if(!empty($post["screenshot"])){ ?> <a href="/posts/<?=$post["id"]?>" class="screenshot-container still-image"><img src="<?=htmlspecialchars($post["screenshot"])?>" height="96"></a><?php } ?>
                        </div>
                <?php } else { ?>
                                            <p class="post-content-text">
                            <a href="/posts/<?=$post["id"]?>" class="to-permalink-button" data-pjax="1" tabindex="0"><?=htmlspecialchars($post["body"])?></a>
                        </p>
                        <?php if(!empty($post["screenshot"])){ ?> <a href="/posts/<?=$post["id"]?>" class="screenshot-container still-image"><img src="<?=htmlspecialchars($post["screenshot"])?>" height="96"></a><?php } ?>
                                                                            </div>
                                                                            <?php } } ?>
                <div class="post-meta">
                    <button type="button" class="symbol submit empathy-button  <?=(isset($my_empathy_added) && $my_empathy_added == true ? ' empathy-added' : '').(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '')?>" data-feeling="<?=($mii['feeling'] ? $mii['feeling'] : 'normal')?>" data-action="/posts/<?=$post["id"]?>/empathies" <?=(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '')?>>
                        <span class="empathy-button-text"><?=(isset($my_empathy_added) && $my_empathy_added == true ? $mii['miitoo_delete'] : (!empty($mii['miitoo']) ? $mii['miitoo'] : 'Yeah!'))?></span>
                    </button>
                    <span class="empathy symbol">
                        <span class="symbol-label">Yeahs</span>
                        <span class="empathy-count"><?=$empathies?></span>
                    </span>
                    <a href="/posts/<?=$post["id"]?>" data-pjax="1" tabindex="0">
                        <span class="reply symbol">
                            <span class="symbol-label">Comments</span>
                            <span class="reply-count"><?=$replies?></span>
                        </span>
                    </a>
                </div>
            </div>
            <!--
                                    <div id="recent-reply-AJGaEx12zX6jMdOwgPQy" class="recent-reply">
                <div class="body">
                                        <a href="/users/TheRealJonny87" data-pjax="1" class="user-icon-container  scroll-focus">
                        <img src="http://mii-images.account.nintendo.net/2h8ld8ajt7j4g_normal_face.png" class="user-icon" width="22" height="22">
                    </a>
                    <div class="header-inner">
                        <p class="timestamp-container">
                            <span class="timestamp">7 hours ago</span>
                        </p>
                        <p class="user-name">
                            <a href="/users/TheRealJonny87" data-pjax="1">TheRealJonny87</a>
                        </p>
                    </div>
                    <div class="recent-reply-content">
                        <p class="recent-reply-content-text">
                            <a href="/posts/AJGaEx12zX6jMdOwgPQy" class="to-permalink-button" data-pjax="#body">
                                                                <span>find mii</span>
                                                                                            </a>
                        </p>
                    </div>
                </div>
            </div>
-->
            </div>
    </div>
<?php skippost: } ?>
</div>