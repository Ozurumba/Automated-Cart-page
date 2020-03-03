<?php

class payfast extends paymentHandler {

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
    // Valid hosts and IP check..
    $ipList = array();
    $hosts  = array(
      'www.payfast.co.za',
      'w1w.payfast.co.za',
      'w2w.payfast.co.za',
      'sandbox.payfast.co.za'
    );
    foreach ($hosts AS $h) {
      $ips = gethostbynamel($h);
      if ($ips !== false) {
        $ipList = array_merge($ipList, $ips);
      }
    }
    $ipList = array_unique($ipList);
    // Check callback came from valid host..
    $this->writeLog($order->id, 'Checking response came from server in allowed hosts list..');
    if (in_array($_SERVER['REMOTE_ADDR'], $ipList)) {
      $this->writeLog($order->id, $_SERVER['REMOTE_ADDR'] . ' accepted as valid. Checking signature..');
      // Check signature...
      $hash = '';
      foreach ($_POST AS $fK => $fV) {
        if ($fK != 'signature') {
          $hash .= $fK . '=' . urlencode(mc_cleanData($fV)) . '&';
        }
      }
      // Remove trailing ampersand..
      $hash = substr($hash, 0, -1);
      // Check signature..
      $this->writeLog($order->id, 'Creating callback MD5 Hash from the following string:' . mc_defineNewline() . $hash);
      $this->writeLog($order->id, 'Hash check comparison to validate. Must match:' . mc_defineNewline() . 'Signature: ' . strtoupper($_POST['signature']) . mc_defineNewline() . 'Hash: ' . strtoupper(md5($hash)));
      if (isset($_POST['signature']) && $_POST['signature'] == md5($hash)) {
        $this->writeLog($order->id, 'Hash successful...post data back to Payfast to validate..');
        // Remove signature from postback vars..
        unset($_POST['signature']);
        // Transmit..
        $postReq = http_build_query($_POST);
        $this->writeLog($order->id, 'Server check successful: ' . $_SERVER['REMOTE_ADDR'] . '..Sending data back to Payfast to validate: ' . mc_defineNewline() . 'Server: ' . ($this->settings->gatewayMode == 'live' ? $params['validation-url'] : $params['validation-sand-url']) . mc_defineNewline() . 'Data: ' . $postReq);
        $r = $this->gatewayTransmission(($this->settings->gatewayMode == 'live' ? $params['validation-url'] : $params['validation-sand-url']), $postReq);
        $this->writeLog($order->id, 'Payfast responded with: ' . $r);
        // Check for invalid response first..
        if (strpos(strtolower($r), 'invalid') === true || strpos(strtolower($r), 'invalid') > 0) {
          return 'err';
        }
        return (strpos(strtolower($r), 'valid') === true || strpos(strtolower($r), 'valid') > 0 ? 'ok' : 'err');
      } else {
        $this->writeLog($order->id, 'Invalid hash calculated.');
        return 'err';
      }
    } else {
      $this->writeLog($order->id, 'Response from invalid server: ' . $_SERVER['REMOTE_ADDR'] . mc_defineNewline() . 'Valid IP(s): ' . implode(', ', $ipList));
      return 'err';
    }
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['pf_payment_id']) ? $_POST['pf_payment_id'] : ''),
      'amount' => (isset($_POST['amount_gross']) ? number_format($_POST['amount_gross'], 2, '.', '') : ''),
      'refund-amount' => '',
      'currency' => $this->settings->baseCurrency,
      'code-id' => (isset($_POST['custom_str1']) ? $_POST['custom_str1'] : ''),
      'pay-status' => (isset($_POST['payment_status']) ? $_POST['payment_status'] : ''),
      'pending-reason' => '',
      'inv-status' => '',
      'fraud-status' => ''
    );
    return $gateway;
  }

  // Assigns fields array..
  public function gatewayFields($ssl, $buyCode, $id, $itemName) {
    $url      = ($ssl == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    $order    = $this->getOrderInfo($buyCode, $id);
    $params   = $this->paymentParams($this->gateway);
    $name     = $this->orderFirstNameLastName($order->bill_1);
    $transmit = array();
    $fields   = array(
      'merchant_id' => $params['merchant-id'],
      'merchant_key' => $params['merchant-key'],
      'return_url' => $url . 'index.php?gw=' . $id . '-' . $buyCode,
      'cancel_url' => $url . 'index.php?p=cancel&o=' . $id . '-' . $buyCode,
      'notify_url' => $url . 'checkout/payfast.php',
      'name_first' => $this->stripInvalidChars($name['first-name']),
      'name_last' => $this->stripInvalidChars($name['last-name']),
      'email_address' => $order->bill_2,
      'm_payment_id' => $id,
      'amount' => $order->grandTotal,
      'item_name' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'custom_str1' => $buyCode . '-' . $id
    );
    // Create hash..
    $hash     = '';
    foreach ($fields AS $fK => $fV) {
      $hash .= ($fK != 'merchant_id' ? '&' : '') . $fK . '=' . urlencode($fV);
      $transmit[$fK] = $fV;
    }
    $this->writeLog($id, 'Create MD5 Hash Digest from the following string:' . mc_defineNewline() . $hash);
    $transmit['signature'] = md5($hash);
    return array(
      'form',
      $transmit
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