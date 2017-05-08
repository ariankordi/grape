<?php

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
  <style type="text/css">* {}</style></head>

  <body data-spy="scroll" data-target=".bs-docs-sidebar">
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          
          <a class="navbar-brand" href="/act/">Grape::Account</a>
          
        </div>
      </div>
    </nav>

    <div class="container"<?php echo (isset($bodyStyle) ? ' style="'.$bodyStyle.'"' : '').'>
	';
}

function printFooter() {?>
    </div> <!-- /container -->

    <footer class="footer">
      <div class="container" style="text-align:center;">
      <hr>
        grape<?php global $dev_server; echo ($dev_server == true ? '/'.VERSION : ''); ?>
      </div>
    </footer>
  

</body></html>
<?php
}

function printErr($code, $message, $back) {
printHeader(); printf('    <h1>Error Code: %s-%s</h1>', substr($code,0,3), substr($code,4,3));
print '<br>
    
    <p>'.nl2br($message).'</p>
<br>
<a href="'.$back.'" class="btn btn-primary btn-primary">Back</a>  '; printFooter();
}