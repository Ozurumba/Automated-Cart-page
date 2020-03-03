<?php

class giftCertificate extends shoppingCart {

  public $settings;

  public function giftCertificates() {
    $html    = '';
    if (defined('MC_TRADE_DISCOUNT')) {
      return '';
    }
    $wrapper = '';
    $first   = 0;
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "giftcerts` WHERE `enabled` = 'yes' ORDER BY `orderBy`") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($GC = mysqli_fetch_object($q)) {
      ++$first;
      $html .= str_replace(array(
        '{title}',
        '{image}',
        '{value}',
        '{id}',
        '{checked}',
        '{theme}',
        '{value_class}'
      ), array(
        mc_safeHTML($GC->name),
        $this->settings->ifolder . '/' . PRODUCTS_FOLDER . '/' . ($GC->image ? $GC->image : 'default_gift.png'),
        mcProducts::formatSystemCurrency(mc_formatPrice($GC->value)),
        $GC->id,
        ($first == '1' && in_array(DEFAULT_CHECKED_GIFT, array(
          '',
          '0'
        )) ? ' checked="checked"' : (DEFAULT_CHECKED_GIFT == $first ? ' checked="checked"' : '')),
        THEME_FOLDER,
        ($first == '1' && in_array(DEFAULT_CHECKED_GIFT, array(
          '',
          '0'
        )) ? 'value_bold' : (DEFAULT_CHECKED_GIFT == $first ? 'value_bold' : 'value'))
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/gift-certificate.htm'));
    }
    return ($html ? str_replace('{certificates}', trim($html), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/gift-certificates-wrapper.htm')) : '');
  }

  public function codeCreator($codeID) {
    $r = substr(md5(uniqid(rand(), 1)), 3, 10);
    return 'GC' . strtoupper($r) . $codeID;
  }

  public function redeemCode($gift, $total) {
    $new = number_format($gift->redeemed + $total, 2, '.', '');
    $red = ($new > $gift->value ? $gift->value : $new);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "giftcodes` SET
    `redeemed`   = '{$red}'
    WHERE `code` = '{$gift->code}'
    ");
  }

  public function activateCertificate($code, $id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "giftcodes` SET
    `code`     = '{$code}',
    `enabled`  = 'yes',
    `active`   = 'yes'
    WHERE `id` = '{$id}'
    ");
  }

  public function updateGiftCert() {
    $form  = array(
      'fr_name' => (isset($_POST['gift']['fn']) ? $_POST['gift']['fn'] : ''),
      'fr_email' => (isset($_POST['gift']['fe']) && mswIsValidEmail($_POST['gift']['fe']) ? $_POST['gift']['fe'] : ''),
      'to_name' => (isset($_POST['gift']['tn']) ? $_POST['gift']['tn'] : ''),
      'to_email' => (isset($_POST['gift']['te']) && mswIsValidEmail($_POST['gift']['te']) ? $_POST['gift']['te'] : ''),
      'msg' => (isset($_POST['gift']['msg']) ? $_POST['gift']['msg'] : '')
    );
    if ($form['fr_name'] && $form['fr_email'] && $form['to_name'] && $form['to_email']) {
      $slot = shoppingCart::productSlotPosition($_GET['prd']);
      if (isset($_SESSION['giftAddr'][$slot])) {
        $_SESSION['giftAddr'][$slot] = array(
          'from_name' => mc_cleanData($form['fr_name']),
          'from_email' => mc_cleanData($form['fr_email']),
          'to_name' => mc_cleanData($form['to_name']),
          'to_email' => mc_cleanData($form['to_email']),
          'message' => mc_cleanData($form['msg'])
        );
      }
      return array(
        'ok'
      );
    } else {
      return array(
        'err'
      );
    }
  }

  public function addGiftToBasket($fm, $gf) {
    $id       = $fm['id'];
    $type     = 'gift';
    $quantity = '1';
    $marker   = 'p' . $fm['id'];
    $value    = $gf->value;
    $code     = giftCertificate::genID();
    // Assign session vars if cart is empty..
    if (!isset($_SESSION['cart_count']) || (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] == 0)) {
      if (isset($_SESSION['cart_count'])) {
        shoppingCart::clearCart();
      }
      $_SESSION['cart_count'] = '0';
      $_SESSION['cost']       = array();
      $_SESSION['product']    = array();
      $_SESSION['quantity']   = array();
      $_SESSION['global']     = array();
      $_SESSION['freebies']   = array();
      $_SESSION['download']   = array();
      $_SESSION['category']   = array();
      $_SESSION['shipping']   = array();
      $_SESSION['exists']     = array();
      $_SESSION['killzone']   = array();
      $_SESSION['giftAddr']   = array();
      $_SESSION['extraCost']  = array();
      $_SESSION['wishlist']   = array();
    }
    $_SESSION['cart_count'] = $_SESSION['cart_count'] + $quantity;
    $_SESSION['product'][]  = $type . '-' . $id . '-' . $code;
    $_SESSION['cost'][]     = mc_formatPrice($value);
    $_SESSION['quantity'][] = $quantity;
    $_SESSION['freebies'][] = 'no';
    $_SESSION['download'][] = 'no';
    $_SESSION['global'][]   = '0.00';
    $_SESSION['category'][] = '0';
    $_SESSION['shipping'][] = 'no';
    $_SESSION['exists'][]   = '0';
    $_SESSION['killzone'][] = 'none';
    // Add selected address info to session..
    $_SESSION['giftAddr'][] = array(
      'from_name' => mc_cleanData($fm['fr_name']),
      'from_email' => mc_cleanData($fm['fr_email']),
      'to_name' => mc_cleanData($fm['to_name']),
      'to_email' => mc_cleanData($fm['to_email']),
      'message' => mc_cleanData($fm['msg'])
    );
    $_SESSION['extraCost'][] = '0.00';
    $_SESSION['wishlist'][]  = '0';
    return array(
      $code,
      $quantity
    );
  }

}

?>