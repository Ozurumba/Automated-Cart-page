<?php

class skrill extends paymentHandler {

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
    $secret_word = strtoupper(md5($params['secret']));
    $md5         = $_POST['merchant_id'] . $_POST['transaction_id'] . $secret_word . $_POST['mb_amount'] . $_POST['mb_currency'] . $_POST['status'];
    $this->writeLog($order->id, 'Creating callback MD5 Hash Digest from the following string:' . mc_defineNewline() . $md5);
    $md5 = strtoupper(md5($md5));
    $this->writeLog($order->id, 'Hash check comparison to validate. Must match:' . mc_defineNewline() . 'Signature: ' . strtoupper($_POST['md5sig']) . mc_defineNewline() . 'Hash: ' . $md5);
    return (isset($_POST['md5sig']) && strtoupper($_POST['md5sig']) == $md5 ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['transaction_id']) ? $_POST['transaction_id'] : ''),
      'amount' => (isset($_POST['mb_amount']) ? number_format($_POST['mb_amount'], 2, '.', '') : ''),
      'refund-amount' => (isset($_POST['mb_amount']) ? number_format($_POST['mb_amount'], 2, '.', '') : ''),
      'currency' => (isset($_POST['mb_currency']) ? $_POST['mb_currency'] : ''),
      'code-id' => (isset($_POST['buycode']) ? $_POST['buycode'] : ''),
      'pay-status' => (isset($_POST['status']) ? $_POST['status'] : ''),
      'pending-reason' => 'N/A'
    );
    return $gateway;
  }

  // Assigns fields array..
  public function gatewayFields($ssl, $buyCode, $id, $itemName) {
    global $public_checkout117, $public_checkout65;
    $url     = ($ssl == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    $order   = $this->getOrderInfo($buyCode, $id);
    $country = mc_getShippingCountry($order->bill_9, true);
    $params  = $this->paymentParams($this->gateway);
    $name    = $this->orderFirstNameLastName($order->bill_1);
    $fields  = array(
      'pay_to_email' => $params['email'],
      'firstname' => $this->stripInvalidChars($name['first-name']),
      'lastname' => $this->stripInvalidChars($name['last-name']),
      'pay_from_email' => $this->stripInvalidChars($order->bill_2),
      'country' => $country->cISO,
      'address' => $this->stripInvalidChars($order->bill_3),
      'address2' => $this->stripInvalidChars($order->bill_4),
      'city' => $this->stripInvalidChars($order->bill_5),
      'state' => $this->stripInvalidChars($order->bill_6),
      'postal_code' => $this->stripInvalidChars($order->bill_7),
      'detail1_description' => $public_checkout65,
      'detail1_text' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'transaction_id' => $id,
      'status_url' => $url . 'checkout/skrill.php',
      'cancel_url' => $url . 'index.php?p=cancel&o=' . $id . '-' . $buyCode,
      'return_url' => $url . 'index.php?gw=' . $id . '-' . $buyCode,
      'return_url_text' => $this->stripInvalidChars(str_replace('{merchant}', $this->settings->website, $public_checkout117)),
      'amount' => rtrim($order->grandTotal, '0'),
      'currency' => $this->settings->baseCurrency,
      'language' => $params['language'],
      'merchant_fields' => 'buycode',
      'buycode' => $buyCode . '-' . $id
    );
    if ($params['logo'] && $ssl == 'yes' && substr($params['logo'], 0, 6) == 'https:') {
      $fields['logo_url'] = $params['logo'];
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