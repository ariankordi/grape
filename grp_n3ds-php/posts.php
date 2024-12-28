<?php
require_once '../grplib-php/init.php';
require_once '../grplib-php/community-helper.php';
require_once 'lib/htm.php';
$search_post = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.(isset($_GET['id']) ? $mysql->real_escape_string($_GET['id']) : 'a').'"');
if(isset($_GET['mode']) && $_GET['mode'] == 'empathies') {
    if($_SERVER['REQUEST_METHOD'] != 'POST') {
        include_once '404.php';
    }
    require_once '../grplib-php/miitoo.php';
    miitooAdd('posts'); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'empathies_delete') {
    if($_SERVER['REQUEST_METHOD'] != 'POST') {
        include_once '404.php';
    }
    require_once '../grplib-php/miitoo.php';
    miitooDelete('posts'); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'replies'){
    dserror(1210401, "Not implemented.");
}
printHeader();
// assume fetching post data
if(empty($_GET['id'])) { dserror(1210400, "Bad request."); }
$search_post = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.(isset($_GET['id']) ? $mysql->real_escape_string($_GET['id']) : 'a').'"');
require_once '../grplib-php/user-helper.php';
$me = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION["pid"].'"')->fetch_assoc();
if(!$me){
    dserror(1210404, "Your profile does not exist. This is an unrecoverable error. LOL!");
}
if(!$search_post || $search_post->num_rows == 0) {
    dserror(1210404, "This post does not exist, or a server-side error has occurred.");
}
$num_replies = prepared('SELECT COUNT(id) AS num FROM replies WHERE replies.reply_to_id = ?', [$_GET['id']])->fetch_assoc()['num'];
$replies = prepared('SELECT * FROM replies WHERE replies.reply_to_id = ? ORDER BY created_at LIMIT '.($num_replies > 19 ? ($num_replies - 20) : 120), [$_GET['id']]);
$post = $search_post->fetch_assoc();
$empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'"')->num_rows;
$canmiitoo = miitooCan($_SESSION['pid'], $post['id'], 'posts'); 
$my_empathy_added = ($mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'" LIMIT 1')->num_rows == 1 ? true : false);
$empathies_data = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'"');
$search_community = $mysql->query('SELECT * FROM communities WHERE communities.olive_community_id = "'.$mysql->real_escape_string($post['community_id']).'" LIMIT 1');
if(!$search_community) {
    dserror(1210500, "Server-side error.");
} else if($search_community->num_rows == 0) {
    dserror(1210404, "The specified community does not exist.");
}
$community = $search_community->fetch_assoc();
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$post["pid"].'"')->fetch_assoc();
$mii = getMii($user, $post['feeling_id']);
$admin_del = $post['is_hidden'] == '1' && $post['hidden_resp'] == 0;
$deleted = $post['is_hidden'] == '1';
?>
<div id="body" class="post-permalink" data-region-id="2">
      			<div id="header">
				<div id="header-body">
					<h1 id="page-title"><span><?=$user["screen_name"]?>'s post</span></h1>
				</div>
			</div>
			<div id="post-permalink-content">
				<div id="header-meta">
					<p class="community-container">
						<a href="/titles/<?=$community["olive_title_id"]?>/<?=$community["olive_community_id"]?>" class="community" data-pjax="1">
							<span class="community-container-inner">
								<img src="<?=$community["icon"]?>" class="community-icon" width="14" height="14"><?=$community["name"]?>
							</span>
						</a>
					</p>
				</div>
				<div id="post-permalink-body" class="post scroll post-subtype-default  ">
					<a href="/users/<?=$user["user_id"]?>" data-pjax="1" class="user-icon-container scroll-focus ">
						<img src="<?=$mii["output"]?>" class="user-icon" width="32" height="32">
					</a>
					<header>
						<div class="header-inner">
														<p class="user-name"><a href="/users/<?=$user["user_id"]?>" data-pjax="1"><?=$user["screen_name"]?></a></p>
							<p class="timestamp-container">
								<a class="timestamp"><?=humanTiming(strtotime($post['created_at']))?></a>
															</p>
						</div>
					</header>
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
                        <?php if(!empty($post["screenshot"])){ ?> <a href="<?=htmlspecialchars($post["screenshot"])?>" class="screenshot-container still-image"><img src="<?=htmlspecialchars($post["screenshot"])?>" height="96"></a><?php } ?>
                        </div>
                <?php } else { ?>
                                            <p class="post-content-text"><?=htmlspecialchars($post["body"])?></p>
                        <?php if(!empty($post["screenshot"])){ ?> <a href="<?=htmlspecialchars($post["screenshot"])?>" class="screenshot-container still-image"><img src="<?=htmlspecialchars($post["screenshot"])?>" height="96"></a><?php } ?>
                                                                            </div>
                                                                            <?php } } ?>
					<div class="post-meta">
													<div class="report-buttons-content">
								<a href="/posts/<?=$post["id"]?>/violations.create" role="button" class="report-button" data-pjax="1">Report Violation</a>
							</div>
												<div class="expression">
							<button type="button" class="symbol submit empathy-button <?=(isset($my_empathy_added) && $my_empathy_added == true ? ' empathy-added' : '').(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '')?>" data-feeling="<?=($mii['feeling'] ? $mii['feeling'] : 'normal')?>" data-action="/posts/<?=$post["id"]?>/empathies" data-other-empathy-count="<?=$empathies?>" <?=(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '')?>><span class="empathy-button-text"><?=(isset($my_empathy_added) && $my_empathy_added == true ? $mii['miitoo_delete'] : (!empty($mii['miitoo']) ? $mii['miitoo'] : 'Yeah!'))?></span></button>
						</div>
					</div>
				</div>
				<div id="empathy-content" class="post-permalink-feeling" style="">
                <?php if($empathies != 0){ ?>
																							<p class="post-permalink-feeling-text"><?=$empathies?> <?=($empathies == 1 ? "person" : "people")?> gave this a yeah.</p>
																<div class="post-permalink-feeling-icon-container">
                                <?php while($empathy = $empathies_data->fetch_assoc()) { 
                                    global $mysql;
                                    $users = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$empathy["pid"].'"');
                                    if($users->num_rows == 0){
                                        goto skipdaempathy;
                                    }
                                    $user = $users->fetch_assoc();
                                    $mii = getMii($user, $post['feeling_id']);
                                    ?>
										<a href="/users/<?=$user["user_id"]?>" data-pjax="1" class="post-permalink-feeling-icon ">
								<img src="<?=$mii["output"]?>" class="user-icon" width="32" height="32">
							</a>
                                <?php skipdaempathy: } ?>
											</div>
                <?php } ?>
				</div>
			</div>
			<div class="body-content">
				<div id="post-permalink-comments">
					<ul class="post-permalink-reply list reply-list" data-parent-post-id="<?=$post["id"]?>">
					<?php
					$get_replies = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$post['id'].'" ORDER BY replies.created_at');
					if(!$get_replies || $get_replies->num_rows == 0) {
						goto skipreply;
					}
					while($post = $get_replies->fetch_assoc()) { 
						$admin_del = $post['is_hidden'] == '1' && $post['hidden_resp'] == 0;
						$deleted = $post['is_hidden'] == '1';
						$search_people = $mysql->query('SELECT * FROM people WHERE pid = "'.$post["pid"].'"');
					if(!$search_people) {
					goto skipreply; }
					elseif($search_people->num_rows == 0) {
					goto skipreply; }
					$person = $search_people->fetch_assoc();
					$empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'"')->num_rows;
					$canmiitoo = miitooCan($_SESSION['pid'], $post['id'], 'posts'); 
					$my_empathy_added = ($mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'" LIMIT 1')->num_rows == 1 ? true : false);
					$mii = getMii($person, $post["feeling_id"]);
					?>
					<li id="reply-<?=$post["id"]?>" class="test-reply scroll other  ">
								<a href="/users/<?=$person["user_id"]?>" data-pjax="1" class="user-icon-container scroll-focus ">
									<img src="<?=$mii["output"]?>" class="user-icon" width="32" height="32">
								</a>
								<div class="reply-body">
									<header>
										<div class="header-inner">
											<p class="user-name"><a href="/users/<?=$person["user_id"]?>" data-pjax="1"><?=$person["screen_name"]?></a></p>
											<p class="timestamp-container">
												<?=humanTiming(strtotime($post["created_at"]))?>
																							</p>
										</div>
									</header>
									<?php if($admin_del){ 
                        $canmiitoo = false;
                        ?>
                        <p class="reply-content-text">
                        <p class="deleted-message">
        Deleted by administrator.<br>
        Post ID: <?=$post['id']?>
      </p></p></div>
                    <?php } else { ?>
                        <p class="reply-content-text">
                            <a href="/replies/<?=$post["id"]?>" class="to-permalink-button" data-pjax="1" tabindex="0"><?=htmlspecialchars($post["body"])?></a>
                        <?php if(!empty($post["screenshot"])){ ?>
							<a href="/replies/<?=$post["id"]?>" class="screenshot-container still-image"><img src="<?=htmlspecialchars($post["screenshot"])?>" height="96"></a>
						<?php } ?>
						</p>
						</div>
                                                                            <?php } ?>
																																				<div class="reply-meta">
										<button type="button" class="symbol submit empathy-button reply" data-feeling="normal" data-action="/replies/<?=$post["id"]?>/empathies">
											<span class="empathy-button-text">Yeah!</span>
										</button>
										<span class="empathy symbol">
											<span class="symbol-label">Yeahs</span>
											<span class="empathy-count"><?=$empathies?></span>
										</span>
																					<div class="report-buttons-content">
												<a href="" role="button" class="button report-button" data-pjax="1">Report Violation</a>
											</div>
								</div>
							</li>
							<?php }
					skipreply: ?>
					</ul>
					<!--/posts/<?=$post["id"]?>/reply--><a class="post-button reply-button test-reply-button disabled" data-pjax="1"><span class="symbol">Comment</span></a>
				</div>
			</div>
    </div>