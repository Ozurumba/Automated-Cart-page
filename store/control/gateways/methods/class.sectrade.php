<?php

class sectrade extends paymentHandler {

  public $gateway_name;
  public $gateway_url;
  public $gateway;

  // Payment server url..
  public function paymentServer() {
    return ($this->settings->gatewayMode == 'live' ? $this->modules[$this->gateway]['live'] : $this->modules[$this->gateway]['sandbox']);
  }

  // Validate gateway payment..
  public function validateResponse($params, $order) {
    // Store gateway params..
    $this->logGateWayParams($_POST, $order->id);
    if (isset($_POST['responsesitesecurity']) && $_POST['responsesitesecurity']) {
      $this->writeLog($order->id, 'POST received from Secure Trading: ' . print_r($_POST, true));
      $string  = '';
      $keys    = array_keys($_POST);
      sort($keys);
      foreach ($keys as $pK) {
        if (!in_array($pK, array('notificationreference', 'responsesitesecurity'))) {
          $string .= $_POST[$pK];
        }
      }
      if ($string) {
        $string .= $params['notify-password'];
        $hash    = hash('sha256', $string);
        $this->writeLog($order->id, 'Hash calculated by system: ' . $hash);
        $this->writeLog($order->id, 'Hash sent by Secure Trading must match: ' . $_POST['responsesitesecurity']);
        if (strtolower($hash) == strtolower($_POST['responsesitesecurity']) && $_POST['sitereference'] == $params['site-reference']) {
          if ($_POST['errorcode'] == '0') {
            return 'ok';
          }
        }
      }
    }
    return 'err';
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['transactionreference']) ? $_POST['transactionreference'] : ''),
      'amount' => (isset($_POST['mainamount']) ? $_POST['mainamount'] : ''),
      'refund-amount' => '',
      'currency' => (isset($_POST['currencyiso3a']) ? $_POST['currencyiso3a'] : ''),
      'code-id' => (isset($_POST['custom-data']) ? $_POST['custom-data'] : ''),
      'pay-status' => 'completed',
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
    $fields   = array(
      'sitereference' => $params['site-reference'],
      'currencyiso3a' => $this->settings->baseCurrency,
      'mainamount' => $order->grandTotal,
      'version' => '1',
      'orderreference' => $id,
      'custom-data' => $buyCode . '-' . $id . '-mswcart',
      'settlestatus' => '',
      'settleduedate' => '',
      'authmethod' => ''
    );
    // Calculate the hash
    $fields['sitesecurity'] = 'g' . sectrade::sechash($fields, $params, $id);
    return array(
      'form',
      $fields
    );
  }

  // Calculate hash..
  public function sechash($arr, $params, $id) {
    $string = $arr['currencyiso3a'] . $arr['mainamount'] . $arr['sitereference'] . $arr['settlestatus'] . $arr['settleduedate'] . $arr['authmethod'] . $params['merchant-password'];
    $hash   = hash('sha256', $string);
    $this->writeLog($id, 'Calculating security hash for site security: ' . $hash);
    return $hash;
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