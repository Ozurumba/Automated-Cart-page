      <?php
      if (!defined('SALE_EDIT')) {
        exit;
      }
      $q_phys = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,`" . DB_PREFIX . "purchases`.`id` AS `pcid` FROM `" . DB_PREFIX . "purchases`
                LEFT JOIN `" . DB_PREFIX . "products`
                ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
                WHERE `saleID`                        = '" . mc_digitSan($_GET['sale']) . "'
                AND `productType`                     = 'physical'
                ".($SALE->saleConfirmation == 'no' ? '' : 'AND `saleConfirmation` = \'yes\'')."
                ORDER BY `" . DB_PREFIX . "purchases`.`id`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($PHYS = mysqli_fetch_object($q_phys)) {
        $details      = '';
        $code         = ($PHYS->pCode ? $PHYS->pCode : '');
        $weight       = ($PHYS->pWeight ? $PHYS->pWeight : 'N/A');
        $PHYS->pName  = ($PHYS->pName ? $PHYS->pName : $PHYS->deletedProductName);
        $isDel        = ($PHYS->deletedProductName ? '<span class="deletedItem">'.$msg_script53.'</span>' : '');
        $img          = mc_storeProductImg($PHYS->pid,$PHYS);
        $hasAttribs   = mc_rowCount('attributes WHERE `productID` = \''.$PHYS->pid.'\'');
        ?>
        <div class="panel panel-default salepurchaseproduct" id="purchase_<?php echo $PHYS->pcid; ?>">
          <input type="hidden" name="mcp_type[]" value="tang">
          <input type="hidden" name="pid[]" value="<?php echo $PHYS->pcid; ?>">
          <input type="hidden" name="prod_id[]" value="<?php echo $PHYS->pid; ?>">
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
                    echo str_replace(array('{code}','{url}'),array($code,'../?pd='.$PHYS->pid),$msg_viewsale16);
                    echo str_replace(array('{code}','{url}'),array($code,'?p=add-product&amp;edit='.$PHYS->pid),$msg_viewsale93);
                    ?>
                    </div>
                    <?php
                  }
                  ?>
                  </td>
                  <td>
                  <?php
                  echo '<span class="bold">' . mc_safeHTML($PHYS->pName). '</span>' . ($details ? ' <span class="highlight">('.$details.')</span>' : '').$isDel;
                  // Has this product got attributes?
                  echo ($code ? '<br><br>' . $code : '');
                  if ($hasAttribs > 0) {
                  ?>
                  <div class="attrSaleBoxes" id="prodAttrArea_<?php echo $PHYS->pcid; ?>">
                  <hr>
                  <?php
                  // Attributes
                  $alreadyLoaded = array();
                  $q_att = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_atts`
                           WHERE `saleID` = '{$SALE->id}'
                           AND `productID` = '{$PHYS->pid}'
                           AND `purchaseID` = '{$PHYS->pcid}'
                           ORDER BY `id`
                           ") or die(mc_MySQLError(__LINE__,__FILE__));
                  if (mysqli_num_rows($q_att) > 0) {
                  ?>
                  <div class="table-responsive">
                  <table class="table">
                  <tbody>
                  <?php
                  while ($ATTRIBUTES = mysqli_fetch_object($q_att)) {
                  $alreadyLoaded[] = $ATTRIBUTES->attributeID;
                  ?>
                  <tr id="attr_<?php echo $PHYS->pcid.'_'.$ATTRIBUTES->id; ?>">
                    <td><input type="text" class="box" name="attr[<?php echo $PHYS->pcid; ?>][<?php echo $ATTRIBUTES->attributeID; ?>]" value="<?php echo mc_safeHTML($ATTRIBUTES->attrName); ?>"></td>
                    <td>
                    <div class="form-group input-group">
                     <input type="text" class="box addon-no-radius-right" name="attr_cost[<?php echo $PHYS->pcid; ?>][<?php echo $ATTRIBUTES->attributeID; ?>]" value="<?php echo mc_safeHTML($ATTRIBUTES->addCost); ?>">
                     <span class="input-group-addon"><a href="#" onclick="mc_hideAttrBox('<?php echo $PHYS->pcid; ?>','<?php echo $ATTRIBUTES->id; ?>');return false"><i class="fa fa-times fa-fw mc-red"></i></a></span>
                    </div>
                    </td>
                  </tr>
                  <?php
                  }
                  ?>
                  </tbody>
                  </table>
                  </div>
                  <?php
                  }
                  ?>
                  <div id="alinks_<?php echo $PHYS->pcid; ?>"><a href="#" onclick="mc_hideAttr('<?php echo $PHYS->pcid; ?>');return false" title="<?php echo mc_cleanDataEntVars($msg_viewsale110); ?>"><i class="fa fa-plus fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo mc_cleanDataEntVars($msg_viewsale110); ?></span></a><?php echo (SALE_EDIT_PRODUCT_ATTRIBUTE_EDIT ? '&nbsp;&nbsp;<a href="?p=product-attributes&amp;product=' . $PHYS->pid . '" onclick="window.open(this);return false" title="' . mc_cleanDataEntVars($msg_viewsale111) . '"><i class="fa fa-pencil fa-fw"></i><span class="hidden-sm hidden-xs"> ' . mc_cleanDataEntVars($msg_viewsale111) . '</span></a>' : ''); ?></div>
                  </div>
                  <?php
                  // Possible attributes..
                  ?>
                  <div class="add" id="attsel_<?php echo $PHYS->pcid; ?>" style="display:none">
                  <?php
                  $q_attc = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "attributes`.`id` AS `attrID` FROM `" . DB_PREFIX . "attributes`
                            LEFT JOIN `" . DB_PREFIX . "attr_groups`
                            ON `" . DB_PREFIX . "attributes`.`attrGroup`    = `" . DB_PREFIX . "attr_groups`.`id`
                            WHERE `" . DB_PREFIX . "attributes`.`productID` = '{$PHYS->pid}'
                            AND `" . DB_PREFIX . "attributes`.`attrStock`   > 0
                            ORDER BY `" . DB_PREFIX . "attr_groups`.`groupName`
                            ") or die(mc_MySQLError(__LINE__,__FILE__));
                  if (mysqli_num_rows($q_attc)>0) {
                  ?>
                  <select name="attsel" id="s_<?php echo $PHYS->pcid; ?>" onchange="mc_addAttributeToSale(this.value,'<?php echo $PHYS->pcid; ?>','<?php echo $PHYS->pid; ?>')">
                  <option value="0"><?php echo $msg_viewsale112; ?></option>
                  <?php
                  while ($ATTSEL = mysqli_fetch_object($q_attc)) {
                  ?>
                  <option value="<?php echo $ATTSEL->attrID; ?>" id="sel_<?php echo $PHYS->pcid; ?>_<?php echo $ATTSEL->attrID; ?>"<?php echo (in_array($ATTSEL->attrID,$alreadyLoaded) ? ' style="display:none"' : ''); ?>><?php echo mc_cleanData($ATTSEL->groupName.' - '.$ATTSEL->attrName); ?></option>
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
                  <?php
                  }
                  // Personalisation
                  $q_ps1 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "personalisation`
                           WHERE `productID` = '{$PHYS->pid}'
                           AND `enabled`     = 'yes'
                           ORDER BY `id`
                           ") or die(mc_MySQLError(__LINE__,__FILE__));
                  if (mysqli_num_rows($q_ps1)>0) {
                  ?>
                  <div class="personalisation" id="pWrapper_<?php echo $PHYS->pcid; ?>">
                  <hr>
                  <div class="table-responsive">
                  <table class="table">
                  <tbody>
                  <?php
                  $iBoxes = 0;
                  while ($PS = mysqli_fetch_object($q_ps1)) {
                  ++$iBoxes;
                  $q_ps = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_pers`
                          WHERE `purchaseID`       = '{$PHYS->pcid}'
                          AND `saleID`             = '" . mc_digitSan($_GET['sale']) . "'
                          AND `productID`          = '{$PHYS->pid}'
                          AND `personalisationID`  = '{$PS->id}'
                          ORDER BY `id`
                          ") or die(mc_MySQLError(__LINE__,__FILE__));
                  $PERS_ITEM    = mysqli_fetch_object($q_ps);
                  $cost         = (isset($PERS_ITEM->addCost) ? $PERS_ITEM->addCost : '0.00');
                  $PS->boxType  = ($PS->persOptions ? 'select' : $PS->boxType);
                  // Input boxes for new items only..
                  $inputBoxes = '<input type="hidden" name="pidnew['.$PHYS->pcid.'][]" value="'.$PHYS->pcid.'">
                  <input type="hidden" name="product['.$PHYS->pcid.'][]" value="'.$PHYS->pid.'">';
                  switch($PS->boxType) {
                   case 'input':
                     ?>
                     <tr>
                       <td colspan="2" class="perHead"><label><?php echo mc_persTextDisplay(mc_safeHTML($PS->persInstructions),true); ?></label></td>
                     </tr>
                     <tr>
                       <td><?php echo $inputBoxes; ?><input type="hidden" name="pers[<?php echo $PHYS->pcid; ?>][]" value="<?php echo (isset($PERS_ITEM->id) ? $PERS_ITEM->id : $PS->id); ?>"><input id="ibox_<?php echo $PHYS->pcid; ?>_<?php echo $iBoxes; ?>" class="box" type="text" name="pvalue[<?php echo $PHYS->pcid; ?>][]" value="<?php echo (isset($PERS_ITEM->visitorData) ? mc_safeHTML($PERS_ITEM->visitorData) : ''); ?>"></td>
                       <td><input type="text" class="box" name="pers_cost[<?php echo $PHYS->pcid; ?>][]" value="<?php echo $cost; ?>"></td>
                     </tr>
                     <?php
                     break;
                   case 'textarea':
                     ?>
                     <tr>
                       <td colspan="2" class="perHead"><label><?php echo mc_persTextDisplay(mc_safeHTML($PS->persInstructions),true); ?></label></td>
                     </tr>
                     <tr>
                       <td><?php echo $inputBoxes; ?><input type="hidden" name="pers[<?php echo $PHYS->pcid; ?>][]" value="<?php echo (isset($PERS_ITEM->id) ? $PERS_ITEM->id : $PS->id); ?>"><textarea id="ibox_<?php echo $PHYS->pcid; ?>_<?php echo $iBoxes; ?>" name="pvalue[<?php echo $PHYS->pcid; ?>][]" rows="5" cols="20"><?php echo (isset($PERS_ITEM->visitorData) ? mc_safeHTML($PERS_ITEM->visitorData) : ''); ?></textarea></td>
                       <td><input type="text" class="box" name="pers_cost[<?php echo $PHYS->pcid; ?>][]" value="<?php echo $cost; ?>"></td>
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
                        <?php echo $inputBoxes; ?><input type="hidden" name="pers[<?php echo $PHYS->pcid; ?>][]" value="<?php echo (isset($PERS_ITEM->id) ? $PERS_ITEM->id : $PS->id); ?>">
                        <select name="pvalue[<?php echo $PHYS->pcid; ?>][]" id="ibox_<?php echo $PHYS->pcid; ?>_<?php echo $iBoxes; ?>">
                        <option value="no-option-selected">- - - - -</option>
                        <?php
                        $OPT = explode('||',$PS->persOptions);
                        foreach ($OPT AS $o) {
                        ?>
                        <option value="<?php echo $o; ?>"<?php echo (isset($PERS_ITEM->visitorData) && $o==$PERS_ITEM->visitorData ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($o); ?></option>
                        <?php
                        }
                        ?>
                        </select>
                       </td>
                       <td><input type="text" class="box" name="pers_cost[<?php echo $PHYS->pcid; ?>][]" value="<?php echo $cost; ?>"></td>
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
                    <select name="qty[]" id="qty_<?php echo $PHYS->pcid; ?>" onchange="if(jQuery('#qty_<?php echo $PHYS->pcid; ?>').val()=='0') {mc_alertBox('<?php echo mc_cleanDataEntVars($msg_javascript220); ?>');mc_MarkForDeletion('<?php echo $PHYS->pcid; ?>','no','view');}">
                    <?php
                    foreach (range(0,SALE_QTY_LIMIT) AS $qty) {
                    ?>
                    <option value="<?php echo $qty; ?>"<?php echo ($PHYS->productQty==$qty ? ' selected="selected"' : ''); ?>><?php echo $qty; ?></option>
                    <?php
                    }
                    ?>
                    </select>
                  </td>
                  <td>
                   <input class="box" id="price_<?php echo $PHYS->pcid; ?>" type="text" name="price[]" value="<?php echo mc_formatPrice($PHYS->salePrice); ?>">
                   <?php
                   if ($hasAttribs>0) {
                   ?>
                   <div style="margin-top:20px">
                     <label><?php echo $msg_viewsale88; ?>:</label>
                     <input type="hidden" name="attrPrice[]" id="attrh_<?php echo $PHYS->pcid; ?>" value="<?php echo mc_formatPrice($PHYS->attrPrice); ?>"><input readonly="readonly" class="box" id="attr_<?php echo $PHYS->pcid; ?>" type="text" name="attrPrice2[]" value="<?php echo mc_formatPrice($PHYS->attrPrice); ?>">
                   </div>
                   <?php
                   } else {
                   ?>
                   <input type="hidden" name="attrPrice[]" value="0.00">
                   <?php
                   }
                   if (mysqli_num_rows($q_ps1)>0) {
                   ?>
                   <div style="margin-top:20px">
                     <label><?php echo $msg_viewsale87; ?>:</label>
                     <input type="hidden" name="persPrice[]" id="persh_<?php echo $PHYS->pcid; ?>" value=""><input class="box" id="pers_<?php echo $PHYS->pcid; ?>" readonly="readonly" type="text" name="persPrice2[]" value="<?php echo mc_formatPrice($PHYS->persPrice); ?>">
                   </div>
                   <?php
                   } else {
                   ?>
                   <input type="hidden" name="persPrice[]" value="0.00">
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
           <span class="highlight" id="highlight_<?php echo $PHYS->pcid; ?>"><?php echo mc_currencyFormat(mc_formatPrice(($PHYS->productQty*$PHYS->salePrice)+($PHYS->persPrice+$PHYS->attrPrice),true)); ?></span>&nbsp;&nbsp;<i class="fa fa-calculator fa-fw mc_cursor_pointer" title="<?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[6]); ?>" onclick="mc_displayPurchaseProductPrices('<?php echo $PHYS->pcid; ?>','sales-view')"></i>
           <input type="hidden" id="total_price_<?php echo $PHYS->pcid; ?>" name="t_price[]" value="<?php echo mc_formatPrice(($PHYS->salePrice + $PHYS->persPrice + $PHYS->attrPrice) * $PHYS->productQty); ?>">
          </div>
        </div>
        <?php
      }
      ?>