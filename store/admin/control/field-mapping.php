<?php

$fieldMapTypes = array_map('trim', explode(',', $msg_productadd49));

if (!isset($fieldMapTypes[0])) {
  $fieldMapTypes[0] = 'Boolean';
}
if (!isset($fieldMapTypes[1])) {
  $fieldMapTypes[1] = 'Integer';
}
if (!isset($fieldMapTypes[2])) {
  $fieldMapTypes[2] = 'String';
}
if (!isset($fieldMapTypes[3])) {
  $fieldMapTypes[3] = 'Date';
}
if (!isset($fieldMapTypes[4])) {
  $fieldMapTypes[4] = 'Monetary Value';
}
if (!isset($fieldMapTypes[5])) {
  $fieldMapTypes[5] = 'Decimal';
}

$fieldMapping_vars = array(
  'attrName' => $msg_prodattributes9 . ' (' . $fieldMapTypes[2] . ')',
  'attrCost' => $msg_prodattributes10 . ' (' . $fieldMapTypes[4] . ')',
  'attrStock' => $msg_prodattributes5 . ' (' . $fieldMapTypes[1] . ')',
  'attrWeight' => $msg_prodattributes15 . ' (' . $fieldMapTypes[5] . ')'
);

$fieldMapping_products = array(
  'pName' => $msg_productadd4 . ' (' . $fieldMapTypes[2] . ')',
  'pTitle' => $msg_productadd75 . ' (' . $fieldMapTypes[2] . ')',
  'pDescription' => $msg_productadd6 . ' (' . $fieldMapTypes[2] . ')',
  'pShortDescription' => $msg_productadd64 . ' (' . $fieldMapTypes[2] . ')',
  'pMetaKeys' => $msg_productadd18 . ' (' . $fieldMapTypes[2] . ')',
  'pMetaDesc' => $msg_productadd19 . ' (' . $fieldMapTypes[2] . ')',
  'pTags' => $msg_productadd11 . ' (' . $fieldMapTypes[2] . ')',
  'rwslug' => $msg_newpages31 . ' (' . $fieldMapTypes[2] . ')',
  'pDownload' => $msg_productadd8 . ' (' . $fieldMapTypes[0] . ')',
  'pDownloadPath' => $msg_productadd9 . ' (' . $fieldMapTypes[2] . ')',
  'pDownloadLimit' => $msg_productadd10 . ' (' . $fieldMapTypes[1] . ')',
  'pVideo' => $msg_productadd29 . ' (' . $fieldMapTypes[2] . ')',
  'pVideo2' => $msg_admin_settings3_0[3] . ' (' . $fieldMapTypes[2] . ')',
  'pVideo3' => $msg_admin_settings3_0[4] . ' (' . $fieldMapTypes[2] . ')',
  'pStock' => $msg_productadd43 . ' (' . $fieldMapTypes[1] . ')',
  'minPurchaseQty' => $msg_productadd79 . ' (' . $fieldMapTypes[1] . ')',
  'maxPurchaseQty' => $msg_productadd88 . ' (' . $fieldMapTypes[1] . ')',
  'pStockNotify' => $msg_productadd20 . ' (' . $fieldMapTypes[1] . ')',
  'pVisits' => $msg_productadd31 . ' (' . $fieldMapTypes[1] . ')',
  'pCode' => $msg_productadd7 . ' (' . $fieldMapTypes[2] . ')',
  'pWeight' => $msg_productadd42 . ' (' . $fieldMapTypes[5] . ')',
  'pPrice' => $msg_productadd44 . ' (' . $fieldMapTypes[4] . ')',
  'pPurPrice' => $msg_admin_product3_0[15] . ' (' . $fieldMapTypes[4] . ')',
  //'pInsurance' => $msg_productadd44 . ' (' . $fieldMapTypes[4] . ')',
  'pOffer' => $msg_productadd39 . ' (' . $fieldMapTypes[4] . ')',
  'pOfferExpiry' => $msg_productadd40 . ' (' . $fieldMapTypes[3] . ')',
  'expiry' => $msg_admin_product3_0[0] . ' (' . $fieldMapTypes[3] . ')',
  'dropshipping' => $msg_admin_product3_0[2] . ' (' . $fieldMapTypes[1] . ')'
);

$fieldMapping_accounts = array(
  'name' => $msg_accimport5 . ' (' . $fieldMapTypes[2] . ')',
  'email' => $msg_accimport6 . ' (' . $fieldMapTypes[2] . ')',
  'pass' => $msg_accimport7 . ' (' . $fieldMapTypes[2] . ')'
);

?>