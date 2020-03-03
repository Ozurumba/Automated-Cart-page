<?php

// STORE PATH..
$basePath = pathinfo(dirname(__FILE__));
define('PATH', $basePath['dirname'] . '/');
define('PARENT', 1);

// ERROR REPORTING..
include(PATH . 'control/classes/class.errors.php');
if (ERR_HANDLER_ENABLED) {
  register_shutdown_function('mcFatalErr');
  set_error_handler('mcErrorhandler');
}

// SET GATEWAY FLAG
$gatewayFlagVar = substr(basename(__file__), 0, -4);

// DATABASE CONNECTION..
include(PATH . 'control/connect.php');
include(PATH . 'control/functions.php');

// INIT..
include(PATH . 'control/system/init.php');

// CLASS..
if (file_exists(PATH . 'control/gateways/methods/class.' . $gatewayFlagVar . '.php')) {
  include(PATH . 'control/gateways/methods/class.' . $gatewayFlagVar . '.php');
} else {
  include(PATH . 'control/system/headers/401.php');
  exit;
}

// CHECK PAYMENT METHOD IS ENABLED..
if (!isset($mcSystemPaymentMethods[$gatewayFlagVar]['ID'])) {
  include(PATH . 'control/system/headers/200.php');
  exit;
}

// INITIATE GATEWAY CLASS..
$GATEWAY               = new $gatewayFlagVar();
$GATEWAY->gateway_name = $mcSystemPaymentMethods[$gatewayFlagVar]['lang'];
$GATEWAY->gateway_url  = $mcSystemPaymentMethods[$gatewayFlagVar]['web'];
$GATEWAY->settings     = $SETTINGS;
$GATEWAY->modules      = $mcSystemPaymentMethods;
$GATEWAY->gateway      = $gatewayFlagVar;

// LOAD INCOMING POST DATA..
$INCOMING = $GATEWAY->gatewayPostFields();

// LOAD INCOMING POST DATA..
$PARAMS = $GATEWAY->paymentParams($gatewayFlagVar);

// HANDLE CALLBACK..
if ($INCOMING['code-id']) {

  // GET BUY/SALE CODE AND ID, ALONG WITH MARKER..
  // Marker determines if sale came from Maian Cart..
  $DATA      = explode('-', $INCOMING['code-id']);
  $SALE_CODE = (isset($DATA[0]) && ctype_alnum($DATA[0]) ? $DATA[0] : '0');
  $SALE_ID   = (isset($DATA[1]) && (int) $DATA[1] > 0 ? $DATA[1] : '0');
  $MARKER    = (isset($DATA[2]) ? $DATA[2] : 'mswcart');

  // DEBUG..
  $GATEWAY->writeLog($SALE_ID, 'Received post callback from ' . $GATEWAY->gateway_name . ' payment server..writing post log..');
  $GATEWAY->writeLog($SALE_ID);

  // GET SALE / ORDER INFO..
  $SALE_ORDER = $GATEWAY->getOrderInfo($SALE_CODE, $SALE_ID);

  // START PROCESSING..
  if (isset($SALE_ORDER->id) && $MARKER == 'mswcart') {

    // DEBUG..
    $GATEWAY->writeLog($SALE_ID, 'Sale found in database');

    // GLOBAL MAIL TAGS..
    $MCMAIL->addTag('{GATEWAY_NAME}', $GATEWAY->gateway_name);
    $MCMAIL->addTag('{GATEWAY_URL}', $GATEWAY->gateway_url);
    $MCMAIL->addTag('{ORDER_IP}', $SALE_ORDER->ipAddress);
    $MCMAIL->addTag('{NAME}', mc_cleanData($SALE_ORDER->bill_1));

    // LOAD MAIL TEMPLATE FILE PREFERENCES..
    $MTEMP = $GATEWAY->mailTemplates();

    // ORDER ADDRESSES..
    $ORDER_ADDR = $GATEWAY->orderAddresses($SALE_ORDER);

    // VALIDATE PAYMENT..
    if ($GATEWAY->validateResponse($PARAMS, $SALE_ORDER) == 'ok') {

      // GET PAYMENT STATUS..
      $paymentStatus = strtolower($INCOMING['pay-status']);

      // DEBUG..
      $GATEWAY->writeLog($SALE_ID, 'Sale validated by ' . $GATEWAY->gateway_name . ' gateway. Payment status: ' . $paymentStatus);

      // ARE PENDING SALES TO BE HANDLED AS COMPLETED..
      if ($SETTINGS->pendingAsComplete == 'yes' && $paymentStatus == 'pending') {
        $paymentStatus = 'completed';
      }

      // ADJUST FOR COMPLETED PAYMENT..
      if ($paymentStatus == '0') {
        $paymentStatus = 'completed';
      }

      // UPDATE SALE / ORDER..
      if ($SALE_ORDER->saleConfirmation == 'no') {

        switch($paymentStatus) {

          //==========================================
          // GATEWAY CALLBACK => COMPLETED PAYMENT
          //==========================================

          case 'completed':
            if (($INCOMING['amount'] >= $SALE_ORDER->grandTotal) && ($INCOMING['currency'] == $SETTINGS->baseCurrency)) {

              // LOAD CALLBACK TEMPLATE..
              include(PATH . 'control/gateways/callback-completed.php');

              // MAIAN CUBE HANDLER..
              include(PATH . 'control/gateways/callback-cube.php');

              // MAIAN GUARDIAN HANDLER..
              include(PATH . 'control/gateways/callback-guardian.php');

              // CUSTOM OPS..
              include(PATH . 'control/gateways/callback-custom.php');

            } else {

              // DEBUG..
              $GATEWAY->writeLog($SALE_ID, 'Currency and/or price amount did not match values in database. Possible fraud. Database (' . $SALE_ORDER->grandTotal . ',' . $SETTINGS->baseCurrency . '), Received (' . $INCOMING['amount'] . ',' . $INCOMING['currency'] . '). If not fraud, check tax is not enabled in gateway settings.');

            }
            break;

          //==========================================
          // GATEWAY CALLBACK => PENDING PAYMENT
          // NOT currently supported by Payment Sense
          //==========================================

          case 'pending':

            // LOAD CALLBACK TEMPLATE..
            include(PATH . 'control/gateways/callback-pending.php');

            break;

          //==========================================
          // GATEWAY CALLBACK => REFUNDED PAYMENT
          // NOT currently supported by Payment Sense
          //==========================================

          case 'refunded':

            // LOAD CALLBACK TEMPLATE..
            include(PATH . 'control/gateways/callback-refunded.php');

            break;

          //==========================================
          // GATEWAY CALLBACK => OTHER OPTIONS
          // May be added in future versions
          //==========================================

          case '4':
          case '5':
          case '20':
          case '30':
          default:
            // DEBUG..
            $GATEWAY->writeLog($SALE_ID, 'Received action not currently supported. (' . $paymentStatus . ')');
            if (isset($_POST['Message'])) {
              $GATEWAY->writeLog($SALE_ID, 'Gateway message: ' . $_POST['Message']);
            }
            break;
        }
      } else {

        // DEBUG..
        $GATEWAY->writeLog($SALE_ID, 'Received callback for sale already processed.');

      }

    } else {

      // DEBUG..
      $GATEWAY->writeLog($SALE_ID, 'Sale not validated by gateway. Check debug log for post data received..');

    }

  } else {

    // DEBUG..
    $GATEWAY->writeLog($SALE_ID, 'Received callback from IPN transmission from another system. Ignored.');

  }

  // DEBUG..
  $GATEWAY->writeLog($SALE_ID, 'Callback processing completed. No further actions.');

}

// Let gateway know the callback was ok..
if (isset($_POST['StatusCode'])) {
  echo 'StatusCode=' . $_POST['StatusCode'] . '&Message=' . (isset($_POST['Message']) ? $_POST['Message'] : '');
} else {
  // Let gateway know the callback was ok..
  header('HTTP/1.0 200 OK');
  header('Content-type: text/plain; charset=UTF-8');
  echo 'OK';
}

?>