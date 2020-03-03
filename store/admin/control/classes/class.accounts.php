<?php

class accounts {

  public $settings;
  public $dl;

  public function newCode($id) {
    $code = substr(mc_encrypt(uniqid(rand(),1)), 3, 15);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `system1`  = '{$code}'
	  WHERE `id` = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return $code;
  }

  public function batchImportAccountsFromCSV() {
    $count = 0;
    if (file_exists(PATH . 'import/' . $_POST['file'])) {
      // Other vars..
      $newsletter    = (isset($_POST['newsletter']) && in_array($_POST['newsletter'], array('yes','no')) ? $_POST['newsletter'] : 'no');
      $enablelog     = (isset($_POST['enablelog']) && in_array($_POST['enablelog'], array('yes','no')) ? $_POST['enablelog'] : 'no');
      $trade = array(
        'type' => (isset($_POST['type']) && in_array($_POST['type'], array('personal','trade')) ? $_POST['type'] : 'personal'),
        'discount' => (isset($_POST['tradediscount']) ? (int) $_POST['tradediscount'] : '0'),
        'min' => (isset($_POST['minqty']) ? (int) $_POST['minqty'] : ''),
        'max' => (isset($_POST['maxqty']) ? (int) $_POST['maxqty'] : ''),
        'stock' => (isset($_POST['stocklevel']) ? (int) $_POST['stocklevel'] : ''),
        'mincheckout' => (isset($_POST['mincheckout']) ? $_POST['mincheckout'] : '')
      );
      $status        = (isset($_POST['status']) && in_array($_POST['status'], array('unverified','active')) ? $_POST['status'] : 'active');
      $enabled       = ($status == 'active' ? 'yes' : 'no');
      $verified      = ($status == 'active' ? 'yes' : 'no');
      $slot          = 0;
      // Read csv..
      $handle        = fopen(PATH . 'import/' . $_POST['file'], "r");
      if ($handle) {
        while (($CSV = fgetcsv($handle, $_SESSION['mc_importPref']['lines'], $_SESSION['mc_importPref']['del'], $_SESSION['mc_importPref']['enc'])) !== FALSE) {
          ++$slot;
          if ($slot > 1) {
            // Clean array..
            $CSV      = array_map('trim', $CSV);
            $flip     = array_flip($_SESSION['mc_fieldMapping']);
            // Check incoming data..
            $i_name   = accounts::readImportAccValue('name', $flip, $CSV);
            $i_email  = accounts::readImportAccValue('email', $flip, $CSV);
            $i_pass   = accounts::readImportAccValue('pass', $flip, $CSV);
            if ($i_pass == '') {
              $i_pa = mc_encrypt(SECRET_KEY . substr(md5(uniqid(rand(),1)), 3, 15));
              $i_pass = mc_PassHash(array('type' => 'add', 'pass' => $i_pa));
            } else {
              $i_pass = mc_PassHash(array('type' => 'add', 'pass' => $i_pass));
            }
            // Check email is unique..
            if ($i_name && $i_email && mswIsValidEmail($i_email) && mc_rowCount('accounts',' WHERE `email` = \'' . $i_email . '\'') == 0) {
              mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "accounts` (
              `name`,
              `created`,
              `email`,
              `pass`,
              `enabled`,
              `verified`,
              `enablelog`,
              `newsletter`,
              `type`,
              `tradediscount`,
              `minqty`,
              `maxqty`,
              `stocklevel`,
              `mincheckout`
              ) VALUES (
              '" . mc_safeSQL($i_name) . "',
              '" . date("Y-m-d") . "',
              '" . mc_safeSQL($i_email) . "',
              '{$i_pass}',
              '{$enabled}',
              '{$verified}',
              '{$enablelog}',
              '{$newsletter}',
              '{$trade['type']}',
              '{$trade['discount']}',
              '{$trade['min']}',
              '{$trade['max']}',
              '{$trade['stock']}',
              '{$trade['mincheckout']}'
              )") or die(mc_MySQLError(__LINE__, __FILE__));
              $ID = mysqli_insert_id($GLOBALS["___msw_sqli"]);
              // Update existingsales...
              if ($ID > 0) {
                ++$count;
                mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
                `account` = '{$ID}'
                WHERE `bill_2` = '" . mc_safeSQL($i_email) . "'
                ") or die(mc_MySQLError(__LINE__, __FILE__));
              }
            }
          }
        }
      }
    }
    mc_clearImportFolder();
    return $count;
  }

  public function readImportAccValue($field, $flip, $CSV) {
    if (isset($flip[$field])) {
      if (isset($_SESSION['mc_fieldMapping_alt'][$flip[$field]]) && trim($_SESSION['mc_fieldMapping_alt'][$flip[$field]]) != '') {
        return $_SESSION['mc_fieldMapping_alt'][$flip[$field]];
      } else {
        return $CSV[$flip[$field]];
      }
    }
    return '';
  }

  public function exportAccounts($type) {
    global $msg_accounts33, $msg_accounts34, $msg_addccts32, $msg_addccts33;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    $separator = ',';
    $csvFile   = PATH . 'import/' . $type . '-accounts-' . date('dmYHis') . '.csv';
    $data      = ($type == 'trade' ? $msg_accounts34 : $msg_accounts33) . mc_defineNewline();
    $sqlFilter  = '';
    if (isset($_GET['keys']) && $_GET['keys']) {
      $stm       = mc_safeSQL($_GET['keys']);
      $sqlFilter = "AND `name` LIKE '%{$stm}%' OR `email` LIKE '%{$stm}%' OR `notes` LIKE '%{$stm}%' OR `reason` LIKE '%{$stm}%'";
    }
    $sqlOrder   = '`name`';
    if (isset($_GET['export'])) {
      switch($_GET['export']) {
        case 'name_asc':     $sqlOrder   = '`name`';                      break;
        case 'name_desc':    $sqlOrder   = '`name` DESC';                 break;
        case 'email_asc':    $sqlOrder   = '`email`';                     break;
        case 'email_desc':   $sqlOrder   = '`email` DESC';                break;
        case 'orders_asc':   $sqlOrder   = '`saleCount` DESC';            break;
        case 'orders_desc':  $sqlOrder   = '`saleCount`';                 break;
        case 'revenue_asc':  $sqlOrder   = '`salesRevenue` * 1000 DESC';  break;
        case 'revenue_desc': $sqlOrder   = '`salesRevenue` * 1000';       break;
      }
    }
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
             DATE_FORMAT(`created`,'" . $this->settings->mysqlDateFormat . "') AS `cdate`,
             (SELECT count(*) FROM `" . DB_PREFIX . "sales`
              WHERE `" . DB_PREFIX . "sales`.`account` = `" . DB_PREFIX . "accounts`.`id`
              AND `" . DB_PREFIX . "sales`.`saleConfirmation` = 'yes'
              AND `" . DB_PREFIX . "sales`.`type` = '{$type}'
             ) AS `saleCount`,
             (SELECT SUM(`grandTotal`) FROM `" . DB_PREFIX . "sales`
              WHERE `" . DB_PREFIX . "sales`.`account` = `" . DB_PREFIX . "accounts`.`id`
              AND `" . DB_PREFIX . "sales`.`saleConfirmation` = 'yes'
              AND `" . DB_PREFIX . "sales`.`type` = '{$type}'
             ) AS `salesRevenue`
             FROM `" . DB_PREFIX . "accounts`
             WHERE `enabled` = 'yes'
             AND `type` = '{$type}'
             $sqlFilter
             ORDER BY " . $sqlOrder
             ) or die(mc_MySQLError(__LINE__, __FILE__));
    while ($A = mysqli_fetch_object($query)) {
      switch($type) {
        case 'personal':
          $data .= mc_cleanCSV($A->name, $separator) . $separator . mc_cleanCSV($A->email, $separator) . $separator .
                   $msg_addccts32 . $separator . $A->cdate . $separator . @number_format($A->saleCount) . $separator . mc_cleanCSV(mc_formatPrice($A->salesRevenue,true), $separator) . mc_defineNewline();
          break;
        case 'trade':
          $data .= mc_cleanCSV($A->name, $separator) . $separator . mc_cleanCSV($A->email, $separator) . $separator .
                   $msg_addccts33 . $separator . $A->cdate . $separator . @number_format($A->saleCount) . $separator . mc_cleanCSV(mc_formatPrice($A->salesRevenue,true), $separator) .
                   $separator . mc_cleanCSV($A->tradediscount . '%', $separator) . mc_defineNewline();
          break;
      }
    }
    if ($data) {
      $this->dl->write($csvFile, trim($data));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    }
  }

  public function exportWish() {
    global $msg_accwishlist4;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    $totalHits = mc_rowCount('accounts_wish');
    $SQL       = ($_GET['export'] != 'all' ? 'AND `category` = \'' . mc_digitSan($_GET['export']) . '\'' : '');
    $separator = ',';
    $csvFile   = PATH . 'import/wish-' . date('dmYHis') . '.csv';
    $data      = $msg_accwishlist4 . mc_defineNewline();
    $q_p       = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
                 (SELECT count(*) FROM `" . DB_PREFIX . "accounts_wish`
                  WHERE `" . DB_PREFIX . "accounts_wish`.`product` = `" . DB_PREFIX . "products`.`id`
                 ) AS `saveCnt`,
                 `" . DB_PREFIX . "products`.`id` AS `pid`
                 FROM `" . DB_PREFIX . "accounts_wish`
                 LEFT JOIN `" . DB_PREFIX . "products`
                 ON `" . DB_PREFIX . "accounts_wish`.`product`  = `" . DB_PREFIX . "products`.`id`
                 LEFT JOIN `" . DB_PREFIX . "prod_category`
                 ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
                 WHERE `pEnable` = 'yes'
                 $SQL
                 GROUP BY `" . DB_PREFIX . "accounts_wish`.`product`
                 ORDER BY `saveCnt` DESC,`pName`
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($PROD = mysqli_fetch_object($q_p)) {
      $perc = '0%';
      // Prevent division by zero errors..
      if ($PROD->saveCnt > 0) {
        $perc = number_format($PROD->saveCnt / $totalHits * 100, STATS_DECIMAL_PLACES) . '%';
      }
      $data .= mc_cleanCSV($PROD->pName, $separator) . $separator . $PROD->saveCnt . $separator . $perc . mc_defineNewline();
    }
    if ($data) {
      $this->dl->write($csvFile, trim($data));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    }
  }

  public function addresses($id) {
    $arr = array(
      'bill' => array(),
      'ship' => array()
    );
    $AB = mc_getTableData('addressbook', 'account', $id, ' AND `type` = \'bill\'');
    if (isset($AB->id)) {
      $arr['bill'] = (array) $AB;
    }
    $AS = mc_getTableData('addressbook', 'account', $id, ' AND `type` = \'ship\'');
    if (isset($AS->id)) {
      $arr['ship'] = (array) $AS;
    }
    return $arr;
  }

  public function addAccount() {
    $_POST = mc_safeImport($_POST);
    if ($_POST['pass'] == '') {
      $_POST['pass'] = accounts::passGen();
    }
    $log  = (isset($_POST['enablelog']) && in_array($_POST['enablelog'],array('yes','no')) ? $_POST['enablelog'] : 'no');
    $news = (isset($_POST['newsletter']) && in_array($_POST['newsletter'],array('yes','no')) ? $_POST['newsletter'] : 'no');
    $trade = array(
      'type' => (isset($_POST['type']) && in_array($_POST['type'],array('personal','trade')) ? $_POST['type'] : 'personal'),
      'discount' => ($_POST['tradediscount'] ? (int) $_POST['tradediscount'] : '0'),
      'min' => (int) $_POST['minqty'],
      'max' => (int) $_POST['maxqty'],
      'stock' => (int) $_POST['stocklevel'],
      'mincheckout' => $_POST['mincheckout']
    );
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "accounts` (
    `name`,
	  `created`,
	  `email`,
	  `pass`,
	  `enabled`,
	  `verified`,
	  `timezone`,
	  `ip`,
	  `notes`,
	  `language`,
	  `enablelog`,
    `newsletter`,
    `type`,
    `tradediscount`,
    `minqty`,
    `maxqty`,
    `stocklevel`,
    `mincheckout`,
    `wishtext`
	  ) VALUES (
    '{$_POST['name']}',
	  '" . date("Y-m-d") . "',
	  '{$_POST['email']}',
	  '" . mc_PassHash(array('type' => 'add', 'pass' => $_POST['pass'])) . "',
	  'yes',
	  'yes',
	  '0',
	  '',
	  '{$_POST['notes']}',
	  'english',
	  '{$log}',
    '{$news}',
    '{$trade['type']}',
    '{$trade['discount']}',
    '{$trade['min']}',
    '{$trade['max']}',
    '{$trade['stock']}',
    '{$trade['mincheckout']}',
    '" . (isset($_POST['wishtext']) ? $_POST['wishtext'] : '') . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
    $ID = mysqli_insert_id($GLOBALS["___msw_sqli"]);
    // Update existingsales...
    if ($ID > 0) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
      `account` = '{$ID}'
      WHERE `bill_2` = '{$_POST['email']}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      accounts::updateAddresses($ID);
    }
    return $ID;
  }

  public function updateAccount() {
    $_POST = mc_safeImport($_POST);
    $ID    = (int) $_POST['update'];
    if ($_POST['pass'] == '') {
      $apass = $_POST['opass'];
    } else {
      $apass = mc_PassHash(array('type' => 'add', 'pass' => $_POST['pass']));
    }
    $log  = (isset($_POST['enablelog']) && in_array($_POST['enablelog'],array('yes','no')) ? $_POST['enablelog'] : 'no');
    $news = (isset($_POST['newsletter']) && in_array($_POST['newsletter'],array('yes','no')) ? $_POST['newsletter'] : 'no');
    $sts  = (isset($_POST['enabled']) && in_array($_POST['enabled'],array('yes','no')) ? $_POST['enabled'] : 'yes');
    $trade = array(
      'type' => (isset($_POST['type']) && in_array($_POST['type'],array('personal','trade')) ? $_POST['type'] : 'personal'),
      'discount' => ($_POST['tradediscount'] ? (int) $_POST['tradediscount'] : '0'),
      'min' => (int) $_POST['minqty'],
      'max' => (int) $_POST['maxqty'],
      'stock' => (int) $_POST['stocklevel'],
      'tcode' => (isset($_POST['trackcode']) ? $_POST['trackcode'] : ''),
      'mincheckout' => $_POST['mincheckout']
    );
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `name`          = '{$_POST['name']}',
	  `email`         = '{$_POST['email']}',
	  `pass`          = '{$apass}',
	  `notes`         = '{$_POST['notes']}',
    `enablelog`     = '{$log}',
    `enabled`       = '{$sts}',
    `verified`      = IF(`verified` = 'no','" . ($sts == 'yes' ? 'yes' : 'no') . "',`verified`),
    `newsletter`    = '{$news}',
    `type`          = '{$trade['type']}',
    `tradediscount` = '{$trade['discount']}',
    `minqty`        = '{$trade['min']}',
    `maxqty`        = '{$trade['max']}',
    `stocklevel`    = '{$trade['stock']}',
    `mincheckout`   = '{$trade['mincheckout']}',
    `trackcode`     = '{$trade['tcode']}',
    `wishtext`      = '" . (isset($_POST['wishtext']) ? $_POST['wishtext'] : '') . "'
	  WHERE `id`      = '{$ID}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    accounts::updateAddresses($ID);
  }

  public function updateAddresses($id) {
    $AB   = mc_getTableData('addressbook', 'account', $id, ' AND `type` = \'bill\'');
    $AS   = mc_getTableData('addressbook', 'account', $id, ' AND `type` = \'ship\'');
    $bill = array(array(),array(),array());
    $ship = array(array(),array(),array());
    foreach ($_POST['bill'] AS $k => $v) {
      $slot      = ($k == 'country' ? '1' : $k);
      $bill[0][] = "'" . substr(mc_safeSQL($v), 0, 250) . "'";
      switch($k) {
        case 'nm':
        case 'em':
          $bill[1][] = "`" . $k . "` = '" . substr(mc_safeSQL($v), 0, 250) . "'";
          break;
        default:
          $bill[1][] = "`addr" . $slot . "` = '" . substr(mc_safeSQL($v), 0, 250) . "'";
          break;
      }
    }
    foreach ($_POST['ship'] AS $k => $v) {
      $slot      = ($k == 'country' ? '1' : $k);
      $ship[0][] = "'" . substr(mc_safeSQL($v), 0, 250) . "'";
      switch($k) {
        case 'nm':
        case 'em':
          $ship[1][] = "`" . $k . "` = '" . substr(mc_safeSQL($v), 0, 250) . "'";
          break;
        default:
          $ship[1][] = "`addr" . $slot . "` = '" . substr(mc_safeSQL($v), 0, 250) . "'";
          break;
      }
    }
    if (!isset($AB->id)) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "addressbook` WHERE `account` = '{$id}' AND `type` = 'bill'");
      mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "addressbook` (
      `account`,`nm`,`em`,`addr1`,`addr2`,`addr3`,`addr4`,`addr5`,`addr6`,`default`,`type`
	    ) VALUES (
      '{$id}',
      " . implode(',', $bill[0]) . ",
      'yes',
      'bill'
      )");
    } else {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "addressbook` SET
      " . implode(',', $bill[1]) . "
	    WHERE `account` = '{$id}'
      AND `type` = 'bill'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    if (!isset($AS->id)) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "addressbook` WHERE `account` = '{$id}' AND `type` = 'ship'");
      mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "addressbook` (
      `account`,`nm`,`em`,`addr1`,`addr2`,`addr3`,`addr4`,`addr5`,`addr6`,`addr7`,`default`,`type`
	    ) VALUES (
      '{$id}',
      " . implode(',', $ship[0]) . ",
      'yes',
      'ship'
      )") or die(mc_MySQLError(__LINE__, __FILE__));
    } else {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "addressbook` SET
      " . implode(',', $ship[1]) . "
	    WHERE `account` = '{$id}'
      AND `type` = 'ship'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function deleteAccounts() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "accounts`
    WHERE `id` IN(" . mc_safeSQL(implode(',', $_POST['del'])) . ")
    ");
  }

  public function updateStatus() {
    $ID  = (int) $_GET['accstatus'];
    $sts = (isset($_POST['status']) && in_array($_POST['status'],array('yes','no')) ? $_POST['status'] : 'yes');
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `enabled` = '{$sts}',
    `verified` = 'yes',
    `reason` = '" . mc_safeSQL($_POST['reason']) . "'
    WHERE `id`  = '{$ID}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function activation($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `enabled` = 'yes',
    `verified` = 'yes'
    WHERE `id`  = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updateNotes() {
    $ID = (int) $_GET['notes'];
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `notes` = '" . mc_safeSQL($_POST['notes']) . "'
    WHERE `id`  = '{$ID}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updateMessage() {
    $ID = (int) $_GET['message'];
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "accounts` SET
    `message`    = '" . mc_safeSQL($_POST['msg']) . "',
    `messageexp` = '" . (strtotime($_POST['exp']) > 0 ? $_POST['exp']: '0000-00-00') . "'
    WHERE `id`   = '{$ID}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function passGen() {
    $pass = '';
    // Check min password isn`t zero by mistake..
    // If it is, set a default..
    if ($this->settings->minPassValue == 0) {
      $this->settings->minPassValue = 8;
    }
    $sec = array(
      'A',
      'B',
      'C',
      'D',
      'E',
      'F',
      'G',
      'H',
      'I',
      'J',
      'K',
      'L',
      'M',
      'N',
      'O',
      'P',
      'Q',
      'R',
      'S',
      'T',
      'U',
      'V',
      'W',
      'X',
      'Y',
      'Z',
      'a',
      'b',
      'c',
      'd',
      'e',
      'f',
      'g',
      'h',
      'i',
      'j',
      'k',
      'l',
      'm',
      'n',
      'o',
      'p',
      'q',
      'r',
      's',
      't',
      'u',
      'v',
      'w',
      'x',
      'y',
      'z',
      '0',
      '1',
      '2',
      '3',
      '4',
      '5',
      '6',
      '7',
      '8',
      '9',
      '[',
      ']',
      '&',
      '*',
      '(',
      ')',
      '#',
      '!',
      '%'
    );
    for ($i = 0; $i < count($sec); $i++) {
      $rand = rand(0, (count($sec) - 1));
      $char = $sec[$rand];
      $pass .= $char;
      if ($this->settings->minPassValue == ($i + 1)) {
        return $pass;
      }
    }
    return $pass;
  }

  public function search($field, $data) {
    $ar = array();
    $q  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`name`,`email`,`ip`,`type` FROM `" . DB_PREFIX . "accounts`
          WHERE `" . $field . "` LIKE '%" . mc_safeSQL($data) . "%'
          AND `enabled` = 'yes'
          AND `verified`= 'yes'
          ");
    while ($A = mysqli_fetch_object($q)) {
      $BILL = mysqli_fetch_assoc(
        mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "addressbook` WHERE `account` = '{$A->id}' AND `type` = 'bill'")
      );
      $SHIP = mysqli_fetch_assoc(
        mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "addressbook` WHERE `account` = '{$A->id}' AND `type` = 'ship'")
      );
      $ar[] = array(
        'value' => ($field == 'name' ? $A->name : $A->email),
        'label' => mc_safeHTML($A->name) . ' (' . $A->email . ')',
        'name' => mc_safeHTML($A->name),
        'email' => $A->email,
        'id' => $A->id,
        'bill' => (isset($BILL['id']) ? $BILL : array()),
        'ship' => (isset($SHIP['id']) ? $SHIP : array()),
        'other' => array(
          'ip' => $A->ip,
          'type' => $A->type
        )
      );
    }
    return $ar;
  }

}

?>