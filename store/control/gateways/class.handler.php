<?php

class paymentHandler {

  public $settings;
  public $modules;
  public $rwr;
  public $hookevt;
  public $lang = array();

  // Logs messages..
  public function writeLog($id = 0, $debug = '', $gname = '') {
    global $msg_script21;
    if ($gname) {
      $this->gateway_name = $gname;
    }
    if ($id == 0 || $id == '') {
      $id = 'general-test-callback';
    }
    if ($this->settings->logErrors == 'yes') {
      if ($debug) {
        $message = 'DEBUG LOG @ ' . date("j F Y @ H:i:A") . mc_defineNewline();
        $message .= 'Database ID: ' . $id . mc_defineNewline();
        $message .= 'Action/Info: ' . $debug . mc_defineNewline();
        $message .= mc_defineNewline() . '= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =' . mc_defineNewline() . mc_defineNewline();
      } else {
        $message = 'GATEWAY POST LOG @ ' . date("j F Y @ H:i:A") . mc_defineNewline();
        $message .= 'Order ID: ' . $id . mc_defineNewline();
        $message .= 'Payment Server: ' . ($this->paymentServer() ? $this->paymentServer() : 'API') . mc_defineNewline();
        $message .= 'Data Received:' . mc_defineNewline() . mc_defineNewline();
        if (!empty($_POST)) {
          foreach ($_POST AS $key => $value) {
            if (is_array($value)) {
              $message .= '[' . $key . '] => ' . mc_cleanData(print_r($value, true)) . mc_defineNewline();
            } else {
              $message .= '[' . $key . '] => ' . mc_cleanData($value) . mc_defineNewline();
            }
          }
        }
        $message .= mc_defineNewline() . '= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =' . mc_defineNewline() . mc_defineNewline();
      }
      $pathTo = (defined('ADMIN_PANEL') ? REL_PATH : PATH);
      // Attempt to create log directory if it doesn`t exist..
      if (!is_dir($pathTo . $this->settings->logFolderName)) {
        $oldumask = @umask(0);
        @mkdir($pathTo . $this->settings->logFolderName, 0777);
        @umask($oldumask);
      }
      if (is_dir($pathTo . $this->settings->logFolderName) && is_writeable($pathTo . $this->settings->logFolderName)) {
        @file_put_contents($pathTo . $this->settings->logFolderName . '/' . str_replace(' ', '-', strtolower($this->gateway_name)) . '-' . $id . '.txt', $message, FILE_APPEND);
      }
    }
  }

  // Build ppst data from input stream..
  public function postStream($str = array()) {
    $arr = array();
    if ($str['stream']) {
      foreach (explode($str['del'][0], $str['stream']) AS $var) {
        $params = explode($str['del'][1], $var);
        if (isset($params[0], $params[1])) {
          $arr[$params[0]] = urldecode($params[1]);
        }
      }
    }
    return $arr;
  }

  // Next invoice number..
  public function getInvoiceNo($update = true) {
    // Are we starting at specific number..
    $number = ($this->settings->invoiceNo > 0 ? $this->settings->invoiceNo : '1');
    $next   = ($number + 1);
    // Update..
    if ($update) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
      `invoiceNo`  = '{$next}'
      LIMIT 1
      ");
    }
    return $number;
  }

  // Write order status..
  public function writeOrderStatus($id, $text, $status = '') {
    global $msg_script54;
    if (!isset($_POST['vaccount'])) {
      $SL = mc_getTableData('sales','id',mc_digitSan($id));
      if (isset($SL->account)) {
        $_POST['vaccount'] = $SL->account;
      }
    }
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "statuses` (
    `saleID`,`statusNotes`,`dateAdded`,`timeAdded`,`orderStatus`,`adminUser`,`account`
    ) VALUES (
    '{$id}',
    '" . mc_safeSQL($text) . "',
    '" . date("Y-m-d") . "',
    '" . date("H:i:s") . "',
    '{$status}',
    '" . mc_safeSQL($msg_script54) . "',
    '" . (isset($_POST['vaccount']) ? (int) $_POST['vaccount'] : '0') . "'
    )");
    // Update sale to reflect change..
    if ($status) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
      `paymentStatus`  = '{$status}'
      WHERE `id`       = '{$id}'
      ");
    }
  }

  // Change status to live..
  public function changeStatusToLiveSale($data = array()) {
    // Update live database table..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `invoiceNo`         = '" . mc_safeSQL($data['invoice']) . "',
    `account`           = '{$data['account']}',
    `gatewayID`         = '" . mc_safeSQL($data['trans']) . "',
    `saleConfirmation`  = 'yes'
    WHERE `id`          = '{$data['id']}'
    AND `buyCode`       = '{$data['code']}'
    ");
    // Update purchases table..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
    `saleConfirmation`  = 'yes'
    WHERE `saleID`      = '{$data['id']}'
    ");
  }

  // Build product order for emails..
  public function buildProductOrder($id) {
    global $msg_emails44, $msg_emails24, $msg_emails45, $msg_emails4, $msg_emails16, $msg_emails17, $mc_checkout, $msg_emails46;
    $ordrSt = '';
    $query  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "purchases`.`id` AS `pid` FROM `" . DB_PREFIX . "purchases`
              LEFT JOIN `" . DB_PREFIX . "products`
              ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
              WHERE `saleID`                          = '{$id}'
              ORDER BY `" . DB_PREFIX . "purchases`.`id`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($ORDER = mysqli_fetch_object($query)) {
      // Gift certificate or normal product..
      if ($ORDER->giftID > 0) {
        $GIFT    = mc_getTableData('giftcerts', '`id`', $ORDER->giftID);
        $ordrSt .= '[PN]' . mc_cleanData($GIFT->name) . '[/PN]' . mc_defineNewline() . mc_defineNewline();
        $ordrSt .= '[I]' . str_replace(array('{qty}','{price}'),array($ORDER->productQty,$ORDER->salePrice),$msg_emails44) . '[/I]';
        // From/to info..
        if (EMAIL_GIFT_FROM_TO_INCL) {
          $FROM_TO = mc_getTableData('giftcodes', '`giftID`', $ORDER->giftID, 'AND `purchaseID` = \'' . $ORDER->pid . '\'');
          if (isset($FROM_TO->from_name)) {
            $ordrSt .= mc_defineNewline() . '[I]' . str_replace(array(
              '{from_name}',
              '{to_name}'
            ), array(
              mc_cleanData($FROM_TO->from_name),
              mc_cleanData($FROM_TO->to_name)
            ), $msg_emails24) . '[/I]';
          }
        }
      } else {
        $ordrSt .= '[PN]' . mc_cleanData($ORDER->pName) . '[/PN]' . mc_defineNewline() . mc_defineNewline();
        $ordrSt .= '[I]' . str_replace(array('{qty}','{price}','{code}'),array($ORDER->productQty,$ORDER->salePrice,$ORDER->pCode),$msg_emails44) . '[/I]';
        // Attributes..
        if (EMAIL_ATTRIBUTES_INCL) {
          $ordrStA = array();
          $q_at = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_atts`
                  LEFT JOIN `" . DB_PREFIX . "attributes`
                  ON `" . DB_PREFIX . "purch_atts`.`attributeID` = `" . DB_PREFIX . "attributes`.`id`
                  LEFT JOIN `" . DB_PREFIX . "attr_groups`
                  ON `" . DB_PREFIX . "attributes`.`attrGroup` = `" . DB_PREFIX . "attr_groups`.`id`
                  WHERE `" . DB_PREFIX . "purch_atts`.`purchaseID` = '{$ORDER->pid}'
                  ORDER BY `" . DB_PREFIX . "purch_atts`.`id`
                  ") or die(mc_MySQLError(__LINE__, __FILE__));
          if (mysqli_num_rows($q_at) > 0) {
            while ($ATT = mysqli_fetch_object($q_at)) {
              $ordrStA[] = str_replace(array(
                '{attr}',
                '{val}',
                '{cost}'
              ), array(
                mc_cleanData($ATT->groupName),
                mc_cleanData($ATT->attrName),
                '+ ' . $ATT->addCost . ' ' . $mc_checkout[32]
              ), ($ATT->addCost > 0 ? $msg_emails45 : $msg_emails46));
            }
          }
          if (!empty($ordrStA)) {
            $ordrSt .= mc_defineNewline() . '[I]' . implode(mc_defineNewline(), $ordrStA) . '[/I]';
          }
        }
        // Personalisation..
        if (EMAIL_PERSONALISATION_INCL) {
          $ordrStP = array();
          $q_ps = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_pers`
                  LEFT JOIN `" . DB_PREFIX . "personalisation`
                  ON `" . DB_PREFIX . "purch_pers`.`personalisationID` = `" . DB_PREFIX . "personalisation`.`id`
                  WHERE `purchaseID`                               = '{$ORDER->pid}'
                  ORDER BY `" . DB_PREFIX . "purch_pers`.`id`
                  ") or die(mc_MySQLError(__LINE__, __FILE__));
          if (mysqli_num_rows($q_ps) > 0) {
            while ($PS = mysqli_fetch_object($q_ps)) {
              $ordrStP[] = str_replace(array(
                '{personalisation_option}',
                '{visitorData}',
                '{cost}'
              ), array(
                mc_persTextDisplay(mc_cleanData($PS->persInstructions), true),
                mc_cleanData($PS->visitorData),
                ($PS->addCost > 0 ? str_replace('{price}', $PS->addCost, $msg_emails17) : $msg_emails16)
              ), $msg_emails4);
            }
          }
        }
        if ($ordrStP) {
          $ordrSt .= mc_defineNewline() . '[I]' . implode(mc_defineNewline(), $ordrStP) . '[/I]';
        }
      }
      $ordrSt .= mc_defineNewline() . mc_defineNewline();
    }
    return trim($ordrSt);
  }

  // Returns iso4217 code for country, which is required by some gateways..
  public function iso4217($id, $iso = '') {
    if ($iso) {
      $qC = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
            WHERE `cISO` = '{$iso}'
            ");
    } else {
      $qC = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
            WHERE `id` = '{$id}'
            ");
    }
    $C = mysqli_fetch_object($qC);
    return (isset($C->iso4217) ? $C->iso4217 : '');
  }

  // Update address.
  public function updateAccount($acc, $sale) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `account`  = '{$acc}'
    WHERE `id` = '{$sale}'
    ");
  }

  // Returns address..
  public function orderAddresses($order, $nohtml = false, $skipfields = array()) {
    $add = array();
    if (!in_array('bill_1', $skipfields)) {
      $add['billing'][] = mc_cleanData($order->bill_1);
    }
    if (!in_array('bill_3', $skipfields)) {
      $add['billing'][] = mc_cleanData($order->bill_3);
    }
    if (!in_array('bill_4', $skipfields)) {
      if ($order->bill_4) {
        $add['billing'][] = mc_cleanData($order->bill_4);
      }
    }
    if (!in_array('bill_5', $skipfields)) {
      $add['billing'][] = mc_cleanData($order->bill_5);
    }
    if (!in_array('bill_6', $skipfields)) {
      $add['billing'][] = mc_cleanData($order->bill_6);
    }
    if (!in_array('bill_7', $skipfields)) {
      $add['billing'][] = mc_cleanData($order->bill_7);
    }
    if (!in_array('bill_9', $skipfields)) {
      $add['billing'][] = mc_getShippingCountry($order->bill_9);
    }
    $add['shipping'][] = mc_cleanData($order->ship_1);
    $add['shipping'][] = mc_cleanData($order->ship_3);
    if ($order->ship_4) {
      $add['shipping'][] = mc_cleanData($order->ship_4);
    }
    $add['shipping'][] = mc_cleanData($order->ship_5);
    $add['shipping'][] = mc_cleanData($order->ship_6);
    $add['shipping'][] = mc_cleanData($order->ship_7);
    $add['shipping'][] = mc_cleanData($order->ship_8);
    $add['shipping'][] = mc_getShippingCountry($order->shipSetCountry);
    return array(
      'bill-address' => implode(mc_defineNewline(), $add['billing']),
      'ship-address' => implode(mc_defineNewline(), $add['shipping'])
    );
  }

  // Order first name,last name from name..
  public function orderFirstNameLastName($input) {
    $string = explode(' ', $input);
    $other  = array();
    for ($i = 0; $i < count($string); $i++) {
      if ($i > 0) {
        $other[] = $string[$i];
      }
    }
    return array(
      'first-name' => $string[0],
      'last-name' => (!empty($other) ? implode(' ', $other) : '')
    );
  }

  // Stock level adjustment..
  public function stockLevelAdjustment($id) {
    global $msg_emails5, $msg_emails15;
    $ls    = array();
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
             `" . DB_PREFIX . "products`.`id` AS `pid`,
             `" . DB_PREFIX . "purchases`.`id` AS `pcid`
             FROM `" . DB_PREFIX . "purchases`
             LEFT JOIN `" . DB_PREFIX . "products`
             ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
             WHERE `saleID`                      = '{$id}'
			       AND `productType`                  != 'virtual'
             ORDER BY `" . DB_PREFIX . "purchases`.`id`
             ");
    while ($PURCHASES = mysqli_fetch_object($query)) {
      if ($PURCHASES->pDownload == 'yes' && $this->settings->reduceDownloadStock == 'no') {
        // Do nothing..
      } else {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
        `pStock`    = (IF(`pStock`-" . $PURCHASES->productQty . ">=0,`pStock`-" . $PURCHASES->productQty . ",0))
        WHERE `id`  = '{$PURCHASES->pid}'
        ");
        // Adjust product stock..
        if ($PURCHASES->pStockNotify > 0) {
          if (($PURCHASES->pStock - $PURCHASES->productQty) == $PURCHASES->pStockNotify) {
            $cur  = ($PURCHASES->pStock - $PURCHASES->productQty >= 0 ? $PURCHASES->pStock - $PURCHASES->productQty : '0');
            $ls[] = mc_cleanData($PURCHASES->pName) . mc_defineNewline() . str_replace('{stock}', $cur, $msg_emails15);
          }
        }
        // Attributes..
        $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "attributes`.`id` AS `atid` FROM `" . DB_PREFIX . "purch_atts`
             LEFT JOIN `" . DB_PREFIX . "attributes`
             ON `" . DB_PREFIX . "purch_atts`.`attributeID`    = `" . DB_PREFIX . "attributes`.`id`
             WHERE `" . DB_PREFIX . "purch_atts`.`purchaseID`  = '{$PURCHASES->pcid}'
             AND `" . DB_PREFIX . "purch_atts`.`saleID`        = '{$id}'
             AND `" . DB_PREFIX . "purch_atts`.`productID`     = '{$PURCHASES->pid}'
             ORDER BY `" . DB_PREFIX . "attributes`.`orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($ATT = mysqli_fetch_object($q)) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attributes` SET
          `attrStock` = (IF(`attrStock`-" . $PURCHASES->productQty . ">=0,`attrStock`-" . $PURCHASES->productQty . ",0))
          WHERE `id`  = '{$ATT->atid}'
          ");
          if ($PURCHASES->pStockNotify > 0) {
            if (($ATT->attrStock - $PURCHASES->productQty) == $PURCHASES->pStockNotify) {
              $cur  = ($ATT->attrStock - $PURCHASES->productQty >= 0 ? $ATT->attrStock - $PURCHASES->productQty : '0');
              $ls[] = mc_cleanData($PURCHASES->pName . ' - ' . $ATT->attrName) . mc_defineNewline() . str_replace('{stock}', $cur, $msg_emails15);
            }
          }
        }
      }
    }
    return $ls;
  }

  // Log gateway parameters..
  public function logGateWayParams($arr, $id) {
    $params = array();
    foreach ($arr AS $k => $v) {
      if (is_array($v)) {
        $params[] = urldecode($k) . '=>' . urldecode(print_r($v, true));
      } else {
        $params[] = urldecode($k) . '=>' . urldecode($v);
      }
    }
    if (!empty($params)) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
      `gateparams`  = '" . mc_safeSQL(implode('<-->', $params)) . "'
      WHERE `id`    = '{$id}'
	    AND (`gateparams` is null OR `gateparams` = '')
      ");
    }
  }

  // Get coupon info..
  public function getCouponInfo($order) {
    global $msg_emails26, $msg_emails27;
    $t = 'N/A';
    if ($order->couponTotal > 0) {
      switch($order->codeType) {
        case 'gift':
          $GIFT = mc_getTableData('giftcodes', '`code`', $order->couponCode);
          if (isset($GIFT->id)) {
            return '-' . $order->couponTotal . ' (' . $order->couponCode . ')';
          }
          break;
        default:
          $CPN = mc_getTableData('campaigns', '`cDiscountCode`', $order->couponCode);
          if (isset($CPN->id)) {
            switch($CPN->cDiscount) {
              // Free shipping..
              case 'freeshipping':
                return $msg_emails26 . ' (' . $order->couponCode . ')';
                break;
              // No tax..
              case 'notax':
                return $msg_emails27 . ' (' . $order->couponCode . ')';
                break;
              // Fixed or percentage..
              default:
                return '-' . $order->couponTotal . ' (' . $order->couponCode . ')';
                break;
            }
          }
          break;
      }
    }
    return $t;
  }

  // Add coupon usage..
  public function addCouponUsage($campaign, $code, $id, $value) {
    if ($campaign->cDiscount == 'freeshipping') {
      $value = 'freeshipping';
    }
    if ($campaign->cDiscount == 'notax') {
      $value = 'notax';
    }
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "coupons` (
    `cCampaign`,
    `cDiscountCode`,
    `cUseDate`,
    `saleID`,
    `discountValue`
    ) VALUES (
    '{$campaign->id}',
    '{$code}',
    '" . date("Y-m-d") . "',
    '{$id}',
    '{$value}'
    )");
  }

  // Get payment parameters..
  public function paymentParams($method) {
    $params = array();
    $q      = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "methods_params`
              WHERE `method`    = '{$method}'
              ");
    while ($M = mysqli_fetch_object($q)) {
      $params[$M->param] = mc_cleanData($M->value);
    }
    return $params;
  }

  // Get order information..
  public function getOrderInfo($code, $id) {
    $qE    = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "sales`
             WHERE `id`    = '{$id}'
             " . ($code != 'none' ? 'AND `buyCode` = \'' . $code . '\'' : '') . "
             ");
    $ORDER = mysqli_fetch_object($qE);
    return (isset($ORDER->id) ? $ORDER : '');
  }

  // Check order for virtual only order..
  public function checkOrderForVirtualOnly($id) {
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `c` FROM `" . DB_PREFIX . "purchases`
             WHERE `saleID`          = '{$id}'
             AND `productType`      IN('physical','download')
             AND `saleConfirmation`  = 'yes'
             GROUP BY `saleID`
             ");
    $row   = mysqli_fetch_object($query);
    return (isset($row->c) && $row->c > 0 ? 'no' : 'yes');
  }

  // Check order for download only order..
  public function checkOrderForDownloadsOnly($id) {
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `c` FROM `" . DB_PREFIX . "purchases`
             WHERE `saleID`          = '{$id}'
             AND `productType`      IN('physical','virtual')
             AND `saleConfirmation`  = 'yes'
             GROUP BY `saleID`
             ");
    $row   = mysqli_fetch_object($query);
    return (isset($row->c) && $row->c > 0 ? 'no' : 'yes');
  }

  // Add refund..
  public function addRefunded($code, $id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `paymentStatus`  = 'refund'
    WHERE `buyCode`  = '{$code}'
    AND `id`         = '{$id}'
    ");
  }

  // Transmits data via curl..
  public function gatewayTransmission($url, $fields) {
    if (!function_exists('curl_init')) {
      $this->writeLog('errors', 'CURL functions not installed and payment routine terminated. Required for' . (isset($this->gateway) ? ' ' . ucfirst($this->gateway) . ' ' : ' ') . 'payment method. Please enable on server. More Info:' . mc_defineNewline() . 'http://php.net/manual/en/book.curl.php');
      die('<a href="http://php.net/manual/en/book.curl.php" onclick="window.open(this);return false">CURL</a> functions not installed. Required for this payment method. Please enable on server.');
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-Callback-Verification-Script');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
    curl_setopt($ch, CURLOPT_CAINFO, dirname(__file__) . '/certs/cacert.pem');
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
  }

  // Clean post string and strip problematic characters..
  public function stripInvalidChars($string) {
    $s = array(
      '#',
      '\\',
      '>',
      '<',
      '"',
      '[',
      ']',
      '|'
    );
    return str_replace($s, '', $string);
  }

  // Loads processing hidden variables..
  public function loadGatewayFields($ssl, $buyCode, $id, $itemName, $direct = false) {
    $fields = $this->gatewayFields($ssl, $buyCode, $id, $itemName);
    // For certain gateways, its a direct redirect without the form..
    if ($direct) {
      return $fields;
    }
    $html = '<form method="post" id="gateway" action="' . $this->paymentServer() . '"><div>' . mc_defineNewline();
    if (!empty($fields[1])) {
      foreach ($fields[1] AS $name => $value) {
        $html .= '<input type="hidden" name="' . mc_safeHTML($name) . '" value="' . mc_safeHTML($value) . '">' . mc_defineNewline();
      }
    }
    $html .= '</div></form>';
    return array(
      $fields[0],
      $html
    );
  }

}

?>