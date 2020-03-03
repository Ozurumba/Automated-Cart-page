<?php

//-------------------------------------------------------------
// CONSTANTS
// Edit values on right, DO NOT change values in capitals
//-------------------------------------------------------------

define('SCRIPT_VERSION', '3.7');
define('MSW_PHP_MIN_VER', '5.5');
define('SCRIPT_NAME', 'Maian Cart');
define('SCRIPT_URL', 'maiancart.com');
define('SCRIPT_ID', 11);
define('SCRIPT_RELEASE_YR', '2006');
define('SCRIPT_DESC', 'PHP Ecommerce System');

define('GLOBAL_PATH', substr(dirname(__file__), 0, strpos(dirname(__file__), 'control')-1) . '/');
define('AUTO_FILL_PATH', dirname(__file__));
define('MSW_PHP', (version_compare(PHP_VERSION, '7.1.0', '<') ? 'old' : 'new'));

?>