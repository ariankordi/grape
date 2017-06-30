<?php
$platform = 2;

switch($_GET['pg'] ?? '')             // Pop off first item and switch
{
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
    default:
        require '../grp_act-php/404.php';
}