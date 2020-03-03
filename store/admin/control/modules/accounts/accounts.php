<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'accounts/accounts.php');

// Export.
if (isset($_GET['export'])) {
  include(MCLANG . 'accounts/add-account.php');
  include(REL_PATH . 'control/classes/class.download.php');
  $DL        = new mcDownload();
  $MCACC->dl = $DL;
  $MCACC->exportAccounts('personal');
}

// Message..
if (isset($_GET['message'])) {
  // Update notes..
  if (isset($_POST['msg'])) {
    $MCACC->updateMessage();
    echo $JSON->encode(array(
      'OK'
    ));
    exit;
  }
  include(PATH . 'templates/windows/account-message.php');
  exit;
}

// Notes..
if (isset($_GET['notes'])) {
  // Update notes..
  if (isset($_POST['notes'])) {
    $MCACC->updateNotes();
    echo $JSON->encode(array(
      'OK'
    ));
    exit;
  }
  include(PATH . 'templates/windows/account-notes.php');
  exit;
}

// Status..
if (isset($_GET['accstatus'])) {
  // Update status..
  if (isset($_POST['reason'])) {
    $MCACC->updateStatus();
    echo $JSON->encode(array(
      'OK'
    ));
    exit;
  }
  include(PATH . 'templates/windows/account-status.php');
  exit;
}

// Delete..
if (!empty($_POST['del'])) {
  $_POST['del'] = array_unique($_POST['del']);
  $MCACC->deleteAccounts();
  header("Location: ?p=accounts&deleted=" . count($_POST['del']));
  exit;
}

$pageTitle    = mc_cleanDataEntVars($msg_admin3_0[42]) . ': ' . $pageTitle;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/accounts/accounts.php');
include(PATH . 'templates/footer.php');

?>
