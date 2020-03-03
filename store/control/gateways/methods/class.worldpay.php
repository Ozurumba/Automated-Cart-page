<?php

class worldpay extends paymentHandler {

  public $gateway_name;
  public $gateway_url;
  public $gateway;

  // Payment server url..
  public function paymentServer() {
    return ($this->settings->gatewayMode == 'live' ? $this->modules[$this->gateway]['live'] : $this->modules[$this->gateway]['sandbox']);
  }

  // Validate gateway payment..
  // Here we are checking that the trans status is Yes, the callback password matches and the Card Verification Value check passed..
  public function validateResponse($params, $order) {
    // Log incoming vars..
    $this->logGateWayParams($_POST, $order->id);
    // Split 4 digit AVS..
    $avs = str_split($_POST['AVS']);
    // Country match and CVV checks. If live, both must match..
    // If you don`t want this check, set $val array same as test. Note that these checks are recommended by WorldPay.
    if ($this->settings->gatewayMode == 'live') {
      $this->writeLog($order->id, 'Validating CVV (Card Verification Value) and Country Match (Both should equal 2):' . mc_defineNewline() . 'CVV: ' . $avs[0] . mc_defineNewline() . 'COUNTRY: ' . $avs[3]);
      $val = array(
        $avs[0],
        $avs[3]
      );
    } else {
      $this->writeLog($order->id, 'CVV and Country Match not checked in test mode. Passed OK.');
      $val = array(
        '2',
        '2'
      );
    }
    return (isset($_POST['transStatus']) && $_POST['transStatus'] == 'Y' && isset($_POST['callbackPW']) && $_POST['callbackPW'] == $params['callback-pw'] && $val[0] == '2' && $val[1] == '2' ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['transId']) ? $_POST['transId'] : ''),
      'amount' => (isset($_POST['amount']) ? number_format($_POST['amount'], 2, '.', '') : ''),
      'refund-amount' => '',
      'currency' => $this->settings->baseCurrency,
      'code-id' => (isset($_POST['MC_custom']) ? $_POST['MC_custom'] : ''),
      'pay-status' => (isset($_POST['transStatus']) ? $_POST['transStatus'] : ''),
      'pending-reason' => '',
      'inv-status' => '',
      'fraud-status' => ''
    );
    return $gateway;
  }

  // Assigns fields array..
  public function gatewayFields($ssl, $buyCode, $id, $itemName) {
    $url     = ($ssl == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    $order   = $this->getOrderInfo($buyCode, $id);
    $params  = $this->paymentParams($this->gateway);
    $country = mc_getShippingCountry($order->bill_9, true);
    $fields  = array(
      'testMode' => ($this->settings->gatewayMode == 'live' ? '0' : '100'),
      'instId' => $params['install-id'],
      'cartId' => 'Cart' . $id,
      'amount' => $order->grandTotal,
      'currency' => $this->settings->baseCurrency,
      'desc' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'name' => ($this->settings->gatewayMode == 'live' ? $this->stripInvalidChars($order->bill_1) : 'AUTHORISED'),
      'address1' => $this->stripInvalidChars($order->bill_3),
      'address2' => $this->stripInvalidChars($order->bill_4),
      'town' => $this->stripInvalidChars($order->bill_5),
      'region' => $this->stripInvalidChars($order->bill_6),
      'postcode' => $this->stripInvalidChars($order->bill_7),
      'country' => $country->cISO_2,
      'tel' => $this->stripInvalidChars($order->bill_8),
      'email' => $this->stripInvalidChars($order->bill_2),
      'hideCurrency' => '',
      'hideContact' => '',
      'MC_custom' => $buyCode . '-' . $id,
      'MC_callback' => $url . 'checkout/worldpay.php'
    );
    return array(
      'form',
      $fields
    );
  }

  // Mail templates assigned to this method..
  public function mailTemplates() {
    $t = array(
      'completed' => 'order-completed.txt',
      'completed-wm' => 'order-completed-webmaster.txt',
      'completed-dl' => 'order-completed-dl.txt',
      'completed-wm-dl' => 'order-completed-dl-webmaster.txt',
      'pending' => 'order-pending.txt',
      'pending-wm' => 'order-pending-webmaster.txt',
      'refunded' => 'order-refunded.txt',
      'cancelled' => 'order-cancelled.txt',
      'completed-wish' => 'order-completed-wish.txt',
      'completed-wish-dl' => 'order-completed-wish-dl.txt',
      'completed-wish-recipient' => 'order-completed-wish-recipient.txt',
      'completed-wish-recipient-dl' => 'order-completed-wish-recipient-dl.txt'
    );
    return $t;
  }

  // Set preferred status..
  public function setOrderStatus($code) {
    $d = array(
      'completed' => 'shipping',
      'download' => 'completed',
      'virtual' => 'completed',
      'free' => 'completed',
      'pending' => 'pending',
      'cancelled' => 'cancelled',
      'refunded' => 'refund'
    );
    $s = ($this->modules[$this->gateway]['statuses'] ? unserialize($this->modules[$this->gateway]['statuses']) : '');
    return (isset($s[$code]) ? $s[$code] : $d[$code]);
  }

}

?>