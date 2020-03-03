<?php if (!defined('PARENT') || !isset($_GET['code'])) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_settings31);
}
$PARENT = mc_getTableData('giftcerts','id',mc_digitSan($_GET['code']));
?>

<div class="fieldHeadWrapper">
  <p><span class="float"><a href="?p=gift" title="<?php echo mc_cleanDataEntVars($msg_giftcerts15); ?>"><i class="fa fa-chevron-left fa-fw"></i></a></span><?php echo mc_safeHTML($PARENT->name); ?>:</p>
</div>

<?php
$limit   = $page * COUPON_REPORTS_PER_PAGE - (COUPON_REPORTS_PER_PAGE);
$scnt    = 0;
$q_codes = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,DATE_FORMAT(`" . DB_PREFIX . "giftcodes`.`dateAdded`,'" . $SETTINGS->mysqlDateFormat . "') AS `udate` FROM `" . DB_PREFIX . "giftcodes`
           LEFT JOIN `" . DB_PREFIX . "sales`
		       ON `" . DB_PREFIX . "giftcodes`.`saleID`    = `" . DB_PREFIX . "sales`.`id`
           WHERE `" . DB_PREFIX . "giftcodes`.`giftID` = '" . mc_digitSan($_GET['code']) . "'
		       AND `" . DB_PREFIX . "giftcodes`.`active`   = 'yes'
           ORDER BY `" . DB_PREFIX . "giftcodes`.`id` DESC
           LIMIT $limit," . COUPON_REPORTS_PER_PAGE . "
           ") or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  =  (isset($c->rows) ? $c->rows : '0');
if (mysqli_num_rows($q_codes)>0) {
while ($PUR = mysqli_fetch_object($q_codes)) {
  $finrep       =  array(
   '{from}'     => mc_safeHTML($PUR->from_name),
   '{to}'       => mc_safeHTML($PUR->to_name),
   '{value}'    => mc_currencyFormat($PUR->value),
   '{redeemed}' => mc_currencyFormat($PUR->redeemed),
   '{id}'       => $PUR->saleID,
   '{inv}'      => (isset($PUR->invoiceNo) ? mc_saleInvoiceNumber($PUR->invoiceNo, $SETTINGS) : '0'),
   '{code}'     => $PUR->code
  );
  ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <?php echo $PUR->udate; ?>
    </div>
    <div class="panel-body">
      <?php echo strtr($msg_giftcerts19,$finrep); ?><br><br>
      <?php echo strtr($msg_giftcerts16,$finrep); ?><br><br>
      <?php echo strtr($msg_giftcerts33,$finrep); ?>
    </div>
  </div>
  <?php
}
if ($countedRows>0) {
  define('PER_PAGE', COUPON_REPORTS_PER_PAGE);
  if ($countedRows>0 && $countedRows > PER_PAGE) {
    $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
    echo $PGS->display();
  }
}

// Show graph..
?>
<br>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_giftcerts18; ?>:</p>
</div>

<div class="graphStats">
  <div class="salesTrends">
    <div class="panel panel-default">
      <div class="panel-body">
       <div class="graphloader"></div>
       <div id="chart_coupons"></div>
      </div>
    </div>
    <script>
     //<![CDATA[
     jQuery(document).ready(function() {
       setTimeout(function() {
         jQuery('.graphloader').remove();
         <?php
         $line    = array();
         $months  = array();
         $range   = 12;
         $ts      = strtotime(date("Y-m-d"));
         $max     = 0;
         for ($i=($range-1); $i>-1; $i--) {
           $y                     = date('y',strtotime('-'.$i.' months',$ts));
           $year                  = date('Y',strtotime('-'.$i.' months',$ts));
           $nm                    = date('m',strtotime('-'.$i.' months',$ts));
           $m                     = $msg_script41[date('n',strtotime('-'.$i.' months',$ts))-1];
           $m                     = mc_filterJS($m);
           $months[]              = "'$m $y'";
           $line[$nm.'-'.$year]   = 0;
         }
         $qc      = mysqli_query($GLOBALS["___msw_sqli"], "SELECT MONTH(`dateAdded`) as `m`,
                    YEAR(`dateAdded`) AS `y`,
                    count(*) AS `cpns`
                    FROM `" . DB_PREFIX . "giftcodes`
                    WHERE `giftID`  = '" . mc_digitSan($_GET['code']) . "'
                    AND `active`    = 'yes'
                    AND `dateAdded` > DATE_SUB('" . date("Y-m-d") . "',INTERVAL 11 MONTH)
                    GROUP BY 2,1
                    ORDER BY YEAR(`dateAdded`) DESC,MONTH(`dateAdded`) DESC
                    ") or die(mc_MySQLError(__LINE__,__FILE__));
         if (mysqli_num_rows($qc) > 0) {
         while ($CP = mysqli_fetch_object($qc)) {
           $m                       = ($CP->m<10 ? '0' . $CP->m : $CP->m);
           $line[$m . '-' . $CP->y] = $CP->cpns;
           // Get highest count value..
           if ($CP->cpns>$max) {
             $max = $CP->cpns;
           }
         }
         ?>
         line1 = [<?php echo implode(',',$line); ?>];
         ticks = [<?php echo implode(',',$months); ?>];
         plot1 = jQuery.jqplot('chart_coupons', [line1], {
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
               min: 0,
               max: <?php echo ($max+1); ?>,
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
               ticks: ticks,
               renderer: jQuery.jqplot.CategoryAxisRenderer
             }
           }
         });
         <?php
         } else {
         ?>
         jQuery('#chart_coupons').html('<p class="text-center"><?php echo mc_filterJS($msg_giftcerts34); ?></p>');
         <?php
         }
         ?>
       }, 2000);
     });
     //]]>
    </script>
  </div>
  <p class="rendering" style="text-align:right;padding:15px"><?php echo $msg_script51; ?>: <a href="http://www.jqPlot.com" onclick="window.open(this);return false" title="jqPlot">jqPlot</a> &copy; Chris Leonello</p>
</div>
<?php
} else {
?>
<span class="noData"><?php echo $msg_giftcerts17; ?></span>
<?php
}
?>

</div>
