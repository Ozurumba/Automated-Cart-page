<?php

/* MAIAN AFFILIATE HANDLER - REFERRALS
   Enable / disable in Maian Cart admin CP
-------------------------------------------------*/

if (defined('AFF_SYS_LOADER') && defined('MSW_AFF_SYS_KEY') && defined('MSW_AFF_SYS_DUR')) {
  if (isset($cfg['affiliate']['url'], $cfg['affiliate']['product'],
    $cfg['affiliate']['logs'], $cfg['affiliate']['api']) && $cfg['affiliate']['url'] && 
    $cfg['affiliate']['product'] > 0 && $cfg['affiliate']['api']
  ) {
    // Keep only alphanumeric, underscores and hyphens
    $_GET[$cfg['affiliate']['param']] = preg_replace('/[^0-9a-zA-Z_\-]/', '', $_GET[$cfg['affiliate']['param']]);
    if (!isset($_GET['r'])) {
      $AFF->log('Maian Affiliate - Affiliate code detected: ' . $_GET[$cfg['affiliate']['param']]);
    }
    // Check affiliate cookie is set
    if (!isset($_GET['r']) && $AFF->getcookie() == '') {
      $AFF->log('Attempting to set affiliate cookie. Key: ' . mc_encrypt(SECRET_KEY . MSW_AFF_SYS_KEY));
      $AFF->setcookie();
      header("Location: " . $SETTINGS->ifolder . "/index.php?r=true&" . $cfg['affiliate']['param'] . "=" . $_GET[$cfg['affiliate']['param']]);
      exit;
    }
    // Only process if the cookie is set in the store..
    if ($AFF->getcookie()) {
      // Prepare array..
      $data = array(
        'apikey' => $cfg['affiliate']['api'],
        'affiliate' => $_GET[$cfg['affiliate']['param']],
        'referrer' => array(
          'url' => (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),
          'ip' => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''),
          'product' => $cfg['affiliate']['product']
        )
      );
      // Check referrer
      if ($data['referrer']['url']) {
        $AFF->log('Preparing to send referral data to Maian Affiliate setup: ' . print_r($data, true));
        // Send referral
        $r = $AFF->ping($data);
        // If response was ok, we reload
        if (isset($r['status']) && $r['status'] == 'ok') {
          $AFF->log('Referral successfully sent. Reloading ' . $SETTINGS->website . ' main page');
        } else {
          $AFF->log('Referral failed. Response: '. (is_array($r) ? print_r($r, true) : (string) $r));
        }
      } else {
        $AFF->log('Referrer does not exist: ' . print_r($data, true));
      }
    } else {
      $AFF->log('Cookie NOT set or not available. Could be caused by visitors browser settings. If cookies are disabled, affiliate codes will not work.');
    }
  } else {
    $AFF->log('One or more parameters not set in Maian Cart system. Settings > General > Other Options > Maian Affiliate');
  }
  // Reload..
  header("Location: " . $SETTINGS->ifolder);
  exit;
}

?>