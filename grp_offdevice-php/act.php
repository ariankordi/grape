<?php
$platform = 1;

switch($_GET['pg'] ?? '')             // Pop off first item and switch
{
	case 'index':
        require '../grp_act-php/index.php';
        break;
    case 'login':
        require '../grp_act-php/login.php';
        break;
    case 'logout':
	    require '../grp_act-php/logout.php';
        break;
	case 'create':
	    require '../grp_act-php/create.php';
		break;
	case 'edit':
		require '../grp_act-php/edit.php';
		break;
	case 'confirm':
	    require '../grp_act-php/email_confirm.php';
		break;
	case 'updatemiidata':
		require '../grp_act-php/updatemii.php';
		break;
	case 'canvas':
		require '../grp_act-php/canvas.php';
		break;
	case 'bootstrap.min.css':
		header("Content-Type: text/css");
		require '../grp_act-php/lib/bootstrap.min.css';
		break;
	case 'bootstrap.min.js':
		header("Content-Type: text/javascript");
		require '../grp_act-php/lib/bootstrap.min.js';
		break;
	case 'jquery.min.js':
		header("Content-Type: text/javascript");
		require '../grp_act-php/lib/jquery.min.js';
		break;
	default:
        require '../grp_act-php/404.php';
}