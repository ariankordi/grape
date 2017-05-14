<?php
setTextDomain('accounts');

function printHeader() { global $bodyStyle; ?>
<html lang="en"><head>
    <meta charset="utf-8">
    <title>Grape::Account</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body { padding-top: 60px; }
      .highlight { background-color: yellow }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style type="text/css">* {}</style>   
  <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"></head>
  <body data-spy="scroll" data-target=".bs-docs-sidebar">
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          
          <a class="navbar-brand" href="/act/">Grape::Account</a>
		  
			<div class="navbar-collapse">

                

                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Language <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
                            
                            <li><a href="?locale.lang=en-US" class="language" rel="en-US">English
</a></li>
<li><a href="?locale.lang=de-DE" class="language" rel="de-DE">Deutsch
</a></li>
<li><a href="?locale.lang=fr-FR" class="language" rel="fr-FR">Français
</a></li>
<li><a href="?locale.lang=ja-JP" class="language" rel="ja-JP">日本語
</a></li>
									</ul>
								</li>
							</ul>

						</div>          
					</div>
				</div>
    </nav>

    <div class="container"<?php echo (isset($bodyStyle) ? ' style="'.$bodyStyle.'"' : '').'>
	';
}

function printFooter() { global $dev_server; ?>
    </div> <!-- /container -->

    <footer class="footer">
      <div class="container" style="text-align:center;">
      <hr>
        grape<?=($dev_server == true ? '/'.VERSION : '')?>
      </div>
    </footer>
  

</body></html>
<?php
}

function printErr($code, $message, $back) {
printHeader(); printf('    <h1>Error Code: %s-%s</h1>', substr($code,0,3), substr($code,3,4));
print '<br>
    
    <p>'.nl2br($message).'</p>
<br>
<a href="'.$back.'" class="btn btn-primary btn-primary">Back</a>  '; printFooter();
}

function defaultRedir($has_post) {
// Please change with 'LOCATION' constant that contains something like: https://portal-t1.grp.app.ariankordi.net
global $grp_config_default_redir_prot;
if($has_post) { $location = $_POST['location']; } else { $location = $_GET['location']; }
header('Location: '.$grp_config_default_redir_prot.''.$_SERVER['HTTP_HOST'].''.(!empty($location) ? htmlspecialchars($location) : '/'), true, 302);
}