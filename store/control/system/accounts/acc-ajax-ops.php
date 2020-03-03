<?php

if (!isset($mc_global) || !isset($_GET['acop'])) {
  exit;
}

// Load language files..
include(MCLANG . 'accounts.php');

// Mail..
include(PATH . 'control/classes/mailer/global-mail-tags.php');

// Download class..
include(PATH . 'control/classes/class.download.php');
$MCDL = new mcDownload();

$arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_global[3]));

if (defined('PARENT')) {
  switch($_GET['acop']) {

    //==============================
    // WISH LIST
    //==============================

    case 'wish':
    case 'wishtext':
    case 'wishzone':
      switch($_GET['acop']) {
        case 'wishtext':
          if (isset($loggedInUser['id']) && $SETTINGS->en_wish == 'yes' && isset($_POST['wtxt'])) {
            $MCACC->wishTxt($loggedInUser['id']);
            $arr = array('OK');
          }
          break;
        case 'wishzone':
          if (isset($loggedInUser['id'])) {
            $zones = $MCACC->wishZones($loggedInUser['id'], $public_accounts_profile);
            $arr = array(
              'html' => $zones
            );
          }
          break;
        case 'wish':
          $ID = (isset($_GET['id']) ? (int) $_GET['id'] : '0');
          if ($ID > 0) {
            $PRD = mc_getTableData('products', 'id', $ID,' AND `pEnable` = \'yes\'');
            $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_product[16]));
            if (isset($loggedInUser['id']) && isset($PRD->id)) {
              $EX = mc_getTableData('accounts_wish', 'product', $PRD->id,' AND `account` = \'' . $loggedInUser['id'] . '\'');
              $url  = $MCRWR->url(array('wishlist'));
              if (!isset($EX->id)) {
                $MCACC->wish($loggedInUser['id'], $PRD->id);
                $arr = array('msg' => 'ok', 'html' => '', 'text' => array($mc_wish[1], str_replace('{url}', $url, $mc_wish[4])));
              } else {
                $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], str_replace('{url}', $url, $mc_wish[3])));
              }
            } else {
              $url  = $MCRWR->url(array('create'));
              $url2 = $MCRWR->url(array('account'));
              if ($SETTINGS->en_create == 'yes') {
                $lnk = str_replace(array('{url}','{url2}'),array($url,$url2),$mc_wish[0]);
              } else {
                $lnk = str_replace('{url}', $url2, $mc_wish[0]);
              }
              $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $lnk));
            }
          }
          break;
      }
      break;


    //==============================
    // SAVE / DELETE SEARCH
    //==============================

    case 'del-saved-search':
    case 'search':
      switch($_GET['acop']) {
        case 'del-saved-search':
          $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_product[16]));
          $ID = (isset($_GET['id']) ? (int) $_GET['id'] : '0');
          if ($ID > 0 && isset($loggedInUser['id'])) {
            $SS = mc_getTableData('accounts_search', 'id', $ID,' AND `account` = \'' . $loggedInUser['id'] . '\'');
            if (isset($SS->id)) {
              $MCACC->searchdel($SS->id);
              $arr = array('msg' => 'ok', 'html' => mc_nothingToShow($public_accounts_saved[5]), 'text' => array($public_accounts_validation[8], $public_accounts_saved[7]));
            }
          }
          break;
        case 'search':
          if (isset($_POST['name'])) {
            $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_product[16]));
            if (isset($loggedInUser['id']) && $_POST['name']) {
              $svc = (isset($_SESSION['store_saveSearchKey_' . mc_encrypt(SECRET_KEY)]) ? $_SESSION['store_saveSearchKey_' . mc_encrypt(SECRET_KEY)] : '');
              if ($svc) {
                if (mc_validateSearchKey($svc)) {
                  $AS = mc_getTableData('accounts_search', 'account', $loggedInUser['id'],' AND `code` = \'' . mc_safeSQL($svc) . '\'');
                  if (isset($AS->id)) {
                    $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], str_replace('{url}',$MCRWR->url(array($MCRWR->config['slugs']['ssc'])),$mc_search[7])));
                  } else {
                    $MCACC->savesearch($svc, $loggedInUser['id']);
                    $arr = array('msg' => 'ok', 'html' => '', 'text' => array($mc_search[5], $mc_search[6] . ($SETTINGS->savedSearches > 0 ? str_replace(array('{days}','{url}'),array($SETTINGS->savedSearches,$MCRWR->url(array($MCRWR->config['slugs']['ssc']))),$mc_search[8]) : '')));
                  }
                }
              }
            } else {
              $url = $MCRWR->url(array('create'));
              $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], ($SETTINGS->en_create == 'yes' ? str_replace('{url}',$url,$mc_search[4]) : $mc_search[10])));
            }
          }
          break;
      }
      break;

    //==============================
    // DELETE WISH LIST ENTRIES
    //==============================

    case 'del-wish-list':
      $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_product[16]));
      $ID = (isset($_GET['id']) ? (int) $_GET['id'] : '0');
      if ($ID > 0 && isset($loggedInUser['id'])) {
        $WL = mc_getTableData('accounts_wish', 'id', $ID,' AND `account` = \'' . $loggedInUser['id'] . '\'');
        if (isset($WL->id)) {
          $MCACC->wishdel($WL->id);
          $arr = array('msg' => 'ok', 'html' => mc_nothingToShow($public_accounts_wish[6]), 'text' => array($public_accounts_validation[8], $public_accounts_wish[11]));
        }
      }
      break;

    //==============================
    // RESET PASSWORD
    //==============================

    case 'passreset':
      $form  = array(
        'blank' => (!isset($_POST['np']['blank']) || $_POST['np']['blank'] ? 'spam' : 'not-spam'),
        'pass' => (isset($_POST['np'][1]) ? $_POST['np'][1] : ''),
        'pass2' => (isset($_POST['np'][2]) ? $_POST['np'][2] : ''),
        'token' => (isset($_POST['np']['token']) && ctype_alnum($_POST['np']['token']) ? $_POST['np']['token'] : 'x')
      );
      // Check spam field..
      $u = mc_getTableData('accounts', 'system1', mc_safeSQL($form['token']), ' AND `verified` = \'yes\'');
      if ($form['blank'] == 'not-spam' || $form['token'] == '' || !isset($u->id)) {
        if ($form['pass'] == '') {
          $formErrors[] = $public_accounts_validation[3];
        } else {
          if ($form['pass'] != $form['pass2']) {
            $formErrors[] = $public_accounts_validation[4];
          } else {
            if ($SETTINGS->minPassValue > 0 && strlen($form['pass']) < $SETTINGS->minPassValue) {
              $formErrors[] = $public_accounts_validation[6];
            } else {
              if ($SETTINGS->forcePass == 'yes' && !$MCACC->checkPass($form['pass'])) {
                $formErrors[] = $public_accounts_validation[5];
              }
            }
          }
        }
        if (empty($formErrors)) {
          $code  = $MCACC->newpass($u->id, $form);
          $sbj   = str_replace('{website}', $SETTINGS->website, $msg_emails33);
          $msg   = MCLANG . 'email-templates/accounts/account-pass-reset-confirmation.txt';
          $MCMAIL->addTag('{NAME}', $u->name);
          $MCMAIL->sendMail(array(
            'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
            'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
            'to_email' => $u->email,
            'to_name' => $u->name,
            'subject' => $sbj,
            'replyto' => array(
              'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
              'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
            ),
            'template' => $msg,
            'language' => (isset($u->language) ? $u->language : $SETTINGS->languagePref)
          ));
          $MCMAIL->smtpClose();
          $arr = array('msg' => 'ok', 'html' => '', 'text' => array($public_accounts_validation[8], str_replace('{url}',$MCRWR->url(array('account')),$public_accounts_forgot[7])));
        } else {
          $arr = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
        }
      }
      break;

    //==============================
    // PASSWORD RESET INITIALISE
    //==============================

    case 'newpass':
      $form  = array(
        'blank' => (!isset($_POST['np']['blank']) || $_POST['np']['blank'] ? 'spam' : 'not-spam'),
        'email' => (isset($_POST['np']['e']) && mswIsValidEmail($_POST['np']['e']) ? $_POST['np']['e'] : '')
      );
      // Check spam field..
      if ($form['blank'] == 'not-spam') {
        if ($form['email'] == '') {
          $formErrors[] = $public_accounts_validation[14];
        } else {
          $u = mc_getTableData('accounts', 'email', mc_safeSQL($form['email']), ' AND `verified` = \'yes\'');
          if (!isset($u->id)) {
            $formErrors[] = $public_accounts_validation[11];
          } else {
            $code  = $MCACC->reset($u->id);
            $sbj   = str_replace('{website}', $SETTINGS->website, $msg_emails33);
            $msg   = MCLANG . 'email-templates/accounts/account-pass-reset.txt';
            $MCMAIL->addTag('{CODE}', $code);
            $MCMAIL->addTag('{NAME}', $u->name);
            $MCMAIL->sendMail(array(
              'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
              'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
              'to_email' => $u->email,
              'to_name' => $u->name,
              'subject' => $sbj,
              'replyto' => array(
                'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
                'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
              ),
              'template' => $msg,
              'language' => (isset($u->language) ? $u->language : $SETTINGS->languagePref)
            ));
            $MCMAIL->smtpClose();
            $arr = array('msg' => 'ok', 'html' => '', 'text' => array($public_accounts_validation[8], str_replace('{email}',mc_safeHTML($form['email']),$public_accounts_validation[15])));
          }
        }
        if (!empty($formErrors)) {
          $arr = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
        }
      }
      break;

    //=========================
    // PROFILE UPDATE
    //=========================

    case 'profile':
      if (isset($loggedInUser['id'])) {
        $form  = array(
          'blank' => (!isset($_POST['acc']['blank']) || $_POST['acc']['blank'] ? 'spam' : 'not-spam'),
          'name' => (isset($_POST['acc']['name']) ? $_POST['acc']['name'] : ''),
          'email' => (isset($_POST['acc']['em']) && mswIsValidEmail($_POST['acc']['em']) ? $_POST['acc']['em'] : ''),
          'news' => (isset($_POST['acc']['news']) ? 'yes' : 'no'),
          'bill' => array(
            'nm' => (isset($_POST['bill']['nm']) ? $_POST['bill']['nm'] : ''),
            'em' => (isset($_POST['bill']['em']) && mswIsValidEmail($_POST['bill']['em']) ? $_POST['bill']['em'] : ''),
            '1' => (isset($_POST['bill']['country']) ? (int) $_POST['bill']['country'] : '0'),
            '2' => (isset($_POST['bill'][1]) ? $_POST['bill'][1] : ''),
            '3' => (isset($_POST['bill'][2]) ? $_POST['bill'][2] : ''),
            '4' => (isset($_POST['bill'][3]) ? $_POST['bill'][3] : ''),
            '5' => (isset($_POST['bill'][4]) ? $_POST['bill'][4] : ''),
            '6' => (isset($_POST['bill'][5]) ? $_POST['bill'][5] : '')
          ),
          'ship' => array(
            'nm' => (isset($_POST['ship']['nm']) ? $_POST['ship']['nm'] : ''),
            'em' => (isset($_POST['ship']['em']) && mswIsValidEmail($_POST['ship']['em']) ? $_POST['ship']['em'] : ''),
            'zone' => (isset($_POST['ship']['zone']) ? (int) $_POST['ship']['zone'] : '0'),
            '1' => (isset($_POST['ship']['country']) ? (int) $_POST['ship']['country'] : '0'),
            '2' => (isset($_POST['ship'][1]) ? $_POST['ship'][1] : ''),
            '3' => (isset($_POST['ship'][2]) ? $_POST['ship'][2] : ''),
            '4' => (isset($_POST['ship'][3]) ? $_POST['ship'][3] : ''),
            '5' => (isset($_POST['ship'][4]) ? $_POST['ship'][4] : ''),
            '6' => (isset($_POST['ship'][5]) ? $_POST['ship'][5] : ''),
            '7' => (isset($_POST['ship'][6]) ? $_POST['ship'][6] : '')
          ),
          'wishtext' => (isset($_POST['acc']['wish']) ? $_POST['acc']['wish'] : ''),
          'old' => (isset($_POST['acc']['old']) ? $_POST['acc']['old'] : ''),
          'pass' => (isset($_POST['acc']['pass']) ? $_POST['acc']['pass'] : ''),
          'pass2' => (isset($_POST['acc']['pass2']) ? $_POST['acc']['pass2'] : '')
        );
        // Check spam field..
        if ($form['blank'] == 'not-spam') {
          // Error check..
          if ($form['name'] == '' || $form['email'] == '') {
            $formErrors[] = $public_accounts_validation[0];
          } else {
            if (strtolower($form['email']) != strtolower($loggedInUser['email'])) {
              $usr = $MCACC->user(array(
                'email' => $form['email']
              ));
              if (isset($usr['id'])) {
                $formErrors[] = $public_accounts_validation[7];
                $arr          = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
                echo $MCJSON->encode($arr);
                exit;
              }
            }
          }
          foreach ($form['bill'] AS $k => $v) {
            switch($k) {
              case 'country':
                if ($v == '0') {
                  $formErrors[] = $public_accounts_validation[1];
                  break 2;
                }
                break;
              case 'em':
                if ($v == '') {
                  $formErrors[] = $public_accounts_validation[16];
                  break 2;
                }
                break;
              default:
                if ($k != '3' && $v == '') {
                  $formErrors[] = $public_accounts_validation[1];
                  break 2;
                }
                break;
            }
          }
          foreach ($form['ship'] AS $k => $v) {
            switch($k) {
              case 'country':
                if ($v == '0') {
                  $formErrors[] = $public_accounts_validation[2];
                  break 2;
                }
                break;
              case 'em':
                if ($v == '') {
                  $formErrors[] = $public_accounts_validation[17];
                  break 2;
                }
                break;
              default:
                if ($k != '3' && $v == '') {
                  $formErrors[] = $public_accounts_validation[2];
                  break 2;
                }
                if ($SETTINGS->en_wish == 'yes' && $k == 'zone' && $v == '0') {
                  $formErrors[] = $public_accounts_validation[18];
                  break 2;
                }
                break;
            }
          }
          if ($form['old']) {
            if (!mc_PassHash(array('type' => 'calc', 'val' => $form['old'], 'hash' => $loggedInUser['pass']))) {
              $formErrors[] = $public_accounts_validation[12];
            } else {
              if ($form['pass'] == '' || ($form['pass'] != $form['pass2'])) {
                $formErrors[] = $public_accounts_validation[4];
              } else {
                if ($SETTINGS->minPassValue > 0 && strlen($form['pass']) < $SETTINGS->minPassValue) {
                  $formErrors[] = $public_accounts_validation[6];
                } else {
                  if ($SETTINGS->forcePass == 'yes' && !$MCACC->checkPass($form['pass'])) {
                    $formErrors[] = $public_accounts_validation[5];
                  }
                }
              }
            }
          }
          if (empty($formErrors)) {
            $MCACC->update($form, $loggedInUser);
            $_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))] = $form['email'];
            $arr = array('msg' => 'ok', 'html' => '', 'text' => array($public_accounts_validation[8], $public_accounts_validation[13]));
          } else {
            $arr = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
          }
        }
      }
      break;

    //=========================
    // LOGIN
    //=========================

    case 'login':
      $form  = array(
        'blank' => (!isset($_POST['lg']['blank']) || $_POST['lg']['blank'] ? 'spam' : 'not-spam'),
        'email' => (isset($_POST['lg']['e']) && mswIsValidEmail($_POST['lg']['e']) ? $_POST['lg']['e'] : ''),
        'pass' => (isset($_POST['lg']['p']) ? $_POST['lg']['p'] : 'x-x')
      );
      // Check spam field..
      if ($form['blank'] == 'not-spam') {
        if ($form['email'] == '' || $form['pass'] == '') {
          $formErrors[] = $public_accounts_validation[10];
        } else {
          $usr = $MCACC->login($form);
          if (!isset($usr->id)) {
            $formErrors[] = $public_accounts_validation[11];
          } else {
            if ($usr->enablelog == 'yes') {
              $MCACC->log($usr);
            }
            $_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))] = $form['email'];
            if ($usr->currency && $usr->currency != $SETTINGS->baseCurrency && in_array($usr->currency, array_keys($currencyConversion)) &&
                mc_rowCount('currencies WHERE LOWER(`currency`) = \'' . strtolower($usr->currency) . '\' AND `enableCur` = \'yes\'') > 0) {
              $_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency'] = $usr->currency;
            }
            if ($usr->language && $usr->language != $SETTINGS->languagePref && is_dir(PATH . 'content/language/' . $usr->language)) {
              $_SESSION[mc_encrypt(SECRET_KEY) . '_mc_language'] = $usr->language;
            }
            $_SESSION['mc_acc_type_' . mc_encrypt(mc_encrypt(SECRET_KEY))] = $usr->type;
            $arr = array(
              'msg' => 'ok',
              'url' => (isset($_SESSION['login-redirect']) ? $_SESSION['login-redirect'] : $MCRWR->url(array('account')))
            );
            if (isset($_SESSION['login-redirect'])) {
              unset($_SESSION['login-redirect']);
            }
            if ($SETTINGS->enableRecentView == 'yes' && $usr->recent) {
              if (!isset($_SESSION['recentlyViewedItems'])) {
                $_SESSION['recentlyViewedItems'] = array();
              }
              $recent = unserialize(mc_cleanData($usr->recent));
              if (is_array($recent)) {
                if (empty($_SESSION['recentlyViewedItems'])) {
                  $_SESSION['recentlyViewedItems'] = $recent;
                } else {
                  $_SESSION['recentlyViewedItems'] = $_SESSION['recentlyViewedItems'] + $recent;
                }
              }
            }
            echo $MCJSON->encode($arr);
            exit;
          }
        }
        if (!empty($formErrors)) {
          $arr = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
        }
      }
      break;

    //=========================
    // ACCOUNT CREATION
    //=========================

    case 'create':
      $form  = array(
        'blank' => (!isset($_POST['acc']['blank']) || $_POST['acc']['blank'] ? 'spam' : 'not-spam'),
        'name' => (isset($_POST['acc']['name']) ? $_POST['acc']['name'] : ''),
        'email' => (isset($_POST['acc']['em']) && mswIsValidEmail($_POST['acc']['em']) ? $_POST['acc']['em'] : ''),
        'news' => (isset($_POST['acc']['news']) ? 'yes' : 'no'),
        'bill' => array(
          'nm' => (isset($_POST['bill']['nm']) ? $_POST['bill']['nm'] : ''),
          'em' => (isset($_POST['bill']['em']) && mswIsValidEmail($_POST['bill']['em']) ? $_POST['bill']['em'] : ''),
          '1' => (isset($_POST['bill']['country']) ? (int) $_POST['bill']['country'] : '0'),
          '2' => (isset($_POST['bill'][1]) ? $_POST['bill'][1] : ''),
          '3' => (isset($_POST['bill'][2]) ? $_POST['bill'][2] : ''),
          '4' => (isset($_POST['bill'][3]) ? $_POST['bill'][3] : ''),
          '5' => (isset($_POST['bill'][4]) ? $_POST['bill'][4] : ''),
          '6' => (isset($_POST['bill'][5]) ? $_POST['bill'][5] : '')
        ),
        'ship' => array(
          'nm' => (isset($_POST['ship']['nm']) ? $_POST['ship']['nm'] : ''),
          'em' => (isset($_POST['ship']['em']) && mswIsValidEmail($_POST['ship']['em']) ? $_POST['ship']['em'] : ''),
          '1' => (isset($_POST['ship']['country']) ? (int) $_POST['ship']['country'] : '0'),
          '2' => (isset($_POST['ship'][1]) ? $_POST['ship'][1] : ''),
          '3' => (isset($_POST['ship'][2]) ? $_POST['ship'][2] : ''),
          '4' => (isset($_POST['ship'][3]) ? $_POST['ship'][3] : ''),
          '5' => (isset($_POST['ship'][4]) ? $_POST['ship'][4] : ''),
          '6' => (isset($_POST['ship'][5]) ? $_POST['ship'][5] : ''),
          '7' => (isset($_POST['ship'][6]) ? $_POST['ship'][6] : '')
        ),
        'pass' => (isset($_POST['acc']['pass']) ? $_POST['acc']['pass'] : ''),
        'pass2' => (isset($_POST['acc']['pass2']) ? $_POST['acc']['pass2'] : '')
      );
      // Check spam field..
      if ($form['blank'] == 'not-spam') {
        // Error check..
        if ($form['name'] == '' || $form['email'] == '') {
          $formErrors[] = $public_accounts_validation[0];
        } else {
          // Is cleanTalk anti spam enabled?
          $cTalk = $MCSOCIAL->params('ctalk');
          if (isset($cTalk['ctalk']['enable']) && $cTalk['ctalk']['enable'] == 'yes' && $cTalk['ctalk']['key']) {
            include(PATH . 'control/classes/class.cleantalk.php');
            $TALK_API            = new cleanTalk();
            $TALK_API->settings  = $SETTINGS;
            $TALK_API->social    = $cTalk;
            $tk_user = $TALK_API->check(array(
              'email' => $form['email'],
              'name' => $form['name']
            ));
            // Check for block..
            if (!isset($tk_user['allow']) || $tk_user['allow'] == 0) {
              // Send email?
              if ($cTalk['ctalk']['mail'] == 'yes') {
                $sbj   = str_replace('{website}', $SETTINGS->website, $msg_emails22);
                $msg   = MCLANG . 'email-templates/cleantalk-block.txt';
                $MCMAIL->addTag('{NAME}', $form['name']);
                $MCMAIL->addTag('{EMAIL}', $form['email']);
                $MCMAIL->addTag('{COMMENTS}', 'N/A');
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
              $formErrors[] = $public_accounts_validation[19];
              $arr          = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
              echo $MCJSON->encode($arr);
              exit;
            }
          }
          $usr = $MCACC->user(array(
            'email' => $form['email']
          ));
          if (isset($usr['id'])) {
            $formErrors[] = $public_accounts_validation[7];
            $arr          = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
            echo $MCJSON->encode($arr);
            exit;
          }
        }
        foreach ($form['bill'] AS $k => $v) {
          switch($k) {
            case 'country':
              if ($v == '0') {
                $formErrors[] = $public_accounts_validation[1];
                break 2;
              }
              break;
            case 'em':
              if ($v == '') {
                $formErrors[] = $public_accounts_validation[16];
                break 2;
              }
              break;
            default:
              if ($k != '3' && $v == '') {
                $formErrors[] = $public_accounts_validation[1];
                break 2;
              }
              break;
          }
        }
        foreach ($form['ship'] AS $k => $v) {
          switch($k) {
            case 'country':
              if ($v == '0') {
                $formErrors[] = $public_accounts_validation[2];
                break 2;
              }
              break;
            case 'em':
              if ($v == '') {
                $formErrors[] = $public_accounts_validation[17];
                break 2;
              }
              break;
            default:
              if ($k != '3' && $v == '') {
                $formErrors[] = $public_accounts_validation[2];
                break 2;
              }
              break;
          }
        }
        if ($form['pass'] == '') {
          $formErrors[] = $public_accounts_validation[3];
        } else {
          if ($form['pass'] != $form['pass2']) {
            $formErrors[] = $public_accounts_validation[4];
          } else {
            if ($SETTINGS->minPassValue > 0 && strlen($form['pass']) < $SETTINGS->minPassValue) {
              $formErrors[] = $public_accounts_validation[6];
            } else {
              if ($SETTINGS->forcePass == 'yes' && !$MCACC->checkPass($form['pass'])) {
                $formErrors[] = $public_accounts_validation[5];
              }
            }
          }
        }
        if (empty($formErrors)) {
          // Create..
          $code = $MCACC->create($form);
          // Update any sales for this email so they are visible..
          $up = $MCACC->saleAcc($form['email'], $code[1]);
          if ($code[1] > 0) {
            $sbj  = str_replace('{website}', $SETTINGS->website, $msg_emails30);
            $msg  = MCLANG . 'email-templates/accounts/account-verification.txt';
            $MCMAIL->addTag('{CODE}', $code[0]);
            $MCMAIL->addTag('{NAME}', $form['name']);
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
              'language' => $SETTINGS->languagePref
            ));
            $MCMAIL->smtpClose();
            $arr = array('msg' => 'ok', 'html' => '', 'text' => array($public_accounts_validation[8], str_replace('{email}',mc_safeHTML($form['email']),$public_accounts_validation[9])));
          }
        } else {
          $arr = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
        }
      }
      break;

    //=========================
    // FILE DOWNLOAD
    //=========================

    case 'dl':
    case 'dl-token':
      include(MCLANG . 'view-order.php');
      include(PATH . 'control/classes/class.block.php');
      $MCBLK           = new mcBlock();
      $MCBLK->settings = $SETTINGS;
      switch($_GET['acop']) {
        case 'dl':
          if (!isset($_SESSION['dl-log-cntr'])) {
            $_SESSION['dl-log-cntr'] = time();
            $_SESSION['dl-log-cntr-ip'] = mc_getRealIPAddr();
          }
          $ID = (isset($_GET['id']) ? (int) $_GET['id'] : '0');
          if (isset($loggedInUser['id']) && $ID > 0) {
            $MCDL->log('Starting sale download for account: ' . $loggedInUser['id'] . ' (' . $loggedInUser['name'] . ')');
            // Check permissions..
            $PUR = mc_getTableData('purchases', 'id', $ID, ' AND `saleConfirmation` = \'yes\'');
            if (isset($PUR->saleID)) {
              $MCDL->log('Valid purchase found for purchase ID: ' . $ID);
              // Has download expired?
              if ($PUR->liveDownload == 'no') {
                $MCDL->log('Download has expired, showing message to buyer');
                $formErrors[] = $msg_public_view10;
                $arr          = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
                echo $MCJSON->encode($arr);
                exit;
              }
              $ORDER = mc_getTableData('sales', 'id', $PUR->saleID, ' AND `account` = \'' . $loggedInUser['id'] . '\' AND `saleConfirmation` = \'yes\'');
              if (!isset($ORDER->id) && $SETTINGS->en_wish == 'yes') {
                $MCDL->log('Wish list download identified for purchase saleID: ' . $PUR->saleID);
                $ORDER = mc_getTableData('sales', 'id', $PUR->saleID, ' AND `wishlist` = \'' . $loggedInUser['id'] . '\' AND `saleConfirmation` = \'yes\'');
              }
              if (isset($ORDER->id)) {
                $MCDL->log('Found valid sale for purchase saleID: ' . $PUR->saleID);
                if ($ORDER->downloadLock == 'no') {
                  if (in_array($ORDER->paymentMethod, array(
                    'cheque',
                    'phone',
                    'cod',
                    'bank'
                    )) && in_array($ORDER->paymentStatus, array('pending','cancelled','refunded'))) {
                    $MCDL->log('Download terminated as access denied. Sale payment via cheque, phone, cod or bank and payment status either pending, cancelled or refunded. Showing message to buyer.');
                    $formErrors[] = $msg_public_view26;
                    $arr          = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
                    echo $MCJSON->encode($arr);
                    exit;
                  } else {
                    // Check for IP restriction..
                    if ($SETTINGS->downloadRestrictIP == 'yes' && $ORDER->ipAccess) {
                      $MCDL->log('IP restrictions in affect, starting checks..');
                      $isNoGo = 'no';
                      // Get restricted IP addresses..
                      $allowed  = array_map('trim', explode(',', $ORDER->ipAccess));
                      // Whats allowed for this user..
                      $user_ips = mc_getRealIPAddr(true);
                      // Are there any global IPs?
                      if ($SETTINGS->downloadRestrictIPGlobal) {
                        $globalIP = array_map('trim', explode(',', $SETTINGS->downloadRestrictIPGlobal));
                        $allowed  = array_merge($allowed, $globalIP);
                      }
                      $ac_chk = 0;
                      if (!empty($user_ips)) {
                        foreach ($user_ips AS $aIP) {
                          if (in_array($aIP, $allowed)) {
                            ++$ac_chk;
                          }
                        }
                        // If no IPs are allowed, we block access..
                        if ($ac_chk == 0) {
                          $isNoGo = 'yes';
                        }
                      } else {
                        $isNoGo = 'yes';
                      }
                      // Is a block in place?
                      if ($isNoGo == 'yes') {
                        $MCDL->log('Block is enabled for IP: ' .print_r($user_ips, true));
                        // Log event if enabled..
                        if ($SETTINGS->downloadRestrictIPLog == 'yes') {
                          $MCBLK->log($ORDER, $allowed, $user_ips);
                          $MCDL->log('Logging restriction to log file as this is enabled in settings');
                        }
                        // Update restriction count..
                        $next = 0;
                        if ($SETTINGS->downloadRestrictIPLock > 0) {
                          $next = ($ORDER->restrictCount + 1);
                          $MCBLK->increment($ORDER->id);
                          // Should we lock download page?
                          if ($next == $SETTINGS->downloadRestrictIPLock) {
                            $MCDL->log('Download access locked for all downloads');
                            $MCBLK->lock($ORDER->id);
                            // Send email if enabled..
                            if ($SETTINGS->downloadRestrictIPMail == 'yes') {
                              $MCDL->log('Email being sent to store owner as email notification is enabled');
                              $sbj  = str_replace(array(
                                '{website}',
                                '{invoice}'
                              ), array(
                                mc_cleanData($SETTINGS->website),
                                mc_saleInvoiceNumber($ORDER->invoiceNo, $SETTINGS)
                              ), $msg_public_view28);
                              $msg  = MCLANG . 'email-templates/ip-restriction-notification.txt';
                              $MCMAIL->addTag('{BUYER}', $ORDER->bill_1);
                              $MCMAIL->addTag('{BLOCKS}', $SETTINGS->downloadRestrictIPLock);
                              $MCMAIL->addTag('{EMAIL}', $ORDER->bill_2);
                              $MCMAIL->addTag('{INVOICE}', mc_saleInvoiceNumber($ORDER->invoiceNo, $SETTINGS));
                              $MCMAIL->addTag('{ALLOW_IP}', implode(', ', $allowed));
                              if ($SETTINGS->downloadRestrictIPLog == 'yes') {
                                $MCMAIL->addTag('{LOG}', $SETTINGS->ifolder . '/' . $SETTINGS->logFolderName . '/restricted-ip-log-S' . mc_saleInvoiceNumber($ORDER->invoiceNo, $SETTINGS) . '.txt');
                              } else {
                                $MCMAIL->addTag('{LOG}', 'N/A');
                              }
                              $MCMAIL->addTag('{BLOCK_IP}', implode(', ', $user_ips));
                              $MCMAIL->addTag('{ORDER_ID}', $ORDER->id);
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
                              $MCMAIL->smtpClose();
                            }
                            $MCDL->log('Showing message to buyer');
                            $formErrors[] = $public_accounts_validation[7];
                            $arr          = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
                            echo $MCJSON->encode($arr);
                            exit;
                          }
                        }
                      }
                    }
                  }
                  // All good so far..
                  $PRD = mc_getTableData('products', 'id', $PUR->productID, ' AND `pDownload` = \'yes\' AND `pDownloadPath` != \'\'');
                  if (isset($PRD->id)) {
                    $MCDL->log('Product found for purchase ID: ' . $ID . ', product identified as: ' . $PRD->pName);
                    // Check path..
                    if (substr($PRD->pDownloadPath, 0, 7) != 'http://'
                        && substr($PRD->pDownloadPath, 0, 8) != 'https://'
                        && substr($PRD->pDownloadPath, 0, 6) != 'ftp://'
                        && substr($PRD->pDownloadPath, 0, 7) != 'sftp://') {
                      if (!file_exists($SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder . '/' . $PRD->pDownloadPath)) {
                        $MCDL->log('File "' . $SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder . '/' . $PRD->pDownloadPath . '" does not exist, showing message to buyer. Download terminated.');
                        $formErrors[] = $msg_view_order[0];
                        $arr          = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
                        echo $MCJSON->encode($arr);
                        exit;
                      }
                    }
                    // Increment download count..
                    $status = $MCCART->incrementProductDownload($PUR, $PRD);
                    // Add click log..
                    $MCCART->addClickHistory($PUR->saleID, $PUR->id, $PUR->productID);
                    if ($status == 'ok') {
                      $token = $MCCART->addDownloadToken($PUR->id);
                      $MCDL->log('Download OK, creating token for download. Token is: ' . $token);
                      $arr = array(
                        'msg' => 'ok',
                        'rdr' => 'index.php?acop=dl-token&tk=' . $token
                      );
                    } else {
                      $MCDL->log('Download has expired, showing message to buyer');
                      $formErrors[] = $msg_public_view10;
                      $arr          = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
                      echo $MCJSON->encode($arr);
                      exit;
                    }
                  }
                } else {
                  $MCDL->log('Download access is locked, showing message to buyer');
                  $formErrors[] = $msg_public_view20;
                  $arr          = array('msg' => 'err', 'html' => implode('<br>', $formErrors), 'text' => array($mc_global[2], $mc_global[3]));
                  echo $MCJSON->encode($arr);
                  exit;
                }
              } else {
                $MCDL->log('Order information not found for ID: ' . $ID . ', download terminated');
              }
            } else {
              $MCDL->log('Purchase information not found for ID: ' . $ID . ', download terminated');
            }
          } else {
            $MCDL->log('User isn`t logged in, permission denied and download terminated');
          }
          break;
        case 'dl-token':
          if (isset($_GET['tk']) && ctype_alnum($_GET['tk'])) {
            $MCDL->log('Token initialised for download. Token ' . $_GET['tk'] . ' identified as valid');
            $PUR = mc_getTableData('purchases', 'downloadCode', mc_safeSQL($_GET['tk']));
            $PRD = mc_getTableData('products', 'id', $PUR->productID, ' AND `pDownload` = \'yes\' AND `pDownloadPath` != \'\'');
            if (isset($PUR->id) && isset($PRD->id)) {
              $MCDL->log('Purchase and product info confirmed, product is: ' . $PRD->pName);
              $MCCART->resetDownloadToken($PUR->id);
              if (substr($PRD->pDownloadPath, 0, 7) == 'http://' || substr($PRD->pDownloadPath, 0, 8) == 'https://') {
                $MCDL->log('Triggering http/https download link: '. $PRD->pDownloadPath);
                $MCDL->log('Downloading/viewing file and completing operation.');
                if (isset($_SESSION['dl-log-cntr'])) {
                  unset($_SESSION['dl-log-cntr'],$_SESSION['dl-log-cntr-ip']);
                }
                header("Location: " . $PRD->pDownloadPath);
              } else {
                $MCDL->log('Determining mime and file info for system download..');
                $path = $MCPROD->determineDownloadPath($PRD->pDownloadPath);
                $mime = $MCDL->mime($path, '');
                $MCDL->log('Path is: ' . $path . ', mime is: ' . $mime);
                $mime = $MCDL->mime($path, '');
                $MCDL->log('Downloading file and completing operation.');
                if (isset($_SESSION['dl-log-cntr'])) {
                  unset($_SESSION['dl-log-cntr'],$_SESSION['dl-log-cntr-ip']);
                }
                $MCDL->dl($path, $mime);
              }
              exit;
            }
          }
          $MCDL->log('Download error, showing error page to buyer. Possibly invalid token.');
          if (isset($_SESSION['dl-log-cntr'])) {
            unset($_SESSION['dl-log-cntr']);
          }
          header("Location: " . $MCRWR->url(array('dl-code-error')));
          break;
      }
      break;

    //=========================
    // CLOSE
    //=========================

    case 'close':
      if ($SETTINGS->en_close == 'yes' && isset($loggedInUser['id'])) {
        $MCACC->closeAccount($loggedInUser);
        // Send email to webmaster..
        $sbj  = str_replace('{website}', $SETTINGS->website, $msg_emails39);
        $msg  = MCLANG . 'email-templates/accounts/account-closed.txt';
        $MCMAIL->addTag('{NAME}', $loggedInUser['name']);
        $MCMAIL->addTag('{EMAIL}', $loggedInUser['email']);
        $MCMAIL->sendMail(array(
          'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
          'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'to_email' => $SETTINGS->email,
          'to_name' => $SETTINGS->website,
          'subject' => $sbj,
          'replyto' => array(
            'name' => $loggedInUser['name'],
            'email' => $loggedInUser['email']
          ),
          'template' => $msg,
          'alive' => 'yes',
          'add-emails' => $SETTINGS->addEmails,
          'language' => $SETTINGS->languagePref
        ));
        // Send email to user..
        $sbj  = str_replace('{website}', $SETTINGS->website, $msg_emails39);
        $msg  = MCLANG . 'email-templates/accounts/account-closed-confirmation.txt';
        $MCMAIL->addTag('{NAME}', $loggedInUser['name']);
        $MCMAIL->addTag('{EMAIL}', $loggedInUser['email']);
        $MCMAIL->sendMail(array(
          'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
          'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'to_email' => $loggedInUser['email'],
          'to_name' => $loggedInUser['name'],
          'subject' => $sbj,
          'replyto' => array(
            'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
            'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
          ),
          'template' => $msg,
          'language' => $SETTINGS->languagePref
        ));
        $MCMAIL->smtpClose();
        $arr = array(
          'msg' => 'ok',
          'rdr' => $MCRWR->url(array('acc-closed'))
        );
      }
      break;

    //=========================
    // PDF INVOICE
    //=========================

    case 'pdf':
      if ($SETTINGS->pdf == 'yes' && isset($loggedInUser['id']) && isset($_GET['id'])) {
        $SALE = mc_getTableData('sales', 'id', (int) $_GET['id'], 'AND `account` = \'' . $loggedInUser['id'] . '\' AND `saleConfirmation` = \'yes\'');
        // If no sale is found, is this a wish list sale?
        if (!isset($SALE->id) && $SETTINGS->en_wish == 'yes') {
          $SALE = mc_getTableData('sales', 'id', (int) $_GET['id'], 'AND `wishlist` = \'' . $loggedInUser['id'] . '\' AND `saleConfirmation` = \'yes\'');
        }
        if (isset($SALE->id)) {
          $arr = array(
            'msg' => 'ok',
            'rdr' => 'index.php?pdf=' . (int) $_GET['id']
          );
        }
      }
      break;
  }
}

echo $MCJSON->encode($arr);

?>