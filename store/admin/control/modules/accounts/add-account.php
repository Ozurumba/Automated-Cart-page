<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'accounts/add-account.php');

// Check..
if (isset($_GET['chkmail'])) {
  $msg = 'err';
  $txt = '';
  if ($_POST['name'] == '' || !mswIsValidEmail($_POST['email'])) {
    $txt = $msg_addccts39;
  } else {
    if (isset($_POST['process']) && $_POST['pass'] == '') {
      $txt = $msg_addccts39;
    } else {
      if (isset($_POST['update'])) {
        $A = mc_getTableData('accounts', 'email', mc_safeSQL($_POST['email']),' AND `id` != \'' . (int) $_POST['update'] . '\'');
      } else {
        $A = mc_getTableData('accounts', 'email', mc_safeSQL($_POST['email']));
      }
    }
    if (isset($A->id)) {
      $txt = $msg_addccts40;
    } else {
      $msg = 'OK';
    }
  }
  echo $JSON->encode(array(
    'msg' => $msg,
    'text' => $txt
  ));
  exit;
}

// Add..
if (isset($_POST['process'])) {
  if ($_POST['name'] && $_POST['email']) {
    if (mswIsValidEmail($_POST['email'])) {
      $aType = (isset($_POST['type']) && in_array($_POST['type'], array('personal','trade')) ? $_POST['type'] : 'personal');
      $tCats = (!empty($_POST['cat']) ? $_POST['cat'] : array());
      $C     = mc_getTableData('accounts', 'email', mc_safeSQL($_POST['email']));
      if (!isset($C->id)) {
        $MCACC->addAccount();
        // Notification email..
        if (isset($_POST['send']) && $_POST['send'] == 'yes') {
          include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
          $sbj = str_replace('{website}', $SETTINGS->website, ($aType == 'personal' ? $msg_emails28 : $msg_emails40));
          $msg = LANG_PATH . 'admin/new-account' . ($aType == 'trade' ? '-trade' : '') . '.txt';
          $MCMAIL->addTag('{NAME}', $_POST['name']);
          $MCMAIL->addTag('{EMAIL}', $_POST['email']);
          $MCMAIL->addTag('{PASS}', $_POST['pass']);
          $MCMAIL->addTag('{DISCOUNT}', (isset($_POST['tradediscount']) ? $_POST['tradediscount'] : '0'));
          $MCMAIL->addTag('{MIN}', (isset($_POST['minqty']) && $_POST['minqty'] > 0 ? (int) $_POST['minqty'] : 'N/A'));
          $MCMAIL->addTag('{MAX}', (isset($_POST['maxqty']) && $_POST['maxqty'] > 0 ? (int) $_POST['maxqty'] : 'N/A'));
          $MCMAIL->addTag('{CAT_RESTRICTIONS}', mc_tradeCatRestr($tCats));
          $MCMAIL->sendMail(array(
            'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
            'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
            'to_email' => $_POST['email'],
            'to_name' => $_POST['name'],
            'subject' => $sbj,
            'replyto' => array(
              'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
              'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
            ),
            'template' => $msg,
            'language' => $SETTINGS->languagePref
          ));
          $MCMAIL->smtpClose();
        }
        $OK = true;
      } else {
        $AER = $msg_addccts11;
      }
    } else {
      $AER = $msg_addccts12;
    }
  } else {
    $AER = $msg_addccts13;
  }
}

// Update..
if (isset($_POST['update'])) {
  if ($_POST['name'] && $_POST['email']) {
    if (mswIsValidEmail($_POST['email'])) {
      $C = mc_getTableData('accounts', 'email', mc_safeSQL($_POST['email']),' AND `id` != \'' . (int) $_POST['update'] . '\'');
      if (!isset($C->id)) {
        $MCACC->updateAccount();
        $OK2 = true;
      } else {
        $AER = $msg_addccts11;
      }
    } else {
      $AER = $msg_addccts12;
    }
  } else {
    $AER = $msg_addccts13;
  }
}

// Generate pass..
if (isset($_GET['passgen'])) {
  echo $JSON->encode(array(
    $MCACC->passGen()
  ));
  exit;
}

$pageTitle     = mc_cleanDataEntVars((isset($_GET['edit']) ? $msg_addccts4 : $msg_addccts3)) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/accounts/add-account.php');
include(PATH . 'templates/footer.php');

?>