<?php

/* Default menu panel on page load. Any array key in 'templates/menu-panel.php' */
define('DEF_ADM_OPEN_MENU_PANEL', 'system');

/* IP lookup service */
define('IP_LOOKUP', 'http://whatismyipaddress.com/ip/{ip}');

/* On sale edit screen, show view and edit product controls beneath image. 0 = Off, 1 = On */
define('SALE_EDIT_PRODUCT_CONTROLS', 0);

/* On sale edit screen, attribute edit link beneath attributes. 0 = Off, 1 = On */
define('SALE_EDIT_PRODUCT_ATTRIBUTE_EDIT', 0);

/* Allowed extensions for music previews. Comma delimit */
define('ALLOWED_MUSIC_EXTENSIONS', 'mp3,mp4,wav');

/* Auto complete limit */
define('AUTO_COMPLETE_MIN_LENGTH', 3);

/* Enable tweet option for global user? Must be active in settings first. */
define('TWEET_GLOBAL', 'yes');

/* Load Admin homescreen tiles. 0 = Off, 1 = On */
define('ADMIN_HOMESCREEN_TILES', 1);

/* Load personal account tile, or trade acount tile */
/* Value can be 'personal' or 'trade' */
define('ACC_TILE_PREF', 'personal');

/* Do you wish to enable the cookie option on the login page? */
define('ENABLE_LOGIN_COOKIE', 1);

/* Admin login cookie duration. In days */
define('LOGIN_COOKIE_DURATION', 30);

/* Hide force password reset option */
define('HIDE_FORCE_PASS_RESET', 0);

?>