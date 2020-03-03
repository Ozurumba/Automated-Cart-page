<?php if (!defined('PARENT')) { die('Permission Denied'); }
// Default filter..
$payStatuses     = mc_loadDefaultStatuses();
$salePlatforms   = mc_loadPlatforms($msg_platforms);
if (isset($_GET['filter']) && $_GET['filter'] != 'all') {
  $_GET['filter']  = (isset($_GET['filter']) && (array_key_exists($_GET['filter'],$payStatuses) || ctype_digit($_GET['filter'])) ? $_GET['filter'] : 'completed');
} else {
  $_GET['filter'] = 'all';
}
$_GET['pfm']     = (isset($_GET['pfm']) && array_key_exists($_GET['pfm'],$salePlatforms) ? $_GET['pfm'] : '');
// Calculate range..
if (!isset($_GET['from']) && !isset($_GET['to'])) {
  if ($SETTINGS->jsWeekStart=='0') {
    switch(date('D')) {
      case 'Sun':
        $_GET['from']   = mc_convertBoxedDate(date("Y-m-d"), $SETTINGS);
        $_GET['fromts'] = date("Y-m-d");
        $_GET['to']     = mc_convertBoxedDate(date("Y-m-d",strtotime("+6 days",strtotime($_GET['fromts']))), $SETTINGS);
        break;
      default:
        $_GET['from']   = mc_convertBoxedDate(date("Y-m-d",strtotime('last sunday')), $SETTINGS);
        $_GET['fromts'] = date("Y-m-d",strtotime('last sunday'));
        $_GET['to']     = mc_convertBoxedDate(date("Y-m-d",strtotime("+6 days",strtotime($_GET['fromts']))), $SETTINGS);
        break;
    }
  } else {
    switch(date('D')) {
      case 'Mon':
        $_GET['from']   = mc_convertBoxedDate(date("Y-m-d"), $SETTINGS);
        $_GET['fromts'] = date("Y-m-d");
        $_GET['to']     = mc_convertBoxedDate(date("Y-m-d",strtotime("+6 days",strtotime($_GET['fromts']))), $SETTINGS);
        break;
      default:
        $_GET['from']   = mc_convertBoxedDate(date("Y-m-d",strtotime('last monday')), $SETTINGS);
        $_GET['fromts'] = date("Y-m-d",strtotime('last monday'));
        $_GET['to']     = mc_convertBoxedDate(date("Y-m-d",strtotime("+6 days",strtotime($_GET['fromts']))), $SETTINGS);
        break;
    }
  }
  define('FRTO_NOT_SET', 1);
}
define('CALBOX', 'from|to');
include(PATH.'templates/js-loader/date-picker.php');
?>
<div id="content">

<div class="fieldHeadWrapper">
  <p><span style="float:right"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a> <a class="export_product_overview" href="?p=sales-revenue&amp;export=<?php echo (isset($_GET['filter']) ? $_GET['filter'] : 'all').(isset($_GET['from']) && !in_array($_GET['from'],array('','0000-00-00')) ? '&amp;from='.$_GET['from'] : '').(isset($_GET['to']) && !in_array($_GET['to'],array('','0000-00-00')) ? '&amp;to='.$_GET['to'] : '') . ($_GET['pfm'] ? '&amp;platform=' . $_GET['pfm'] : ''); ?>" title="'<?php echo mc_cleanDataEntVars($msg_revenue2); ?>"><i class="fa fa-save fa-fw"></i></a></span><?php echo $msg_javascript425; ?></p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
<form method="get" action="index.php">
  <div>
  <input type="hidden" name="p" value="sales-revenue">
  <select name="filter">
  <option value="all"<?php echo (isset($_GET['filter']) && $_GET['filter']=='all' ? ' selected="selected"' : ''); ?>><?php echo $msg_revenue10; ?></option>
  <?php
  foreach ($payStatuses AS $key => $value) {
  ?>
  <option value="<?php echo $key; ?>"<?php echo (isset($_GET['filter']) && $_GET['filter']==$key ? ' selected="selected"' : ''); ?>><?php echo $value; ?></option>
  <?php
  }
  // Get additional payment statuses..
  $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                 ORDER BY `pMethod`,`statname`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($q_add_stats)>0) {
  ?>
  <option value="0" disabled="disabled">- - - - - - - - -</option>
  <?php
  }
  while ($ST = mysqli_fetch_object($q_add_stats)) {
  ?>
  <option value="<?php echo $ST->id; ?>"<?php echo (isset($_GET['filter']) && $_GET['filter']==$ST->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($ST->statname); ?></option>
  <?php
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

  <label style="margin-top:10px"><?php echo $msg_stats4; ?>:</label>
  <input type="text" name="from" value="<?php echo (!in_array($_GET['from'],array('','0000-00-00')) ? (isset($_GET['fromts']) ? mc_enterDatesBox($_GET['from']) : mc_safeHTML($_GET['from'])) : ''); ?>" class="box" id="from">
  <input style="margin-top:5px" type="text" name="to" value="<?php echo (!in_array($_GET['to'],array('','0000-00-00')) ? (isset($_GET['fromts']) ? mc_enterDatesBox($_GET['to']) : mc_safeHTML($_GET['to'])) : ''); ?>" class="box" id="to">
  <input style="margin-top:10px" type="submit" class="btn btn-primary" value="<?php echo mc_cleanDataEntVars($msg_stats5); ?>" title="<?php echo mc_cleanDataEntVars($msg_stats5); ?>">
  </div>
</form>
</div>

<?php
$SQL = '';
if (mc_checkValidDate($_GET['from'])!='0000-00-00' && mc_checkValidDate($_GET['to'])!='0000-00-00') {
  $SQL .= mc_defineNewline().'AND `'.DB_PREFIX.'purchases`.`purchaseDate` BETWEEN \''.mc_convertCalToSQLFormat($_GET['from'], $SETTINGS).'\' AND \''.mc_convertCalToSQLFormat($_GET['to'], $SETTINGS).'\'';
}
?>

<div class="table-responsive">
  <table class="table table-striped table-hover">
  <thead>
    <tr>
      <th><?php echo $msg_revenue3; ?></th>
      <th><?php echo $msg_revenue4; ?></th>
      <th><?php echo $msg_revenue5; ?></th>
      <th><?php echo $msg_revenue6; ?></th>
      <th><?php echo $msg_sales_revenue[0]; ?></th>
      <th><?php echo $msg_sales_revenue[1]; ?></th>
      <th><?php echo $msg_revenue7; ?></th>
    </tr>
  </thead>
  <tbody>
  <?php
  // Loop through date range..
  if ($_GET['from'] != '' && $_GET['to'] != '') {
  $start     = strtotime(mc_convertCalToSQLFormat($_GET['from'], $SETTINGS));
  $end       = strtotime(mc_convertCalToSQLFormat($_GET['to'], $SETTINGS));
  $loopDays  = round(($end-$start)/86400);
  $split     = explode('-',mc_convertCalToSQLFormat($_GET['from'], $SETTINGS));
  $gTot      = array('0.00','0.00','0.00','0.00','0.00','0.00');
  if ($loopDays>0) {
  for ($i=0; $i<($loopDays+1); $i++) {
  if (defined('FRTO_NOT_SET')) {
    $ts       = strtotime(date('Y-m-d', mktime(0,0,0,$split[1],$split[0],$split[2])));
  } else {
    $ts       = strtotime(date('Y-m-d', mktime(0,0,0,$split[1],$split[2],$split[0])));
  }
  $day      = date($SETTINGS->systemDateFormat,strtotime('+ '.$i.' days',$ts));
  $sday     = date('Y-m-d',strtotime('+ '.$i.' days',$ts));
  $qS       = mysqli_query($GLOBALS["___msw_sqli"], "SELECT
              SUM(`subTotal`) AS `sub`,
              SUM(`shipTotal`) AS `ship`,
              SUM(`taxPaid`) AS `tax`,
              SUM(`insuranceTotal`) AS `ins`,
              SUM(`chargeTotal`) AS `charge`,
              SUM(`grandTotal`) AS `grand`
              FROM `" . DB_PREFIX . "sales`
              WHERE `saleConfirmation`  = 'yes'
              AND `purchaseDate`        = '{$sday}'
              " . (isset($_GET['filter']) && $_GET['filter'] != 'all' ? 'AND `paymentStatus` = \'' . $_GET['filter'] . '\'' : '') . "
              " . ($_GET['pfm'] ? 'AND `platform` = \'' . $_GET['pfm'] . '\'' : '') . "
              GROUP BY `purchaseDate`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
  $SALE     = mysqli_fetch_object($qS);
  // Increment..
  $gTot[0]  = mc_formatPrice($gTot[0]+(isset($SALE->sub) ? $SALE->sub : '0.00'));
  $gTot[1]  = mc_formatPrice($gTot[1]+(isset($SALE->ship) ? $SALE->ship : '0.00'));
  $gTot[2]  = mc_formatPrice($gTot[2]+(isset($SALE->tax) ? $SALE->tax : '0.00'));
  $gTot[3]  = mc_formatPrice($gTot[3]+(isset($SALE->grand) ? $SALE->grand : '0.00'));
  $gTot[4]  = mc_formatPrice($gTot[4]+(isset($SALE->ins) ? $SALE->ins : '0.00'));
  $gTot[5]  = mc_formatPrice($gTot[5]+(isset($SALE->charge) ? $SALE->charge : '0.00'));
  ?>
  <tr>
   <td><?php echo $day; ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice((isset($SALE->sub) ? $SALE->sub : '0.00'),true)); ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice((isset($SALE->ship) ? $SALE->ship : '0.00'),true)); ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice((isset($SALE->tax) ? $SALE->tax : '0.00'),true)); ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice((isset($SALE->ins) ? $SALE->ins : '0.00'),true)); ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice((isset($SALE->charge) ? $SALE->charge : '0.00'),true)); ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice((isset($SALE->grand) ? $SALE->grand : '0.00'),true)); ?></td>
  </tr>
  <?php
  }
  ?>
  <tr class="revtotals">
   <td><?php echo $msg_revenue9; ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice($gTot[0],true)); ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice($gTot[1],true)); ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice($gTot[2],true)); ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice($gTot[4],true)); ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice($gTot[5],true)); ?></td>
   <td><?php echo mc_currencyFormat(mc_formatPrice($gTot[3],true)); ?></td>
  </tr>
  <?php
  ?>
  </tbody>
  </table>
</div>
<?php
} else {
?>
<span class="noData"><?php echo $msg_revenue8; ?></span>
<?php
}
} else {
?>
<span class="noData"><?php echo $msg_revenue11; ?></span>
<?php
}
?>

</div>
