<?php

class shipping {

  public $settings;

  public function addDropShipper() {
    $status = (!empty($_POST['status']) ? mc_safeSQL(implode(',', $_POST['status'])) : 'all');
    $method = (!empty($_POST['method']) ? mc_safeSQL(implode(',', $_POST['method'])) : 'all');
    $prod   = ($_POST['salestatus'] ? mc_safeSQL($_POST['salestatus']) : '');
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "dropshippers` (
    `name`,
	  `emails`,
	  `status`,
	  `method`,
	  `salestatus`,
	  `enable`
    ) VALUES (
    '" . mc_safeSQL($_POST['name']) . "',
    '" . mc_safeSQL($_POST['email']) . "',
    '{$status}',
    '{$method}',
    '{$prod}',
    '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
      'yes',
      'no'
    )) ? $_POST['enable'] : 'no') . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updateDropShipper() {
    $status = (!empty($_POST['status']) ? mc_safeSQL(implode(',', $_POST['status'])) : 'all');
    $method = (!empty($_POST['method']) ? mc_safeSQL(implode(',', $_POST['method'])) : 'all');
    $prod   = ($_POST['salestatus'] ? mc_safeSQL($_POST['salestatus']) : '');
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "dropshippers` SET
    `name`       = '" . mc_safeSQL($_POST['name']) . "',
	  `emails`     = '" . mc_safeSQL($_POST['email']) . "',
	  `status`     = '{$status}',
	  `method`     = '{$method}',
	  `salestatus` = '{$prod}',
	  `enable`     = '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
      'yes',
      'no'
    )) ? $_POST['enable'] : 'no') . "'
    WHERE `id` = '" . mc_digitSan($_POST['update']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function deleteDropShipper() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "dropshippers`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'dropshippers'
    ));
    return $rows;
  }

  public function batchUpdateRatesRoutine($type) {
    $which = (isset($_POST['enable']) ? $_POST['enable'] : 'yes');
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . $type . "` SET
    `enabled` = '{$which}'
    WHERE `inZone` IN(" . implode(',', $_POST['inZone']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return mysqli_affected_rows($GLOBALS["___msw_sqli"]);
  }

  public function batchUpdateRates() {
    $_POST = mc_safeImport($_POST);
    $count = 0;
    $pref  = (!empty($_POST['rpref']) ? $_POST['rpref'] : array(
      'weight'
    ));
    $type  = (isset($_POST['type']) ? $_POST['type'] : 'incr');
    if (!empty($_POST['zones'])) {
      // Flat rates..
      if (in_array('flat', $pref)) {
        $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "flat`
                 WHERE `inZone` IN(" . implode(',', $_POST['zones']) . ")
                 ORDER BY `inZone`
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($FRATE = mysqli_fetch_object($query)) {
          switch($type) {
            case 'fixed':
              mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "flat` SET
              `rate`     = '" . mc_cleanInsertionPrice($_POST['price']) . "'
              WHERE `id` = '{$FRATE->id}'
              ") or die(mc_MySQLError(__LINE__, __FILE__));
              break;
            case 'incr':
              shipping::increaseRateByAmount($_POST['price'], $FRATE->rate, $FRATE->id, 'flat');
              break;
            default:
              shipping::decreaseRateByAmount($_POST['price'], $FRATE->rate, $FRATE->id, 'flat');
              break;
          }
          ++$count;
        }
        // Fix minus values..
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "flat` SET `rate` = '0.00' WHERE `rate` < 0") or die(mc_MySQLError(__LINE__, __FILE__));
      }
      // Per item rates..
      if (in_array('peri', $pref)) {
        $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "per`
                 WHERE `inZone` IN(" . implode(',', $_POST['zones']) . ")
                 ORDER BY `inZone`
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($PIRATE = mysqli_fetch_object($query)) {
          switch($type) {
            case 'fixed':
              if ($_POST['price'] > 0) {
                mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "per` SET
                `rate`     = '" . mc_cleanInsertionPrice($_POST['price']) . "'
                WHERE `id` = '{$PIRATE->id}'
                ") or die(mc_MySQLError(__LINE__, __FILE__));
              }
              // Adjustment for additional items..
              if ($_POST['add'] > 0) {
                mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "per` SET
                `item`     = '" . mc_cleanInsertionPrice($_POST['add']) . "'
                WHERE `id` = '{$PIRATE->id}'
                ") or die(mc_MySQLError(__LINE__, __FILE__));
              }
              break;
            case 'incr':
              if ($_POST['price'] > 0) {
                shipping::increaseRateByAmount($_POST['price'], $PIRATE->rate, $PIRATE->id, 'per_item');
              }
              // Adjustment for additional items..
              if ($_POST['add'] > 0) {
                shipping::increaseRateByAmount($_POST['add'], $PIRATE->item, $PIRATE->id, 'per_item_add');
              }
              break;
            default:
              if ($_POST['price'] > 0) {
                shipping::decreaseRateByAmount($_POST['price'], $PIRATE->rate, $PIRATE->id, 'per_item');
              }
              // Adjustment for additional items..
              if ($_POST['add'] > 0) {
                shipping::decreaseRateByAmount($_POST['add'], $PIRATE->item, $PIRATE->id, 'per_item_add');
              }
              break;
          }
          ++$count;
        }
        // Fix minus values..
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "per` SET `rate` = '0.00' WHERE `rate` < 0") or die(mc_MySQLError(__LINE__, __FILE__));
      }
      // Percentage based rates..
      if (in_array('perc', $pref)) {
        $quer2 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "percent`
                 WHERE `inZone` IN(" . implode(',', $_POST['zones']) . ")
                 ORDER BY `inZone`
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($PRATE = mysqli_fetch_object($quer2)) {
          switch($type) {
            case 'fixed':
              mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "percent` SET
              `percentage`  = '" . mc_rateCleaner($_POST['price']) . "'
              WHERE `id`    = '{$PRATE->id}'
              ") or die(mc_MySQLError(__LINE__, __FILE__));
              break;
            case 'incr':
              shipping::increaseRateByAmount($_POST['price'], $PRATE->percentage, $PRATE->id, 'perc');
              break;
            default:
              shipping::decreaseRateByAmount($_POST['price'], $PRATE->percentage, $PRATE->id, 'perc');
              break;
          }
          ++$count;
        }
        // Fix minus values..
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "percent` SET `percentage` = '0' WHERE `percentage` < 0") or die(mc_MySQLError(__LINE__, __FILE__));
      }
      // Weight based rates..
      if (in_array('weight', $pref)) {
        $quer3 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "rates`.`id` AS `rid`,`" . DB_PREFIX . "services`.`id` AS `sid` FROM `" . DB_PREFIX . "rates`
               LEFT JOIN `" . DB_PREFIX . "services`
               ON `" . DB_PREFIX . "services`.`id` = `" . DB_PREFIX . "rates`.`rService`
               WHERE `inZone` IN(" . implode(',', $_POST['zones']) . ")
               ORDER BY `inZone`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($RATE = mysqli_fetch_object($quer3)) {
          switch($type) {
            case 'fixed':
              mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "rates` SET
              `rCost`     = '" . mc_cleanInsertionPrice($_POST['price']) . "'
              WHERE `id`  = '{$RATE->rid}'
              ") or die(mc_MySQLError(__LINE__, __FILE__));
              break;
            case 'incr':
              shipping::increaseRateByAmount($_POST['price'], $RATE->rCost, $RATE->rid, 'weight');
              break;
            default:
              shipping::decreaseRateByAmount($_POST['price'], $RATE->rCost, $RATE->rid, 'weight');
              break;
          }
          ++$count;
        }
        // Fix minus values..
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "rates` SET `rCost` = '0.00' WHERE `rCost` < 0") or die(mc_MySQLError(__LINE__, __FILE__));
      }
      // Tare based rates..
      if (in_array('tare', $pref)) {
        $quer4 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "tare`.`id` AS `tid`,`" . DB_PREFIX . "services`.`id` AS `sid` FROM `" . DB_PREFIX . "tare`
               LEFT JOIN `" . DB_PREFIX . "services`
               ON `" . DB_PREFIX . "services`.`id` = `" . DB_PREFIX . "tare`.`rService`
               WHERE `inZone` IN(" . implode(',', $_POST['zones']) . ")
               ORDER BY `inZone`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($TARE = mysqli_fetch_object($quer4)) {
          switch($type) {
            case 'fixed':
              mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "tare` SET
              `rCost`     = '{$_POST['price']}'
              WHERE `id`  = '{$TARE->tid}'
              ") or die(mc_MySQLError(__LINE__, __FILE__));
              break;
            case 'incr':
              shipping::increaseRateByAmount($_POST['price'], $TARE->rCost, $TARE->tid, 'tare');
              break;
            default:
              shipping::decreaseRateByAmount($_POST['price'], $TARE->rCost, $TARE->tid, 'tare');
              break;
          }
          ++$count;
        }
        // Fix minus values..
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "tare` SET `rCost` = '0.00' WHERE `rCost` < 0") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    return $count;
  }

  public function increaseRateByAmount($price, $cur_price, $id, $type) {
    // Is the current price, percentage based, eg: tare weight
    if (substr($cur_price, -1) == '%') {
      $cur_price = substr($cur_price, 0, -1);
      if (substr($price, -1) == '%') {
        $perc     = floor(substr($price, 0, strpos($price, '%')));
        $sum      = number_format($cur_price * $perc / 100, 2);
        $newPrice = mc_formatPrice($cur_price + $sum) . '%';
      } else {
        $newPrice = mc_formatPrice($cur_price + $price) . '%';
      }
      if (str_replace('%', '', $newPrice) < 0) {
        $newPrice = '0.01%';
      }
    } else {
      if (substr($price, -1) == '%') {
        $perc     = floor(substr($price, 0, strpos($price, '%')));
        $sum      = number_format($cur_price * $perc / 100, 2);
        $newPrice = mc_formatPrice($cur_price + $sum);
      } else {
        $newPrice = mc_formatPrice($cur_price + $price);
      }
      if ($newPrice < 0) {
        $newPrice = '0.01';
      }
    }
    switch($type) {
      // Flat rate..
      case 'flat':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "flat` SET
        `rate`     = '" . mc_cleanInsertionPrice($newPrice) . "'
        WHERE `id` = '{$id}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      // Per item rates..
      case 'per_item':
      case 'per_item_add':
        if ($type == 'per_item') {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "per` SET
          `rate`     = '" . mc_cleanInsertionPrice($newPrice) . "'
          WHERE `id` = '{$id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "per` SET
          `item`     = '" . mc_cleanInsertionPrice($newPrice) . "'
          WHERE `id` = '{$id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
        break;
      // Percentage rate..
      case 'perc':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "percent` SET
        `percentage` = '" . mc_cleanInsertionPrice($newPrice) . "'
        WHERE `id`   = '{$id}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      // Weight rates..
      case 'weight':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "rates` SET
        `rCost`    = '" . mc_cleanInsertionPrice($newPrice) . "'
        WHERE `id` = '{$id}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      // Tare rates..
      case 'tare':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "tare` SET
        `rCost`    = '{$newPrice}'
        WHERE `id` = '{$id}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
    }
  }

  public function decreaseRateByAmount($price, $cur_price, $id, $type) {
    // Is the current price, percentage based, eg: tare weight
    if (substr($cur_price, -1) == '%') {
      $cur_price = substr($cur_price, 0, -1);
      if (substr($price, -1) == '%') {
        $perc     = floor(substr($price, 0, strpos($price, '%')));
        $sum      = number_format($cur_price * $perc / 100, 2);
        $newPrice = mc_formatPrice($cur_price - $sum) . '%';
      } else {
        $newPrice = mc_formatPrice($cur_price - $price) . '%';
      }
      if (str_replace('%', '', $newPrice) < 0) {
        $newPrice = '0.01%';
      }
    } else {
      if (substr($price, -1) == '%') {
        $perc     = floor(substr($price, 0, strpos($price, '%')));
        $sum      = number_format($cur_price * $perc / 100, 2);
        $newPrice = mc_formatPrice($cur_price - $sum);
      } else {
        $newPrice = mc_formatPrice($cur_price - $price);
      }
      if ($newPrice < 0) {
        $newPrice = '0.01';
      }
    }
    switch($type) {
      // Flat rate..
      case 'flat':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "flat` SET
        `rate`     = '" . mc_cleanInsertionPrice($newPrice) . "'
        WHERE `id` = '{$id}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      // Per item rates..
      case 'per_item':
      case 'per_item_add':
        if ($type == 'per_item') {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "per` SET
          `rate`     = '" . mc_cleanInsertionPrice($newPrice) . "'
          WHERE `id` = '{$id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "per` SET
          `item`     = '" . mc_cleanInsertionPrice($newPrice) . "'
          WHERE `id` = '{$id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
        break;
      // Percentage rate..
      case 'perc':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "percent` SET
        `percentage` = '" . mc_cleanInsertionPrice($newPrice) . "'
        WHERE `id`   = '{$id}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      // Weight rates..
      case 'weight':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "rates` SET
        `rCost`    = '" . mc_cleanInsertionPrice($newPrice) . "'
        WHERE `id` = '{$id}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      // Tare rates..
      case 'tare':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "tare` SET
        `rCost`    = '{$newPrice}'
        WHERE `id` = '{$id}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
    }
  }

  public function addPercentRate() {
    $c = 0;
    if (!empty($_POST['inZone'])) {
      for ($i = 0; $i < count($_POST['inZone']); $i++) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "percent` (
        `inZone`,`percentage`,`enabled`,`priceFrom`,`priceTo`
        ) VALUES (
        '{$_POST['inZone'][$i]}',
        '" . mc_rateCleaner($_POST['percentage']) . "',
        '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
            'yes',
            'no'
          )) ? $_POST['enable'] : 'no') . "',
        '" . mc_cleanInsertionPrice($_POST['priceFrom']) . "',
        '" . mc_cleanInsertionPrice($_POST['priceTo']) . "'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
        ++$c;
      }
    }
    return $c;
  }

  public function updatePercentRate() {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "percent` SET
    `inZone`      = '{$_POST['inZone']}',
    `priceFrom`   = '" . mc_cleanInsertionPrice($_POST['priceFrom']) . "',
    `priceTo`     = '" . mc_cleanInsertionPrice($_POST['priceTo']) . "',
    `percentage`  = '{$_POST['percentage']}',
    `enabled`     = '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
        'yes',
        'no'
      )) ? $_POST['enable'] : 'no') . "'
    WHERE `id`      = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return '1';
  }

  public function deletePercentRate() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "percent`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'percent'
    ));
    return $rows;
  }

  public function addFlatRate() {
    $c = 0;
    $u = 0;
    if (!empty($_POST['inZone'])) {
      for ($j = 0; $j < count($_POST['inZone']); $j++) {
        if (mc_rowCount('flat WHERE `inZone` = \'' . $_POST['inZone'][$j] . '\'') > 0) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "flat` SET
          `rate`         = '{$_POST['rate']}',
          `enabled`      = '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
              'yes',
              'no'
            )) ? $_POST['enable'] : 'no') . "'
          WHERE `inZone` = '{$_POST['inZone'][$j]}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
          ++$u;
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "flat` (
          `inZone`,`rate`,`enabled`
          ) VALUES (
          '{$_POST['inZone'][$j]}',
          '{$_POST['rate']}',
          '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
              'yes',
              'no'
            )) ? $_POST['enable'] : 'no') . "'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
          ++$c;
        }
      }
    }
    return array(
      $c,
      $u
    );
  }

  public function updateFlatRate() {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "flat` SET
    `inZone`   = '{$_POST['inZone']}',
    `rate`     = '" . mc_cleanInsertionPrice($_POST['rate']) . "',
    `enabled`  = '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
        'yes',
        'no'
      )) ? $_POST['enable'] : 'no') . "'
    WHERE `id` = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return '1';
  }

  public function deleteFlatRate() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "flat`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'flat'
    ));
    return $rows;
  }

  public function addPerItemRate() {
    $c = 0;
    $u = 0;
    if (!empty($_POST['inZone'])) {
      for ($j = 0; $j < count($_POST['inZone']); $j++) {
        if (mc_rowCount('per WHERE `inZone` = \'' . $_POST['inZone'][$j] . '\'') > 0) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "per` SET
          `rate`         = '{$_POST['rate']}',
          `item`         = '{$_POST['item']}',
          `enabled`      = '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
              'yes',
              'no'
            )) ? $_POST['enable'] : 'no') . "'
          WHERE `inZone` = '{$_POST['inZone'][$j]}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
          ++$u;
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "per` (
          `inZone`,`rate`,`item`,`enabled`
          ) VALUES (
          '{$_POST['inZone'][$j]}',
          '{$_POST['rate']}',
          '{$_POST['item']}',
          '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
              'yes',
              'no'
            )) ? $_POST['enable'] : 'no') . "'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
          ++$c;
        }
      }
    }
    return array(
      $c,
      $u
    );
  }

  public function updatePerItemRate() {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "per` SET
    `inZone`   = '{$_POST['inZone']}',
    `rate`     = '" . mc_cleanInsertionPrice($_POST['rate']) . "',
    `item`     = '" . mc_cleanInsertionPrice($_POST['item']) . "',
    `enabled`  = '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
        'yes',
        'no'
      )) ? $_POST['enable'] : 'no') . "'
    WHERE `id` = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return '1';
  }

  public function deletePerItemRate() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "per`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'per'
    ));
    return $rows;
  }

  public function addQtyRate() {
    $c = 0;
    if (!empty($_POST['inZone'])) {
      for ($i = 0; $i < count($_POST['inZone']); $i++) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "qtyrates` (
        `inZone`,`rate`,`enabled`,`qtyFrom`,`qtyTo`
        ) VALUES (
        '{$_POST['inZone'][$i]}',
        '" . mc_rateCleaner($_POST['rate']) . "',
        '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
            'yes',
            'no'
          )) ? $_POST['enable'] : 'no') . "',
        '" . mc_cleanInsertionPrice($_POST['qtyFrom']) . "',
        '" . mc_cleanInsertionPrice($_POST['qtyTo']) . "'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
        ++$c;
      }
    }
    return $c;
  }

  public function updateQtyRate() {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "qtyrates` SET
    `inZone`   = '{$_POST['inZone']}',
    `qtyFrom`  = '" . mc_cleanInsertionPrice($_POST['qtyFrom']) . "',
    `qtyTo`    = '" . mc_cleanInsertionPrice($_POST['qtyTo']) . "',
    `rate`     = '{$_POST['rate']}',
    `enabled`  = '" . (isset($_POST['enable']) && in_array($_POST['enable'], array(
        'yes',
        'no'
      )) ? $_POST['enable'] : 'no') . "'
    WHERE `id`      = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return '1';
  }

  public function deleteQtyRate() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "qtyrates`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'qtyrates'
    ));
    return $rows;
  }

  public function addService() {
    $_POST = mc_safeImport($_POST);
    $temp  = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '');
    $name  = (isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '');
    $zones = count($_POST['inZone']);
    $count = 0;
    if ($temp && $name) {
      if (is_uploaded_file($temp)) {
        $handle = fopen($temp, 'r');
        while (($CSV = fgetcsv($handle, 10000)) !== FALSE) {
          for ($i = 0; $i < $zones; $i++) {
            if ($CSV[0] && $CSV[1]) {
              mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "services` (
              `sName`,
              `sEstimation`,
              `sSignature`,
              `inZone`,
              `enableCOD`
              ) VALUES (
              '{$CSV[0]}',
              '{$CSV[1]}',
              '" . (isset($CSV[2]) && in_array($CSV[2], array(
                  'yes',
                  'no'
                )) ? $CSV[2] : 'no') . "',
              '{$_POST['inZone'][$i]}',
              '" . (isset($CSV[3]) && in_array($CSV[3], array(
                  'yes',
                  'no'
                )) ? $CSV[3] : 'no') . "'
              )") or die(mc_MySQLError(__LINE__, __FILE__));
            }
          }
          ++$count;
        }
        fclose($handle);
        if (isset($temp) && file_exists($temp)) {
          @unlink($temp);
        }
      }
    } else {
      for ($i = 0; $i < $zones; $i++) {
        if ($_POST['sName'] && $_POST['sEstimation']) {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "services` (
          `sName`,
          `sEstimation`,
          `sSignature`,
          `inZone`,
          `enableCOD`
          ) VALUES (
          '{$_POST['sName']}',
          '{$_POST['sEstimation']}',
          '" . (isset($_POST['sSignature']) ? $_POST['sSignature'] : 'no') . "',
          '{$_POST['inZone'][$i]}',
          '" . (isset($_POST['enableCOD']) ? $_POST['enableCOD'] : 'no') . "'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
      $count = 1;
    }
    return array(
      $count,
      $zones
    );
  }

  public function updateService() {
    $_POST = mc_safeImport($_POST);
    $c     = 0;
    if ($_POST['sName'] && $_POST['sEstimation'] && $_POST['inZone'] > 0) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "services` SET
      `sName`        = '{$_POST['sName']}',
      `sEstimation`  = '{$_POST['sEstimation']}',
      `sSignature`   = '" . (isset($_POST['sSignature']) ? $_POST['sSignature'] : 'no') . "',
      `inZone`       = '{$_POST['inZone']}',
      `enableCOD`    = '" . (isset($_POST['enableCOD']) ? $_POST['enableCOD'] : 'no') . "'
      WHERE `id`     = '" . mc_digitSan($_GET['edit']) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      ++$c;
    }
    return $c;
  }

  public function deleteService() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "services`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "rates`
    WHERE `rService` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "tare`
    WHERE `rService` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mc_tableTruncationRoutine(array(
      'services',
      'rates',
      'tare'
    ));
    return $rows;
  }

  public function addTareRates() {
    $_POST    = mc_safeImport($_POST);
    $temp     = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '');
    $name     = (isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '');
    $services = count($_POST['rService']);
    $count    = 0;
    if ($temp && $name) {
      if (is_uploaded_file($temp)) {
        $handle = fopen($temp, 'r');
        while (($CSV = fgetcsv($handle, 10000)) !== FALSE) {
          for ($i = 0; $i < $services; $i++) {
            if ($CSV[0] != '' && $CSV[1] != '' && isset($CSV[2])) {
              mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "tare` (
              `rWeightFrom`,
              `rWeightTo`,
              `rCost`,
              `rService`
              ) VALUES (
              '" . str_replace(array(
                  '.',
                  ','
                ), array(), $CSV[0]) . "',
              '" . str_replace(array(
                  '.',
                  ','
                ), array(), $CSV[1]) . "',
              '" . ($CSV[2] ? $CSV[2] : '0.00') . "',
              '{$_POST['rService'][$i]}'
              )") or die(mc_MySQLError(__LINE__, __FILE__));
            }
          }
          ++$count;
        }
        fclose($handle);
        if (isset($temp) && file_exists($temp)) {
          @unlink($temp);
        }
      }
    } else {
      for ($i = 0; $i < $services; $i++) {
        if ($_POST['rWeightFrom'] != '' && $_POST['rWeightTo'] != '') {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "tare` (
          `rWeightFrom`,
          `rWeightTo`,
          `rCost`,
          `rService`
          ) VALUES (
          '" . str_replace(array(
              '.',
              ','
            ), array(), $_POST['rWeightFrom']) . "',
          '" . str_replace(array(
              '.',
              ','
            ), array(), $_POST['rWeightTo']) . "',
          '" . ($_POST['rCost'] ? $_POST['rCost'] : '0.00') . "',
          '{$_POST['rService'][$i]}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
      $count = 1;
    }
    return array(
      $count,
      $services
    );
  }

  public function updateTareRates() {
    $_POST = mc_safeImport($_POST);
    $c     = 0;
    if ($_POST['rWeightFrom'] != '' && $_POST['rWeightTo'] != '') {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "tare` SET
      `rWeightFrom`  = '" . str_replace(array(
          '.',
          ','
        ), array(), $_POST['rWeightFrom']) . "',
      `rWeightTo`    = '" . str_replace(array(
          '.',
          ','
        ), array(), $_POST['rWeightTo']) . "',
      `rCost`        = '" . ($_POST['rCost'] ? $_POST['rCost'] : '0.00') . "',
      `rService`     = '{$_POST['rService']}'
      WHERE `id`     = '" . mc_digitSan($_GET['edit']) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      ++$c;
    }
    return $c;
  }

  public function deleteTareRates() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "tare`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'tare'
    ));
    return $rows;
  }

  public function addRates() {
    $_POST    = mc_safeImport($_POST);
    $temp     = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '');
    $name     = (isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '');
    $services = count($_POST['rService']);
    $count    = 0;
    if ($temp && $name) {
      if (is_uploaded_file($temp)) {
        $handle = fopen($temp, 'r');
        while (($CSV = fgetcsv($handle, 10000)) !== FALSE) {
          for ($i = 0; $i < $services; $i++) {
            if ($CSV[0] != '' && $CSV[1] != '' && isset($CSV[2])) {
              mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "rates` (
              `rWeightFrom`,
              `rWeightTo`,
              `rCost`,
              `rService`
              ) VALUES (
              '" . str_replace(array(
                  '.',
                  ','
                ), array(), $CSV[0]) . "',
              '" . str_replace(array(
                  '.',
                  ','
                ), array(), $CSV[1]) . "',
              '" . ($CSV[2] ? mc_cleanInsertionPrice($CSV[2]) : '0.00') . "',
              '{$_POST['rService'][$i]}'
              )") or die(mc_MySQLError(__LINE__, __FILE__));
            }
          }
          ++$count;
        }
        fclose($handle);
        if (isset($temp) && file_exists($temp)) {
          @unlink($temp);
        }
      }
    } else {
      for ($i = 0; $i < $services; $i++) {
        if ($_POST['rWeightFrom'] != '' && $_POST['rWeightTo'] != '') {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "rates` (
          `rWeightFrom`,
          `rWeightTo`,
          `rCost`,
          `rService`
          ) VALUES (
          '" . str_replace(array(
              '.',
              ','
            ), array(), $_POST['rWeightFrom']) . "',
          '" . str_replace(array(
              '.',
              ','
            ), array(), $_POST['rWeightTo']) . "',
          '" . ($_POST['rCost'] ? mc_cleanInsertionPrice($_POST['rCost']) : '0.00') . "',
          '{$_POST['rService'][$i]}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
      $count = 1;
    }
    return array(
      $count,
      $services
    );
  }

  public function updateRates() {
    $_POST = mc_safeImport($_POST);
    $c     = 0;
    if ($_POST['rWeightFrom'] != '' && $_POST['rWeightTo'] != '') {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "rates` SET
      `rWeightFrom`  = '" . str_replace(array(
          '.',
          ','
        ), array(), $_POST['rWeightFrom']) . "',
      `rWeightTo`    = '" . str_replace(array(
          '.',
          ','
        ), array(), $_POST['rWeightTo']) . "',
      `rCost`        = '" . ($_POST['rCost'] ? mc_cleanInsertionPrice($_POST['rCost']) : '0.00') . "',
      `rService`     = '{$_POST['rService']}'
      WHERE `id`     = '" . mc_digitSan($_GET['edit']) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      ++$c;
    }
    return $c;
  }

  public function deleteRates() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "rates`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'rates'
    ));
    return $rows;
  }

  public function updateZone($temp, $name) {
    $_POST['zRate'] = mc_rateCleaner($_POST['zRate']);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "zones` SET
    `zName`      = '" . mc_safeSQL($_POST['zName']) . "',
    `zCountry`   = '{$_POST['zCountry']}',
    `zRate`      = '{$_POST['zRate']}',
    `zShipping`  = '" . (isset($_POST['zShipping']) ? $_POST['zShipping'] : 'no') . "'
    WHERE `id`   = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    if (($name && $temp) || $_POST['zones2']) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "zone_areas`
      WHERE `inZone` = '" . mc_digitSan($_GET['edit']) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      mc_tableTruncationRoutine(array(
        'zone_areas'
      ));
      shipping::batchAddZoneAreas(mc_digitSan($_GET['edit']), $temp, $name, $_POST['zones2']);
    } else {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "zone_areas` SET
      `zRate`         = '{$_POST['zRate']}',
      `zShipping`     = '" . (isset($_POST['zShipping']) ? $_POST['zShipping'] : 'no') . "'
      WHERE `inZone`  = '" . mc_digitSan($_GET['edit']) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function deleteZone() {
    // Remove zone..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "zones`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    // Remove zone areas..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "zone_areas`
    WHERE `inZone` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Remove zone rates..
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "services`
             WHERE inZone = '" . mc_digitSan($_GET['del']) . "'
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($SERVICES = mysqli_fetch_object($query)) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "rates`
      WHERE `rService` = '{$SERVICES->id}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "tare`
      WHERE `rService` = '{$SERVICES->id}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    // Remove zone services..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "services`
    WHERE `inZone` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mc_tableTruncationRoutine(array(
      'zones',
      'zone_areas',
      'services',
      'rates',
      'tare'
    ));
    return $rows;
  }

  public function addNewZone($temp, $name) {
    $F              = array_map('trim', ($temp && $name ? file($temp) : explode(mc_defineNewline(), $_POST['zones2'])));
    $_POST['zRate'] = mc_rateCleaner($_POST['zRate']);
    foreach ($_POST['zCountry'] AS $cid) {
      mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "zones` (
      `zName`,`zCountry`,`zRate`,`zShipping`
      ) VALUES (
      '" . mc_safeSQL($_POST['zName']) . "','{$cid}',
      '{$_POST['zRate']}','" . (isset($_POST['zShipping']) ? $_POST['zShipping'] : 'no') . "'
      )") or die(mc_MySQLError(__LINE__, __FILE__));
      $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
      if (($name && $temp) || $_POST['zones2']) {
        foreach ($F AS $zoneName) {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "zone_areas` (
          `inZone`,`areaName`,`zCountry`,`zRate`,`zShipping`
          ) VALUES (
          '{$id}','" . mc_safeSQL(substr($zoneName, 0, 250)) . "','{$cid}',
          '{$_POST['zRate']}','" . (isset($_POST['zShipping']) ? mc_safeSQL($_POST['zShipping']) : 'no') . "'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }
    if (isset($temp) && file_exists($temp)) {
      @unlink($temp);
    }
  }

  public function batchAddZoneAreas($zone, $temp, $name, $box) {
    $F = array_map('trim', ($temp && $name ? file($temp) : explode(mc_defineNewline(), $box)));
    foreach ($F AS $zoneName) {
      mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "zone_areas` (
      `inZone`,`areaName`,`zCountry`,`zRate`,`zShipping`
      ) VALUES (
      '{$zone}','" . mc_safeSQL(substr($zoneName, 0, 250)) . "','{$_POST['zCountry']}',
      '{$_POST['zRate']}','" . (isset($_POST['zShipping']) ? mc_safeSQL($_POST['zShipping']) : 'no') . "'
      )") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    if (isset($temp) && file_exists($temp)) {
      @unlink($temp);
    }
  }

}

?>