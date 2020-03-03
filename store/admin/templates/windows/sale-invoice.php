<?php if (!defined('PARENT') || !isset($SALE->id)) { die('Permission Denied'); }

  if (in_array($SALE->paymentStatus, array('','pending'))) {
    define('INCPL_SALE', 1);
  }

  define('WINPARENT', 1);
  define('SALE_INVOICE', 1);
  include(PATH . 'templates/windows/header.php');

  $bill  = array();
  $ship  = array();
  // Addresses. Check for legacy..
  if ($SALE->buyerAddress) {
    $billingAddress   = mc_NL2BR(mc_safeHTML($SALE->buyerAddress));
    $shippingAddress  = mc_NL2BR(mc_safeHTML($SALE->buyerAddress));
  } else {
    // Build addresses from fields..lazy loop..:)
    for ($i=3; $i<8; $i++) {
      $f = 'bill_'.$i;
      $s = 'ship_'.$i;
      if (trim($SALE->$f)) {
        $bill[] = mc_safeHTML($SALE->$f);
      }
      if (trim($SALE->$s)) {
        $ship[] = mc_safeHTML($SALE->$s);
      }
    }
    $billingAddress   = implode('<br>',$bill);
    $shippingAddress  = implode('<br>',$ship);
  }
  ?>

  <body>

  <div class="container invoicetemplate">
    <div class="row">
        <div class="col-xs-12">
            <div class="text-center">
                <i class="fa fa-search-plus pull-left icon hidden-xs"></i>
                <h2><?php echo $msg_invoice . ': #' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS); ?></h2>
            </div>
            <hr>
            <div class="row">
                <div class="col-xs-12 col-md-4 col-lg-4">
                    <div class="panel panel-default height">
                        <div class="panel-heading"><?php echo $msg_admin_invoice3_0[0]; ?></div>
                        <div class="panel-body infoboxes">
                            <b><?php echo $msg_invoice3; ?>:</b> <?php echo $SALE->pdate; ?><br>
                            <b><?php echo $msg_invoice4; ?>:</b> <?php echo mc_paymentMethodName($SALE->paymentMethod); ?><br>
                            <b><?php echo $msg_admin_invoice3_0[4]; ?>:</b> <?php echo ($SALE->type == 'personal' ? $msg_admin_invoice3_0[5] : $msg_admin_invoice3_0[6]); ?>
                            <?php
                            if ($SALE->setShipRateID > 0 && in_array($SALE->shipType,array('weight'))) {
                            ?>
                            <br><b><?php echo $msg_invoice30; ?>:</b> <?php echo mc_getShippingService(mc_getShippingServiceFromRate($SALE->setShipRateID)); ?>
                            <?php
                            }
                            if ($SALE->couponCode) {
                            ?>
                            <br><b><?php echo $msg_invoice19; ?>:</b> <?php echo $msg_script5; ?>
                            <?php
                            }
                            if ($SALE->insuranceTotal > 0) {
                            ?>
                            <br><b><?php echo $msg_invoice33; ?>:</b> <?php echo $msg_script5; ?>
                            <?php
                            }
                            if ($SALE->isPickup == 'yes') {
                            ?>
                            <br><b><?php echo $msg_admin_invoice3_0[1]; ?>:</b> <?php echo $msg_script5; ?>
                            <?php
                            }
                            if ($SETTINGS->en_wish == 'yes' && $SALE->wishlist > 0) {
                            ?>
                            <br><br>
                            <div class="alert alert-warning" style="padding:5px;margin-bottom:0"><i class="fa fa-heart fa-fw"></i> <?php echo $msg_admin_invoice3_0[3]; ?></div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4 col-lg-4 pull-left">
                    <div class="panel panel-default height">
                        <div class="panel-heading"><?php echo $msg_invoice5; ?></div>
                        <div class="panel-body infoboxes">
                            <b><?php echo mc_safeHTML($SALE->bill_1); ?></b><br>
                            <?php echo $billingAddress; ?><br>
                            <?php echo mc_getShippingCountry($SALE->bill_9); ?><br><br>
                            <b>E</b>: <?php echo mc_safeHTML($SALE->bill_2); ?><br>
                            <?php
                            if ($SALE->bill_8) {
                            ?>
                            <b>T</b>: <?php echo mc_safeHTML($SALE->bill_8);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4 col-lg-4">
                    <div class="panel panel-default height">
                        <div class="panel-heading"><?php echo $msg_invoice34; ?></div>
                        <div class="panel-body infoboxes">
                            <b><?php echo mc_safeHTML($SALE->ship_1); ?></b><br>
                            <?php echo $shippingAddress; ?><br>
                            <?php echo mc_getShippingCountry($SALE->shipSetCountry); ?><br><br>
                            <b>E</b>: <?php echo mc_safeHTML($SALE->ship_2); ?><br>
                            <b>T</b>: <?php echo mc_safeHTML($SALE->ship_8); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><?php echo $msg_admin_invoice3_0[2]; ?></h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td style="width:60%"><b><?php echo $msg_invoice6; ?></b></td>
                                    <td class="text-center"><b><?php echo $msg_invoice7; ?></b></td>
                                    <td class="text-center"><b><?php echo $msg_invoice8; ?></b></td>
                                    <td class="text-center"><b><?php echo $msg_invoice31; ?></b></td>
                                    <td class="text-right"><b><?php echo $msg_invoice9; ?></b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $subTotal = '0.00';

                                //=============================
                                // TANGIBLE PRODUCTS
                                //=============================

                                $q_phys = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,`" . DB_PREFIX . "purchases`.`id` AS `pcid` FROM `" . DB_PREFIX . "purchases`
                                          LEFT JOIN `" . DB_PREFIX . "products`
                                          ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
                                          WHERE `saleID`                        = '" . mc_digitSan($_GET['sale']) . "'
                                          AND `productType`                     = 'physical'
                                          AND `saleConfirmation`                = '" . (defined('INCPL_SALE') ? 'no' : 'yes') . "'
                                          ORDER BY `" . DB_PREFIX . "purchases`.`id`
                                          ") or die(mc_MySQLError(__LINE__,__FILE__));
                                if (mysqli_num_rows($q_phys)>0) {
                                  while ($PHYS = mysqli_fetch_object($q_phys)) {
                                  $details      = '';
                                  $code         = ($PHYS->pCode ? $PHYS->pCode : 'N/A');
                                  $weight       = ($PHYS->pWeight ? $PHYS->pWeight : 'N/A');
                                  $PHYS->pName  = ($PHYS->pName ? $PHYS->pName : $PHYS->deletedProductName);
                                  $isDel        = ($PHYS->deletedProductName ? '<span class="deletedItem">'.$msg_script53.'</span>' : '');
                                  $pers_Price   = '0.00';
                                  ?>
                                  <tr>
                                      <td><i class="fa fa-cube fa-fw"></i> <?php echo ($code ? '[' . $code . '] ' : '') . mc_safeHTML($PHYS->pName);
                                      $attr = mc_saleAttributes(mc_digitSan($_GET['sale']),$PHYS->pcid,$PHYS->pid);
                                      if (trim($attr)) {
                                        echo '<hr><div class="alert alert-info">' . $attr . '</div>';
                                      }
                                      // Personalised items..
                                      $q_ps = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_pers`
                                              WHERE `purchaseID` = '{$PHYS->pcid}'
                                              ORDER BY `id`
                                              ") or die(mc_MySQLError(__LINE__,__FILE__));
                                      if (mysqli_num_rows($q_ps)>0) {
                                      ?>
                                      <hr>
                                      <div class="alert alert-success">
                                      <?php
                                        while ($PS = mysqli_fetch_object($q_ps)) {
                                          $PERSONALISED  = mc_getTableData('personalisation','id',$PS->personalisationID);
                                          if ($PS->visitorData && $PS->visitorData!='no-option-selected') {
                                            echo '<div><i class="fa fa-angle-right fa-fw"></i>' . mc_persTextDisplay(mc_safeHTML($PERSONALISED->persInstructions),true).($PERSONALISED->persAddCost>0 ? ' (+'.mc_currencyFormat(mc_formatPrice($PERSONALISED->persAddCost)).')' : '').': ' . mc_safeHTML($PS->visitorData).'</div>';
                                          }
                                        }
                                        // Pers per item..
                                        $pers_Price = ($PHYS->persPrice>0 ? mc_formatPrice($PHYS->persPrice) : '0.00');
                                      ?>
                                      </div>
                                      <?php
                                      }
                                      ?>
                                      </td>
                                      <td class="text-center"><?php echo $PHYS->productQty; ?></td>
                                      <td class="text-center"><?php echo mc_currencyFormat(mc_formatPrice($PHYS->salePrice,true)); ?></td>
                                      <td class="text-center"><?php echo ($pers_Price>0 || $PHYS->attrPrice>0 ? mc_currencyFormat(mc_formatPrice($pers_Price+$PHYS->attrPrice,true)) : '- -'); ?></td>
                                      <td class="text-right"><?php echo mc_currencyFormat(mc_formatPrice(($PHYS->productQty*$PHYS->salePrice)+($pers_Price+$PHYS->attrPrice),true)); ?></td>
                                  </tr>
                                  <?php
                                  $subTotal = $subTotal+mc_formatPrice(($PHYS->productQty*$PHYS->salePrice)+($PHYS->persPrice+$PHYS->attrPrice));
                                  }
                                }

                                //=============================
                                // DOWNLOAD PRODUCTS
                                //=============================

                                if (INCLUDE_DOWNLOADS_ON_INVOICE) {
                                $q_down = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,`" . DB_PREFIX . "purchases`.`id` AS `pcid` FROM `" . DB_PREFIX . "purchases`
                                          LEFT JOIN `" . DB_PREFIX . "products`
                                          ON `" . DB_PREFIX . "purchases`.`productID`  = `" . DB_PREFIX . "products`.`id`
                                          WHERE `saleID`                         = '" . mc_digitSan($_GET['sale']) . "'
                                          AND `productType`                      = 'download'
                                          AND `saleConfirmation`                = '" . (defined('INCPL_SALE') ? 'no' : 'yes') . "'
                                          ORDER BY `" . DB_PREFIX . "purchases`.`id`
                                          ") or die(mc_MySQLError(__LINE__,__FILE__));
                                if (mysqli_num_rows($q_down)>0) {
                                  while ($DOWN = mysqli_fetch_object($q_down)) {
                                  $details      = '';
                                  $code         = ($DOWN->pCode ? $DOWN->pCode : 'N/A');
                                  $weight       = ($DOWN->pWeight ? $DOWN->pWeight : 'N/A');
                                  $DOWN->pName  = ($DOWN->pName ? $DOWN->pName : $DOWN->deletedProductName);
                                  $isDel2       = ($DOWN->deletedProductName ? '<span class="deletedItem">'.$msg_script53.'</span>' : '');

                                  $pers_Price = '0.00';
                                  ?>
                                  <tr>
                                      <td><i class="fa fa-download fa-fw"></i> <?php echo ($code ? '[' . $code . '] ' : '') . mc_safeHTML($DOWN->pName); ?></td>
                                      <td class="text-center"><?php echo $DOWN->productQty; ?></td>
                                      <td class="text-center"><?php echo mc_currencyFormat(mc_formatPrice($DOWN->salePrice,true)); ?></td>
                                      <td class="text-center"><?php echo ($pers_Price>0 || $DOWN->attrPrice>0 ? mc_currencyFormat(mc_formatPrice($pers_Price+$DOWN->attrPrice,true)) : '- -'); ?></td>
                                      <td class="text-right"><?php echo mc_currencyFormat(mc_formatPrice($DOWN->productQty*($DOWN->salePrice+$pers_Price+$DOWN->attrPrice),true)); ?></td>
                                  </tr>
                                  <?php
                                  $subTotal = $subTotal+mc_formatPrice($DOWN->productQty*$DOWN->salePrice);
                                  }
                                }
                                }

                                //=============================
                                // GIFT CERTIFICATES
                                //=============================

                                if (INCLUDE_GIFT_ON_INVOICE) {
                                $q_virtual = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,`" . DB_PREFIX . "purchases`.`id` AS `pcid` FROM `" . DB_PREFIX . "purchases`
                                             LEFT JOIN `" . DB_PREFIX . "products`
                                             ON `" . DB_PREFIX . "purchases`.`productID`  = `" . DB_PREFIX . "products`.`id`
                                             WHERE `saleID`                         = '" . mc_digitSan($_GET['sale']) . "'
                                             AND `productType`                      = 'virtual'
                                             AND `saleConfirmation`                = '" . (defined('INCPL_SALE') ? 'no' : 'yes') . "'
                                             ORDER BY `" . DB_PREFIX . "purchases`.`id`
                                             ") or die(mc_MySQLError(__LINE__,__FILE__));
                                if (mysqli_num_rows($q_virtual)>0) {
                                  while ($VUTL = mysqli_fetch_object($q_virtual)) {
                                  $GIFT         = mc_getTableData('giftcerts','id',$VUTL->giftID);
                                  $details      = '';
                                  $code         = '';
                                  $weight       = 'N/A';
                                  $VUTL->pName  = (isset($GIFT->name) ? $GIFT->name : $VUTL->deletedProductName);
                                  $isDel2       = ($VUTL->deletedProductName ? '<span class="deletedItem">'.$msg_script53.'</span>' : '');

                                  $pers_Price = '0.00';
                                  ?>
                                  <tr>
                                      <td><i class="fa fa-gift fa-fw"></i> <?php echo ($code ? '[' . $code . '] ' : '') . mc_safeHTML($VUTL->pName);
                                        echo '<hr><div class="alert alert-info">' . mc_saleGiftCerts(mc_digitSan($_GET['sale']),$VUTL->pcid) . '</div>';
                                      ?>
                                      </td>
                                      <td class="text-center"><?php echo $VUTL->productQty; ?></td>
                                      <td class="text-center"><?php echo mc_currencyFormat(mc_formatPrice($VUTL->salePrice,true)); ?></td>
                                      <td class="text-center"><?php echo ($pers_Price>0 || $VUTL->attrPrice>0 ? mc_currencyFormat(mc_formatPrice($pers_Price+$VUTL->attrPrice,true)) : '- -'); ?></td>
                                      <td class="text-right"><?php echo mc_currencyFormat(mc_formatPrice($VUTL->productQty*($VUTL->salePrice+$pers_Price+$VUTL->attrPrice),true)); ?></td>
                                  </tr>
                                  <?php
                                  $subTotal = $subTotal+mc_formatPrice($VUTL->productQty*$VUTL->salePrice);
                                  }
                                }
                                }

                                ?>
                                <tr>
                                    <td class="highrow"></td>
                                    <td class="highrow"></td>
                                    <td class="highrow"></td>
                                    <td class="highrow text-right"><b><?php echo $msg_invoice17; ?></b></td>
                                    <td class="highrow text-right"><?php echo mc_currencyFormat(mc_formatPrice($subTotal,true)); ?></td>
                                </tr>
                                <?php

                                //=============================
                                // DISCOUNT / COUPON
                                //=============================

                                $discountCouponArea = array();
                                if ($SALE->globalDiscount>0) {
                                  switch($SALE->type) {
                                    case 'trade':
                                      $discountCouponArea['text'] = str_replace('{percentage}',$SALE->globalDiscount,$msg_admin_invoice3_0[7]);
                                      $discountCouponArea['cost'] = '- ' . mc_currencyFormat(mc_formatPrice($SALE->globalTotal,true));
                                      break;
                                    default:
                                      $discountCouponArea['text'] = str_replace('{percentage}',$SALE->globalDiscount,$msg_invoice27);
                                      $discountCouponArea['cost'] = '- ' . mc_currencyFormat(mc_formatPrice($SALE->globalTotal,true));
                                      break;
                                  }
                                } elseif ($SALE->manualDiscount>0) {
                                  $discountCouponArea['text'] = $msg_invoice28;
                                  $discountCouponArea['cost'] = '- ' . mc_currencyFormat(mc_formatPrice($SALE->manualDiscount,true));
                                } else {
                                  // Discount Coupon...
                                  if ($SALE->couponTotal>0 && $SALE->codeType=='discount') {
                                    $COUPON  = mc_getTableData('coupons','cDiscountCode',$SALE->couponCode);
                                    if (isset($COUPON->cCampaign)) {
                                      $CAMP  = mc_getTableData('campaigns','id',$COUPON->cCampaign);
                                      $discountCouponArea['text'] = $msg_invoice19;
                                      $discountCouponArea['cost'] = '- ' . mc_currencyFormat($MCPROD->getDiscount(mc_formatPrice($subTotal),$CAMP->cDiscount));
                                    }
                                  }
                                  // Gift Certificate...
                                  if ($SALE->couponTotal>0 && $SALE->codeType=='gift') {
                                    $GIFT = mc_getTableData('giftcodes','code',$SALE->couponCode);
                                    if (isset($GIFT->id)) {
                                      $discountCouponArea['text'] = $msg_invoice36 . ' (' . $SALE->couponCode . ')';
                                      $discountCouponArea['cost'] = '- ' . mc_currencyFormat($MCPROD->getDiscount(mc_formatPrice($subTotal),$SALE->couponTotal));
                                    }
                                  }
                                }
                                // Was there a discount?
                                if (isset($discountCouponArea['text'])) {
                                ?>
                                <tr>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow text-right"><b><?php echo $discountCouponArea['text']; ?></b></td>
                                    <td class="emptyrow text-right"><?php echo $discountCouponArea['cost']; ?></td>
                                </tr>
                                <?php
                                }

                                //=============================
                                // SHIPPING / TAX
                                //=============================

                                $shippingTaxArea = array();
                                if ($SALE->shipSetArea>0) {
                                  // If tax isn`t applied to shipping, show tax before shipping..
                                  $A  = mc_getTableData('zone_areas','id',$SALE->shipSetArea,'','inZone');
                                  if (isset($A->inZone)) {
                                    $Z  = mc_getTableData('zones','id',$A->inZone,'','zShipping');
                                  }
                                  // Tax on shipping as well as total..
                                  if (isset($Z->zShipping) && $Z->zShipping=='yes') {
                                    if ($SALE->shipTotal>0) {
                                      $shippingTaxArea[] = array(
                                        'text' => $msg_invoice20,
                                        'cost' => ($SALE->shipTotal>0 ? mc_currencyFormat(mc_formatPrice($SALE->shipTotal,true)) : $msg_invoice35)
                                      );
                                    }
                                    if ($SALE->taxPaid>0) {
                                      $shippingTaxArea[] = array(
                                        'text' => str_replace('{rate}',$SALE->taxRate.'%',$msg_invoice21),
                                        'cost' => mc_currencyFormat(mc_formatPrice($SALE->taxPaid,true))
                                      );
                                    }
                                  } else {
                                    // Tax on total, not shipping..
                                    if ($SALE->taxPaid>0) {
                                      $shippingTaxArea[] = array(
                                        'text' => str_replace('{rate}',$SALE->taxRate.'%',$msg_invoice21),
                                        'cost' => mc_currencyFormat(mc_formatPrice($SALE->taxPaid,true))
                                      );
                                    }
                                    if ($SALE->shipTotal>0) {
                                      $shippingTaxArea[] = array(
                                        'text' => $msg_invoice20,
                                        'cost' => mc_currencyFormat(mc_formatPrice($SALE->shipTotal,true))
                                      );
                                    }
                                  }
                                } else {
                                  // If no areas are on display, but shipping does exist, show it..
                                  if ($SALE->shipTotal>0) {
                                    $shippingTaxArea[] = array(
                                      'text' => $msg_invoice20,
                                      'cost' => mc_currencyFormat(mc_formatPrice($SALE->shipTotal,true))
                                    );
                                  }
                                }
                                if (!empty($shippingTaxArea)) {
                                  for ($i=0; $i<count($shippingTaxArea); $i++) {
                                  ?>
                                  <tr>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow text-right"><b><?php echo $shippingTaxArea[$i]['text']; ?></b></td>
                                    <td class="emptyrow text-right"><?php echo $shippingTaxArea[$i]['cost']; ?></td>
                                  </tr>
                                  <?php
                                  }
                                }

                                //=============================
                                // INSURANCE
                                //=============================

                                if ($SALE->insuranceTotal>0) {
                                  ?>
                                  <tr>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow text-right"><b><?php echo $msg_invoice33; ?></b></td>
                                    <td class="emptyrow text-right"><?php echo mc_currencyFormat(mc_formatPrice($SALE->insuranceTotal,true)); ?></td>
                                  </tr>
                                  <?php
                                }

                                //=============================
                                // ADDITIONAL CHARGE
                                //=============================

                                if ($SALE->chargeTotal>0) {
                                  ?>
                                  <tr>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow text-right"><b><?php echo $msg_sales_view[3]; ?></b></td>
                                    <td class="emptyrow text-right"><?php echo mc_currencyFormat(mc_formatPrice($SALE->chargeTotal,true)); ?></td>
                                  </tr>
                                  <?php
                                }

                                //=============================
                                // GRAND TOTAL
                                //=============================

                                ?>
                                <tr>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow"></td>
                                    <td class="emptyrow text-right"><b><?php echo $msg_invoice22; ?></b></td>
                                    <td class="emptyrow text-right"><?php echo mc_currencyFormat(mc_formatPrice($SALE->grandTotal,true)); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if ($SALE->saleNotes) {
    ?>
    <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <?php echo $msg_invoice10; ?>
            </div>
            <div class="panel-body">
              <?php echo mc_NL2BR(mc_safeHTML($SALE->saleNotes)); ?>
            </div>
          </div>
        </div>
    </div>
    <?php
    }
    ?>

    <div class="row">
        <div class="col-xs-12">
          <div class="row">
            <div class="col-xs-12 col-md-6 col-lg-6">
              <div class="panel panel-default">
                <div class="panel-body company">
                  <b><?php echo mc_safeHTML($SETTINGS->cName); ?></b><br>
                  <?php echo mc_NL2BR(mc_safeHTML($SETTINGS->cAddress)); ?>
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-6">
              <div class="panel panel-default">
                <div class="panel-body company">
                  <b><?php echo $msg_invoice11; ?></b>: <?php echo mc_safeHTML($SETTINGS->cTel); ?><br>
                  <b><?php echo $msg_invoice14; ?></b>: <?php echo mc_safeHTML($SETTINGS->cFax); ?><br>
                  <b><?php echo $msg_invoice12; ?></b>: <?php echo mc_safeHTML($SETTINGS->email); ?><br>
                  <b><?php echo $msg_invoice13; ?></b>: <?php echo mc_safeHTML($SETTINGS->cWebsite); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>

    <?php
    if ($SETTINGS->cOther) {
    ?>
    <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-default">
            <div class="panel-body">
              <?php echo mc_cleanData($SETTINGS->cOther); ?>
            </div>
          </div>
        </div>
    </div>
    <?php
    }

    if (INVOICE_SHOW_IP) {
    ?>
    <p class="ip"><?php echo $msg_invoice29 .' ' . $SALE->ipAddress; ?></p>
    <?php
    }

    // Are PDFs enabled?
    if ($SETTINGS->pdf == 'yes') {
    ?>
    <p style="text-align: center;margin-bottom:20px"><a class="btn btn-default btn-sm" href="#" onclick="mc_loadPDF('<?php echo $SALE->id . '-' . $SALE->buyCode; ?>','pdf-invoice');return false"><i class="fa fa-file-pdf-o fa-fw"></i></a></p>

    <?php
    // Action spinner, DO NOT REMOVE
    ?>
    <div class="overlaySpinner" style="display:none"></div>
    <?php
    }
    ?>

  </div>

  <?php
  include(PATH . 'templates/windows/footer.php');
  ?>
