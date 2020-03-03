<?php if (!defined('PARENT') || !isset($_GET['q'])) { die('Permission Denied'); }
// Search routines..
$SQL = array(array('',0),array('',0),array('',0),array('',0));
if ($_GET['q']) {
  $sch       = mc_safeSQL($_GET['q']);
  if (in_array('sales', $sysCartUser[3]) || $sysCartUser[1] != 'restricted') {
    $SQL[0][0] = 'WHERE (`couponCode` = \'' . $sch . '\' OR `gatewayID` LIKE \'%' . $sch . '%\' OR `invoiceNo` = \'' . ltrim(mc_saleInvoiceNumber($sch, $SETTINGS), '0') . '\' OR `saleNotes` LIKE \'%' . $sch . '%\' OR `bill_1` LIKE \'%' . $sch . '%\' OR `bill_2` LIKE \'%' . $sch . '%\' OR `ship_1` LIKE \'%' . $sch . '%\' OR `ship_2` LIKE \'%' . $sch . '%\')';
  }
  if (in_array('manage-products', $sysCartUser[3]) || $sysCartUser[1] != 'restricted') {
    $SQL[1][0] = 'WHERE (`'.DB_PREFIX.'products`.`id` = \'' . (int) $sch . '\' OR `pName` LIKE \'%' . $sch . '%\' OR `pDescription` LIKE \'%' . $sch . '%\' OR `pNotes` LIKE \'%' . $sch . '%\' OR `pCode` LIKE \'%' . $sch . '%\')';
  }
  if (in_array('accounts', $sysCartUser[3]) || $sysCartUser[1] != 'restricted') {
    $SQL[2][0] = 'WHERE (`name` LIKE \'%' . $sch . '%\' OR `email` LIKE \'%' . $sch . '%\' OR `notes` LIKE \'%' . $sch . '%\' OR `reason` LIKE \'%' . $sch . '%\')';
  }
  if (in_array('users', $sysCartUser[3]) || $sysCartUser[1] != 'restricted') {
    $SQL[3][0] = 'WHERE (`userName` LIKE \'%' . $sch . '%\' OR `userEmail` LIKE \'%' . $sch . '%\')';
  }
  // Queries..
  if (in_array('sales', $sysCartUser[3]) || $sysCartUser[1] != 'restricted') {
    $q1 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`purchaseDate`,'" . $SETTINGS->mysqlDateFormat . "') AS `sdate`
          FROM `" . DB_PREFIX . "sales`
          {$SQL[0][0]}
          AND `saleConfirmation` = 'yes'
          ORDER BY `id` DESC
          ") or die(mc_MySQLError(__LINE__,__FILE__));
    $SQL[0][1] = mysqli_num_rows($q1);
  }
  if (in_array('manage-products', $sysCartUser[3]) || $sysCartUser[1] != 'restricted') {
    $q2 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
          DATE_FORMAT(`pDateAdded`,'" . $SETTINGS->mysqlDateFormat . "') AS `adate`,
          `" . DB_PREFIX . "products`.`id` AS `pid`
          FROM `" . DB_PREFIX . "products`
          LEFT JOIN `" . DB_PREFIX . "prod_category`
          ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_category`.`product`
          {$SQL[1][0]}
          GROUP BY `" . DB_PREFIX . "products`.`id`
          ORDER BY `pName`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
    $SQL[1][1] = mysqli_num_rows($q2);
  }
  if (in_array('accounts', $sysCartUser[3]) || $sysCartUser[1] != 'restricted') {
    $q3 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
          DATE_FORMAT(`created`,'" . $SETTINGS->mysqlDateFormat . "') AS `cdate`,
          (SELECT count(*) FROM `" . DB_PREFIX . "sales`
           WHERE `" . DB_PREFIX . "sales`.`account` = `" . DB_PREFIX . "accounts`.`id`
           AND `" . DB_PREFIX . "sales`.`saleConfirmation` = 'yes'
          ) AS `saleCount`,
          (SELECT SUM(`grandTotal`) FROM `" . DB_PREFIX . "sales`
           WHERE `" . DB_PREFIX . "sales`.`account` = `" . DB_PREFIX . "accounts`.`id`
           AND `" . DB_PREFIX . "sales`.`saleConfirmation` = 'yes'
          ) AS `salesRevenue`
          FROM `" . DB_PREFIX . "accounts`
          {$SQL[2][0]}
          ORDER BY `name`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
    $SQL[2][1] = mysqli_num_rows($q3);
  }
  if (in_array('users', $sysCartUser[3]) || $sysCartUser[1] != 'restricted') {
    $q4 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "users`
          {$SQL[3][0]}
          ORDER BY `userName`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
    $SQL[3][1] = mysqli_num_rows($q4);
  }
}
?>
<div id="content">

  <div class="form-group">
    <form method="get" action="index.php">
    <div class="form-group input-group">
      <input type="hidden" name="p" value="globsearch">
      <input type="text" class="form-control inputboxborderleft" name="q" value="<?php echo mc_safeHTML($_GET['q']); ?>">
      <span class="input-group-addon"><i class="fa fa-search fa-fw"></i></span>
    </div>
    </form>
  </div>

  <hr>

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#one" data-toggle="tab"><i class="fa fa-money fa-fw"></i><span class="hidden-xs"> <?php echo $msg_globalsearch2; ?> (<?php echo ($SQL[0][1] > 0 ? @number_format($SQL[0][1]) : '0'); ?>)</span></a></li>
        <li><a href="#two" data-toggle="tab"><i class="fa fa-cube fa-fw"></i><span class="hidden-xs"> <?php echo $msg_globalsearch3; ?> (<?php echo ($SQL[1][1] > 0 ? @number_format($SQL[1][1]) : '0'); ?>)</span></a></li>
        <li><a href="#three" data-toggle="tab"><i class="fa fa-user fa-fw"></i><span class="hidden-xs"> <?php echo $msg_globalsearch4; ?> (<?php echo ($SQL[2][1] > 0 ? @number_format($SQL[2][1]) : '0'); ?>)</span></a></li>
        <li><a href="#four" data-toggle="tab"><i class="fa fa-group fa-fw"></i><span class="hidden-xs"> <?php echo $msg_globalsearch5; ?> (<?php echo ($SQL[3][1] > 0 ? @number_format($SQL[3][1]) : '0'); ?>)</span></a></li>
      </ul>
    </div>
  </div>

  <div class="row" style="margin-top:10px">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="tab-content">
        <div class="tab-pane active in" id="one">
          <?php
          if ($SQL[0][1] > 0) {
          while ($SALES = mysqli_fetch_object($q1)) {
          $isShip = 'no';
          if (mc_rowCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\' AND `productType` = \'physical\'')>0) {
            $isShip = 'yes';
          }
          ?>
          <div class="panel panel-default" id="salearea_<?php echo $SALES->id; ?>">
            <div class="panel-body">

              <div class="table-responsive hidden-xs">
                <table class="table" style="border-top:0;margin:0;padding:0">
                <tbody>
                  <tr>
                    <td style="border-top:0"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b></td>
                    <td style="border-top:0"><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?></td>
                    <td style="border-top:0"><?php echo $SALES->sdate; ?></td>
                    <td style="border-top:0"><?php echo mc_paymentMethodName($SALES->paymentMethod); ?></td>
                    <td style="border-top:0"><?php echo mc_statusText($SALES->paymentStatus); ?></td>
                    <td style="border-top:0"><b><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></b></td>
                  </tr>
                </tbody>
                </table>
              </div>

              <div class="hidden-sm hidden-md hidden-lg">
              <?php echo $SALES->sdate; ?><br>
              <?php echo mc_statusText($SALES->paymentStatus); ?><br>
              <?php echo mc_paymentMethodName($SALES->paymentMethod); ?>
              <div class="manageCost"><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></div>
              </div>

              <div id="slsp_<?php echo $SALES->id; ?>" style="display:none">
              <hr>
              <?php
              echo str_replace(array('{id}','{count}'),array($SALES->id,mc_sumCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\'','productQty',true)),$msg_sales31);
              ?><br><br>
              <a href="?p=sales&amp;export=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales4); ?>"><i class="fa fa-save fa-fw"></i> <?php echo $msg_sales4; ?></a>
              <?php
              // Show personalisation link if there are personalised products..
              if (mc_rowCount('purch_pers WHERE `saleID` = \''.mc_digitSan($SALES->id).'\'')>0) {
              ?>
              <br><a href="?p=sales-view&amp;view-personalisation=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales37); ?>" onclick="window.open(this);return false"><i class="fa fa-print fa-fw"></i> <?php echo $msg_admin3_0[29]; ?></a>
              <?php
              }
              // Show downloads link if there are downloadable products..
              if (mc_rowCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\' AND `productType` = \'download\'')>0) {
              ?>
              <br><a href="?p=downloads&amp;sale=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[7]); ?>"><i class="fa fa-download fa-fw"></i> <?php echo $msg_admin_viewsale3_0[7]; ?></a>
              <?php
              }
              if ($isShip == 'yes') {
              ?>
              <br><a href="?p=sales-view&amp;shipLabel=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales38); ?>" onclick="window.open(this);return false"><i class="fa fa-tags fa-fw"></i> <?php echo $msg_viewsale105; ?></a>
              <?php
              }
              if (!in_array($SALES->gateparams, array(null,''))) {
              ?>
              <br><a href="?p=sales-view&amp;gatewayParams=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_viewsale107); ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_PERS_HEIGHT; ?>','<?php echo DIVWIN_PERS_WIDTH; ?>',this.title);return false;"><i class="fa fa-list-alt fa-fw"></i> <?php echo $msg_viewsale107; ?></a>
              <?php
              }
              ?>
              <br><a href="?p=sales-view&amp;stock_adj=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[24]); ?>"><i class="fa fa-bar-chart fa-fw"></i> <?php echo $msg_admin_viewsale3_0[24]; ?></a>
              </div>
            </div>
            <div class="panel-footer">
              <span class="hidden-sm hidden-md hidden-lg"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b> - </span>
              <a href="?p=sales-view&amp;sale=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales13); ?>"><i class="fa fa-pencil fa-fw"></i></a>
              <a href="?p=invoice&amp;sale=<?php echo $SALES->id; ?>" onclick="window.open(this);return false" title="<?php echo mc_cleanDataEntVars($msg_sales5); ?>"><i class="fa fa-file-text-o fa-fw"></i></a>
              <?php
              // Show packing slip link if there are physical products..
              if ($isShip == 'yes') {
              ?>
              <a href="?p=packing-slip&amp;sale=<?php echo $SALES->id; ?>" onclick="window.open(this);return false" title="<?php echo mc_cleanDataEntVars($msg_sales6); ?>"><i class="fa fa-truck fa-fw"></i></a>
              <?php
              }
              ?>
              <a href="?p=sales-update&amp;sale=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales2); ?>"><i class="fa fa-history fa-fw"></i></a>
              &nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-down fa-fw" style="cursor:pointer" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[10]); ?>" onclick="mc_toggleMoreOptions(this,'<?php echo $SALES->id; ?>','slsp_')"></i>
            </div>
          </div>
          <?php
          }
          } else {
          ?>
          <span class="noData"><?php echo $msg_globalsearch6; ?></span>
          <?php
          }
          ?>
        </div>
        <div class="tab-pane fade" id="two">
          <?php
          if ($SQL[1][1] > 0) {
          while ($PRODUCTS = mysqli_fetch_object($q2)) {
          $img  = mc_storeProductImg($PRODUCTS->pid,$PRODUCTS);
          $atRs = mc_rowCount('attributes WHERE `productID` = \''.$PRODUCTS->pid.'\'');
          ?>
          <div class="panel panel-default" id="prwrap_<?php echo $PRODUCTS->pid; ?>">
            <div class="panel-body">
              <span style="float:right" class="productimg hidden-xs"><?php echo $img; ?></span>
              <b><?php echo mc_safeHTML($PRODUCTS->pName); ?></b><br><br>
              <?php echo $msg_productmanage18; ?>: <?php echo ($PRODUCTS->pCode ? $PRODUCTS->pCode : 'N/A'); ?><br>
              <?php echo $msg_productmanage13; ?>: <span id="stock_<?php echo $PRODUCTS->pid; ?>" onclick="mc_updateStock('<?php echo $PRODUCTS->pid; ?>','<?php echo ($PRODUCTS->pStock>0 ? number_format($PRODUCTS->pStock) : '0'); ?>','<?php echo mc_filterJS($msg_admin3_0[39]); ?>')" class="prodstockchange"><?php echo ($PRODUCTS->pStock>0 ? number_format($PRODUCTS->pStock) : '0'); ?></span><br>
              <div class="manageCost"><?php echo ($PRODUCTS->pOffer>0 ? '<del>'.mc_currencyFormat(mc_formatPrice($PRODUCTS->pPrice)).'</del> '.mc_currencyFormat(mc_formatPrice($PRODUCTS->pOffer)) : mc_currencyFormat(mc_formatPrice($PRODUCTS->pPrice))); ?></div>
              <div id="prd_<?php echo $PRODUCTS->pid; ?>" style="display:none">
              <hr>
              <?php echo $msg_productmanage11; ?>: <?php echo $PRODUCTS->adate; ?>, <?php echo $msg_productmanage21; ?>: <?php echo ($PRODUCTS->pVisits>0 ? number_format($PRODUCTS->pVisits) : '0'); ?>, <span onclick="mc_enableDisableProduct('<?php echo $PRODUCTS->pid; ?>')" id="endis_<?php echo $PRODUCTS->pid; ?>" style="cursor:pointer" title="<?php echo mc_safeHTML($PRODUCTS->pEnable=='yes' ? $msg_productmanage39 : $msg_productmanage38); ?>"><?php echo ($PRODUCTS->pEnable=='yes' ? $msg_productmanage22 : $msg_productmanage23); ?></span><br>
              <?php echo $msg_productmanage67 . ': ' . $PRODUCTS->pid; ?><br><br>
              <a href="?p=product-pictures&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage14); ?>"><i class="fa fa-camera fa-fw"></i> <?php echo $msg_productmanage14; ?></a> (<?php echo mc_rowCount('pictures WHERE `product_id` = \''.$PRODUCTS->pid.'\''); ?>)<br>
              <a href="?p=product-related&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage17); ?>"><i class="fa fa-exchange fa-fw"></i> <?php echo $msg_productmanage17; ?></a> (<?php echo mc_rowCount('prod_relation WHERE `product` = \''.$PRODUCTS->pid.'\''); ?>)<br>
              <?php
              if (PRODUCT_MP3_PREVIEWS) {
              ?>
              <a href="?p=product-mp3&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage19); ?>"><i class="fa fa-music fa-fw"></i> <?php echo $msg_productmanage19; ?></a> (<?php echo mc_rowCount('mp3 WHERE `product_id` = \''.$PRODUCTS->pid.'\''); ?>)<br>
              <?php
              }
              if ($PRODUCTS->pDownload=='no') {
              ?>
              <a href="?p=product-personalisation&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage20); ?>"><i class="fa fa-quote-left fa-fw"></i> <?php echo $msg_productmanage20; ?></a> (<?php echo mc_rowCount('personalisation WHERE `productID` = \''.$PRODUCTS->pid.'\''); ?>)<br>
              <a href="?p=product-attributes&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage7); ?>"><i class="fa fa-pencil-square-o fa-fw"></i> <?php echo $msg_productmanage7; ?></a> (<?php echo mc_rowCount('attributes WHERE `productID` = \''.$PRODUCTS->pid.'\''); ?>)<br>
              <?php
              if ($atRs > 0) {
              ?>
              <a href="?p=copy-attributes&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_prodattributes24); ?>"><i class="fa fa-clone fa-fw"></i> <?php echo $msg_prodattributes24; ?></a><br>
              <?php
              }
              }
              ?>
              </div>
            </div>
            <div class="panel-footer">
              <a href="../?pd=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage8); ?>" onclick="window.open(this);return false"><i class="fa fa-desktop fa-fw"></i></a>
              <a href="?p=add-product&amp;edit=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9); ?>"><i class="fa fa-pencil fa-fw"></i></a>
              <a href="?p=add-product&amp;copyp=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage6); ?>"><i class="fa fa-copy fa-fw"></i></a>
              <a href="?p=manage-products&amp;notes=<?php echo $PRODUCTS->pid; ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_NOTES_HEIGHT; ?>','<?php echo DIVWIN_NOTES_WIDTH; ?>',this.title);return false;" title="<?php echo mc_cleanDataEntVars($msg_productmanage62); ?>"><i class="fa fa-file-text-o fa-fw"></i></a>
              &nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-down fa-fw" style="cursor:pointer" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[10]); ?>" onclick="mc_toggleMoreOptions(this,'<?php echo $PRODUCTS->pid; ?>')"></i>
            </div>
          </div>
          <?php
          }
          } else {
          ?>
          <span class="noData"><?php echo $msg_globalsearch7; ?></span>
          <?php
          }
          ?>
        </div>
        <div class="tab-pane fade" id="three">
          <?php
          if ($SQL[2][1] > 0) {
          while ($ACCOUNTS = mysqli_fetch_object($q3)) {
          ?>
          <div class="panel panel-default" id="accarea_<?php echo $ACCOUNTS->id; ?>">
            <div class="panel-body">

              <div class="table-responsive hidden-xs">
                <table class="table accitemtablenodel" style="margin:0;padding:0">
                <tbody>
                  <tr>
                    <td><i class="fa fa-user fa-fw"></i> <?php echo mc_safeHTML($ACCOUNTS->name); ?></td>
                    <td><?php echo mc_safeHTML($ACCOUNTS->email); ?></td>
                    <td><?php echo str_replace('{count}',@number_format($ACCOUNTS->saleCount),$msg_accounts5); ?></td>
                    <td><?php echo mc_currencyFormat(mc_formatPrice($ACCOUNTS->salesRevenue,true)); ?></td>
                  </tr>
                </tbody>
                </table>
              </div>

              <div class="hidden-sm hidden-md hidden-lg">
              <i class="fa fa-user fa-fw"></i> <?php echo mc_safeHTML($ACCOUNTS->name); ?><br><br>
              <?php echo mc_safeHTML($ACCOUNTS->email); ?><br>
              <?php echo str_replace('{count}',@number_format($ACCOUNTS->saleCount),$msg_accounts5); ?><br>
              <?php echo mc_currencyFormat(mc_formatPrice($ACCOUNTS->salesRevenue,true)); ?>

              </div>

              <div id="acstab_<?php echo $ACCOUNTS->id; ?>" style="display:none">
              <hr>
              <?php echo $msg_accounts11; ?>: <?php echo $ACCOUNTS->cdate; ?><br>
              <?php echo $msg_accounts12; ?>: <a href="?p=accounts&amp;accstatus=<?php echo $ACCOUNTS->id; ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_NOTES_HEIGHT; ?>','<?php echo DIVWIN_NOTES_WIDTH; ?>',this.title);return false;"><?php echo ($ACCOUNTS->enabled == 'yes' ? $msg_accounts13 : $msg_accounts14); ?></a><br>
              <?php echo $msg_accounts15; ?>: <?php echo ($ACCOUNTS->ip ? $ACCOUNTS->ip : 'N/A'); ?>
              <hr>
              <a href="?p=accounts&amp;message=<?php echo $ACCOUNTS->id; ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_NOTES_HEIGHT; ?>','<?php echo DIVWIN_NOTES_WIDTH; ?>',this.title);return false;"><i class="fa fa-bullhorn fa-fw"></i> <?php echo $msg_accounts29; ?></a>
              </div>
            </div>
            <div class="panel-footer">
              <a href="?p=add-account&amp;edit=<?php echo $ACCOUNTS->id; ?>" title="<?php echo mc_safeHTML($msg_accounts6); ?>"><i class="fa fa-pencil fa-fw"></i></a>
              <a href="?p=sales&amp;ahis=<?php echo $ACCOUNTS->id; ?>" title="<?php echo mc_safeHTML($msg_accounts7); ?>"><i class="fa fa-shopping-basket fa-fw"></i></a>
              <a href="?p=accounts&amp;notes=<?php echo $ACCOUNTS->id; ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_NOTES_HEIGHT; ?>','<?php echo DIVWIN_NOTES_WIDTH; ?>',this.title);return false;" title="<?php echo mc_safeHTML($msg_accounts8); ?>"><i class="fa fa-file-text-o fa-fw"></i></a>
              &nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-down fa-fw" style="cursor:pointer" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[10]); ?>" onclick="mc_toggleMoreOptions(this,'<?php echo $ACCOUNTS->id; ?>','acstab_')"></i>
            </div>
          </div>
          <?php
          }
          } else {
          ?>
          <span class="noData"><?php echo $msg_globalsearch8; ?></span>
          <?php
          }
          ?>
        </div>
        <div class="tab-pane fade" id="four">
          <?php
          if ($SQL[3][1] > 0) {
          while ($USERS = mysqli_fetch_object($q4)) {
          ?>
          <div class="panel panel-default">
            <div class="panel-body">
            <b><?php echo mc_safeHTML($USERS->userName); ?></b> <?php echo ($USERS->userEmail ? '(' . mc_safeHTML($USERS->userEmail) . ')' : ''); ?><br><br>
            <?php echo str_replace(array('{type}','{del_priv}','{enabled}','{notify}','{tweet}'),array(mc_userManagementType($USERS->userType),($USERS->userPriv=='yes' ? $msg_script5 : $msg_script6),($USERS->enableUser=='yes' ? $msg_script5 : $msg_script6),($USERS->userNotify=='yes' ? $msg_script5 : $msg_script6),($USERS->tweet=='yes' ? $msg_script5 : $msg_script6)),$msg_users17); ?>
            </div>
            <div class="panel-footer">
            <a href="?p=users&amp;edit=<?php echo $USERS->id; ?>"><i class="fa fa-pencil fa-fw"></i></a>
            </div>
          </div>
          <?php
          }
          } else {
          ?>
          <span class="noData"><?php echo $msg_globalsearch9; ?></span>
          <?php
          }
          ?>
        </div>
      </div>
    </div>
  </div>

</div>