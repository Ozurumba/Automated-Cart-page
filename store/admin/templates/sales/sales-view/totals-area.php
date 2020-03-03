      <?php
      if (!defined('SALE_EDIT')) {
        exit;
      }
      ?>
      <div class="formFieldWrapper">
        <label><?php echo $msg_viewsale31; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="text" class="box addon-no-radius" name="subTotal" id="subTotal" value="<?php echo $SALE->subTotal; ?>">
        </div>
        <?php
        // Global discount..
        if ($SALE->globalTotal>0) {
        ?>
        <label style="margin-top:10px"><?php echo str_replace('{percentage}',$SALE->globalDiscount,($SALE->type == 'trade' ? $msg_admin_viewsale3_0[20] : $msg_viewsale75)); ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">-</span>
         <input type="hidden" class="box" name="globalDiscount" value="<?php echo $SALE->globalDiscount; ?>"><input type="text" class="box addon-no-radius" name="globalTotal" id="globalTotal" value="<?php echo ($SALE->globalTotal>0 ? $SALE->globalTotal : '0.00'); ?>">
        </div>
        <?php
        } elseif ($SALE->couponTotal>0) {
          switch($SALE->codeType) {
            case 'gift':
              ?>
              <label style="margin-top:10px"><?php echo $msg_viewsale116; ?>:</label>
              <div class="form-group input-group">
               <span class="input-group-addon">-</span>
               <input type="text" class="box addon-no-radius" name="couponTotal" id="couponTotal" value="<?php echo ($SALE->couponTotal>0 ? $SALE->couponTotal : '0.00'); ?>">
              </div>
              <?php
              break;
            default:
              ?>
              <label style="margin-top:10px"><?php echo $msg_viewsale20; ?>:</label>
              <div class="form-group input-group">
               <span class="input-group-addon">-</span>
               <input type="text" class="box addon-no-radius" name="couponTotal" id="couponTotal" value="<?php echo ($SALE->couponTotal>0 ? $SALE->couponTotal : '0.00'); ?>">
              </div>
              <?php
              break;
          }
        } else {
        ?>

        <label style="margin-top:10px"><?php echo $msg_viewsale78; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">-</span>
         <input type="text" class="box addon-no-radius" name="manualDiscount" id="manualDiscount" value="<?php echo ($SALE->manualDiscount>0 ? $SALE->manualDiscount : '0.00'); ?>">
        </div>
        <?php
        }
        if (isset($shipWithTax) && $shipWithTax=='yes') {
        ?>
        <label style="margin-top:10px"><?php echo $msg_viewsale30; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="text" class="box addon-no-radius" name="shipTotal" id="shipTotal" value="<?php echo ($SALE->shipTotal>0 ? $SALE->shipTotal : '0.00'); ?>">
        </div>

        <label style="margin-top:10px"><?php echo str_replace('{percentage}',$SALE->taxRate,$msg_viewsale23); ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="hidden" name="taxRate" id="taxRate" value="<?php echo $SALE->taxRate; ?>"><input type="text" class="box addon-no-radius" name="taxPaid" id="taxPaid" value="<?php echo ($SALE->taxPaid>0 ? $SALE->taxPaid : '0.00'); ?>">
        </div>

        <?php
        } else {
        ?>
        <label style="margin-top:10px"><?php echo str_replace('{percentage}',$SALE->taxRate,$msg_viewsale23); ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="hidden" name="taxRate" id="taxRate" value="<?php echo $SALE->taxRate; ?>"><input type="text" class="box addon-no-radius" name="taxPaid" id="taxPaid" value="<?php echo ($SALE->taxPaid>0 ? $SALE->taxPaid : '0.00'); ?>">
        </div>

        <label style="margin-top:10px"><?php echo $msg_viewsale30; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="text" class="box addon-no-radius" name="shipTotal" id="shipTotal" value="<?php echo ($SALE->shipTotal>0 ? $SALE->shipTotal : '0.00'); ?>">
        </div>

        <?php
        }
        ?>
        <label style="margin-top:10px"><?php echo $msg_viewsale97; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="text" class="box addon-no-radius" name="insuranceTotal" id="insuranceTotal" value="<?php echo ($SALE->insuranceTotal>0 ? $SALE->insuranceTotal : '0.00'); ?>">
        </div>

        <label style="margin-top:10px"><?php echo $msg_sales_view[3]; ?>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">+</span>
         <input type="text" class="box addon-no-radius" name="chargeTotal" id="chargeTotal" value="<?php echo ($SALE->chargeTotal>0 ? $SALE->chargeTotal : '0.00'); ?>">
        </div>

      </div>

      <div class="formFieldWrapper">

        <label><b><?php echo $msg_viewsale33; ?></b>:</label>
        <div class="form-group input-group">
         <span class="input-group-addon">=</span>
         <input type="text" class="box addon-no-radius-both" name="grandTotal" id="grandTotal" value="<?php echo $SALE->grandTotal; ?>">
         <span class="input-group-addon"><a href="#" onclick="mc_recalculateTotals('<?php echo mc_digitSan($_GET['sale']); ?>');return false" title="<?php echo mc_cleanDataEntVars($msg_viewsale48); ?>"><i class="fa fa-refresh fa-fw"></i></a></span>
        </div>

      </div>