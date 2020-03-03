<?php

class stripe extends paymentHandler {

  public $gateway_name;
  public $gateway_url;
  public $gateway;

  // API version..
  private $api_version = '2019-05-16';
  private $locale = 'auto';

  // Payment server url..
  // Stripe doesn`t use this..
  public function paymentServer() {}

  // Validate gateway payment..
  public function validateResponse($params, $order) {
    // Validate callback..
    return 'ok';
  }

  // Variables created on callback..
  public function gatewayPostFields() {
    $ramount = '0.00';
    // Check for webhook event..
    // Not all are supported..
    if (isset($this->hookevt['data']['object']['client_reference_id'])) {
      $chop = explode('-', $this->hookevt['data']['object']['client_reference_id']);
      $order = $this->getOrderInfo($chop[0], $chop[1]);
      if (isset($order->id)) {
        $this->writeLog($order->id, 'Webhook event received from payment gateway. Received: ' . print_r($this->hookevt, true));
        $_POST['custom'] = $this->hookevt['data']['object']['client_reference_id'];
        $failCode  = '';
        $txn_id    = '';
        switch($this->hookevt['type']) {
          // Checkout payment completed..
          case 'checkout.session.completed':
            if (isset($this->hookevt['data']['object']['display_items'][0]['currency'],
                $this->hookevt['data']['object']['display_items'][0]['amount'])) {
              $this->writeLog($order->id, 'Processing webhook: ' . $this->hookevt['type']);
              $payStatus = 'completed';
              $txn_id    = $this->hookevt['data']['object']['id'];
              $currency  = strtolower($this->hookevt['data']['object']['display_items'][0]['currency']);
              $amount    = $this->hookevt['data']['object']['display_items'][0]['amount'];
            }
            break;
          // Refund processed..
          case 'charge.refunded':
            if (isset($this->hookevt['data']['object']['display_items'][0]['currency'],
                $this->hookevt['data']['object']['display_items'][0]['amount'])) {
              $this->writeLog($order->id, 'Processing webhook: ' . $this->hookevt['type']);
              $currency  = $this->hookevt['data']['object']['display_items'][0]['currency'];
              $amount    = ($this->hookevt['data']['object']['display_items'][0]['amount'] / 100);
              $payStatus = 'refunded';
              $ramount   = ($this->hookevt['data']['object']['display_items'][0]['amount_refunded'] / 100);
            }
            break;
          // Not supported..
          default:
            if (isset($this->hookevt['data']['object']['display_items'][0]['currency'],
                $this->hookevt['data']['object']['display_items'][0]['amount'])) {
              $this->writeLog($order->id, 'Unsupported webhook: ' . $this->hookevt['type']);
              $currency  = $this->hookevt['data']['object']['display_items'][0]['currency'];
              $amount    = ($this->hookevt['data']['object']['display_items'][0]['amount'] / 100);
              $payStatus = $this->hookevt['type'];
            }
            break;
        }
      }
    }
    $arr = array(
      'trans-id' => (isset($txn_id) ? $txn_id : ''),
      'amount' => (isset($amount) ? number_format($amount, 2, '.', '') : ''),
      'pay-total' => (isset($amount) ? number_format($amount, 2, '.', '') : ''),
      'refund-amount' => (isset($ramount) ? number_format($ramount, 2, '.', '') : ''),
      'currency' => (isset($currency) ? $currency : ''),
      'code-id' => (isset($_POST['custom']) ? $_POST['custom'] : ''),
      'pay-status' => (isset($payStatus) ? $payStatus : 'failed'),
      'fail-code' => (isset($failCode) ? $failCode : ''),
      'pending-reason' => '',
      'inv-status' => '',
      'fraud-status' => ''
    );
    return $arr;
  }

  // Initialise checkout session..
  public function checkout($data = array()) {
    $payStatus = array('fail','','');
    $chop = explode('-', $data['custom']);
    $url  = ($data['ssl'] == 'yes' ? str_replace('http://', 'https://', $this->settings->ifolder) . '/' : $this->settings->ifolder . '/');
    if (isset($chop[0], $chop[1])) {
      $order = $this->getOrderInfo($chop[0], $chop[1]);
      if (isset($order->id)) {
        $this->writeLog($order->id, 'Attempting to create Stripe checkout session (v3 api)');
        $params = $this->paymentParams($this->gateway);
        include(PATH . 'control/gateways/lib/stripe/init.php');
        // Initialise checkout session..
        try {
          \Stripe\Stripe::setApiKey($params['secret-key']);
          \Stripe\Stripe::setApiVersion($this->api_version);
          $stripeID = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'locale' => '' . $this->locale . '',
            'customer_email' => $order->bill_2,
            'line_items' => [[
              'name' => $this->settings->website,
              'description' => trim($data['lang'][0]),
              'images' => ['' . $url . THEME_FOLDER . '/images/stripe.png'],
              'amount' => ($order->grandTotal * 100),
              'currency' => $this->settings->baseCurrency,
              'quantity' => 1
            ]],
            'client_reference_id' => $data['custom'],
            'success_url' => $url . 'index.php?gw=' . $chop[1] . '-' . $chop[0],
            'cancel_url' => $url . 'index.php?p=cancel&o=' . $chop[1] . '-' . $chop[0]
          ]);
          $payStatus[0] = 'ok';
          $payStatus[1] = $stripeID;
        } catch (\Stripe\Exception\CardException $e) {
          $payStatus = array('declined_by_gateway', '', $e->getMessage());
          $this->writeLog($order->id, $e->getMessage());
        } catch (\Stripe\Exception\RateLimitException $e) {
          $payStatus = array('declined_by_gateway', '', $e->getMessage());
          $this->writeLog($order->id, $e->getMessage());
        } catch (\Stripe\Exception\InvalidRequestException $e) {
          $payStatus = array('declined_by_gateway', '', $e->getMessage());
          $this->writeLog($order->id, $e->getMessage());
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
          $payStatus = array('declined_by_gateway', '', $e->getMessage());
          $this->writeLog($order->id, $e->getMessage());
        } catch (\Stripe\Exception\AuthenticationException $e) {
          $payStatus = array('declined_by_gateway', '', $e->getMessage());
          $this->writeLog($order->id, $e->getMessage());
        } catch (\Stripe\Exception\ApiConnectionException $e) {
          $payStatus = array('declined_by_gateway', '', $e->getMessage());
          $this->writeLog($order->id, $e->getMessage());
        } catch (\Stripe\Exception\ApiErrorException $e) {
          $payStatus = array('declined_by_gateway', '', $e->getMessage());
          $this->writeLog($order->id, $e->getMessage());
        } catch (Exception $e) {
          $payStatus = array('declined_by_gateway', '', $e->getMessage());
          $this->writeLog($order->id, 'An undetermined error occurred from the server');
        }
      }
    }
    return $payStatus;
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

  // Pending reasons..
  public function decline_reasons($code) {
    $arr = array(
      'approve_with_id' => 'The payment cannot be authorized.',
      'call_issuer' => 'The card has been declined for an unknown reason.',
      'card_not_supported' => 'The card does not support this type of purchase.',
      'card_velocity_exceeded' => 'The customer has exceeded the balance or credit limit available on their card.',
      'currency_not_supported' => 'The card does not support the specified currency.',
      'do_not_honor' => 'The card has been declined for an unknown reason.',
      'do_not_try_again' => 'The card has been declined for an unknown reason.',
      'duplicate_transaction' => 'A transaction with identical amount and credit card information was submitted very recently.',
      'expired_card' => 'The card has expired.',
      'fraudulent' => 'The payment has been declined as Stripe suspects it is fraudulent.',
      'generic_decline' => 'The card has been declined for an unknown reason.',
      'incorrect_number' => 'The card number is incorrect.',
      'incorrect_cvc' => 'The CVC number is incorrect.',
      'incorrect_pin' => 'The PIN entered is incorrect. This decline code only applies to payments made with a card reader. ',
      'incorrect_zip' => 'The ZIP/postal code is incorrect.',
      'insufficient_funds' => 'The card has insufficient funds to complete the purchase.',
      'invalid_account' => 'The card or account the card is connected to is invalid.',
      'invalid_amount' => 'The payment amount is invalid or exceeds the amount that is allowed.',
      'invalid_cvc' => 'The CVC number is incorrect.',
      'invalid_expiry_year' => 'The expiration year invalid.',
      'invalid_number' => 'The card number is incorrect.',
      'invalid_pin' => 'The PIN entered is incorrect. This decline code only applies to payments made with a card reader.',
      'issuer_not_available' => 'The card issuer could not be reached so the payment could not be authorized.',
      'lost_card' => 'The payment has been declined because the card is reported lost.',
      'new_account_information_available' => 'The card or account the card is connected to is invalid.',
      'no_action_taken' => 'The card has been declined for an unknown reason.',
      'not_permitted' => 'The payment is not permitted.',
      'pickup_card' => 'The card cannot be used to make this payment (it is possible it has been reported lost or stolen).',
      'pin_try_exceeded' => 'The allowable number of PIN tries has been exceeded.',
      'processing_error' => 'An error occurred while processing the card.',
      'reenter_transaction' => 'The payment could not be processed by the issuer for an unknown reason.',
      'restricted_card' => 'The card cannot be used to make this payment (it is possible it has been reported lost or stolen).',
      'revocation_of_all_authorizations' => 'The card has been declined for an unknown reason.',
      'revocation_of_authorization' => 'The card has been declined for an unknown reason.',
      'security_violation' => 'The card has been declined for an unknown reason.',
      'service_not_allowed' => 'The card has been declined for an unknown reason.',
      'stolen_card' => 'The payment has been declined because the card is reported stolen.',
      'stop_payment_order' => 'The card has been declined for an unknown reason.',
      'testmode_decline' => 'A Stripe test card number was used.',
      'transaction_not_allowed' => 'The card has been declined for an unknown reason.',
      'try_again_later' => 'The card has been declined for an unknown reason.',
      'withdrawal_count_limit_exceeded' => 'The customer has exceeded the balance or credit limit available on their card.',
      'just_failed' => 'The card has been declined for an unknown reason. Please contact your issuing bank.'
    );
    return (isset($arr[$code]) ? $arr[$code] : 'N/A');
  }

}

?>