          <?php
          if (!defined('SALE_EDIT')) {
            exit;
          }
          ?>
          <label><?php echo $msg_viewsale42; ?>:</label>
          <input type="text" class="box" name="ipAddress" value="<?php echo mc_safeHTML($SALE->ipAddress); ?>">

          <label style="margin-top:10px"><?php echo $msg_viewsale43; ?>:</label>
          <input type="text" class="box" name="purchaseDate" id="pDate" value="<?php echo ($SALE->purchaseDate!='0000-00-00' ? mc_convertMySQLDate($SALE->purchaseDate, $SETTINGS) : ''); ?>">

          <label style="margin-top:10px"><?php echo $msg_viewsale91; ?>:</label>
          <input type="text" class="box" name="purchaseTime" value="<?php echo $SALE->purchaseTime; ?>" maxlength="8">

          <label style="margin-top:10px"><?php echo $msg_viewsale51; ?>:</label>
          <input type="text" class="box" name="gatewayID" value="<?php echo mc_safeHTML($SALE->gatewayID); ?>">

          <label style="margin-top:10px"><?php echo $msg_admin_viewsale3_0[21]; ?>:</label>
          <input type="text" class="box" name="trackcode" value="<?php echo mc_safeHTML($SALE->trackcode); ?>" maxlength="100">

          <label style="margin-top:10px"><?php echo $mc_37_admin_sales[0]; ?>:</label>
          <input type="text" class="box" name="affcode" value="<?php echo mc_safeHTML($SALE->affcode); ?>" maxlength="200">

          <label style="margin-top:10px"><?php echo $msg_viewsale65; ?></label>
          <div class="form-group input-group">
           <span class="input-group-addon"><a href="#" onclick="mc_nextInvoice();return false" title="<?php echo mc_cleanDataEntVars($msg_sales43); ?>"><i class="fa fa-refresh fa-fw"></i></a></span>
           <input type="text" class="box addon-no-radius" name="invoiceNo" id="invoiceNo" value="<?php echo mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS); ?>">
          </div>