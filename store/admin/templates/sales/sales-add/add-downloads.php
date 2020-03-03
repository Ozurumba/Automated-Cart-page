      <?php
      if (!defined('SALE_ADD')) {
        exit;
      }
      if (!empty($_SESSION['add-down-'.mc_encrypt(SECRET_KEY)])) {
        ++$salesToAdd;
        ?>
        <div class="addmoreproducts"><a href="?p=add-manual&amp;type=download"><i class="fa fa-plus fa-fw"></i></a></div>
        <?php
        foreach ($_SESSION['add-down-'.mc_encrypt(SECRET_KEY)] AS $d) {
          $split    = explode('-', $d);
          $q_down   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "products`
                      WHERE `id` = '" . (int) $split[0] . "'
                      ") or die(mc_MySQLError(__LINE__,__FILE__));
          $DOWN     = mysqli_fetch_object($q_down);
          $details  = '';
          $code     = $DOWN->pCode;
          $weight   = $DOWN->pWeight;
          $price    = $DOWN->pPrice;
          $offer    = $DOWN->pOffer;
          $hidden   = '<input type="hidden" name="pd[]" value="'.$split[0].'-0-'.$split[2].'">
                       <input type="hidden" name="prod_id[]" value="'.$DOWN->id.'">'.mc_defineNewline();
          $img      = mc_storeProductImg($DOWN->id,$DOWN);
          ?>
          <div class="panel panel-default salepurchaseproduct" id="purchase_<?php echo $DOWN->id.'-'.$split[1].'-'.$split[2]; ?>">
            <?php
            echo $hidden;
            ?>
            <div class="panel-body">
              <div class="table-responsive">
                <table class="table">
                <tbody>
                  <tr>
                    <td>
                    <?php echo $img;
                    if (SALE_EDIT_PRODUCT_CONTROLS && $isDel == '') {
                      ?>
                      <div class="editviewcontrols">
                      <?php
                      echo str_replace(array('{code}','{url}'),array($code,'../?pd='.$DOWN->id),$msg_viewsale16);
                      echo str_replace(array('{code}','{url}'),array($code,'?p=add-product&amp;edit='.$DOWN->id),$msg_viewsale93);
                      ?>
                      </div>
                      <?php
                    }
                    ?>
                    </td>
                    <td>
                    <?php
                    echo '<span class="bold">' . mc_safeHTML($DOWN->pName). '</span>' . ($details ? ' <span class="highlight">('.$details.')</span>' : '');
                    // Has this product got attributes?
                    echo ($code ? '<br><br>' . $code : '');
                    ?>
                    </td>
                    <td>
                      <select name="qty[]" id="qty_<?php echo $DOWN->id.'-'.$split[1].'-'.$split[2]; ?>" onchange="if(jQuery('#qty_<?php echo $DOWN->id.'-'.$split[1].'-'.$split[2]; ?>').val()=='0') {mc_alertBox('<?php echo mc_cleanDataEntVars($msg_javascript220); ?>');mc_MarkForDeletion('<?php echo $DOWN->id.'-'.$split[1].'-'.$split[2]; ?>','no','add');}">
                      <?php
                      foreach (range(0,SALE_QTY_LIMIT) AS $qty) {
                      ?>
                      <option value="<?php echo $qty; ?>"<?php echo ($qty=='1' ? ' selected="selected"' : ''); ?>><?php echo $qty; ?></option>
                      <?php
                      }
                      ?>
                      </select>
                    </td>
                    <td>
                      <input class="box" id="price_<?php echo $DOWN->id.'-'.$split[1].'-'.$split[2]; ?>" type="text" name="price[]" value="<?php echo ($offer>0 ? mc_formatPrice($offer) : mc_formatPrice($price)); ?>">
                    </td>
                  </tr>
                </tbody>
                </table>
              </div>
            </div>
            <div class="panel-footer">
             <span class="highlight" id="highlight_<?php echo $DOWN->id.'-'.$split[1].'-'.$split[2]; ?>"><?php echo mc_currencyFormat(($offer>0 ? mc_formatPrice($offer) : mc_formatPrice($price))); ?></span> &nbsp;&nbsp;<i class="fa fa-calculator fa-fw mc_cursor_pointer" title="<?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[6]); ?>" onclick="mc_displayPurchaseProductPrices('<?php echo $DOWN->id.'-'.$split[1].'-'.$split[2]; ?>','sales-add')"></i>
             <input type="hidden" id="total_price_<?php echo $DOWN->id.'-'.$split[1].'-'.$split[2]; ?>" name="t_price[]" value="<?php echo ($offer>0 ? mc_formatPrice($offer) : mc_formatPrice($price)); ?>">
            </div>
          </div>
          <?php
        }
      } else {
      ?>
      <div class="no-product-area">
        <?php echo str_replace(array('{url}','{type}'), array('?p=add-manual&amp;type=download', $msg_viewsale3), $msg_addsale3); ?>
      </div>
      <?php
      }
      ?>