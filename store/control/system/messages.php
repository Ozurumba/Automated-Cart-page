<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Load language files..
include(MCLANG . 'view-order.php');
include(MCLANG . 'product.php');
include(MCLANG . 'home.php');
include(MCLANG . 'accounts.php');

switch($cmd) {
  // Account verification check..
  case 'acc-verification':
    if (ctype_alnum($_GET['ve'])) {
      $A = mc_getTableData('accounts', 'system1', mc_safeSQL($_GET['ve']), ' AND `verified` = \'no\'');
      if (isset($A->id)) {
        // Mail..
        include(PATH . 'control/classes/mailer/global-mail-tags.php');
        $ret = $MCACC->activate($A->id);
        if ($ret == 'ok') {
          // Send email to visitor..
          $sbj  = str_replace('{website}', $SETTINGS->website, $msg_emails31);
          $msg  = MCLANG . 'email-templates/accounts/account-created.txt';
          $MCMAIL->addTag('{NAME}', $A->name);
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
            'language' => ($A->language ? $A->language : $SETTINGS->languagePref)
          ));
          // If enabled, send to webmaster..
          if ($SETTINGS->en_create_mail == 'yes') {
            $sbj  = str_replace('{website}', $SETTINGS->website, $msg_emails32);
            $msg  = MCLANG . 'email-templates/accounts/wm-account-activated.txt';
            $MCMAIL->addTag('{NAME}', $A->name);
            $MCMAIL->addTag('{EMAIL}', $A->email);
            $MCMAIL->addTag('{IP}', $A->ip);
            $MCMAIL->sendMail(array(
              'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
              'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
              'to_email' => $SETTINGS->email,
              'to_name' => $SETTINGS->website,
              'subject' => $sbj,
              'replyto' => array(
                'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
                'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
              ),
              'template' => $msg,
              'add-emails' => $SETTINGS->addEmails,
              'language' => $SETTINGS->languagePref
            ));
          }
          $MCMAIL->smtpClose();
          $msg = array(
            $public_accounts_messages[0],
            str_replace('{url}',$MCRWR->url(array('account')),$public_accounts_messages[1])
          );
          $_SESSION['ss_mail_' . mc_encrypt(SECRET_KEY)] = $A->email;
        }
      } else {
        $msg = array(
          $public_accounts_messages[2],
          str_replace('{url}',$MCRWR->url(array('account')),$public_accounts_messages[3])
        );
      }
    } else {
      $msg = array(
          $public_accounts_messages[2],
          str_replace('{url}',$MCRWR->url(array('account')),$public_accounts_messages[3])
        );
    }
    break;
  // Download code is invalid..
  case 'dl-code-error':
    $msg = array(
      $msg_public_view3,
      str_replace('{website}', mc_cleanData($SETTINGS->website), $msg_public_view9)
    );
    break;
  // Sale code is invalid..
  case 'code-error':
    $msg = array(
      $msg_public_view3,
      str_replace('{website}', mc_cleanData($SETTINGS->website), $msg_public_view4)
    );
    break;
  // Order Page Invalid..
  case 'order-invalid':
    $msg = array(
      $msg_public_view23,
      str_replace('{website}', mc_cleanData($SETTINGS->website), $msg_public_view24)
    );
    break;
  // Invalid order status..
  case 'status-err':
    $msg = array(
      $msg_public_view25,
      str_replace('{website}', mc_cleanData($SETTINGS->website), $msg_public_view26)
    );
    break;
  // Cart error..
  case 'cart-error':
    $msg = array(
      $mc_checkout[14],
      str_replace('{website}', mc_cleanData($SETTINGS->website), $msg_public_view18)
    );
    break;
  // Product is out of stock..
  case 'out-of-stock':
    $msg = array(
      $public_product9,
      str_replace('{website}', mc_cleanData($SETTINGS->website), $public_product41)
    );
    break;
  // Opt out completed..
  case 'opt-out':
    $msg = array(
      $public_home5,
      str_replace('{website}', mc_cleanData($SETTINGS->website), $public_home4)
    );
    break;
  // Account exists..
  case 'acc-exists':
    $msg = array(
      $mc_sysmessage[1],
      str_replace(array('{website}','{url}'), array(mc_cleanData($SETTINGS->website),$MCRWR->url(array('account'))), $mc_sysmessage[2])
    );
    break;
  // Account closed..
  case 'acc-closed':
    $msg = array(
      $mc_sysmessage[3],
      str_replace('{website}', mc_cleanData($SETTINGS->website), $mc_sysmessage[4])
    );
    break;
  // No Search..
  case 'no-search':
    $msg = array(
      $mc_sysmessage[5],
      str_replace(array('{website}','{url}'), array(mc_cleanData($SETTINGS->website),$MCRWR->url(array('advanced-search'))), $mc_sysmessage[6])
    );
    break;
  // Gatewway ok..
  case 'gate1':
    if (isset($_SESSION['mc_checkrdr_' . mc_encrypt(mc_encrypt(SECRET_KEY))])) {
      header("Location: " . $_SESSION['mc_checkrdr_' . mc_encrypt(mc_encrypt(SECRET_KEY))]);
      exit;
    }
    $msg = array(
      $public_checkout21,
      str_replace(array('{website}'), array(mc_cleanData($SETTINGS->website)), $mc_checkout[26])
    );
    break;
  // Gateway declined..
  case 'gate2':
    // Stripe error code..
    if (isset($_GET['errStr'])) {
      include(PATH . 'control/gateways/methods/class.stripe.php');
      $ST = new stripe();
      $_GET['errorMessage'] = $ST->decline_reasons($_GET['errStr']);
    }
    $msg = array(
      $public_checkout122,
      str_replace(array('{website}','{reason}'), array(mc_cleanData($SETTINGS->website),(isset($_GET['errorMessage']) ? urldecode($_GET['errorMessage']) : 'N/A')), $public_checkout123)
    );
    break;
  // No category assigned to product..
  case 'no-category-assigned':
    $msg = array(
      $mc_sysmessage[7],
      str_replace(array('{website}'), array(mc_cleanData($SETTINGS->website)), $mc_sysmessage[8])
    );
    break;
  // No country set for wish list receiver..
  case 'no-wish-country':
    $msg = array(
      $mc_sysmessage[7],
      str_replace(array('{website}'), array(mc_cleanData($SETTINGS->website)), $mc_sysmessage[9])
    );
    break;
  // Rubbish, reject with error..
  default:
    include(PATH . 'control/system/headers/403.php');
    exit;
    break;
}

$headerTitleText = $msg[0] . ': ' . $headerTitleText;

$breadcrumbs = array(
  $mc_sysmessage[0],
  mc_safeHTML($msg[0])
);

// Left menu boxes..
$skipMenuBoxes['brands']  = true;
$skipMenuBoxes['points']  = true;
include(PATH . 'control/left-box-controller.php');

include(PATH . 'control/header.php');

$tpl = mc_getSavant();
$tpl->assign('TITLE', mc_safeHTML($msg[0]));
$tpl->assign('TEXTM', array(
  $msg[0],
  $msg[1],
  $public_checkout116,
  $mc_sysmessage
));
$tpl->assign('TEXT', $public_accounts);
$tpl->assign('URL', array(
  $MCRWR->url(array('account')),
  $MCRWR->url(array('profile')),
  $MCRWR->url(array('history')),
  $MCRWR->url(array('wishlist')),
  $MCRWR->url(array('saved-searches')),
  $MCRWR->url(array('create')),
  $MCRWR->url(array('logout'))
));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/message.tpl.php');

include(PATH . 'control/footer.php');

?>