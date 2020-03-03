<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// iBox on all pages because of menu..
$loadJS['ibox'] = 'load';

// Opt out..
if (isset($_GET['optOut']) && mswIsValidEmail($_GET['optOut'])) {
  include(MCLANG . 'emails.php');
  // Get account..
  $ACC = mc_getTableData('accounts', '`email`', mc_safeSQL($_GET['optOut']));
  if (isset($ACC->id)) {
    $MCACC->optOut($ACC->id);
    // Send email..
    if (NEWSLETTER_EMAIL_AUTO_RESPONDERS) {
      if (!defined('MAIL_SWITCH')) {
        include(PATH . 'control/classes/mailer/global-mail-tags.php');
      }
      $sbj = str_replace('{website}', $SETTINGS->website, $msg_emails21);
      $msg = MCLANG . 'email-templates/newsletter-unsubscribe.txt';
      $MCMAIL->addTag('{NAME}', $ACC->name);
      $MCMAIL->sendMail(array(
        'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
        'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
        'to_email' => $ACC->email,
        'to_name' => $ACC->name,
        'subject' => $sbj,
        'replyto' => array(
          'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
        ),
        'template' => $msg,
        'language' => $SETTINGS->languagePref
      ));
      $MCMAIL->smtpClose();
    }
  }
  header("Location: " . $MCRWR->url(array('opt-out')));
  exit;
}

// Clear recent view..
if (isset($_GET['clearView'])) {
  $_SESSION['recentlyViewedItems'] = array();
  unset($_SESSION['recentlyViewedItems']);
  // Clear saved if logged in..
  if (isset($loggedInUser['id'])) {
    $MCACC->updateRecent($loggedInUser['id']);
  }
  echo $MCJSON->encode(array(
    'OK'
  ));
  exit;
}

// Expire global discount..
if ($SETTINGS->globalDiscountExpiry != '0000-00-00') {
  if ($SETTINGS->globalDiscountExpiry <= date('Y-m-d')) {
    mc_resetGlobalExpiryDiscount();
  }
}

// Clear auto delete blog entries..
if ($cmd == 'home') {
  mc_clearBlogAutoDelete();
}

// Clear search..
if (!in_array($cmd, array(
  'advanced-search',
  'search'
))) {
  if (isset($_SESSION['store_SearchResults'])) {
    unset($_SESSION['store_SearchResults']);
  }
}

// Banner info into array..
$bannerSlider = '';
if (isset($loadJS['banners'])) {
  $bannerSlider = $MCSYS->bannerSlider(
    (defined('SLIDER_CAT') ? SLIDER_CAT : 0),
    (defined('SLIDER_HOME') ? true : false),
    (isset($loggedInUser['type']) ? $loggedInUser['type'] : '')
  );
  // If no banners, we don`t need to load banner code..
  if ($bannerSlider == '' ) {
    unset($loadJS['banners']);
  }
}

// Ticker loader..
$mc_ticker = $MCSYS->buildNewsTicker($cmd);
if ($mc_ticker) {
  $loadJS['params'] = array(
    0 => trim($msg_javascript424)
  );
} else {
  // If no news, we don`t need to load ticker code..
  if (isset($loadJS['ticker'])) {
    unset($loadJS['ticker']);
  }
}

// Build structured meta data..
include(PATH . 'control/system/meta-data.php');

$tpl = mc_getSavant();
// For relative loading..
if (defined('SAV_PATH')) {
  $tpl->addPath('template', SAV_PATH);
}
$tpl->assign('TEXT', array(
  $msg_public_header2,
  $msg_public_header23,
  $msg_public_header27,
  $msg_public_header31,
  (isset($_SESSION['store_SearchResults']) ? $_SESSION['store_SearchResults'] : $msg_public_header24),
  $msg_public_header5,
  $msg_public_header28,
  $msg_public_header29,
  $msg_public_header30,
  $msg_public_header33,
  $mc_header
));
$tpl->assign('OPTS', array(
  'currencies' => $MCSYS->headOptions('currencies', $loggedInUser, $currencyConversion),
  'languages' => $MCSYS->headOptions('lang', $loggedInUser, $currencyConversion, $systemLang)
));
$tpl->assign('SET_CURRENCY', (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency']) && in_array($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency'], array_keys($currencyConversion)) ? mc_safeHTML($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency']) : $SETTINGS->baseCurrency));
$tpl->assign('TITLE', mc_safeHTML($headerTitleText));
$tpl->assign('MODULES', $MCSYS->loadJSFunctions($loadJS));
$tpl->assign('RSS_LINK', $MCRWR->url(array(
  $MCRWR->config['slugs']['rsl'],
  'rss=latest'
)));
$tpl->assign('META_DESC', (isset($overRideMetaDesc) ? mc_safeHTML($overRideMetaDesc) : mc_safeHTML($SETTINGS->metaDesc)));
$tpl->assign('META_KEYS', (isset($overRideMetaKeys) ? mc_safeHTML($overRideMetaKeys) : mc_safeHTML($SETTINGS->metaKeys)));
$tpl->assign('CART_COUNT', $MCCART->cartCount());
$tpl->assign('OTHER_LINKS', $msg_public_header6);
$tpl->assign('BANNERS', $bannerSlider);
$tpl->assign('CONTACT_US', $msg_public_header7);
$tpl->assign('SHIPPING_RETURNS', $msg_public_header8);
$tpl->assign('TIME_ADJ', $timezones);
$tpl->assign('ABOUT_US', $msg_public_header9);
$tpl->assign('FEED_URL', $MCRWR->url(array(
  $MCRWR->config['slugs']['rsl'],
  'rss=latest'
)));
$tpl->assign('SPECIALS_URL', $MCRWR->url(array(
  $MCRWR->config['slugs']['sof'] . '/1',
  'p=special-offers'
)));
$tpl->assign('SITEMAP_URL', $MCRWR->url(array('sitemap')));
$tpl->assign('LATEST_URL', $MCRWR->url(array(
  $MCRWR->config['slugs']['lpr'] . '/1',
  'p=latest-products'
)));
$tpl->assign('CHECKOUT_URL', $MCRWR->url(array('checkpay')));
$tpl->assign('ADVANCED_SEARCH_URL', $MCRWR->url(array('advanced-search')));
$tpl->assign('WISH_LIST_URL', $MCRWR->url(array('wishlist')));
$tpl->assign('ACCOUNT_URL', $MCRWR->url(array('account')));
$tpl->assign('URL_I', $MCRWR->url(array('base_href')));
$tpl->assign('BREADCRUMBS', $MCSYS->breadcrumbs($breadcrumbs, $msg_public_header32));
$tpl->assign('NEWS_TICKER', $mc_ticker);
$tpl->assign('STRUCTURED_META_DATA', (isset($mc_structDataHtml) ? $mc_structDataHtml : ''));
$tpl->assign('GREETING_MSG', (isset($loggedInUser['id']) ? str_replace('{name}',mc_safeHTML($loggedInUser['name']),$mc_header[10]) : ''));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/header.tpl.php');

?>