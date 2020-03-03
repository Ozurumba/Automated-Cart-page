<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

if (isset($_SESSION[mc_encrypt('store_menu_panel' . SECRET_KEY)])) {
  unset($_SESSION[mc_encrypt('store_menu_panel' . SECRET_KEY)]);
}

// Mobile Menu
if (isset($_GET['mobileMenu'])) {
  include(MCLANG . 'accounts.php');
  $tpl = mc_getSavant();
  $tpl->assign('TEXT', $public_accounts);
  $tpl->assign('URL', array(
    $MCRWR->url(array('account')),
    $MCRWR->url(array('profile')),
    $MCRWR->url(array('history')),
    $MCRWR->url(array($MCRWR->config['slugs']['wst'])),
    $MCRWR->url(array($MCRWR->config['slugs']['ssc'])),
    $MCRWR->url(array('create')),
    $MCRWR->url(array('logout')),
    $MCRWR->url(array('close'))
  ));

  // Global..
  include(PATH . 'control/system/global.php');

  $tpl->display(THEME_FOLDER . '/mobile-menu.tpl.php');
  exit;
}

// Sort and order filters..
if (isset($_GET['mc_sys_filters']) && isset($_GET['t'])) {
  // Clr = clear filter
  if ($_GET['mc_sys_filters'] == 'clr') {
    if (isset($_SESSION['mc_' . $_GET['t'] . '_filters_' . mc_encrypt(SECRET_KEY)])) {
      unset($_SESSION['mc_' . $_GET['t'] . '_filters_' . mc_encrypt(SECRET_KEY)]);
    }
    // Clear advanced search if enabled..
    if (!empty($_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)]['search'])) {
      $_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)]['search'] = array();
      unset($_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)]['search']);
    }
    if (isset($_SESSION['reset-next-links'])) {
      unset($_SESSION['reset-next-links']);
    }
  } else {
    switch($_GET['t']) {
      case 'brands':
        $_SESSION['mc_' . $_GET['t'] . '_filters_' . mc_encrypt(SECRET_KEY)] = $_GET['mc_sys_filters'];
        break;
      case 'cat':
        $_SESSION['mc_' . $_GET['t'] . '_filters_' . mc_encrypt(SECRET_KEY)] = (int) $_GET['mc_sys_filters'];
        break;
      case 'points':
        // Clear advanced search if enabled..
        if (!empty($_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)]['search'])) {
          $_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)]['search'] = array();
          unset($_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)]['search']);
        }
        $PP = mc_getTableData('price_points', 'id', (int) $_GET['mc_sys_filters']);
        if (isset($PP->id)) {
          $_SESSION['mc_' . $_GET['t'] . '_filters_' . mc_encrypt(SECRET_KEY)] = array($PP->priceFrom,$PP->priceTo,$PP->id);
        }
        break;
    }
    // Set var to tell system to always load first page of filter..
    $_SESSION['reset-next-links'] = 'yes';
  }
  echo $MCJSON->encode(array('OK'));
  exit;
}

if (isset($_GET['mc_sys_sort']) && preg_match('/^[a-z0-9\-]+$/i', $_GET['mc_sys_sort'])) {
  $_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)] = $_GET['mc_sys_sort'];
  // Set var to tell system to always load first page of filter..
  $_SESSION['reset-next-links'] = 'yes';
  echo $MCJSON->encode(array('OK'));
  exit;
}

// Layout change / save..
if (isset($_GET['mc_sys_layout']) && in_array($_GET['mc_sys_layout'], array('list','grid'))) {
  // If user is logged in, save preference..
  if (isset($loggedInUser['id'])) {
    $MCACC->params(
      $loggedInUser['id'],
      array(
        'key' => 'layout',
        'val' => $_GET['mc_sys_layout']
      ),
      $loggedInUser['params']
    );
  }
  $_SESSION['mc_layout_' . mc_encrypt(SECRET_KEY)] = $_GET['mc_sys_layout'];
  echo $MCJSON->encode(array('OK'));
  exit;
}

// Load language files..
include(MCLANG . 'home.php');
include(MCLANG . 'category.php');
include(MCLANG . 'product.php');
define('SLIDER_HOME', 1);

// Store message
// From form in template footer.tpl.php file
if (isset($_GET['store-msg'])) {
  $arr   = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_global[3]));
  $form  = array(
    'blank' => (!isset($_POST['msg']['blank']) || $_POST['msg']['blank'] ? 'spam' : 'not-spam'),
    'email' => (isset($_POST['msg']['em']) && mswIsValidEmail($_POST['msg']['em']) ? $_POST['msg']['em'] : ''),
    'name' => (isset($_POST['msg']['nm']) ? $_POST['msg']['nm'] : ''),
    'msg' => (isset($_POST['msg']['msg']) ? $_POST['msg']['msg'] : '')
  );
  if ($form['blank'] == 'not-spam') {
    if ($form['email'] == '' || $form['name'] == '' || $form['msg'] == '') {
      $ferr = $msg_storeform[0];
      $arr  = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $ferr));
    } else {
      include(PATH . 'control/classes/mailer/global-mail-tags.php');
      // Is cleanTalk anti spam enabled?
      $cTalk = $MCSOCIAL->params('ctalk');
      if (isset($cTalk['ctalk']['enable']) && $cTalk['ctalk']['enable'] == 'yes' && $cTalk['ctalk']['key']) {
        include(PATH . 'control/classes/class.cleantalk.php');
        $TALK_API            = new cleanTalk();
        $TALK_API->settings  = $SETTINGS;
        $TALK_API->social    = $cTalk;
        $tk_comms = $TALK_API->check(array(
          'method' => 'check_message',
          'email' => $form['email'],
          'name' => $form['name'],
          'comms' => $form['msg']
        ));
        // Check for block..
        if (!isset($tk_comms['allow']) || $tk_comms['allow'] == 0) {
          // Send email?
          if ($cTalk['ctalk']['mail'] == 'yes') {
            // To store owner..
            $sbj   = str_replace('{website}', $SETTINGS->website, $msg_emails47);
            $msg   = MCLANG . 'email-templates/cleantalk-block.txt';
            $MCMAIL->addTag('{NAME}', $form['name']);
            $MCMAIL->addTag('{EMAIL}', $form['email']);
            $MCMAIL->addTag('{COMMENTS}', $form['msg']);
            $MCMAIL->sendMail(array(
              'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
              'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
              'to_email' => $SETTINGS->email,
              'to_name' => $SETTINGS->website,
              'subject' => $sbj,
              'replyto' => array(
                'name' => $form['name'],
                'email' => $form['email']
              ),
              'template' => $msg,
              'add-emails' => $SETTINGS->addEmails,
              'language' => $SETTINGS->languagePref
            ));
            $MCMAIL->smtpClose();
          }
          $ferr = $msg_storeform[3];
          $arr  = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $ferr));
          echo $MCJSON->encode($arr);
          exit;
        }
      }
      // To store owner..
      $sbj   = str_replace('{website}', $SETTINGS->website, $msg_emails34);
      $msg   = MCLANG . 'email-templates/store-message.txt';
      $MCMAIL->addTag('{NAME}', $form['name']);
      $MCMAIL->addTag('{EMAIL}', $form['email']);
      $MCMAIL->addTag('{MESSAGE}', $form['msg']);
      $MCMAIL->sendMail(array(
        'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
        'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
        'to_email' => $SETTINGS->email,
        'to_name' => $SETTINGS->website,
        'subject' => $sbj,
        'replyto' => array(
          'name' => $form['name'],
          'email' => $form['email']
        ),
        'template' => $msg,
        'add-emails' => $SETTINGS->addEmails,
        'alive' => 'yes',
        'language' => $SETTINGS->languagePref
      ));
      if (CONTACT_AUTO_RESPONDER) {
        // To visitor..
        $sbj   = str_replace('{website}', $SETTINGS->website, $msg_emails35);
        $msg   = MCLANG . 'email-templates/store-message-confirmation.txt';
        $MCMAIL->addTag('{NAME}', $form['name']);
        $MCMAIL->addTag('{EMAIL}', $form['email']);
        $MCMAIL->addTag('{MESSAGE}', $form['msg']);
        $MCMAIL->sendMail(array(
          'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
          'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'to_email' => $form['email'],
          'to_name' => $form['name'],
          'subject' => $sbj,
          'replyto' => array(
            'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
            'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
          ),
          'template' => $msg,
          'language' => (isset($loggedInUser['language']) ? $loggedInUser['language'] : $SETTINGS->languagePref)
        ));
      }
      $MCMAIL->smtpClose();
      $arr   = array('msg' => 'ok', 'html' => '', 'text' => array($msg_storeform[1], $msg_storeform[2]));
    }
  }
  echo $MCJSON->encode($arr);
  exit;
}

// Set new currency..
if (isset($_GET['cg_cur']) && ctype_alpha($_GET['cg_cur'])) {
  $MCCACHE->clear_cache_file('head-currencies');
  $setCur = substr($_GET['cg_cur'], 0, 3);
  // Remove legacy cookies..
  if (isset($_SERVER['HTTP_HOST']) && isset($_COOKIE[mc_encrypt(SECRET_KEY . $_SERVER['HTTP_HOST']) . '_mc_currency'])) {
    setcookie(mc_encrypt(SECRET_KEY . $_SERVER['HTTP_HOST']) . '_mc_currency', '');
    unset($_COOKIE[mc_encrypt(SECRET_KEY . $_SERVER['HTTP_HOST']) . '_mc_currency']);
  }
  // Check currency..
  if (mc_rowCount('currencies WHERE LOWER(`currency`) = \'' . mc_safeSQL(strtolower($setCur)) . '\' AND `enableCur` = \'yes\'') > 0) {
    $_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency'] = $setCur;
    // If user is logged in, update account..
    if (isset($loggedInUser['id'])) {
      $MCACC->updateOp(array('op' => 'cur','val' => $setCur, 'acc' => $loggedInUser));
    }
  }
  // Attempt to direct to current page. Check if referer is set and if its valid to domain..
  // If not available, redirect to homepage as default..
  if (isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] &&
      strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
  } else {
    header("Location: " . $MCRWR->url(array('base_href')));
  }
  exit;
}

// Set new language..
if (isset($_GET['cg_lang']) && is_dir(PATH . 'content/language/' . $_GET['cg_lang'])) {
  $MCCACHE->clear_cache_file('head-languages');
  $_SESSION[mc_encrypt(SECRET_KEY) . '_mc_language'] = $_GET['cg_lang'];
  // If user is logged in, update account..
  if (isset($loggedInUser['id'])) {
    $MCACC->updateOp(array('op' => 'lang','val' => $setCur, 'acc' => $loggedInUser));
  }
  // Attempt to direct to current page. Check if referer is set and if its valid to domain..
  // If not available, redirect to homepage as default..
  if (isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] &&
      strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
  } else {
    header("Location: " . $MCRWR->url(array('base_href')));
  }
  exit;
}

// Check for alternative landing page..
$qLP = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "newpages`
       WHERE `landingPage` = 'yes'
       " . (defined('MC_TRADE_DISCOUNT') ? 'AND `trade` IN(\'yes\')' : 'AND `trade` IN(\'no\')') . "
       LIMIT 1
       ") or die(mc_MySQLError(__LINE__, __FILE__));
$L_PAGE = mysqli_fetch_object($qLP);
if (isset($L_PAGE->id)) {
  $url = $MCRWR->url(array(
    $MCRWR->config['slugs']['npg'] . '/' . $L_PAGE->id . '/' . ($L_PAGE->rwslug ? $L_PAGE->rwslug : $MCRWR->title($L_PAGE->pageName)),
    'np=' . $L_PAGE->id
  ));
  header("Location: " . $url);
  exit;
}

// Load javascript..
$loadJS['swipe']  = 'load';

// If at least 1 mp3 exists, load sound manager..
if (mc_rowCount('mp3') > 0) {
  $loadJS['soundmanager'] = 'load';
}

// Set session var for CleanTalk..
$_SESSION[mc_encrypt(SECRET_KEY) . '_stime'] = time();

// Product count..
$pCount = $MCPROD->productList('home', array('count' => 'yes'));

// Left menu boxes..
include(PATH . 'control/left-box-controller.php');

include(PATH . 'control/header.php');

$tpl = mc_getSavant();
$tpl->assign('PRODUCTS', $MCPROD->productList('home'));
$tpl->assign('CATEGORIES', ($SETTINGS->parentCatHomeDisplay == 'yes' ? $MCSYS->loadHomepageCategories() : ''));
$tpl->assign('TXT', array(
  ($SETTINGS->homeProdIDs ? $public_home3 : ($SETTINGS->homeProdType == 'latest' ? str_replace('{count}', $SETTINGS->homeProdValue, $public_home) : $public_home2))
));
$tpl->assign('BLOG', $MCSYS->blog($msg_blog));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/home.tpl.php');

include(PATH . 'control/footer.php');

?>