<?php

class sagepay extends paymentHandler {

  public $gateway_name;
  public $gateway_url;
  public $gateway;

  // Payment server url..
  public function paymentServer() {
    return ($this->settings->gatewayMode == 'live' ? $this->modules[$this->gateway]['live'] : $this->modules[$this->gateway]['sandbox']);
  }

  // Validate gateway payment..
  public function validateResponse($params, $order) {
    return (isset($_POST['vendor']) && $_POST['vendor'] == sha1($params['vendor']) ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['txn_id']) ? $_POST['txn_id'] : ''),
      'amount' => (isset($_POST['amount']) ? number_format($_POST['amount'], 2, '.', '') : ''),
      'refund-amount' => '',
      'currency' => $this->settings->baseCurrency,
      'code-id' => (isset($_POST['custom']) ? $_POST['custom'] : ''),
      'pay-status' => (isset($_POST['status']) ? $_POST['status'] : ''),
      'message' => (isset($_POST['message']) ? $_POST['message'] : ''),
      'pending-reason' => (isset($_POST['message']) ? $_POST['message'] : ''),
      'inv-status' => '',
      'fraud-status' => ''
    );
    return $gateway;
  }

  // Assigns fields array..
  public function gatewayFields($ssl, $buyCode, $id, $itemName) {
    $url       = ($ssl == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    $order     = $this->getOrderInfo($buyCode, $id);
    $params    = $this->paymentParams($this->gateway);
    $name_b    = $this->orderFirstNameLastName($order->bill_1);
    $name_s    = $this->orderFirstNameLastName($order->ship_1);
    $country_b = mc_getShippingCountry($order->bill_9, true);
    $country_s = mc_getShippingCountry($order->shipSetCountry, true);
    $encrypt   = array(
      'VendorTxCode' => $buyCode . '-' . $id,
      'Amount' => $order->grandTotal,
      'Currency' => $this->settings->baseCurrency,
      'Description' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'SuccessURL' => $url . 'index.php?gw=' . $id . '-' . $buyCode,
      'FailureURL' => $url . 'index.php?p=declined',
      'SendEMail' => '2',
      'CustomerName' => $this->stripInvalidChars($order->bill_1),
      'CustomerEMail' => $this->stripInvalidChars($order->bill_2),
      'VendorEMail' => $this->settings->email,
      'BillingFirstnames' => $this->stripInvalidChars($name_b['first-name']),
      'BillingSurname' => $this->stripInvalidChars($name_b['last-name']),
      'BillingAddress1' => $this->stripInvalidChars($order->bill_3),
      'BillingAddress2' => $this->stripInvalidChars($order->bill_4),
      'BillingCity' => $this->stripInvalidChars($order->bill_5),
      'BillingState' => substr($this->stripInvalidChars($order->bill_6), 0, 2),
      'BillingPostCode' => $this->stripInvalidChars($order->bill_7),
      'BillingPhone' => $this->stripInvalidChars($order->bill_8),
      'BillingCountry' => $country_b->cISO_2,
      'DeliveryFirstnames' => $this->stripInvalidChars($name_s['first-name']),
      'DeliverySurname' => $this->stripInvalidChars($name_s['last-name']),
      'DeliveryAddress1' => $this->stripInvalidChars($order->ship_3),
      'DeliveryAddress2' => $this->stripInvalidChars($order->ship_4),
      'DeliveryCity' => $this->stripInvalidChars($order->ship_5),
      'DeliveryState' => substr($this->stripInvalidChars($order->ship_6), 0, 2),
      'DeliveryPostCode' => $this->stripInvalidChars($order->ship_7),
      'DeliveryCountry' => $country_s->cISO_2,
      'AllowGiftAid' => '0',
      'ApplyAVSCV2' => '0',
      'Apply3DSecure' => '0'
    );
    // If none US addresses, remove state fields..
    // Sagepay allows this. If we include it, payment page fails..
    if ($country_b->cISO_2 != 'US') {
      unset($encrypt['BillingState']);
    }
    if ($country_s->cISO_2 != 'US') {
      unset($encrypt['DeliveryState']);
    }
    $data = '';
    foreach ($encrypt AS $k => $v) {
      $data .= ($k != 'VendorTxCode' ? '&' : '') . $k . '=' . $v;
    }
    $fields = array(
      'VPSProtocol' => '2.23',
      'TxType' => 'PAYMENT',
      'Vendor' => $params['vendor'],
      'Crypt' => $this->encoder($data, $params)
    );
    return array(
      'form',
      $fields
    );
  }

  // Pings handler on successful return..
  // This will only occur once..
  public function pingHandler($order) {
    $params   = $this->paymentParams($this->gateway);
    $log      = array();
    $incoming = $this->decoder($_GET['crypt'], $params);
    // Log..
    $this->writeLog($order->id, 'Decoding payment data: ' . $incoming);
    // Build data to post..
    $post = explode('&', $incoming);
    // Log incoming vars..
    foreach ($post AS $k) {
      // Split..
      $split          = explode('=', $k);
      $log[$split[0]] = $split[1];
    }
    $this->logGateWayParams($log, $order->id);
    $build = 'txn-id=' . (isset($log['TxAuthNo']) ? $log['TxAuthNo'] : (isset($log['VPSTxId']) ? $log['VPSTxId'] : ''));
    $build .= '&status=' . (isset($log['Status']) ? $log['Status'] : 'INV');
    $build .= '&vendor=' . sha1($params['vendor']);
    $build .= '&amount=' . (isset($log['Amount']) ? $log['Amount'] : '0.00');
    $build .= '&custom=' . (isset($log['VendorTxCode']) ? $log['VendorTxCode'] : '');
    $build .= '&message=' . (isset($log['StatusDetail']) ? $log['StatusDetail'] : '');
    // Ping handler..
    if (isset($log['Status']) && strtoupper($log['Status']) == 'OK') {
      // Log..
      $this->writeLog($order->id, 'Status OK from Sagepay...sending ping to Maian Cart handler with the following: ' . $build);
      // Ping..
      $r = $this->gatewayTransmission($this->settings->ifolder . '/checkout/sagepay.php', $build);
    } else {
      // Log..
      $this->writeLog($order->id, 'Status NOT OK from SagePay..stop gateway processing. Check SagePay account for more details. Data: ' . $build);
    }
  }

  //----------------------------------------------
  // ENCRYPTION FUNCTIONS
  // AES encryption or XOR encryption
  // Taken from Sage Pay Dev Kit
  //----------------------------------------------

  public function encoder($data, $params) {
    switch($params['encryption']) {
      // XOR encryption..
      case 'xor':
        return $this->base64Encode($this->simpleXor($data, $params['xor-password']));
        break;
      // AES - default
      default:
        if (!function_exists('mcrypt_encrypt')) {
          die('You have enabled AES encryption, but the <a href="http://www.php.net/mcrypt">Mcrypt</a> functions are not available on your server.
	  Please recompile your server with Mcrypt support OR try using the XOR encryption method for SagePay.');
        }
        $strIn = $this->addPKCS5Padding($data);
        $crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $params['xor-password'], $strIn, MCRYPT_MODE_CBC, $params['xor-password']);
        return '@' . bin2hex($crypt);
        break;
    }
  }

  public function decoder($data, $params) {
    if (substr($data, 0, 1) == '@') {
      $strIn = substr($data, 1);
      $strIn = pack('H*', $strIn);
      return $this->removePKCS5Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $params['xor-password'], $strIn, MCRYPT_MODE_CBC, $params['xor-password']));
    } else {
      return $this->simpleXor($this->base64Decode($data), $params['xor-password']);
    }
  }

  public function base64Encode($plain) {
    return base64_encode($plain);
  }

  public function base64Decode($scrambled) {
    $scrambled = str_replace(" ", "+", $scrambled);
    return base64_decode($scrambled);
  }

  public function simpleXor($InString, $Key) {
    $KeyList = array();
    $output  = '';
    for ($i = 0; $i < strlen($Key); $i++) {
      $KeyList[$i] = ord(substr($Key, $i, 1));
    }
    for ($i = 0; $i < strlen($InString); $i++) {
      $output .= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
    }
    return $output;
  }

  public function removePKCS5Padding($decrypted) {
    $padChar = ord($decrypted[strlen($decrypted) - 1]);
    return substr($decrypted, 0, -$padChar);
  }

  public function addPKCS5Padding($input) {
    $blocksize = 16;
    $padding   = '';
    $padlength = $blocksize - (strlen($input) % $blocksize);
    for ($i = 1; $i <= $padlength; $i++) {
      $padding .= chr($padlength);
    }
    return $input . $padding;
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