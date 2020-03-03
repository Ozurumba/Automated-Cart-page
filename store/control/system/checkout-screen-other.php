<?php

if (!isset($_GET['checkout-rdr'])) {
  exit;
}

include(MCLANG . 'payment.php');
include(MCLANG . 'accounts.php');

$chop = explode('-', $_GET['checkout-rdr']);
$saleID = (isset($chop[0]) ? (int) $chop[0] : '');
$saleCode = (isset($chop[1]) && ctype_alnum($chop[1]) ? $chop[1] : '');

if ($saleID && $saleCode) {
  // GET ORDER..
  $HDLR           = new paymentHandler();
  $HDLR->settings = $SETTINGS;
  $HDLR->rwr      = $MCRWR;
  $SALE_ORDER     = $HDLR->getOrderInfo($saleCode, $saleID);

  // CHECK IF ORDER IS VALID..
  if (isset($SALE_ORDER->id)) {

    $HDLR->writeLog($saleID, 'Displaying message and payment instructions to ' . mc_cleanData($SALE_ORDER->bill_1) . ' for ' . $mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['lang'], $mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['lang']);

    // INSTRUCTIONS..
    $htmlInstructions = mc_txtParsingEngine($mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['html']);
    $mailInstructions = mc_cleanData($mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['plain']);

    $headerTitleText = mc_cleanData($public_checkout21 . ': ' . $headerTitleText);
    $cOTxt           = $msg_public_header4 . ' ' . $public_checkout31;

    // BREADCRUMB..
    $breadcrumbs = array(
      $cOTxt
    );

    // CLEAR CART..
    $MCCART->clearCart();

    // Left menu boxes..
    $skipMenuBoxes['points']  = true;
    $skipMenuBoxes['popular'] = true;
    $skipMenuBoxes['brands']  = true;
    $skipMenuBoxes['rss']     = true;
    $skipMenuBoxes['tweets']  = true;
    include(PATH . 'control/left-box-controller.php');

    include(PATH . 'control/header.php');

    $tpl = mc_getSavant();
    $tpl->assign('TEXT', $public_accounts);
    $tpl->assign('TXT', array(
      $public_checkout50,
      str_replace('{INVOICE_NO}',mc_saleInvoiceNumber($SALE_ORDER->invoiceNo, $SETTINGS),$htmlInstructions),
      str_replace('{method}',$mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['lang'],$chk_payment_finish_other)
    ));
    $tpl->assign('URL', array(
      $MCRWR->url(array('account')),
      $MCRWR->url(array('profile')),
      $MCRWR->url(array('history')),
      $MCRWR->url(array('wishlist')),
      $MCRWR->url(array('saved-searches')),
      $MCRWR->url(array('create')),
      $MCRWR->url(array('logout')),
      $MCRWR->url(array('close'))
    ));
    // Check user is logged in to display this info..
    if (isset($loggedInUser['id'])) {
      $tpl->assign('TRADE', array(
        str_replace('{percent}', $loggedInUser['tradediscount'], $public_accounts[23]),
        ($loggedInUser['minqty'] > 0 ? str_replace('{count}', $loggedInUser['minqty'], $public_accounts[22]) : $public_accounts[24]),
        ($loggedInUser['maxqty'] > 0 ? str_replace('{count}', $loggedInUser['maxqty'], $public_accounts[22]) : $public_accounts[24]),
        ($loggedInUser['mincheckout'] > 0 ? $MCCART->formatSystemCurrency(mc_formatPrice($loggedInUser['mincheckout'])) : $public_accounts[24])
      ));
    }

    // Global..
    include(PATH . 'control/system/global.php');

    $tpl->display(THEME_FOLDER . '/checkout-finish-none-gateway.tpl.php');

    include(PATH . 'control/footer.php');

  } else {

    header("Location: " . $MCRWR->url(array('cart-error')));
    exit;

  }
} else {

  header("Location: " . $MCRWR->url(array('cart-error')));
  exit;

}

?>