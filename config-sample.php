<?php
/*
* This is the sample config file. It's intended to stay outside all of the environment folders, like grp_portal.
* You'll probably have to change these. Please don't make your server environment 'S1'.
*/

// Hosts. These are very important and will determine what mode you are even in.
// also these aren't that important anymore but they're still here
const HOSTS = array(
'PORTAL_HOST'=>'im going to explode',
'OFFDEVICE_HOST'=>'your mom',
'N3DS_HOST'=>'your mom',
'ADMIN_HOST'=>null,
);

/* Environment values.
* "nsslog" can be true or false to indicate NSS being enabled.
* Server type can be 'dev' or 'prod'. Environment can be one letter and then one number, like 'J2' or 'T1'.
*/
const CONFIG_SRV_NAME = 'rverse3';
// NSS is almost never necessary, don't enable it.
const CONFIG_SRV_NSS = false;
const CONFIG_SRV_TYPE = 2;
// 0 - dev, 1 - prod
const CONFIG_SRV_ENV = 1;

$dev_server = true;

// put nnidlt here or whatever
$grp_config_mii_endpoint_prefix = 'https://nnidlt.murilo.eu.org/api.php?output=hash_only&env=production&user_id=';

// If NSS is enabled, then these can be the allowed keys.
$grp_config_nss_keys = array(
''
);
$grp_config_allow_signup = true;

// Database connection info for a MySQL database.
const CONFIG_DB_SERVER = 'localhost:3306';
const CONFIG_DB_USER = 'your mom';
const CONFIG_DB_PASS = 'your mom';
const CONFIG_DB_NAME = 'your mom';

// Default protocol for redirects.
$grp_config_recommend_ssl = false;

// Keys; private and public, RSA, raw PEM data.
$grp_config_privkey = <<< END_OF_DATA
-----BEGIN RSA PRIVATE KEY-----
[key]
-----END RSA PRIVATE KEY-----

END_OF_DATA;
$grp_config_pubkey = <<< END_OF_DATA
-----BEGIN PUBLIC KEY-----
[key]
-----END PUBLIC KEY-----

END_OF_DATA;

// reCAPTCHA keys
$grp_config_recaptcha_pubkey = '';
$grp_config_recaptcha_pkey = '';

// Maximum time allowed per one post to the next in seconds.
$grp_config_max_postbuffertime = 2;
// Same as above, but with replies.
$grp_config_max_replybuffertime = 1;

// Allow users to block?
$grp_config_allow_blacklist = true;

// Allow users to post images without permissions?
$grp_config_allow_allimages = false;

// disabled if 
$grp_mail_param = array(
		'addr' => 'aaaa@ariankordi.net',
        'host' => 'tls://smtp.zoho.com',
        'IDHost' => 'smtp.zoho.com',
        'port' => 465,
        'username' => 'aaaa@ariankordi.net',
        'password' => 'no',
        'auth' => true,
);
