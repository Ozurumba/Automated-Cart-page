<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'tools/newsletter.php');

// Newsletter class
include(PATH . 'control/classes/class.newsletter.php');
$MCNSL            = new newsletter();
$MCNSL->settings  = $SETTINGS;

// Save template..
if (isset($_GET['saveTemplate'])) {
  $r = $MCNSL->addNewsTemplate();
  echo $JSON->encode(array(
    ($r == 'OK' ? $msg_newsletter30 : $msg_newsletter31)
  ));
  exit;
}

// Load templates..
if (isset($_GET['loadTemplates'])) {
  $r = $MCNSL->loadNewsTemplates();
  echo $r;
  exit;
}

// Template loader..
if ($cmd == 'newsletter-templates') {
  if (isset($_POST['process'])) {
    $_GET['load'] = $_GET['update'];
    $MCNSL->updateNewsTemplate();
    $OK = true;
  }
  if (isset($_GET['del'])) {
    $MCNSL->deleteNewsTemplate();
    header("Location: ?p=newsletter-templates&deldone=yes");
    exit;
  }
  $pageTitle   = mc_cleanDataEntVars($msg_javascript309) . ' - ' . mc_cleanDataEntVars($msg_newsletter29) . ': ' . $pageTitle;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/tools/mail-templates.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Mailer..
if ($cmd == 'newsletter-mail') {
  if (isset($_GET['search'])) {
    $tmp = $MCNSL->searchTemplates();
    echo $JSON->encode($tmp);
    exit;
  }
  if (isset($_POST['process'])) {
    mc_memoryLimit();
    include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
    $sent      = 0;
    $SQL       = '';
    $attach    = array();
    $fromEmail = ($_POST['email'] ? $_POST['email'] : ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email));
    $fromName  = ($_POST['from'] ? $_POST['from'] : ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website));
    $type      = (isset($_POST['atype']) && in_array($_POST['atype'],array('all','personal','trade')) ? $_POST['atype'] : 'all');
    switch($type) {
      case 'personal':
        $SQL = 'AND `type` = \'personal\'';
        break;
      case 'trade':
        $SQL = 'AND `type` = \'trade\'';
        break;
    }
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `name`,`email` FROM `" . DB_PREFIX . "accounts`
         WHERE `enabled` = 'yes'
         AND `verified` = 'yes'
         AND `newsletter` = 'yes'
         $SQL
         ORDER BY `name`,`email`
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    if (mysqli_num_rows($q) > 0) {
      // Deal with attachments..
      for ($i = 0; $i < count($_FILES['attachment']['tmp_name']); $i++) {
        $name = $_FILES['attachment']['name'][$i];
        $temp = $_FILES['attachment']['tmp_name'][$i];
        if (is_uploaded_file($temp) && $name && is_writeable(PATH . 'import')) {
          move_uploaded_file($temp, PATH . 'import/' . $name);
          if (file_exists(PATH . 'import/' . $name)) {
            $attach[PATH . 'import/' . $name] = $name;
          }
        }
      }
      // Loop and send..
      while ($NSL = mysqli_fetch_object($q)) {
        ++$sent;
        // Messages..
        $html   = str_replace(array('{name}','{unsubscribe}'), array($NSL->name, $SETTINGS->ifolder . '/?optOut=' . $NSL->email), $_POST['html']);
        $plain  = str_replace(array('{name}','{unsubscribe}'), array($NSL->name, $SETTINGS->ifolder . '/?optOut=' . $NSL->email), $_POST['plain']);
        if (!empty($attach)) {
          $MCMAIL->attachments = $attach;
        }
        $MCMAIL->sendMail(array(
          'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
          'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'to_email' => $NSL->email,
          'to_name' => $NSL->name,
          'subject' => str_replace('{name}',$NSL->name,$_POST['subject']),
          'replyto' => array(
            'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
            'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
          ),
          'template' => array(
            'html' => $html,
            'plain' => $plain
          ),
          'alive' => 'yes',
          'language' => $SETTINGS->languagePref
        ));
      }
      if ($sent > 0) {
        $MCMAIL->smtpClose();
      }
      // Clear attachments..
      if (!empty($attach)) {
        foreach (array_keys($attach) AS $f) {
          if (file_exists($f)) {
            @unlink($f);
          }
        }
      }
    }
    // Update news template..
    if (isset($_POST['updateTemp'])) {
      $MCNSL->updateNewsTemplate();
    }
    $OK = true;
  }

  $pageTitle   = mc_cleanDataEntVars($msg_javascript309) . ' - ' . mc_cleanDataEntVars($msg_newsletter11) . ': ' . $pageTitle;
  $loadiBox = true;

  include(PATH . 'templates/header.php');
  include(PATH . 'templates/tools/newsletter-mailer.php');
  include(PATH . 'templates/footer.php');
  exit;
}

if (isset($_GET['reset'])) {
  $MCNSL->resetNewsletter();
  echo $JSON->encode(array(
    'OK'
  ));
  exit;
}

if (isset($_GET['export'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL        = new mcDownload();
  $MCNSL->dl = $DL;
  $MCNSL->exportNewsletter();
}

$pageTitle = mc_cleanDataEntVars($msg_javascript309) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/newsletter.php');
include(PATH . 'templates/footer.php');

?>