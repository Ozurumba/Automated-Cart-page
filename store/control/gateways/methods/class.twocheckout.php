<?php

class twocheckout extends paymentHandler {

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
    $saleID     = $_POST['sale_id'];
    $vendorID   = $_POST['vendor_id'];
    $invoiceID  = $_POST['invoice_id'];
    $orderTotal = $_POST['invoice_list_amount'];
    if (isset($_POST['demo']) && $_POST['demo'] == 'Y') {
      $invoiceID = 1;
    }
    // Calculate md5 hash as 2co formula: md5(saleID + vendorID + invoiceID + secret_word)
    $key        = strtoupper(md5($saleID . $vendorID . $invoiceID . $params['secret']));
    $comparison = strtoupper($_POST['md5_hash']);
    $this->writeLog($order->id, 'Hash check comparison to validate. Must match:' . mc_defineNewline() . 'Key: ' . $key . mc_defineNewline() . 'Hash: ' . $comparison);
    // verify if the key is accurate
    return ($comparison == $key ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['invoice_id']) ? $_POST['invoice_id'] : ''),
      'amount' => (isset($_POST['invoice_list_amount']) ? number_format($_POST['invoice_list_amount'], 2, '.', '') : ''),
      'refund-amount' => (isset($_POST['item_list_amount_1']) ? number_format($_POST['item_list_amount_1'], 2, '.', '') : ''),
      'currency' => (isset($_POST['list_currency']) ? $_POST['list_currency'] : ''),
      'code-id' => (isset($_POST['vendor_order_id']) ? $_POST['vendor_order_id'] : ''),
      'pay-status' => (isset($_POST['message_type']) ? $_POST['message_type'] : ''),
      'inv-status' => (isset($_POST['invoice_status']) ? $_POST['invoice_status'] : ''),
      'fraud-status' => (isset($_POST['fraud_status']) ? $_POST['fraud_status'] : '')
    );
    return $gateway;
  }

  // Assigns fields array..
  public function gatewayFields($ssl, $buyCode, $id, $itemName) {
    $url      = ($ssl == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    $order    = $this->getOrderInfo($buyCode, $id);
    $country  = mc_getShippingCountry($order->shipSetCountry, true);
    $country2 = mc_getShippingCountry($order->bill_9, true);
    $params   = $this->paymentParams($this->gateway);
    $fields   = array(
      'sid' => $params['account'],
      'cart_order_id' => $id,
      'total' => $order->grandTotal,
      'x_Receipt_Link_URL' => $url . 'index.php?gw=' . $id . '-' . $buyCode,
      'tco_currency' => $this->settings->baseCurrency,
      'merchant_order_id' => $buyCode . '-' . $id,
      'lang' => strtolower($params['language']),
      'card_holder_name' => $this->stripInvalidChars($order->bill_1),
      'street_address' => $this->stripInvalidChars($order->bill_3),
      'street_address2' => $this->stripInvalidChars($order->bill_4),
      'city' => $this->stripInvalidChars($order->bill_5),
      'state' => $this->stripInvalidChars($order->bill_6),
      'zip' => $this->stripInvalidChars($order->bill_7),
      'country' => $this->stripInvalidChars($country2->cName),
      'email' => $this->stripInvalidChars($order->bill_2),
      'phone' => $this->stripInvalidChars($order->bill_8),
      'ship_name' => $this->stripInvalidChars($order->ship_1),
      'ship_street_address' => $this->stripInvalidChars($order->ship_3),
      'ship_street_address2' => $this->stripInvalidChars($order->ship_4),
      'ship_city' => $this->stripInvalidChars($order->ship_5),
      'ship_state' => $this->stripInvalidChars($order->ship_6),
      'ship_zip' => $this->stripInvalidChars($order->ship_7),
      'ship_country' => $this->stripInvalidChars($country->cName)
    );
    // Send var for test mode..
    if ($this->settings->gatewayMode == 'test') {
      $fields['demo'] = 'Y';
    }
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
      'pending' => 'order-pending-2co.txt',
      'pending-wm' => 'order-pending-2co-webmaster.txt',
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