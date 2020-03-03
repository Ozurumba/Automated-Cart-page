<?php

class icepay extends paymentHandler {

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
    // Checksum..
    $sum = $params['encryption-code'] . '|' . $_POST['Merchant'] . '|' . $_POST['Status'] . '|' . $_POST['StatusCode'] . '|' . $_POST['OrderID'] . '|' . $_POST['PaymentID'] . '|' . $_POST['Reference'] . '|' . $_POST['TransactionID'] . '|' . $_POST['Amount'] . '|' . $_POST['Currency'] . '|' . $_POST['Duration'] . '|' . $_POST['ConsumerIPAddress'];
    $this->writeLog($order->id, 'Create callback SHA1 Hash Digest from the following string:' . mc_defineNewline() . $sum);
    $sum = sha1($sum);
    $this->writeLog($order->id, 'Hash check comparison to validate. Must match:' . mc_defineNewline() . 'Hash Sent: ' . strtoupper($_POST['Checksum']) . mc_defineNewline() . 'Calculated: ' . strtoupper($sum));
    return (isset($_POST['Checksum']) && strtoupper($sum) == strtoupper($_POST['Checksum']) ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['paymentID']) ? $_POST['paymentID'] : ''),
      'amount' => (isset($_POST['Amount']) ? $_POST['Amount'] : ''),
      'refund-amount' => (isset($_POST['Amount']) ? $_POST['Amount'] : ''),
      'currency' => (isset($_POST['Currency']) ? $_POST['Currency'] : ''),
      'code-id' => (isset($_POST['Reference']) ? $_POST['Reference'] : ''),
      'pay-status' => (isset($_POST['Status']) ? $_POST['Status'] : ''),
      'message' => (isset($_POST['StatusCode']) ? $this->statusCodeText($_POST['StatusCode']) : ''),
      'pending-reason' => '',
      'inv-status' => '',
      'fraud-status' => ''
    );
    return $gateway;
  }

  // Assigns fields array..
  public function gatewayFields($ssl, $buyCode, $id, $itemName) {
    $url    = ($ssl == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    $order  = $this->getOrderInfo($buyCode, $id);
    $params = $this->paymentParams($this->gateway);
    $fields = array(
      'IC_PaymentMethod' => 'CREDITCARD',
      'IC_Issuer' => 'VISA',
      'IC_Merchant' => $params['merchant-id'],
      'IC_Amount' => str_replace('.', '', $order->grandTotal),
      'IC_Currency' => $this->settings->baseCurrency,
      'IC_Language' => $params['language'],
      'IC_Country' => '00',
      'IC_OrderID' => $id,
      'IC_Reference' => $buyCode . '-' . $id,
      'IC_Description' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'IC_URLCompleted' => $url . 'index.php?gw=' . $id . '-' . $buyCode,
      'IC_URLError' => $this->settings->ifolder . '/?p=error',
      'IC_ResponseType' => 'REDIRECT',
      'IC_Postback' => $url . 'checkout/icepay.php'
    );
    // Generate checksum..
    $this->writeLog($id, 'Create SHA1 Hash Digest from the following string:' . mc_defineNewline() . $params['encryption-code'] . '|' . $params['merchant-id'] . '|' . $fields['IC_Amount'] . '|' . $fields['IC_Currency'] . '|' . $fields['IC_OrderID'] . '|' . $fields['IC_PaymentMethod'] . '|' . $fields['IC_Issuer']);
    $fields['IC_CheckSum'] = sha1($params['encryption-code'] . '|' . $params['merchant-id'] . '|' . $fields['IC_Amount'] . '|' . $fields['IC_Currency'] . '|' . $fields['IC_OrderID'] . '|' . $fields['IC_PaymentMethod'] . '|' . $fields['IC_Issuer']);
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

  // Icepay statuses..
  public function statusCodeText($code) {
    $a = array(
      'OK' => 'The payment has been completed.',
      'OPEN' => 'The payment is not yet completed (pending). After some time you will receive a Postback Notification which contains the OK or ERR status. The time varies depending on the payment method that was used.',
      'ERR' => 'The payment was not completed successfully or expired. It cannot change into anything else.',
      'REFUND' => 'A payment has been successfully refunded. You will receive a different PaymentID parameter but all the other parameters remain the same.',
      'CBACK' => 'The consumer has filed a chargeback via their issuing bank.',
      'VALIDATE' => 'The payment is awaiting validation by the consumer by means of a validation code returned by ICEPAY. Currently, this status is only used by SMS payments. You can safely ignore postbacks with this status if you have integrated ICEPAY using the Checkout.aspx method.You should ignore all other statuses. If a new status is introduced, you will be notified by your account manager.'
    );
    return (isset($a[$code]) ? $a[$code] : 'Unknown');
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