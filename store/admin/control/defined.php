<?php

/*
  RELATIVE PATHS
  Rel path auto calculated by system
  Add in your own paths if required by server. Examples:

  define('REL_PATH', '/home/server/path/cart/');
  define('REL_HTTP_PATH', 'http://www.example.com/cart/');

*/
$last_fldr = basename(substr(dirname(__file__), 0, strpos(dirname(__file__), 'control')-1));
define('REL_PATH', substr(dirname(__file__), 0, strpos(dirname(__file__), 'control')-strlen($last_fldr)-2) . '/');
define('REL_HTTP_PATH', '../');

/*
  MP3 PREVIEW LINK
  Do you want to show the MP3 previews link as an option?
  0 = Disabled, 1 = Enabled
*/
define('PRODUCT_MP3_PREVIEWS', 1);

/*
  ADMIN HOMEPAGE DEFAULT SALES/TOTALS VIEW
  Specify the default view for the admin homepage
  Can be any of the following:
  week   = This Week
  month  = This Month
  year   = This Year
  1m     = Last Month
  3m     = Last 3 Months
  6m     = Last 6 Months
  last   = Last Year
*/
define('ADMIN_HOME_DEFAULT_SALES_VIEW', 'week');

/*
  ENABLE 'HELP' LINK
  Do you wish to enable the help link on the top bar in the admin area?
  This is only an option in the commercial version..
  0 = Disabled, 1 = Enabled
*/
define('DISPLAY_HELP_LINK', 1);

/*
  ELFINDER FILE MANAGER LOCALE
  Language locale for Elfinder File Manager

  Available languages at:
  admin/templates/js/i18n/

  File must exist in 'templates/js/i18n/' folder
  Example: templates/js/i18n/elfinder.fr.js
  define('ELF_LOCALE', 'fr');
*/
define('ELF_LOCALE', 'en');

/*
  ENABLE SOFTWARE VERSION CHECK
  Displays on the top bar and is an easy check option to see if new versions have
  been release. You may wish to disable this for clients.
  0 = Disabled, 1 = Enabled
*/
define('DISPLAY_SOFTWARE_VERSION_CHECK', 1);

/*
  DATA PER PAGE
  Relates to data shown per page in the admin area
*/
define('PRODUCTS_PER_PAGE', 20);
define('SEARCH_LOGS_PER_PAGE', 25);
define('LOGS_PER_PAGE', 35);
define('CAMPAIGNS_PER_PAGE', 20);
define('COUPON_REPORTS_PER_PAGE', 10);
define('ZONES_PER_PAGE', 20);
define('SERVICES_PER_PAGE', 20);
define('RATES_PER_PAGE', 20);
define('EMAILS_PER_PAGE', 50);
define('TRACKERS_PER_PAGE', 30);
define('ACCOUNTS_PER_PAGE', 30);
define('SALES_OVERVIEW_PER_PAGE', 25);

/*
  SYSTEM MESSAGES
  When an action is performed in the admin area a system confirmation message appears. This can be disabled if you wish
  0 = Disabled, 1 = Enabled
*/
define('ENABLE_SYSTEM_MESSAGES', 1);

/*
  COMPLETED SALES ON ADMIN HOMEPAGE
  On the admin homepage you`ll see the latest completed sales. Enter amount to show or 0 to disable
*/
define('SHOW_COMPLETED_SALES_ON_MAIN_PAGE', 10);

/*
  PENDING SALES ON ADMIN HOMEPAGE
  On the admin homepage you`ll see the latest pending sales. ALL pending sales display if enabled.
  0 = Disabled, 1 = Enabled
*/
define('SHOW_PENDING_SALES_ON_MAIN_PAGE', 1);

/*
  SALE QTY LIMIT
  For drop downs on sale edit page
*/
define('SALE_QTY_LIMIT', 50);

/*
  REWRITE RULE SLUG AUTO SUGGESTION
  Do you wish to enable the auto suggestion for the rewrite slug?
  0 = Disabled, 1 = Enabled
*/
define('ENABLE_SLUG_SUGGESTION', 1);

/*
  DISPLAY IP ADDRESS AT FOOTER OF INVOICES/PACKING SLIP
  Do you want to display the order IP address(es) at the bottom of invoiced and packing slips?
  0 = Disabled, 1 = Enabled
*/
define('INVOICE_SHOW_IP', 1);
define('PACKING_SLIP_SHOW_IP', 1);

/*
  INCLUDE DOWNLOADS ON PACKING SLIPS & INVOICES
  Do you want to show download purchases on invoices and packing slips?
  0 = No, 1 = Yes
*/
define('INCLUDE_DOWNLOADS_ON_INVOICE', 1);
define('INCLUDE_DOWNLOADS_ON_PACKING_SLIP', 0);

/*
  INCLUDE GIFT CERT PURCHASES ON PACKING SLIPS / INVOICES?
  Do you want to show gift certificate purchases on invoices and packing slips?
  0 = No, 1 = Yes
*/
define('INCLUDE_GIFT_ON_INVOICE', 1);
define('INCLUDE_GIFT_ON_PACKING_SLIP', 0);

/*
  AUTO TAGS TEXT LIMIT
  When you auto create tags from descriptions, do you want to only include words of a certain character length?
  0 for no limit
*/
define('AUTO_TAGS_TEXT_LIMIT', 5);

/*
  AUTO TAGS CAPITALISATION
  Do you want to capitalise all tags?
  0 = No, 1 = Yes
*/
define('CAPITALISE_TAGS', 1);

/*
  WRITE STATUS WHEN DOWNLOAD PAGE LOCKED / UNLOCKED
  Do you want to write an edit status when a download page is locked / unlocked.
  0 = No, 1 = Yes
*/
define('DL_LOCK_UNLOCK_STATUS', 1);

/*
 DEFAULT CHECKED OPTION FOR PRODUCT DOWNLOAD
 When adding products, do you want the product download radio button checked as yes or no by default?
*/
define('IS_PRODUCT_DOWNLOAD','no');

/*
  WRITE STATUS WHEN DOWNLOADS ARE RE-ACTIVATED
  Do you want to write an edit status when downloads are re-activated?
  0 = No, 1 = Yes
*/
define('DL_ACTIVATE_STATUS', 1);

/*
  WRITE STATUS WHEN PRODUCTS ARE ADDED TO SALE
  Do you want to write an edit status when products are added to sale?
  0 = No, 1 = Yes
*/
define('NEW_PRODUCT_EDIT_STATUS', 1);

/*
  PREFIX FOR ATTRIBUTE / PRODUCT ON IN STOCK OVERVIEW EXPORT
  Specify prefixes for in stock export
*/
define('IN_STOCK_PREFIX_PRODUCTS', '(P) ');
define('IN_STOCK_PREFIX_ATTRIBUTES', '(A) ');

/*
  DIV OVERLAY POPUPS
  Adjust sizes if necessary
*/
define('DIVWIN_WIDTH', 920);
define('DIVWIN_HEIGHT', 580);
define('DIVWIN_WIDTH_PRINT', 850);
define('DIVWIN_HEIGHT_PRINT', 400);
define('DIVWIN_STATUS_WIDTH', 875);
define('DIVWIN_STATUS_HEIGHT', 500);
define('DIVWIN_PRODUCTS_WIDTH', 875);
define('DIVWIN_PRODUCTS_HEIGHT', 500);
define('DIVWIN_PERS_WIDTH', 875);
define('DIVWIN_PERS_HEIGHT', 500);
define('DIVWIN_DOWNLOADS_WIDTH', 875);
define('DIVWIN_DOWNLOADS_HEIGHT', 500);
define('DIVWIN_FIELD_INFO_WIDTH', 800);
define('DIVWIN_FIELD_INFO_HEIGHT', 400);
define('BBCODE_WINDOW_WIDTH', 920);
define('BBCODE_WINDOW_HEIGHT', 580);
define('DIVWIN_NOTES_WIDTH', 750);
define('DIVWIN_NOTES_HEIGHT', 500);
define('DIVWIN_RESCATS_WIDTH', 600);
define('DIVWIN_RESCATS_HEIGHT', 400);
define('DIVWIN_BUYNOW_WIDTH', 600);
define('DIVWIN_BUYNOW_HEIGHT', 350);
define('DIVWIN_PEXPIRY_WIDTH', 600);
define('DIVWIN_PEXPIRY_HEIGHT', 450);
define('DIVWIN_OVPROF_HEIGHT', 450);
define('DIVWIN_OVPROF_WIDTH', 650);

/*
  ISBN API URL
  Url for ISBN API lookup. DO NOT change unless you know what you are doing!
*/
define('ISBN_API_URL', 'http://isbndb.com/api/books.xml?access_key={KEY}&index1=isbn&value1={ISBN}&results=texts');

/* PRODUCT IMPORT HEADER TEXT LIMIT
   When importing products, sets a text limit for the header row
*/
define('PROD_IMPORT_HEAD_TXT_LIMIT', 100);

/*
  CHMOD VALUES
  For linux servers only. Only change if you understand this.
  DO NOT enclose values in quotes or apostrophes..
*/
define('CHMOD_VALUE', 0777);
define('AFTER_UPLOAD_CHMOD_VALUE', 0644);

/*
  ATTACHMENT CLEANUP
  This cleans up attachment names and removes problem characters
*/
define('ATTACHMENT_FILE_CLEANUP', '[^a-zA-Z0-9\s]');

/*
  ATTACHMENTS FOLDER NAME
  By default the admin attachments folder is called 'attachments'. If you wish to change it, rename folder
  and enter name here
*/
define('ATTACH_FOLDER', 'attachments');

/*
  CHECK SAVE ATTACHMENTS TO SERVER
  Set default checked option for save to server option for attachments on sale update page
  yes = default checked option is yes
  no  = default checked option is no
*/
define('SAVE_ATTACHMENTS_TO_SERVER', 'no');

/*
  REDIRECT TO ORDER IF ORDER LINK CLICKED IN EMAILS
  If you have clicked a link to an order in an email the system will redirect you to the order page
  if the users permissions are allowed.

  You can disable this by default if you wish.
  0 = No Redirect, 1 = Redirect if allowed
*/
define('ORDER_REDIRECT', 1);

/*
  SHOW DISABLED CATEGORIES ON PRODUCT ADD/EDIT PAGE
  Do you want to show disabled products on add/edit product pages?
  Can be useful if you want to add products to a disabled category before enabling the category
  0 = No, 1 = Yes
*/
define('SHOW_DISABLED_CATS_ADD_PRODUCT', 1);

/*
  ZONE AREA DISPLAY LIMIT
  Amount of zone areas to show before 'View/Close' appears.
*/
define('ZONE_AREA_DISPLAY_LIMIT', 20);

/*
  DEFAULT SCREEN LOAD FOR SALES TRENDS
  Can be any of the following values:
  3,6,12,24 or year
*/
define('DEFAULT_SALES_TREND', 'year');

/*
  BANNER SETTINGS
  Specify text prefix for banners
  Do you want to rename banner file images? (0 = Disabled, 1 = Enabled)
*/
define('BANNER_PREFIX', 'img_');
define('RENAME_BANNERS', 1);

/*
  STATS DECIMAL PLACES
  Amount of decimals for stats. This probably won`t need changing
*/
define('STATS_DECIMAL_PLACES', 1);

?>
