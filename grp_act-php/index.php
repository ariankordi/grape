<?php
$grpmode = 1; require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php';

if(empty($_SESSION['pid'])) {
defaultRedir(false, true); exit();
}
printHeader();
?>
<div class="page-header">
    <h3><?=loc('grp.act.index_welcome')?></h3>
    <h5><?=loc('grp.act.index_welcome_sub')?></h5>
</div>
<a href="/act/updatemiidata"><?=loc('grp.act.index_upd_mii_data')?></p>
<a href="/act/edit"><?=loc('grp.act.index_edit_account')?></p>
<a href="/act/logout"><?=loc('grp.act.index_logout')?></p>
<a href="#" id="test">Loading...</a>
<script>
    document.getElementById("test").innerHTML = window.navigator.platform;
    </script>