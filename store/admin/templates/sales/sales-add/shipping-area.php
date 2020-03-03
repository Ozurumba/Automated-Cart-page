      <?php
      if (!defined('SALE_ADD')) {
        exit;
      }
      $howManyZones = mc_rowCount('zones');
      if ($howManyZones>0) {
      ?>
      <label><?php echo $msg_viewsale26; ?>:</label>
      <select name="shipSetCountry" id="shipSetCountry" onchange="mc_reloadCountry()">
      <?php
      $firstCountry = array();
      $q_ctry   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "countries`.`id` AS `cnt_id` FROM `" . DB_PREFIX . "countries`
                  LEFT JOIN `" . DB_PREFIX . "zones`
                  ON `" . DB_PREFIX . "countries`.`id` = `" . DB_PREFIX . "zones`.`zCountry`
                  WHERE `enCountry`  = 'yes'
                  AND `zName`       != ''
                  GROUP BY `" . DB_PREFIX . "countries`.`id`
                  ORDER BY `cName`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($CTY = mysqli_fetch_object($q_ctry)) {
      $firstCountry[] = $CTY->cnt_id;
      ?>
      <option value="<?php echo $CTY->cnt_id; ?>"<?php echo ($SETTINGS->shipCountry==$CTY->cnt_id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CTY->cName); ?></option>
      <?php
      }
      ?>
      </select>

      <label style="margin-top:10px"><?php echo $msg_viewsale27; ?>:</label>
      <select name="shipSetArea" id="shipSetArea" onchange="mc_setZoneTax()">
      <?php
      $shipWithTax = 'no';
      $q_zone = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "zones`
                WHERE `zCountry` = '".($SETTINGS->shipCountry>0 ? $SETTINGS->shipCountry : (isset($firstCountry[0]) ? $firstCountry[0] : '0'))."'
                ORDER BY `zName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($Z = mysqli_fetch_object($q_zone)) {
      $q_zarea   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`areaName` FROM `" . DB_PREFIX . "zone_areas`
                   WHERE `inZone` = '{$Z->id}'
                   ORDER BY `areaName`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
      if (mysqli_num_rows($q_zone)>0) {
      ?>
      <optgroup label="<?php echo mc_safeHTML($Z->zName); ?>">
      <?php
      }
      while ($ZAREA = mysqli_fetch_object($q_zarea)) {
      ?>
      <option value="<?php echo $ZAREA->id; ?>"><?php echo mc_safeHTML($ZAREA->areaName); ?></option>
      <?php
      }
      if (mysqli_num_rows($q_zone)>0) {
      ?>
      </optgroup>
      <?php
      }
      }
      // Add option to prevent validation error on page load..
      if (mysqli_num_rows($q_zone)==0) {
      ?>
      <option value="0"></option>
      <?php
      }
      ?>
      </select>

      <label style="margin-top:10px"><?php echo $msg_viewsale28; ?>:</label>
      <select name="setShipRateID" id="setShipRateID" onchange="mc_loadShippingPrice(jQuery('#subTotal').val())">
      <optgroup label="<?php echo mc_cleanDataEntVars($msg_viewsale50); ?>">
        <option value="na"<?php echo isset($q_phys) && (mysqli_num_rows($q_phys)==0 ? ' selected="selected"' : ''); ?>><?php echo $msg_viewsale50; ?></option>
      </optgroup>
      <?php
      if ($SETTINGS->enablePickUp=='yes') {
      ?>
      <optgroup label="<?php echo mc_cleanDataEntVars($msg_javascript214); ?>">
       <option value="pickup"><?php echo $msg_javascript215; ?></option>
      </optgroup>
      <?php
      }
      $q_zone = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`zName` FROM `" . DB_PREFIX . "zones`
                WHERE `zCountry` = '".($SETTINGS->shipCountry>0 ? $SETTINGS->shipCountry : (isset($firstCountry[0]) ? $firstCountry[0] : '0'))."'
                ORDER BY `zName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($Z = mysqli_fetch_object($q_zone)) {
      //---------------------------------------------------------------------
      $q_service = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`sName` FROM `" . DB_PREFIX . "services`
                   WHERE `inZone` = '{$Z->id}'
                   ORDER BY `sName`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
      //---------------------------------------------------------------------
      $q_flat = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`rate` FROM `" . DB_PREFIX . "flat`
                WHERE `inZone` = '{$Z->id}'
                LIMIT 1
                ") or die(mc_MySQLError(__LINE__,__FILE__));
      $FLAT = mysqli_fetch_object($q_flat);
      //---------------------------------------------------------------------
      $q_prte = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`rate`,`item` FROM `" . DB_PREFIX . "per`
                WHERE `inZone` = '{$Z->id}'
                LIMIT 1
                ") or die(mc_MySQLError(__LINE__,__FILE__));
      $PER_ITEM = mysqli_fetch_object($q_prte);
      //---------------------------------------------------------------------
      $q_percent = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "percent`
                   WHERE `inZone` = '{$Z->id}'
                   ORDER BY `priceFrom`*100,`priceTo`*100
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
      //---------------------------------------------------------------------
      $q_qtyrate = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "qtyrates`
                   WHERE `inZone` = '{$Z->id}'
                   ORDER BY `qtyFrom`,`qtyTo`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
      //---------------------------------------------------------------------
      if (mysqli_num_rows($q_service)>0 || mysqli_num_rows($q_percent)>0 || mysqli_num_rows($q_qtyrate)>0 || isset($FLAT->id) || isset($PER_ITEM->id)) {
      ?>
      <optgroup label="<?php echo mc_safeHTML($Z->zName); ?>">
      <?php
      }
      //---------------------------------------------------------------------
      // Flat
      //---------------------------------------------------------------------
      if (isset($FLAT->id)) {
      ?>
      <option value="0" disabled="disabled">(&#043;) <?php echo $msg_viewsale95; ?></option>
      <option value="flat<?php echo $FLAT->id; ?>"><?php echo $msg_viewsale95.' - '.mc_currencyFormat(mc_formatPrice($FLAT->rate)); ?></option>
      <?php
      }
      //---------------------------------------------------------------------
      // PerItem Rate
      //---------------------------------------------------------------------
      if (isset($PER_ITEM->id)) {
      ?>
      <option value="0" disabled="disabled">(&#043;) <?php echo $msg_viewsale124; ?></option>
      <option value="pert<?php echo $PER_ITEM->id; ?>"><?php echo str_replace(array('{first}','{item}'),array(mc_currencyFormat(mc_formatPrice($PER_ITEM->rate)),mc_currencyFormat(mc_formatPrice($PER_ITEM->item))),$msg_viewsale125); ?></option>
      <?php
      }
      //---------------------------------------------------------------------
      // Percentage based..
      //---------------------------------------------------------------------
      if (mysqli_num_rows($q_percent)>0) {
      ?>
      <option value="0" disabled="disabled">(&#043;) <?php echo $msg_viewsale96; ?></option>
      <?php
      while ($PR = mysqli_fetch_object($q_percent)) {
      ?>
      <option value="perc<?php echo $PR->id; ?>">&nbsp;<?php echo mc_currencyFormat(mc_formatPrice($PR->priceFrom)).' - '.mc_currencyFormat(mc_formatPrice($PR->priceTo)).' ('.$PR->percentage.'%)'; ?></option>
      <?php
      }
      }
      //---------------------------------------------------------------------
      // Quantity based..
      //---------------------------------------------------------------------
      if (mysqli_num_rows($q_qtyrate)>0) {
      ?>
      <option value="0" disabled="disabled">(&#043;) <?php echo $msg_sales_view[0]; ?></option>
      <?php
      while ($QR = mysqli_fetch_object($q_qtyrate)) {
      ?>
      <option value="qtyr<?php echo $QR->id; ?>">&nbsp;<?php echo $QR->qtyFrom.' - '.$QR->qtyTo.' ('.(substr($QR->rate,-1) == '%' ? $QR->rate : mc_currencyFormat(mc_formatPrice($QR->rate))) . ')'; ?></option>
      <?php
      }
      }
      //---------------------------------------------------------------------
      // Services/rates = weight based
      //---------------------------------------------------------------------
      if (mysqli_num_rows($q_service)>0) {
      while ($S = mysqli_fetch_object($q_service)) {
      ?>
      <option value="0" disabled="disabled">(&#043;) <?php echo mc_safeHTML($S->sName); ?></option>
      <?php
      $q_rates = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "rates`
                 WHERE `rService` = '{$S->id}'
                 ORDER BY `id`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($R = mysqli_fetch_object($q_rates)) {
      // Tare weight..
      $tareCost = '';
      $tare     = mc_getTareWeight(0,$R->rService,array($R->rWeightFrom,$R->rWeightTo));
      if (isset($tare[0]) && $tare[0]=='yes') {
        switch(substr($tare[1],-1)) {
          case '%':
          $calc     = substr($tare[1],0,-1).'%';
          $tareCost = str_replace('{amount}',$calc,$msg_viewsale106);
          break;
          default:
          $tareCost = str_replace('{amount}',mc_currencyFormat(mc_formatPrice($tare[1])),$msg_viewsale106);
          break;
        }
      }
      ?>
      <option value="<?php echo $R->id; ?>">&nbsp;<?php echo $R->rWeightFrom.' - '.$R->rWeightTo.' ('.mc_currencyFormat(mc_formatPrice($R->rCost)).$tareCost; ?>)</option>
      <?php
      }
      }
      }
      if (mysqli_num_rows($q_service)>0 || mysqli_num_rows($q_percent)>0 || mysqli_num_rows($q_qtyrate)>0 || isset($FLAT->id) || isset($PER_ITEM->id)) {
      ?>
      </optgroup>
      <?php
      }
      }
      ?>
      </select>
      <?php
      }
      ?>

      <label style="margin-top:10px"><?php echo $msg_viewsale29; ?>:</label>
      <input type="text" class="box" name="cartWeight" id="cartWeight" value="0">

      <label style="margin-top:10px"><?php echo $msg_viewsale19; ?>:</label>
      <select name="paymentMethod">
      <?php
      foreach ($mcSystemPaymentMethods AS $key => $value) {
        if ($value['enable']=='yes' && !in_array($key, $noneGateway)) {
        ?>
        <option value="<?php echo $key; ?>"><?php echo $value['lang']; ?></option>
        <?php
        }
      }
      ?>
      <option value="0" disabled="disabled">- - - - - - - - -</option>
      <?php
      foreach ($mcSystemPaymentMethods AS $key => $value) {
        if ($value['enable']=='yes' && in_array($key, $noneGateway)) {
        ?>
        <option value="<?php echo $key; ?>"><?php echo $value['lang']; ?></option>
        <?php
        }
      }
      ?>
      </select>