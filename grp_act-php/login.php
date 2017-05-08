<?php
$grpmode = 1; require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php'; $bodyClass = 'min-height:400px';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
require_once '../grplib-php/account-helper.php';
	    if(empty($_POST['user_id'])) {
printErr(1022543, 'You must enter a login ID.', '/act/login'); grpfinish($mysql); exit(); 
}        if(empty($_POST['password'])) {
printErr(1022616, 'You must enter a password.', '/act/login'); grpfinish($mysql); exit(); 
} 
$check_login = actLoginCheck($mysql->real_escape_string($_POST['user_id']), $mysql->real_escape_string($_POST['password']));
if(!is_array($check_login)) {
if($check_login == 'none' || $check_login == 'fail') {
printErr(1022611, 'Invalid account ID and password combination.', '/act/login'); grpfinish($mysql); exit(); 
} elseif($check_login == 'ban') {
printErr(1022812, "Account has been banned.\n\nPlease contact the admin if you need any help.", '/act/login'); grpfinish($mysql); exit(); 
}
grpfinish($mysql); exit();
} else {
setLoginVars($check_login, true);
       }
grpfinish($mysql); exit();
}
printHeader();
print '<div class="page-header">
        <h3>Authenticate</h3>
    </div>
    <div class="col-sm-6">
    <form action="/act/login" method="post" class="form-horizontal">       
		    
			  <br>
			         <div class="row">
			  <input type="text" class="form-control" name="user_id" placeholder="Login ID" required autofocus>
                     </div><div class="row">
			  <input type="password" class="form-control" name="password" placeholder="Password" required>  
                     </div><div class="row">   		  
			 <div class="form-actions">
			<span><br>
	<button class="btn btn-primary btn-block" name="Submit" value="Log In" type="Submit">Login</button>  
</span></div>
		</div>			
		</form></div>
';
printFooter();