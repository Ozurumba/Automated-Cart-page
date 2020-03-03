      <?php
      if (!defined('SALE_EDIT')) {
        exit;
      }
      $q_down = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,`" . DB_PREFIX . "purchases`.`id` AS `pcid` FROM `" . DB_PREFIX . "purchases`
                LEFT JOIN `" . DB_PREFIX . "products`
                ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
                WHERE `saleID`                        = '" . mc_digitSan($_GET['sale']) . "'
                AND `productType`                     = 'download'
                ".($SALE->saleConfirmation=='no' ? '' : 'AND `saleConfirmation` = \'yes\'')."
                ORDER BY `" . DB_PREFIX . "purchases`.`id`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($DOWN = mysqli_fetch_object($q_down)) {
        $details      = '';
        $code         = ($DOWN->pCode ? $DOWN->pCode : '');
        $weight       = ($DOWN->pWeight ? $DOWN->pWeight : 'N/A');
        $DOWN->pName  = ($DOWN->pName ? $DOWN->pName : $DOWN->deletedProductName);
        $isDel2       = ($DOWN->deletedProductName ? '<span class="deletedItem">'.$msg_script53.'</span>' : '');
        $img          = mc_storeProductImg($DOWN->pid,$DOWN);
        ?>
        <div class="panel panel-default salepurchaseproduct" id="purchase_<?php echo $DOWN->pcid; ?>">
          <input type="hidden" name="mcp_type[]" value="down">
          <input type="hidden" name="pid[]" value="<?php echo $DOWN->pcid; ?>">
          <input type="hidden" name="prod_id[]" value="<?php echo $DOWN->pid; ?>">
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
                    echo str_replace(array('{code}','{url}'),array($code,'../?pd='.$DOWN->pid),$msg_viewsale16);
                    echo str_replace(array('{code}','{url}'),array($code,'?p=add-product&amp;edit='.$DOWN->pid),$msg_viewsale93);
                    ?>
                    </div>
                    <?php
                  }
                  ?>
                  </td>
                  <td>
                  <?php
                  echo '<span class="bold">' . mc_safeHTML($DOWN->pName). '</span>' . ($details ? ' <span class="highlight">('.$details.')</span>' : '').$isDel2;
                  // Has this product got attributes?
                  echo ($code ? '<br><br>' . $code : '');
                  ?>
                  </td>
                  <td>
                    <select name="qty[]" id="qty_<?php echo $DOWN->pcid; ?>" onchange="if(jQuery('#qty_<?php echo $DOWN->pcid; ?>').val()=='0') {mc_alertBox('<?php echo mc_cleanDataEntVars($msg_javascript220); ?>');mc_MarkForDeletion('<?php echo $DOWN->pcid; ?>','no','view');}">
                    <?php
                    foreach (range(0,SALE_QTY_LIMIT) AS $qty) {
                    ?>
                    <option value="<?php echo $qty; ?>"<?php echo ($DOWN->productQty==$qty ? ' selected="selected"' : ''); ?>><?php echo $qty; ?></option>
                    <?php
                    }
                    ?>
                    </select>
                  </td>
                  <td>
                    <input class="box" id="price_<?php echo $DOWN->pcid; ?>" type="text" name="price[]" value="<?php echo mc_formatPrice($DOWN->salePrice); ?>">
                  </td>
                </tr>
              </tbody>
              </table>
            </div>
          </div>
          <div class="panel-footer">
            <span class="highlight" id="highlight_<?php echo $DOWN->pcid; ?>"><?php echo mc_currencyFormat(mc_formatPrice(($DOWN->productQty*$DOWN->salePrice)+($DOWN->persPrice+$DOWN->attrPrice),true)); ?></span> &nbsp;&nbsp;<i class="fa fa-calculator fa-fw mc_cursor_pointer" title="<?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[6]); ?>" onclick="mc_displayPurchaseProductPrices('<?php echo $DOWN->pcid; ?>','sales-view')"></i>
            <input type="hidden" id="total_price_<?php echo $DOWN->pcid; ?>" name="t_price[]" value="<?php echo mc_formatPrice(($DOWN->salePrice + $DOWN->persPrice + $DOWN->attrPrice) * $DOWN->productQty); ?>">
          </div>
        </div>
        <?php
      }
      ?>