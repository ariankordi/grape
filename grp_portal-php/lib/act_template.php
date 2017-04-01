<?php
//Signup form
if (empty($pagetitle)) { $pagetitle = 'Grape::Account'; }
$body_id = 'help';


	$has_header_js = 'no';
    include 'header.php';
	?>
	<div id="body">
<header id="header">
  
  <h1 id="page-title"><?php print $pagetitle; ?></h1>

</header>

<div class="help-left-button">
  <a href="<?php print $act_back_location; ?>" class="guide-exit-button exit-button index" data-sound="SE_WAVE_BACK">Cancel</a>
</div>
<h2 id="sub-header" class="guide-sub-header"><?php print $act_template_subheader ?></h2>
<div id="guide" class="help-content"><style>.btn_001 { 
margin:0 30px 35px 20px; float:left; 
display:block; width:355px; height:60px; line-height:60px; text-align:center; margin:auto; font-size:26px; color:#323232; text-decoration:none; 
    background:-webkit-gradient(linear, left top, left bottom, from(#ffffff), color-stop(0.5, #ffffff), color-stop(0.8, #f6f6f6), color-stop(0.96, #f5f5f5), to(#bbbbbb));
  border: 0;
  margin: 0;
    border-radius:50px; box-shadow:0 3px 10px 0 #555555; text-align:center; margin:10px; padding:auto; text-decoration:none; cursor:pointer; }
.textbox{ background:#ffffff; border:2px #747474 solid; border-radius:10px; color:#828282; box-shadow: 0 2px 6px 1px #aaaaaa inset; }</style>
<?php print $act_content; ?>
    </div>
	<?php include 'lib/footer.php'; ?>