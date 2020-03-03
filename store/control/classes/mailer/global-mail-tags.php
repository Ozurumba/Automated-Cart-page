<?php

if (!isset($MCMAIL) || !method_exists('mcMail', 'addTag')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

define('MAIL_SWITCH', 1);

/*
  CUSTOM MAIL HEADERS
  -------------------------------------------------------------------------------------------
  Custom mail headers should always start 'X-'. Array key is custom header name and array
  value is the custom header value. Example:

  $customMailHeaders = array(
    'X-Custom'  => 'Value',
    'X-Custom2' => 'Value 2'
  );
---------------------------------------------------------------------------------------------*/

$customMailHeaders = array();

/*
  GLOBAL MAIL TAGS
  ------------------------------------------
  Tags here are sent to ALL emails..
--------------------------------------------*/

$MCMAIL->smtp_host     = $SETTINGS->smtp_host;
$MCMAIL->smtp_user     = $SETTINGS->smtp_user;
$MCMAIL->smtp_pass     = $SETTINGS->smtp_pass;
$MCMAIL->smtp_port     = $SETTINGS->smtp_port;
$MCMAIL->debug         = $SETTINGS->smtp_debug;
$MCMAIL->smtp_sec      = $SETTINGS->smtp_security;
$MCMAIL->allowInsecure = $SETTINGS->smtp_insec;
$MCMAIL->protocol      = $SETTINGS->mailpref;

$MCMAIL->htmlelements = array(
  'lang' => (isset($mc_global[1]) ? $mc_global[1] : 'en'),
  'dir' => (isset($mc_global[0]) ? $mc_global[0] : 'ltr'),
  'charset' => $mail_charset
);

$MCMAIL->xheaders    = $customMailHeaders;
$MCMAIL->config      = (array) $SETTINGS;
$MCMAIL->htmltags    = (isset($mc_mailHTMLTags) ? $mc_mailHTMLTags : array());
$MCMAIL->mailSwitch  = (MAIL_SWITCH ? 'yes' : 'no');

$MCMAIL->addTag('{DATE}', date($SETTINGS->systemDateFormat));
$MCMAIL->addTag('{TIME}', date('H:iA'));
$MCMAIL->addTag('{WEBSITE_NAME}', $SETTINGS->website);
$MCMAIL->addTag('{WEBSITE_EMAIL}', $SETTINGS->email);
$MCMAIL->addTag('{WEBSITE_URL}', $SETTINGS->ifolder);
$MCMAIL->addTag('{ADMIN_FOLDER}', $SETTINGS->adminFolderName);
$MCMAIL->addTag('{IP}', mc_getRealIPAddr());

?>