<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/payment-methods.php');

// Enable/disable..
if (isset($_GET['enable']) || isset($_GET['disable'])) {
  $MCGWY->enableDisablePaymentMethods();
  header("Location: ?p=payment-methods");
  exit;
}

$pageTitle = mc_cleanDataEntVars($msg_javascript168) . ': ' . $pageTitle;

// Configure payment method..
if (isset($_GET['conf'])) {
  // Update payment methods..
  if (isset($_POST['process'])) {
    $MCGWY->updatePaymentMethods();
    $OK = true;
  }
  $loadiBox   = true;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/catalogue/payment-method.php');
  include(PATH . 'templates/footer.php');
  exit;
}


include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/payment-methods.php');
include(PATH . 'templates/footer.php');

?>
