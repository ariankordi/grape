<?php
require_once '../grplib-php/init.php';
$pagetitle = "Notifications".($dev_server ? ' ('.CONFIG_SRV_ENV.')' : '');
require_once 'lib/htm.php';
require_once '../grplib-php/user-helper.php';
printHeader();
$search_user = $mysql->prepare("SELECT * FROM `people` WHERE `user_id` = ?");
$search_user->bind_param("s", $_GET["user_id"]);
$search_user->execute();
if($search_user->error){
    dserror(1210500, "An error has occurred.");
}
$search_user_res = $search_user->get_result();
if($search_user_res->num_rows == 0){
	dserror(1210404, "This user does not exist.");
}
$user = $search_user_res->fetch_assoc();
$num_posts = $mysql->query('SELECT COUNT(id) FROM posts WHERE posts.pid = "'.$user['pid'].'" AND posts.is_hidden = "0"')->fetch_assoc()['COUNT(id)'];
//$num_friends = $mysql->query('SELECT COUNT(relationship_id) FROM friend_relationships WHERE friend_relationships.source = "'.$user['pid'].'" OR friend_relationships.target = "'.$user['pid'].'"')->fetch_assoc()['COUNT(relationship_id)'];
$num_following = $mysql->query('SELECT COUNT(relationship_id) FROM relationships WHERE relationships.source = "'.$user['pid'].'" AND relationships.is_me2me != "1"')->fetch_assoc()['COUNT(relationship_id)'];
$num_followers = $mysql->query('SELECT COUNT(relationship_id) FROM relationships WHERE relationships.target = "'.$user['pid'].'" AND relationships.is_me2me != "1"')->fetch_assoc()['COUNT(relationship_id)'];
$usermii = getMii($user, false);
?>
<div id="body" class="user-page" data-region-id="2">
      			<div id="user-content-container">
				<div id="user-content" class="user-page">
					<div id="header">
						<div id="header-body">
							<h1 id="page-title"><span><?=$user["screen_name"]?>'s Profile</span></h1>
						</div>
					</div>
					<div class="icon-name-container">
						<div class="user-icon-container">
							<img src="<?=$usermii["output"]?>" class="user-icon" width="32" height="32">
						</div>
						<p class="title">
							<span class="nick-name"><?=$user["screen_name"]?></span>
							<span class="id-name"><?=$user["user_id"]?></span>
						</p>
					</div>
				</div>
				<div id="header-meta">
					<div class="button-with-option">
													<div id="user-violator-blacklist" class="symbol">
								<select>
									<option value="" selected="">Please make a selection.</option>
									<option value="/users/<?=$user["user_id"]?>/violators.create" class="option-violators-create">Report</option>
									<option value="/users/<?=$user["user_id"]?>/blacklist.confirm" data-screen-name="tmkm165">Block</option>
								</select>
							</div>
							<div class="toggle-follow-button">
								<button type="button" data-action="/users/<?=$user["user_id"]?>/follow.json" class="follow-button ">Follow</button>
								<button type="button" data-action="/users/<?=$user["user_id"]?>/unfollow.json" class="unfollow-button none" data-screen-name="<?=$user["screen_name"]?>">Unfollow</button>
							</div>
											</div>
				</div>
			</div>
			<div id="nav-menu" class="nav-3">
				<a href="/users/<?=$user['user_id']?>/posts" data-pjax="1" class="user-posts-count ">
					<span class="number"><?=$num_posts?></span>
					<span class="name">Posts</span>
				</a>
				<!--<a href="/users/<?=$user['user_id']?>/friends" data-pjax="1" class="user-friends-count ">
					<span class="number">? / 100</span>
					<span class="name">Friends</span>
				</a>-->
				<a href="/users/<?=$user['user_id']?>/following" data-pjax="1" class="user-followings-count ">
					<span class="number"><?=$num_following?></span>
					<span class="name">Following</span>
				</a>
				<a href="/users/<?=$user['user_id']?>/followers" data-pjax="1" class="user-followers-count ">
					<span class="number"><?=$num_followers?></span>
					<span class="name">Followers</span>
				</a>
			</div>
			<div class="body-content">
				<div class="profile-content">
					<div class="favorite-communities">
						<h2 class="headline headline-special">Favorite Communities</h2>
						<div class="communities-content">
																							<span class="favorite-community">
									<a href="/titles/1/1/new" data-pjax="1">
										<span class="icon-container"><img class="icon" src="#"></span>
									</a></span
																					<a href="/users/<?=$user['user_id']?>/favorites" data-pjax="1" class="arrow-button"></a>
						</div>
					</div>
				</div>
			</div>
    </div>