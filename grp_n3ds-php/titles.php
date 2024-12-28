<?php
require_once '../grplib-php/init.php';
$pagetitle = "Communities".($dev_server ? ' ('.CONFIG_SRV_ENV.')' : '');
require_once 'lib/htm.php';
printHeader();
//require_once '../grplib-php/community-helper.php';
if($mysql->query('SELECT * FROM titles LIMIT 1')->num_rows == 0) {
	$err_msgd = true;
$err_msg["error_code"] = 9999999;
$err_msg["message"] = "No communities have been created.";
require_once("postform-err.php");
exit();
}
$get_platformtitles = $mysql->query('SELECT * FROM titles WHERE titles.platform_id IS NOT NULL AND titles.hidden != 1 ORDER BY titles.created_at DESC LIMIT 20');
?>
<script>cave.snd_playBgm("BGM_CAVE_MAIN");</script>
<div id="body" class="community-top platform-3ds" data-region-id="2">
      			<div class="title-header text-header">
				<div class="header-banner-container">
					<div class="header-banner">
						<p>Check out the communities for games that you play or games that you're curious about!</p>
					</div>
				</div>
				<h1 class="info-content">
					<span class="icon-container"></span>
					<span class="title-container">
						<span class="title">Communities</span>
					</span>
				</h1>
			</div>
			<div class="community-top-top-container">
				<span class="top-left-button title-search-button">
					<span class="symbol">Search</span>
					<input data-action="/titles/search" name="query" class="title-search-title-id" minlength="2" maxlength="20" type="text" monospace="on" guide="Search Communities" cave_oninput="$(document.activeElement).trigger('input')" value="">
				</span>
				<a href="/communities/favorites" class="favorites-button top-right-button" data-pjax="1">
					<span class="symbol">Favorite Communities</span>
				</a>
			</div>
			<div class="body-content" id="community-top" data-region="USA">
				<div class="news-label-content">
				</div>
				<div class="community-list">
					<div class="headline with-filter headline-wiiu"> <!-- 3ds -->
						<h2>New Communities</h2>
						<div class="with-filter-right">
							<div class="select-button">
								<label id="community-filter" class="">
									<span class="select-button-content">Filter</span>
									<select name="filter">
										<option value="" selected="">Please make a selection.</option>
										<option value="/communities/categories/3ds_all">All Software</option>
										<option value="/communities/categories/3ds_game">Wii U Games</option>
										<option value="/communities/categories/3ds_virtualconsole">Virtual Console</option>
										<option value="/communities/categories/3ds_other">Others</option>
									</select>
								</label>
							</div>
						</div>
					</div>
											<ul class="list-content-with-icon-and-text arrow-list" id="community-top-content">
					<?php while($platformtitles = $get_platformtitles->fetch_assoc()) {
						if($platformtitles['platform_type'] == '1' && $platformtitles['platform_id'] == '1'){
							$platform_css = "-wiiu";
							$platform = "Wii U Games";
						}
						if($platformtitles['platform_type'] == '2' && $platformtitles['platform_id'] == '2'){
							$platform_css = "-3ds";
							$platform = "3DS Games";
						}
						if($platformtitles['platform_type'] == '3' && $platformtitles['platform_id'] == '3'){
							$platform_css = "";
							$platform = "Virtual Console Games";
						} ?>
								<li id="community-<?=$platformtitles['olive_community_id']?>" class="">
									<span class="icon-container">
										<img src="<?=htmlspecialchars($platformtitles['icon'])?>" class="icon" width="48" height="48">
									</span>
									<a href="/titles/<?=$platformtitles['olive_title_id']?>/<?=$platformtitles['olive_community_id']?>" class="arrow-button scroll" data-pjax="1"></a>
									<div class="body">
										<span class="community-name title"><?=htmlspecialchars($platformtitles['name'])?></span>
																			<span class="platform-tag platform-tag<?=$platform_css?>"></span>
										<span class="text"><?=$platform?></span>
																		</div>
								</li>
					<?php } ?>
														</ul>
					<?php $get_specialtitles = $mysql->query('SELECT * FROM titles WHERE titles.platform_id IS NULL AND titles.hidden != 1 ORDER BY titles.created_at DESC LIMIT 6'); ?>
					<h2 class="headline headline-special">Special</h2>
					<ul class="list-content-with-icon-and-text arrow-list" id="community-top-content">
					<?php while($platformtitles = $get_specialtitles->fetch_assoc()) { ?>
								<li id="community-<?=$platformtitles['olive_community_id']?>" class="">
									<span class="icon-container">
										<img src="<?=htmlspecialchars($platformtitles['icon'])?>" class="icon" width="48" height="48">
									</span>
									<a href="/titles/<?=$platformtitles['olive_title_id']?>/<?=$platformtitles['olive_community_id']?>" class="arrow-button scroll" data-pjax="1"></a>
									<div class="body">
										<span class="community-name title"><?=htmlspecialchars($platformtitles['name'])?></span>
																		</div>
								</li>
					<?php } ?>
														</ul>
														</div>
			</div>
			<div class="community-list-footer">
				<div class="select-button">
					<span class="select-button-content">Wii U Communities</span>
										<label id="view-region-selector" class="">
						<select name="type">
							<option value="" selected="">Please make a selection.</option>
							<option value="/communities/categories/switch">Switch Communities</option>
							<option value="/communities/categories/3ds">3DS Communities</option>
							<option value="/communities/categories/wiiu">Wii U Communities</option>
							<option value="/communities/categories/wii">Wii Communities</option>
							<option value="/communities/categories/ds">DS Communities</option>
						</select>
					</label>				</div>
					<br><a href="https://n3ds.rverse.club/titles/show">rverse2</a>
			</div>
    </div>