<?php

$arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_global[3]));

// For shopping basket dialog..
$clearallbasket = str_replace('{text}', $mc_checkout[34], mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-dialog/clear-all-link.htm'));

// If loading from parent, var is set and checkout is enabled, we are good to go..
if (defined('PARENT') && isset($_GET['cart-ops']) && $SETTINGS->enableCheckout == 'yes') {
  // Load language files..
  include(MCLANG . 'accounts.php');
  include(MCLANG . 'payment.php');
  // Ajax class..
  include(PATH . 'control/classes/class.ajax.php');
  $MCOPS            = new cartOps();
  $MCOPS->settings  = $SETTINGS;
  $MCOPS->json      = $MCJSON;
  $MCOPS->checkout  = $MCCKO;
  $MCOPS->products  = $MCPROD;
  $MCOPS->rwr       = $MCRWR;
  $MCOPS->shipping  = $MCSHIP;
  // Ops..
  switch($_GET['cart-ops']) {
    case 'stateload':
      $billCnty = (int) $_GET['bc'];
      $shipCnty = (int) $_GET['sc'];
      $mc_states = array();
      $accID = (isset($loggedInUser['id']) ? $loggedInUser['id'] : (int) $_GET['ac']);
      if ($accID > 0) {
        $addrFields = $MCACC->getaddresses($accID);
        $billVal = (isset($addrFields[0]['addr5']) ? mc_safeHTML($addrFields[0]['addr5']) : '');
        $shipVal = (isset($addrFields[1]['addr5']) ? mc_safeHTML($addrFields[1]['addr5']) : '');
      }
      // Get arrays.
      $addarr = array(array(),array());
      if (file_exists(PATH . 'control/states/' . $billCnty . '.php')) {
        include(PATH . 'control/states/' . $billCnty . '.php');
        $addarr[0] = $mc_states;
      }
      if ($shipCnty != $billCnty) {
        if ($shipCnty > 0 && file_exists(GLOBAL_PATH . 'control/states/' . $shipCnty . '.php')) {
          include(PATH . 'control/states/' . $shipCnty . '.php');
          $addarr[1] = $mc_states;
        }
      } else {
        $addarr[1] = $mc_states;
      }
      include(PATH . 'control/classes/class.html.php');
      $MCHTML           = new mcHtml();
      $MCHTML->settings = $SETTINGS;
      $arr = array(
        'ship_addr' => $MCHTML->loadStates('ship',(isset($shipVal) ? $shipVal : ''),$addarr[1]),
        'bill_addr' => $MCHTML->loadStates('bill',(isset($billVal) ? $billVal : ''),$addarr[0]),
      );
      echo $MCJSON->encode($arr);
      exit;
      break;
    case 'add':
    case 'buynow':
      $ret = $MCOPS->add();
      if (!in_array($ret['status'], array('inv-product','failed'))) {
        switch($ret['status']) {
          case 'wishcheck':
            $arr['text'][1] = str_replace('{url}',$MCRWR->url(array('checkpay')),$mc_checkout[28]);
            break;
          case 'wishrestr':
            $arr['text'][1] = str_replace('{url}',$MCRWR->url(array('checkpay')),$mc_checkout[27]);
            break;
          case 'nothing':
            $arr['text'][1] = $mc_checkout[20];
            break;
          case 'no-stock':
            $arr['text'][1] = $msg_javascript296;
            break;
          case 'min-fail':
            $url = $MCRWR->url(array(
              $MCRWR->config['slugs']['prd'] . '/' . $ret['product']['id'] . '/' . ($ret['product']['rwslug'] ? $ret['product']['rwslug'] : $MCRWR->title($ret['product']['pName'])),
              'pd=' . $ret['product']['id']
            ));
            $arr['text'][1] = str_replace('{min}', $ret['min'], $msg_javascript455) . ($_GET['cart-ops'] ? '<br><br>' . str_replace('{url}',$url,$mc_checkout[21]) : '');
            break;
          case 'max-fail':
            $arr['text'][1] = str_replace('{max}', $ret['max'], $msg_jscript3);
            break;
          case 'attr-required':
            $arr['text'][1]   = $msg_javascript437;
            $arr['highlight'] = 'attr';
            $arr['id']        = $ret['group'];
            break;
          case 'attr-exceed':
            $arr['text'][1] = str_replace(array(
              '{count}',
              '{attribute}'
            ), array(
              $ret['stock'],
              $ret['name']
            ), $msg_javascript415);
            break;
          case 'pers-required':
            $arr['text'][1]   = $msg_javascript297;
            $arr['highlight'] = 'pers';
            $arr['id']        = $ret['field'];
            break;
          case 'force-product':
            $url = $MCRWR->url(array(
              $MCRWR->config['slugs']['prd'] . '/' . $ret['product']['id'] . '/' . ($ret['product']['rwslug'] ? $ret['product']['rwslug'] : $MCRWR->title($ret['product']['pName'])),
              'pd=' . $ret['product']['id']
            ));
            if ($_GET['cart-ops'] == 'buynow') {
              $arr = array(
                'msg' => 'rdr',
                'url' => $url
              );
              echo $MCJSON->encode($arr);
              exit;
            }
            $arr['text'][1] = str_replace('{url}',$url,$msg_shop_basket[0]);
            break;
          case 'exists':
          case 'ok':
            if ($_GET['cart-ops'] == 'buynow' || $MCCART->wishBasketCnt() == 'yes') {
              $arr = array(
                'msg' => 'rdr',
                'url' => $MCRWR->url(array('checkpay'))
              );
              echo $MCJSON->encode($arr);
              exit;
            }
            $items = $MCCART->buildDialogBasket();
            $arr = array(
              'msg' => 'ok',
              'html' => $items,
              'txt' => '<i class="fa fa-shopping-basket fa-fw"></i> ' . $msg_public_header21 . ($MCCART->cartCount() > 0 ? $clearallbasket : ''),
              'hidden' => $MCOPS->hidden(),
              'totals' => $MCCART->buildBasketTotals(),
              'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
              'count' => $MCCART->cartCount(),
              '_fr' => 'CASE ADD'
            );
            break;
        }
      } else {
        if ($_GET['cart-ops'] == 'buynow') {
          $arr = array(
            'msg' => 'rdr',
            'url' => $SETTINGS->ifolder,
            '_fr' => 'CASE ADD'
          );
          echo $MCJSON->encode($arr);
          exit;
        }
      }
      break;
    case 'reorder':
      if ($SETTINGS->salereorder == 'yes' && isset($loggedInUser['id']) && isset($_GET['sale'])) {
        $saleID = (int) $_GET['sale'];
        $sale   = mc_getTableData('sales', 'id', $saleID,' AND `saleConfirmation` = \'yes\' AND `account` = \'' . $loggedInUser['id'] . '\' AND `wishlist` = 0');
        if (isset($sale->id)) {
          // Set post vars..
          $data = $MCCKO->rebuildAccountOrder($_GET['sale'], $loggedInUser['id'], $sale);
          if ($data > 0) {
            $items = $MCCART->buildDialogBasket();
            $arr = array(
              'msg' => 'ok',
              'html' => $items,
              'txt' => '<i class="fa fa-shopping-basket fa-fw"></i> ' . $msg_public_header21 . ($MCCART->cartCount() > 0 ? $clearallbasket : ''),
              'hidden' => $MCOPS->hidden(),
              'totals' => $MCCART->buildBasketTotals(),
              'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
              'count' => $MCCART->cartCount(),
              '_fr' => 'CASE RE-ORDER'
            );
          } else {
            $arr['text'][1] = $msg_checkout_reorder[2];
            echo $MCJSON->encode($arr);
            exit;
          }
        } else {
          $arr['text'][1] = $msg_checkout_reorder[0];
          echo $MCJSON->encode($arr);
          exit;
        }
      } else {
        $arr['text'][1] = $msg_checkout_reorder[1];
        echo $MCJSON->encode($arr);
        exit;
      }
      break;
    case 'gift':
      $form  = array(
        'id' => (isset($_POST['gift']['id']) ? (int) $_POST['gift']['id'] : ''),
        'fr_name' => (isset($_POST['gift']['fn']) ? $_POST['gift']['fn'] : ''),
        'fr_email' => (isset($_POST['gift']['fe']) && mswIsValidEmail($_POST['gift']['fe']) ? $_POST['gift']['fe'] : ''),
        'to_name' => (isset($_POST['gift']['tn']) ? $_POST['gift']['tn'] : ''),
        'to_email' => (isset($_POST['gift']['te']) && mswIsValidEmail($_POST['gift']['te']) ? $_POST['gift']['te'] : ''),
        'msg' => (isset($_POST['gift']['msg']) ? $_POST['gift']['msg'] : '')
      );
      $gf = mc_getTableData('giftcerts', 'id', $form['id'], ' AND `enabled` = \'yes\'');
      if (isset($gf->id)) {
        if ($form['fr_name'] == '' || $form['fr_email'] == '' || $form['to_name'] == '' || $form['to_email'] == '') {
          $MCOPS->log('Gift Coupon Error: ' . implode(mc_defineNewline(), $formErrors));
          $formErrors[] = '<i class="fa fa-angle-right fa-fw"></i> ' . $msg_shop_basket[2];
          $arr          = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
        } else {
          $MCGIFT->addGiftToBasket($form, $gf);
          $items = $MCCART->buildDialogBasket();
          $arr = array(
            'msg' => 'ok',
            'html' => $items,
            'txt' => '<i class="fa fa-shopping-basket fa-fw"></i> ' . $msg_public_header21 . ($MCCART->cartCount() > 0 ? $clearallbasket : ''),
            'hidden' => $MCOPS->hidden(),
            'count' => $MCCART->cartCount(),
            'totals' => $MCCART->buildBasketTotals(),
            'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
            '_fr' => 'CASE GIFT'
          );
        }
      }
      break;
    case 'update-basket':
      $ret = $MCOPS->update();
      switch($ret[0]) {
        case 'ok':
          $arr = array(
            'msg' => 'ok',
            'html' => $ret[1],
            'qty' => $ret[2],
            'min-checkout' => $ret[3],
            'total' => $MCOPS->formatSystemCurrency($MCOPS->cartTotal()),
            'hidden' => $MCOPS->hidden(),
            'totals' => $MCCART->buildBasketTotals(),
            'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
            'count' => $MCCART->cartCount(),
            '_fr' => 'CASE UPDATE BASKET'
          );
          break;
        case 'err':
          $arr['text'][1] = $ret[1];
          break;
      }
      break;
    case 'delete':
      define('NO_GLOBAL_DISCOUNT_MENU', 1);
      $cost = $MCOPS->delete();
      if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) {
        $arr = array(
          'msg' => 'ok',
          'cost' => $cost,
          'count' => $MCCART->cartCount(),
          '_fr' => 'CASE DELETE'
        );
      } else {
        $items = $MCCART->buildDialogBasket();
        $arr = array(
          'msg' => 'empty',
          'html' => $items,
          'hidden' => $MCOPS->hidden(),
          'totals' => $MCCART->buildBasketTotals(),
          'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
          'count' => $MCCART->cartCount(),
          '_fr' => 'CASE DELETE'
        );
      }
      break;
    case 'delete-check-item':
      $MCOPS->deleteItem();
      $arr = array(
        'msg' => 'ok',
        'total' => $MCOPS->formatSystemCurrency($MCOPS->cartTotal()),
        'cnt' => $MCOPS->cartCount(),
        'hidden' => $MCOPS->hidden(),
        'totals' => $MCCART->buildBasketTotals(),
        'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
        'count' => $MCCART->cartCount(),
        '_fr' => 'CASE DELETE CHECKOUT ITEM'
      );
      break;
    case 'gift-edit':
      $ret = $MCGIFT->updateGiftCert();
      $html = $MCCKO->buildBasketItems($_GET['prd']);
      switch($ret[0]) {
        case 'ok':
          $arr = array(
            'msg' => 'ok',
            'html' => $html,
            'total' => $MCOPS->formatSystemCurrency($MCOPS->cartTotal()),
            '_fr' => 'CASE GIFT EDIT'
          );
          break;
        case 'err':
          $arr = array(
            'msg' => 'err',
            'txt' => $mc_giftcert[3],
            '_fr' => 'CASE GIFT EDIT'
          );
          break;
      }
      break;
    case 'pers-edit':
      $ret = $MCOPS->updatePersonalisation();
      $html = $MCCKO->buildBasketItems($_GET['prd']);
      switch($ret[0]) {
        case 'ok':
          $arr = array(
            'msg' => 'ok',
            'html' => $html,
            'total' => $MCOPS->formatSystemCurrency($MCOPS->cartTotal()),
            '_fr' => 'CASE PERS EDIT'
          );
          break;
        case 'err':
          $arr = array(
            'msg' => 'err',
            'id' => $ret[2],
            'txt' => $ret[1],
            '_fr' => 'CASE PERS EDIT'
          );
          break;
      }
      break;
    case 'checkout':
      // If already logged in, but login is re-submitted, clear and reset..
      if (isset($_GET['pf']) && $_GET['pf'] == 'yes') {
        if (isset($_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))])) {
          unset($_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))], $loggedInUser['id']);
        }
      }
      $form  = array(
        'vis' => (isset($_GET['v']) && in_array($_GET['v'],array('acc','guest')) ? $_GET['v'] : 'guest'),
        'email' => (isset($_POST['chk']['em']) ? $_POST['chk']['em'] : ''),
        'pass' => (isset($_POST['chk']['pw']) ? $_POST['chk']['pw'] : '')
      );
      if (!isset($loggedInUser['id']) && $form['vis'] == 'acc') {
        if ($form['email'] == '' || $form['pass'] == '') {
          $formErrors[] = $public_accounts_validation[10];
        } else {
          $usr = $MCACC->login($form);
          if (!isset($usr->id)) {
            $formErrors[] = $public_accounts_validation[11];
          } else {
            if ($usr->enablelog == 'yes') {
              $MCACC->log($usr);
            }
            $_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))] = $form['email'];
            $first = $MCCKO->gateways($mcSystemPaymentMethods, '', 'yes', $usr->type);
            if (isset($first[0])) {
              $url = $MCRWR->url(array(
                $MCRWR->config['slugs']['hlp'] . '/' . $first[0],
                'help=' . $first[0]
              ));
            }
            $arr = array(
              'msg' => 'ok',
              'url' => $MCRWR->url(array('checkpay')),
              'fields' => $MCACC->getaddresses($usr->id),
              'methods' => $MCCKO->gateways($mcSystemPaymentMethods, '', 'no', $usr->type),
              'meth-img' => (isset($first[0]) ? THEME_FOLDER . '/images/gateways/' . $first[0] : '#'),
              'meth-url' => (isset($first[0]) ? $url : '#')
            );
          }
        }
        if (!empty($formErrors)) {
          $MCOPS->log('Checkout Error: ' . implode(mc_defineNewline(), $formErrors));
          $arr = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
        }
      } else {
        $arr = array(
          'msg' => 'ok',
          'url' => $MCRWR->url(array('checkpay')),
          'hidden' => $MCOPS->hidden(),
          'totals' => $MCCART->buildBasketTotals(),
          'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
          'count' => $MCCART->cartCount(),
          'sub' => $MCPROD->formatSystemCurrency($MCCART->cartTotal()),
          '_fr' => 'CASE CHECKOUT'
        );
      }
      break;
    case 'checkout-ops':
      $_SESSION['mc_checkout'] = array();
      $pos = (isset($_GET['nav']) ? (in_array($_GET['nav'], array('pay', 'stripe')) ? $_GET['nav'] : (int) $_GET['nav']) : 'fail');
      $MCOPS->log('Checkout ops position: '. $pos);
      $form  = array(
        'bill' => array(
          'nm' => (isset($_POST['bill']['nm']) ? $_POST['bill']['nm'] : ''),
          'em' => (isset($_POST['bill']['em']) && mswIsValidEmail($_POST['bill']['em']) ? $_POST['bill']['em'] : ''),
          '1' => (isset($_POST['bill']['country']) ? (int) $_POST['bill']['country'] : 'mc_c_fail'),
          '2' => (isset($_POST['bill'][1]) ? $_POST['bill'][1] : ''),
          '3' => (isset($_POST['bill'][2]) ? $_POST['bill'][2] : ''),
          '4' => (isset($_POST['bill'][3]) ? $_POST['bill'][3] : ''),
          '5' => (isset($_POST['bill'][4]) ? $_POST['bill'][4] : ''),
          '6' => (isset($_POST['bill'][5]) ? $_POST['bill'][5] : '')
        ),
        'ship' => array(
          'nm' => (isset($_POST['ship']['nm']) ? $_POST['ship']['nm'] : ''),
          'em' => (isset($_POST['ship']['em']) && mswIsValidEmail($_POST['ship']['em']) ? $_POST['ship']['em'] : ''),
          '1' => (isset($_POST['ship']['country']) ? (int) $_POST['ship']['country'] : 'mc_c_fail'),
          '2' => (isset($_POST['ship'][1]) ? $_POST['ship'][1] : ''),
          '3' => (isset($_POST['ship'][2]) ? $_POST['ship'][2] : ''),
          '4' => (isset($_POST['ship'][3]) ? $_POST['ship'][3] : ''),
          '5' => (isset($_POST['ship'][4]) ? $_POST['ship'][4] : ''),
          '6' => (isset($_POST['ship'][5]) ? $_POST['ship'][5] : ''),
          '7' => (isset($_POST['ship'][6]) ? $_POST['ship'][6] : '')
        ),
        'zones' => array(
          'id' => (isset($_POST['ship_code']) ? (int) $_POST['ship_code'] : '0'),
          'ship_id' => (isset($_POST['ship_id']) ? $_POST['ship_id'] : '0')
        ),
        'coupon' => (isset($_POST['coupon']) ? $_POST['coupon'] : ''),
        'account' => (isset($_POST['acc-open']) && in_array($_POST['acc-open'], array('yes','no',)) ? $_POST['acc-open'] : 'no'),
        'notes' => (isset($_POST['notes']) ? $_POST['notes'] : ''),
        'method' => (isset($_POST['payment-type']) && array_key_exists($_POST['payment-type'], $mcSystemPaymentMethods) ? $_POST['payment-type'] : 'paypal'),
        'accountID' => (isset($loggedInUser['id']) ? $loggedInUser['id'] : '0'),
        'wish' => (isset($_POST['wish']) ? (int) $_POST['wish'] : '0')
      );
      $MCOPS->log(print_r($form, true));
      // Billing address..
      foreach ($form['bill'] AS $k => $v) {
        switch($k) {
          case 'em':
            if (in_array($v, array('','mc_c_fail'))) {
              $MCOPS->log('Billing Error: ' . implode(mc_defineNewline(), $formErrors));
              $formErrors[] = $chk_payment_errs[6];
              $arr = array('msg' => 'err', 'nav' => 1, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
              echo $MCJSON->encode($arr);
              exit;
            } else {
              // For guest checkout, does account exist for email used?
              if ($form['accountID'] == 0) {
                $BemExists = mc_getTableData('accounts', 'email', mc_safeSQL($v), ' AND `enabled` = \'yes\'');
                if (isset($BemExists->id)) {
                  $MCOPS->log('Guest checkout initialised, but account exists with email address used.');
                  $formErrors[] = str_replace('{email}', mc_safeHTML($v), $chk_payment_errs[10]);
                  $arr = array('msg' => 'err-guest-exists', 'nav' => 1, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
                  echo $MCJSON->encode($arr);
                  exit;
                }
              }
            }
            break;
          default:
            if ($k != '3' && in_array($v, array('','mc_c_fail'))) {
              $MCOPS->log('Billing Error: ' . implode(mc_defineNewline(), $formErrors));
              $formErrors[] = $chk_payment_errs[0];
              $arr = array('msg' => 'err', 'nav' => 1, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
              echo $MCJSON->encode($arr);
              exit;
            }
            break;
        }
      }
      // Store..
      if (isset($loggedInUser['id'])) {
        $MCOPS->log('Account Checkout');
        $MCACC->addresses($loggedInUser['id'], $form);
      }  else {
        $MCOPS->log('Guest Checkout');
        $_SESSION['mc_checkout']['bill'] = $form['bill'];
      }
      // For debug..
      $isWish = $MCOPS->wishBasketCnt();
      if ($isWish == 'yes') {
        $MCOPS->log('Wish List item present in checkout');
      }
      if (defined('KILL_CHECKOUT_SHIPPING')) {
        $MCOPS->log('Kill shipping enabled');
      }
      // Shipping address..
      if ($pos != 2 && !defined('KILL_CHECKOUT_SHIPPING') && $isWish == 'no') {
        foreach ($form['ship'] AS $k => $v) {
          switch($k) {
            case 'em':
              if (in_array($v, array('','mc_c_fail'))) {
                $MCOPS->log('Shipping Error: ' . implode(mc_defineNewline(), $formErrors));
                $formErrors[] = $chk_payment_errs[7];
                $arr = array('msg' => 'err', 'nav' => 1, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
                echo $MCJSON->encode($arr);
                exit;
              }
              break;
            default:
              if ($k != '3' && in_array($v, array('','mc_c_fail'))) {
                $MCOPS->log('Shipping Error: ' . implode(mc_defineNewline(), $formErrors));
                $formErrors[] = $chk_payment_errs[1];
                $arr = array('msg' => 'err', 'nav' => 2, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
                echo $MCJSON->encode($arr);
                exit;
              }
              break;
          }
        }
      }
      // Store..
      if (!defined('KILL_CHECKOUT_SHIPPING') && isset($loggedInUser['id'])) {
        $MCOPS->log('Getting addresses for logged in user');
        $MCACC->addresses($loggedInUser['id'], $form);
      } else {
        $_SESSION['mc_checkout']['ship'] = $form['ship'];
      }
      // Shipping check..
      if (!defined('KILL_CHECKOUT_SHIPPING') && $pos > 3) {
        if ($form['zones']['id'] == '0') {
          $MCOPS->log('Shipping Error: ' . implode(mc_defineNewline(), $formErrors));
          $formErrors[] = $chk_payment_errs[3];
          $arr = array('msg' => 'err', 'nav' => 3, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
          echo $MCJSON->encode($arr);
          exit;
        }
        if ($form['zones']['ship_id'] == '0') {
          $MCOPS->log('Shipping Error: ' . implode(mc_defineNewline(), $formErrors));
          $formErrors[] = $chk_payment_errs[4];
          $arr = array('msg' => 'err', 'nav' => 3, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
          echo $MCJSON->encode($arr);
          exit;
        }
      }
      // Coupon check..
      if ($pos > 4) {
        if ($form['coupon']) {
          $coupon = $MCOPS->coupon($form['coupon']);
          if ($coupon['msg'] != 'ok') {
            $MCOPS->log('Coupon Error: ' . implode(mc_defineNewline(), $formErrors));
            $formErrors[] = '<i class="fa fa-exclamation fa-fw"></i> ' . $coupon['msg'];
            $arr = array('msg' => 'err', 'nav' => 4, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
            echo $MCJSON->encode($arr);
            exit;
          }
        } else {
          $_SESSION['couponCode'] = array();
        }
      }
      // Check account..
      if ($pos > 5) {
        if ($form['account'] == 'yes') {
          $usr = $MCACC->user(array(
            'email' => $form['bill']['em']
          ));
          if (isset($usr['id'])) {
            $MCOPS->log('Account Error: ' . implode(mc_defineNewline(), $formErrors));
            $formErrors[] = str_replace('{email}', mc_safeHTML($form['bill']['em']), $chk_payment_errs[8]);
            $arr          = array('msg' => 'err', 'nav' => 5, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
            echo $MCJSON->encode($arr);
            exit;
          }
        }
      }
      // Additional instructions..
      switch($pos) {
        // Billing address..
        case '2':
          $arr = array(
            'msg' => 'ok',
            'nav' => (defined('KILL_CHECKOUT_SHIPPING') ? 4 : $pos),
            '_fr' => 'CASE 2 BILLING'
          );
          break;
        // Shipping address..
        case '3':
          $srg = $MCOPS->regions($form['ship'][1]);
          $arr = array(
            'msg' => $srg[0],
            'nav' => $pos,
            'ship_ops' => (isset($srg[2]) ? 'err' : $srg[1]),
            'flags' => (isset($srg[2]) ? $srg[2] : ''),
            'txt' => array(
              (isset($srg[1]) ? $srg[1] : ''),
              $mc_global[2],
              $mc_global[3]
            ),
            '_fr' => 'CASE 3 SHIPPING'
          );
          break;
        // Shipping cost..
        case '4':
          $MCOPS->setShipping($form['zones']['ship_id']);
          $arr = array(
            'msg' => 'ok',
            'nav' => $pos,
            'hidden' => $MCOPS->hidden(),
            'totals' => $MCCART->buildBasketTotals(),
            'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
            '_fr' => 'CASE 4 SHIP COST'
          );
          break;
        // Coupon..
        case '5':
          // Reset shipping if coupon code was removed..
          if (empty($_SESSION['couponCode']) && $form['zones']['ship_id']) {
            $MCOPS->setShipping($form['zones']['ship_id']);
          }
          $arr = array(
            'msg' => 'ok',
            'nav' => (isset($loggedInUser['id']) ? 6 : $pos),
            'hidden' => $MCOPS->hidden(),
            'totals' => $MCCART->buildBasketTotals(),
            'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
            '_fr' => 'CASE 5 COUPON CODE'
          );
          break;
        // Account..
        case '6':
          $_SESSION['sale-create-account'] = $form['account'];
          $arr = array(
            'msg' => 'ok',
            'nav' => $pos,
            '_fr' => 'CASE 6 ACCOUNT'
          );
          break;
        // Notes..
        case '7':
          $arr = array(
            'msg' => 'ok',
            'nav' => $pos,
            '_fr' => 'CASE 7 NOTES'
          );
          break;
        // Pay..
        case 'pay':
        case 'stripe':
          // Does terms and conditions exist?
          if ($SETTINGS->tc == 'yes' && !isset($_POST['tandc'])) {
            $MCOPS->log('T&C Error: ' . implode(mc_defineNewline(), $formErrors));
            $formErrors[] = $chk_payment_errs[9];
            $arr          = array('msg' => 'err', 'nav' => 7, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
            echo $MCJSON->encode($arr);
            exit;
          }
          $paymentMethod = $form['method'];
          $ssl           = mc_detectSSLConnection($SETTINGS);
          // At this point check that we need to go through a gateway..
          // If no restrictions are set on downloads and cart only contains downloads, just process..
          if ($MCCART->allDownloadItemsInCart() == '0' && $MCCART->cartFreebies() > 0 && $MCCART->cartTotal() == '0') {
            // Check there are no restrictions on free products, ie, no paid products are required..
            if ($SETTINGS->freeDownloadRestriction == '0') {
              $MCOPS->log('Freebie checkout with no download restriction');
              $paymentMethod = 'download';
            } else {
              $MCOPS->log('System Error: ' . implode(mc_defineNewline(), $formErrors));
              $formErrors[] = str_replace('{count}', $SETTINGS->freeDownloadRestriction, $public_checkout136);
              $arr          = array('msg' => 'err', 'nav' => 'basket', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
              echo $MCJSON->encode($arr);
              exit;
            }
          }
          // Check for free order containing tangible items..
          if ($MCCART->allDownloadItemsInCart() > 0) {
            if (isset($_POST['t-total']) && in_array($_POST['t-total'], array('0','0.00')) && $MCCART->cartTotal() == '0') {
              $paymentMethod = 'free';
            }
          }
          // Check the process constant is loaded..
          if (!defined('CHECKOUT_LOADED')) {
            define('CHECKOUT_LOADED', 1);
          }
          $mc_pay_status = 'err';
          switch($paymentMethod) {
            // None gateway payment options..
            case 'bank';
            case 'phone';
            case 'cheque';
            case 'cod';
              $MCOPS->log('Processing OTHER (' . ucfirst($paymentMethod) . ') payment option');
              include(PATH . 'control/gateways/process-other.php');
              break;
            // None gateway, on account
            case 'account';
              $MCOPS->log('Processing OTHER (' . ucfirst($paymentMethod) . ') payment option');
              include(PATH . 'control/gateways/process-on-account.php');
              break;
            // Downloads / free orders / virtual orders..
            case 'download':
            case 'free':
              $MCOPS->log('Processing FREE payment option');
              include(PATH . 'control/gateways/process-free.php');
              break;
            // Payment gateways..
            default:
              $MCOPS->log('Processing GATEWAY payment option');
              include(PATH . 'control/gateways/process-payment.php');
              break;
          }
          if ($mc_pay_status == 'ok') {
            $arr = array(
              'msg' => 'ok',
              'nav' => $pos,
              'hidden' => $MCOPS->hidden(),
              'totals' => $MCCART->buildBasketTotals(),
              'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
              'redir' => (isset($redrWin) && $redrWin ? $redrWin : $MCRWR->url(array('cart-error'))),
              '_fr' => 'PAY'
            );
            // Adjustments for certain gateway APIs..
            switch($pos) {
              // Start stripe checkout session..
              case 'stripe':
                if (isset($buyCode, $id)) {
                  $ret = $GATEWAY->checkout(array(
                    'custom' => $buyCode . '-' . $id . '-mswcart',
                    'ssl' => mc_detectSSLConnection($SETTINGS),
                    'lang' => array(
                      str_replace('{store}', '', $public_checkout18)
                    )
                  ));
                  $GATEWAY->writeLog($id, 'Stripe Checkout Session Init: ' . print_r($ret, true));
                  $arr['msg'] = ($ret[0] == 'ok' && $ret[1]->id ? 'ok' : 'err');
                  $arr['s_id'] = ($ret[0] == 'ok' && $ret[1]->id ? $ret[1]->id : '');
                  $arr['html'] = $ret[2];
                  $MCOPS->clearCart();
                }
                break;
            }
          }
          break;
      }
      break;
    case 'checkout-init':
      $MCOPS->log('Initialise Checkout');
      include(PATH . 'control/classes/class.html.php');
      $MCHTML           = new mcHtml();
      $MCHTML->settings = $SETTINGS;
      $addarr           = array(array(),array());
      $mc_states        = array();
      $ship             = (isset($_GET['ship']) ? (int) $_GET['ship'] : '0');
      $bill             = (isset($_GET['bill']) ? (int) $_GET['bill'] : '0');
      // Get arrays.
      if (file_exists(PATH . 'control/states/' . $bill . '.php')) {
        $MCOPS->log('Using states file for billing: control/states/' . $bill . '.php');
        include(PATH . 'control/states/' . $bill . '.php');
        $addarr[0] = $mc_states;
      }
      if ($ship != $bill) {
        if ($ship > 0 && file_exists(PATH . 'control/states/' . $ship . '.php')) {
          $MCOPS->log('Using states file for shipping: control/states/' . $ship . '.php');
          include(PATH . 'control/states/' . $ship . '.php');
          $addarr[1] = $mc_states;
        }
      } else {
        $addarr[1] = $mc_states;
      }
      // Is this a wish list purchase?
      $isWish = 'no';
      if ($SETTINGS->en_wish == 'yes') {
        $isWish = $MCOPS->wishBasketCnt();
      }
      // Is visitor logged in?
      $addr   = array(array(),array(),array('wishzone' => 0));
      if (isset($loggedInUser['id'])) {
        $MCOPS->log('Initialising Checkout [Account Checkout]');
        $addr = $MCACC->getaddresses($loggedInUser['id']);
      } else {
        $MCOPS->log('Initialising Checkout [Guest Checkout]');
        $addr = $MCCKO->getaddresses();
      }
      // Wish list purchase checks..
      if ($isWish == 'no') {
        $srg = $MCOPS->regions($ship);
      } else {
        $MCOPS->log('Wish List Checkout. Zones will not be displayed as wish list owner should have default zone set.');
        $srg                 = array('wish-ok','wishlist','');
        $w_addr              = $MCACC->getaddresses($MCOPS->wishBasketCnt('yes'));
        if (isset($w_addr[1]['zone'])) {
          $addr[2]['wishzone'] = $w_addr[1]['zone'];
        }
        $MCOPS->log('Check wish list zone: ' . ($addr[2]['wishzone'] > 0 ? mc_getShippingZoneArea($addr[2]['wishzone']) . ' [OK]' : 'Not Set, checkout will abort'));
      }
      $arr = array(
        'msg' => $srg[0],
        'ship_addr' => $MCHTML->loadStates('ship',(isset($addr[1]['addr5']) ? $addr[1]['addr5'] : '0'),$addarr[1]),
        'bill_addr' => $MCHTML->loadStates('bill',(isset($addr[0]['addr5']) ? $addr[0]['addr5'] : '0'),$addarr[0]),
        'ship_ops' => $srg[1],
        'flags' => (isset($srg[2]) ? $srg[2] : ''),
        'hidden' => $MCOPS->hidden(),
        'txt' => array(
          $mc_global[2],
          $chk_payment_errs[2],
          $mc_global[3],
          '_fr' => 'INITIALISE'
        ),
        'zone' => (isset($addr[2]['wishzone']) ? $addr[2]['wishzone'] : '0')
      );
      break;
    case 'shipping':
      $zone = (isset($_GET['a']) ? (int) $_GET['a'] : '0');
      $cnty = (isset($_GET['c']) ? (int) $_GET['c'] : '0');
      $ship = $MCOPS->shipping($cnty, $zone);
      // Check free download restrictions aren`t set..
      // Trigger error if restriction is in place..
      if ($SETTINGS->freeDownloadRestriction > 0) {
        if ($MCCART->cartFreebies() > 0) {
          if ($MCCART->cartCountPaidItems() < $SETTINGS->freeDownloadRestriction) {
            $MCOPS->log('Shipping Error: ' . implode(mc_defineNewline(), $formErrors));
            $formErrors[] = str_replace('{count}', $SETTINGS->freeDownloadRestriction, $msg_javascript363);
            $arr = array('msg' => 'free-err', 'nav' => 3, 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
            echo $MCJSON->encode($arr);
            exit;
          }
        }
      }
      // Do we have shipping options?
      if ($ship[0]) {
        $_SESSION['shipto']    = array();
        $_SESSION['shipto'][0] = $zone;
        $_SESSION['shipto'][1] = $cnty;
        $arr = array (
          'msg' => 'ok',
          'html' => $ship[0],
          'txt' => array(
            0 => str_replace(array(
              '{region}',
              '{country}'
            ), array(
              mc_safeHTML($ship[1]),
              mc_safeHTML($ship[2])
            ), $msg_javascript119)
          ),
          '_fr' => 'CASE SHIPPING'
        );
      } else {
        $arr = array(
          'msg' => 'err',
          'txt' => array(
            $chk_payment_errs[5],
            $mc_global[2],
            $mc_global[3]
          ),
          '_fr' => 'CASE SHIPPING'
        );
      }
      break;
    case 'insurance':
      $insur = $MCOPS->insurance();
      if (isset($insur['class'])) {
        $arr = array(
          'msg' => 'ok',
          'class' => $insur['class'],
          'html' => $insur['html'],
          'hidden' => $MCOPS->hidden(),
          'totals' => $MCCART->buildBasketTotals(),
          'grand' => $MCPROD->formatSystemCurrency($MCCART->buildBasketTotals(true)),
          '_fr' => 'CASE INSURANCE'
        );
      } else {
        $MCOPS->log('NO shipping insurance will be applied');
      }
      break;
    case 'clear-basket':
      $MCOPS->clearCart();
      $arr = array(
        'msg' => 'ok',
        'count' => '0',
        '_fr' => 'CASE CLEAR BASKET'
      );
      break;
    case 'clear-dialog-basket':
      $MCOPS->clearCart();
      $items = $MCCART->buildDialogBasket();
      $arr = array(
        'msg' => 'ok',
        'html' => $items,
        'txt' => '<i class="fa fa-shopping-basket fa-fw"></i> ' . $msg_public_header21,
        'count' => $MCCART->cartCount(),
        '_fr' => 'CASE CLEAR ALL DIALOG BASKET'
      );
      break;
    case 'build-basket':
      $items = $MCCART->buildDialogBasket();
      $arr = array(
        'msg' => 'ok',
        'html' => $items,
        'txt' => '<i class="fa fa-shopping-basket fa-fw"></i> ' . $msg_public_header21 . ($MCCART->cartCount() > 0 ? $clearallbasket : ''),
        'count' => $MCCART->cartCount(),
        '_fr' => 'CASE BUILD BASKET'
      );
      break;
    case 'menu-panel':
      $_SESSION[mc_encrypt('store_menu_panel' . SECRET_KEY)] = $_GET['pnl'];
      $arr['status'] = 'ok';
      break;
  }
}

$MCOPS->log('RESPONSE: ' . (is_array($arr) ? print_r($arr, true) : ''));
echo $MCJSON->encode($arr);
exit;

?>