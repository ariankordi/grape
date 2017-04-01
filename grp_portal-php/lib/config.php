<?php

# Environment values.
# "nsslog" can be true or false to indicate NSS being enabled.
# Server type can be 'dev' or 'prod'. Environment can be one letter and then one number, like 'S1'.
$grp_config_server_nsslog = true;
$grp_config_server_type = 'dev';
$grp_config_server_env = 'S1';

# If NSS is enabled, then these can be the allowed keys.
$grp_config_server_nss_keys = array(
'NG2fbc438e',
'NG12a68c23',
'NG1729ae0b',
'NG0801fb57'
);

# Database connection info for a MySQL database.
$grp_config_database_server = 'localhost:3306';
$grp_config_database_user = 'root';
$grp_config_database_pw = '[password]';
$grp_config_database_name = 'grape';

# Default protocol for redirects.
$grp_config_default_redir_prot = 'https://';

# Keys; private and public, RSA, raw PEM data.
$grp_config_privkey = <<< END_OF_DATA
-----BEGIN RSA PRIVATE KEY-----
[key]
-----END RSA PRIVATE KEY-----

END_OF_DATA;
$grp_config_pubkey = <<< END_OF_DATA
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuHRkfzhZN8M4OxYOPEHL
/RIIvAOHlGyF+wDzK87aUbWf0W6LIwNf3S5xlGYCLOAh8NPdk/lXKqX/pi0e42ez
zYIfddr7cDUCv4qml4HoQ6kVwv3AqlBXKxTeoyg/kRiaiiNYKkz5sZhp2YGKS8rq
hKutvtsTGatN3yZq+TbAFD53ARw0ldNpy5Og1t+YF49wp6au8tvzVYedlKXRAN7D
IOvPO4SYsZmz430UFgv0jqfAMBqX5JVXqzSAAaJvdF0kN3YRhYIlz7Etf4oCMo66
6AuCwmYe58oMEPgkBLZYayymZ3w4GJEfH/+0SvGHphdzRFz38I4XJb+RAmWLT/OT
0wIDAQAB
-----END PUBLIC KEY-----

END_OF_DATA;

# Maximum time allowed per one post to the next in seconds.
$grp_config_max_postbuffertime = 2;
# Same as above, but with replies.
$grp_config_max_replybuffertime = 1;

?>