<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Cache control..
$mCache = $MCCACHE->cache_options['cache_dir'] . '/payment-methods' . $MCCACHE->cache_options['cache_ext'];
if ($MCCACHE->cache_options['cache_enable'] == 'yes' && file_exists($mCache) && $MCCACHE->cache_exp($MCCACHE->cache_time($mCache)) == 'load') {
  $mcSystemPaymentMethods = $MCCACHE->cache_unserialize($mCache);
} else {
  $mcSystemPaymentMethods = array();
  $qM = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "methods` " . (!defined('ADMIN_LOADER') ? 'WHERE `status` = \'yes\'' : '') . " ORDER BY `display`") or die(mc_MySQLError(__LINE__, __FILE__));
  while ($GPM = mysqli_fetch_object($qM)) {
    // PAYPAL..
    $mcSystemPaymentMethods[$GPM->method] = array(
      'ID' => $GPM->id,
      'lang' => mc_cleanData($GPM->display),
      'enable' => $GPM->status,
      'img' => $GPM->image,
      'docs' => $GPM->docs,
      'web' => $GPM->webpage,
      'live' => $GPM->liveserver,
      'sandbox' => $GPM->sandboxserver,
      'plain' => mc_cleanData($GPM->plaintext),
      'html' => mc_cleanData($GPM->htmltext),
      'info' => mc_cleanData($GPM->info),
      'redirect' => $GPM->redirect,
      'statuses' => $GPM->statuses,
      'default' => $GPM->defmeth,
      'viewtype' => $GPM->viewtype
    );
  }
  if (!empty($mcSystemPaymentMethods)) {
    $MCCACHE->cache_file($mCache, $MCCACHE->cache_serialize($mcSystemPaymentMethods));
  }
}

?>