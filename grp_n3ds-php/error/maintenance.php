<?php
$pagetitle = "Maintenance".($dev_server ? ' ('.CONFIG_SRV_ENV.')' : '');
require_once '../lib/htm.php';
$bodyClass = "";
printHeader();
?>
<script>setTimeout(function(){cave.toolbar_setVisible(false);},1000);</script>
<div id="welcome-3ds_start" class="start-page slide-page" data-slide-number="1" style="">
				<div class="window-page">
					<div class="window welcome-window">
						<div class="window-title">
                            <h1 class="window-title-inner">rverse is undergoing maintenance!</h1>
                        </div>
						<div class="window-body">
							<div class="window-body-inner message">
								<p>rverse is undergoing maintenance and will be back soon.</p><br><a href="https://n3ds.rverse.club/" class="black-button">Go to rverse2</a>
							</div>
						</div>
					</div>
				</div>
				<a href="#" class="fixed-bottom-button left exit-button welcome-exit-button accesskey-B" data-sound="SE_CTR_COMMON_SYSAPPLET_END" onClick="cave.exitApp();">Exit</a>
                <!--<a href="#" class="fixed-bottom-button right next-button welcome-next-button" onClick="cave.snd_playBgm('BGM_CAVE_SETTING');">Play sound</a>-->
			</div>
			<script>setTimeout(function(){cave.snd_stopBgm();cave.snd_playBgm('BGM_CAVE_SETTING');},2000);</script>
<?php
printFooter();
?>