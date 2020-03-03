<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('tare','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace(array('{count}','{services}'),array($run[0],$run[1]),$msg_tare8));
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_tare11);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_tare10);
}
?>

<form method="post" action="?p=tare<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>" enctype="multipart/form-data">
<div class="fieldHeadWrapper">
  <p>
  <?php echo (isset($EDIT->id) ? $msg_tare9 : $msg_tare3);
  ?>
  :</p>
</div>
<?php
$tabIndex = 0;
?>
<div id="fieldCloneArea">

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_tare5; ?> <?php echo mc_displayHelpTip($msg_javascript50,'RIGHT'); ?> / <?php echo $msg_tare6; ?>: <?php echo mc_displayHelpTip($msg_javascript51,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="rWeightFrom" value="<?php echo (isset($EDIT->rWeightFrom) ? $EDIT->rWeightFrom : '0'); ?>" class="box">
    <input style="margin-top:5px" tabindex="<?php echo (++$tabIndex); ?>" type="text" name="rWeightTo" value="<?php echo (isset($EDIT->rWeightTo) ? $EDIT->rWeightTo : '0'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_tare7; ?>: <?php echo mc_displayHelpTip($msg_javascript498,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="rCost" value="<?php echo (isset($EDIT->rCost) ? $EDIT->rCost : '0'); ?>" class="box">

    <?php
    if (!isset($EDIT->id)) {
    ?>
    <label style="margin-top:10px"><?php echo $msg_services19; ?>: <?php echo mc_displayHelpTip($msg_javascript499,'RIGHT'); ?></label>
    <input tabindex="5" type="file" name="file">
    <?php
    }
    ?>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <?php
    if (isset($EDIT->id)) {
    ?>
    <select name="rService" tabindex="<?php echo (++$tabIndex); ?>" >
    <?php
    $q_services = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "services`.`id` AS `sid` FROM `" . DB_PREFIX . "services`
                  LEFT JOIN `" . DB_PREFIX . "zones`
                  ON `" . DB_PREFIX . "services`.`inZone` = `" . DB_PREFIX . "zones`.`id`
                  LEFT JOIN `" . DB_PREFIX . "countries`
                  ON `" . DB_PREFIX . "countries`.`id` = `" . DB_PREFIX . "zones`.`zCountry`
                  WHERE `enCountry` = 'yes'
                  ORDER BY `cName`,`zName`,`sName`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($SERVICES = mysqli_fetch_object($q_services)) {
    ?>
    <option value="<?php echo $SERVICES->sid; ?>"<?php echo (isset($EDIT->rService) && $EDIT->rService==$SERVICES->sid ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($SERVICES->cName.' - '.$SERVICES->zName.' - '.$SERVICES->sName); ?></option>
    <?php
    }
    ?>
    </select>
    <?php
    } else {
    ?>
    <div class="categoryBoxes">
    <input type="checkbox" name="log" id="log" value="all" onclick="mc_toggleCheckBoxes(this.checked,'categoryBoxes')" tabindex="<?php echo (++$tabIndex); ?>" > <b><?php echo $msg_tare17; ?></b><br>
    <?php
    $q_services = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "services`.`id` AS `sid` FROM `" . DB_PREFIX . "services`
                  LEFT JOIN `" . DB_PREFIX . "zones`
                  ON `" . DB_PREFIX . "services`.`inZone`  = `" . DB_PREFIX . "zones`.`id`
                  LEFT JOIN `" . DB_PREFIX . "countries`
                  ON `" . DB_PREFIX . "countries`.`id`     = `" . DB_PREFIX . "zones`.`zCountry`
                  WHERE `enCountry`                  = 'yes'
                  ORDER BY `cName`,`zName`,`sName`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($SERVICES = mysqli_fetch_object($q_services)) {
    ?>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="rService[]" value="<?php echo $SERVICES->sid; ?>"> <?php echo mc_safeHTML($SERVICES->cName.' - '.$SERVICES->zName.' - '.$SERVICES->sName); ?><br>
    <?php
    }
    ?>
    </div>
    <?php
    }
    ?>
  </div>
  <br class="clear">
</div>

</div>

<p style="text-align:center;padding-top:20px">
  <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="yes">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_tare9 : $msg_tare3)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_tare9 : $msg_tare3)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=tare'.(isset($_GET['service']) ? '&amp;service='.mc_digitSan($_GET['service']) : '').'\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form>

<div class="fieldHeadWrapper"  style="margin-top:30px">
  <p><span style="float:right"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span><?php echo $msg_tare4; ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=tare"><?php echo $msg_tare18; ?></option>
  <?php
  $q_services = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "services`.`id` AS `sid` FROM `" . DB_PREFIX . "services`
                LEFT JOIN `" . DB_PREFIX . "zones`
                ON `" . DB_PREFIX . "services`.`inZone` = `" . DB_PREFIX . "zones`.`id`
                LEFT JOIN `" . DB_PREFIX . "countries`
                ON `" . DB_PREFIX . "countries`.`id` = `" . DB_PREFIX . "zones`.`zCountry`
                WHERE `enCountry` = 'yes'
                ORDER BY `cName`,`zName`,`sName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($SERVICES = mysqli_fetch_object($q_services)) {
  ?>
  <option value="?p=tare&amp;service=<?php echo $SERVICES->sid; ?>"<?php echo (isset($_GET['service']) && mc_digitSan($_GET['service'])==$SERVICES->sid ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($SERVICES->cName.' - '.$SERVICES->zName.' - '.$SERVICES->sName); ?></option>
  <?php
  }
  ?>
  </select>
</div>

<?php
$limit   = $page * RATES_PER_PAGE - (RATES_PER_PAGE);
$q_rates = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,`" . DB_PREFIX . "tare`.`id` AS `tid` FROM `" . DB_PREFIX . "tare`
           LEFT JOIN `" . DB_PREFIX . "services`
           ON `" . DB_PREFIX . "services`.`id`     = `" . DB_PREFIX . "tare`.`rService`
           LEFT JOIN `" . DB_PREFIX . "zones`
           ON `" . DB_PREFIX . "services`.`inZone` = `" . DB_PREFIX . "zones`.`id`
           LEFT JOIN `" . DB_PREFIX . "countries`
           ON `" . DB_PREFIX . "countries`.`id`    = `" . DB_PREFIX . "zones`.`zCountry`
           ".(isset($_GET['service']) ? 'WHERE `rService`   = \''.mc_digitSan($_GET['service']).'\'' : '')."
           ORDER BY `cName`,`zName`,`sName`,`rWeightFrom`*100,`rWeightTo`*100
           LIMIT $limit,".RATES_PER_PAGE."
           ") or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  = (isset($c->rows) ? $c->rows : '0');
if (mysqli_num_rows($q_rates)>0) {
  while ($RATES = mysqli_fetch_object($q_rates)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <b><?php echo mc_cleanData($RATES->cName.' - '.$RATES->zName.' - '.$RATES->sName); ?></b><br><br>
      <?php echo $RATES->rWeightFrom; ?> - <?php echo $RATES->rWeightTo; ?> (<?php echo (substr($RATES->rCost,-1)=='%' ? $RATES->rCost : mc_currencyFormat($RATES->rCost)); ?>)
    </div>
    <div class="panel-footer">
      <a href="?p=tare&amp;edit=<?php echo $RATES->tid.(isset($_GET['service']) ? '&amp;service='.mc_digitSan($_GET['service']) : ''); ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=tare&amp;del='.$RATES->tid.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
  define('PER_PAGE',RATES_PER_PAGE);
  if ($countedRows>0 && $countedRows>PER_PAGE) {
    $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
    echo $PGS->display();
  }
} else {
?>
<span class="noData"><?php echo $msg_tare12; ?></span>
<?php
}
?>


</div>
