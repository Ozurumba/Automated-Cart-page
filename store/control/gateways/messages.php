<?php

// Left menu boxes..
include(PATH . 'control/left-box-controller.php');

switch($_GET['p']) {

  //========================
  // PAYMENT CANCELLED
  //========================

  case 'cancel':
    // Try and find order for this cancellation and inform webmaster..
    include(PATH . 'control/gateways/buyer-cancelled.php');
    $headerTitleText = $public_checkout19 . ': ' . $headerTitleText;
    $breadcrumbs = array(
      $mc_checkout[0],
      $public_checkout19
    );
    $MCCART->clearCart();
    include(PATH . 'control/header.php');
    $tpl             = mc_getSavant();
    $tpl->assign('TITLE', $public_checkout19);
    $tpl->assign('TEXT', array(
      $public_checkout19,
      str_replace('{website}', mc_cleanData($SETTINGS->website), $public_checkout20),
      $public_checkout116
    ));
    // Global..
    include(PATH . 'control/system/global.php');
    $tpl->display(THEME_FOLDER . '/checkout-message.tpl.php');
    break;

  //========================
  // PAYMENT DECLINED
  //========================

  case 'declined':
    $headerTitleText = $public_checkout122 . ': ' . $headerTitleText;
    $breadcrumbs = array(
      $mc_checkout[0],
      $public_checkout122
    );
    $MCCART->clearCart();
    include(PATH . 'control/header.php');
    $tpl             = mc_getSavant();
    $tpl->assign('TITLE', $public_checkout122);
    $tpl->assign('TEXT', array(
      $public_checkout122,
      str_replace(array('{website}','{reason}'), array(mc_cleanData($SETTINGS->website),(isset($_GET['errorMessage']) ? urldecode($_GET['errorMessage']) : 'N/A')), $public_checkout123),
      $public_checkout116
    ));

    // Global..
    include(PATH . 'control/system/global.php');

    $tpl->display(THEME_FOLDER . '/checkout-message.tpl.php');
    break;

  //========================
  // MESSAGE
  //========================

  case 'message':
    if (isset($_SESSION['mc_checkrdr_' . mc_encrypt(mc_encrypt(SECRET_KEY))])) {
      header("Location: " . $_SESSION['mc_checkrdr_' . mc_encrypt(mc_encrypt(SECRET_KEY))]);
      exit;
    }
    $headerTitleText = $public_checkout31 . ': ' . $headerTitleText;
    $breadcrumbs = array(
      $mc_checkout[0],
      $public_checkout31
    );
    $MCCART->clearCart();
    include(PATH . 'control/header.php');
    $tpl             = mc_getSavant();
    $tpl->assign('TITLE', $public_checkout31);
    $tpl->assign('TEXT', array(
      $public_checkout31,
      str_replace('{website}', mc_cleanData($SETTINGS->website), $public_checkout32),
      $public_checkout116
    ));

    // Global..
    include(PATH . 'control/system/global.php');

    $tpl->display(THEME_FOLDER . '/checkout-message.tpl.php');
    break;

  //========================
  // ERROR
  //========================

  case 'error':
    $_GET['msg']     = (isset($_GET['msg']) ? urldecode($_GET['msg']) : $public_checkout131);
    $headerTitleText = $public_checkout125 . ': ' . $headerTitleText;
    $breadcrumbs = array(
      $mc_checkout[0],
      $public_checkout125
    );
    $MCCART->clearCart();
    include(PATH . 'control/header.php');
    $tpl             = mc_getSavant();
    // For relative loading..
    if (defined('SAV_PATH')) {
      $tpl->addPath('template', SAV_PATH);
    }
    $tpl->assign('TITLE', $public_checkout125);
    $tpl->assign('TEXT', array(
      $public_checkout125,
      str_replace(array(
        '{website}',
        '{msg}'
      ), array(
        mc_cleanData($SETTINGS->website),
        str_replace(array('&lt;br&gt;', '&lt;/br&gt;'),array('<br>','</br>'),mc_safeHTML($_GET['msg']))
      ), $public_checkout124),
      $public_checkout116
    ));

    // Global..
    include(PATH . 'control/system/global.php');

    $tpl->display(THEME_FOLDER . '/checkout-message.tpl.php');
    break;

}

include(PATH . 'control/footer.php');

?>