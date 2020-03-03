      <?php
      if (!defined('SALE_ADD')) {
        exit;
      }
      ?>
      <div class="formFieldWrapper">
        <label><?php echo $msg_sales45; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="text" class="box addon-no-radius" name="subTotal" id="subTotal" value="0.00">
        </div>

        <label style="margin-top:10px"><?php echo $msg_addsale9; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">-</span>
         <input type="text" class="box addon-no-radius" name="globalTotal" id="globalTotal" value="0.00">
        </div>

        <label style="margin-top:10px"><?php echo $msg_viewsale30; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="text" class="box addon-no-radius" name="shipTotal" id="shipTotal" value="0.00">
        </div>

        <label style="margin-top:10px"><?php echo str_replace('{percentage}',0,$msg_viewsale23); ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="hidden" name="taxRate" id="taxRate" value="0"><input type="text" class="box addon-no-radius" name="taxPaid" id="taxPaid" value="0.00">
        </div>

        <label style="margin-top:10px"><?php echo $msg_viewsale97; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="text" class="box addon-no-radius" name="insuranceTotal" id="insuranceTotal" value="0.00">
        </div>

        <label style="margin-top:10px"><?php echo $msg_sales_view[3]; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="text" class="box addon-no-radius" name="chargeTotal" id="chargeTotal" value="">
        </div>
      </div>

      <div class="formFieldWrapper">
        <label><b><?php echo $msg_viewsale33; ?></b>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">=</span>
         <input type="text" class="box addon-no-radius-both" name="grandTotal" id="grandTotal" value="0.00">
         <span class="input-group-addon"><a href="#" onclick="mc_recalculateManualTotals();return false" title="<?php echo mc_cleanDataEntVars($msg_viewsale48); ?>"><i class="fa fa-refresh fa-fw"></i></a></span>
        </div>
      </div>