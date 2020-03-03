<?php

class payvector extends paymentHandler {

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
    $hashcode = 'PreSharedKey=' . $params['pre-share-key'];
    $hashcode .= '&MerchantID=' . (isset($_POST['MerchantID']) ? $_POST['MerchantID'] : '');
    $hashcode .= '&Password=' . $params['password'];
    $hashcode .= '&StatusCode=' . (isset($_POST['StatusCode']) ? $_POST['StatusCode'] : '');
    $hashcode .= '&Message=' . (isset($_POST['Message']) ? $_POST['Message'] : '');
    $hashcode .= '&PreviousStatusCode=' . (isset($_POST['PreviousStatusCode']) ? $_POST['PreviousStatusCode'] : '');
    $hashcode .= '&PreviousMessage=' . (isset($_POST['PreviousMessage']) ? $_POST['PreviousMessage'] : '');
    $hashcode .= '&CrossReference=' . (isset($_POST['CrossReference']) ? $_POST['CrossReference'] : '');
    $hashcode .= '&AddressNumericCheckResult=' . (isset($_POST['AddressNumericCheckResult']) ? $_POST['AddressNumericCheckResult'] : '');
    $hashcode .= '&PostCodeCheckResult=' . (isset($_POST['PostCodeCheckResult']) ? $_POST['PostCodeCheckResult'] : '');
    $hashcode .= '&CV2CheckResult=' . (isset($_POST['CV2CheckResult']) ? $_POST['CV2CheckResult'] : '');
    $hashcode .= '&ThreeDSecureAuthenticationCheckResult=' . (isset($_POST['ThreeDSecureAuthenticationCheckResult']) ? $_POST['ThreeDSecureAuthenticationCheckResult'] : '');
    $hashcode .= '&CardType=' . (isset($_POST['CardType']) ? $_POST['CardType'] : '');
    $hashcode .= '&CardClass=' . (isset($_POST['CardClass']) ? $_POST['CardClass'] : '');
    $hashcode .= '&CardIssuer=' . (isset($_POST['CardIssuer']) ? $_POST['CardIssuer'] : '');
    $hashcode .= '&CardIssuerCountryCode=' . (isset($_POST['CardIssuerCountryCode']) ? $_POST['CardIssuerCountryCode'] : '');
    $hashcode .= '&Amount=' . (isset($_POST['Amount']) ? $_POST['Amount'] : '');
    $hashcode .= '&CurrencyCode=' . (isset($_POST['CurrencyCode']) ? $_POST['CurrencyCode'] : '');
    $hashcode .= '&OrderID=' . (isset($_POST['OrderID']) ? $_POST['OrderID'] : '');
    $hashcode .= '&TransactionType=' . (isset($_POST['TransactionType']) ? $_POST['TransactionType'] : '');
    $hashcode .= '&TransactionDateTime=' . (isset($_POST['TransactionDateTime']) ? $_POST['TransactionDateTime'] : '');
    $hashcode .= '&OrderDescription=' . (isset($_POST['OrderDescription']) ? $_POST['OrderDescription'] : '');
    $hashcode .= '&CustomerName=' . (isset($_POST['CustomerName']) ? $_POST['CustomerName'] : '');
    $hashcode .= '&Address1=' . (isset($_POST['Address1']) ? $_POST['Address1'] : '');
    $hashcode .= '&Address2=' . (isset($_POST['Address2']) ? $_POST['Address2'] : '');
    $hashcode .= '&Address3=' . (isset($_POST['Address3']) ? $_POST['Address3'] : '');
    $hashcode .= '&Address4=' . (isset($_POST['Address4']) ? $_POST['Address4'] : '');
    $hashcode .= '&City=' . (isset($_POST['City']) ? $_POST['City'] : '');
    $hashcode .= '&State=' . (isset($_POST['State']) ? $_POST['State'] : '');
    $hashcode .= '&PostCode=' . (isset($_POST['PostCode']) ? $_POST['PostCode'] : '');
    $hashcode .= '&CountryCode=' . (isset($_POST['CountryCode']) ? $_POST['CountryCode'] : '');
    $hashcode .= '&EmailAddress=' . (isset($_POST['EmailAddress']) ? $_POST['EmailAddress'] : '');
    $hashcode .= '&PhoneNumber=' . (isset($_POST['PhoneNumber']) ? $_POST['PhoneNumber'] : '');
    $this->writeLog($order->id, 'Create callback SHA1 Hash Digest from the following string:' . mc_defineNewline() . $hashcode);
    $hashcode = sha1($hashcode);
    $this->writeLog($order->id, 'Hash check comparison to validate. Must match:' . mc_defineNewline() . 'Digest: ' . strtoupper($_POST['HashDigest']) . mc_defineNewline() . 'Hash: ' . strtoupper($hashcode));
    return (isset($_POST['HashDigest']) && strtoupper($hashcode) == strtoupper($_POST['HashDigest']) ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['OrderID']) ? substr($_POST['OrderID'], strpos($_POST['OrderID'], '-') + 1) : ''),
      'amount' => (isset($_POST['Amount']) ? number_format($_POST['Amount'], 2, '.', '') : ''),
      'refund-amount' => '',
      'currency' => $this->settings->baseCurrency,
      'code-id' => (isset($_POST['OrderID']) ? $_POST['OrderID'] : ''),
      'pay-status' => (isset($_POST['StatusCode']) ? $_POST['StatusCode'] : ''),
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
      'PreSharedKey' => $params['pre-share-key'],
      'MerchantID' => $params['merchant-id'],
      'Password' => $params['password'],
      'Amount' => str_replace('.', '', $order->grandTotal),
      'CurrencyCode' => mc_iso4217_conversion($this->settings->baseCurrency),
      'EchoAVSCheckResult' => 'false',
      'EchoCV2CheckResult' => 'false',
      'EchoThreeDSecureAuthenticationCheckResult' => 'false',
      'EchoCardType' => 'false',
      'OrderID' => $buyCode . '-' . $id,
      'TransactionType' => 'SALE',
      'TransactionDateTime' => date('Y-m-d H:i:s P'),
      'CallbackURL' => $url . 'index.php?gw=' . $id . '-' . $buyCode,
      'OrderDescription' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'CustomerName' => $this->stripInvalidChars($order->bill_1),
      'Address1' => $this->stripInvalidChars($order->bill_3),
      'Address2' => $this->stripInvalidChars($order->bill_4),
      'Address3' => '',
      'Address4' => '',
      'City' => $this->stripInvalidChars($order->bill_5),
      'State' => $this->stripInvalidChars($order->bill_6),
      'PostCode' => $this->stripInvalidChars($order->bill_7),
      'CountryCode' => $this->iso4217($order->bill_9),
      'EmailAddress' => $this->stripInvalidChars($order->bill_2),
      'PhoneNumber' => $this->stripInvalidChars($order->bill_8),
      'EmailAddressEditable' => 'false',
      'PhoneNumberEditable' => 'false',
      'CV2Mandatory' => 'true',
      'Address1Mandatory' => 'true',
      'CityMandatory' => 'true',
      'PostCodeMandatory' => 'true',
      'StateMandatory' => 'true',
      'CountryMandatory' => 'true',
      'ResultDeliveryMethod' => 'SERVER',
      'ServerResultURL' => $url . 'checkout/payvector.php',
      'PaymentFormDisplaysResult' => 'false'
    );
    // Calculate hash..
    $hash   = '';
    foreach ($fields AS $fK => $fV) {
      $hash .= ($fK != 'PreSharedKey' ? '&' : '') . $fK . '=' . $fV;
    }
    $this->writeLog($id, 'Create SHA1 Hash Digest from the following string:' . mc_defineNewline() . $hash);
    $fields['HashDigest'] = sha1($hash);
    // Remove key and password from fields..
    unset($fields['PreSharedKey'], $fields['Password']);
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