<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// LEGACY METHOD DATA
//==========================

$gateways = array(
  'paypal' => array(
    'status' => $METHODS->enablePP,
    'plain' => '',
    'html' => '',
    'info' => mc_cleanData($METHODS->paypal_info)
  ),
  'twocheckout' => array(
    'status' => $METHODS->enableTwoCheckout,
    'plain' => '',
    'html' => '',
    'info' => mc_cleanData($METHODS->twocheckout_info)
  ),
  'skrill' => array(
    'status' => $METHODS->enableMoneyBookers,
    'plain' => '',
    'html' => '',
    'info' => mc_cleanData($METHODS->moneybookers_info)
  ),
  'phone' => array(
    'status' => $METHODS->enablePhone,
    'plain' => mc_cleanData($METHODS->phone_plain),
    'html' => mc_cleanData($METHODS->phone_html),
    'info' => mc_cleanData($METHODS->phone_info)
  ),
  'cheque' => array(
    'status' => $METHODS->enableCheque,
    'plain' => mc_cleanData($METHODS->cheque_plain),
    'html' => mc_cleanData($METHODS->cheque_html),
    'info' => mc_cleanData($METHODS->cheque_info)
  ),
  'bank' => array(
    'status' => $METHODS->enableBank,
    'plain' => mc_cleanData($METHODS->bank_plain),
    'html' => mc_cleanData($METHODS->bank_html),
    'info' => mc_cleanData($METHODS->bank_info)
  ),
  'cod' => array(
    'status' => $METHODS->enableCash,
    'plain' => mc_cleanData($METHODS->cash_plain),
    'html' => mc_cleanData($METHODS->cash_html),
    'info' => mc_cleanData($METHODS->cash_info)
  )
);

?>