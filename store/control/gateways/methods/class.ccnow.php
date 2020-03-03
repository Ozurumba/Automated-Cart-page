<?php

class ccnow extends paymentHandler {

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
    $hash  = $this->responseHash($params, $order->id);
    $phash = (isset($_POST['x_fp_hash']) ? $_POST['x_fp_hash'] : 'XX');
    $this->writeLog($order->id, 'Hash check comparison to validate. Must match:' . mc_defineNewline() . 'Callback: ' . strtoupper($phash) . mc_defineNewline() . 'Calculated: ' . strtoupper($hash));
    return (strtoupper($hash) == strtoupper($phash) ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $order   = $this->getOrderInfo('none', (isset($_POST['x_invoice_num']) ? (int) $_POST['x_invoice_num'] : '0'));
    $gateway = array(
      'trans-id' => (isset($_POST['x_orderid']) ? $_POST['x_orderid'] : ''),
      'amount' => (isset($_POST['x_amount']) ? number_format($_POST['x_amount'], 2, '.', '') : ''),
      'refund-amount' => (isset($_POST['x_refund_amount']) ? number_format($_POST['x_refund_amount'], 2, '.', '') : ''),
      'currency' => (isset($_POST['x_currency_code']) ? $_POST['x_currency_code'] : ''),
      'code-id' => (isset($order->id) ? $order->buyCode . '-' . $order->id : '0-0'),
      'pay-status' => (isset($_POST['x_status']) ? $_POST['x_status'] : ''),
      'message' => (isset($_POST['x_reason']) ? '[' . $_POST['x_reason'] . '] ' . $_POST['x_reason'] : ''),
      'inv-status' => '',
      'fraud-status' => ''
    );
    return $gateway;
  }

  // Assigns fields array..
  public function gatewayFields($ssl, $buyCode, $id, $itemName) {
    global $public_checkout127;
    $url       = ($ssl == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    $order     = $this->getOrderInfo($buyCode, $id);
    $params    = $this->paymentParams($this->gateway);
    $timestamp = time();
    $country   = mc_getShippingCountry($order->bill_9, true);
    $country2  = mc_getShippingCountry($order->shipSetCountry, true);
    $fields    = array(
      'x_login' => $params['login-id'],
      'x_version' => '1.0',
      'x_fp_sequence' => $id,
      'x_fp_arg_list' => 'x_login^x_fp_arg_list^x_fp_sequence^x_amount^x_currency_code',
      'x_fp_hash' => $this->submissionHash($params, $id, $this->settings->baseCurrency, $order->grandTotal),
      'x_product_sku_1' => '1',
      'x_product_title_1' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'x_product_quantity_1' => '1',
      'x_product_unitprice_1' => $order->grandTotal,
      'x_product_url_1' => $url,
      'x_name' => $this->stripInvalidChars($order->bill_1),
      'x_address' => $this->stripInvalidChars($order->bill_3),
      'x_address2' => $this->stripInvalidChars($order->bill_4),
      'x_city' => $this->stripInvalidChars($order->bill_5),
      'x_state' => $this->stripInvalidChars($order->bill_6),
      'x_zip' => $this->stripInvalidChars($order->bill_7),
      'x_country' => $this->stripInvalidChars($country->cISO_2),
      'x_phone' => $this->stripInvalidChars($order->bill_8),
      'x_email' => $this->stripInvalidChars($order->bill_2),
      'x_ship_to_name' => $this->stripInvalidChars($order->ship_1),
      'x_ship_to_address' => $this->stripInvalidChars($order->ship_3),
      'x_ship_to_address2' => $this->stripInvalidChars($order->ship_4),
      'x_ship_to_city' => $this->stripInvalidChars($order->ship_5),
      'x_ship_to_state' => $this->stripInvalidChars($order->ship_6),
      'x_ship_to_zip' => $this->stripInvalidChars($order->ship_7),
      'x_ship_to_country' => $this->stripInvalidChars($country2->cISO_2),
      'x_ship_to_phone' => $this->stripInvalidChars($order->ship_8),
      'x_invoice_num' => $id,
      'x_language' => $params['language'],
      'x_currency_code' => $this->settings->baseCurrency,
      'x_method' => 'NONE',
      'x_amount' => $order->grandTotal
    );
    return array(
      'form',
      $fields
    );
  }

  // Hashes..
  public function submissionHash($params, $id, $code, $amount) {
    $string = $params['login-id'] . '^x_login^x_fp_arg_list^x_fp_sequence^x_amount^x_currency_code^' . $id . '^' . $amount . '^' . $code . '^' . $params['activation-key'];
    $this->writeLog($id, 'Creating MD5 hash from the following string:' . mc_defineNewline() . mc_defineNewline() . $string);
    return md5($string);
  }

  public function responseHash($params, $id) {
    $orderID = (isset($_POST['x_orderid']) ? $_POST['x_orderid'] : '');
    $status  = (isset($_POST['x_status']) ? $_POST['x_status'] : '');
    $date    = (isset($_POST['x_timestamp']) ? $_POST['x_timestamp'] : '');
    $key     = $params['secret-key'];
    $code    = $orderID . '^' . $status . '^' . $date . '^' . $key;
    $this->writeLog($id, 'Create callback MD5 Hash Digest from the following string:' . mc_defineNewline() . $code);
    return md5($code);
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