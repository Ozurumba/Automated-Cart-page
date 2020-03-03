<?php if (!defined('PARENT')) { die('Permission Denied'); }

  $SLE = mc_getTableData('sales','id', $saleID);
  $saleCnf = 'yes';

  if (!defined('INCPL_SALE') && in_array($SLE->paymentStatus, array('','pending'))) {
    define('INCPL_SALE', 1);
    $saleCnf = 'no';
  }

  ?>
  <div>
    <div class="btn-group">
      <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-cog fa-fw"></i>
      </button>
      <ul class="dropdown-menu salesquicklinks">
        <?php
        if (!defined('SALE_EDIT')) {
        ?>
        <li><a href="?p=sales-view&amp;sale=<?php echo $saleID; ?>"><i class="fa fa-pencil fa-fw"></i> <?php echo $msg_admin_viewsale3_0[8]; ?></a></li>
        <?php
        }
        if (!defined('INCPL_SALE')) {
        ?>
        <li><a href="?p=sales-update&amp;sale=<?php echo $saleID; ?>"><i class="fa fa-history fa-fw"></i> <?php echo $msg_sales2; ?></a></li>
        <?php
        }
        if (!isset($_GET['ordered'])) {
        ?>
        <li><a href="?p=sales&amp;ordered=<?php echo $saleID; ?>"><i class="fa fa-cubes fa-fw"></i> <?php echo $msg_admin3_0[20]; ?></a></li>
        <?php
        }
        ?>
        <li><a href="?p=sales&amp;export=<?php echo $saleID . (defined('INCPL_SALE') ? '&amp;incsale=yes' : ''); ?>"><i class="fa fa-save fa-fw"></i> <?php echo $msg_sales4; ?></a></li>
        <li><a href="?p=invoice&sale=<?php echo $saleID; ?>" onclick="window.open(this);return false"><i class="fa fa-file-text-o fa-fw"></i> <?php echo $msg_sales5; ?></a></li>
        <?php
        if (mc_rowCount('purchases WHERE `saleID` = \'' . $saleID . '\' AND `saleConfirmation` = \'' . $saleCnf . '\' AND `productType` = \'physical\'') > 0) {
        ?>
        <li><a href="?p=packing-slip&sale=<?php echo $saleID; ?>" onclick="window.open(this);return false"><i class="fa fa-truck fa-fw"></i> <?php echo $msg_sales6; ?></a></li>
        <?php
        }
        // Show downloads link if there are downloadable products..
        if (!isset($dPage) && mc_rowCount('purchases WHERE `saleID` = \'' . $saleID . '\' AND `saleConfirmation` = \'' . $saleCnf . '\' AND `productType` = \'download\'') > 0) {
        ?>
        <li><a href="?p=downloads&amp;sale=<?php echo $saleID; ?>"><i class="fa fa-download fa-fw"></i> <?php echo $msg_admin_viewsale3_0[7]; ?></a></li>
        <?php
        }
        if (mc_rowCount('purch_pers WHERE `saleID` = \'' . $saleID . '\'') > 0) {
        ?>
        <li><a href="?p=sales-view&view-personalisation=<?php echo $saleID; ?>" onclick="window.open(this);return false"><i class="fa fa-print fa-fw"></i> <?php echo $msg_admin3_0[29]; ?></a></li>
        <?php
        }
        if (mc_rowCount('purchases WHERE `saleID` = \'' . $saleID . '\' AND `saleConfirmation` = \'' . $saleCnf . '\' AND `productType` = \'physical\'') > 0) {
        ?>
        <li><a href="?p=sales-view&shipLabel=<?php echo $saleID; ?>" onclick="window.open(this);return false"><i class="fa fa-tags fa-fw"></i> <?php echo $msg_viewsale105; ?></a></li>
        <?php
        }
        $s_p = 'yes';
        $s_d = 'yes';
        if (defined('SALES_ADD_PRODUCTS')) {
          if (isset($_GET['type']) && $_GET['type'] == 'physical') {
            $s_p = 'no';
          }
          if (isset($_GET['type']) && $_GET['type'] == 'download') {
            $s_d = 'no';
          }
        }
        ?>
        <li role="separator" class="divider"></li>
        <li><a href="?p=sales-view&amp;stock_adj=<?php echo $saleID; ?>"><i class="fa fa-bar-chart fa-fw"></i> <?php echo $msg_admin_viewsale3_0[24]; ?></a></li>
        <li role="separator" class="divider"></li>
        <?php
        if ($s_p == 'yes') {
        ?>
        <li><a href="?p=add&amp;sale=<?php echo $saleID; ?>&amp;type=physical"><i class="fa fa-plus fa-fw"></i> <?php echo $msg_admin_viewsale3_0[3]; ?></a></li>
        <?php
        }
        if ($s_d == 'yes') {
        ?>
        <li><a href="?p=add&amp;sale=<?php echo $saleID; ?>&amp;type=download"><i class="fa fa-plus fa-fw"></i> <?php echo $msg_admin_viewsale3_0[4]; ?></a></li>
        <?php
        }
        ?>
        <li role="separator" class="divider"></li>
        <?php
        if (defined('INCPL_SALE')) {
        ?>
        <li><a href="?p=sales-incomplete"><i class="fa fa-minus-circle fa-fw"></i> <?php echo $msg_header21; ?></a></li>
        <?php
        } else {
        ?>
        <li><a href="?p=sales"><i class="fa fa-money fa-fw"></i> <?php echo $msg_sales3; ?></a></li>
        <?php
        }
        ?>
      </ul>
    </div>

    <span class="quicklinktext"><i class="fa fa-<?php echo $qLinksIcon; ?> fa-fw"></i> - #<?php echo mc_saleInvoiceNumber($SLE->invoiceNo, $SETTINGS) . ' - ' . mc_cleanData($SLE->bill_1); ?></span>
  </div>