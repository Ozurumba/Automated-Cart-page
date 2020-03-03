<?php

class paymate extends paymentHandler {

  public $gateway_name;
  public $gateway_url;
  public $gateway;

  // Payment server url..
  public function paymentServer() {
    return ($this->settings->gatewayMode == 'live' ? $this->modules[$this->gateway]['live'] : $this->modules[$this->gateway]['sandbox']);
  }

  // Validate gateway payment..
  public function validateResponse($params, $order) {
    // Log incoming vars..
    $this->logGateWayParams($_POST, $order->id);
    $this->writeLog($order->id, 'Hash check comparison to validate. Must match:' . mc_defineNewline() . 'Sys Generated: ' . strtoupper($_POST['hash']) . mc_defineNewline() . 'Order: ' . strtoupper(sha1($order->id . $order->buyCode)));
    return (isset($_POST['hash']) && strtoupper($_POST['hash']) == strtoupper(sha1($order->id . $order->buyCode)) ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['txn_id']) ? $_POST['txn_id'] : ''),
      'amount' => (isset($_POST['amount']) ? number_format($_POST['amount'], 2, '.', '') : ''),
      'refund-amount' => '',
      'currency' => (isset($_POST['currency']) ? $_POST['currency'] : ''),
      'code-id' => (isset($_POST['custom']) ? $_POST['custom'] : ''),
      'pay-status' => (isset($_POST['code']) ? $_POST['code'] : ''),
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
    $name    = $this->orderFirstNameLastName($order->bill_1);
    $fields  = array(
      'mid' => $params['merchant-id'],
      'amt' => $order->grandTotal,
      'currency' => $this->settings->baseCurrency,
      'ref' => $id,
      'pmt_sender_email' => $this->stripInvalidChars($order->bill_2),
      'pmt_contact_firstname' => $this->stripInvalidChars($name['first-name']),
      'pmt_contact_surname' => $this->stripInvalidChars($name['last-name']),
      'pmt_contact_phone' => $order->bill_8,
      'pmt_country' => $country->cISO_2,
      'regindi_address1' => $this->stripInvalidChars($order->bill_3),
      'regindi_address2' => $this->stripInvalidChars($order->bill_4),
      'regindi_sub' => $this->stripInvalidChars($order->bill_5),
      'regindi_state' => $this->stripInvalidChars($order->bill_6),
      'regindi_pcode' => $order->bill_7,
      'return' => $url . 'checkout/paymate.php?callback=' . $id . '-' . $buyCode
    );
    return array(
      'form',
      $fields
    );
  }

  // Pings handler on successful return..
  // This will only occur once..
  public function pingHandler() {
    $return = array(
      'err',
      'err'
    );
    if (isset($_POST['ref'])) {
      $split      = explode('-', $_GET['callback']);
      $params     = $this->paymentParams($this->gateway);
      $SALE_ORDER = $this->getOrderInfo('none', $_POST['ref']);
      if (isset($SALE_ORDER->id)) {
        $return = array(
          $SALE_ORDER->id,
          $SALE_ORDER->buyCode
        );
        $build  = 'txn-id=' . (isset($_POST['transactionID']) ? $_POST['transactionID'] : '');
        $build .= '&code=' . (isset($_POST['responseCode']) ? $_POST['responseCode'] : '');
        $build .= '&amount=' . (isset($_POST['paymentAmount']) ? $_POST['paymentAmount'] : '');
        $build .= '&custom=' . $_GET['callback'];
        $build .= '&currency=' . (isset($_POST['currency']) ? $_POST['currency'] : '');
        $build .= '&hash=' . sha1($SALE_ORDER->id . $SALE_ORDER->buyCode);
        // Log..
        $this->writeLog($SALE_ORDER->id, 'Sending ping to handler with the following: ' . $build);
        // Ping paymate callback handler..
        $r = $this->gatewayTransmission($this->settings->ifolder . '/checkout/paymate.php', $build);
        // Log..
        $this->writeLog($SALE_ORDER->id, 'Ping completed');
      }
    }
    return $return;
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