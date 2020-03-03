<?php

if (!defined('GUEST_OPS') || !isset($ORDER->id)) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Load action class..
include(PATH . 'control/classes/class.block.php');
$MCBLK           = new mcBlock();
$MCBLK->settings = $SETTINGS;

// Mail..
include(PATH . 'control/classes/mailer/global-mail-tags.php');

// Download class..
include(PATH . 'control/classes/class.download.php');
$MCDL = new mcDownload();

$arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_global[3]));

// Download ops..
if (!isset($_GET['token'])) {
  if (!isset($_SESSION['dl-log-cntr'])) {
    $_SESSION['dl-log-cntr']    = 'guest-' . time();
    $_SESSION['dl-log-cntr-ip'] = mc_getRealIPAddr();
  }
  $ID = (isset($_GET['pdl']) ? (int) $_GET['pdl'] : '0');
  if ($ID > 0) {
    $MCDL->log('Starting sale download for guest checkout: ' . $ORDER->id . ' (' . $ORDER->bill_1 . ')');
    // Check permissions..
    $PUR = mc_getTableData('purchases', 'id', $ID, ' AND `saleConfirmation` = \'yes\'');
    if (isset($PUR->saleID)) {
      $MCDL->log('Valid purchase found for purchase ID: ' . $ID);
      // Has download expired?
      if ($PUR->liveDownload == 'no') {
        $MCDL->log('Download has expired, showing message to buyer');
        $formErrors[] = $msg_public_view10;
        $arr          = array(
          'msg' => 'err',
          'html' => implode('<br>', $formErrors),
          'text' => array(
            $mc_global[2],
            $mc_global[3]
          )
        );
        echo $MCJSON->encode($arr);
        exit;
      }
      $ORDER = mc_getTableData('sales', 'id', $PUR->saleID, ' AND `saleConfirmation` = \'yes\'');
      if (isset($ORDER->id)) {
        $MCDL->log('Found valid sale for purchase saleID: ' . $PUR->saleID);
        if ($ORDER->downloadLock == 'no') {
          if (in_array($ORDER->paymentMethod, array(
            'cheque',
            'phone',
            'cod',
            'bank'
          )) && in_array($ORDER->paymentStatus, array(
            'pending',
            'cancelled',
            'refunded'
          ))) {
            $MCDL->log('Download terminated as access denied. Sale payment via cheque, phone, cod or bank and payment status either pending, cancelled or refunded. Showing message to buyer.');
            $formErrors[] = $msg_public_view26;
            $arr          = array(
              'msg' => 'err',
              'html' => implode('<br>', $formErrors),
              'text' => array(
                $mc_global[2],
                $mc_global[3]
              )
            );
            echo $MCJSON->encode($arr);
            exit;
          } else {
            // Check for IP restriction..
            if ($SETTINGS->downloadRestrictIP == 'yes' && $ORDER->ipAccess) {
              $MCDL->log('IP restrictions in affect, starting checks..');
              $isNoGo   = 'no';
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
                $MCDL->log('Block is enabled for IP: ' . print_r($user_ips, true));
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
                      $sbj = str_replace(array(
                        '{website}',
                        '{invoice}'
                      ), array(
                        mc_cleanData($SETTINGS->website),
                        mc_saleInvoiceNumber($ORDER->invoiceNo, $SETTINGS)
                      ), $msg_public_view28);
                      $msg = MCLANG . 'email-templates/ip-restriction-notification.txt';
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
                        'add-emails' => $SETTINGS->addEmails,
                        'template' => $msg,
                        'language' => $SETTINGS->languagePref
                      ));
                      $MCMAIL->smtpClose();
                    }
                    $MCDL->log('Showing message to buyer');
                    $formErrors[] = $public_accounts_validation[7];
                    $arr          = array(
                      'msg' => 'err',
                      'html' => implode('<br>', $formErrors),
                      'text' => array(
                        $mc_global[2],
                        $mc_global[3]
                      )
                    );
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
            if (substr($PRD->pDownloadPath, 0, 7) != 'http://' && substr($PRD->pDownloadPath, 0, 8) != 'https://' && substr($PRD->pDownloadPath, 0, 6) != 'ftp://' && substr($PRD->pDownloadPath, 0, 7) != 'sftp://') {
              if (!file_exists($SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder . '/' . $PRD->pDownloadPath)) {
                $MCDL->log('File "' . $SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder . '/' . $PRD->pDownloadPath . '" does not exist, showing message to buyer. Download terminated.');
                $formErrors[] = $msg_view_order[0];
                $arr          = array(
                  'msg' => 'err',
                  'html' => implode('<br>', $formErrors),
                  'text' => array(
                    $mc_global[2],
                    $mc_global[3]
                  )
                );
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
                'rdr' => 'index.php?vOrder=' . $ORDER->id . '-' . $ORDER->buyCode . '&pdl=' . $PUR->id . '&token=' . $token
              );
            } else {
              $MCDL->log('Download has expired, showing message to buyer');
              $formErrors[] = $msg_public_view10;
              $arr          = array(
                'msg' => 'err',
                'html' => implode('<br>', $formErrors),
                'text' => array(
                  $mc_global[2],
                  $mc_global[3]
                )
              );
              echo $MCJSON->encode($arr);
              exit;
            }
          }
        } else {
          $MCDL->log('Download access is locked, showing message to buyer');
          $formErrors[] = $msg_public_view20;
          $arr          = array(
            'msg' => 'err',
            'html' => implode('<br>', $formErrors),
            'text' => array(
              $mc_global[2],
              $mc_global[3]
            )
          );
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
    $MCDL->log('ID ' . $ID . ' doesn`t appear to be valid');
  }
} else {
  if (isset($_GET['token']) && ctype_alnum($_GET['token'])) {
    $MCDL->log('Token initialised for download. Token ' . $_GET['token'] . ' identified as valid');
    $PUR = mc_getTableData('purchases', 'downloadCode', mc_safeSQL($_GET['token']));
    $PRD = mc_getTableData('products', 'id', $PUR->productID, ' AND `pDownload` = \'yes\' AND `pDownloadPath` != \'\'');
    if (isset($PUR->id) && isset($PRD->id)) {
      $MCDL->log('Purchase and product info confirmed, product is: ' . $PRD->pName);
      $MCCART->resetDownloadToken($PUR->id);
      if (substr($PRD->pDownloadPath, 0, 7) == 'http://' || substr($PRD->pDownloadPath, 0, 8) == 'https://') {
        $MCDL->log('Triggering http/https download link: ' . $PRD->pDownloadPath);
        $MCDL->log('Downloading file and completing operation.');
        if (isset($_SESSION['dl-log-cntr'])) {
          unset($_SESSION['dl-log-cntr'], $_SESSION['dl-log-cntr-ip']);
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
          unset($_SESSION['dl-log-cntr'], $_SESSION['dl-log-cntr-ip']);
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
  header("Location: " .$MCRWR->url(array('dl-code-error')));
}

echo $MCJSON->encode($arr);

?>