<?php

/*
   FOR AFFILIATES
   Key is raw value. This is encoded in the system
   Duration cookie stays alive in days
   Log file name. Can be changed for security
*/

define('MSW_AFF_SYS_KEY', 'affiliate_key');
define('MSW_AFF_SYS_DUR', 180);
define('MSW_AFF_SYS_LOG_FILE', 'affiliate-system.log');

/*
  OFF CANVAS - DEFAULT PANEL
  Set default open panel for off canvas menu
  ID number of parent category
*/
define('DEF_OPEN_MENU_PANEL', 0);

/*
  OFF CANVAS - INCLUDE PARENTS AS CHILD
  If set to ON, when the panel opens, the parent
  category is also shown.
  1 = Enabled, 0 = Disabled
*/
define('OFF_CANVAS_PARENT_AS_CHILD', 1);

/*
  ENABLE BUY NOW OPTION
  If enabled, allows buy now links to function as direct add to basket links
*/
define('BUY_NOW_CODE_OPTION', 1);

/*
  DATA TO SHOW PER PAGE
  How many products to show per page
*/
define('HISTORY_PER_PAGE', 20);
define('SAVED_SEARCHES_PER_PAGE', 20);
define('WISHLIST_PER_PAGE', 20);
// Only applicable if admin value is blank or 0
define('HOMEPAGE_PRODUCTS_LIMIT', 10);

/*
 LATEST ORDER COUNT ON ACCOUNT DASHBOARD
 How many latest sales to show on account main screen
*/
define('ACC_DASH_LATEST_COUNT', 5);

/*
  CATEGORY, SEARCH, SPECIAL, LATEST - VIEW ALL OPTIONS
  Do you want to enable the 'View All Products', 'View All Searches' option in the drop down filters?
  If selected loads all products without pagination. For heavy databases this may cause load issues, so enable with caution..
*/
define('VIEW_ALL_CATEGORY_DD', 'no');
define('VIEW_ALL_BRANDS_DD', 'no');
define('VIEW_ALL_SPECIALS_DD', 'no');
define('VIEW_ALL_SEARCH_DD', 'no');
define('VIEW_ALL_LATEST_DD', 'no');
define('VIEW_ALL_WISH_DD', 'no');

/*
  PRODUCT DOWNLOADS - FLUSH OUTPUT BUFFER
  Enabling this generally results in better reliablity for product downloads
  If you find that your downloads are 0 bytes and your paths are correct you may
    want to disable this
  1 = Enabled, 0 = Disabled
*/
define('DOWNLOADS_FLUSH_BUFFER', 1);

/*
  BREADCRUMB LINKS SEPARATOR
  Specify separator character or symbol for breadcrumb links
*/
define('BREADCRUMBS_SEPARATOR', ' <i class="fa fa-angle-right fa-fw"></i> ');

/*
  PRODUCT TEXT CHARACTER LIMIT IN LEFT MENU BASKET
  How much text to show for product name in left menu basket..
  Set to 0 for no limit
*/
define('LEFT_MENU_BASKET_CHAR_LIMIT', 60);

/*
  ORDER SALE COMPARISON ITEMS
  How do you wish to order sale comparison items?
  Can be a product table field name or rand()
  id DESC, id ASC, pName, pName DESC, rand() etc

  You can also use FIELD to map certain fields first.
  FIELD(id,1,5,9),pName
*/
define('COMPARISON_ORDER_BY', 'pName');

/*
  ORDER RELATED PRODUCT ITEMS
  How do you wish to order related product items?
  Can be a product table field name or rand()
  id DESC, id ASC, pName ASC, pName DESC, rand() etc

  You can also use FIELD to map certain fields first.
  FIELD(id,1,5,9),pName
*/
define('RELATED_ORDER_BY', 'pName');

/*
  ORDER BRANDS
  How do you wish to order brands?
  Can be a product table field name
  id DESC, id ASC, name ASC, name DESC

  You can also use FIELD to map certain fields first.
  FIELD(id,1,5,9),name
*/
define('BRANDS_ORDER_BY', 'name');

/*
  AUTO SORT PERSONALISATION
  Do you want to auto sort personalisation options if specified?
  1 = Enabled, 0 = Disabled
*/
define('AUTO_SORT_PERSONALISATION_OPTIONS', 1);

/*
  RESTRICT PERSONALISATION TEXT
  On the checkout page, do you wish to restrict how much personalisation
  text data is shown? Enter number for restriction, or 0 to disable
*/
define('PERSONALISATION_TEXT_RESTRICTION', 200);

/*
  SPECIAL OFFER PAGE - DEFAULT ORDER
  Can be any column name in the products table, asc or desc
  id DESC, id ASC, pName ASC, pName DESC, pOffer ASC, pOffer DESC etc
*/
define('ORDER_SPECIAL_OFFERS', 'pName');

/*
  SET DEFAULT FILTER FOR CATEGORY PAGE
  price-low  = Price: Low - High
  price-high = Price: High - Low
  title-az   = Title: A - Z
  title-za   = Title: Z - A
  date-new   = Date: Newest
  date-old   = Date: Oldest
  stock      = Low Stock
  multi-buy  = Multi Buy
*/
define('CATEGORY_FILTER', 'title-az');

/*
  SET DEFAULT FILTER FOR BRANDS PAGE
  price-low  = Price: Low - High
  price-high = Price: High - Low
  title-az   = Title: A - Z
  title-za   = Title: Z - A
  date-new   = Date: Newest
  date-old   = Date: Oldest
  stock      = Low Stock
  multi-buy  = Multi Buy
*/
define('BRANDS_FILTER', 'title-az');

/*
  SET DEFAULT FILTER FOR SEARCH PAGE
  price-low  = Price: Low - High
  price-high = Price: High - Low
  title-az   = Title: A - Z
  title-za   = Title: Z - A
  date-new   = Date: Newest
  date-old   = Date: Oldest
  multi-buy  = Multi Buy
*/
define('SEARCH_FILTER', 'title-az');

/*
  SET DEFAULT FILTER FOR SPECIAL OFFERS
  price-low  = Price: Low - High
  price-high = Price: High - Low
  title-az   = Title: A - Z
  title-za   = Title: Z - A
  date-new   = Date: Newest
  date-old   = Date: Oldest
  stock      = Low Stock
  multi-buy  = Multi Buy
*/
define('SPECIAL_OFFER_FILTER', 'title-az');

/*
  SET DEFAULT FILTER FOR LATEST PRODUCTS
  price-low  = Price: Low - High
  price-high = Price: High - Low
  title-az   = Title: A - Z
  title-za   = Title: Z - A
  date-new   = Date: Newest
  date-old   = Date: Oldest
  stock      = Low Stock
  multi-buy  = Multi Buy
*/
define('LATEST_PRODUCTS_FILTER', 'date-new');

/*
  SET DEFAULT FILTER FOR PUBLIC WISHLIST
  price-low  = Price: Low - High
  price-high = Price: High - Low
  title-az   = Title: A - Z
  title-za   = Title: Z - A
  date-new   = Date: Newest
  date-old   = Date: Oldest
  stock      = Low Stock
*/
define('WISH_PRODUCTS_FILTER', 'title-az');

/*
  ORDER SEARCH RESULTS
  How do you wish to order search results initially?
  Can be a product table field name
  id DESC, id ASC, pName ASC, pName DESC

  You can also use FIELD to map certain fields first.
  FIELD(id,1,5,9),pName
*/
define('SEARCH_ORDER_BY', 'pName');

/*
  ORDER PRODUCT DOWNLOADS
  How do you wish to order product downloads?
  Can be a product table field name
  id DESC, id ASC, pName ASC, pName DESC
*/
define('DOWNLOADS_ORDER_BY', 'pName');

/*
  ORDER ZONES
  How do you wish to order zones on the checkout page
  Can be a zone table field name
  id DESC, id ASC, zName ASC, zName DESC
*/
define('CHECKOUT_ZONE_ORDER_BY', 'zName');

/*
  ORDER ZONES AREAS
  How do you wish to order zone areas on the checkout page
  Can be a zone_areas table field name
  id DESC, id ASC, areaName ASC, areaName DESC
*/
define('CHECKOUT_ZONE_AREA_ORDER_BY', 'areaName');

/*
  ONLY SHOW NEWS TICKER ON STORE MAIN PAGE
  Do you only want to show the news ticker on the store main page?
  1 = Yes, 0 = No, show on all pages (except checkout)
*/
define('NEWS_TICKER_DISPLAY_PREF', 1);

/*
  SHOW TICKER ON CUSTOM PAGES
  Do you want to show the news ticker on custom pages?
  Enter ID numbers separated with comma. Look for the ID number in the url.

  np/about-us/3/index.html = 3
  index.php?np=3           = 3

  Example to show ticker on pages 3,5 & 6
  define('NEWS_TICKER_DISPLAY_CUSTOM_PAGES', '3,5,6');

  Set to 0 to disable on custom pages.
*/
define('NEWS_TICKER_DISPLAY_CUSTOM_PAGES', 0);

/*
  ZONE AREAS DELIMITER
  Delimiter for displaying zone areas on shipping/returns page..
*/
define('ZONE_AREA_DELIMITER', ',');

/*
  SET DEFAULT CHECKED GIFT CERTIFICATE
  First gift certificate is checked by default.
  To change this enter position to override. Example, to auto check 3rd, set as 3
  Zero or blank to disable
*/
define('DEFAULT_CHECKED_GIFT', '');

/*
  CONTACT FORM AUTO RESPONDER
  Do you wish to enable the contact form auto responder?
  Sends confirmation email to sender to confirm their enquiry..
  1 = Enabled, 0 = Disabled
*/
define('CONTACT_AUTO_RESPONDER', 1);

/*
  PRODUCT ENQUIRY AUTO RESPONDER
  Do you wish to enable the product enquiry auto responder?
  Sends confirmation email to sender to confirm their enquiry..
  1 = Enabled, 0 = Disabled
*/
define('PROD_ENQUIRY_AUTO_RESPONDER', 1);

/*
  DEFAULT BUILD DATE FOR RSS FEEDS
  Should NOT be changed
*/
define('RSS_BUILD_DATE_FORMAT', date('r'));

/*
  AUTO PARSE LINE BREAKS
  Do you want to auto parse line breaks for new page text data?
*/
define('AUTO_PARSE_LINE_BREAKS', 1);

/*
  INCLUDE PERSONALISED OPTIONS IN ORDER EMAILS
  Include personalised options in emails if applicable?
  1 = Enabled, 0 = Disabled
*/
define('EMAIL_PERSONALISATION_INCL', 1);

/*
  INCLUDE ATTRIBUTE OPTIONS IN EMAILS
  Include attribute options in emails if applicable?
  1 = Enabled, 0 = Disabled
*/
define('EMAIL_ATTRIBUTES_INCL', 1);

/*
  INCLUDE GIFT FROM/TO IN EMAILS
  Include gift certificate from/to info in emails if applicable?
  1 = Enabled, 0 = Disabled
*/
define('EMAIL_GIFT_FROM_TO_INCL', 1);

/*
  AMOUNT OF REFRESHES FOR RESPONSE PAGE AND REFRESH TIME
  Set refresh time and limit for gateway checks
  Once the limit is hit a message will display
*/
define('RESPONSE_REFRESH_TIME', 5);
define('RESPONSE_PAGE_REFRESHES', 12);

/*
  CUSTOM TAG SEPARATOR
  Specify custom tag separator. For product tags, this is the separator between each
*/
define('TAG_SEPARATOR', ', ');

/*
  ENABLE NEWSLETTER EMAIL AUTO RESPONDERS
  Do you wish to enable the email confirmation auto responder?
  If enabled, sends email confirmation on unsubscribe
  1 = Enabled, 0 = Disabled
*/
define('NEWSLETTER_EMAIL_AUTO_RESPONDERS', 1);

/*
  THOUSANDS SEPARATOR ON PRICES
  Applies to formatting on certain pages..
  Blank for none.
*/
define('PRICE_THOUSANDS_SEPARATORS', ',');

/*
  YOUTUBE EMBED CODE
  Adjust code if required for Youtube embed code

  {CODE} = Video code
*/
define('YOU_TUBE_EMBED_CODE','<iframe src="https://www.youtube.com/embed/{CODE}" style="border:0 !important" allowfullscreen></iframe>');

/*
  VIMEO EMBED CODE
  Adjust code if required for Vimeo embed code

  {CODE} = Video code
*/
define('VIMEO_EMBED_CODE', '<iframe src="https://player.vimeo.com/video/{CODE}" style="border:0 !important" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>');

/*
  DAILY MOTION EMBED CODE
  Adjust code if required for Daily Motion embed code

  {CODE} = Video Code
*/
define('DAILY_MOTION_EMBED_CODE', '<iframe src="https://www.dailymotion.com/embed/video/{CODE}" style="border:0 !important" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>');

/*
  SOUNDCLOUD PLAYER CODE
  Adjust code if required for soundcloud BB code

  {CODE} = Soundcloud URL
*/
define('SOUNDCLOUD_EMBED_CODE','<iframe width="100%" height="166" scrolling="no" style="border:0 !important" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{CODE}&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>');

/*
  MP3 PLAYER EMBED CODE - HTML5 ONLY
  Adjust code if required for mp3 bb code
  This is used in BB code tags.
  Where the mp3 path loads use {MP3}
*/
define('MP3_EMBED_CODE','<audio controls><source src="{MP3}" type="audio/mpeg">Your browser does not support MP3 files via HTML5 audio tags.</audio>');

/*
  SKIP LOGGING OF CERTAIN USERS / ACCOUNTS
  If you have the entry log enabled, you can skip the logging of certain users/accounts. Enter email addresses
  exactly as they appear on the user/accounts screens.
*/
define('ENTRY_LOG_SKIP_USERS', '');

/*
  TRACKING CODE PREFIX
  Example: tr
  http://www.example.com/store/?tr=xxx
*/
define('TRACKING_CODE_PREFIX', 'tr');

/*
  CURRENCY CRON OUTPUT
  When the currency updater cron is run (control/cron/currency-updater.php), do you want to see output?
  1 = Enabled, 0 = Disabled
*/
define('CURRENCY_CONVERTER_CRON_OUTPUT', 1);

/*
  PRICE DECIMAL PLACES
  How many decimal places to show for prices?
*/
define('PRICE_FORMAT_DECIMAL_PLACES', 2);

/*
  BACKUP CRON OUTPUT
  When the backup cron is run (control/cron/db-backup.php), do you want to see output?
  1 = Enabled, 0 = Disabled
*/
define('BACKUP_CRON_OUTPUT', 1);

/*
  PRODUCT OPS CRON OUTPUT
  When the product ops cron is run (control/cron/product-ops.php), do you want to see output?
  1 = Enabled, 0 = Disabled
*/
define('POPS_CRON_OUTPUT', 1);

/*
  BACKUP CRON EMAILS
  If you are running the 'control/cron/db-backup.php' file as a cron, enter emails here, separated with a comma for multiple addresses.
  The cron tab/job emails should not exist on the same server as your database.
  If this is left blank, backups are saved locally in the 'logs' folder.

  Examples:
  define('BACKUP_CRON_EMAILS', 'email@example.com');
  define('BACKUP_CRON_EMAILS', 'email@example.com,email2@example.com');

*/
define('BACKUP_CRON_EMAILS', '');

/*
  NOT SHOWN ON SMALL DEVICES
  Set to 0 to show.
*/
define('MC_MB_BANNERS', 0);

/*
  ENABLE SHIPPING / CHECKOUT DEBUG LOGS
  For development only.
*/
define('SHIP_DEBUG_LOG', 0);
define('CHECKOUT_DEBUG_LOG', 0);

/*
  FOLDERS
  Folder paths. DO NOT change these values..
*/
define('PRODUCTS_FOLDER', 'content/products');
define('VIDEO_FOLDER', 'content/video');
define('BANNER_FOLDER', '{theme}/images/banners');

?>