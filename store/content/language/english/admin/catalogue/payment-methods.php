<?php

//------------------------------------------------------------------------------
// LANGUAGE FILE
// Edit with care. Make a backup first before making changes.
//
// [1] Apostrophes should be escaped. eg: it\'s christmas.
// [2] Take care when editing arrays as they are spread across multiple lines
//
// If you make a mistake and you see a parse error, revert to backup file
//------------------------------------------------------------------------------


$msg_paymethods                = 'Payfast';
$msg_paymethods2               = 'Merchant ID';
$msg_paymethods3               = 'Merchant Key';
$msg_paymethods4               = 'Nochex APC';
$msg_paymethods4               = 'Pre Share Key';
$msg_paymethods5               = 'Cardsave Password';
$msg_paymethods6               = 'CardSave';
$msg_paymethods7               = 'Vendor Username/ID';
$msg_paymethods8               = 'Encryption Password / Encryption Type';
$msg_paymethods9               = 'AES Encryption';
$msg_paymethods10              = 'XOR Encryption';
$msg_paymethods11              = 'Payment Page Banner (Optional)';
$msg_paymethods12              = 'UK Customer ID';
$msg_paymethods13              = 'UK Username';
$msg_paymethods14              = 'Payment Request Url';
$msg_paymethods15              = 'Payment Result Url';
$msg_paymethods16              = 'Payment Page Title (Optional)';
$msg_paymethods17              = 'Payment Page Footer (Optional)';
$msg_paymethods18              = 'Payment Page Description (Optional)';
$msg_paymethods19              = 'WorldPay Installation ID';
$msg_paymethods20              = 'WorldPay Callback Password';
$msg_paymethods21              = 'Remote Password';
$msg_paymethods22              = 'Display Logo on Payment Page';
$msg_paymethods23              = 'Verification Signature (<a href="#" onclick="mc_genString(\'sig\');return false">Auto Generate</a>)';
$msg_paymethods24              = 'Transaction Authentication Url';
$msg_paymethods25              = 'API Login ID';
$msg_paymethods26              = 'Transaction Key / Signature key';
$msg_paymethods27              = 'Order Status Preferences';
$msg_paymethods28              = 'Shared Secret Key';
$msg_paymethods29              = 'API Access Key / API Secret Key';
$msg_paymethods30              = 'Sandbox Transaction Authentication Url';
$msg_paymethods31              = 'Encryption Code';
$msg_paymethods32              = 'SHA1 Security Hash Key';
$msg_paymethods33              = 'API Activation Key/Hash Key';
$msg_paymethods34              = 'Account ID';
$msg_paymethods35              = 'Authentication Hash';
$msg_paymethods36              = 'Supported Payment Gateways';
$msg_paymethods37              = 'None Gateway Supported Payment Methods';
$msg_paymethods38              = 'Merchant Sub Account';

$callBack                      = array(
 'completed'  => 'Order Completed',
 'download'   => 'Order Completed - Downloads ONLY',
 'virtual'    => 'Order Completed - Gift Certificates ONLY',
 'pending'    => 'Order Pending - Payment not confirmed by Gateway',
 'cancelled'  => 'Order Cancelled - Cancelled response from Gateway',
 'refunded'   => 'Order Refunded - Refunded response from Gateway'
);

$gateway_errors                = array(
 'refresh'  => 'Once these functions are available refresh page to load gateway options.',
 'refresh2' => 'Once one of these functions are available refresh page to load gateway options.',
 'curl'     => 'The following functions are required to be enabled on your server for this gateway method to function (If you are unsure, contact your host).',
 'either'   => 'At least one of following functions are required to be enabled on your server for this gateway method to function (If you are unsure, contact your host).'
);

?>
