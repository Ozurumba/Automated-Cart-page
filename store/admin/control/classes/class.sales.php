<?php

class sales {

  public $settings;
  public $dl;
  public $account;

  public function saleItemStockAdj() {
    if (!empty($_POST['p'])) {
      foreach ($_POST['p'] AS $k => $v) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
        `pStock`  = '" . (int) $v . "'
        WHERE `id`  = '{$k}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    if (!empty($_POST['a'])) {
      foreach ($_POST['a'] AS $k => $v) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attributes` SET
        `attrStock` = '" . (int) $v . "'
        WHERE `id`  = '{$k}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function updateSaleIPAccess() {
    // Update sale parameters..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `ipAccess`  = '" . mc_safeSQL($_POST['ips_update']) . "'
    WHERE `id`  = '" . mc_digitSan($_GET['saveIP']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function exportRevenue() {
    global $msg_revenue12;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    $separator = ',';
    $csvFile   = PATH . 'import/revenue-' . mc_convertBoxedDate(mc_convertCalToSQLFormat($_GET['from'], $this->settings), $this->settings) . '-to-' . mc_convertBoxedDate(mc_convertCalToSQLFormat($_GET['to'], $this->settings), $this->settings) . '.csv';
    $data      = $msg_revenue12 . mc_defineNewline();
    // Loop through data..
    $start     = strtotime(mc_convertCalToSQLFormat($_GET['from'], $this->settings));
    $end       = strtotime(mc_convertCalToSQLFormat($_GET['to'], $this->settings));
    $loopDays  = round(($end - $start) / 86400);
    $split     = explode('-', mc_convertCalToSQLFormat($_GET['from'], $this->settings));
    if ($loopDays > 0) {
      for ($i = 0; $i < ($loopDays + 1); $i++) {
        $ts   = strtotime(date('Y-m-d', mktime(0, 0, 0, $split[1], $split[2], $split[0])));
        $day  = date($this->settings->systemDateFormat, strtotime('+ ' . $i . ' days', $ts));
        $sday = date('Y-m-d', strtotime('+ ' . $i . ' days', $ts));
        $qS   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT
                SUM(`subTotal`) AS `sub`,
                SUM(`shipTotal`) AS `ship`,
                SUM(`taxPaid`) AS `tax`,
                SUM(`grandTotal`) AS `grand`,
                SUM(`insuranceTotal`) AS `ins`,
                SUM(`chargeTotal`) AS `charges`
                FROM `" . DB_PREFIX . "sales`
                WHERE `saleConfirmation`  = 'yes'
                AND `purchaseDate`        = '{$sday}'
                AND `paymentStatus`       = '" . mc_safeSQL($_GET['export']) . "'
                " . (isset($_GET['platform']) && in_array($_GET['platform'], array('desktop','mobile','tablet')) ? 'AND `platform` = \'' . $_GET['platform'] . '\'' : '') . "
                GROUP BY `purchaseDate`
                ") or die(mc_MySQLError(__LINE__, __FILE__));
        $SALE = mysqli_fetch_object($qS);
        $data .= mc_cleanCSV($day, $separator) . $separator;
        $data .= mc_cleanCSV((isset($SALE->sub) ? mc_formatPrice($SALE->sub) : '0.00'), $separator) . $separator;
        $data .= mc_cleanCSV((isset($SALE->ship) ? mc_formatPrice($SALE->ship) : '0.00'), $separator) . $separator;
        $data .= mc_cleanCSV((isset($SALE->tax) ? mc_formatPrice($SALE->tax) : '0.00'), $separator) . $separator;
        $data .= mc_cleanCSV((isset($SALE->ins) ? mc_formatPrice($SALE->ins) : '0.00'), $separator) . $separator;
        $data .= mc_cleanCSV((isset($SALE->charges) ? mc_formatPrice($SALE->charges) : '0.00'), $separator) . $separator;
        $data .= mc_cleanCSV((isset($SALE->grand) ? mc_formatPrice($SALE->grand) : '0.00'), $separator) . mc_defineNewline();
      }
      if ($data) {
        $this->dl->write($csvFile, trim($data));
        $this->dl->dl($csvFile, 'application/force-download', 'yes');
      }
    }
  }

  public function homepageGraphData() {
    global $msg_script72, $msg_script73, $msg_script41;
    $ticks = '';
    $line1 = '';
    $line2 = '';
    $line3 = '';
    $range = (isset($_GET['range']) && in_array($_GET['range'], array(
      'week',
      'month',
      'year',
      '3m',
      '6m',
      'last'
    )) ? $_GET['range'] : ADMIN_HOME_DEFAULT_SALES_VIEW);
    switch($range) {
      // This week..
      case 'week':
        $t     = array();
        $l1    = array();
        $l2    = array();
        $l3    = array();
        $which = ($this->settings->jsWeekStart == '0' ? $msg_script73 : $msg_script72);
        // Determine start and end day for loop..
        if ($this->settings->jsWeekStart == '0') {
          switch(date('D')) {
            case 'Sun':
              $from = date("Y-m-d");
              break;
            default:
              $from = date("Y-m-d", strtotime('last sunday'));
              break;
          }
        } else {
          switch(date('D')) {
            case 'Mon':
              $from = date("Y-m-d");
              break;
            default:
              $from = date("Y-m-d", strtotime('last monday'));
              break;
          }
        }
        for ($i = 0; $i < 7; $i++) {
          $date = date("Y-m-d", strtotime("+" . $i . " days", strtotime($from)));
          $l1[] = sales::homepageGraphCounts($date, 'physical', 'week');
          $l2[] = sales::homepageGraphCounts($date, 'download', 'week');
          $l3[] = sales::homepageGraphCounts($date, 'virtual', 'week');
        }
        foreach ($which AS $ts) {
          $t[] = "'$ts'";
        }
        $line1 = implode(',', $l1);
        $line2 = implode(',', $l2);
        $line3 = implode(',', $l3);
        $ticks = implode(',', $t);
        break;
      // Last Month..
      // This month..
      case 'month':
      case '1m':
        $t           = array();
        $l1          = array();
        $l2          = array();
        $l3          = array();
        switch($range) {
          case '1m':
            $daysInMonth = date('t', mktime(0, 0, 0, date('m',strtotime('-1 month')), 1, date('Y',strtotime('-1 month'))));
            break;
          default:
            $daysInMonth = date('t', mktime(0, 0, 0, date('m'), 1, date('Y')));
            break;
        }
        if ($daysInMonth > 0) {
          for ($i = 1; $i < $daysInMonth + 1; $i++) {
            $l1[] = sales::homepageGraphCounts($i, 'physical', $range);
            $l2[] = sales::homepageGraphCounts($i, 'download', $range);
            $l3[] = sales::homepageGraphCounts($i, 'virtual', $range);
            $i    = ($i < 10 ? '0' . $i : $i);
            $t[]  = "'$i'";
          }
          $line1 = implode(',', $l1);
          $line2 = implode(',', $l2);
          $line3 = implode(',', $l3);
          $ticks = implode(',', $t);
        }
        break;
      // This year..
      case 'year':
        $t  = array();
        $l1 = array();
        $l2 = array();
        $l3 = array();
        if (!empty($msg_script41)) {
          for ($i = 1; $i < 13; $i++) {
            $l1[] = sales::homepageGraphCounts($i, 'physical', 'year');
            $l2[] = sales::homepageGraphCounts($i, 'download', 'year');
            $l3[] = sales::homepageGraphCounts($i, 'virtual', 'year');
          }
          foreach ($msg_script41 AS $ts) {
            $t[] = "'$ts'";
          }
          $line1 = implode(',', $l1);
          $line2 = implode(',', $l2);
          $line3 = implode(',', $l3);
          $ticks = implode(',', $t);
        }
        break;
      // Last 3 months..
      // Last 6 months
      case '3m':
      case '6m':
        $t  = array();
        $l1 = array();
        $l2 = array();
        $l3 = array();
        if (!empty($msg_script41)) {
          for ($i = ($range == '3m' ? 3 : 6); $i > 0; $i--) {
            $yr   = date('Y', strtotime('-' . $i . ' month' . ($i > 1 ? 's' : '')));
            $yrs  = date('y', strtotime('-' . $i . ' month' . ($i > 1 ? 's' : '')));
            $mt   = date('m', strtotime('-' . $i . ' month' . ($i > 1 ? 's' : '')));
            $mts  = date('n', strtotime('-' . $i . ' month' . ($i > 1 ? 's' : '')));
            $l1[] = sales::homepageGraphCounts($i, 'physical', $range, array($yr, $mt));
            $l2[] = sales::homepageGraphCounts($i, 'download', $range, array($yr, $mt));
            $l3[] = sales::homepageGraphCounts($i, 'virtual', $range, array($yr, $mt));
            $t[]  = "'" . $msg_script41[($mts - 1)] . " " . $yrs . "'";
          }
          $line1 = implode(',', $l1);
          $line2 = implode(',', $l2);
          $line3 = implode(',', $l3);
          $ticks = implode(',', $t);
        }
        break;
      // Last year..
      case 'last':
        $t  = array();
        $l1 = array();
        $l2 = array();
        $l3 = array();
        if (!empty($msg_script41)) {
          for ($i = 1; $i < 13; $i++) {
            $l1[] = sales::homepageGraphCounts($i, 'physical', 'last');
            $l2[] = sales::homepageGraphCounts($i, 'download', 'last');
            $l3[] = sales::homepageGraphCounts($i, 'virtual', 'last');
          }
          foreach ($msg_script41 AS $ts) {
            $t[] = "'$ts'";
          }
          $line1 = implode(',', $l1);
          $line2 = implode(',', $l2);
          $line3 = implode(',', $l3);
          $ticks = implode(',', $t);
        }
        break;
    }
    // Prevent JS error..
    if ($line1 == '' || $line2 == '' || $line3 == '' || $ticks == '') {
      return array(
        "'0','0','0'",
        "'0','0','0'",
        "'Invalid Config','Invalid Config','Invalid Config'"
      );
    }
    return array(
      $line1,
      $line2,
      $line3,
      $ticks
    );
  }

  public function homepageGraphCounts($value, $type, $range, $other = array()) {
    $today = date('Y-m-d');
    $month = date('m');
    $year  = date('Y');
    switch($range) {
      // This week..
      case 'week':
        $SQL = "WHERE `saleConfirmation` = 'yes' AND `productType` = '{$type}' AND `purchaseDate` = '{$value}'";
        break;
      // This month..
      case 'month':
        $SQL = "WHERE `saleConfirmation` = 'yes' AND `productType` = '{$type}' AND MONTH(`purchaseDate`) = '{$month}' AND YEAR(`purchaseDate`) = '{$year}' AND DAY(`purchaseDate`) = '{$value}'";
        break;
      // This year..
      case 'year':
        $SQL = "WHERE `saleConfirmation` = 'yes' AND `productType` = '{$type}' AND MONTH(`purchaseDate`) = '{$value}' AND YEAR(`purchaseDate`) = '{$year}'";
        break;
      // Last month..
      case '1m':
        $month = date('m', strtotime('-1 month'));
        $SQL   = "WHERE `saleConfirmation` = 'yes' AND `productType` = '{$type}' AND MONTH(`purchaseDate`) = '{$month}' AND YEAR(`purchaseDate`) = '{$year}' AND DAY(`purchaseDate`) = '{$value}'";
        break;
      // Last 3 months..
      // Last 6 months..
      case '3m':
      case '6m':
        $SQL = "WHERE `saleConfirmation` = 'yes' AND `productType` = '{$type}' AND MONTH(`purchaseDate`) = '{$other[1]}' AND YEAR(`purchaseDate`) = '{$other[0]}'";
        break;
      // Last year..
      case 'last':
        $year = date('Y', strtotime('last year'));
        $SQL  = "WHERE `saleConfirmation` = 'yes' AND `productType` = '{$type}' AND MONTH(`purchaseDate`) = '{$value}' AND YEAR(`purchaseDate`) = '{$year}'";
        break;
    }
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`productQty`) AS `qty` FROM `" . DB_PREFIX . "purchases` $SQL") or die(mc_MySQLError(__LINE__, __FILE__));
    $P = mysqli_fetch_object($q);
    return (isset($P->qty) ? $P->qty : "0");
  }

  public function homepageTotalDisplay($range) {
    switch($range) {
      // This week..
      case 'week':
        if ($this->settings->jsWeekStart == '0') {
          switch(date('D')) {
            case 'Sun':
              $from = date('Y-m-d');
              $to   = date('Y-m-d', strtotime("+6 days", strtotime($from)));
              break;
            default:
              $from = date('Y-m-d', strtotime('last sunday'));
              $to   = date('Y-m-d', strtotime("+6 days", strtotime($from)));
              break;
          }
        } else {
          switch(date('D')) {
            case 'Mon':
              $from = date('Y-m-d');
              $to   = date('Y-m-d', strtotime("+6 days", strtotime($from)));
              break;
            default:
              $from = date('Y-m-d', strtotime('last monday'));
              $to   = date('Y-m-d', strtotime("+6 days", strtotime($from)));
              break;
          }
        }
        $SQL = "WHERE `saleConfirmation` = 'yes' AND `purchaseDate` BETWEEN '{$from}' AND '{$to}'";
        break;
      // This month..
      case 'month':
        $daysInMonth = date('t', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $from        = date('Y-m') . '-01';
        $to          = date('Y-m') . '-' . $daysInMonth;
        $SQL         = "WHERE `saleConfirmation` = 'yes' AND `purchaseDate` BETWEEN '{$from}' AND '{$to}'";
        break;
      // This year..
      case 'year':
        $from = date('Y') . '-01-01';
        $to   = date('Y') . '-12-31';
        $SQL  = "WHERE `saleConfirmation` = 'yes' AND `purchaseDate` BETWEEN '{$from}' AND '{$to}'";
        break;
      // Last month..
      case '1m':
        $daysInMonth = date('t', mktime(0, 0, 0, date('m', strtotime('-1 month')), 1, date('Y', strtotime('-1 month'))));
        $from        = date('Y-m') . '-01';
        $to          = date('Y-m') . '-' . $daysInMonth;
        $SQL         = "WHERE `saleConfirmation` = 'yes' AND `purchaseDate` BETWEEN '{$from}' AND '{$to}'";
        break;
      // Last 3 months..
      // Last 6 months..
      case '3m':
      case '6m':
        $months      = substr($range, 0, -1);
        $from        = date('Y-m', strtotime('-' . $months . ' months')) . '-01';
        $to          = date('Y-m', strtotime('-1 month')) . '-' . date('t', strtotime('-1 months'));
        $SQL         = "WHERE `saleConfirmation` = 'yes' AND `purchaseDate` BETWEEN '{$from}' AND '{$to}'";
        break;
      // Last year..
      case 'last':
        $from = date('Y', strtotime('last year')) . '-01-01';
        $to   = date('Y', strtotime('last year')) . '-12-31';
        $SQL  = "WHERE `saleConfirmation` = 'yes' AND `purchaseDate` BETWEEN '{$from}' AND '{$to}'";
        break;
    }
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT
         ROUND(SUM(`subTotal`), 2) AS `sb`,
         ROUND(SUM(`grandTotal`), 2) AS `gt`,
         ROUND(SUM(`taxPaid` + `shipTotal`), 2) AS `sx`
         FROM `" . DB_PREFIX . "sales`
         $SQL
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    $T = mysqli_fetch_object($q);
    return (isset($T->sb) ? array(
      $T->sb,
      $T->sx,
      $T->gt
    ) : array(
      '0.00',
      '0.00',
      '0.00'
    ));
  }

  public function downloadPageLock($sale, $status, $txt) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `downloadLock`   = '" . ($_GET['action'] == 'lock' ? 'yes' : 'no') . "',
    `restrictCount`  = '0'
    WHERE `id`       = '{$sale}'
    ");
    // Write order status..
    if ($status && DL_LOCK_UNLOCK_STATUS) {
      sales::writeOrderStatus($sale, $txt, $status);
    }
  }

  public function editSaleStatus($txt) {
    $visacc = (isset($_POST['visa']) && in_array($_POST['visa'], array('yes','no')) ? $_POST['visa'] : 'no');
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "statuses` SET
    `statusNotes`  = '" . mc_safeSQL($_POST['notes']) . "',
    `orderStatus`  = '" . mc_safeSQL($_POST['sts']) . "',
    `visacc`       = '{$visacc}'
    WHERE `id`     = '" . mc_digitSan($_GET['statnotes']) . "'
    ");
  }

  public function addStatusText() {
    if (isset($_POST['title'], $_POST['text'], $_POST['sref']) && $_POST['sref']) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "status_text`
           WHERE `ref` = '" . mc_safeSQL($_POST['sref']) . "'
           LIMIT 1
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      $ST = mysqli_fetch_object($q);
      if (isset($ST->id)) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "status_text` SET
        `statTitle` = '" . mc_safeSQL($_POST['title']) . "',
        `statText` = '" . mc_safeSQL($_POST['text']) . "'
        WHERE `id` = '{$ST->id}'
        ");
        return 'update';
      } else {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "status_text` (
        `statTitle`,
        `statText`,
        `ref`
        ) VALUES (
        '" . mc_safeSQL($_POST['title']) . "',
        '" . mc_safeSQL($_POST['text']) . "',
        '" . mc_safeSQL($_POST['sref']) . "'
        )");
        return 'add';
      }
    }
    return 'fail';
  }

  public function updateStatusText() {
    if (isset($_GET['id'], $_GET['sale'])) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "status_text`
           WHERE `ref` = '" . mc_safeSQL($_POST['ref']) . "'
           AND `id`   != '" . mc_digitSan($_GET['id']) . "'
           LIMIT 1
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      $ST = mysqli_fetch_object($q);
      if (!isset($ST->id)) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "status_text` SET
        `statTitle` = '" . mc_safeSQL($_POST['statTitle']) . "',
        `statText`  = '" . mc_safeSQL($_POST['statText']) . "',
        `ref`       = '" . mc_safeSQL($_POST['ref']) . "'
        WHERE `id`    = '" . mc_digitSan($_GET['id']) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      } else {
        header("Location: index.php?p=sales-statuses&id=" . (int) $_GET['id'] . "&sale=" . (int) $_GET['sale'] . "&error=" . urlencode($_POST['ref']));
        exit;
      }
    }
  }

  public function deleteStatusText() {
    $_GET['del'] = mc_digitSan($_GET['del']);
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "status_text` WHERE `id` = '{$_GET['del']}'") or die(mc_MySQLError(__LINE__, __FILE__));
    mc_tableTruncationRoutine(array(
      'status_text'
    ));
  }

  // Generate buy code for sales..
  public function generateUniCode($num = 50, $email = '', $name = '') {
    $f = mc_encrypt(date('Ymdhis') . $email . $name . uniqid(rand(), 1));
    $f .= mc_encrypt(time() . $email . uniqid(rand(), 1));
    return substr($f, 0, $num);
  }

  public function addManualSale() {
    $code        = sales::generateUniCode(50, date('dmYhis'), uniqid(rand(), 1));
    $currentTime = date("H:i:s");
    $shipType    = 'weight';
    $tCode       = '';
    // Adjustments for shipping type..
    switch(substr($_POST['setShipRateID'], 0, 4)) {
      case 'flat':
        $shipType               = 'flat';
        $_POST['setShipRateID'] = substr($_POST['setShipRateID'], 4);
        break;
      case 'perc':
        $shipType               = 'percent';
        $_POST['setShipRateID'] = substr($_POST['setShipRateID'], 4);
        break;
      case 'pert':
        $shipType               = 'pert';
        $_POST['setShipRateID'] = substr($_POST['setShipRateID'], 4);
        break;
      case 'qtyr':
        $shipType               = 'qtyr';
        $_POST['setShipRateID'] = substr($_POST['setShipRateID'], 4);
        break;
    }
    // Are we creating account?
    $acc  = (isset($_POST['account']) ? (int) $_POST['account'] : '0');
    $iscr = 'no';
    if ($acc == '0' && isset($_POST['acc_create'])) {
      // See if account already exists first..
      $EX_AC = mc_getTableData('accounts', 'email', mc_safeSQL($_POST['acc_email']));
      if (isset($EX_AC->id)) {
        // If account hasn`t been activated, activate it now..assume admin adding sale is good enough for activation..
        if ($EX_AC->enabled == 'no' || $EX_AC->verified == 'no') {
          $this->account->activation($EX_AC->id);
        }
        $acc            = $EX_AC->id;
        $tCode          = mc_safeSQL($EX_AC->trackcode);
      } else {
        $iscr           = 'yes';
        $_POST['pass']  = '';
        $_POST['name']  = $_POST['acc_name'];
        $_POST['email'] = $_POST['acc_email'];
        $_POST['notes'] = '';
        $acc            = $this->account->addAccount();
      }
    } else {
      $EX_AC = mc_getTableData('accounts', ($acc > 0 ? 'id' : 'email'), ($acc > 0 ? $acc : mc_safeSQL($_POST['acc_email'])));
      if (isset($EX_AC->trackcode)) {
        $tCode = mc_safeSQL($EX_AC->trackcode);
      }
    }
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "sales` (
    `account`,
    `invoiceNo`,
    `saleNotes`,
    `bill_1`,
    `bill_2`,
    `bill_3`,
    `bill_4`,
    `bill_5`,
    `bill_6`,
    `bill_7`,
    `bill_8`,
    `bill_9`,
    `ship_1`,
    `ship_2`,
    `ship_3`,
    `ship_4`,
    `ship_5`,
    `ship_6`,
    `ship_7`,
    `ship_8`,
    `buyerAddress`,
    `paymentStatus`,
    `gatewayID`,
    `taxPaid`,
    `taxRate`,
    `subTotal`,
    `grandTotal`,
    `shipTotal`,
    `insuranceTotal`,
    `chargeTotal`,
    `manualDiscount`,
    `isPickup`,
    `shipSetCountry`,
    `shipSetArea`,
    `setShipRateID`,
    `shipType`,
    `cartWeight`,
    `purchaseDate`,
    `purchaseTime`,
    `buyCode`,
    `saleConfirmation`,
    `paymentMethod`,
    `ipAddress`,
    `trackcode`,
    `type`,
    `platform`,
    `affcode`
    ) VALUES (
    '{$acc}',
    '" . mc_safeSQL($_POST['invoiceNo']) . "',
    '" . mc_safeSQL($_POST['saleNotes']) . "',
    '" . mc_safeSQL($_POST['bill_1']) . "',
    '" . mc_safeSQL($_POST['bill_2']) . "',
    '" . mc_safeSQL($_POST['bill_3']) . "',
    '" . mc_safeSQL($_POST['bill_4']) . "',
    '" . mc_safeSQL($_POST['bill_5']) . "',
    '" . mc_safeSQL($_POST['bill_6']) . "',
    '" . mc_safeSQL($_POST['bill_7']) . "',
    '" . (isset($_POST['bill_8']) ? mc_safeSQL($_POST['bill_8']) : '') . "',
    '" . (int) ($_POST['bill_9']) . "',
    '" . mc_safeSQL($_POST['ship_1']) . "',
    '" . mc_safeSQL($_POST['ship_2']) . "',
    '" . mc_safeSQL($_POST['ship_3']) . "',
    '" . mc_safeSQL($_POST['ship_4']) . "',
    '" . mc_safeSQL($_POST['ship_5']) . "',
    '" . mc_safeSQL($_POST['ship_6']) . "',
    '" . mc_safeSQL($_POST['ship_7']) . "',
    '" . mc_safeSQL($_POST['ship_8']) . "',
    '',
    '" . (isset($_POST['editStatus']) ? $_POST['editStatus'] : 'completed') . "',
    '" . mc_safeSQL($_POST['gatewayID']) . "',
    '" . mc_safeSQL($_POST['taxPaid']) . "',
    '" . mc_safeSQL($_POST['taxRate']) . "',
    '" . mc_safeSQL($_POST['subTotal']) . "',
    '" . mc_safeSQL($_POST['grandTotal']) . "',
    '" . mc_safeSQL($_POST['shipTotal']) . "',
    '" . mc_safeSQL($_POST['insuranceTotal']) . "',
    '" . mc_safeSQL($_POST['chargeTotal']) . "',
    '" . mc_safeSQL($_POST['globalTotal']) . "',
    '" . ($_POST['setShipRateID'] == 'pickup' ? 'yes' : 'no') . "',
    '{$_POST['shipSetCountry']}',
    '{$_POST['shipSetArea']}',
    '" . ($_POST['setShipRateID'] ? $_POST['setShipRateID'] : '0') . "',
    '{$shipType}',
    '" . mc_safeSQL($_POST['cartWeight']) . "',
    '" . date("Y-m-d") . "',
    '{$currentTime}',
    '{$code}',
    'yes',
    '{$_POST['paymentMethod']}',
    '{$_POST['ipAddress']}',
    '{$tCode}',
    '" . ($_POST['sale_type'] == 'personal' ? 'personal' : 'trade') . "',
    '" . mc_safeSQL((MC_PLATFORM_DETECTION == 'pc' ? 'desktop' : MC_PLATFORM_DETECTION)) . "',
    '" . mc_safeSQL($_POST['affcode']) . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
    //---------------------------
    // Last inserted sale id..
    //---------------------------
    $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
    //---------------------------
    // Add purchases..
    //---------------------------
    for ($i = 0; $i < count($_POST['pd']); $i++) {
      $marker = $_POST['pd'][$i];
      if ($_POST['qty'][$i] > 0) {
        $split    = explode('-', $marker);
        $slot     = "'" . $marker . "'";
        $atslot   = "'attr-" . $marker . "'";
        $type     = 'product';
        $price    = mc_formatPrice($_POST['price'][$i]);
        $pr_price = (isset($_POST['persPrice'][$i]) ? mc_formatPrice($_POST['persPrice'][$i]) : '0.00');
        $at_price = (isset($_POST['attrPrice'][$i]) ? mc_formatPrice($_POST['attrPrice'][$i]) : '0.00');
        $P        = mc_getTableData('products', 'id', $split[0]);
        $C        = mc_getTableData('prod_category', 'product', $split[0]);
        $cat      = (isset($C->category) ? $C->category : '0');
        $weight   = $P->pWeight;
        $iFS      = $P->freeShipping;
        $d1       = $P->id . time() . rand(1111, 9999);
        $d2       = date('dmYHis') . uniqid(rand(), 1);
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "purchases` (
        `purchaseDate`,
        `purchaseTime`,
        `saleID`,
        `productType`,
        `productID`,
        `categoryID`,
        `salePrice`,
        `attrPrice`,
        `persPrice`,
        `productQty`,
        `productWeight`,
        `liveDownload`,
        `downloadAmount`,
        `downloadCode`,
        `buyCode`,
        `saleConfirmation`,
        `freeShipping`,
        `platform`
        ) VALUES (
        '" . date("Y-m-d") . "',
        '{$currentTime}',
        '{$id}',
        '" . ($P->pDownload == 'yes' ? 'download' : 'physical') . "',
        '{$P->id}',
        '{$cat}',
        '{$price}',
        '{$at_price}',
        '{$pr_price}',
        '{$_POST['qty'][$i]}',
        '{$weight}',
        '{$P->pDownload}',
        '0',
        '" . ($P->pDownload == 'yes' ? sales::generateDownloadCode($d1, $d2) : '') . "',
        '{$code}',
        'yes',
        '{$iFS}',
        '" . mc_safeSQL((MC_PLATFORM_DETECTION == 'pc' ? 'desktop' : MC_PLATFORM_DETECTION)) . "'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
        $lastPurchaseID = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
        //--------------------------
        // Add personalisation..
        //--------------------------
        if (!empty($_POST['product'])) {
          if (isset($_POST['product'][$slot])) {
            for ($j = 0; $j < count($_POST['product'][$slot]); $j++) {
              if (isset($_POST['pnvalue'][$slot][$j]) && $_POST['pnvalue'][$slot][$j] != '' && $_POST['pnvalue'][$slot][$j] != 'no-option-selected') {
                $ac = '0.00';
                if ($_POST['pnvalue'][$slot][$j] != '' && $_POST['pnvalue'][$slot][$j] != 'no-option-selected' && $_POST['pers_cost'][$slot][$j] > 0) {
                  $ac = mc_formatPrice($_POST['pers_cost'][$slot][$j]);
                }
                mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "purch_pers` (
                `saleID`,
                `productID`,
                `purchaseID`,
                `personalisationID`,
                `visitorData`,
                `addCost`
                ) VALUES (
                '{$id}',
                '{$P->id}',
                '{$lastPurchaseID}',
                '{$_POST['persnew'][$slot][$j]}',
                '" . mc_safeSQL($_POST['pnvalue'][$slot][$j]) . "',
                '{$ac}'
                )") or die(mc_MySQLError(__LINE__, __FILE__));
              }
            }
          }
        }
        //--------------------------
        // Add attributes..
        //--------------------------
        if (!empty($_POST['attr'])) {
          // Add new if applicable..
          for ($i = 0; $i < count($_POST['pd']); $i++) {
            $pAtID = $_POST['pd'][$i];
            if (!empty($_POST['attr'][$pAtID])) {
              foreach ($_POST['attr'][$pAtID] AS $aK => $aV) {
                if ($aV) {
                  $cost = (isset($_POST['attr_cost'][$pAtID][$aK]) && $_POST['attr_cost'][$pAtID][$aK] > 0 ? mc_formatPrice($_POST['attr_cost'][$pAtID][$aK]) : '0.00');
                  mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "purch_atts` (
                  `saleID`,
                  `productID`,
                  `purchaseID`,
                  `attributeID`,
                  `addCost`,
                  `attrName`
                  ) VALUES (
                  '{$id}',
                  '{$_POST['prod_id'][$i]}',
                  '{$lastPurchaseID}',
                  '{$aK}',
                  '{$cost}',
                  '" . mc_safeSQL(mc_cleanData($aV)) . "'
                  )") or die(mc_MySQLError(__LINE__, __FILE__));
                  // Adjust stock level.
                  mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attributes` SET
                  `attrStock`  = (`attrStock` - " . (int) $_POST['qty'][$i] . ")
                  WHERE `id`  = '{$aK}'
                  ") or die(mc_MySQLError(__LINE__, __FILE__));
                }
              }
            }
          }
        }
      }
    }
    // Update stock levels..
    for ($s = 0; $s < count($_POST['prod_id']); $s++) {
      $qty = (int) $_POST['qty'][$s];
      if ($qty > 0) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
        `pStock`  = (`pStock` - " . $qty . ")
        WHERE `id`  = '{$_POST['prod_id'][$s]}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    // Update any products / attributes that might have negative stock..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
    `pStock` = '0'
    WHERE `pStock` < 0
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attributes` SET
    `attrStock` = '0'
    WHERE `attrStock` < 0
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Add status..
    if ($id > 0 && isset($_POST['editNotes'], $_POST['editStatus'])) {
      sales::writeOrderStatus($id, $_POST['editNotes'], $_POST['editStatus']);
    }
    // Clear session vars..
    unset($_SESSION['add-phys-' . mc_encrypt(SECRET_KEY)], $_SESSION['add-down-' . mc_encrypt(SECRET_KEY)]);
    $_SESSION['add-phys-' . mc_encrypt(SECRET_KEY)] = array();
    $_SESSION['add-down-' . mc_encrypt(SECRET_KEY)] = array();
    return array(
      $id,
      $iscr
    );
  }

  public function addProductToSale() {
    global $msg_viewsale73;
    $tweight = 0;
    $looper  = 0;
    $string  = str_replace('{count}', count($_POST['product']), $msg_viewsale73) . mc_defineNewline();
    $stockRd = array();
    $saleIn  = mc_getTableData('sales', 'id', (int) $_GET['sale']);
    if (!empty($_POST['product'])) {
      foreach ($_POST['product'] AS $p) {
        $P       = mc_getTableData('products', 'id', $p);
        $price   = ($P->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? mc_formatPrice($P->pOffer) : mc_formatPrice($P->pPrice));
        $weight  = $P->pWeight;
        $tweight = ($tweight + $weight);
        $d1      = $P->id . time() . rand(1111, 9999);
        $d2      = date('dmYHis') . uniqid(rand(), 1);
        $string .= mc_cleanData($P->pName) . mc_defineNewline();
        // Do we reduce stock for this item?
        switch($P->pDownload) {
          case 'yes':
            if ($this->settings->reduceDownloadStock == 'yes') {
              $stockRd[] = $p;
            }
            break;
          case 'no':
            $stockRd[] = $p;
            break;
        }
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "purchases` (
        `purchaseDate`,
        `purchaseTime`,
        `saleID`,
        `productType`,
        `productID`,
        `categoryID`,
        `persPrice`,
        `attrPrice`,
        `productQty`,
        `salePrice`,
        `productWeight`,
        `liveDownload`,
        `downloadAmount`,
        `downloadCode`,
        `buyCode`,
        `saleConfirmation`,
        `platform`
        ) VALUES (
        '" . mc_convertCalToSQLFormat($_POST['purchaseDate'], $this->settings) . "',
        '" . mc_formatTime($_POST['purchaseTime']) . "',
        '" . (int) $_GET['sale'] . "',
        '" . (in_array($_POST['type'], array(
          'physical',
          'download'
        )) ? $_POST['type'] : 'physical') . "',
        '{$P->id}',
        '" . (int) $_POST['pCat'] . "',
        '0.00',
        '0.00',
        '1',
        '{$price}',
        '{$weight}',
        '{$P->pDownload}',
        '0',
        '" . ($P->pDownload == 'yes' ? sales::generateDownloadCode($d1, $d2) : '') . "',
        '{$_POST['buyCode']}',
        'yes',
        '{$saleIn->platform}'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
        ++$looper;
      }
      // Reduce stock..
      if (!empty($stockRd)) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
        `pStock`  = (IF(`pStock` - 1 >= 0, `pStock` - 1, 0))
        WHERE `id`  IN(" . implode(',', $stockRd) . ")
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    // Adjust sale weight if applicable..
    if ($tweight > 0) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
      `cartWeight`  = ROUND(`cartWeight`+" . $tweight . ",2)
      WHERE `id`    = '{$_GET['sale']}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    // Add status..
    if ($looper > 0 && NEW_PRODUCT_EDIT_STATUS) {
      sales::writeOrderStatus($_GET['sale'], trim($string), $_POST['status']);
    }
  }

  public function writeOrderStatus($sale, $notes, $status) {
    // If account id isn`t present, get it from sale..
    if (!isset($_POST['vaccount'])) {
      $SL = mc_getTableData('sales','id',mc_digitSan($sale));
      if (isset($SL->account)) {
        $_POST['vaccount'] = $SL->account;
      }
    }
    $visacc = (isset($_POST['visacc']) && in_array($_POST['visacc'], array('yes','no')) ? $_POST['visacc'] : 'no');
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "statuses` (
    `saleID`,`statusNotes`,`dateAdded`,`timeAdded`,`orderStatus`,`adminUser`,`visacc`,`account`
    ) VALUES (
    '" . mc_digitSan($sale) . "',
    '" . mc_safeSQL($notes) . "',
    '" . date("Y-m-d") . "',
    '" . date("H:i:s") . "',
    '" . mc_safeSQL($status) . "',
    '" . (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']) ? mc_safeSQL($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']) : 'N/A') . "',
    '{$visacc}',
    '" . (isset($_POST['vaccount']) ? (int) $_POST['vaccount'] : '0') . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function generateDownloadCode($data, $data2) {
    $f = mc_encrypt($data);
    $f .= mc_encrypt($data2);
    return substr($f, 0, 50);
  }

  public function editSale() {
    global $msg_viewsale53, $msg_viewsale92;
    $pcs        = 0;
    $weight_sub = 0;
    $addc       = '0.00';
    $removed    = array();
    $remcount   = 0;
    $remstring  = '';
    $shipType   = 'weight';
    $thisAcc    = (int) $_POST['s_acc'];
    $accType    = (in_array($_POST['s_type'], array('personal','trade')) ? $_POST['s_type'] : 'personal');
    if ((strtolower($_POST['bill_mail']) != strtolower($_POST['bill_2'])) || $thisAcc == '0') {
      $EX_AC = mc_getTableData('accounts', 'email', mc_safeSQL($_POST['bill_2']));
      $thisAcc = (isset($EX_AC->id) ? $EX_AC->id : '0');
      $accType = (isset($EX_AC->type) ? $EX_AC->type : 'personal');
    }
    // Personalised items..
    if (!empty($_POST['pid'])) {
      for ($i = 0; $i < count($_POST['pid']); $i++) {
        $slot = $_POST['pid'][$i];
        // Update existing..
        if (isset($_POST['pers'][$slot][$i])) {
          for ($j = 0; $j < count($_POST['pers'][$slot]); $j++) {
            if ($_POST['pvalue'][$slot][$j] != '' && $_POST['pvalue'][$slot][$j] != 'no-option-selected' && $_POST['qty'][$i] > 0) {
              // Update..
              if (mc_rowCount('purch_pers WHERE `id` = \'' . mc_digitSan($_POST['pers'][$slot][$j]) . '\' AND saleID = \'' . mc_digitSan($_GET['sale']) . '\'') > 0) {
                mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purch_pers` SET
                `visitorData`  = '" . mc_safeSQL($_POST['pvalue'][$slot][$j]) . "'
                WHERE `id`     = '" . mc_digitSan($_POST['pers'][$slot][$j]) . "'
                AND `saleID`   = '" . mc_digitSan($_GET['sale']) . "'
                ") or die(mc_MySQLError(__LINE__, __FILE__));
              } else {
                mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "purch_pers` (
                `saleID`,
                `productID`,
                `purchaseID`,
                `personalisationID`,
                `visitorData`,
                `addCost`
                ) VALUES (
                '" . mc_digitSan($_GET['sale']) . "',
                '{$_POST['product'][$slot][$j]}',
                '{$_POST['pidnew'][$slot][$j]}',
                '{$_POST['pers'][$slot][$j]}',
                '" . mc_safeSQL($_POST['pvalue'][$slot][$j]) . "',
                '" . ($_POST['pers_cost'][$slot][$j] > 0 ? mc_formatPrice($_POST['pers_cost'][$slot][$j]) : '0.00') . "'
              )") or die(mc_MySQLError(__LINE__, __FILE__));
              }
            } else {
              mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purch_pers` WHERE `id` = '" . mc_digitSan($_POST['pers'][$slot][$j]) . "' AND saleID = '" . mc_digitSan($_GET['sale']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
              mc_tableTruncationRoutine(array(
                'purch_pers'
              ));
            }
          }
        }
      }
    }
    // Attributes..
    if (!empty($_POST['attr'])) {
      // Remove current..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purch_atts` WHERE `saleID` = '" . mc_digitSan($_GET['sale']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
      mc_tableTruncationRoutine(array(
        'purch_atts'
      ));
      // Add new if applicable..
      for ($i = 0; $i < count($_POST['pid']); $i++) {
        $pAtID = $_POST['pid'][$i];
        if (!empty($_POST['attr'][$pAtID])) {
          foreach ($_POST['attr'][$pAtID] AS $aK => $aV) {
            if ($aV) {
              $cost = (isset($_POST['attr_cost'][$pAtID][$aK]) && $_POST['attr_cost'][$pAtID][$aK] > 0 ? mc_formatPrice($_POST['attr_cost'][$pAtID][$aK]) : '0.00');
              $awgt = '0';
              $qnat = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `attrWeight` FROM `" . DB_PREFIX . "attributes` WHERE `id` = '{$aK}'")
                      or die(mc_MySQLError(__LINE__, __FILE__));
              $anwt  = mysqli_fetch_object($qnat);
              if (isset($anwt->attrWeight)) {
                $awgt = $anwt->attrWeight;
              }
              mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "purch_atts` (
              `saleID`,
              `productID`,
              `purchaseID`,
              `attributeID`,
              `addCost`,
              `attrName`,
              `attrWeight`
              ) VALUES (
              '" . mc_digitSan($_GET['sale']) . "',
              '{$_POST['prod_id'][$i]}',
              '{$_POST['pid'][$i]}',
              '{$aK}',
              '{$cost}',
              '" . mc_safeSQL(mc_cleanData($aV)) . "',
              '{$awgt}'
              )") or die(mc_MySQLError(__LINE__, __FILE__));
            }
          }
        }
      }
    }
    // Update purchases..
    if (!empty($_POST['pid'])) {
      for ($i = 0; $i < count($_POST['pid']); $i++) {
        if ($_POST['qty'][$i] == '0') {
          $SUB        = mc_getTableData('purchases', 'id', $_POST['pid'][$i], ' AND `saleID` = \'' . mc_digitSan($_GET['sale']) . '\'');
          $PROD_INFO  = mc_getTableData('products', 'id', $SUB->productID);
          $weight_sub = ($weight_sub + $SUB->productWeight);
          if (isset($PROD_INFO->id)) {
            $removed[] = mc_cleanData($PROD_INFO->pName);
            ++$remcount;
          }
          mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purchases` WHERE `id` = '{$_POST['pid'][$i]}' AND `saleID` = '" . mc_digitSan($_GET['sale']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
          mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purch_pers` WHERE `purchaseID` = '{$_POST['pid'][$i]}' AND `saleID` = '" . mc_digitSan($_GET['sale']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
          mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purch_atts` WHERE `purchaseID` = '{$_POST['pid'][$i]}' AND `saleID` = '" . mc_digitSan($_GET['sale']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
          mc_tableTruncationRoutine(array(
            'purchases',
            'purch_pers',
            'purch_atts'
          ));
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
          `salePrice`        = '" . mc_cleanInsertionPrice($_POST['price'][$i]) . "',
          `persPrice`        = '" . (isset($_POST['persPrice'][$i]) ? mc_cleanInsertionPrice($_POST['persPrice'][$i]) : '0.00') . "',
          `attrPrice`        = '" . (isset($_POST['attrPrice'][$i]) ? mc_cleanInsertionPrice($_POST['attrPrice'][$i]) : '0.00') . "',
          `productQty`       = '" . mc_digitSan($_POST['qty'][$i]) . "',
          `saleConfirmation` = '" . (isset($_POST['writeEditStatus']) ? 'yes' : $_POST['saleConfirm']) . "'
          WHERE `id`         = '" . mc_digitSan($_POST['pid'][$i]) . "'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
          ++$pcs;
        }
      }
    }
    // Update stock levels..
    /*if (!empty($_POST['prod_id'])) {
      for ($s = 0; $s < count($_POST['prod_id']); $s++) {
        if (isset($_POST['qtyAdjustment'][$s])) {
          $stock = (int) $_POST['qtyAdjustment'][$s];
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
          `pStock`  = '{$stock}'
          WHERE `id`  = '{$_POST['prod_id'][$s]}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }*/
    // At this point, check that purchases still exist for sale..
    // If all have been set to qty 0, remove sale..
    // Updating it would be pointless..
    if ($pcs == 0) {
      sales::deleteOrderSale($_GET['sale']);
      header("Location: index.php?p=sales");
      exit;
    }
    // Were any products removed..
    if (!empty($removed)) {
      $remstring = str_replace('{count}', $remcount, $msg_viewsale92) . '<br>';
      $remstring .= implode('<br>', $removed);
    }
    // To prevent shipping errors if no shipping..
    $_POST['shipSetCountry'] = (isset($_POST['shipSetCountry']) ? $_POST['shipSetCountry'] : '0');
    $_POST['shipSetArea']    = (isset($_POST['shipSetArea']) ? $_POST['shipSetArea'] : '0');
    $_POST['setShipRateID']  = (isset($_POST['setShipRateID']) ? $_POST['setShipRateID'] : '0');
    // Adjustments for shipping type..
    switch(substr($_POST['setShipRateID'], 0, 4)) {
      case 'flat':
        $shipType               = 'flat';
        $_POST['setShipRateID'] = substr($_POST['setShipRateID'], 4);
        break;
      case 'perc':
        $shipType               = 'percent';
        $_POST['setShipRateID'] = substr($_POST['setShipRateID'], 4);
        break;
      case 'pert':
        $shipType               = 'pert';
        $_POST['setShipRateID'] = substr($_POST['setShipRateID'], 4);
        break;
      case 'qtyr':
        $shipType               = 'qtyr';
        $_POST['setShipRateID'] = substr($_POST['setShipRateID'], 4);
        break;
    }
    // Update sale parameters..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `invoiceNo`         = '" . ltrim($_POST['invoiceNo'], '0') . "',
    `account`           = '{$thisAcc}',
    `saleNotes`         = '" . mc_safeSQL($_POST['saleNotes']) . "',
    `bill_1`            = '" . mc_safeSQL($_POST['bill_1']) . "',
    `bill_2`            = '" . mc_safeSQL($_POST['bill_2']) . "',
    `bill_3`            = '" . mc_safeSQL($_POST['bill_3']) . "',
    `bill_4`            = '" . mc_safeSQL($_POST['bill_4']) . "',
    `bill_5`            = '" . mc_safeSQL($_POST['bill_5']) . "',
    `bill_6`            = '" . mc_safeSQL($_POST['bill_6']) . "',
    `bill_7`            = '" . mc_safeSQL($_POST['bill_7']) . "',
    `bill_8`            = '" . (isset($_POST['bill_8']) ? mc_safeSQL($_POST['bill_8']) : '') . "',
    `bill_9`            = '" . (int) ($_POST['bill_9']) . "',
    `ship_1`            = '" . mc_safeSQL($_POST['ship_1']) . "',
    `ship_2`            = '" . mc_safeSQL($_POST['ship_2']) . "',
    `ship_3`            = '" . mc_safeSQL($_POST['ship_3']) . "',
    `ship_4`            = '" . mc_safeSQL($_POST['ship_4']) . "',
    `ship_5`            = '" . mc_safeSQL($_POST['ship_5']) . "',
    `ship_6`            = '" . mc_safeSQL($_POST['ship_6']) . "',
    `ship_7`            = '" . mc_safeSQL($_POST['ship_7']) . "',
    `ship_8`            = '" . mc_safeSQL($_POST['ship_8']) . "',
    `buyerAddress`      = '',
    `paymentStatus`     = '" . (isset($_POST['editStatus']) ? $_POST['editStatus'] : $_POST['hidStatus']). "',
    `gatewayID`         = '" . mc_safeSQL($_POST['gatewayID']) . "',
    `taxPaid`           = '" . mc_cleanInsertionPrice($_POST['taxPaid']) . "',
    `taxRate`           = '" . mc_digitSan($_POST['taxRate']) . "',
    `couponTotal`       = '" . (isset($_POST['couponTotal']) ? mc_cleanInsertionPrice($_POST['couponTotal']) : '0.00') . "',
    `subTotal`          = '" . mc_cleanInsertionPrice($_POST['subTotal']) . "',
    `grandTotal`        = '" . mc_cleanInsertionPrice($_POST['grandTotal']) . "',
    `shipTotal`         = '" . mc_cleanInsertionPrice($_POST['shipTotal']) . "',
    `insuranceTotal`    = '" . mc_cleanInsertionPrice($_POST['insuranceTotal']) . "',
    `chargeTotal`       = '" . mc_cleanInsertionPrice($_POST['chargeTotal']) . "',
    `globalTotal`       = '" . (isset($_POST['globalTotal']) ? mc_cleanInsertionPrice($_POST['globalTotal']) : '0.00') . "',
    `globalDiscount`    = '" . (isset($_POST['globalDiscount']) ? $_POST['globalDiscount'] : '0') . "',
    `manualDiscount`    = '" . (isset($_POST['manualDiscount']) ? mc_cleanInsertionPrice($_POST['manualDiscount']) : '0.00') . "',
    `isPickup`          = '" . ($_POST['setShipRateID'] == 'pickup' ? 'yes' : 'no') . "',
    `shipSetCountry`    = '{$_POST['shipSetCountry']}',
    `shipSetArea`       = '{$_POST['shipSetArea']}',
    `setShipRateID`     = '{$_POST['setShipRateID']}',
    `shipType`          = '{$shipType}',
    `cartWeight`        = '" . ($weight_sub > 0 ? mc_safeSQL($_POST['cartWeight'] - $weight_sub) : mc_safeSQL($_POST['cartWeight'])) . "',
    `purchaseDate`      = '" . mc_convertCalToSQLFormat($_POST['purchaseDate'], $this->settings) . "',
    `purchaseTime`      = '" . mc_formatTime($_POST['purchaseTime']) . "',
    `paymentMethod`     = '" . mc_safeSQL($_POST['paymentMethod']) . "',
    `ipAddress`         = '{$_POST['ipAddress']}',
    `saleConfirmation`  = '" . (isset($_POST['writeEditStatus']) ? 'yes' : $_POST['saleConfirm']) . "',
    `type`              = '{$accType}',
    `trackcode`         = '{$_POST['trackcode']}',
    `affcode`           = '" . mc_safeSQL($_POST['affcode']) . "'
    WHERE `id`          = '" . mc_digitSan($_GET['sale']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Update purchases date/time..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
    `purchaseDate`  = '" . mc_convertCalToSQLFormat($_POST['purchaseDate'], $this->settings) . "',
    `purchaseTime`  = '" . mc_formatTime($_POST['purchaseTime']) . "'
    WHERE `saleID`  = '" . mc_digitSan($_GET['sale']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Add edit status..
    if (isset($_POST['editStatus'])) {
      sales::writeOrderStatus($_GET['sale'], ($remstring ? mc_safeSQL(trim($remstring)) . mc_defineNewline() . mc_defineNewline() : '') . trim($_POST['editNotes']), $_POST['editStatus']);
    } else {
      if ($remstring) {
        sales::writeOrderStatus($_GET['sale'], mc_safeSQL(trim($remstring)), $_POST['editStatus']);
      }
    }
  }

  public function reloadServices($getzonerate = false) {
    global $msg_javascript214, $msg_javascript215, $msg_viewsale45, $msg_viewsale95, $msg_viewsale124, $msg_viewsale96, $msg_viewsale125, $msg_viewsale106;
    $string = '';
    if ($this->settings->enablePickUp == 'yes') {
      $string .= '<optgroup label="' . $msg_javascript214 . '">';
      $string .= '<option value="pickup">' . $msg_javascript215 . '</option>' . mc_defineNewline();
      $string .= '</optgroup>';
    }
    $q_zone = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`zName`,`zRate` FROM `" . DB_PREFIX . "zones`
              WHERE `zCountry` = '{$_GET['c']}'
              ORDER BY `zName`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($Z = mysqli_fetch_object($q_zone)) {
      if ($getzonerate) {
        return ($Z->zRate == '' ? '0' : $Z->zRate);
      }
      $q_service = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`sName` FROM `" . DB_PREFIX . "services`
                   WHERE `inZone` = '{$Z->id}'
                   ORDER BY `sName`
                   ") or die(mc_MySQLError(__LINE__, __FILE__));
      //---------------------------------------------------------------------
      $q_flat = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`rate` FROM " . DB_PREFIX . "flat
                WHERE `inZone` = '{$Z->id}'
                LIMIT 1
                ") or die(mc_MySQLError(__LINE__, __FILE__));
      $FLAT = mysqli_fetch_object($q_flat);
      //---------------------------------------------------------------------
      $q_prte = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`rate`,`item` FROM `" . DB_PREFIX . "per`
                WHERE `inZone` = '{$Z->id}'
                LIMIT 1
                ") or die(mc_MySQLError(__LINE__, __FILE__));
      $PER_ITEM = mysqli_fetch_object($q_prte);
      //---------------------------------------------------------------------
      $q_percent = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "percent`
                   WHERE `inZone` = '{$Z->id}'
                   ORDER BY `priceFrom`*100,`priceTo`*100
                   ") or die(mc_MySQLError(__LINE__, __FILE__));
      //---------------------------------------------------------------------
      $q_qtyrate = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "qtyrates`
                   WHERE `inZone` = '{$Z->id}'
                   ORDER BY `qtyFrom`,`qtyTo`
                   ") or die(mc_MySQLError(__LINE__, __FILE__));
      if (mysqli_num_rows($q_service) > 0 || mysqli_num_rows($q_percent) > 0 || mysqli_num_rows($q_qtyrate)>0 || isset($FLAT->id) || isset($PER_ITEM->id)) {
        $string .= '<optgroup label="' . mc_cleanData($Z->zName) . '">' . mc_defineNewline();
      }
      //---------------------------------------------------------------------
      // Flat
      //---------------------------------------------------------------------
      if (isset($FLAT->id)) {
        $string .= '<option value="0" disabled="disabled">(&#043;) ' . $msg_viewsale95 . '</option>';
        $string .= '<option value="flat' . $FLAT->id . '">' . $msg_viewsale95 . ' - ' . mc_currencyFormat(mc_formatPrice($FLAT->rate)) . '</option>';
      }
      //---------------------------------------------------------------------
      // PerItem Rate
      //---------------------------------------------------------------------
      if (isset($PER_ITEM->id)) {
        $string .= '<option value="0" disabled="disabled">(&#043;) ' . $msg_viewsale124 . '</option>';
        $string .= '<option value="pert' . $PER_ITEM->id . '">' . str_replace(array(
          '{first}',
          '{item}'
        ), array(
          mc_currencyFormat(mc_formatPrice($PER_ITEM->rate)),
          mc_currencyFormat(mc_formatPrice($PER_ITEM->item))
        ), $msg_viewsale125) . '</option>';
      }
      //---------------------------------------------------------------------
      // Percentage based..
      //---------------------------------------------------------------------
      if (mysqli_num_rows($q_percent) > 0) {
        $string .= '<option value="0" disabled="disabled">(&#043;) ' . $msg_viewsale96 . '</option>';
        while ($PR = mysqli_fetch_object($q_percent)) {
          $string .= '<option value="perc' . $PR->id . '">&nbsp;' . mc_currencyFormat(mc_formatPrice($PR->priceFrom)) . ' - ' . mc_currencyFormat(mc_formatPrice($PR->priceTo)) . ' (' . $PR->percentage . '%)</option>';
        }
      }
      //---------------------------------------------------------------------
      // Quantity based..
      //---------------------------------------------------------------------
      if (mysqli_num_rows($q_qtyrate) > 0) {
        $string .= '<option value="0" disabled="disabled">(&#043;) ' . $msg_sales_view[0] . '</option>';
        while ($QR = mysqli_fetch_object($q_qtyrate)) {
          $string .= '<option value="qtyr' . $QR->id . '">&nbsp;' . $QR->qtyFrom.' - '.$QR->qtyTo.' ('.(substr($QR->rate,-1) == '%' ? $QR->rate.'%' : mc_currencyFormat(mc_formatPrice($QR->rate))) . ')</option>';
        }
      }
      //---------------------------------------------------------------------
      // Services - weight based
      //---------------------------------------------------------------------
      while ($S = mysqli_fetch_object($q_service)) {
        $string .= '<option value="0" disabled="disabled">(&#043;) ' . mc_cleanData($S->sName) . '</option>' . mc_defineNewline();
        $q_rates = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "rates`
                   WHERE `rService` = '{$S->id}'
                   ORDER BY `id`
                   ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($R = mysqli_fetch_object($q_rates)) {
          // Tare weight..
          $tareCost = '';
          $tare     = mc_getTareWeight(0, $R->rService, array(
            $R->rWeightFrom,
            $R->rWeightTo
          ));
          if (isset($tare[0]) && $tare[0] == 'yes') {
            switch(substr($tare[1], -1)) {
              case '%':
                $calc     = substr($tare[1], 0, -1) . '%';
                $tareCost = str_replace('{amount}', $calc, $msg_viewsale106);
                break;
              default:
                $tareCost = str_replace('{amount}', mc_currencyFormat(mc_formatPrice($tare[1])), $msg_viewsale106);
                break;
            }
          }
        }
        $q_rates = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "rates`
                   WHERE `rService` = '{$S->id}'
                   ORDER BY `id`
                   ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($R = mysqli_fetch_object($q_rates)) {
          $string .= '<option value="' . $R->id . '">&nbsp;' . $R->rWeightFrom . ' - ' . $R->rWeightTo . ' (' . mc_currencyFormat(mc_formatPrice($R->rCost)) . $tareCost . ')</option>' . mc_defineNewline();
        }
      }
      if (mysqli_num_rows($q_service) > 0 || mysqli_num_rows($q_percent) > 0 || mysqli_num_rows($q_qtyrate)>0 || isset($FLAT->id) || isset($PER_ITEM->id)) {
        $string .= '</optgroup>' . mc_defineNewline();
      }
    }
    if ($getzonerate) {
      return '0';
    }
    return ($string ? trim($string) : '<option value="0">' . $msg_viewsale45 . '</option>');
  }

  public function reloadCountries() {
    global $msg_viewsale45;
    $string = '';
    $q_zone = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`zName` FROM `" . DB_PREFIX . "zones`
              WHERE `zCountry` = '{$_GET['c']}'
              ORDER BY `zName`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($Z = mysqli_fetch_object($q_zone)) {
      $q_zarea = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`areaName` FROM `" . DB_PREFIX . "zone_areas`
                 WHERE `inZone` = '{$Z->id}'
                 ORDER BY `areaName`
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
      if (mysqli_num_rows($q_zone) > 0) {
        $string .= '<optgroup label="' . mc_cleanData($Z->zName) . '">' . mc_defineNewline();
      }
      while ($ZAREA = mysqli_fetch_object($q_zarea)) {
        $string .= '<option value="' . $ZAREA->id . '">' . mc_cleanData($ZAREA->areaName) . '</option>' . mc_defineNewline();
      }
      if (mysqli_num_rows($q_zone) > 0) {
        $string .= '</optgroup>' . mc_defineNewline();
      }
    }
    return ($string ? trim($string) : '<option value="0">' . $msg_viewsale45 . '</option>');
  }

  public function addActivationLog($sale, $products, $count) {
    global $msg_viewsale49;
    $user = str_replace(array(
      '{user}',
      '{count}'
    ), array(
      (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']) ? mc_safeSQL($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']) : 'N/A'),
      $count
    ), $msg_viewsale49);
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "activation_history` (
    `saleID`,`products`,`restoreDate`,`restoreTime`,`adminUser`
    ) VALUES (
    '{$sale}','{$products}','" . date("Y-m-d") . "',
    '" . date("H:i:s") . "',
    '" . (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']) ? mc_safeSQL($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']) : 'N/A') . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
    // Add entry to order history..
    $lastStatus = mc_getTableData('statuses', 'saleID', $sale, 'ORDER BY `id` DESC', 'orderStatus');
    if (DL_ACTIVATE_STATUS) {
      sales::writeOrderStatus($sale, trim($user), $lastStatus->orderStatus);
    }
  }

  public function activateDownloads($code, $id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
    `liveDownload`    = 'yes',
    `downloadAmount`  = '0',
    `downloadCode`    = '{$code}'
    WHERE `id`        = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Reset zip limit, lock and restriction count..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `zipLimit`       = '0',
    `downloadLock`   = 'no',
    `restrictCount`  = '0'
    WHERE `id`       = '{$_GET['sale']}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function batchUpdateOrderStatus($sale) {
    // Add status..
    sales::writeOrderStatus($sale, $_POST['text'], $_POST['status']);
    $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
    // Update sale to reflect change..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `paymentStatus` = '{$_POST['status']}'
    WHERE `id`      = '{$sale}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    if ($_POST['copy_email'] != '') {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
      `batchMail`    = '" . mc_safeSQL($_POST['copy_email']) . "'
	    ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    return $id;
  }

  public function updateOrderStatus() {
    $_GET['sale'] = mc_digitSan($_GET['sale']);
    // Add status..
    sales::writeOrderStatus($_GET['sale'], $_POST['text'], $_POST['status']);
    $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
    // Update sale to reflect change..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `paymentStatus`    = '{$_POST['status']}',
    `orderCopyEmails`  = '{$_POST['copy_email']}',
    `saleConfirmation` = '" . ($_POST['status'] ? 'yes' : $_POST['saleConfirm']) . "'
    WHERE `id`         = '{$_GET['sale']}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return $id;
  }

  public function addStatusAttachments($id) {
    // Save attachments to server and add to database..
    $attachments = array();
    $isSaving    = (isset($_POST['save']) && $_POST['save'] == 'yes' ? 'yes' : 'no');
    $saveFolder  = ($isSaving == 'yes' ? ($_POST['folder'] == ATTACH_FOLDER ? ATTACH_FOLDER : ATTACH_FOLDER . '/' . $_POST['folder']) : ATTACH_FOLDER);
    if (!empty($_FILES['attachment']['tmp_name'])) {
      for ($i = 0; $i < count($_FILES['attachment']['tmp_name']); $i++) {
        if (ATTACHMENT_FILE_CLEANUP) {
          $ext  = strrchr(strtolower($_FILES['attachment']['name'][$i]), '.');
          $name = preg_replace('/' . ATTACHMENT_FILE_CLEANUP . '/', '', substr($_FILES['attachment']['name'][$i], 0, -strlen($ext) + 1)) . $ext;
        } else {
          $name = $_FILES['attachment']['name'][$i];
        }
        $temp = $_FILES['attachment']['tmp_name'][$i];
        $size = $_FILES['attachment']['size'][$i];
        $type = $_FILES['attachment']['type'][$i];
        if ($name && $temp && $size > 0) {
          if (is_uploaded_file($temp) && $isSaving == 'yes') {
            move_uploaded_file($temp, PATH . $saveFolder . '/' . $name);
            if (file_exists(PATH . $saveFolder . '/' . $name)) {
              $attachments[PATH . $saveFolder . '/' . $name] = $name;
            }
          } else {
            if (file_exists($temp)) {
              $attachments[$temp] = $name;
            }
          }
          // Add to database..
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "attachments` (
          `saleID`,
          `statusID`,
          `attachFolder`,
          `fileName`,
          `fileType`,
          `fileSize`,
          `isSaved`
          ) VALUES (
          '" . mc_digitSan($_GET['sale']) . "',
          '{$id}',
          '" . ($isSaving == 'yes' ? $saveFolder : '') . "',
          '" . mc_safeSQL($name) . "',
          '{$type}',
          '" . ($isSaving == 'yes' ? $size : '0') . "',
          '{$isSaving}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }
    return $attachments;
  }

  public function clearAllAttachments($files = array()) {
    foreach ($files AS $attachments) {
      $split = explode('|||||', $attachments);
      if (file_exists($split[0])) {
        @unlink($split[0]);
      }
    }
  }

  public function deleteOrderStatus() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "statuses`
    WHERE `id` = '" . mc_digitSan($_GET['delete']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `attachFolder`,`fileName` FROM `" . DB_PREFIX . "attachments`
             WHERE `statusID` = '" . mc_digitSan($_GET['delete']) . "'
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($ATT = mysqli_fetch_object($query)) {
      if (file_exists(PATH . $ATT->attachFolder . '/' . $ATT->fileName)) {
        @unlink(PATH . $ATT->attachFolder . '/' . $ATT->fileName);
      }
    }
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "attachments`
    WHERE `statusID` = '" . mc_digitSan($_GET['delete']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mc_tableTruncationRoutine(array(
      'statuses',
      'attachments'
    ));
    return $rows;
  }

  public function createAttachmentsFolder($folder) {
    $chmod  = (CHMOD_VALUE ? CHMOD_VALUE : 0777);
    $status = 'error';
    if (is_dir(PATH . ATTACH_FOLDER) && is_writeable(PATH . ATTACH_FOLDER)) {
      $oldumask = @umask(0);
      @mkdir(PATH . ATTACH_FOLDER . '/' . $folder, $chmod);
      @umask($oldumask);
      if (is_dir(PATH . ATTACH_FOLDER . '/' . $folder)) {
        return 'ok';
      }
    }
    return $status;
  }

  public function exportStatsToCSV() {
    global $msg_stats19, $msg_stats6;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    $separator = ',';
    $csvFile   = PATH . 'import/stats-' . date('d-m-Y-His') . '.csv';
    $split     = explode('|', $_GET['export']);
    $sCounts   = explode('-', $_GET['counts']);
    $data      = $msg_stats19;
    foreach ($split AS $status) {
      $split2 = explode('-', $status);
      $data .= $separator . strtolower(mc_statusText($split2[0]));
    }
    $data .= mc_defineNewline();
    if ($_GET['cat'] > 0) {
      $CAT = mc_getTableData('categories', 'id', mc_digitSan($_GET['cat']));
      $data .= mc_cleanCSV($CAT->catname, $separator) . $separator;
    } else {
      $data .= $msg_stats6 . $separator;
    }
    $data .= substr($_GET['from'], 8, 2) . '/' . substr($_GET['from'], 5, 2) . '/' . substr($_GET['from'], 0, 4) . $separator;
    $data .= substr($_GET['to'], 8, 2) . '/' . substr($_GET['to'], 5, 2) . '/' . substr($_GET['to'], 0, 4) . $separator;
    $data .= $sCounts[0] . $separator;
    $data .= $sCounts[1] . $separator;
    $data .= $sCounts[2] . $separator;
    $data .= $sCounts[3] . $separator;
    $data .= mc_cleanCSV($_GET['gross'], $separator) . $separator;
    $data .= mc_cleanCSV($_GET['fees'], $separator) . $separator;
    $data .= mc_cleanCSV($_GET['net'], $separator) . $separator;
    foreach ($split AS $status) {
      $split2 = explode('-', $status);
      $data .= $split2[1] . $separator;
    }
    if ($data) {
      $this->dl->write($csvFile, trim($data));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    }
  }

  public function exportSalesToCSV($id = 0, $searchids = array()) {
    global $msg_salesexport11, $cmd, $msg_sales39, $msg_sales41, $mcSystemPaymentMethods, $msg_salesexport18, $msg_salesexport19;
    $separator = ',';
    $sales     = '';
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    // Set format..
    if (empty($searchids)) {
      $count    = 0;
      if (isset($_GET['incsale'])) {
        $sqlQuery = 'AND (`s`.`saleConfirmation` = \'yes\' OR `s`.`saleConfirmation` = \'no\') ';
      } else {
        $sqlQuery = 'AND `s`.`saleConfirmation` = \'yes\' ';
      }
      if (!empty($_POST['method']) && in_array($_POST['method'], array_keys($mcSystemPaymentMethods))) {
        $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `paymentMethod` = \'' . $_POST['method'] . '\'';
        ++$count;
      }
      // Check date filter..
      if (isset($_POST['from']) && isset($_POST['to']) && mc_checkValidDate($_POST['from']) != '0000-00-00' && mc_checkValidDate($_POST['to']) != '0000-00-00') {
        if ($_POST['from'] && $_POST['to']) {
          $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `p`.`purchaseDate` BETWEEN \'' . mc_convertCalToSQLFormat($_POST['from'], $this->settings) . '\' AND \'' . mc_convertCalToSQLFormat($_POST['to'], $this->settings) . '\'';
          ++$count;
        }
      }
      // Country filter..
      if (isset($_POST['country']) && $_POST['country'] > 0) {
        $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `shipSetCountry` = \'' . mc_digitSan($_POST['country']) . '\'';
        ++$count;
      }
      // Range filter..
      if (isset($_POST['range']) && (ctype_digit($_POST['range']) && $_POST['range'] > 0 || in_array($_POST['range'], array(
        'completed',
        'pending',
        'refund',
        'cancelled',
        'despatched',
        'shipping'
      )))) {
        $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `paymentStatus` = \'' . $_POST['range'] . '\'';
        ++$count;
      }
      // Type
      if (isset($_POST['type'])) {
        switch($_POST['type']) {
          case 'guest':
            $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `s`.`account` = \'0\'';
            ++$count;
            break;
          case 'trade':
            $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `s`.`type` = \'trade\'';
            ++$count;
            break;
          case 'personal':
            $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `s`.`type` = \'personal\'';
            ++$count;
            break;
        }
      }
      if ($id > 0) {
        $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `s`.`id` = \'' . $id . '\'';
      }
      // File name determined by export type..
      // For single sale, use sale invoice number..
      if ($id > 0) {
        $csvFile = PATH . 'import/sale-' . mc_saleInvoiceNumber($id, $this->settings) . '.csv';
      } else {
        $csvFile = PATH . 'import/sales-' . date('d-m-Y-His') . '.csv';
      }
      if ($count == 0 && $cmd == 'sales-export') {
        return 'none';
      }
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
               DATE_FORMAT(`p`.`purchaseDate`,'" . $this->settings->mysqlDateFormat . "') AS `sdate`,
               `s`.`type` AS `invType`,
               `p`.`id` AS `pID`
               FROM `" . DB_PREFIX . "sales` AS `s`,`" . DB_PREFIX . "purchases` AS `p`
               WHERE `s`.`id` = `p`.`saleID`
               $sqlQuery
               ORDER BY `p`.`saleID`,`p`.`id`,`p`.`productID`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
    } else {
      $csvFile = PATH . 'import/sales-' . date('d-m-Y-His') . '.csv';
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
               DATE_FORMAT(`p`.`purchaseDate`,'" . $this->settings->mysqlDateFormat . "') AS `sdate`,
               `s`.`type` AS `invType`,
               `p`.`id` AS `pID`
               FROM `" . DB_PREFIX . "sales` AS `s`,`" . DB_PREFIX . "purchases` AS `p`
               WHERE `s`.`id` = `p`.`saleID`
               AND `s`.`id` IN(" . mc_safeSQL(implode(',',$searchids)) . ")
               ORDER BY `p`.`saleID`,`p`.`id`,`p`.`productID`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    if (mysqli_num_rows($query) > 0) {
      if ($cmd == 'sales-export') {
        $headerData = $msg_salesexport11;
      } else {
        $headerData = $msg_sales39;
      }
      while ($CSV = mysqli_fetch_object($query)) {
        if ($CSV->giftID > 0) {
          $GTF    = mc_getTableData('giftcerts', 'id', $CSV->giftID);
          $code   = 'N/A';
          $weight = 'N/A';
          $pName  = (isset($GTF->name) && $GTF->name ? $GTF->name : $CSV->deletedProductName);
        } else {
          $PRD    = mc_getTableData('products', 'id', $CSV->productID);
          $code   = (isset($PRD->pCode) && $PRD->pCode ? $PRD->pCode : 'N/A');
          $weight = (isset($PRD->pWeight) && $PRD->pWeight ? $PRD->pWeight : 'N/A');
          $pName  = (isset($PRD->pName) && $PRD->pName ? $PRD->pName : $CSV->deletedProductName);
        }
        $CTRY  = mc_getTableData('countries', 'id', $CSV->shipSetCountry, '', 'cName');
        $shipC = (isset($CTRY->cName) ? $CTRY->cName : 'N/A');
        $CTRY  = mc_getTableData('countries', 'id', $CSV->bill_9, '', 'cName');
        $billC = (isset($CTRY->cName) ? $CTRY->cName : 'N/A');
        $extra = '';
        if ($CSV->globalCost > 0) {
          $discount = $CSV->globalCost;
        } else {
          $discount = '0.00';
        }
        // Does this sale have attributes?
        $q_at = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_atts`
                WHERE `purchaseID`  = '{$CSV->pID}'
                ORDER BY `id`
                ") or die(mc_MySQLError(__LINE__, __FILE__));
        if (mysqli_num_rows($q_at) > 0) {
          while ($ATTR = mysqli_fetch_object($q_at)) {
            $ATR = mc_getTableData('attributes', 'id', $ATTR->attributeID);
            $AG  = mc_getTableData('attr_groups', 'id', $ATR->attrGroup);
            $extra .= '- ' . mc_cleanData($AG->groupName . ': ' . $ATTR->attrName) . mc_defineNewline();
          }
        }
        // Does this sale have personalisation?
        $q_ps = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_pers`
              WHERE `purchaseID`  = '{$CSV->pID}'
              AND `visitorData`  != ''
              AND `visitorData`  != 'no-option-selected'
              ORDER BY `id`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
        if (mysqli_num_rows($q_ps) > 0) {
          if ($extra) {
            $extra = mc_defineNewline() . $extra;
          }
          while ($PS = mysqli_fetch_object($q_ps)) {
            $PERSONALISED = mc_getTableData('personalisation', 'id', $PS->personalisationID);
            $extra .= '- ' . mc_cleanData(mc_persTextDisplay($PERSONALISED->persInstructions, true) . ': ' . $PS->visitorData) . mc_defineNewline();
          }
        }
        // Legacy address field and new..
        if ($CSV->buyerAddress) {
          $chopAddr   = explode(mc_defineNewline(), $CSV->buyerAddress);
          $addyFields = mc_cleanCSV($CSV->ship_1, $separator) . $separator . mc_cleanCSV((isset($chopAddr[0]) ? $chopAddr[0] : ''), $separator) . $separator . mc_cleanCSV((isset($chopAddr[1]) ? $chopAddr[1] : ''), $separator) . $separator . mc_cleanCSV((isset($chopAddr[2]) ? $chopAddr[2] : ''), $separator) . $separator . mc_cleanCSV((isset($chopAddr[3]) ? $chopAddr[3] : ''), $separator) . $separator . mc_cleanCSV((isset($chopAddr[4]) ? $chopAddr[4] : ''), $separator) . $separator . mc_cleanCSV($CSV->ship_8, $separator) . $separator . mc_cleanCSV($CSV->ship_2, $separator) . $separator . mc_cleanCSV($shipC, $separator) . $separator . mc_cleanCSV($CSV->bill_1, $separator) . $separator . mc_cleanCSV((isset($chopAddr[0]) ? $chopAddr[0] : ''), $separator) . $separator . mc_cleanCSV((isset($chopAddr[1]) ? $chopAddr[1] : ''), $separator) . $separator . mc_cleanCSV((isset($chopAddr[2]) ? $chopAddr[2] : ''), $separator) . $separator . mc_cleanCSV((isset($chopAddr[3]) ? $chopAddr[3] : ''), $separator) . $separator . mc_cleanCSV((isset($chopAddr[4]) ? $chopAddr[4] : ''), $separator) . $separator . mc_cleanCSV($CSV->bill_8, $separator) . $separator . mc_cleanCSV($CSV->bill_2, $separator) . $separator . mc_cleanCSV($billC, $separator);
        } else {
          $addyFields = mc_cleanCSV($CSV->bill_1, $separator) . $separator . mc_cleanCSV($CSV->bill_3, $separator) . $separator . mc_cleanCSV($CSV->bill_4, $separator) . $separator . mc_cleanCSV($CSV->bill_5, $separator) . $separator . mc_cleanCSV($CSV->bill_6, $separator) . $separator . mc_cleanCSV($CSV->bill_7, $separator) . $separator . mc_cleanCSV($CSV->bill_8, $separator) . $separator . mc_cleanCSV($CSV->bill_2, $separator) . $separator . mc_cleanCSV($billC, $separator) . $separator . mc_cleanCSV($CSV->ship_1, $separator) . $separator . mc_cleanCSV($CSV->ship_3, $separator) . $separator . mc_cleanCSV($CSV->ship_4, $separator) . $separator . mc_cleanCSV($CSV->ship_5, $separator) . $separator . mc_cleanCSV($CSV->ship_6, $separator) . $separator . mc_cleanCSV($CSV->ship_7, $separator) . $separator . mc_cleanCSV($CSV->ship_8, $separator) . $separator . mc_cleanCSV($CSV->ship_2, $separator) . $separator . mc_cleanCSV($shipC, $separator);
        }
        $sales .= mc_cleanCSV(mc_saleInvoiceNumber($CSV->invoiceNo, $this->settings), $separator) . $separator . mc_cleanCSV($pName . ($extra ? mc_defineNewline() . rtrim($extra) : ''), $separator) . $separator . mc_cleanCSV($code, $separator) . $separator . $CSV->productQty . $separator . mc_formatPrice($CSV->salePrice * $CSV->productQty) . $separator . $CSV->persPrice . $separator . $CSV->attrPrice . $separator . $discount . $separator . $CSV->shipTotal . $separator . $CSV->taxPaid . $separator . $CSV->taxRate . $separator . $CSV->insuranceTotal . $separator . $CSV->chargeTotal . $separator . $weight . $separator . $addyFields . $separator . $CSV->sdate . $separator . mc_cleanCSV(mc_paymentMethodName($CSV->paymentMethod), $separator) . $separator . mc_cleanCSV(($CSV->saleNotes ? $CSV->saleNotes : 'N/A'), $separator) . $separator . mc_cleanCSV(($CSV->invType == 'trade' ? $msg_salesexport18 : $msg_salesexport19), $separator) . mc_defineNewline();
      }
    }
    if ($sales && $headerData) {
      $this->dl->write($csvFile, trim($headerData) . mc_defineNewline() . $sales);
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    } else {
      return 'none';
    }
  }

  public function exportBuyersToCSV() {
    global $msg_sales_export_buyers;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    // Set format..
    $separator    = ',';
    $refunded     = (isset($_POST['refunded']) && in_array($_POST['refunded'], array(
      'yes',
      'no'
    )) ? $_POST['refunded'] : 'no');
    $sqlQuery     = '';
    if (!empty($_POST['range'])) {
      $filterIDs = implode(',', $_POST['range']);
      $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `c`.`category` IN(' . $filterIDs . ') ';
    }
    // Check date filter..
    if (mc_checkValidDate($_POST['from']) != '0000-00-00' && mc_checkValidDate($_POST['to']) != '0000-00-00') {
      $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `p`.`purchaseDate` BETWEEN \'' . mc_convertCalToSQLFormat($_POST['from'], $this->settings) . '\' AND \'' . mc_convertCalToSQLFormat($_POST['to'], $this->settings) . '\'';
    }
    // Country filter..
    if ($_POST['country'] > 0) {
      $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `shipSetCountry` = \'' . mc_digitSan($_POST['country']) . '\'';
    }
    // Type..
    if (isset($_POST['type']) && in_array($_POST['type'], array('all','acc','tacc','guest'))) {
      switch($_POST['type']) {
        case 'acc':
          $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `s`.`account` > 0 AND `s`.`type` = \'personal\'';
          break;
        case 'tacc':
          $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `s`.`account` > 0 AND `s`.`type` = \'trade\'';
          break;
        case 'guest':
          $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `s`.`account` = \'0\'';
          break;
        case 'all':
          $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `s`.`type` IN(\'trade\',\'personal\')';
          break;
      }
    }
    if ($sqlQuery == '') {
      return 'none';
    }
    // Refunded data..
    if ($refunded == 'no') {
      $sqlQuery .= ($sqlQuery ? mc_defineNewline() : '') . 'AND `paymentStatus` != \'refund\'';
    }
    $contacts = array();
    $csvData  = '';
    $csvFile  = PATH . 'import/buyers_' . date('d-m-Y-His') . '.csv';
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *
             FROM `" . DB_PREFIX . "sales` AS `s`,
             `" . DB_PREFIX . "purchases` AS `p`,
             `" . DB_PREFIX . "prod_category` AS `c`,
             `" . DB_PREFIX . "countries` AS `cn`
             WHERE `s`.`id`              = `p`.`saleID`
             AND `s`.`bill_9`            = `cn`.`id`
             AND `p`.`productID`         = `c`.`product`
             AND `s`.`saleConfirmation`  = 'yes'
             $sqlQuery
             GROUP BY `bill_2`
             ORDER BY `bill_1`,`bill_2`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($CSV = mysqli_fetch_object($query)) {
      $inbld  = mc_cleanCSV(mc_cleanData($CSV->bill_1), $separator) . $separator;
      $inbld .= mc_cleanCSV(mc_cleanData($CSV->bill_2), $separator) . $separator;
      $inbld .= mc_cleanCSV(mc_cleanData($CSV->bill_3), $separator) . $separator;
      $inbld .= mc_cleanCSV(mc_cleanData($CSV->bill_4), $separator) . $separator;
      $inbld .= mc_cleanCSV(mc_cleanData($CSV->bill_5), $separator) . $separator;
      $inbld .= mc_cleanCSV(mc_cleanData($CSV->bill_6), $separator) . $separator;
      $inbld .= mc_cleanCSV(mc_cleanData($CSV->bill_7), $separator) . $separator;
      $inbld .= mc_cleanCSV(mc_cleanData($CSV->bill_8), $separator) . $separator;
      $inbld .= mc_cleanCSV(mc_cleanData((isset($CSV->cName) ? $CSV->cName : 'N/A')), $separator);
      $contacts[] = trim($inbld);
    }
    if (!empty($contacts)) {
      // Build csv file..
      $csvData  = implode(mc_defineNewline(), $contacts);
      // Save file to server and download..
      $this->dl->write($csvFile, $msg_sales_export_buyers[5] . mc_defineNewline() . trim($csvData));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    } else {
      return 'none';
    }
  }

  // Delete sale..
  public function deleteOrderSale($id = 0) {
    if ($id > 0) {
      $_GET['delete'] = $id;
    }
    $rows = 0;
    $del  = (isset($_GET['delete']) && ctype_digit($_GET['delete']) ? $_GET['delete'] : '0');
    // Remove attachment files..
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attachments`
             WHERE `saleID` = '{$del}'
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($ATT = mysqli_fetch_object($query)) {
      if ($ATT->isSaved == 'yes' && file_exists(PATH . $ATT->attachFolder . '/' . $ATT->fileName)) {
        @unlink(PATH . $ATT->attachFolder . '/' . $ATT->fileName);
      }
    }
    // Remove attachment data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "attachments` WHERE `saleID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Remove status data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "statuses` WHERE `saleID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Remove personalisation data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purch_pers` WHERE `saleID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purchases` WHERE `saleID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Remove download activation history data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "activation_history` WHERE `saleID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Remove click history data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "click_history` WHERE `saleID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Remove comparisons data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "comparisons` WHERE `saleID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Remove coupon data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "coupons` WHERE `saleID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Remove sale data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "sales` WHERE `id` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'attachments',
      'statuses',
      'purch_pers',
      'purchases',
      'activation_history',
      'click_history',
      'comparisons',
      'coupons',
      'sales'
    ));
    return $rows;
  }

  public function searchStatuses() {
    $ar  = array();
    $q   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "status_text`
           WHERE LOWER(`ref`) LIKE '%" . strtolower(mc_safeSQL($_GET['term'])) . "%'
            OR LOWER(`statTitle`) LIKE '%" . strtolower(mc_safeSQL($_GET['term'])) . "%'
           ORDER BY `ref`
           ");
    while ($S = mysqli_fetch_object($q)) {
      $ar[] = array(
        'value' => $S->id,
        'label' => mc_cleanData($S->statTitle),
        'text' => mc_cleanData($S->statText)
      );
    }
    return $ar;
  }

}

?>