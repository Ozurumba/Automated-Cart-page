<?php

/* AFFILIATE CALLBACK OPS - MAIAN AFFILIATE
-----------------------------------------------------*/

if (defined('CALLBACK_AFFILIATE')) {

  if (isset($cfg['affiliate']['url'], $cfg['affiliate']['product'],
    $cfg['affiliate']['logs'], $cfg['affiliate']['api']) && $cfg['affiliate']['url'] && 
    $cfg['affiliate']['product'] > 0 && $cfg['affiliate']['api']
  ) {

    // LOG..
    $GATEWAY->writeLog($SALE_ID, 'Affiliate sale code is active (' . MSW_AFF_SALE_CODE . '). Starting affiliate callback ops. Refer to affiliate logs if enabled.');

    // PREPARE ARRAY
    $data = array(
      'apikey' => $cfg['affiliate']['api'],
      'affiliate' => MSW_AFF_SALE_CODE,
      'commission' => array(
        'product' => $cfg['affiliate']['product'],
        'saletotal' => $SALE_ORDER->subTotal,
        'commtotal' => number_format(mc_formatPrice($cfg['affiliate']['commission'] * $SALE_ORDER->subTotal / 100), 2, '.', ''),
        'notes' => $SETTINGS->website . ' (#' . $SALE_ID . ')',
        'ip' => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '')
      )
    );

    // SEND COMMISSION TO AFFILIATE SYSTEM
    $AFF->log('Preparing to send commission data to Maian Affiliate setup: ' . print_r($data, true));
    $r = $AFF->ping($data);
    if (isset($r['status']) && $r['status'] == 'ok') {
      $AFF->log('Affiliate commission successfully sent to Maian Affiliate setup for sale ID: ' . $SALE_ID);
    } else {
      $AFF->log('Referral failed for sale ID ' . $SALE_ID . '. Response: '. print_r($r, true));
    }

    $GATEWAY->writeLog($SALE_ID, 'Affiliate operations completed');

  } else {
    $AFF->log('One or more parameters not set in Maian Cart system. System > General Settings > Settings Menu > Maian Affiliate Integration. Commission callback ignored.');
  }

}

?>