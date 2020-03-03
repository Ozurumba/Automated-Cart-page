          <?php
          if (!defined('SALE_ADD')) {
            exit;
          }
          ?>
          <label><?php echo $msg_viewsale42; ?>:</label>
          <input type="text" class="box" name="ipAddress" value="">

          <label style="margin-top:10px"><?php echo $msg_viewsale43; ?>:</label>
          <input type="text" class="box" name="purchaseDate" id="pDate" value="<?php echo mc_convertMySQLDate(date('Y-m-d'), $SETTINGS); ?>">

          <label style="margin-top:10px"><?php echo $msg_viewsale91; ?>:</label>
          <input type="text" class="box" name="purchaseTime" value="<?php echo date('H:i:s'); ?>" maxlength="8">

          <label style="margin-top:10px"><?php echo $msg_viewsale51; ?>:</label>
          <input type="text" class="box" name="gatewayID" value="">

          <label style="margin-top:10px"><?php echo $msg_viewsale65; ?>:</label>
          <div class="form-group input-group">
           <span class="input-group-addon"><a href="#" onclick="mc_nextInvoice();return false" title="<?php echo mc_cleanDataEntVars($msg_sales43); ?>"><i class="fa fa-refresh fa-fw"></i></a></span>
           <input type="text" class="box addon-no-radius" name="invoiceNo" id="invoiceNo" value="">
          </div>