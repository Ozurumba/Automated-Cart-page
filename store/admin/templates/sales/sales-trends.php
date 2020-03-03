<?php if (!defined('PARENT')) { die('Permission Denied'); }
$salePlatforms = mc_loadPlatforms($msg_platforms);
$range = (isset($_GET['range']) && in_array($_GET['range'],array(3,6,12,24,'year')) ? $_GET['range'] :
         (DEFAULT_SALES_TREND && in_array(strtolower(DEFAULT_SALES_TREND),array(3,6,12,24,'year')) ?
         strtolower(DEFAULT_SALES_TREND) :
         'year')
);
$cat   = (isset($_GET['cat']) && $_GET['cat'] > 0 ? (int) $_GET['cat'] : '0');
$pfm   = (isset($_GET['pfm']) && in_array($_GET['pfm'], array_keys($salePlatforms)) ? $_GET['pfm'] : '');
?>
<div id="content">

<div class="fieldHeadWrapper">
  <p><span style="float:right"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span><?php echo $msg_stats25; ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <form method="get" action="index.php">
  <input type="hidden" name="p" value="sales-trends">
  <select name="cat">
    <option value="0"><?php echo $msg_stats6; ?></option>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`
			        ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <option value="<?php echo $CATS->id; ?>"<?php echo (isset($_GET['cat']) && $_GET['cat']==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`
				          ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <option value="<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['cat']) && $_GET['cat']==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                 WHERE `catLevel` = '3'
                 AND `childOf`    = '{$CHILDREN->id}'
                 AND `enCat`      = 'yes'
                 ORDER BY `catname`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <option value="<?php echo $INFANTS->id; ?>"<?php echo (isset($_GET['cat']) && $_GET['cat']==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;- <?php echo mc_safeHTML($INFANTS->catname); ?></option>
    <?php
    }
    }
    }
    ?>
  </select>

  <select style="margin-top:10px" name="pfm">
    <option value="all"><?php echo $msg_sales_view[2]; ?></option>
    <?php
    if (!empty($salePlatforms)) {
    foreach ($salePlatforms AS $key => $value) {
	  ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_GET['pfm']) && $_GET['pfm']==$key ? ' selected="selected"' : ''); ?>><?php echo $value; ?></option>
    <?php
	  }
    }
    ?>
  </select>

  <select name="range" style="margin-top:10px">
    <option value="year"<?php echo ($range=='year' ? ' selected="selected"' : ''); ?>><?php echo $msg_stats24; ?></option>
    <option value="3"<?php echo ($range==3 ? ' selected="selected"' : ''); ?>><?php echo str_replace('{duration}',3,$msg_stats20); ?></option>
    <option value="6"<?php echo ($range==6 ? ' selected="selected"' : ''); ?>><?php echo str_replace('{duration}',6,$msg_stats20); ?></option>
    <option value="12"<?php echo ($range==12 ? ' selected="selected"' : ''); ?>><?php echo str_replace('{duration}',12,$msg_stats20); ?></option>
    <option value="24"<?php echo ($range==24 ? ' selected="selected"' : ''); ?>><?php echo str_replace('{duration}',24,$msg_stats20); ?></option>
  </select><br>

  <input type="submit" class="btn btn-primary" value="<?php echo mc_cleanDataEntVars($msg_admin3_0[3]); ?>" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[3]); ?>">

  </form>

</div>

<?php
if (mc_rowCount('purchases WHERE `saleConfirmation` = \'yes\'') > 0) {
?>
<div class="graphStats">
  <div class="salesTrends">
    <div class="panel panel-default">
      <div class="panel-body">
       <div id="gloader1_s"></div>
       <div id="chartdiv_trends"></div>
      </div>
    </div>
    <script>
     //<![CDATA[
     jQuery(document).ready(function() {
       setTimeout(function() {
         jQuery('#gloader1_s').remove();
         <?php
         $months = array();
         $line1  = array();
         $line2  = array();
         $line3  = array();
         $ts     = strtotime(date("Y-m-d"));
         switch($range) {
           case 'year':
             for ($i=1; $i<13; $i++) {
               $y                     = date('y',$ts);
               $year                  = date('Y',$ts);
               $nm                    = ($i<10 ? '0'.$i : $i);
               $m                     = $msg_script41[$i-1];
               $m                     = mc_filterJS($m);
               $months[]              = "'$m $y'";
               $line1[$nm.'-'.$year]  = 0;
               $line2[$nm.'-'.$year]  = 0;
               $line3[$nm.'-'.$year]  = 0;
             }
             $SQL  = "AND `purchaseDate` BETWEEN '".date("Y",$ts)."-01-01' AND '".date("Y",$ts)."-12-31'";
             break;
           default:
             for ($i=($range-1); $i>-1; $i--) {
               $y                     = date('y',strtotime('-'.$i.' months',$ts));
               $year                  = date('Y',strtotime('-'.$i.' months',$ts));
               $nm                    = date('m',strtotime('-'.$i.' months',$ts));
               $m                     = $msg_script41[date('n',strtotime('-'.$i.' months',$ts))-1];
               $m                     = mc_filterJS($m);
               $months[]              = "'$m $y'";
               $line1[$nm.'-'.$year]  = 0;
               $line2[$nm.'-'.$year]  = 0;
               $line3[$nm.'-'.$year]  = 0;
             }
             $SQL  = "AND `purchaseDate` > DATE_SUB('" . date("Y-m-d") . "',INTERVAL ".($range-1)." MONTH)".mc_defineNewline();
             break;
         }
         $qs  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT MONTH(`purchaseDate`) as `m`,
                YEAR(`purchaseDate`) AS `y`,
                SUM(`productQty`) AS `qty`,
                `productType`
                FROM `" . DB_PREFIX . "purchases`
                WHERE `saleConfirmation` = 'yes'
                $SQL
                " . ($cat > 0 ? 'AND `categoryID` = \'' . $cat . '\'' : '') . "
                " . (in_array($pfm, array_keys($salePlatforms)) ? 'AND `platform` = \'' . $pfm . '\'' : '') . "
                GROUP BY 2,1,`productType`
                ORDER BY YEAR(`purchaseDate`),MONTH(`purchaseDate`)
                ") or die(mc_MySQLError(__LINE__,__FILE__));
         while ($P_P = mysqli_fetch_object($qs)) {
           $P_P->m = ($P_P->m<10 ? '0'.$P_P->m : $P_P->m);
           switch($P_P->productType) {
             case 'physical':
               $line1[$P_P->m.'-'.$P_P->y] = ($line1[$P_P->m.'-'.$P_P->y]+$P_P->qty);
               break;
             case 'download':
               $line2[$P_P->m.'-'.$P_P->y] = ($line2[$P_P->m.'-'.$P_P->y]+$P_P->qty);
               break;
             case 'virtual':
               $line3[$P_P->m.'-'.$P_P->y] = ($line3[$P_P->m.'-'.$P_P->y]+$P_P->qty);
               break;
           }
         }
         ?>
         line1 = [<?php echo implode(',',$line1); ?>];
         line2 = [<?php echo implode(',',$line2); ?>];
         line3 = [<?php echo implode(',',$line3); ?>];
         ticks = [<?php echo implode(',',$months); ?>];
         plot1 = jQuery.jqplot('chartdiv_trends', [line1,line2,line3], {
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
                               tickOptions: {
                                 formatString: '%d',
                                 fontSize: '8pt'
                               }
                             },
                             xaxis: {
                               rendererOptions:{
                                 tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer
                               },
                               ticks:ticks,
                               <?php
                               if ($range=='24') {
                               ?>
                               tickOptions: {
                                 fontSize: '8pt'
                                 fontFamily: 'Arial',
                                 angle:-40
                               },
                               <?php
                               } else {
                               ?>
                               tickOptions: {
                                 fontSize: '8pt'
                               },
                               <?php
                               }
                               ?>
                               renderer: jQuery.jqplot.CategoryAxisRenderer
                             }
                           },
                           series: [{
                              lineWidth: 1,
                              label: '<?php echo mc_filterJS($msg_stats12); ?>'
                           },{
                              lineWidth: 1,
                              label: '<?php echo mc_filterJS($msg_stats13); ?>'
                           },{
                              lineWidth: 1,
                              label: '<?php echo mc_filterJS($msg_stats28); ?>'
                           }],
                           legend: {
                             show: true
                           }
         });
       }, 2000);
     });
     //]]>
    </script>
  </div>
</div>

<div class="fieldHeadWrapper" style="margin-top:10px">
  <p><?php echo $msg_stats23; ?>:</p>
</div>

<div class="graphStats" style="margin-top:10px">
  <div class="salesTrends">
    <div class="panel panel-default">
      <div class="panel-body">
       <div id="gloader2_s"></div>
       <div id="chartdiv_trends2"></div>
      </div>
    </div>
    <script>
     //<![CDATA[
     jQuery(document).ready(function() {
       setTimeout(function() {
         jQuery('#gloader2_s').remove();
         <?php
         $months = array();
         $line1  = array();
         $line2  = array();
         $line3  = array();
         $ts     = strtotime(date("Y-m-d"));
         switch($range) {
           case 'year':
           for ($i=1; $i<13; $i++) {
             $y                     = date('y',$ts);
             $year                  = date('Y',$ts);
             $nm                    = ($i<10 ? '0'.$i : $i);
             $m                     = $msg_script41[$i-1];
             $m                     = mc_filterJS($m);
             $months[]              = "'$m $y'";
             $line1[$nm.'-'.$year]  = 0;
             $line2[$nm.'-'.$year]  = 0;
             $line3[$nm.'-'.$year]  = 0;
           }
           $SQL  = "AND purchaseDate BETWEEN '".date("Y",$ts)."-01-01' AND '".date("Y",$ts)."-12-31'";
           break;
           default:
           for ($i=($range-1); $i>-1; $i--) {
             $y                     = date('y',strtotime('-'.$i.' months',$ts));
             $year                  = date('Y',strtotime('-'.$i.' months',$ts));
             $nm                    = date('m',strtotime('-'.$i.' months',$ts));
             $m                     = $msg_script41[date('n',strtotime('-'.$i.' months',$ts))-1];
             $m                     = mc_filterJS($m);
             $months[]              = "'$m $y'";
             $line1[$nm.'-'.$year]  = 0;
             $line2[$nm.'-'.$year]  = 0;
             $line3[$nm.'-'.$year]  = 0;
           }
           $SQL  = "AND `purchaseDate` > DATE_SUB('" . date("Y-m-d") . "',INTERVAL ".($range-1)." MONTH)".mc_defineNewline();
           break;
         }
         $qs  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT MONTH(`purchaseDate`) AS `m`,
                YEAR(`purchaseDate`) AS `y`,
                SUM((`salePrice`+`persPrice`+`attrPrice`)*`productQty`) AS `g`,
                `productType`
                FROM `" . DB_PREFIX . "purchases`
                WHERE `saleConfirmation` = 'yes'
                $SQL
                ".($cat>0 ? 'AND `categoryID` = \''.$cat.'\'' : '')."
                " . (in_array($pfm, array_keys($salePlatforms)) ? 'AND `platform` = \'' . $pfm . '\'' : '') . "
                GROUP BY 1,2,4
                ORDER BY YEAR(`purchaseDate`),MONTH(`purchaseDate`)
                ") or die(mc_MySQLError(__LINE__,__FILE__));
         while ($P_P = mysqli_fetch_object($qs)) {
           $P_P->m = ($P_P->m<10 ? '0'.$P_P->m : $P_P->m);
           switch($P_P->productType) {
             case 'physical':
               $line1[$P_P->m.'-'.$P_P->y] += mc_formatPrice($P_P->g);
               break;
             case 'download':
               $line2[$P_P->m.'-'.$P_P->y] += mc_formatPrice($P_P->g);
               break;
             case 'virtual':
               $line3[$P_P->m.'-'.$P_P->y] += mc_formatPrice($P_P->g);
               break;
           }
         }
         ?>
         line1 = [<?php echo implode(',',$line1); ?>];
         line2 = [<?php echo implode(',',$line2); ?>];
         line3 = [<?php echo implode(',',$line3); ?>];
         ticks = [<?php echo implode(',',$months); ?>];
         plot1 = jQuery.jqplot('chartdiv_trends2', [line1,line2,line3], {
                           grid: {
                             borderWidth: 0,
                             shadow: false,
                             gridLineColor: '#eeeeee',
                             backgroundColor: '#fcfcfc',
                             borderColor: '#eeeeee'
                           },
                           axes: {
                             yaxis: {
                               tickOptions: {
                                 formatString: '%.2f',
                                 fontSize: '8pt'
                               },
                               min: 0
                             },
                             xaxis: {
                               rendererOptions:{
                                 tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer
                               },
                               ticks:ticks,
                               <?php
                               if ($range=='24') {
                               ?>
                               tickOptions: {
                                 fontSize: '8pt',
                                 angle: -40
                               },
                               <?php
                               } else {
                               ?>
                               tickOptions: {
                                 fontSize: '8pt'
                               },
                               <?php
                               }
                               ?>
                               renderer: jQuery.jqplot.CategoryAxisRenderer
                             }
                           },
                           series: [{
                              lineWidth: 1,
                              label: '<?php echo mc_filterJS($msg_stats12); ?>'
                           },{
                              lineWidth: 1,
                              label: '<?php echo mc_filterJS($msg_stats13); ?>'
                           },{
                              lineWidth: 1,
                              label: '<?php echo mc_filterJS($msg_stats28); ?>'
                           }],
                           legend: {
                             show: true
                           }
         });
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
<div class="graphStats">
  <span class="noData"><?php echo $msg_sales17; ?></span>
</div>
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
