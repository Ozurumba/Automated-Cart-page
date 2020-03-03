<?php

/*
  DATABASE BACKUP CRON
  See docs/tools-11.html
-----------------------------*/

date_default_timezone_set('UTC');

// SET PATH TO CART FOLDER..
define('PARENT', 1);
define('PATH', substr(dirname(__file__), 0, strpos(dirname(__file__), 'control')-1) . '/');

// ERROR REPORTING..
include(PATH . 'control/classes/class.errors.php');
if (ERR_HANDLER_ENABLED) {
  register_shutdown_function('mcFatalErr');
  set_error_handler('mcErrorhandler');
}

// DATABASE CONNECTION..
include(PATH . 'control/connect.php');
include(PATH . 'control/functions.php');

// TIMEOUT..
mc_memAllocation();

// Connect..
mc_dbConnector();

include(PATH . 'control/defined.php');
include(PATH . 'control/system/constants.php');

mc_fileController();

include(PATH . 'control/system/core/sys-controller.php');
include(PATH . 'control/classes/mailer/class.send.php');

// LOAD SETTINGS DATA..
$SETTINGS = @mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"));

// CHECK INSTALLER..
if (isset($SETTINGS->languagePref) && is_writeable(PATH . 'logs') || is_dir(PATH . 'logs')) {

  // DEFINE LANGUAGE PATH..
  define('MCLANG', mc_loadLangFile($SETTINGS));
  include(MCLANG . 'global.php');

  $time     = date('H:i:s');
  $filepath = PATH . 'logs/store-backup-' . date(mc_backupDateFormat($SETTINGS, true)) . '-' . str_replace(':', '-', $time) . '.gz';

  // BACKUP CLASS..
  include(PATH . 'control/classes/class.db-backup.php');
  $BACKUP           = new dbBackup($filepath, true);
  $BACKUP->settings = $SETTINGS;

  // DO BACKUP..
  $BACKUP->doDump();

  // SEND EMAILS IF ENABLED..
  if ($SETTINGS->smtp == 'yes' && BACKUP_CRON_EMAILS && file_exists($filepath)) {
    include(PATH . 'control/classes/class.parser.php');
    $MCMAIL            = new mcMail();
    $MCPARSER          = new mcDataParser();
    $MCMAIL->parser    = $MCPARSER;
    include(PATH . 'control/classes/mailer/global-mail-tags.php');
    $emails = array();
    $ot = array();
    if (strpos(BACKUP_CRON_EMAILS, ',') !== FALSE) {
      $emails = array_map('trim', explode(',', BACKUP_CRON_EMAILS));
    } else {
      $emails[] = BACKUP_CRON_EMAILS;
    }
    if (count($emails) > 1) {
      $ot = $emails;
      unset($ot[0]);
    }
    $sbj = str_replace(array(
      '{website}',
      '{date}',
      '{time}'
    ), array(
      mc_cleanData($SETTINGS->website),
      date(mc_backupDateFormat($SETTINGS)),
      $time
    ), $msg_script67);
    $msg = str_replace(array(
      '{STORE}',
      '{DATE_TIME}',
      '{VERSION}',
      '{FILE}'
    ), array(
      mc_cleanData($SETTINGS->website),
      date(mc_backupDateFormat($SETTINGS)) . ' @ ' . $time,
      SCRIPT_VERSION,
      basename($filepath)
    ), mc_loadTemplateFile(MCLANG . 'email-templates/cron-backup.txt'));
    // Attach file..
    $MCMAIL->attachments[$filepath] = basename($filepath);
    // Send..
    $MCMAIL->sendMail(array(
      'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
      'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
      'to_email' => $emails[0],
      'to_name' => $emails[0],
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
    @unlink($filepath);
  }

  if (BACKUP_CRON_OUTPUT) {
    echo '[' . date('j F Y @ H:iA') . '] Backup Completed' . PHP_EOL . str_repeat('-=', 50) . PHP_EOL;
  }

} else {

  if (BACKUP_CRON_OUTPUT) {
    echo 'Database backup failed: Settings not detected, backup aborted @ ' . date('j F Y H:iA');
  }

}

?>