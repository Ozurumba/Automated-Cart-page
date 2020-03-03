<?php if (!defined('PARENT')) { die('Permission Denied'); }
// Get first sale..
$q_f       = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purchases`
             WHERE `saleConfirmation` = 'yes'
             ORDER BY `purchaseDate`
             LIMIT 1
             ")
             or die(mc_MySQLError(__LINE__,__FILE__));
$FIRST     = mysqli_fetch_object($q_f);
// Filter vars..
$fromDate  = (isset($_GET['from']) ? mc_checkValidDate($_GET['from']) : (isset($FIRST->purchaseDate) ? mc_convertBoxedDate($FIRST->purchaseDate, $SETTINGS) : mc_convertBoxedDate(date('Y-m-d'), $SETTINGS)));
$toDate    = (isset($_GET['to']) ? mc_checkValidDate($_GET['to']) : mc_convertBoxedDate(date('Y-m-d'), $SETTINGS));
$sqlFD     = (isset($_GET['from']) ? mc_convertCalToSQLFormat($fromDate, $SETTINGS) : $fromDate);
$sqlTD     = (isset($_GET['to']) ? mc_convertCalToSQLFormat($toDate, $SETTINGS) : $toDate);
// Count..Physical..
$q_cnts    = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`productQty`) AS `phys_qty` FROM `" . DB_PREFIX . "purchases`
             WHERE `purchaseDate` BETWEEN '{$sqlFD}' AND '{$sqlTD}'
             AND `productType`       = 'physical'
             AND `saleConfirmation`  = 'yes'
             ") or die(mc_MySQLError(__LINE__,__FILE__));
$PHYS      = mysqli_fetch_object($q_cnts);
$PHYS->phys_qty = (isset($PHYS->phys_qty) ? $PHYS->phys_qty : 0);
// Count ..Downloads..
$q_cnts2   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`productQty`) AS `down_qty` FROM `" . DB_PREFIX . "purchases`
             WHERE `purchaseDate` BETWEEN '{$sqlFD}' AND '{$sqlTD}'
             AND `productType`       = 'download'
             AND `saleConfirmation`  = 'yes'
             ") or die(mc_MySQLError(__LINE__,__FILE__));
$DOWN      = mysqli_fetch_object($q_cnts2);
$DOWN->down_qty = (isset($DOWN->down_qty) ? $DOWN->down_qty : 0);
// Count ..Virtual..
$q_cnts3   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`productQty`) AS `gift_qty` FROM `" . DB_PREFIX . "purchases`
             WHERE `purchaseDate` BETWEEN '{$sqlFD}' AND '{$sqlTD}'
             AND `productType`       = 'virtual'
             AND `saleConfirmation`  = 'yes'
             ") or die(mc_MySQLError(__LINE__,__FILE__));
$GIFT      = mysqli_fetch_object($q_cnts3);
$GIFT->gift_qty = (isset($GIFT->gift_qty) ? $GIFT->gift_qty : 0);
// Count ..Account sales..
$q_cnts4   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `acc_sale_cnt` FROM `" . DB_PREFIX . "sales`
             WHERE `purchaseDate` BETWEEN '{$sqlFD}' AND '{$sqlTD}'
             AND `account`           > 0
             AND `saleConfirmation`  = 'yes'
             ") or die(mc_MySQLError(__LINE__,__FILE__));
$ASLS      = mysqli_fetch_object($q_cnts4);
$ASLS->acc_sale_cnt = (isset($ASLS->acc_sale_cnt) ? $ASLS->acc_sale_cnt : 0);
// Count ..Guest sales..
$q_cnts5   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `acc_guest_cnt` FROM `" . DB_PREFIX . "sales`
             WHERE `purchaseDate` BETWEEN '{$sqlFD}' AND '{$sqlTD}'
             AND `account`           = '0'
             AND `saleConfirmation`  = 'yes'
             ") or die(mc_MySQLError(__LINE__,__FILE__));
$GSLS      = mysqli_fetch_object($q_cnts5);
$GSLS->acc_guest_cnt = (isset($GSLS->acc_guest_cnt) ? $GSLS->acc_guest_cnt : 0);
// Get counts..
$q_cnts  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `scount`,sum(`grandTotal`) as `gt`
           FROM `" . DB_PREFIX . "sales`
           WHERE `saleConfirmation` = 'yes'
           AND `purchaseDate` BETWEEN '{$sqlFD}' AND '{$sqlTD}'
           ") or die(mc_MySQLError(__LINE__,__FILE__));
$SUMS    = mysqli_fetch_object($q_cnts);
$orderStatsArray = array();
$totalOrders     = mc_rowCount('sales WHERE `saleConfirmation` = \'yes\'');
$fdate = mc_enterDatesBox(mc_convertCalToSQLFormat($fromDate, $SETTINGS));
$tdate = mc_enterDatesBox(mc_convertCalToSQLFormat($toDate, $SETTINGS));

define('CALBOX', 'from|to');
include(PATH.'templates/js-loader/date-picker.php');
?>
<div id="content">

<div class="fieldHeadWrapper">
  <p><span class="float"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a> <a href="#" onclick="window.print();return false" title="<?php echo mc_cleanDataEntVars($msg_stats17); ?>"><i class="fa fa-print fa-fw"></i></a></span><?php echo $msg_stats2; ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <form method="get" action="index.php">
  <input type="hidden" name="p" value="stats">
  <label><?php echo $msg_stats4; ?>:</label>
  <input type="text" name="from" value="<?php echo mc_enterDatesBox($fromDate); ?>" class="box" id="from">
  <input style="margin-top:5px" type="text" name="to" value="<?php echo mc_enterDatesBox($toDate); ?>" class="box" id="to">
  <input style="margin-top:5px" type="submit" class="btn btn-primary" value="<?php echo mc_cleanDataEntVars($msg_stats5); ?>" title="<?php echo mc_cleanDataEntVars($msg_stats5); ?>">
  </form>
</div>

<div class="alert alert-warning"><i class="fa fa-calendar fa-fw" style="cursor:pointer" onclick="jQuery('#filters').slideToggle()"></i> <?php echo $fdate . ' &gt; ' . $tdate; ?></div>

<?php
if ($totalOrders==0) {
?>
<span class="noData"><?php echo (isset($_GET['cat']) || isset($_GET['from']) ? $msg_stats16 : $msg_sales17); ?></span>
<?php
} else {
$payStatuses = mc_loadDefaultStatuses();
?>
<div class="graphStats">
  <div class="left">
    <?php
    if ($PHYS->phys_qty > 0 || $DOWN->down_qty > 0 || $GIFT->gift_qty > 0) {
    ?>
    <div class="panel panel-default">
      <div class="panel-body">
       <div class="graphloader" id="gloader1"></div>
       <div id="chartdiv"></div>
      </div>
    </div>
    <script>
     //<![CDATA[
     jQuery(document).ready(function() {
       setTimeout(function() {
         jQuery('#gloader1').remove();
         line1 = [['<?php echo mc_filterJS($msg_stats12); ?>',<?php echo number_format($PHYS->phys_qty); ?>], ['<?php echo mc_filterJS($msg_stats13); ?>',<?php echo number_format($DOWN->down_qty); ?>], ['<?php echo mc_filterJS($msg_stats28); ?>',<?php echo number_format($GIFT->gift_qty); ?>]];
         plot1 = jQuery.jqplot('chartdiv', [line1], {
           grid: {
             borderWidth: 0,
             shadow: false,
             gridLineColor: '#eeeeee',
             backgroundColor: '#fcfcfc',
             borderColor: '#eeeeee'
           },
           seriesDefaults: {
             seriesColors: ["#4bb2c5", "#EAA228", "#c5b47f", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc", "#c747a3", "#cddf54", "#FBD178", "#26B4E3", "#bd70c7"],
             renderer: jQuery.jqplot.BarRenderer,
               pointLabels: {
                 show: true ,
                 formatString: '%d'
               },
               rendererOptions: {
                 highlightMouseDown: false,
                 varyBarColor: true
               }
             },
             axes: {
               yaxis: {
                 tickOptions: {
                   formatString: '%d',
                   fontSize: '8pt'
                 }
               },
               xaxis: {
                 rendererOptions:{
                   tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer
                 },
                 tickOptions: {
                   fontSize: '8pt'
                 },
               ticks: '',
               renderer: jQuery.jqplot.CategoryAxisRenderer
             }
           }
         });
       }, 200);
     });
     //]]>
    </script>
    <?php
    }
    $urlString        = array();
    $urlExportString  = array();
    foreach ($payStatuses AS $key => $value) {
      $statCount              = mc_getStatusStatCount($key, $sqlFD, $sqlTD);
      $orderStatsArray[$key]  = $statCount;
      $urlString[]            = urlencode($value).'-'.$statCount;
      $urlExportString[]      = $key.'-'.$statCount;
    }
    $defaultKeys = array_keys($orderStatsArray);
    // Get additional payment statuses..
    $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                   ORDER BY `pMethod`,`statname`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($ST = mysqli_fetch_object($q_add_stats)) {
      $statCount                 = mc_getStatusStatCount($ST->id, $sqlFD, $sqlTD);
      $orderStatsArray[$ST->id]  = $statCount;
      $urlString[]               = urlencode(mc_cleanData($ST->statname)).'-'.$statCount;
      $urlExportString[]         = $ST->id.'-'.$statCount;
    }
    ?>

  </div>
  <div class="right">
    <div class="table-responsive">
    <table class="table table-striped table-hover">
    <thead>
    <tr>
      <th><?php echo $msg_stats11; ?></th>
      <th><?php echo $msg_tool_stats[0]; ?></th>
      <th><?php echo $msg_tool_stats[1]; ?></th>
      <th><?php echo $msg_stats15; ?></th>
      <th><?php echo $msg_stats12; ?></th>
      <th><?php echo $msg_stats13; ?></th>
      <th><?php echo $msg_stats28; ?></th>
      <th><?php echo $msg_stats14; ?></th>
    </tr>
    <tbody>
    <tr class="statbig">
      <td><?php echo ($totalOrders>0 ? number_format($totalOrders) : 0); ?></td>
      <td><?php echo ($ASLS->acc_sale_cnt>0 ? number_format($ASLS->acc_sale_cnt) : 0); ?></td>
      <td><?php echo ($GSLS->acc_guest_cnt>0 ? number_format($GSLS->acc_guest_cnt) : 0); ?></td>
      <td><?php echo ($DOWN->down_qty>0 || $PHYS->phys_qty>0 ? number_format($PHYS->phys_qty + $DOWN->down_qty + $GIFT->gift_qty) : 0); ?></td>
      <td><?php echo ($PHYS->phys_qty>0 ? number_format($PHYS->phys_qty) : 0); ?></td>
      <td><?php echo ($DOWN->down_qty>0 ? number_format($DOWN->down_qty) : 0); ?></td>
      <td><?php echo ($GIFT->gift_qty>0 ? number_format($GIFT->gift_qty) : 0); ?></td>
      <td><?php echo mc_currencyFormat(($SUMS->gt>0 ? mc_formatPrice($SUMS->gt,true) : '0.00')); ?></td>
    </tr>
    </tbody>
    </table>
    </div>

    <div class="table-responsive">
    <table class="table table-striped table-hover">
    <tbody>
    <?php
    foreach ($payStatuses AS $key => $value) {
    ?>
    <tr>
      <td><?php echo $value; ?></td>
      <td class="text-right"><a href="?p=sales&amp;next=1&amp;filter=<?php echo $key; ?>" title="<?php echo mc_safeHTML($value); ?>"><?php echo $orderStatsArray[$key]; ?></a></td>
    </tr>
    <?php
    }
    // Get additional payment statuses..
    $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                   ORDER BY `pMethod`,`statname`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($ST = mysqli_fetch_object($q_add_stats)) {
    ?>
    <tr>
      <td><?php echo mc_cleanData($ST->statname); ?></td>
      <td class="text-right"><a href="?p=sales&amp;next=1&amp;filter=<?php echo $ST->id; ?>" title="<?php echo mc_safeHTML($ST->statname); ?>"><?php echo $orderStatsArray[$ST->id]; ?></a></td>
    </tr>
    <?php
    }
    ?>
    </tbody>
    </table>
    </div>
  </div>
  <br class="clear">
</div>

<div class="fieldHeadWrapper">
  <p><span class="float"><a class="print" href="#" onclick="window.print();return false" title="<?php echo mc_cleanDataEntVars($msg_stats17); ?>"><i class="fa fa-print fa-fw"></i></a></span><?php echo $msg_stats26; ?>:</p>
</div>

<div class="graphStats">
  <div class="left">
    <?php
    $mcounts = array();
    $js      = array();
    $qPMTHS  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `paymentMethod`,count(*) AS `cnt` FROM `" . DB_PREFIX . "sales`
               WHERE `saleConfirmation` = 'yes'
               AND `purchaseDate` BETWEEN '{$sqlFD}' AND '{$sqlTD}'
               GROUP BY `paymentMethod`
               ORDER BY 2 DESC
               ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($PC = mysqli_fetch_object($qPMTHS)) {
      $mcounts[$PC->paymentMethod] = $PC->cnt;
    }
    // Build js array..
    if (!empty($mcounts)) {
    foreach ($mcounts AS $k => $v) {
      if (isset($mcSystemPaymentMethods[$k])) {
        $js[] = "['" . mc_filterJS(mc_paymentMethodName($k)) . "'," . $mcounts[$k] . "]";
      }
    }
    ?>
    <div class="panel panel-default">
      <div class="panel-body">
       <div class="graphloader" id="gloader2"></div>
       <div id="chartdiv2"></div>
      </div>
    </div>
    <script>
     //<![CDATA[
     jQuery(document).ready(function() {
       setTimeout(function() {
         jQuery('#gloader2').remove();
         line1 = [<?php echo implode(',',$js); ?>];
         plot1 = jQuery.jqplot('chartdiv2', [line1], {
           grid: {
             borderWidth: 0,
             shadow: false,
             gridLineColor: '#eeeeee',
             backgroundColor: '#fcfcfc',
             borderColor: '#eeeeee'
           },
           seriesDefaults: {
             seriesColors: ["#4bb2c5", "#EAA228", "#c5b47f", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc", "#c747a3", "#cddf54", "#FBD178", "#26B4E3", "#bd70c7"],
             renderer: jQuery.jqplot.BarRenderer,
               pointLabels: {
                 show: true ,
                 formatString: '%d'
               },
               rendererOptions: {
                 highlightMouseDown: false,
                 varyBarColor: true
               }
             },
             axes: {
               yaxis: {
                 tickOptions: {
                   formatString: '%d',
                   fontSize: '8pt'
                 }
               },
               xaxis: {
                 rendererOptions:{
                   tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer
                 },
                 tickOptions: {
                   fontSize: '8pt'
                 },
               ticks: '',
               renderer: jQuery.jqplot.CategoryAxisRenderer
             }
           }
         });
       }, 200);
     });
     //]]>
    </script>
    <?php
    }
    ?>
  </div>
  <div class="right">
    <div class="table-responsive">
    <table class="table table-striped table-hover">
    <tbody>
    <?php
    if (!empty($mcounts)) {
    foreach ($mcounts AS $k => $v) {
    if (isset($mcSystemPaymentMethods[$k])) {
    ?><tr><td><?php
      echo mc_paymentMethodName($k);
    ?></td><td><a href="?p=sales&amp;pm=<?php echo $k; ?>" title="<?php echo mc_safeHTML(mc_paymentMethodName($k)); ?>"><?php echo mc_paymentMethodName($k); ?></a>
    </td>
    <td class="text-right"><?php echo $mcounts[$k]; ?></td>
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
  <br class="clear">
</div>

<div class="fieldHeadWrapper">
  <p><span class="float"><a class="print" href="#" onclick="window.print();return false" title="<?php echo mc_cleanDataEntVars($msg_stats17); ?>"><i class="fa fa-print fa-fw"></i></a></span><?php echo $msg_stats27; ?>:</p>
</div>

<div class="graphStats">
  <div class="left">
    <?php
    $country  = array();
    $js       = array();
    $qPMTHS   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT shipSetCountry,count(*) AS `cnt` FROM `" . DB_PREFIX . "sales`
                WHERE `saleConfirmation` = 'yes'
                AND `purchaseDate` BETWEEN '{$sqlFD}' AND '{$sqlTD}'
                GROUP BY `shipSetCountry`
                ORDER BY 2 DESC
                ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($PC = mysqli_fetch_object($qPMTHS)) {
      $country[$PC->shipSetCountry] = $PC->cnt;
    }
    // Build js array..
    if (!empty($country)) {
    foreach ($country AS $k => $v) {
      if (isset($country[$k])) {
        $js[] = "['" . mc_filterJS(mc_getShippingCountry($k)) . "'," . $country[$k] . "]";
      }
    }
    ?>
    <div class="panel panel-default">
      <div class="panel-body">
       <div class="graphloader" id="gloader3"></div>
       <div id="chartdiv3"></div>
      </div>
    </div>
    <script>
     //<![CDATA[
     jQuery(document).ready(function() {
       setTimeout(function() {
         jQuery('#gloader3').remove();
         line1 = [<?php echo implode(',',$js); ?>];
         plot1 = jQuery.jqplot('chartdiv3', [line1], {
           grid: {
             borderWidth: 0,
             shadow: false,
             gridLineColor: '#eeeeee',
             backgroundColor: '#fcfcfc',
             borderColor: '#eeeeee'
           },
           seriesDefaults: {
             seriesColors: ["#4bb2c5", "#EAA228", "#c5b47f", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc", "#c747a3", "#cddf54", "#FBD178", "#26B4E3", "#bd70c7"],
             renderer: jQuery.jqplot.BarRenderer,
               pointLabels: {
                 show: true ,
                 formatString: '%d'
               },
               rendererOptions: {
                 highlightMouseDown: false,
                 varyBarColor: true
               }
             },
             axes: {
               yaxis: {
                 tickOptions: {
                   formatString: '%d',
                   fontSize: '8pt'
                 }
               },
               xaxis: {
                 rendererOptions:{
                   tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer
                 },
                 tickOptions: {
                   fontSize: '8pt'
                 },
               ticks: '',
               renderer: jQuery.jqplot.CategoryAxisRenderer
             }
           }
         });
       }, 500);
     });
     //]]>
    </script>
    <?php
    }
    ?>
  </div>
  <div class="right">
    <div class="table-responsive">
    <table class="table table-striped table-hover">
    <tbody>
    <?php
    if (!empty($country)) {
    foreach ($country AS $k => $v) {
    if (isset($country[$k])) {
    ?><tr><td><?php
      echo mc_getShippingCountry($k);
    ?></td>
    <td class="text-right"><a href="?p=sales&amp;country=<?php echo $k; ?>" title="<?php echo mc_safeHTML(mc_getShippingCountry($k)); ?>"><?php echo $country[$k]; ?></a></td>
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
  <br class="clear">
</div>

<p class="rendering" style="text-align:right;padding:15px"><?php echo $msg_script51; ?>: <a href="http://www.jqPlot.com" onclick="window.open(this);return false" title="jqPlot">jqPlot</a> &copy; Chris Leonello</p>
<?php
}
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery('.graphloader').remove();
});
//]]>
</script>
</div>
