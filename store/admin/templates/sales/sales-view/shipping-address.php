          <?php
          if (!defined('SALE_EDIT')) {
            exit;
          }
          ?>
          <label><?php echo $msg_viewsale30 . ' ' . $msg_viewsale37; ?>:</label>
          <div class="form-group input-group">
            <span class="input-group-addon"><a href="#" onclick="mc_fieldCopy('shipping');return false" title="<?php echo mc_safeHTML($msg_admin_viewsale3_0[11]); ?>"><i class="fa fa-copy fa-fw"></i></a></span>
            <input type="text" class="box addon-no-radius" name="ship_1" value="<?php echo mc_safeHTML($SALE->ship_1); ?>">
          </div>

          <label style="margin-top:10px"><?php echo $msg_viewsale30 . ' ' . $msg_viewsale39; ?>:</label>
          <input type="text" class="box" name="ship_2" value="<?php echo mc_safeHTML($SALE->ship_2); ?>">

          <label style="margin-top:10px"><?php echo $msg_viewsale30 . ' ' . $msg_viewsale99; ?>:</label>
          <input type="text" class="box" name="ship_3" value="<?php echo mc_safeHTML($SALE->ship_3); ?>">

          <label style="margin-top:10px"><?php echo $msg_viewsale30 . ' ' . $msg_viewsale100; ?>:</label>
          <input type="text" class="box" name="ship_4" value="<?php echo mc_safeHTML($SALE->ship_4); ?>">

          <label style="margin-top:10px"><?php echo $msg_viewsale30 . ' ' . $msg_viewsale101; ?>:</label>
          <input type="text" class="box" name="ship_5" value="<?php echo mc_safeHTML($SALE->ship_5); ?>">

          <label style="margin-top:10px"><?php echo $msg_viewsale30 . ' ' . $msg_viewsale102; ?>:</label>
          <input type="text" class="box" name="ship_6" value="<?php echo mc_safeHTML($SALE->ship_6); ?>">

          <label style="margin-top:10px"><?php echo $msg_viewsale30 . ' ' . $msg_viewsale103; ?>:</label>
          <input type="text" class="box" name="ship_7" value="<?php echo mc_safeHTML($SALE->ship_7); ?>">

          <label style="margin-top:10px"><?php echo $msg_viewsale30 . ' ' . $msg_viewsale40; ?>:</label>
          <input type="text" class="box" name="ship_8" value="<?php echo mc_safeHTML($SALE->ship_8); ?>">

          <?php
          // Only show shipping country in address list if not shown above..
          if ($howManyZones==0) {
          ?>
          <label style="margin-top:10px"><?php echo $msg_viewsale26; ?>:</label>
          <select name="shipSetCountry">
          <?php
          $q_ctry   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
                      WHERE `enCountry`  = 'yes'
                      ORDER BY `cName`
                      ") or die(mc_MySQLError(__LINE__,__FILE__));
          while ($CTY = mysqli_fetch_object($q_ctry)) {
          ?>
          <option value="<?php echo $CTY->id; ?>"<?php echo ($SALE->shipSetCountry==$CTY->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CTY->cName); ?></option>
          <?php
          }
          ?>
          </select>
          <?php
          }
          ?>