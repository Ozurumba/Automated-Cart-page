<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'accounts/accounts.php');
include(MCLANG . 'accounts/add-account.php');

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
  // Resend verification emails..
  if (isset($_POST['resend'])) {
    include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`name`,`email` FROM `" . DB_PREFIX . "accounts`
         WHERE `id` IN(" . implode(',', $_POST['del']) . ")
         ORDER BY `id`
         ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($ACC = mysqli_fetch_object($q)) {
      $code = $MCACC->newCode($ACC->id);
      $sbj = str_replace('{website}', $SETTINGS->website, $msg_emails30);
      $msg = LANG_PATH . 'admin/resend-verification.txt';
      $MCMAIL->addTag('{NAME}', mc_cleanData($ACC->name));
      $MCMAIL->addTag('{CODE}', $code);
      $MCMAIL->sendMail(array(
        'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
        'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
        'to_email' => $ACC->email,
        'to_name' => mc_cleanData($ACC->name),
        'subject' => $sbj,
        'replyto' => array(
          'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
        ),
        'template' => $msg,
        'alive' => 'yes',
        'language' => $SETTINGS->languagePref
      ));
    }
    $MCMAIL->smtpClose();
    header("Location: ?p=daccounts&rsnt=" . count($_POST['del']) . (isset($_GET['type']) ? '&type=' . mc_safeHTML($_GET['type']) : '') . (isset($_GET['orderby']) ? '&orderby=' . mc_safeHTML($_GET['orderby']) : ''));
  } else {
    $MCACC->deleteAccounts();
    header("Location: ?p=daccounts&deleted=" . count($_POST['del']) . (isset($_GET['type']) ? '&type=' . mc_safeHTML($_GET['type']) : '') . (isset($_GET['orderby']) ? '&orderby=' . mc_safeHTML($_GET['orderby']) : ''));
  }
  exit;
}

$pageTitle    = mc_cleanDataEntVars($msg_admin3_0[51]) . ': ' . $pageTitle;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/accounts/disabled-accounts.php');
include(PATH . 'templates/footer.php');

?>
