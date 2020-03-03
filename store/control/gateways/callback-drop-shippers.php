<?php

/* CALLBACK OPS - DROP SHIPPING
----------------------------------*/

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Are there any drop shippers?
if (!in_array($SALE_ORDER->paymentMethod, array('bank','cod','cheque','phone'))) {

  if (mc_rowCount('dropshippers') > 0) {

    $GATEWAY->writeLog($SALE_ID, 'Checking products for drop shipping..');

    // Load drop ship class..
    include(PATH . 'control/classes/class.drop.php');
    $DSP           = new dropShipper();
    $DSP->settings = $SETTINGS;
    $DSP->lang     = $drop_shipping;
    $DSP->sale     = $SALE_ORDER;
    $shipInfo      = array();

    // LOAD PURCHASES WHERE PRODUCTS HAVE DROP SHIPPERS SET..
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
         `" . DB_PREFIX . "products`.`id` AS `pid`,
         `" . DB_PREFIX . "purchases`.`id` AS `prid`
         FROM `" . DB_PREFIX . "purchases`
         LEFT JOIN `" . DB_PREFIX . "products`
         ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
         WHERE `" . DB_PREFIX . "purchases`.`saleID` = '{$SALE_ORDER->id}'
         AND `" . DB_PREFIX . "purchases`.`productType` = 'physical'
         AND `" . DB_PREFIX . "purchases`.`saleConfirmation` = 'yes'
         AND `" . DB_PREFIX . "products`.`dropshipping` > 0
         ");
    while ($PHYS = mysqli_fetch_object($q)) {
      $SHIPPER      = mc_getTableData('dropshippers', 'id', $PHYS->dropshipping);
      $DSP->purprod = $PHYS;
      if (isset($SHIPPER->id)) {
        if (!isset($shipInfo[$PHYS->dropshipping])) {
          $em  = array_map('trim', explode(',', $SHIPPER->emails));
          $ot  = array();
          if (count($em) > 1) {
            $ot = $em;
            unset($ot[0]);
          }
          if (isset($em[0])) {
            $shipInfo[$PHYS->dropshipping] = array(
              'name' => $SHIPPER->name,
              'email' => $em[0],
              'enabled' => $SHIPPER->enable,
              'ship-status' => $SHIPPER->salestatus,
              'add-emails' => (!empty($ot) ? implode(',',$ot) : ''),
              'flags' => array(
                'statuses' => ($SHIPPER->status ? explode(',', $SHIPPER->status) : array('all')),
                'methods' => ($SHIPPER->method ? explode(',', $SHIPPER->method) : array('all'))
              )
            );
          }
        }
        if (isset($shipInfo[$PHYS->dropshipping])) {
          if (!isset($shipInfo[$PHYS->dropshipping]['items'])) {
            $shipInfo[$PHYS->dropshipping]['items'] = array();
            $shipInfo[$PHYS->dropshipping]['items'][] = $DSP->shiporder();
          } else {
            $shipInfo[$PHYS->dropshipping]['items'][] = $DSP->shiporder();
          }
        }
      }
    }

    if (!empty($shipInfo)) {
      $GATEWAY->writeLog($SALE_ID, 'Products found that require drop shipping, processing...');
      $s = 0;
      foreach (array_keys($shipInfo) AS $sKey) {
        if (!empty($shipInfo[$sKey]['items'])) {
          $sendDSOn      = 'yes';
          // Check flags..
          if (!in_array('all', $shipInfo[$sKey]['flags']['statuses'])) {
            if (!empty($shipInfo[$sKey]['flags']['statuses']) && !in_array($SALE_ORDER->paymentStatus, $shipInfo[$sKey]['flags']['statuses'])) {
              $sendDSOn = 'no';
            }
          }
          if (!in_array('all', $shipInfo[$sKey]['flags']['methods'])) {
            if (!empty($shipInfo[$sKey]['flags']['methods']) && !in_array($SALE_ORDER->paymentMethod, $shipInfo[$sKey]['flags']['methods'])) {
              $sendDSOn = 'no';
            }
          }
          switch($shipInfo[$sKey]['enabled']) {
            case 'yes':
              $GATEWAY->writeLog($SALE_ID, count($shipInfo[$sKey]['items']) . ' products found for enabled drop shipper "' . $shipInfo[$sKey]['name'] . '".');
              if ($sendDSOn == 'yes') {
                ++$s;
                $GATEWAY->writeLog($SALE_ID, 'Instructions being sent via email to ship the following: ' . mc_defineNewline() . mc_defineNewline() . implode(mc_defineNewline(), $shipInfo[$sKey]['items']));
                $sbj = str_replace('{store}', $SETTINGS->website, $msg_emails29);
                if (file_exists(MCLANG . 'email-templates/drop-shipping-confirmation-' . $sKey . '.txt')) {
                  $msg = MCLANG . 'email-templates/drop-shipping-confirmation-' . $sKey . '.txt';
                } else {
                  $msg = MCLANG . 'email-templates/drop-shipping-confirmation.txt';
                }
                $MCMAIL->addTag('{ITEMS}', implode(mc_defineNewline(), $shipInfo[$sKey]['items']));
                $MCMAIL->addTag('{SHIP_METHOD}', $DSP->shipmethod());
                $MCMAIL->addTag('{SHIP_ADDRESS}', $DSP->shipaddress());
                // Include everything else in product / purchase array..
                foreach ((array) $PHYS AS $pk => $pv) {
                  $MCMAIL->addTag('{' . strtoupper($pk) . '}', $pv);
                }
                // Include everything else in sale array..
                foreach ((array) $SALE_ORDER AS $sk => $sv) {
                  $MCMAIL->addTag('{' . strtoupper($sk) . '}', $sv);
                }
                $MCMAIL->sendMail(array(
                  'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
                  'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
                  'to_email' => $shipInfo[$sKey]['email'],
                  'to_name' => $shipInfo[$sKey]['name'],
                  'subject' => $sbj,
                  'replyto' => array(
                    'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
                    'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
                  ),
                  'template' => $msg,
                  'add-emails' => $shipInfo[$sKey]['add-emails'],
                  'alive' => 'yes',
                  'language' => $SETTINGS->languagePref
                ));
                $GATEWAY->writeLog($SALE_ID, 'Email(s) sent to: ' . $shipInfo[$sKey]['email'] . ($shipInfo[$sKey]['add-emails'] ? ' & ' . $shipInfo[$sKey]['add-emails'] : ''));
              } else {
                $GATEWAY->writeLog($SALE_ID, 'Payment status and/or method restrictions are in place for drop shipper "' . $shipInfo[$sKey]['name'] . '" and the sale does not match those restrictions. No emails will be sent.');
              }
              // Write status for order..
              $string  = str_replace('{drop}', $shipInfo[$sKey]['name'], $drop_shipping[1]);
              $string .= mc_defineNewline() . mc_defineNewline() . $MCMAIL->plainWrap(array('template' => $msg));
              if ($SALE_ORDER->paymentStatus != $shipInfo[$sKey]['ship-status']) {
                $GATEWAY->writeLog($SALE_ID, 'Drop shipping status change: Old status = ' . mc_statusText($SALE_ORDER->paymentStatus) . ', New status = ' . mc_statusText($shipInfo[$sKey]['ship-status']));
                $GATEWAY->writeOrderStatus(
                  $SALE_ORDER->id,
                  trim($string),
                  $shipInfo[$sKey]['ship-status']
                );
              }
              break;
            case 'no':
              $GATEWAY->writeLog($SALE_ID, count($shipInfo[$sKey]['items']) . ' products found for drop shipper "' . $shipInfo[$sKey]['name'] . '" but this drop shipper is disabled. No emails will be sent.');
              break;
          }
        } else {
          $GATEWAY->writeLog($SALE_ID, 'Product items array was empty when processing for drop shipper "' . $shipInfo[$sKey]['name'] . '". No emails will be sent.');
        }
      }

      if ($s > 0) {
        $MCMAIL->smtpClose();
      }
    }

    $GATEWAY->writeLog($SALE_ID, 'Drop shipping operations completed');

  }

} else {

  $GATEWAY->writeLog($SALE_ID, 'Payment method none gateway (' . mc_paymentMethodName($SALE_ORDER->paymentMethod) . '), so drop shipping routines ignored. If required, trigger email manually via admin CP');

}

?>