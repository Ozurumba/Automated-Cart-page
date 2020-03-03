<?php

class paytrail extends paymentHandler {

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
    $phash = (isset($_POST['RETURN_AUTHCODE']) ? $_POST['RETURN_AUTHCODE'] : 'XX');
    $this->writeLog($order->id, 'Hash check comparison to validate. Must match:' . mc_defineNewline() . 'Callback: ' . strtoupper($phash) . mc_defineNewline() . 'Calculated: ' . strtoupper($hash));
    return (strtoupper($hash) == strtoupper($phash) ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $order   = $this->getOrderInfo('none', (isset($_POST['ORDER_NUMBER']) ? (int) $_POST['ORDER_NUMBER'] : '0'));
    $gateway = array(
      'amount' => $order->grandTotal,
      'trans-id' => (isset($_POST['PAID']) ? $_POST['PAID'] : ''),
      'refund-amount' => '',
      'currency' => 'EUR',
      'code-id' => (isset($_POST['PAID']) && $_POST['PAID'] ? $order->buyCode . '-' . $order->id : '0-0'),
      'pay-status' => (isset($_POST['PAID']) && $_POST['PAID'] ? 'OK' : ''),
      'pending-reason' => '',
      'inv-status' => '',
      'fraud-status' => ''
    );
    return $gateway;
  }

  // Assigns fields array..
  public function gatewayFields($ssl, $buyCode, $id, $itemName) {
    global $public_checkout133;
    $url                = ($ssl == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    $order              = $this->getOrderInfo($buyCode, $id);
    $params             = $this->paymentParams($this->gateway);
    $country            = mc_getShippingCountry($order->bill_9, true);
    $name               = $this->orderFirstNameLastName($order->bill_1);
    $fields             = array(
      'MERCHANT_ID' => $params['merchant-id'],
      'ORDER_NUMBER' => $id,
      'REFERENCE_NUMBER' => '',
      'ORDER_DESCRIPTION' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'CURRENCY' => 'EUR',
      'RETURN_ADDRESS' => $url . 'index.php?gw=' . $id . '-' . $buyCode,
      'CANCEL_ADDRESS' => $url . 'index.php?p=cancel&o=' . $id . '-' . $buyCode,
      'PENDING_ADDRESS' => $url . 'index.php?p=message',
      'NOTIFY_ADDRESS' => $url . 'checkout/paytrail.php',
      'TYPE' => 'E1',
      'CULTURE' => $params['language'],
      'PRESELECTED_METHOD' => '',
      'MODE' => '1',
      'VISIBLE_METHODS' => '',
      'GROUP' => '',
      'CONTACT_TELNO' => $this->stripInvalidChars($order->bill_8),
      'CONTACT_CELLNO' => '',
      'CONTACT_EMAIL' => $this->stripInvalidChars($order->bill_2),
      'CONTACT_FIRSTNAME' => $this->stripInvalidChars($name['first-name']),
      'CONTACT_LASTNAME' => $this->stripInvalidChars($name['last-name']),
      'CONTACT_COMPANY' => '',
      'CONTACT_ADDR_STREET' => $this->stripInvalidChars($order->bill_3 . ($order->bill_4 ? ', ' . $order->bill_4 : '')),
      'CONTACT_ADDR_ZIP' => $this->stripInvalidChars($order->bill_7),
      'CONTACT_ADDR_CITY' => $this->stripInvalidChars($order->bill_5),
      'CONTACT_ADDR_COUNTRY' => $country->cISO_2,
      'INCLUDE_VAT' => '0',
      'ITEMS' => '1',
      'ITEM_TITLE[0]' => $this->stripInvalidChars(str_replace('{id}', $id, $public_checkout133)),
      'ITEM_NO[0]' => '',
      'ITEM_AMOUNT[0]' => '1',
      'ITEM_PRICE[0]' => $order->grandTotal,
      'ITEM_TAX[0]' => '0',
      'ITEM_DISCOUNT[0]' => '',
      'ITEM_TYPE[0]' => '1'
    );
    // Calculate hash..
    $fields['AUTHCODE'] = $this->submissionHash($params, $fields, $id);
    return array(
      'form',
      $fields
    );
  }

  // Hashes..
  public function submissionHash($params, $fields, $id) {
    $string = $params['auth-hash'] . '|';
    foreach ($fields AS $k => $v) {
      $string .= $v . ($k != 'ITEM_TYPE[0]' ? '|' : '');
    }
    $this->writeLog($id, 'Create MD5 Hash Digest from the following string:' . mc_defineNewline() . $string);
    $hash = md5($string);
    return strtoupper($hash);
  }

  public function responseHash($params, $id) {
    $string = (isset($_POST['ORDER_NUMBER']) ? $_POST['ORDER_NUMBER'] : 'XX') . '|' . (isset($_POST['TIMESTAMP']) ? $_POST['TIMESTAMP'] : 'XX') . '|' . (isset($_POST['PAID']) ? $_POST['PAID'] : 'XX') . '|' . (isset($_POST['METHOD']) ? $_POST['METHOD'] : 'XX') . '|' . $params['auth-hash'];
    $this->writeLog($id, 'Creating callback MD5 Hash Digest from the following string:' . mc_defineNewline() . $string);
    return strtoupper(md5($string));
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