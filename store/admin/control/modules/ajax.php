<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

if ($cmd == 'ajax-ops') {
  switch($_GET['op']) {
    case 'stateload':
      $billCnty = (int) $_GET['bc'];
      $shipCnty = (int) $_GET['sc'];
      $accID = (int) $_GET['ac'];
      $mc_states = array();
      if ($accID > 0) {
        include(GLOBAL_PATH . 'control/classes/class.accounts.php');
        $MCACC2 = new mcAccounts();
        $addrFields = $MCACC2->getaddresses($accID);
        $billVal = (isset($addrFields[0]['addr5']) ? mc_safeHTML($addrFields[0]['addr5']) : '');
        $shipVal = (isset($addrFields[1]['addr5']) ? mc_safeHTML($addrFields[1]['addr5']) : '');
      }
      // Get arrays.
      $addarr = array(array(),array());
      if (file_exists(GLOBAL_PATH . 'control/states/' . $billCnty . '.php')) {
        include(GLOBAL_PATH . 'control/states/' . $billCnty . '.php');
        $addarr[0] = $mc_states;
      }
      if ($shipCnty != $billCnty) {
        if ($shipCnty > 0 && file_exists(GLOBAL_PATH . 'control/states/' . $shipCnty . '.php')) {
          include(GLOBAL_PATH . 'control/states/' . $shipCnty . '.php');
          $addarr[1] = $mc_states;
        }
      } else {
        $addarr[1] = $mc_states;
      }
      include(GLOBAL_PATH . 'control/classes/class.html.php');
      $MCHTML           = new mcHtml();
      $MCHTML->settings = $SETTINGS;
      $tmp = array(
        'box' => PATH . 'templates/accounts/html/basket-states-input.htm',
        'option' => PATH . 'templates/accounts/html/basket-states-select-option.htm',
        'select' => PATH . 'templates/accounts/html/basket-states-select.htm'
      );
      $arr = array(
        'ship_addr' => $MCHTML->loadStates('ship',(isset($shipVal) ? $shipVal : ''),$addarr[1], $tmp),
        'bill_addr' => $MCHTML->loadStates('bill',(isset($billVal) ? $billVal : ''),$addarr[0], $tmp),
      );
      echo $JSON->encode($arr);
      exit;
      break;
    case 'tweet':
      $arr = array(
        'ERR',
        $msg_admin_settings3_0[43]
      );
      if (mc_tweetPerms($sysCartUser) == 'yes' && $SETTINGS->tweet == 'yes' && isset($_POST['tweet']) && $_POST['tweet']) {
        include(GLOBAL_PATH . 'control/system/api/twitter/codebird.php');
        include(GLOBAL_PATH . 'control/classes/class.social.php');
        $MCSOC = new mcSocial();
        $tweetapi = $MCSOC->params('twitter');
        if (isset($tweetapi['twitter']['conkey'])) {
          $CB  = new Codebird();
          $CB->setConsumerKey($tweetapi['twitter']['conkey'], $tweetapi['twitter']['consecret']);
          $cbi = $CB->getInstance();
          $cbi->setToken($tweetapi['twitter']['token'], $tweetapi['twitter']['key']);
          $params = array(
            'status' => $_POST['tweet']
          );
          $pingreply  = (array) $cbi->statuses_update($params);
          if (isset($pingreply['httpstatus']) && $pingreply['httpstatus'] == '200') {
            $arr[0] = 'OK';
          }
        }
      }
      echo $JSON->encode($arr);
      break;
    case 'elfinder':
      include(PATH . 'control/classes/_elfinder/elFinderConnector.class.php');
      include(PATH . 'control/classes/_elfinder/elFinder.class.php');
      include(PATH . 'control/classes/_elfinder/elFinderVolumeDriver.class.php');
      include(PATH . 'control/classes/_elfinder/elFinderVolumeLocalFileSystem.class.php');
      $opts = array(
        'debug' => false,
        'roots' => array(
          array(
            'driver'        => 'LocalFileSystem',
            'path'          => $SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder . '/',
            'URL'           => $SETTINGS->ifolder . '/' . $SETTINGS->downloadFolder . '/',
            'accessControl' => 'mc_elFinderAccessControl',
            'tmbPath'       => (@ini_get('upload_tmp_dir') ? @ini_get('upload_tmp_dir') : sys_get_temp_dir()),
            'quarantine'    => (@ini_get('upload_tmp_dir') ? @ini_get('upload_tmp_dir') : sys_get_temp_dir())
          )
        )
      );
      $mcElCon = new elFinderConnector(new elFinder($opts));
      $mcElCon->run();
      break;
    case 'catlist':
      include(MCLANG . 'catalogue/product-import.php');
      include(MCLANG . 'catalogue/product-manage.php');
      $html = array();
      $q    = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
              `" . DB_PREFIX . "products`.`id` AS `pid`
              FROM `" . DB_PREFIX . "products`,`" . DB_PREFIX . "prod_category`
              WHERE `category` = '" . mc_digitSan($_GET['cat']) . "'
              AND `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
              AND `pEnable` = 'yes'
              GROUP BY `product`
              ORDER BY `pName`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($P = mysqli_fetch_object($q)) {
        $html[] = '<input type="checkbox" name="product[]" value="' . $P->pid . '" onclick="mc_attCntCheck()"> ' . mc_cleanData($P->pName);
      }
      echo $JSON->encode(array(
        (!empty($html) ? '<input type="checkbox" name="log" value="all" onclick="mc_toggleCheckBoxesID(this.checked,\'prds\');mc_attCntCheck()"> <b>' . $msg_productmanage33 . '</b><br>' . implode('<br>', $html) : $msg_import52),
        (!empty($html) ? 'OK' : 'fail')
      ));
      break;
    case 'auto-name':
      $arr = $MCACC->search('name', $_GET['term']);
      echo $JSON->encode($arr);
      break;
    case 'auto-email':
      $arr = $MCACC->search('email', $_GET['term']);
      echo $JSON->encode($arr);
      break;
    case 'pdf-invoice':
      echo $JSON->encode(array(
        'rdr' => '../index.php?pdf=' . $_GET['pdf'] . '&admin-pdf-loader=yes'
      ));
      break;
    case 'pdf-slip':
      echo $JSON->encode(array(
        'rdr' => '?pdf-slip=' . $_GET['pdf']
      ));
      break;
    case 'pdf-inv-batch':
    case 'pdf-slip-batch':
      $_SESSION['batchPDFIDs'] = (!empty($_POST['batch']) ? $_POST['batch'] : array());
      switch($_GET['op']) {
        case 'pdf-inv-batch':
          $rdr = '../index.php?pdf=batch&admin-pdf-loader=yes';
          break;
        case 'pdf-slip-batch':
          $rdr = '?pdf-slip=batch';
          break;
      }
      echo $JSON->encode(array(
        'rdr' => $rdr
      ));
      break;
    case 'mailtest':
      mc_memoryLimit();
      include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
      $msg = $msg_smtp_settings[5];
      $sbj = str_replace('{website}', $SETTINGS->website, $msg_smtp_settings[6]);
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
      echo $JSON->encode(array('msg' => $msg_smtp_settings[4]));
      break;

    case 'menupanel':
      $_SESSION[mc_encrypt('adm_menu_panel' . SECRET_KEY)] = $_GET['pnl'];
      $arr['status'] = 'ok';
      break;
  }
  exit;
}


?>