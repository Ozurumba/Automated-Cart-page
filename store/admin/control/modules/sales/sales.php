<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/sales-export.php');
include(MCLANG . 'sales/view-sales.php');
include(MCLANG . 'sales/sales-view.php');
include(MCLANG . 'sales/sales-incomplete.php');
include(MCLANG . 'catalogue/product-manage.php');

if (isset($_GET['ssdel'])) {
  $MCSALE->deleteOrderSale($_GET['ssdel']);
  echo $JSON->encode(array(
    'OK'
  ));
  exit;
}

if (isset($_GET['export'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL         = new mcDownload();
  $MCSALE->dl = $DL;
  $MCSALE->exportSalesToCSV($_GET['export']);
  exit;
}

if (isset($_GET['ordered'])) {
  // Maian Cube / Guardian manual triggers..
  if (isset($_POST['api-trigger']) && in_array($_POST['api-trigger'], array('cube','guardian'))) {
    $sale = (isset($_POST['api-sale']) ? (int) $_POST['api-sale'] : '0');
    if ($sale > 0) {
      $SALE_ORDER = mc_getTableData('sales', 'id', $sale);
      if (isset($SALE_ORDER->id)) {
        $gatewayFlagVar = (array_key_exists($SALE_ORDER->paymentMethod, $mcSystemPaymentMethods) ? $SALE_ORDER->paymentMethod : 'fail');
        if ($gatewayFlagVar != 'fail') {
          // LOAD PAYMENT CLASS..
          include(REL_PATH . 'control/gateways/class.handler.php');
          switch($SALE_ORDER->paymentMethod) {
            case 'cod':
            case 'phone':
            case 'bank':
            case 'cheque':
              include(REL_PATH . 'control/gateways/methods/class.other.php');
              $GATEWAY = new otherpayment();
              break;
            case 'account':
              include(REL_PATH . 'control/gateways/methods/class.account.php');
              $GATEWAY  = new onAccount();
              break;
            default:
              include(REL_PATH . 'control/gateways/methods/class.' . $gatewayFlagVar . '.php');
              $GATEWAY  = new $gatewayFlagVar();
              break;
          }
          $SALE_ORDER->paymentMethod = 'manualtrigger';
          $GATEWAY->gateway_name     = $mcSystemPaymentMethods[$gatewayFlagVar]['lang'];
          $GATEWAY->gateway_url      = $mcSystemPaymentMethods[$gatewayFlagVar]['web'];
          $GATEWAY->settings         = $SETTINGS;
          $GATEWAY->modules          = $mcSystemPaymentMethods;
          $GATEWAY->gateway          = $gatewayFlagVar;
          $SALE_CODE                 = $SALE_ORDER->buyCode;
          $SALE_ID                   = $SALE_ORDER->id;
          $GATEWAY->writeLog($SALE_ID, 'Starting admin manual trigger to Maian ' . ucfirst($_POST['api-trigger']) . ' API. Triggered by User: '. $sysCartUser[0]);
          include(REL_PATH . 'control/gateways/callback-' . $_POST['api-trigger'] . '.php');
          $apisent = true;
        } else {
          header("Location: index.php?p=sales&ordered=" . (int) $_GET['ordered']);
          exit;
        }
      } else {
        header("Location: index.php?p=sales&ordered=" . (int) $_GET['ordered']);
        exit;
      }
    } else {
      header("Location: index.php?p=sales&ordered=" . (int) $_GET['ordered']);
      exit;
    }
  }
  // Resend to drop shippers..
  if (!isset($_POST['api-trigger']) && isset($_POST['process'])) {
    if (!empty($_POST['purchase'])) {
      mc_memoryLimit();
      $s = 0;
      include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
      include(GLOBAL_PATH . 'control/classes/class.drop.php');
      $DSP           = new dropShipper();
      $DSP->settings = $SETTINGS;
      $DSP->lang     = $drop_shipping;
      $shipInfo      = array();
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
           `" . DB_PREFIX . "products`.`id` AS `pid`,
           `" . DB_PREFIX . "purchases`.`id` AS `prid`
           FROM `" . DB_PREFIX . "purchases`
           LEFT JOIN `" . DB_PREFIX . "products`
           ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
           WHERE `" . DB_PREFIX . "purchases`.`id` IN(" . mc_safeSQL(implode(',',$_POST['purchase'])) .")
           AND `" . DB_PREFIX . "purchases`.`productType` = 'physical'
           AND `" . DB_PREFIX . "purchases`.`saleConfirmation` = 'yes'
           AND `" . DB_PREFIX . "products`.`dropshipping` > 0
           ");
      while ($PHYS = mysqli_fetch_object($q)) {
        $SHIPPER      = mc_getTableData('dropshippers', 'id', $PHYS->dropshipping);
        $SALE         = mc_getTableData('sales', 'id', $PHYS->saleID);
        $DSP->sale    = $SALE;
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
                'sale' => $SALE->id,
                'status' => $SALE->paymentStatus,
                'ship-status' => $SHIPPER->salestatus,
                'add-emails' => (!empty($ot) ? implode(',',$ot) : '')
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
        foreach (array_keys($shipInfo) AS $sKey) {
          if (!empty($shipInfo[$sKey]['items'])) {
            ++$s;
            $sbj = str_replace('{store}', $SETTINGS->website, $msg_emails29);
            if (file_exists(LANG_PATH . 'drop-shipping-confirmation-' . $sKey . '.txt')) {
              $msg = LANG_PATH . 'drop-shipping-confirmation-' . $sKey . '.txt';
            } else {
              $msg = LANG_PATH . 'drop-shipping-confirmation.txt';
            }
            $MCMAIL->addTag('{ITEMS}', implode(mc_defineNewline(), $shipInfo[$sKey]['items']));
            $MCMAIL->addTag('{SHIP_METHOD}', $DSP->shipmethod());
            $MCMAIL->addTag('{SHIP_ADDRESS}', $DSP->shipaddress());
            // Include everything else in product / purchase array..
            foreach ((array) $PHYS AS $pk => $pv) {
              $MCMAIL->addTag('{' . strtoupper($pk) . '}', $pv);
            }
            // Include everything else in sale array..
            foreach ((array) $SALE AS $sk => $sv) {
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
            // Write status for order..
            $string  = str_replace('{drop}', $shipInfo[$sKey]['name'], $drop_shipping[1]);
            $string .= mc_defineNewline() . mc_defineNewline() . $MCMAIL->plainWrap(array('template' => $msg));
            if ($shipInfo[$sKey]['status'] != $shipInfo[$sKey]['ship-status']) {
              $MCSALE->writeOrderStatus(
                $shipInfo[$sKey]['sale'],
                trim($string),
                $shipInfo[$sKey]['status']
              );
            }
          }
        }
      }
      if ($s > 0) {
        $MCMAIL->smtpClose();
      }
      $OK = true;
    }
  }

  $pageTitle    = mc_cleanDataEntVars($msg_javascript84) . ': ' . $pageTitle;
  $loadiBox = true;

  include(PATH . 'templates/header.php');
  include(PATH . 'templates/sales/sale-products-view.php');
  include(PATH . 'templates/footer.php');
  exit;
}

if (isset($_GET['delete'])) {
  $cnt  = $MCSALE->deleteOrderSale();
  $OK   = true;
}

$pageTitle    = mc_cleanDataEntVars($msg_javascript84) . ': ' . $pageTitle;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales.php');
include(PATH . 'templates/footer.php');

?>
