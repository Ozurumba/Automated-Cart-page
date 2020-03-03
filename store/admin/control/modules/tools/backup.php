<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'tools/backup.php');
include(REL_PATH . 'control/classes/class.db-backup.php');

// Backup..
if (isset($_POST['process'])) {
  if (!is_writeable(PATH . 'import') || !is_dir(PATH . 'import')) {
    die('"<b>' . PATH . 'import' . '</b>" must exist and be writeable. Please check directory and permissions..');
  }
  $time     = date('H:i:s');
  $download = (isset($_POST['download']) ? $_POST['download'] : 'yes');
  $compress = (isset($_POST['compress']) ? $_POST['compress'] : 'yes');
  // Force download if off and no emails..
  if ($download == 'no' && $_POST['emails'] == '') {
    $download = 'yes';
  }
  // File path..
  if ($compress == 'yes') {
    $filepath = PATH . 'import/store-backup-' . date(mc_backupDateFormat($SETTINGS, true)) . '-' . str_replace(':', '-', $time) . '.gz';
  } else {
    $filepath = PATH . 'import/store-backup-' . date(mc_backupDateFormat($SETTINGS, true)) . '-' . str_replace(':', '-', $time) . '.sql';
  }
  // Save backup..
  $BACKUP           = new dbBackup($filepath, ($compress == 'yes' ? true : false));
  $BACKUP->settings = $SETTINGS;
  $BACKUP->doDump();
  // Copy email addresses if set..
  if ($_POST['emails'] && @file_exists($filepath)) {
    $em  = array_map('trim', explode(',',$_POST['emails']));
    $ot  = array();
    if (count($em) > 1) {
      $ot = $em;
      unset($ot[0]);
    }
    include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
    // Subject..
    $sbj = str_replace(array(
      '{website}',
      '{date}',
      '{time}'
    ), array(
      mc_cleanData($SETTINGS->website),
      date(mc_backupDateFormat($SETTINGS)),
      $time
    ), $msg_script67);
    // Body..
    $msg = LANG_PATH . 'admin/backup.txt';
    $MCMAIL->addTag('{STORE}', $SETTINGS->website);
    $MCMAIL->addTag('{DATE_TIME}', date($SETTINGS->systemDateFormat) . ' @ ' . date('H:iA'));
    $MCMAIL->addTag('{VERSION}', SCRIPT_VERSION);
    $MCMAIL->addTag('{FILE}', basename($filepath));
    // Include attachment..
    $MCMAIL->attachments[$filepath] = basename($filepath);
    // Send..
    $MCMAIL->sendMail(array(
      'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
      'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
      'to_email' => $em[0],
      'to_name' => $SETTINGS->website,
      'subject' => $sbj,
      'replyto' => array(
        'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
        'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
      ),
      'template' => $msg,
      'add-emails' => (!empty($ot) ? implode(',',$ot) : ''),
      'language' => $SETTINGS->languagePref
    ));
    $MCMAIL->smtpClose();
  }
  // Download..
  if ($download == 'yes') {
    include(REL_PATH . 'control/classes/class.download.php');
    $DL = new mcDownload();
    $DL->dl($filepath, ($compress == 'yes' ? 'application/x-compressed' : 'text/plain'));
  } else {
    if (@file_exists($filepath)) {
      @unlink($filepath);
    }
    $OK = true;
  }
}

$pageTitle    = mc_cleanDataEntVars($msg_javascript393) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/backup.php');
include(PATH . 'templates/footer.php');

?>