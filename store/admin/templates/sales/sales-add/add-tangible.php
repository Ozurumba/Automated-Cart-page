      <?php
      if (!defined('SALE_ADD')) {
        exit;
      }
      if (!empty($_SESSION['add-phys-'.mc_encrypt(SECRET_KEY)])) {
        ++$salesToAdd;
        ?>
        <div class="addmoreproducts"><a href="?p=add-manual&amp;type=physical"><i class="fa fa-plus fa-fw"></i></a></div>
        <?php
        foreach ($_SESSION['add-phys-'.mc_encrypt(SECRET_KEY)] AS $p) {
          $split    = explode('-', $p);
          $q_phys   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "products`
                      WHERE `id` = '" . (int) $split[0] . "'
                      ") or die(mc_MySQLError(__LINE__,__FILE__));
          $PHYS     = mysqli_fetch_object($q_phys);
          $details  = '';
          $code     = $PHYS->pCode;
          $weight   = $PHYS->pWeight;
          $price    = $PHYS->pPrice;
          $offer    = $PHYS->pOffer;
          $hidden   = '<input type="hidden" name="pd[]" value="'.$split[0].'-0-'.$split[2].'">
                       <input type="hidden" name="prod_id[]" value="'.$PHYS->id.'">
                       '.mc_defineNewline();
          $img          = mc_storeProductImg($PHYS->id,$PHYS);
          $hasAttribs   = mc_rowCount('attributes WHERE `productID` = \''.$PHYS->id.'\'');
          ?>
          <div class="panel panel-default salepurchaseproduct" id="purchase_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>">
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
                      echo str_replace(array('{code}','{url}'),array($code,'../?pd='.$PHYS->id),$msg_viewsale16);
                      echo str_replace(array('{code}','{url}'),array($code,'?p=add-product&amp;edit='.$PHYS->id),$msg_viewsale93);
                      ?>
                      </div>
                      <?php
                    }
                    ?>
                    </td>
                    <td>
                    <?php echo mc_safeHTML($PHYS->pName).($details ? ' <span class="highlight">('.$details.')</span>' : ''); ?>
                    <?php
                    // Has this product got attributes?
                    echo ($code ? '<br><br>' . $code : '');
                    if ($hasAttribs > 0) {
                    ?>
                    <div class="attrSaleBoxes" id="prodAttrArea_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>">
                    <hr>
                    <div id="alinks_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>"><a href="#" onclick="mc_hideAttr('<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>');return false" title="<?php echo mc_cleanDataEntVars($msg_viewsale110); ?>"><i class="fa fa-plus fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo mc_cleanDataEntVars($msg_viewsale110); ?></span></a><?php echo (SALE_EDIT_PRODUCT_ATTRIBUTE_EDIT ? '&nbsp;&nbsp;<a href="?p=product-attributes&amp;product=' . $PHYS->id . '" onclick="window.open(this);return false" title="' . mc_cleanDataEntVars($msg_viewsale111) . '"><i class="fa fa-pencil fa-fw"></i><span class="hidden-sm hidden-xs"> ' . mc_cleanDataEntVars($msg_viewsale111) . '</span></a>' : ''); ?></div>

                    <?php
                    // Possible attributes..
                    ?>
                    <div class="add" id="attsel_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>" style="display:none">
                    <?php
                    $q_attc = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "attributes`.`id` AS `attrID` FROM `" . DB_PREFIX . "attributes`
                              LEFT JOIN `" . DB_PREFIX . "attr_groups`
                              ON `" . DB_PREFIX . "attributes`.`attrGroup`    = `" . DB_PREFIX . "attr_groups`.`id`
                              WHERE `" . DB_PREFIX . "attributes`.`productID` = '{$PHYS->id}'
                              AND `" . DB_PREFIX . "attributes`.`attrStock`   > 0
                              ORDER BY `" . DB_PREFIX . "attr_groups`.`groupName`
                              ") or die(mc_MySQLError(__LINE__,__FILE__));
                    if (mysqli_num_rows($q_attc)>0) {
                    ?>
                    <select name="attsel" id="s_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>" onchange="mc_addAttributeToSale(this.value,'<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>','<?php echo $PHYS->id; ?>','yes')">
                    <option value="0"><?php echo $msg_viewsale112; ?></option>
                    <?php
                    while ($ATTSEL = mysqli_fetch_object($q_attc)) {
                    ?>
                    <option value="<?php echo $ATTSEL->attrID; ?>" id="sel_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>_<?php echo $ATTSEL->attrID; ?>"><?php echo mc_cleanData($ATTSEL->groupName.' - '.$ATTSEL->attrName); ?></option>
                    <?php
                    }
                    ?>
                    <option value="0">- - - - - -</option>
                    <option value="close"><?php echo $msg_admin_viewsale3_0[5]; ?></option>
                    </select>
                    <?php
                    }
                    ?>
                    </div>
                    </div>
                    <?php
                    }

                    // Personalisation
                    $q_ps1 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "personalisation`
                             WHERE `productID` = '{$PHYS->id}'
                             AND `enabled`     = 'yes'
                             ORDER BY `id`
                             ") or die(mc_MySQLError(__LINE__,__FILE__));
                    if (mysqli_num_rows($q_ps1)>0) {
                    ?>
                    <div class="personalisation" id="pWrapper_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>">
                    <hr>
                    <div class="table-responsive">
                    <table class="table">
                    <tbody>
                    <?php
                    $iBoxes = 0;
                    while ($PS = mysqli_fetch_object($q_ps1)) {
                    ++$iBoxes;
                    $cost         = $PS->persAddCost;
                    $PS->boxType  = ($PS->persOptions ? 'select' : $PS->boxType);
                    // Input boxes for new items only..
                    $inputBoxes = '<input type="hidden" name="product[\''.$p.'\'][]" value="'.$PHYS->id.'">';
                    switch($PS->boxType) {
                     case 'input':
                       ?>
                       <tr>
                         <td colspan="2" class="perHead"><label><?php echo mc_persTextDisplay(mc_safeHTML($PS->persInstructions),true); ?></label></td>
                       </tr>
                       <tr>
                         <td><?php echo $inputBoxes; ?><input type="hidden" name="persnew[<?php echo $p; ?>][]" value="<?php echo $PS->id; ?>"><input id="ibox_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>_<?php echo $iBoxes; ?>" class="box" type="text" name="pnvalue[<?php echo $p; ?>][]" value=""></td>
                         <td><input type="text" class="box" name="pers_cost[<?php echo $p; ?>][]" value="<?php echo $cost; ?>"></td>
                       </tr>
                       <?php
                       break;
                     case 'textarea':
                       ?>
                       <tr>
                         <td colspan="2" class="perHead"><label><?php echo mc_persTextDisplay(mc_safeHTML($PS->persInstructions),true); ?></label></td>
                       </tr>
                       <tr>
                         <td><?php echo $inputBoxes; ?><input type="hidden" name="persnew[<?php echo $p; ?>][]" value="<?php echo $PS->id; ?>"><textarea id="ibox_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>_<?php echo $iBoxes; ?>" name="pnvalue[<?php echo $p; ?>][]" rows="5" cols="20"></textarea></td>
                         <td><input type="text" class="box" name="pers_cost[<?php echo $p; ?>][]" value="<?php echo $cost; ?>"></td>
                       </tr>
                       <?php
                       break;
                     case 'select':
                       ?>
                       <tr>
                         <td colspan="2" class="perHead"><label><?php echo mc_persTextDisplay(mc_safeHTML($PS->persInstructions),true); ?></label></td>
                       </tr>
                       <tr>
                         <td>
                          <?php echo $inputBoxes; ?><input type="hidden" name="persnew[<?php echo $p; ?>][]" value="<?php echo $PS->id; ?>">
                          <select name="pnvalue[<?php echo $p; ?>][]" id="ibox_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>_<?php echo $iBoxes; ?>">
                          <option value="no-option-selected">- - - - -</option>
                          <?php
                          $OPT = explode('||',$PS->persOptions);
                          foreach ($OPT AS $o) {
                          ?>
                          <option value="<?php echo $o; ?>"><?php echo mc_cleanData($o); ?></option>
                          <?php
                          }
                          ?>
                          </select>
                         </td>
                         <td><input type="text" class="box" name="pers_cost[<?php echo $p; ?>][]" value="<?php echo $cost; ?>"></td>
                       </tr>
                       <?php
                       break;
                    }
                    }
                    ?>
                    </tbody>
                    </table>
                    </div>
                    </div>
                    <?php
                    }
                    ?>
                    </td>
                    <td>
                      <select name="qty[]" id="qty_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>" onchange="if(jQuery('#qty_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>').val()=='0') {mc_alertBox('<?php echo mc_cleanDataEntVars($msg_javascript220); ?>');mc_MarkForDeletion('<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>','no','add');}">
                      <?php
                      foreach (range(0,(SALE_QTY_LIMIT > $PHYS->pStock ? $PHYS->pStock : SALE_QTY_LIMIT)) AS $qty) {
                      ?>
                      <option value="<?php echo $qty; ?>"<?php echo ($qty=='1' ? ' selected="selected"' : ''); ?>><?php echo $qty; ?></option>
                      <?php
                      }
                      ?>
                      </select>
                    </td>
                    <td>
                     <input class="box" id="price_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>" type="text" name="price[]" value="<?php echo ($offer>0 ? mc_formatPrice($offer) : mc_formatPrice($price)); ?>">
                     <?php
                     if ($hasAttribs>0) {
                     ?>
                     <div style="margin-top:20px">
                       <label><?php echo $msg_viewsale88; ?>:</label>
                       <input type="hidden" name="attrPrice['<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>']" id="attrh_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>" value="0.00"><input readonly="readonly" class="box" id="attr_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>" type="text" name="attrPrice2[]" value="0.00">
                     </div>
                     <?php
                     } else {
                     ?>
                     <input type="hidden" name="attrPrice['<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>']" value="0.00">
                     <?php
                     }
                     if (mysqli_num_rows($q_ps1)>0) {
                     ?>
                     <div style="margin-top:20px">
                       <label><?php echo $msg_viewsale87; ?>:</label>
                       <input type="hidden" name="persPrice['<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>']" id="persh_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>" value=""><input class="box" id="pers_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>" readonly="readonly" type="text" name="persPrice2['<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>']" value="0.00">
                     </div>
                     <?php
                     } else {
                     ?>
                     <input type="hidden" name="persPrice['<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>']" value="0.00">
                     <?php
                     }
                     ?>
                    </td>
                  </tr>
                </tbody>
                </table>
              </div>
            </div>
            <div class="panel-footer">
             <span class="highlight" id="highlight_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>"><?php echo mc_currencyFormat(($offer>0 ? mc_formatPrice($offer) : mc_formatPrice($price))); ?></span> &nbsp;&nbsp;<i class="fa fa-calculator fa-fw mc_cursor_pointer" title="<?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[6]); ?>" onclick="mc_displayPurchaseProductPrices('<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>','sales-add')"></i>
             <input type="hidden" id="total_price_<?php echo $PHYS->id.'-'.$split[1].'-'.$split[2]; ?>" name="t_price[]" value="<?php echo ($offer>0 ? mc_formatPrice($offer) : mc_formatPrice($price)); ?>">
            </div>
          </div>
          <?php
        }
      } else {
      ?>
      <div class="no-product-area">
        <?php echo str_replace(array('{url}','{type}'), array('?p=add-manual&amp;type=tangible', $msg_viewsale2), $msg_addsale3); ?>
      </div>
      <?php
      }
      ?>