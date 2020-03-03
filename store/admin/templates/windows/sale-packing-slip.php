<?php if (!defined('PARENT') || !isset($SALE->id)) { die('Permission Denied'); }

  if (in_array($SALE->paymentStatus, array('','pending'))) {
    define('INCPL_SALE', 1);
  }

  define('WINPARENT', 1);
  define('SALE_PACKING_SLIP', 1);
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

  $gridWidth = 4;
  if ($SETTINGS->en_wish == 'yes' && $SALE->wishlist > 0) {
    $gridWidth = 6;
  }
  ?>

  <body>

  <div class="container packsliptemplate">
    <div class="row">
        <div class="col-xs-12">
            <div class="text-center">
                <i class="fa fa-truck pull-left icon hidden-xs"></i>
                <h2><?php echo $msg_invoice15 . ': #' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS); ?></h2>
            </div>
            <hr>
            <div class="row">
                <div class="col-xs-12 col-md-<?php echo $gridWidth; ?> col-lg-<?php echo $gridWidth; ?>">
                    <div class="panel panel-default height">
                        <div class="panel-heading"><?php echo $msg_admin_invoice3_0[0]; ?></div>
                        <div class="panel-body infoboxes">
                            <b><?php echo $msg_invoice3; ?>:</b> <?php echo $SALE->pdate; ?><br>
                            <b><?php echo $msg_invoice4; ?>:</b> <?php echo mc_paymentMethodName($SALE->paymentMethod); ?><br>
                            <b><?php echo $msg_admin_invoice3_0[4]; ?>:</b> <?php echo ($SALE->type == 'personal' ? $msg_admin_invoice3_0[5] : $msg_admin_invoice3_0[6]); ?>
                            <?php
                            if ($SALE->setShipRateID>0 && in_array($SALE->shipType,array('weight'))) {
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
                <?php
                if ($SETTINGS->en_wish == 'yes' && $SALE->wishlist == 0) {
                ?>
                <div class="col-xs-12 col-md-4 col-lg-4 pull-left">
                    <div class="panel panel-default height">
                        <div class="panel-heading"><?php echo $msg_invoice5; ?></div>
                        <div class="panel-body infoboxes">
                            <b><?php echo mc_safeHTML($SALE->bill_1); ?></b><br>
                            <?php echo $billingAddress; ?><br>
                            <?php echo mc_getShippingCountry($SALE->bill_9); ?><br><br>
                            <b>E</b>: <?php echo mc_safeHTML($SALE->bill_2); ?>
                        </div>
                    </div>
                </div>
                <?php
                }
                ?>
                <div class="col-xs-12 col-md-<?php echo $gridWidth; ?> col-lg-<?php echo $gridWidth; ?>">
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
                                    <td></td>
                                    <td><b><?php echo $msg_invoice6; ?></b></td>
                                    <td class="text-center"><b><?php echo $msg_invoice7; ?></b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                //=============================
                                // TANGIBLE PRODUCTS
                                //=============================

                                $q_phys = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,`" . DB_PREFIX . "purchases`.`id` AS `pcid` FROM `" . DB_PREFIX . "purchases`
                                          LEFT JOIN `" . DB_PREFIX . "products`
                                          ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
                                          WHERE `saleID`                        = '" . mc_digitSan($_GET['sale']) . "'
                                          AND `productType`                     = 'physical'
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
                                      <td class="tickbox"><span>&nbsp;</span></td>
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
                                  </tr>
                                  <?php
                                  }
                                }

                                //=============================
                                // DOWNLOAD PRODUCTS
                                //=============================

                                if (INCLUDE_DOWNLOADS_ON_PACKING_SLIP) {
                                $q_down = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,`" . DB_PREFIX . "purchases`.`id` AS `pcid` FROM `" . DB_PREFIX . "purchases`
                                          LEFT JOIN `" . DB_PREFIX . "products`
                                          ON `" . DB_PREFIX . "purchases`.`productID`  = `" . DB_PREFIX . "products`.`id`
                                          WHERE `saleID`                         = '" . mc_digitSan($_GET['sale']) . "'
                                          AND `productType`                      = 'download'
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
                                      <td class="tickbox"><span>&nbsp;</span></td>
                                      <td><i class="fa fa-download fa-fw"></i> <?php echo ($code ? '[' . $code . '] ' : '') . mc_safeHTML($DOWN->pName); ?></td>
                                      <td class="text-center"><?php echo $DOWN->productQty; ?></td>
                                  </tr>
                                  <?php
                                  $subTotal = $subTotal+mc_formatPrice($DOWN->productQty*$DOWN->salePrice);
                                  }
                                }
                                }

                                //=============================
                                // GIFT CERTIFICATES
                                //=============================

                                if (INCLUDE_GIFT_ON_PACKING_SLIP) {
                                $q_virtual = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,`" . DB_PREFIX . "purchases`.`id` AS `pcid` FROM `" . DB_PREFIX . "purchases`
                                             LEFT JOIN `" . DB_PREFIX . "products`
                                             ON `" . DB_PREFIX . "purchases`.`productID`  = `" . DB_PREFIX . "products`.`id`
                                             WHERE `saleID`                         = '" . mc_digitSan($_GET['sale']) . "'
                                             AND `productType`                      = 'virtual'
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
                                      <td class="tickbox"><span>&nbsp;</span></td>
                                      <td><i class="fa fa-gift fa-fw"></i> <?php echo ($code ? '[' . $code . '] ' : '') . mc_safeHTML($VUTL->pName);
                                        echo '<hr><div class="alert alert-info">' . mc_saleGiftCerts(mc_digitSan($_GET['sale']),$VUTL->pcid) . '</div>';
                                      ?>
                                      </td>
                                      <td class="text-center"><?php echo $VUTL->productQty; ?></td>
                                  </tr>
                                  <?php
                                  }
                                }
                                }

                                ?>
                             </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-default">
            <div class="panel-body checkarea">
              <p><?php echo $msg_invoice23; ?>: ____________________________________&nbsp;&nbsp;&nbsp;<?php echo $msg_invoice25; ?>: _________________________</p>
              <p><?php echo $msg_invoice24; ?>: ____________________________________&nbsp;&nbsp;&nbsp;<?php echo $msg_invoice25; ?>: _________________________</p>
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
    <p style="text-align: center;margin-bottom:20px"><a class="btn btn-default btn-sm" href="#" onclick="mc_loadPDF('<?php echo $SALE->id . '-' . $SALE->buyCode; ?>','pdf-slip');return false"><i class="fa fa-file-pdf-o fa-fw"></i></a></p>

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
