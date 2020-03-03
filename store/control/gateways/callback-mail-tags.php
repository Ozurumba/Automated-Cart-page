<?php

/*
  MAIL TAGS - Sent to all order emails
-----------------------------------------*/

if (!defined('PARENT') || !isset($SALE_ORDER->id)) {
  include(PATH.'control/system/headers/403.php');
  exit;
}

// General tags..
$MCMAIL->addTag('{ORDER_IP}', $SALE_ORDER->ipAddress);
$MCMAIL->addTag('{NAME}', mc_cleanData($SALE_ORDER->bill_1));
$MCMAIL->addTag('{GATEWAY_NAME}', (isset($mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['lang']) ? $mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['lang'] : 'N/A'));
$MCMAIL->addTag('{DOWNLOAD_ONLY}', (defined('DOWNLOAD_ONLY') ? $msg_script5 : $msg_script6));
$MCMAIL->addTag('{BILLING_ADDRESS}', $ORDER_ADDR['bill-address']);
$MCMAIL->addTag('{SHIPPING_ADDRESS}', $ORDER_ADDR['ship-address']);
$MCMAIL->addTag('{SHIPPING_PHONE}', mc_cleanData($SALE_ORDER->ship_8));
$MCMAIL->addTag('{SHIPPING_MAIL}', mc_cleanData($SALE_ORDER->ship_2));
$MCMAIL->addTag('{BILLING_PHONE}', mc_cleanData($SALE_ORDER->bill_8));
$MCMAIL->addTag('{BILLING_MAIL}', mc_cleanData($SALE_ORDER->bill_2));
$MCMAIL->addTag('{TRADE_SALE}', ($SALE_ORDER->type == 'trade' ? $msg_script5 : $msg_script6));
$MCMAIL->addTag('{INVOICE_NO}', mc_saleInvoiceNumber((isset($invoice) ? $invoice : $SALE_ORDER->invoiceNo), $SETTINGS));
$MCMAIL->addTag('{SUB_TOTAL}', $SALE_ORDER->subTotal);
$MCMAIL->addTag('{TOTAL}', $SALE_ORDER->grandTotal);
$MCMAIL->addTag('{RECIPIENT_NAME}', (isset($WS_ACC->name) ? mc_cleanData($WS_ACC->name) : ''));
$MCMAIL->addTag('{CURRENCY}', $SETTINGS->baseCurrency);
$MCMAIL->addTag('{WISHLIST_PURCHASE}', (defined('WISH_LIST_ACTIVE') ? $msg_script5 : $msg_script6));
$MCMAIL->addTag('{COUPON}', $GATEWAY->getCouponInfo($SALE_ORDER));
$MCMAIL->addTag('{PRODUCT_ORDER}', $GATEWAY->buildProductOrder($SALE_ID));
$MCMAIL->addTag('{TRANS_ID}', (isset($INCOMING['trans-id']) ? $INCOMING['trans-id'] : ''));
$MCMAIL->addTag('{ORDER_ID}', $SALE_ID);
$MCMAIL->addTag('{ORDER_URL}', $SETTINGS->ifolder . '/?vOrder=' . $SALE_ID . '-' . $SALE_CODE);
$MCMAIL->addTag('{ACCOUNT_URL}', $MCRWR->url(array('account')));
$MCMAIL->addTag('{SHIP}', mc_formatPrice($SALE_ORDER->shipTotal));
$MCMAIL->addTag('{TAX}', mc_formatPrice($SALE_ORDER->taxPaid));
$MCMAIL->addTag('{INSURANCE}', mc_formatPrice($SALE_ORDER->insuranceTotal));
$MCMAIL->addTag('{INV_STATUS}', (isset($INCOMING['inv-status']) ? $INCOMING['inv-status'] : ''));
$MCMAIL->addTag('{FRAUD_STATUS}', (isset($INCOMING['fraud-status']) ? $INCOMING['fraud-status'] : ''));
$MCMAIL->addTag('{INSTRUCTIONS}', (isset($mailInstructions) ? str_replace('{INVOICE_NO}',mc_saleInvoiceNumber($SALE_ORDER->invoiceNo, $SETTINGS),$mailInstructions) : ''));
$MCMAIL->addTag('{DISCOUNTS}', ($SALE_ORDER->couponTotal > 0 || $SALE_ORDER->globalDiscount > 0 || $SALE_ORDER->manualDiscount > 0 ? $msg_script5 : $msg_script6));

// Shipping rellated..
switch($SALE_ORDER->shipType) {
  case 'flat':
  case 'percent':
  case 'pert':
  case 'qtyr':
    $MCMAIL->addTag('{SHIP_METHOD}', $public_checkout96);
    break;
  default:
    $MCMAIL->addTag('{SHIP_METHOD}', mc_getShippingService(mc_getShippingServiceFromRate($SALE_ORDER->setShipRateID)));
    break;
}

// Wish list purchase only..
$wUrl = '';
if (defined('WISH_LIST_ACTIVE')) {
  $wUrl = $MCRWR->url(array(
    $MCRWR->config['slugs']['wls'] . '/' . md5($WS_ACC->id . $WS_ACC->email),
    'wls=' . md5($WS_ACC->id . $WS_ACC->email)
  ));
}
$MCMAIL->addTag('{WISHLIST_URL}', $wUrl);

?>