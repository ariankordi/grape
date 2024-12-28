<?php
require_once '../grplib-php/init.php';
$pagetitle = "Notifications".($dev_server ? ' ('.CONFIG_SRV_ENV.')' : '');
require_once 'lib/htm.php';
require_once '../grplib-php/user-helper.php';
printHeader();
?>
<div id="body" class="activity" data-region-id="2">
      	<div class="title-header text-header">
		<div class="header-banner-container">
			<div class="header-banner">
				<p>Here you can view posts and more from the users you follow.</p>
			</div>
		</div>
		<h1 class="info-content">
			<span class="icon-container"></span>
			<span class="title-container">
			<span class="title"><span>Activity Feed</span>
		</span></span></h1>
	</div>
	<div class="body-content post-list" id="activity-feed" style="height:220px;">
		<div class="no-content-window content-loading-window">
			<div class="window">
				<p>Don't get your hopes up, I'm not loading anything...</p>
			</div>
		</div>
		<div class="no-content-window content-load-error-window none">
			<div class="window">
				<p>The activity feed could not be loaded. Check your Internet connection, wait a moment, and then try reloading.</p>
				<div class="window-bottom-buttons">
					<a href="/" class="button">Reload</a>
				</div>
			</div>
		</div>
	</div>
    </div>