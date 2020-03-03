<?php

//-------------------------------------------------------------
// STRUCTURED META DATA
// Facebook Open Graph
// Twitter Cards
// Google+ Schema.org
//-------------------------------------------------------------


if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Defaults..
if (!isset($twPar['twitter']['username'])) {
  $twPar = $MCSOCIAL->params('twitter');
}
$mc_structDataHtml = $MCSOCIAL->structData(array(
  'fb' => array(
    'site' => mc_safeHTML($SETTINGS->website),
    'url' => (isset($mc_structUrl) && $mc_structUrl ? $mc_structUrl : $SETTINGS->ifolder),
    'title' => (isset($mc_structTitle) && $mc_structTitle ? $mc_structTitle : mc_safeHTML($SETTINGS->website)),
    'desc' => (isset($mc_structDesc) && $mc_structDesc ? $mc_structDesc : mc_safeHTML($SETTINGS->metaDesc)),
    'image' => (isset($mc_structImage) && $mc_structImage ? $mc_structImage : $SETTINGS->ifolder . '/' . THEME_FOLDER . '/images/social-facebook.png'),
    'img-path' => (isset($mc_structImageRaw) && $mc_structImageRaw ? $mc_structImageRaw : PATH . THEME_FOLDER . '/images/social-facebook.png')
  ),
  'tw' => array(
    'user' => (isset($twPar['twitter']['username']) ? $twPar['twitter']['username'] : ''),
    'title' => (isset($mc_structTitle) && $mc_structTitle ? $mc_structTitle : mc_safeHTML($SETTINGS->website)),
    'desc' => (isset($mc_structDesc) && $mc_structDesc ? $mc_structDesc : mc_safeHTML($SETTINGS->metaDesc)),
    'image' => (isset($mc_structImage) && $mc_structImage ? $mc_structImage : $SETTINGS->ifolder . '/' . THEME_FOLDER . '/images/social-twitter.png')
  ),
  'gg' => array(
    'title' => (isset($mc_structTitle) && $mc_structTitle ? $mc_structTitle : mc_safeHTML($SETTINGS->website)),
    'desc' => (isset($mc_structDesc) && $mc_structDesc ? $mc_structDesc : mc_safeHTML($SETTINGS->metaDesc)),
    'image' => (isset($mc_structImage) && $mc_structImage ? $mc_structImage : $SETTINGS->ifolder . '/' . THEME_FOLDER . '/images/social-google.png')
  ))
);

?>