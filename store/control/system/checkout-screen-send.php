<?php

if (!isset($_GET['checkout-pay'])) {
  exit;
}

include(MCLANG . 'payment.php');

$fields   = array();
$chop     = explode('-', $_GET['checkout-pay']);
$saleID   = (isset($chop[0]) ? (int) $chop[0] : '');
$saleCode = (isset($chop[1]) && ctype_alnum($chop[1]) ? $chop[1] : '');

if ($saleID && $saleCode) {
  // GET ORDER..
  $HDLR           = new paymentHandler();
  $HDLR->rwr      = $MCRWR;
  $HDLR->settings = $SETTINGS;
  $SALE_ORDER     = $HDLR->getOrderInfo($saleCode, $saleID);

  // CHECK IF ORDER IS VALID..
  if (isset($SALE_ORDER->id)) {
    $paymentMethod = $SALE_ORDER->paymentMethod;
    // Check method exists for this payment gateway..
    if (isset($mcSystemPaymentMethods[$paymentMethod]['lang']) && file_exists(PATH . 'control/gateways/methods/class.' . $paymentMethod . '.php')) {
      include(PATH . 'control/gateways/methods/class.' . $paymentMethod . '.php');
      if (class_exists($paymentMethod)) {
        $rdrGateways      = array();
        $GW               = new $paymentMethod();
        $GW->settings     = $SETTINGS;
        $GW->modules      = $mcSystemPaymentMethods;
        $GW->gateway      = $paymentMethod;
        $GW->gateway_name = $mcSystemPaymentMethods[$paymentMethod]['lang'];
        $GW->gateway_url  = $mcSystemPaymentMethods[$paymentMethod]['web'];
        $fields           = $GW->loadGatewayFields(mc_detectSSLConnection($SETTINGS), $saleCode, $saleID, $public_checkout18, (in_array($paymentMethod, $rdrGateways) ? true : false));
      } else {
        die('Class "' . mc_safeHTML($paymentMethod) . '" does NOT exist in "<b>' . PATH . 'control/gateways/methods/class.' . mc_safeHTML($paymentMethod) . '.php</b>"');
      }
    } else {
      die('Aborted: File "<b>' . PATH . 'control/gateways/methods/class.' . mc_safeHTML($paymentMethod) . '.php</b>" does NOT exist!');
    }

    // Store redirect session vars for certain gateways..
    if (in_array($paymentMethod, array('sectrade'))) {
      $url = (mc_detectSSLConnection($SETTINGS) == 'yes' ? str_replace('http://', 'https://', $SETTINGS->ifolder) . '/' : $SETTINGS->ifolder . '/');
      $_SESSION['mc_checkrdr_' . mc_encrypt(mc_encrypt(SECRET_KEY))] = $url . 'index.php?gw=' . $saleID . '-' . $saleCode;
    }

    // Do we have something to send to the gateway..
    if (!empty($fields)) {
      switch($fields[0]) {
        case 'form':
          $GW->writeLog($saleID, 'Order started (' . $mcSystemPaymentMethods[$paymentMethod]['lang'] . '). Form data being sent: ' . PHP_EOL . $fields[1]);
          $GW->writeLog($saleID, 'Awaiting callback from gateway..');
          break;
        case 'refresh':
        case 'redirect':
          $GW->writeLog($saleID, 'Order started (' . $mcSystemPaymentMethods[$paymentMethod]['lang'] . '). Redirect url: ' . PHP_EOL . $fields[1]);
          $GW->writeLog($saleID, 'Awaiting callback from gateway..');
          break;
      }

      $MCCART->clearCart();

      $tpl = mc_getSavant();
      $tpl->assign('TITLE', $public_checkout51);
      $tpl->assign('TXT', array(
        $public_checkout51,
        ($fields[0] == 'form' ? $public_checkout55 : str_replace('{url}',$fields[1],$chk_payment_finish_other[1]))
      ));
      $tpl->assign('DATA', $fields);

      // Global..
      include(PATH . 'control/system/global.php');

      $tpl->display(THEME_FOLDER . '/checkout-connect.tpl.php');

    } else {

      $GW->writeLog($saleID, '[Fatal Error] The field data array was empty, there is nothing else to do. Payment terminated.<br><br>
      If you are using a custom class, check the fields array contains data. See the docs for more information.');
      die('[Fatal Error] Check error log, process terminated');

    }
  } else {

    header("Location: " . $MCRWR->url(array('cart-error')));
    exit;

  }
} else {

  header("Location: " . $MCRWR->url(array('cart-error')));
  exit;

}

?>