<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'accounts/mail-accounts.php');

// Send..
if (isset($_POST['subject'])) {
  include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
  $s   = 0;
  $SQL = ($_POST['type'] != 'all' ? 'AND `type` IN(\'' . mc_safeSQL($_POST['type'])  . '\')' : '');
  if ($_POST['subject'] && $_POST['msg']) {
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `name`,`email`,`language` FROM `" . DB_PREFIX . "accounts`
         WHERE `enabled` = 'yes'
         $SQL
         GROUP BY `email`
         ORDER BY `name`
         ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($A = mysqli_fetch_object($q)) {
      ++$s;
      $sbj = str_replace(array('{name}','{email}'),array($A->name,$A->email),$_POST['subject']);
      $msg = str_replace(array('{name}','{email}'),array($A->name,$A->email),$_POST['msg']);
      $MCMAIL->sendMail(array(
        'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
        'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
        'to_email' => $A->email,
        'to_name' => $A->name,
        'subject' => $sbj,
        'replyto' => array(
          'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
        ),
        'template' => $msg,
        'language' => ($A->language ? $A->language : $SETTINGS->languagePref),
        'alive' => 'yes'
      ));
    }
    if ($s > 0) {
      $MCMAIL->smtpClose();
    }
  }
  echo $JSON->encode(array(
    str_replace('{count}', @number_format($s), $msg_mailaccnts6)
  ));
  exit;
}

$pageTitle = mc_cleanDataEntVars($msg_admin3_0[43]) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/accounts/mail-accounts.php');
include(PATH . 'templates/footer.php');

?>
