      <?php
      if (!defined('SALE_EDIT')) {
        exit;
      }
      $q_gift = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "giftcerts`.`id` AS `pid`,`" . DB_PREFIX . "purchases`.`id` AS `pcid` FROM `" . DB_PREFIX . "purchases`
                LEFT JOIN `" . DB_PREFIX . "giftcerts`
                ON `" . DB_PREFIX . "purchases`.`giftID` = `" . DB_PREFIX . "giftcerts`.`id`
                WHERE `saleID`                       = '" . mc_digitSan($_GET['sale']) . "'
                AND `productType`                    = 'virtual'
                ".($SALE->saleConfirmation=='no' ? '' : 'AND `saleConfirmation` = \'yes\'')."
                ORDER BY `" . DB_PREFIX . "purchases`.`id`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($GIFT = mysqli_fetch_object($q_gift)) {
        $details      = '';
        $code         = '';
        $weight       = 'N/A';
        $GIFT->pName  = ($GIFT->name ? $GIFT->name : $GIFT->deletedProductName);
        $isDel2       = ($GIFT->deletedProductName ? '<span class="deletedItem">'.$msg_script53.'</span>' : '');
        $img          = mc_storeProductImg($GIFT->pid,$GIFT,true,(isset($GIFT->image) ? $GIFT->image : ''));
        ?>
        <div class="panel panel-default salepurchaseproduct" id="purchase_<?php echo $GIFT->pcid; ?>">
          <input type="hidden" name="mcp_type[]" value="gift">
          <input type="hidden" name="pid[]" value="<?php echo $GIFT->pcid; ?>">
          <input type="hidden" name="prod_id[]" value="<?php echo $GIFT->pid; ?>">
          <input type="hidden" name="gift_id[]" value="<?php echo $GIFT->giftID; ?>">
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table">
              <tbody>
                <tr>
                  <td>
                  <?php echo $img;
                  if (SALE_EDIT_PRODUCT_CONTROLS && $isDel2 == '') {
                    ?>
                    <div class="editviewcontrols">
                    <?php
                    echo str_replace(array('{code}','{url}'),array($code,'../?p=gift'),$msg_viewsale16);
                    echo str_replace(array('{code}','{url}'),array($code,'?p=gift&amp;edit='.$GIFT->pid),$msg_viewsale93);
                    ?>
                    </div>
                    <?php
                  }
                  ?>
                  </td>
                  <td>
                  <?php
                  echo '<span class="bold">' . mc_safeHTML($GIFT->pName). '</span>' . ($details ? ' <span class="highlight">('.$details.')</span>' : '').$isDel2;
                  // Has this product got attributes?
                  echo ($code ? '<br><br>' . $code : '');
                  ?>
                  </td>
                  <td>
                    <select name="qty[]" id="qty_<?php echo $GIFT->pcid; ?>" onchange="if(jQuery('#qty_<?php echo $GIFT->pcid; ?>').val()=='0') {mc_alertBox('<?php echo mc_cleanDataEntVars($msg_javascript220); ?>');mc_MarkForDeletion('<?php echo $GIFT->pcid; ?>','no','view');}">
                    <?php
                    foreach (range(0,SALE_QTY_LIMIT) AS $qty) {
                    ?>
                    <option value="<?php echo $qty; ?>"<?php echo ($GIFT->productQty==$qty ? ' selected="selected"' : ''); ?>><?php echo $qty; ?></option>
                    <?php
                    }
                    ?>
                  </td>
                  <td>
                  <input class="box" id="price_<?php echo $GIFT->pcid; ?>" type="text" name="price[]" value="<?php echo mc_formatPrice($GIFT->salePrice); ?>">
                  </td>
                </tr>
              </tbody>
              </table>
            </div>
          </div>
          <div class="panel-footer">
            <span class="highlight" id="highlight_<?php echo $GIFT->pcid; ?>"><?php echo mc_currencyFormat(mc_formatPrice(($GIFT->productQty*$GIFT->salePrice)+($GIFT->persPrice+$GIFT->attrPrice),true)); ?></span>&nbsp;&nbsp;<i class="fa fa-calculator fa-fw mc_cursor_pointer" title="<?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[6]); ?>" onclick="mc_displayPurchaseProductPrices('<?php echo $GIFT->pcid; ?>','sales-view')"></i>
            <input type="hidden" id="total_price_<?php echo $GIFT->pcid; ?>" name="t_price[]" value="<?php echo mc_formatPrice(($GIFT->salePrice + $GIFT->persPrice + $GIFT->attrPrice) * $GIFT->productQty); ?>">
          </div>
        </div>
        <?php
      }
      ?>