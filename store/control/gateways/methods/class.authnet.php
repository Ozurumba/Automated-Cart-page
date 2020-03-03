<?php

class authnet extends paymentHandler {

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
    $hash = $this->callback_hash($params, $order);
    if (isset($_POST['x_SHA2_Hash'])) {
      $phash = strtolower($_POST['x_SHA2_Hash']);
      $this->writeLog($order->id, 'Hash check comparison to validate. Must match:' . mc_defineNewline() . 'Callback: ' . strtoupper($phash) . mc_defineNewline() . 'Calculated: ' . strtoupper($hash));
      return (strtoupper($hash) == strtoupper($phash) ? 'ok' : 'err');
    }
    return 'err';
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $order   = $this->getOrderInfo('none', (isset($_POST['x_invoice_num']) ? (int) $_POST['x_invoice_num'] : '0'));
    $gateway = array(
      'trans-id' => (isset($_POST['x_trans_id']) ? $_POST['x_trans_id'] : ''),
      'amount' => (isset($_POST['x_amount']) ? number_format($_POST['x_amount'], 2, '.', '') : ''),
      'refund-amount' => '',
      'currency' => $this->settings->baseCurrency,
      'code-id' => (isset($order->id) ? $order->buyCode . '-' . $order->id : '0-0'),
      'pay-status' => (isset($_POST['x_response_code']) ? $_POST['x_response_code'] : ''),
      'message' => (isset($_POST['x_response_reason_text']) ? '[' . $_POST['x_response_reason_code'] . '] ' . $_POST['x_response_reason_text'] : ''),
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
    $name      = $this->orderFirstNameLastName($order->bill_1);
    $name2     = $this->orderFirstNameLastName($order->ship_1);
    $country   = mc_getShippingCountry($order->bill_9, true);
    $country2  = mc_getShippingCountry($order->shipSetCountry, true);
    $fields    = array(
      'x_login' => $params['login-id'],
      'x_amount' => $order->grandTotal,
      'x_description' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'x_invoice_num' => $id,
      'x_fp_sequence' => $id,
      'x_fp_timestamp' => $timestamp,
      'x_fp_hash' => $this->submissionHash($timestamp, $params, $id, $order->grandTotal),
      'x_test_request' => ($this->settings->gatewayMode == 'live' ? 'false' : 'true'),
      'x_show_form' => 'PAYMENT_FORM',
      'x_type' => 'AUTH_CAPTURE',
      'x_first_name' => $this->stripInvalidChars($name['first-name']),
      'x_last_name' => $this->stripInvalidChars($name['last-name']),
      'x_address' => $this->stripInvalidChars($order->bill_3 . ($order->bill_4 ? ', ' . $order->bill_4 : '')),
      'x_email' => $this->stripInvalidChars($order->bill_2),
      'x_city' => $this->stripInvalidChars($order->bill_5),
      'x_state' => $this->stripInvalidChars($order->bill_6),
      'x_zip' => $this->stripInvalidChars($order->bill_7),
      'x_country' => $this->stripInvalidChars($country->cName),
      'x_phone' => $this->stripInvalidChars($order->bill_8),
      'x_ship_to_first_name' => $this->stripInvalidChars($name2['first-name']),
      'x_ship_to_last_name' => $this->stripInvalidChars($name2['last-name']),
      'x_ship_to_address' => $this->stripInvalidChars($order->ship_3 . ($order->ship_4 ? ', ' . $order->ship_4 : '')),
      'x_ship_to_city' => $this->stripInvalidChars($order->ship_5),
      'x_ship_to_state' => $this->stripInvalidChars($order->ship_6),
      'x_ship_to_zip' => $this->stripInvalidChars($order->ship_7),
      'x_ship_to_country' => $this->stripInvalidChars($country2->cName),
      'x_relay_response' => 'false',
      'x_cancel_url' => $url . 'index.php?p=cancel&o=' . $id . '-' . $buyCode,
      'x_receipt_method' => 'POST',
      'x_receipt_link_text' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $public_checkout127)),
      'x_receipt_link_url' => $url . 'index.php?gw=' . $id . '-' . $buyCode
    );
    // Only include currency code for live server..
    // Seems to throw errors for test server..
    // If this throws (99) errors on live, uncomment..
    if ($this->settings->gatewayMode == 'live') {
      $fields['x_currency_code'] = (in_array($this->settings->baseCurrency, array(
        'USD',
        'GBP',
        'CAD',
        'EUR'
      )) ? $this->settings->baseCurrency : 'USD');
    }
    return array(
      'form',
      $fields
    );
  }

  // Hashes..
  public function submissionHash($timestamp, $params, $id, $amount) {
    if (function_exists('hash_hmac')) {
      $this->writeLog($id, 'Create MD5 (Hash_Hmac) Digest from the following string (with key appended):' . mc_defineNewline() . $params['login-id'] . '^' . $id . '^' . $timestamp . '^' . $amount . '^');
      return hash_hmac('md5', $params['login-id'] . '^' . $id . '^' . $timestamp . '^' . $amount . '^', $params['transaction-key']);
    } else {
      $this->writeLog($id, 'Create MD5 (Bin2Hex/Mhash) Digest from the following string (with key appended):' . mc_defineNewline() . $params['login-id'] . '^' . $id . '^' . $timestamp . '^' . $amount . '^');
      return bin2hex(mhash(MHASH_MD5, $params['login-id'] . '^' . $id . '^' . $timestamp . '^' . $amount . '^', $params['transaction-key']));
    }
  }

  // Legacy, not used
  public function responseHash($params, $id) {
    $code = $params['response-key'] . $params['login-id'] . $_POST['x_trans_id'] . $_POST['x_amount'];
    $this->writeLog($id, 'Creating callback Hash Digest from the following string:' . mc_defineNewline() . $code);
    return md5($code);
  }
  
  // Callback hash calculation..
  private function callback_hash($params, $order) {
    $code = '';
    if (isset($params['response-key'])) {
      $conc = array();
      $fields = array(
        'x_trans_id','x_test_request','x_response_code','x_auth_code','x_cvv2_resp_code',
        'x_cavv_response','x_avs_code','x_method','x_account_number','x_amount','x_company',
        'x_first_name','x_last_name','x_address','x_city','x_state','x_zip','x_country',
        'x_phone','x_fax','x_email','x_ship_to_company','x_ship_to_first_name','x_ship_to_last_name',
        'x_ship_to_address','x_ship_to_city','x_ship_to_state','x_ship_to_zip','x_ship_to_country',
        'x_invoice_num'
      );
      foreach ($fields AS $f) {
        if (isset($_POST[$f])) {
          $conc[] = $_POST[$f];
        }
      }
      if (!empty($conc) && function_exists('hash_hmac') && function_exists('hex2bin')) {
        $hstr = '^' . implode('^', $conc) . '^';
        $this->writeLog($order->id, 'Creating callback SHA2 Hash Digest from the following string:' . mc_defineNewline() . $hstr);
        return strtoupper(hash_hmac('sha512', $hstr, hex2bin($params['response-key'])));
      } else {
        $this->writeLog($order->id, 'One or more POST parameters not present OR hash_hmac/hex2bin functions not available. Hash could not be calculated');
        return '';
      }
    } else {
      $this->writeLog($order->id, 'One or more hash parameters not present. Hash could not be calculated');
      return '';
    }
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