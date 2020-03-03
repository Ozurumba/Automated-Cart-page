<?php if (!defined('PARENT')) { die('Permission Denied'); }
$payStatuses    = mc_loadDefaultStatuses();
$graph          = $MCSALE->homepageGraphData();
$lineOne        = $graph[0];
$lineTwo        = $graph[1];
$lineThree      = $graph[2];
$boomOne        = explode(',',$graph[0]);
$boomTwo        = explode(',',$graph[1]);
$boomThree      = explode(',',$graph[2]);
$ticks          = $graph[3];
$range          = (isset($_GET['range']) && in_array($_GET['range'],array('week','month','year','1m','3m','6m','last')) ? $_GET['range'] : ADMIN_HOME_DEFAULT_SALES_VIEW);
$displayTotals  = $MCSALE->homepageTotalDisplay($range);
?>
<div id="content">

  <?php
  include(PATH.'templates/custom-user-message.php');
  ?>

  <div class="row">
    <div class="col-xs-12 col-md-8 col-lg-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <span style="float:right"><a href="#" onclick="jQuery('#ranges').slideToggle();return false"><i class="fa fa-calendar fa-fw"></i></a></span><?php echo $msg_main17; ?>
        </div>
        <div class="panel-body">
          <div id="ranges" style="display:none">
            <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
              <option value="?range=week"<?php echo ($range=='week' ? ' selected="selected"' : ''); ?>><?php echo $msg_main2; ?></option>
              <option value="?range=month"<?php echo ($range=='month' ? ' selected="selected"' : ''); ?>><?php echo $msg_main9; ?></option>
              <option value="?range=year"<?php echo ($range=='year' ? ' selected="selected"' : ''); ?>><?php echo $msg_main6; ?></option>
              <option value="0" disabled="disabled">- - - - - - - - - -</option>
              <option value="?range=1m"<?php echo ($range=='1m' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_homescreen[6]; ?></option>
              <option value="?range=3m"<?php echo ($range=='3m' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_homescreen[0]; ?></option>
              <option value="?range=6m"<?php echo ($range=='6m' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_homescreen[1]; ?></option>
              <option value="?range=last"<?php echo ($range=='last' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_homescreen[2]; ?></option>
            </select>
            <hr>
          </div>
          <div class="graph" id="graph">
           <?php
           if (array_sum($boomOne)>0 || array_sum($boomTwo)>0 || array_sum($boomThree)>0) {
           ?>
           <div id="chartgraph"></div>
           <div class="graphloader"></div>
           <script>
           //<![CDATA[
           jQuery(document).ready(function() {
             setTimeout(function() {
               jQuery('.graphloader').remove();
               line1 = [<?php echo $lineOne; ?>];
               line2 = [<?php echo $lineTwo; ?>];
               line3 = [<?php echo $lineThree; ?>];
               ticks = [<?php echo $ticks; ?>];
               plot1 = jQuery.jqplot('chartgraph', [line1,line2,line3], {
                 grid: {
                   borderWidth: 0,
                   shadow: false,
                   gridLineColor: '#eeeeee',
                   backgroundColor: '#fcfcfc',
                   borderColor: '#eeeeee'
                 },
                 axes: {
                   yaxis: {
                     min: 0,
                     size: 2,
                     tickOptions: {
                       formatString: '%d',
                       fontSize: '8pt'
                     }
                   },
                   xaxis: {
                     rendererOptions: {
                       tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer
                     },
                     tickOptions: {
                       fontSize: '8pt'
                     },
                     size: 2,
                     ticks:ticks,
                     renderer: jQuery.jqplot.CategoryAxisRenderer
                   }
                 },
                 series: [{
                   lineWidth: 1
                 },{
                   lineWidth: 1
                 }],
                 legend: {
                   show: false
                 }
               });
             }, 2000);
           });
           //]]>
           </script>
           <?php
           } else {
           ?>
           <div class="chartgraph_nostats">
             <p><i class="fa fa-times fa-fw"></i>
             <?php
             switch($range) {
               // This week..
               case 'week':
                 $txt = $msg_main2;
                 break;
               // This month..
               case 'month':
                 $txt = $msg_main9;
                 break;
               // This year..
               case 'year':
                 $txt = $msg_main6;
                 break;
               // Last month..
               case '1m':
                 $txt = $msg_admin_homescreen[6];
                 break;
               // Last 3 months..
               case '3m':
                 $txt = $msg_admin_homescreen[0];
                 break;
               // Last 6 months..
               case '6m':
                 $txt = $msg_admin_homescreen[1];
                 break;
               // Last year..
               case 'last':
                 $txt = $msg_admin_homescreen[2];
                 break;
             }
             echo str_replace('{time}', $txt, $msg_admin3_0[35]);
             ?>
             </p>
           </div>
           <?php
           }
           ?>
           </div>
        </div>
      </div>
    </div>

    <div class="col-xs-12 col-md-4 col-lg-4">

      <?php
      // Show for beta ONLY..
      if (defined('DEV_BETA') && DEV_BETA != 'no') {
      ?>
      <div class="alert alert-warning" style="border-width:2px">
        <span class="pull-right"><i class="fa fa-flask fa-fw"></i></span>
        <b>BETA VERSION</b>
        <hr>
        <i class="fa fa-hourglass fa-fw"></i> Currently at beta: <?php echo DEV_BETA; ?><br>
        <i class="fa fa-calendar fa-fw"></i> Beta Expiry: <?php echo date('j M Y', strtotime(DEV_BETA_EXP)); ?>
        <hr>
        <i class="fa fa-arrow-right fa-fw"></i> <a href="http://www.maianbeta.com/forum/" onclick="window.open(this);return false">View Beta Forum</a>
      </div>
      <?php
      }

      if ($sysCartUser[1] != 'restricted' || (in_array('sales', $sysCartUser[3]) || in_array('manage-products', $sysCartUser[3]) || in_array('accounts', $sysCartUser[3]) || in_array('users', $sysCartUser[3]))) {
      ?>
      <div class="form-group">
        <form method="get" action="index.php">
        <div class="form-group input-group">
          <input type="hidden" name="p" value="globsearch">
          <input type="text" class="form-control inputboxborderleft" name="q" placeholder="<?php echo mc_safeHTML($msg_admin3_0[53]); ?>">
          <span class="input-group-addon"><i class="fa fa-search fa-fw"></i></span>
        </div>
        </form>
      </div>
      <?php
      }
      ?>

      <div class="panel panel-default">
        <div class="panel-body">
          <i class="fa fa-user fa-fw"></i> <?php echo mc_safeHTML($sysCartUser[0]); ?><br>
          <i class="fa fa-envelope-o fa-fw"></i> <?php echo mc_safeHTML(($sysCartUser[1] != 'restricted' ? $SETTINGS->email : '')); ?>
          <?php
          if (defined('DEV_BETA') && DEV_BETA != 'no') {
            $noShowVer = true;
          }
          if ($sysCartUser[1] != 'restricted' && DISPLAY_SOFTWARE_VERSION_CHECK && !isset($noShowVer)) {
					?>
          <hr>
          <i class="fa fa-caret-right fa-fw"></i><a href="index.php?versionCheck=yes"><?php echo $msg_header17; ?></a>
          <?php
          }
          ?>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">
          <i class="fa fa-money fa-fw"></i>
          <?php
          switch($range) {
            // This week..
            case 'week':
              echo $msg_main19;
              break;
            // This month..
            case 'month':
              echo $msg_main20;
              break;
            // This year..
            case 'year':
              echo $msg_main21;
              break;
            // Last month..
            case '1m':
              echo $msg_admin_homescreen[7];
              break;
            // Last 3 months..
            case '3m':
              echo $msg_admin_homescreen[3];
              break;
            // Last 6 months..
            case '6m':
              echo $msg_admin_homescreen[4];
              break;
            // Last year..
            case 'last':
              echo $msg_admin_homescreen[5];
              break;
          }
          ?>
        </div>
        <div class="panel-body maintotals">
          <b><?php echo mc_currencyFormat(mc_formatPrice($displayTotals[2],true)); ?></b>
        </div>
      </div>

      <?php
      include(PATH.'templates/system/main-quick-links.php');
      ?>
    </div>
  </div>

  <?php
  // Determine what tabs are showing..
  $homeTabs = array();
  $tabContent = array(
    'pend' => 'active in',
    'comp' => 'fade',
    'stat' => 'fade'
  );
  if (SHOW_PENDING_SALES_ON_MAIN_PAGE) {
    $homeTabs['pending'] = array(
      'txt' => $msg_sales34,
      'icon' => 'hand-stop-o',
      'count' => mc_rowCount('sales',' WHERE `saleConfirmation` = \'yes\' AND `paymentStatus` IN(\'pending\',\'shipping\')'),
      'tab' => 'pending'
    );
  }
  if (SHOW_COMPLETED_SALES_ON_MAIN_PAGE > 0) {
    $homeTabs['completed'] = array(
      'txt' => str_replace('{count}',SHOW_COMPLETED_SALES_ON_MAIN_PAGE,$msg_sales33),
      'icon' => 'check',
      'count' => mc_rowCount('sales',' WHERE `saleConfirmation` = \'yes\' AND `paymentStatus` = \'completed\''),
      'tab' => 'completed'
    );
    if (!SHOW_PENDING_SALES_ON_MAIN_PAGE) {
      $tabContent['pend'] = 'fade';
      $tabContent['comp'] = 'active in';
    }
  }
  if (!SHOW_PENDING_SALES_ON_MAIN_PAGE && SHOW_COMPLETED_SALES_ON_MAIN_PAGE == 0) {
    $tabContent = array(
      'pend' => 'fade',
      'comp' => 'fade',
      'stat' => 'active in'
    );
  }
  $qPay = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
          WHERE `homepage` = 'yes'
          ORDER BY `statname`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($qPay) > 0) {
    while ($PST = mysqli_fetch_object($qPay)) {
      $homeTabs['other'][] = array(
        'txt' => mc_safeHTML($PST->statname),
        'icon' => 'file-text-o',
        'id' => $PST->id,
        'count' => mc_rowCount('sales',' WHERE `saleConfirmation` = \'yes\' AND `paymentStatus` = \'' . $PST->id . '\''),
        'tab' => 'other' . $PST->id
      );
    }
  }

  if (!empty($homeTabs)) {
  ?>
  <div class="row" style="margin-top:20px">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <ul class="nav nav-tabs">
        <?php
        $hkrun = 0;
        foreach (array_keys($homeTabs) AS $hK) {
          $slot = ++$hkrun;
          switch($hK) {
            case 'other':
              ?>
              <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-file-text-o fa-fw" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[37]); ?>"></i> <span class="hidden-sm hidden-xs"><?php echo $msg_admin3_0[37]; ?></span> <span class="caret"></span></a>
              <ul class="dropdown-menu dropdown-menu-right verysmalladjustment" role="menu">
                <?php
                for ($i=0; $i<count($homeTabs['other']); $i++) {
                ?>
                <li><a href="#<?php echo $homeTabs[$hK][$i]['tab']; ?>" data-toggle="tab"><?php echo $homeTabs[$hK][$i]['txt']; ?> (<?php echo $homeTabs[$hK][$i]['count']; ?>)</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
              <?php
              break;
            default:
              ?>
              <li<?php echo ($slot == '1' ? ' class="active"' : ''); ?>><a href="#<?php echo $homeTabs[$hK]['tab']; ?>" data-toggle="tab"><i class="fa fa-<?php echo $homeTabs[$hK]['icon']; ?> fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $homeTabs[$hK]['txt']; ?></span> (<?php echo $homeTabs[$hK]['count']; ?>)</a></li>
              <?php
              break;
          }
        }
        ?>
      </ul>
    </div>
  </div>

  <div class="row" style="margin-top:10px">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="tab-content">
        <?php
        if (SHOW_PENDING_SALES_ON_MAIN_PAGE) {
        ?>
        <div class="tab-pane <?php echo $tabContent['pend']; ?>" id="pending">
            <?php
            $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`purchaseDate`,'" . $SETTINGS->mysqlDateFormat . "') AS `sdate`
            FROM `" . DB_PREFIX . "sales`
            WHERE `saleConfirmation` = 'yes'
            AND `paymentStatus`     IN('pending','shipping')
            ORDER BY `id` DESC
            ") or die(mc_MySQLError(__LINE__,__FILE__));
            if (mysqli_num_rows($query)>0) {
            while ($SALES = mysqli_fetch_object($query)) {
            $isShip = 'no';
            if (mc_rowCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\' AND `productType` = \'physical\'')>0) {
              $isShip = 'yes';
            }
            ?>
            <div class="panel panel-default" id="salearea_<?php echo $SALES->id; ?>">
              <div class="panel-body">

                <div class="table-responsive hidden-xs">
                  <table class="table salesitemtablehome" style="margin:0;padding:0">
                  <tbody>
                    <tr>
                      <td><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b></td>
                      <td><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?></td>
                      <td><?php echo $SALES->sdate; ?></td>
                      <td><?php echo mc_paymentMethodName($SALES->paymentMethod); ?></td>
                      <td><?php echo mc_statusText($SALES->paymentStatus); ?></td>
                      <td><b><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></b></td>
                    </tr>
                  </tbody>
                  </table>
                </div>

                <div class="hidden-sm hidden-md hidden-lg">
                <span class="hidden-xs"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b> - </span><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?><br><br>

                <?php echo $SALES->sdate; ?><br>
                <?php echo mc_statusText($SALES->paymentStatus); ?><br>
                <?php echo mc_paymentMethodName($SALES->paymentMethod); ?>
                <div class="manageCost"><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></div>
                </div>

                <div id="prd_<?php echo $SALES->id; ?>" style="display:none">
                <hr>
                <?php
                echo str_replace(array('{id}','{count}'),array($SALES->id,mc_sumCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\'','productQty',true)),$msg_sales31);
                echo '<br>' . $msg_sales_view[1] . ': ' . mc_salePlatform($SALES->platform, $msg_platforms);
                if ($SALES->wishlist > 0) {
                ?><br><i class="fa fa-heart fa-fw"></i> <?php echo $msg_sales_screen[0]; ?>
                <?php
                }
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
                <?php
                if ($uDel == 'yes') {
                ?>
                <br><a href="?p=sales&amp;delete=<?php echo $SALES->id; ?>" onclick="mc_confSaleDeletion('<?php echo mc_filterJS($msg_javascript45); ?>','<?php echo $SALES->id; ?>');return false" title="<?php echo mc_cleanDataEntVars($msg_script10); ?>"><i class="fa fa-times fa-fw mc-red"></i> <?php echo $msg_script10; ?></a>
                <?php
                }
                ?>
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
                &nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-down fa-fw" style="cursor:pointer" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[10]); ?>" onclick="mc_toggleMoreOptions(this,'<?php echo $SALES->id; ?>')"></i>
              </div>
            </div>
            <?php
            }
            } else {
            ?>
            <p class="noData"><?php echo $msg_sales17; ?></p>
            <?php
            }
            ?>
        </div>
        <?php
        }
        if (SHOW_COMPLETED_SALES_ON_MAIN_PAGE>0) {
        ?>
        <div class="tab-pane <?php echo $tabContent['comp']; ?>" id="completed">
          <?php
            $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`purchaseDate`,'" . $SETTINGS->mysqlDateFormat . "') AS `sdate`
            FROM `" . DB_PREFIX . "sales`
            WHERE `saleConfirmation` = 'yes'
            AND `paymentStatus`     IN('completed')
            ORDER BY `id` DESC
            LIMIT " . SHOW_COMPLETED_SALES_ON_MAIN_PAGE
            ) or die(mc_MySQLError(__LINE__,__FILE__));
            if (mysqli_num_rows($query)>0) {
            while ($SALES = mysqli_fetch_object($query)) {
            $isShip = 'no';
            if (mc_rowCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\' AND `productType` = \'physical\'')>0) {
              $isShip = 'yes';
            }
            ?>
            <div class="panel panel-default" id="salearea_<?php echo $SALES->id; ?>">
              <div class="panel-body">

                <div class="table-responsive hidden-xs">
                  <table class="table salesitemtablehome" style="margin:0;padding:0">
                  <tbody>
                    <tr>
                      <td><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b></td>
                      <td><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?></td>
                      <td><?php echo $SALES->sdate; ?></td>
                      <td><?php echo mc_paymentMethodName($SALES->paymentMethod); ?></td>
                      <td><?php echo mc_statusText($SALES->paymentStatus); ?></td>
                      <td><b><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></b></td>
                    </tr>
                  </tbody>
                  </table>
                </div>

                <div class="hidden-sm hidden-md hidden-lg">
                <span class="hidden-xs"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b> - </span><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?><br><br>

                <?php echo $SALES->sdate; ?><br>
                <?php echo mc_statusText($SALES->paymentStatus); ?><br>
                <?php echo mc_paymentMethodName($SALES->paymentMethod); ?>
                <div class="manageCost"><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></div>
                </div>

                <div id="prd_<?php echo $SALES->id; ?>" style="display:none">
                <hr>
                <?php
                echo str_replace(array('{id}','{count}'),array($SALES->id,mc_sumCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\'','productQty',true)),$msg_sales31);
                echo '<br>' . $msg_sales_view[1] . ': ' . mc_salePlatform($SALES->platform, $msg_platforms);
                if ($SALES->wishlist > 0) {
                ?><br><i class="fa fa-heart fa-fw"></i> <?php echo $msg_sales_screen[0]; ?>
                <?php
                }
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
                <?php
                if ($uDel == 'yes') {
                ?>
                <br><a href="?p=sales&amp;delete=<?php echo $SALES->id; ?>" onclick="mc_confSaleDeletion('<?php echo mc_filterJS($msg_javascript45); ?>','<?php echo $SALES->id; ?>');return false" title="<?php echo mc_cleanDataEntVars($msg_script10); ?>"><i class="fa fa-times fa-fw mc-red"></i> <?php echo $msg_script10; ?></a>
                <?php
                }
                ?>
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
                &nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-down fa-fw" style="cursor:pointer" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[10]); ?>" onclick="mc_toggleMoreOptions(this,'<?php echo $SALES->id; ?>')"></i>
              </div>
            </div>
            <?php
            }
            } else {
            ?>
            <p class="noData"><?php echo $msg_sales17; ?></p>
            <?php
            }
            ?>
        </div>
        <?php
        }
        if (!empty($homeTabs['other'])) {
        for ($i=0; $i<count($homeTabs['other']); $i++) {
        ?>
        <div class="tab-pane <?php echo ($i<1 ? $tabContent['stat'] : 'fade'); ?>" id="<?php echo $homeTabs['other'][$i]['tab']; ?>">
          <?php
            $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`purchaseDate`,'" . $SETTINGS->mysqlDateFormat . "') AS `sdate`
            FROM `" . DB_PREFIX . "sales`
            WHERE `saleConfirmation` = 'yes'
            AND `paymentStatus`     IN('{$homeTabs['other'][$i]['id']}')
            ORDER BY `id` DESC
            ") or die(mc_MySQLError(__LINE__,__FILE__));
            if (mysqli_num_rows($query)>0) {
            while ($SALES = mysqli_fetch_object($query)) {
            $isShip = 'no';
            if (mc_rowCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\' AND `productType` = \'physical\'')>0) {
              $isShip = 'yes';
            }
            ?>
            <div class="panel panel-default" id="salearea_<?php echo $SALES->id; ?>">
              <div class="panel-body">

                <div class="table-responsive hidden-xs">
                  <table class="table salesitemtablehome" style="margin:0;padding:0">
                  <tbody>
                    <tr>
                      <td><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b></td>
                      <td><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?></td>
                      <td><?php echo $SALES->sdate; ?></td>
                      <td><?php echo mc_paymentMethodName($SALES->paymentMethod); ?></td>
                      <td><?php echo mc_statusText($SALES->paymentStatus); ?></td>
                      <td><b><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></b></td>
                    </tr>
                  </tbody>
                  </table>
                </div>

                <div class="hidden-sm hidden-md hidden-lg">
                <span class="hidden-xs"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b> - </span><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?><br><br>

                <?php echo $SALES->sdate; ?><br>
                <?php echo mc_statusText($SALES->paymentStatus); ?><br>
                <?php echo mc_paymentMethodName($SALES->paymentMethod); ?>
                <div class="manageCost"><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></div>
                </div>

                <div id="prd_<?php echo $SALES->id; ?>" style="display:none">
                <hr>
                <?php
                echo str_replace(array('{id}','{count}'),array($SALES->id,mc_sumCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\'','productQty',true)),$msg_sales31);
                echo '<br>' . $msg_sales_view[1] . ': ' . mc_salePlatform($SALES->platform, $msg_platforms);
                if ($SALES->wishlist > 0) {
                ?><br><i class="fa fa-heart fa-fw"></i> <?php echo $msg_sales_screen[0]; ?>
                <?php
                }
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
                <?php
                if ($uDel == 'yes') {
                ?>
                <br><a href="?p=sales&amp;delete=<?php echo $SALES->id; ?>" onclick="mc_confSaleDeletion('<?php echo mc_filterJS($msg_javascript45); ?>','<?php echo $SALES->id; ?>');return false" title="<?php echo mc_cleanDataEntVars($msg_script10); ?>"><i class="fa fa-times fa-fw mc-red"></i> <?php echo $msg_script10; ?></a>
                <?php
                }
                ?>
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
                &nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-down fa-fw" style="cursor:pointer" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[10]); ?>" onclick="mc_toggleMoreOptions(this,'<?php echo $SALES->id; ?>')"></i>
              </div>
            </div>
            <?php
            }
            } else {
            ?>
            <p class="noData"><?php echo $msg_sales17; ?></p>
            <?php
            }
            ?>
        </div>
        <?php
        }
        }
        ?>
      </div>
    </div>
  </div>


  <?php
  }
  ?>

</div>