<?php

class paypal extends paymentHandler {

  public $gateway_name;
  public $gateway_url;
  public $gateway;
  public $endpoints = array(
    'sandbox' => 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr',
    'live' => 'https://ipnpb.paypal.com/cgi-bin/webscr'
  );

  // Payment server url..
  public function paymentServer($mode = '') {
    switch($mode) {
      case 'verify':
        return ($this->settings->gatewayMode == 'live' ? $this->endpoints['live'] : $this->endpoints['sandbox']);
        break;
      default:
        return ($this->settings->gatewayMode == 'live' ? $this->modules[$this->gateway]['live'] : $this->modules[$this->gateway]['sandbox']);
        break;
    }
  }

  // Validate gateway payment..
  public function validateResponse($params, $order) {
    // Log incoming vars..
    $this->logGateWayParams($_POST, $order->id);
    $postReq = http_build_query($_POST) . '&cmd=_notify-validate';
    $this->writeLog($order->id, 'Sending data back to paypal to validate: ' . $postReq);
    $r = $this->gatewayTransmission($this->paymentServer('verify'), $postReq);
    $this->writeLog($order->id, 'Paypal responded with: ' . $r);
    return (strpos(strtolower($r), 'verified') !== false || strpos(strtolower($r), 'verified') > 0 ? 'ok' : 'err');
  }

  // Convert gateway post options to global vars handled by callback..
  // Add other options if required by handler..
  public function gatewayPostFields() {
    $gateway = array(
      'trans-id' => (isset($_POST['txn_id']) ? $_POST['txn_id'] : ''),
      'amount' => (isset($_POST['mc_gross']) ? number_format($_POST['mc_gross'], 2, '.', '') : ''),
      'refund-amount' => (isset($_POST['mc_gross']) ? number_format($_POST['mc_gross'], 2, '.', '') : ''),
      'currency' => (isset($_POST['mc_currency']) ? $_POST['mc_currency'] : ''),
      'code-id' => (isset($_POST['custom']) ? $_POST['custom'] : ''),
      'pay-status' => (isset($_POST['payment_status']) ? $_POST['payment_status'] : ''),
      'pending-reason' => (isset($_POST['pending_reason']) ? $this->pendingReasons($_POST['pending_reason']) : ''),
      'inv-status' => '',
      'fraud-status' => ''
    );
    return $gateway;
  }

  // Assigns fields array..
  public function gatewayFields($ssl, $buyCode, $id, $itemName) {
    $url     = ($ssl == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    $order   = $this->getOrderInfo($buyCode, $id);
    $params  = $this->paymentParams($this->gateway);
    $country = mc_getShippingCountry($order->bill_9, true);
    $name    = $this->orderFirstNameLastName($order->bill_1);
    $fields  = array(
      'rm' => '2',
      'cmd' => '_xclick',
      'business' => $params['email'],
      'item_name' => $this->stripInvalidChars(str_replace('{store}', $this->settings->website, $itemName)),
      'quantity' => '1',
      'notify_url' => $url . 'checkout/paypal.php',
      'cancel_return' => $url . 'index.php?p=cancel&o=' . $id . '-' . $buyCode,
      'return' => $url . 'index.php?gw=' . $id . '-' . $buyCode,
      'amount' => $order->grandTotal,
      'currency_code' => $this->settings->baseCurrency,
      'no_shipping' => '1',
      'custom' => $buyCode . '-' . $id . '-mswcart',
      'address_country' => $this->stripInvalidChars($country->cName),
      'address_city' => $this->stripInvalidChars($order->bill_5),
      'address_country_code' => $country->cISO_2,
      'address_state' => $this->stripInvalidChars($order->bill_6),
      'address_street' => $this->stripInvalidChars($order->bill_3),
      'address_zip' => $this->stripInvalidChars($order->bill_7),
      'contact_phone' => $this->stripInvalidChars($order->bill_8),
      'first_name' => $this->stripInvalidChars($name['first-name']),
      'last_name' => $this->stripInvalidChars($name['last-name']),
      'payer_email' => $this->stripInvalidChars($order->bill_2)
    );
    if ($this->settings->gatewayMode == 'test') {
      $fields['test_ipn'] = '1';
    }
    // Add locale if set..
    if ($params['locale']) {
      $fields['lc'] = $params['locale'];
    }
    // Only show page style field if one is set, otherwise paypal throws an error..
    if ($params['pagestyle']) {
      $fields['page_style'] = $params['pagestyle'];
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

  // Pending reasons..
  public function pendingReasons($code) {
    $reasons = array(
      'address' => 'The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set to allow you to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile.',
      'authorization' => 'You set the payment action to Authorization and have not yet captured funds.',
      'echeck' => 'The payment is pending because it was made by an eCheck that has not yet cleared.',
      'intl' => 'The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.',
      'multi-currency' => 'You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment.',
      'order' => 'You set the payment action to Order and have not yet captured funds.',
      'paymentreview' => 'The payment is pending while it is being reviewed by PayPal for risk.',
      'unilateral' => 'The payment is pending because it was made to an email address that is not yet registered or confirmed.',
      'upgrade' => 'The payment is pending because it was made via credit card and you must upgrade your account to Business or Premier status in order to receive the funds. upgrade can also mean that you have reached the monthly limit for transactions on your account.',
      'verify' => 'The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.',
      'other' => 'The payment is pending for a reason other than the standard reasons. For more information, contact PayPal Customer Service.'
    );
    return (isset($reasons[$code]) ? $reasons[$code] : 'N/A');
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