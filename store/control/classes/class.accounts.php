<?php

class mcAccounts {

  public $settings;
  public $json;
  public $rwr;
  public $products;

  public function saleAcc($email, $id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `account` = '{$id}'
    WHERE `bill_2` = '" . mc_safeSQL($email) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return mysqli_affected_rows($GLOBALS["___msw_sqli"]);
  }

  public function wishZones($id, $l) {
    $html = '';
    $addr = mcAccounts::getaddresses($id);
    $optg = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/accounts/zone-opt-group.htm');
    $opt  = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/accounts/zone-option.htm');
    $cnt  = $addr[1]['addr1'];
    if ($cnt > 0) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`zName` FROM `" . DB_PREFIX . "zones`
           WHERE `zCountry` = '{$cnt}'
           ORDER BY `zName`
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($Z = mysqli_fetch_object($q)) {
        $ons  = '';
        $qa = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`areaName` FROM `" . DB_PREFIX . "zone_areas`
              WHERE `inZone` = '{$Z->id}'
              AND `zCountry` = '{$cnt}'
              ORDER BY `areaName`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($ZA = mysqli_fetch_object($qa)) {
          $sel  = '';
          if ($ZA->id == $addr[1]['zone']) {
            $sel = ' selected="selected"';
          }
          $ons .= str_replace(array('{id}','{text}','{selected}'),array($ZA->id,mc_cleanData($ZA->areaName),$sel),$opt);
        }
        if ($ons) {
          $html .= str_replace(array('{label}','{options}'),array(mc_safeHTML($Z->zName),$ons),$optg);
        }
      }
    }
    return (trim($html) ? str_replace(array('{id}','{text}','{selected}'),array('0','- - - - - - - -',''),$opt) . $html : str_replace(array('{id}','{text}','{selected}'),array('0',$l[28],''),$opt));
  }

  public function wishTxt($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `wishtext` = '" . mc_safeSQL(strip_tags($_POST['wtxt'])) . "'
    WHERE `id` = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updateRecent($id, $val = '') {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `recent` = '" . mc_safeSQL($val) . "'
    WHERE `id` = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function closeAccount($acc) {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "accounts` WHERE `id` = '{$acc['id']}'");
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "accounts_search` WHERE `account` = '{$acc['id']}'");
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "accounts_wish` WHERE `account` = '{$acc['id']}'");
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "addressbook` WHERE `account` = '{$acc['id']}'");
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "entry_log` WHERE `type` = 'user' AND `userName` = '" . mc_safeSQL($acc['email']) . "'");
    mc_tableTruncationRoutine(array('accounts','accounts_search','accounts_wish','addressbook','entry_log'));
    // Clear session..
    if (isset($_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))])) {
      $_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))] = '';
      unset($_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))]);
    }
  }

  public function params($id = 0, $pref = array(), $saved, $act = 'save') {
    switch($act) {
      case 'save':
        $cur = ($saved ? unserialize($saved) : array());
        $cur[$pref['key']] = $pref['val'];
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
        `params` = '" . mc_safeSQL(serialize($cur)) . "'
        WHERE `id` = '{$id}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      case 'load':
        return ($saved ? unserialize($saved) : array());
        break;
    }
  }

  public function optOut($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `newsletter` = 'no'
    WHERE `id` = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function wish($acc, $prd) {
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "accounts_wish` (
    `account`,
	  `product`,
	  `saved`
	  ) VALUES (
    '{$acc}',
	  '{$prd}',
	  '" . date("Y-m-d") . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function clearMsg($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "accounts` SET
    `message`    = '',
	  `messageexp` = '0000-00-00'
    WHERE `id`   = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function wishdel($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "accounts_wish` WHERE `id` = '{$id}'");
    mc_tableTruncationRoutine(array('accounts_wish'));
  }

  public function searchdel($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "accounts_search` WHERE `id` = '{$id}'");
    mc_tableTruncationRoutine(array('accounts_search'));
  }

  public function wishlist($id, $l, $p, $cnt = 'no') {
    $html  = '';
    $wlar  = array();
    $tmp_w = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/accounts/wishlist-wrapper.htm');
    $tmp_p = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/accounts/wishlist-product.htm');
    $q     = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
             `" . DB_PREFIX . "accounts_wish`.`id` AS `wlid`,
             `" . DB_PREFIX . "products`.`id` AS `pid`,
             DATE_FORMAT(`" . DB_PREFIX . "accounts_wish`.`saved`,'" . $this->settings->mysqlDateFormat . "') AS `wishdate`
             FROM `" . DB_PREFIX . "accounts_wish`
             LEFT JOIN `" . DB_PREFIX . "products`
             ON `" . DB_PREFIX . "accounts_wish`.`product` = `" . DB_PREFIX . "products`.`id`
             WHERE `" . DB_PREFIX . "accounts_wish`.`account` = '{$id}'
             AND `" . DB_PREFIX . "products`.`pEnable` = 'yes'
             ORDER BY `" . DB_PREFIX . "accounts_wish`.`id` DESC
             LIMIT " . $p['limit'] . "," . $p['per']
             ) or die(mc_MySQLError(__LINE__, __FILE__));
    // Get just count..
    if ($cnt == 'yes') {
      $c  = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
      return (isset($c->rows) ? $c->rows : '0');
    }
    while ($WL = mysqli_fetch_object($q)) {
      $wlar[] = $WL->wlid;
      $images  = $this->products->loadProductImage($WL->pid);
      $rwurl = $this->rwr->url(array(
        $this->rwr->config['slugs']['prd'] . '/' . $WL->pid . '/' . ($WL->rwslug ? $WL->rwslug : $this->rwr->title($WL->pName)),
        'pd=' . $WL->pid
      ));
      $html .= str_replace(array(
        '{image}',
        '{big_image_url}',
        '{url}',
        '{name}',
        '{date}',
        '{text}',
        '{id}'
      ),
      array(
      $images[0],
      $images[1],
      $rwurl,
      mc_safeHTML($WL->pName),
      $WL->wishdate,
      mc_filterJS($l[10]),
      $WL->wlid
      ),
      $tmp_p);
    }
    // Clean up any wish entries that had no product...
    if (!empty($wlar)) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "accounts_wish`
      WHERE `id` NOT IN(" . implode(',',$wlar) .") AND `account` = '{$id}'");
      mc_tableTruncationRoutine(array('accounts_wish'));
    }
    return ($html ? str_replace(array(
      '{txt1}','{txt2}','{txt3}','{wish_list}'
    ),
    array(
      $l[7],$l[8],$l[9],$html
    ), $tmp_w) : mc_nothingToShow($l[6]));
  }

  public function searches($id, $l, $p, $cnt = 'no') {
    $html  = '';
    $tmp_w = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/accounts/saved-searches-wrapper.htm');
    $tmp_s = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/accounts/saved-search.htm');
    $q     = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
             DATE_FORMAT(`saved`,'" . $this->settings->mysqlDateFormat . "') AS `sdate`
             FROM `" . DB_PREFIX . "accounts_search`
             WHERE `account` = '{$id}'
             ORDER BY `id` DESC
             LIMIT " . $p['limit'] . "," . $p['per']
             ) or die(mc_MySQLError(__LINE__, __FILE__));
    // Get just count..
    if ($cnt == 'yes') {
      $c  = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
      return (isset($c->rows) ? $c->rows : '0');
    }
    while ($SS = mysqli_fetch_object($q)) {
      $url = $this->rwr->url(array($this->rwr->config['slugs']['sch'] . '/' . $SS->code . '/1','sk=' . $SS->code));
      $html .= str_replace(array(
        '{url}',
        '{name}',
        '{date}',
        '{text}',
        '{id}'
      ),
      array(
      $url,
      mc_safeHTML($SS->name),
      $SS->sdate,
      mc_filterJS($l[6]),
      $SS->id
      ),
      $tmp_s);
    }
    return ($html ? str_replace(array(
      '{txt1}','{txt2}','{txt3}','{saved_searches}'
    ),
    array(
      $l[2],$l[3],$l[4],$html
    ), $tmp_w) : mc_nothingToShow($l[5]));
  }

  public function history($id, $l, $mth, $p, $cnt = 'no') {
    $html  = '';
    $tmp_w = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/accounts/history-wrapper.htm');
    $tmp_s = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/accounts/history-sale.htm');
    $q     = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
             DATE_FORMAT(`purchaseDate`,'" . $this->settings->mysqlDateFormat . "') AS `sdate`
             FROM `" . DB_PREFIX . "sales`
             WHERE (`account` = '{$id}' OR `wishlist` = '{$id}')
             AND `saleConfirmation` = 'yes'
             AND `invoiceNo` > 0
             ORDER BY `id` DESC
             LIMIT " . (isset($p['dash']) ? $p['dash'] : $p['limit'] . "," . $p['per'])
             ) or die(mc_MySQLError(__LINE__, __FILE__));
    // Get just count..
    if ($cnt == 'yes') {
      $c  = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
      return (isset($c->rows) ? $c->rows : '0');
    }
    while ($H = mysqli_fetch_object($q)) {
      $html .= str_replace(array(
        '{url}',
        '{invoice}',
        '{date}',
        '{status}',
        '{cost}'
      ),
      array(
      $this->rwr->url(array(
        'view-order/' . $H->id,
        'vodr=' . $H->id
      )),
      mc_saleInvoiceNumber($H->invoiceNo, $this->settings),
      $H->sdate,
      mc_statusText($H->paymentStatus),
      mc_currencyFormat(mc_formatPrice($H->grandTotal,true))
      ),
      $tmp_s);
    }
    return ($html ? str_replace(array(
      '{txt1}','{txt2}','{txt3}','{txt4}','{history}'
    ),
    array(
      $l[3],$l[4],$l[5],$l[6],$html
    ), $tmp_w) : mc_nothingToShow($l[1]));
  }

  public function checkPass($p) {
    // At least 1 lower case letter..
    if (!preg_match('/([a-z])/', $p)) {
      return false;
    }
    // At least 1 upper case letter..
    if (!preg_match('/([A-Z])/', $p)) {
      return false;
    }
    // At least 1 digit..
    if (!preg_match('/(\d)/', $p)) {
      return false;
    }
    // At least 1 special character..
    if (!preg_match('/([^a-zA-Z0-9])/', $p)) {
      return false;
    }
    return true;
  }

  public function savesearch($code, $acc) {
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "accounts_search` (
    `account`,
	  `code`,
	  `saved`,
    `name`
	  ) VALUES (
    '{$acc}',
	  '{$code}',
	  '" . date("Y-m-d") . "',
    '" . mc_safeSQL(substr($_POST['name'], 0, 50)) . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function access() {
    $u = array();
    if (isset($_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))])) {
      $e = $_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))];
      $u = mcAccounts::user(array(
        'email' => $e
      ));
    }
    return $u;
  }

  public function updateOp($data = array()) {
    switch($data['op']) {
      case 'cur':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
        `currency`  = '" . mc_safeSQL($data['val']) . "'
        WHERE `id`  = '{$data['acc']['id']}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      case 'lang':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
        `language`  = '" . mc_safeSQL($data['val']) . "'
        WHERE `id`  = '{$data['acc']['id']}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
    }
  }

  public function update($f, $a) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `name`       = '" . mc_safeSQL($f['name']) . "',
	  `email`      = '" . mc_safeSQL($f['email']) . "',
	  `pass`       = '" . ($f['pass'] ? mc_PassHash(array('type' => 'add', 'pass' => $f['pass'])) : $a['pass']) . "',
	  `newsletter` = '{$f['news']}',
    `wishtext`   = '" . mc_safeSQL($f['wishtext']) . "'
	  WHERE `id`   = '{$a['id']}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mcAccounts::addresses($a['id'], $f);
  }

  public function create($f) {
    $code = mcAccounts::code();
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
    `system1`,
	  `language`,
	  `enablelog`,
    `newsletter`
	  ) VALUES (
    '" . mc_safeSQL($f['name']) . "',
	  '" . date("Y-m-d") . "',
	  '" . mc_safeSQL($f['email']) . "',
	  '" . mc_PassHash(array('type' => 'add', 'pass' => $f['pass'])) . "',
	  '" . (isset($f['enabled']) && in_array($f['enabled'], array('yes','no')) ? $f['enabled'] : 'no') . "',
	  '" . (isset($f['verified']) && in_array($f['verified'], array('yes','no')) ? $f['verified'] : 'no') . "',
	  '0',
	  '" . mc_safeSQL(mc_getRealIPAddr()) . "',
	  '',
    '{$code}',
	  'english',
	  'yes',
    '{$f['news']}'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
    $ID = mysqli_insert_id($GLOBALS["___msw_sqli"]);
    if ($ID >0) {
      mcAccounts::addresses($ID, $f);
    }
    return array($code, $ID);
  }

  public function addresses($id, $f) {
    $AB   = mc_getTableData('addressbook', 'account', $id, ' AND `type` = \'bill\'');
    $AS   = mc_getTableData('addressbook', 'account', $id, ' AND `type` = \'ship\'');
    $bill = array(array(),array(),array());
    $ship = array(array(),array(),array());
    foreach ($f['bill'] AS $k => $v) {
      $bill[0][] = "'" . substr(mc_safeSQL($v), 0, 250) . "'";
      switch($k) {
        case 'nm':
        case 'em':
          $bill[1][] = "`" . $k . "` = '" . substr(mc_safeSQL($v), 0, 250) . "'";
          break;
        default:
          $bill[1][] = "`addr" . $k . "` = '" . substr(mc_safeSQL($v), 0, 250) . "'";
          break;
      }
    }
    foreach ($f['ship'] AS $k => $v) {
      if ($k != 'zone') {
        $ship[0][] = "'" . substr(mc_safeSQL($v), 0, 250) . "'";
      }
      switch($k) {
        case 'nm':
        case 'em':
          $ship[1][] = "`" . $k . "` = '" . substr(mc_safeSQL($v), 0, 250) . "'";
          break;
        case 'zone':
          $ship[1][] = "`" . $k . "` = '" . (int) $v . "'";
          break;
        default:
          $ship[1][] = "`addr" . $k . "` = '" . substr(mc_safeSQL($v), 0, 250) . "'";
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
      )") or die(mc_MySQLError(__LINE__, __FILE__));
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
      `account`,`nm`,`em`,`addr1`,`addr2`,`addr3`,`addr4`,`addr5`,`addr6`,`addr7`,`zone`,`default`,`type`
	    ) VALUES (
      '{$id}',
      " . implode(',', $ship[0]) . ",
      '" . (isset($f['ship']['zone']) ? $f['ship']['zone'] : '0') . "',
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

  public function getaddresses($id) {
    $b    = array('addr1' => '0','addr2' => '','addr3' => '','addr4' => '','addr5' => '','addr6' => '','addr7' => '','addr8' => '','nm' => '','em' => '');
    $s    = array('addr1' => '0','addr2' => '','addr3' => '','addr4' => '','addr5' => '','addr6' => '','addr7' => '','addr8' => '','nm' => '','em' => '');
    $bill = mc_getTableData('addressbook', 'account', $id, ' AND `type` = \'bill\'');
    $ship = mc_getTableData('addressbook', 'account', $id, ' AND `type` = \'ship\'');
    return array(
      (isset($bill->id) ? (array) $bill : $b),
      (isset($ship->id) ? (array) $ship : $s)
    );
  }

  public function login($f) {
    $a = mc_getTableData('accounts', 'email', mc_safeSQL($f['email']), ' AND `verified` = \'yes\'');
    if (isset($a->id) && mc_PassHash(array('type' => 'calc', 'val' => $f['pass'], 'hash' => $a->pass))) {
      return $a;
    } else {
      $a = new stdclass();
      return $a;
    }
  }

  public function reset($id) {
    $code = mcAccounts::code(25);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `system1`  = '{$code}'
	  WHERE `id` = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return $code;
  }

  public function newpass($id, $f) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `pass`     = '" . mc_PassHash(array('type' => 'add', 'pass' => $f['pass'])) . "',
    `system1`  = ''
	  WHERE `id` = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function log($user) {
    if (!empty($skip) && in_array(strtolower($user->email), $skip)) {
      return;
    }
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "entry_log` (
    `userid`,`logdatetime`,`ip`,`type`
    ) VALUES (
    '{$user->id}','" . date("Y-m-d H:i:s") . "','" . mc_safeSQL(mc_getRealIPAddr()) . "','{$user->type}'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function activate($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `enabled`  = 'yes',
	  `verified` = 'yes',
    `system1`  = ''
	  WHERE `id` = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    sleep(3);
    return 'ok';
  }

  public function code($num = 15) {
    return substr(mc_encrypt(uniqid(rand(),1)), 3, $num);
  }

  public function user($usr = array()) {
    $u = array();
    if (isset($usr['email']) && mswIsValidEmail($usr['email'])) {
      $u = mc_getTableData('accounts', 'email', mc_safeSQL($usr['email']));
    }
    return (array) $u;
  }

}

?>