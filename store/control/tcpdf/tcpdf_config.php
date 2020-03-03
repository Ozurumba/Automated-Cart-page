<?php

if (!defined('MC_PDF')) {
  include(GLOBAL_PATH . 'control/system/headers/403.php');
  exit;
}

/* TCPDF CONFIG
   Most values here won`t need changing. Edit cautiously.
----------------------------------------------------------------*/

define('PDF_FONT_SIZE', 8);
define('DRAW_OUTSIDE_BORDER', 1);

// Personal / retail
$drawOutsideBorderCfg = array(
  'width' => 2,
  'color' => array(117,132,92)
);
// Trade
$drawOutsideBorderTradeCfg = array(
  'width' => 2,
  'color' => array(255,0,222)
);

define('PURCHASE_DATE_IN_FILE_NAME', 1);
define('PDF_SHOW_CURRENCY_SYMBOLS', 1);

define('PDF_CREATOR', $SETTINGS->website);
define('PDF_AUTHOR', $SETTINGS->website);
define('PDF_UNIT', 'mm');
define('PDF_MARGIN_HEADER', 0);
define('PDF_MARGIN_FOOTER', 10);
define('PDF_MARGIN_TOP', 1);
define('PDF_MARGIN_BOTTOM', 10);
define('PDF_MARGIN_LEFT', 5);
define('PDF_MARGIN_RIGHT', 5);
define('PDF_FONT_NAME_MAIN', $PDF_CFG->font);
define('PDF_FONT_SIZE_MAIN', 6);
define('PDF_FONT_NAME_DATA', $PDF_CFG->font);
define('PDF_FONT_SIZE_DATA', 6);
define('PDF_FONT_MONOSPACED', $PDF_CFG->font);
define('PDF_IMAGE_SCALE_RATIO', 1.25);
define('HEAD_MAGNIFICATION', 1.1);
define('K_CELL_HEIGHT_RATIO', 1.25);
define('K_TITLE_MAGNIFICATION', 0.2);
define('K_SMALL_RATIO', 2/3);

// Don`t modify these unless you understand what you are doing..
define('PDF_PAGE_FORMAT', 'A4');
define('PDF_PAGE_ORIENTATION', 'P');
define('K_PATH_MAIN', GLOBAL_PATH . 'control/tcpdf/');
define('K_PATH_FONTS', K_PATH_MAIN . 'fonts/');

$cache = (@ini_get('upload_tmp_dir') ? @ini_get('upload_tmp_dir') : sys_get_temp_dir());
if ($cache == '' || !is_dir($cache)) {
  if (defined('THEME_FOLDER')) {
    $cache = GLOBAL_PATH . THEME_FOLDER . '/cache/';
  } else {
    $cache = GLOBAL_PATH . 'content/_theme_default/cache/';
  }
}
define('K_PATH_CACHE', $cache . (substr($cache, -1) != '/' ? '/' : ''));
define('K_TIMEZONE', (in_array($SETTINGS->timezone, array_keys($timezones)) ? $SETTINGS->timezone : 'UTC'));
define('K_THAI_TOPCHARS', true);
define('K_TCPDF_CALLS_IN_HTML', false);
define('K_TCPDF_THROW_EXCEPTION_ERROR', false);

// Not used, modify wrapper instead..
// content/**theme**/html/pdf-invoice/trade/wrapper.htm
// content/**theme**/html/pdf-invoice/personal/wrapper.htm
define('K_BLANK_IMAGE', '_mc_blank.png');
define('K_PATH_IMAGES', '');
define('PDF_HEADER_LOGO', '');
define('PDF_HEADER_LOGO_WIDTH', 0);
define('K_PATH_URL', '');
define('PDF_HEADER_TITLE', '');
define('PDF_HEADER_STRING', '');

?>