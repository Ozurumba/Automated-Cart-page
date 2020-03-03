<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// PAYMENT METHOD PARAMS
//==========================

if (property_exists($METHODS, 'id')) {

  @mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "methods_params` (`method`, `param`, `value`) VALUES
  ('paypal', 'email', '{$METHODS->ppEmail}'),
  ('paypal', 'pagestyle', '{$METHODS->ppPageStyle}'),
  ('paypal', 'locale', '{$METHODS->ppLocale}'),
  ('twocheckout', 'account', '{$METHODS->twoCheckoutAccNumber}'),
  ('twocheckout', 'secret', '{$METHODS->twoCheckoutSecretWord}'),
  ('twocheckout', 'language', 'EN'),
  ('skrill', 'email', '{$METHODS->mbEmail}'),
  ('skrill', 'language', '{$METHODS->mbLanguage}'),
  ('skrill', 'logo', '{$METHODS->mbLogo}'),
  ('skrill', 'secret', '{$METHODS->mbSecretWord}'),
  ('payfast', 'merchant-id', ''),
  ('payfast', 'merchant-key', ''),
  ('payfast', 'validation-url', 'https://www.payfast.co.za/eng/query/validate'),
  ('payfast', 'validation-sand-url', 'https://sandbox.payfast.co.za/eng/query/validate'),
  ('cardsave', 'pre-share-key', ''),
  ('cardsave', 'merchant-id', ''),
  ('cardsave', 'password', ''),
  ('sagepay', 'vendor', ''),
  ('sagepay', 'encryption', ''),
  ('sagepay', 'xor-password', ''),
  ('worldpay', 'install-id', ''),
  ('worldpay', 'callback-pw', ''),
  ('cardstream', 'merchant-id', ''),
  ('liqpay', 'merchant-id', ''),
  ('liqpay', 'signature', ''),
  ('authnet', 'login-id', ''),
  ('authnet', 'transaction-key', ''),
  ('authnet', 'response-key', ''),
  ('paymate', 'merchant-id', ''),
  ('icepay', 'merchant-id', ''),
  ('icepay', 'language', 'EN'),
  ('icepay', 'encryption-code', ''),
  ('ccnow', 'login-id', ''),
  ('ccnow', 'language', 'en'),
  ('ccnow', 'secret-key', ''),
  ('ccnow', 'activation-key', ''),
  ('paytrail', 'merchant-id', ''),
  ('paytrail', 'language', ''),
  ('paytrail', 'auth-hash', ''),
  ('payvector', 'pre-share-key', ''),
  ('payvector', 'merchant-id', ''),
  ('payvector', 'password', ''),
  ('sectrade', 'site-reference', ''),
  ('sectrade', 'notify-password', ''),
  ('sectrade', 'merchant-password', ''),
  ('paysense', 'pre-share-key', ''),
  ('paysense', 'merchant-id', ''),
  ('paysense', 'password', '')");

  mc_upgradeLog('Added new payment methods parameters');

}

?>