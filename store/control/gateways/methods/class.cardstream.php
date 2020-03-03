<?php

class cardstream extends paymentHandler {

  public $gateway_name;
  public $gateway_url;
  public $gateway;
  private $hashmethod = 'SHA512';

  // Payment server url..
  public function paymentServer() {
    return ($this->settings->gatewayMode == 'live' ? $this->modules[$this->gateway]['live'] : $this->modules[$this->gateway]['sandbox']);
  }

  // Validate gateway payment..
  public function validateResponse($params, $order) {
    // Log incoming vars..
    $this->logGateWayParams($_POST, $order->id);
    return (isset($_POST['responseCode']) && $_POST['responseCode'] == '0' ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['transactionID']) ? $_POST['transactionID'] : ''),
      'amount' => (isset($_POST['amountReceived']) ? $_POST['amountReceived'] : ''),
      'refund-amount' => '',
      'currency' => $this->settings->baseCurrency,
      'code-id' => (isset($_POST['transactionUnique']) ? $_POST['transactionUnique'] : ''),
      'pay-status' => (isset($_POST['responseCode']) ? $_POST['responseCode'] : ''),
      'pending-reason' => '',
      'inv-status' => '',
      'fraud-status' => ''
    );
    return $gateway;
  }

  // Assigns fields array..
  public function gatewayFields($ssl, $buyCode, $id, $itemName) {
    global $iso4217_conversion;
    $url     = ($ssl == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    $order   = $this->getOrderInfo($buyCode, $id);
    $address = $this->orderAddresses($order, true, array(
      'bill_1',
      'bill_7'
    ));
    $params  = $this->paymentParams($this->gateway);
    $country = mc_getShippingCountry($order->bill_9, true);
    $fields  = array(
      'merchantID' => ($this->settings->gatewayMode == 'live' ? $params['merchant-id'] : '100001'),
      'amount' => str_replace('.', '', $order->grandTotal),
      'action' => 'SALE',
      'type' => '1',
      'countryCode' => $country->iso4217,
      'currencyCode' => $iso4217_conversion[$this->settings->baseCurrency],
      'transactionUnique' => $buyCode . '-' . $id,
      'orderRef' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'redirectURL' => $url . 'index.php?gw=' . $id . '-' . $buyCode,
      'callbackURL' => $url . 'checkout/cardstream.php',
      'merchantData' => $buyCode . '-' . $id,
      'customerName' => $this->stripInvalidChars($order->bill_1),
      'customerAddress' => $this->stripInvalidChars($address['bill-address']),
      'customerPostCode' => $this->stripInvalidChars($order->bill_7),
      'customerEmail' => $this->stripInvalidChars($order->bill_2),
      'customerPhone' => $this->stripInvalidChars($order->bill_8),
      'item1Description' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'item1Quantity' => '1',
      'item1GrossValue' => str_replace('.', '', $order->grandTotal)
    );
    // Create signature..
    $fields['signature'] = cardstream::cStreamSig($fields, $params['pre-share-key'], $id);
    return array(
      'form',
      $fields
    );
  }

  // Calculate signature..
  private function cStreamSig($data, $key, $id) {
    $ret  = '';
    if (in_array($this->hashmethod, array('SHA512', 'SHA256', 'SHA1', 'MD5', 'CRC32'))) {
      $this->writeLog($id, 'Creating ' . $this->hashmethod . ' signature');
      // Sort by numeric ASCII values..
      ksort($data);
      // Create the URL encoded signature string
      $ret = http_build_query($data, '', '&');
      // Normalise all line endings (CRNL|NLCR|NL|CR) to just NL (%0A)
      $ret = preg_replace('/%0D%0A|%0A%0D|%0A|%0D/i', '%0A', $ret);
      // Hash the signature string and the key together
      $ret = hash($this->hashmethod, $ret . $key);
      // Prefix the algorithm if not the default
      if ($this->hashmethod !== 'SHA512') {
        $ret = '{' . $this->hashmethod . '}' . $ret;
      }
      $this->writeLog($id, 'Signature is: ' . $ret);
    } else {
      $this->writeLog($id, $this->hashmethod . ' is not a valid algorithm, must be SHA512, SHA256, SHA1, MD5 or CRC32. Payment terminated.');
    }
    return $ret;
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