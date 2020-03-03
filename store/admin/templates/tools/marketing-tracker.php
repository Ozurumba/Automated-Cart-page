<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('tracker','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
$orderBy = '`name`';
if (isset($_GET['order'])) {
  switch($_GET['order']) {
    case 'click_desc':
      $orderBy = '`trackClicks` DESC';
      break;
    case 'click_asc':
      $orderBy = '`trackClicks`';
      break;
    case 'sales_desc':
      $orderBy = '`salesRevenue` * 1000 DESC';
      break;
    case 'sales_asc':
      $orderBy = '`salesRevenue` * 1000';
      break;
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_marketing3);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_marketing4);
}
if (isset($OK3) && $rows > 0) {
  echo mc_actionCompleted($msg_marketing5);
}
if (isset($OK4)) {
  echo mc_actionCompleted($msg_marketing19);
}
?>

<form method="post" action="?p=marketing<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_marketing2 : $msg_marketing); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_marketing6; ?> <?php echo mc_displayHelpTip($msg_javascript50,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="name" value="<?php echo (isset($EDIT->name) ? mc_safeHTML($EDIT->name) : ''); ?>" maxlength="250" class="box">

    <label style="margin-top:10px"><?php echo $msg_marketing7; ?>: <?php echo mc_displayHelpTip($msg_javascript52,'RIGHT'); ?></label>
    <div class="form-group input-group">
      <span class="input-group-addon"><a href="#" onclick="mc_genTracker();return false"><i class="fa fa-refresh fa-fw"></i></a></span>
      <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="code" value="<?php echo (isset($EDIT->code) ? mc_safeHTML($EDIT->code) : ''); ?>" maxlength="100" class="box addon-no-radius">
    </div>
    <?php
    if (!isset($_GET['edit'])) {
    ?>
    <span class="help-block" style="text-align:left !important"><?php echo str_replace(array('{url}','{prefix}'),array($SETTINGS->ifolder,TRACKING_CODE_PREFIX),$msg_marketing8); ?></span>
    <?php
    }
    ?>
  </div>
</div>

<p style="text-align:center;padding-top:20px">
  <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="yes">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_marketing2 : $msg_marketing)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_marketing2 : $msg_marketing)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=marketing\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>

</form>

<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><span style="float:right"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span><?php echo $msg_marketing10; ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none;overflow-y:auto">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
    <option value="?p=marketing"><?php echo $msg_marketing13; ?></option>
    <option value="?p=marketing&amp;order=click_desc"<?php echo (isset($_GET['order']) && $_GET['order'] == 'click_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_marketing14; ?></option>
    <option value="?p=marketing&amp;order=click_asc"<?php echo (isset($_GET['order']) && $_GET['order'] == 'click_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_marketing15; ?></option>
    <option value="?p=marketing&amp;order=sales_desc"<?php echo (isset($_GET['order']) && $_GET['order'] == 'sales_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_marketing16; ?></option>
    <option value="?p=marketing&amp;order=sales_asc"<?php echo (isset($_GET['order']) && $_GET['order'] == 'sales_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_marketing17; ?></option>
  </select>
</div>

<div id="formField">
<form method="post" action="?p=marketing" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">
<?php
$limit   = $page * TRACKERS_PER_PAGE - (TRACKERS_PER_PAGE);
$q_track = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
           (SELECT count(*) FROM `" . DB_PREFIX . "tracker_clicks` WHERE `" . DB_PREFIX . "tracker`.`code` = `" . DB_PREFIX . "tracker_clicks`.`code`) AS `trackClicks`,
           (SELECT SUM(`grandTotal`) FROM `" . DB_PREFIX . "sales`
            WHERE `" . DB_PREFIX . "sales`.`trackcode` = `" . DB_PREFIX . "tracker`.`code`
            AND `" . DB_PREFIX . "sales`.`saleConfirmation` = 'yes'
           ) AS `salesRevenue`
           FROM `" . DB_PREFIX . "tracker`
           ORDER BY $orderBy
           LIMIT $limit,".TRACKERS_PER_PAGE."
           ") or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  = (isset($c->rows) ? $c->rows : '0');
if (mysqli_num_rows($q_track)>0) {
  while ($TRACK = mysqli_fetch_object($q_track)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <input type="checkbox" name="reset[]" onclick="mc_chkCnt('reset','counter','button')" value="<?php echo $TRACK->id; ?>"> <b><?php echo mc_cleanData($TRACK->name); ?></b><br>
      <a href="<?php echo $SETTINGS->ifolder . '/?' . TRACKING_CODE_PREFIX . '=' . $TRACK->code; ?>" onclick="window.open(this);return false"><?php echo $SETTINGS->ifolder . '/?' . TRACKING_CODE_PREFIX . '=' . $TRACK->code; ?></a>
      <br><br>
      <?php echo $msg_marketing11; ?>: <?php echo @number_format($TRACK->trackClicks); ?><br>
      <?php echo $msg_marketing12; ?>: <?php echo mc_currencyFormat(mc_formatPrice($TRACK->salesRevenue,true)); ?>
    </div>
    <div class="panel-footer">
      <a href="?p=marketing&amp;edit=<?php echo $TRACK->id; ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=marketing&amp;del='.$TRACK->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
  ?>
  <p style="padding:10px 0 10px 10px">
  <input type="checkbox" name="all" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'formField');mc_chkCnt('reset','counter','button')">&nbsp;&nbsp;&nbsp;
  <button type="submit" disabled="disabled" class="btn btn-primary" id="button"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_marketing18); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-pencil fa-fw"></i></span> (<span class="counter">0</span>)</button>
  </p>
  <?php
  define('PER_PAGE',TRACKERS_PER_PAGE);
  if ($countedRows>0 && $countedRows>PER_PAGE) {
    $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
    echo $PGS->display();
  }
} else {
?>
<span class="noData"><?php echo $msg_marketing9; ?></span>
<?php
}
?>
</form>
</div>

</div>
