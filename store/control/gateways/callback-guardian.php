<?php

/* CALLBACK OPS - MAIAN GUARDIAN
----------------------------------*/

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

$guardian = array(
  'ids' => array(),
  'qty' => array()
);

// IF ENABLED, SEND DATA TO MAIAN GUARDIAN INSTALLATION..
if (!in_array($SALE_ORDER->paymentMethod, array('bank','cod','cheque','phone'))) {

  if ($SETTINGS->guardianUrl && $SETTINGS->guardianAPI) {

    // LOG..
    $GATEWAY->writeLog($SALE_ID, 'Initialising data to send to Maian Guardian installation @ ' . $SETTINGS->guardianUrl);

    // LOAD PURCHASES WHERE PRODUCTS HAVE GUARDIAN ID..
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT
         `" . DB_PREFIX . "products`.`pGuardian` AS `guardianID`,
         `" . DB_PREFIX . "purchases`.`productQty` AS `purQty`
         FROM `" . DB_PREFIX . "purchases`
         LEFT JOIN `" . DB_PREFIX . "products`
         ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
         WHERE `" . DB_PREFIX . "purchases`.`saleID` = '{$SALE_ORDER->id}'
         AND `" . DB_PREFIX . "purchases`.`productType` = 'physical'
         AND `" . DB_PREFIX . "purchases`.`saleConfirmation` = 'yes'
         AND `" . DB_PREFIX . "products`.`pGuardian` > 0
         GROUP BY `" . DB_PREFIX . "purchases`.`id`
         ORDER BY `" . DB_PREFIX . "purchases`.`productID`
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($PUR = mysqli_fetch_object($q)) {
      $guardian['ids'][] = $PUR->guardianID;
      $guardian['qty'][] = $PUR->purQty;
    }

    // BUILD DATA TO SEND TO MAIAN GUARDIAN INSTALLATION..
    if (!empty($guardian['ids'])) {

      // LOG..
      $GATEWAY->writeLog($SALE_ID, 'Products queried for Guardian IDs: ' . print_r($guardian, true));

      // FIELD DATA..
      if (count($guardian['ids']) > 1) {
        $fields = 'name=' . (defined('WISH_LIST_ACTIVE') ? mc_cleanData($WS_ACC->name) : $SALE_ORDER->bill_1);
        $fields .= '&email=' . (defined('WISH_LIST_ACTIVE') ? $WS_ACC->email : $SALE_ORDER->bill_2);
        $fields .= '&prodIDMulti=' . implode(',', $guardian['ids']);
        $fields .= '&licAmountMulti=' . implode(',', $guardian['qty']);
        $fields .= '&apiKey=' . $SETTINGS->guardianAPI;
        $fields .= '&timeline=' . str_replace('{store}', mc_cleanData($SETTINGS->website), $public_checkout118);
        $fields .= '&ip=' . $SALE_ORDER->ipAddress;
        $fields .= '&orderno=' . (isset($invoice) ? $invoice : $SALE_ORDER->invoiceNo);
      } else {
        $fields = 'name=' . (defined('WISH_LIST_ACTIVE') ? mc_cleanData($WS_ACC->name) : $SALE_ORDER->bill_1);
        $fields .= '&email=' . (defined('WISH_LIST_ACTIVE') ? $WS_ACC->email : $SALE_ORDER->bill_2);
        $fields .= '&prodID=' . $guardian['ids'][0];
        $fields .= '&licAmount=' . $guardian['qty'][0];
        $fields .= '&apiKey=' . $SETTINGS->guardianAPI;
        $fields .= '&timeline=' . str_replace('{store}', mc_cleanData($SETTINGS->website), $public_checkout118);
        $fields .= '&ip=' . $SALE_ORDER->ipAddress;
        $fields .= '&orderno=' . (isset($invoice) ? $invoice : $SALE_ORDER->invoiceNo);
      }

      // LOG..
      $GATEWAY->writeLog($SALE_ID, 'Sending data to Maian Guardian installation: ' . $fields);

      // SEND..
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $SETTINGS->guardianUrl);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_TIMEOUT, 120);
      $co = curl_exec($ch);
      curl_close($ch);

      // LOG..
      $GATEWAY->writeLog($SALE_ID, 'Data sending to Maian Guardian installation completed');

    } else {

      $GATEWAY->writeLog($SALE_ID, 'No products found from sale with Guardian IDs.');

    }
  }

}  else {

  $GATEWAY->writeLog($SALE_ID, 'Payment method none gateway (' . mc_paymentMethodName($SALE_ORDER->paymentMethod) . '), so Maian Guardian routines ignored. If required, trigger email manually via admin CP');

}

?>