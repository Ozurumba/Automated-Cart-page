<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('qtyrates','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
$cnt_rates = mc_rowCount('qtyrates');
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',$cnt,$msg_percrates8));
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_percrates11);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_percrates10);
}

?>

<div id="addArea">
<form method="post" id="form" action="?p=qtyrates<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php
  if ($cnt_rates > 0 && !isset($EDIT->id)) {
  ?>
  <span style="float:right">
  <a href="#" onclick="jQuery('#addArea').hide();jQuery('#enabArea').show();return false" class="enableDisable" title="<?php echo mc_cleanDataEntVars($msg_percrates20); ?>"><i class="fa fa-check-square-o fa-fw"></i></a>
  </span>
  <?php
  }
  echo $msg_admin3_0[55]; ?> <a href="?p=shipping"><i class="fa fa-cog fa-fw"></i></a>:</p>
</div>

<div id="fieldCloneArea">

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_percrates6; ?> (<?php echo $msg_percrates14; ?>): <?php echo mc_displayHelpTip($msg_javascript444,'LEFT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="qtyFrom" value="<?php echo (isset($EDIT->qtyFrom) ? $EDIT->qtyFrom : '0'); ?>" class="box">
    <input style="margin-top:5px" tabindex="<?php echo (++$tabIndex); ?>" type="text" name="qtyTo" value="<?php echo (isset($EDIT->qtyTo) ? $EDIT->qtyTo : '0'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_percrates15; ?>: <?php echo mc_displayHelpTip($msg_javascript445,'LEFT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="rate" value="<?php echo (isset($EDIT->rate) ? mc_rateCleaner($EDIT->rate) : '0.00'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_percrates13; ?>: <?php echo mc_displayHelpTip($msg_javascript443,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enable" value="yes"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='yes' ? ' checked="checked"' : (!isset($EDIT->enabled) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enable" value="no"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='no' ? ' checked="checked"' : ''); ?>>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <?php
    if (isset($EDIT->id)) {
    ?>
    <select tabindex="<?php echo ($tabIndex+3); ?>" name="inZone">
    <?php
    $q_zones = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "zones`.`id` AS `zid` FROM `" . DB_PREFIX . "zones`
               LEFT JOIN `" . DB_PREFIX . "countries`
               ON `" . DB_PREFIX . "zones`.`zCountry` = `" . DB_PREFIX . "countries`.`id`
               WHERE `enCountry`              = 'yes'
               ORDER BY `zName`,`cName`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($ZONES = mysqli_fetch_object($q_zones)) {
    ?>
    <option value="<?php echo $ZONES->zid; ?>"<?php echo (isset($EDIT->inZone) && $EDIT->inZone==$ZONES->zid ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($ZONES->cName); ?> - <?php echo mc_safeHTML($ZONES->zName); ?></option>
    <?php
    }
    ?>
    </select>
    <?php
    } else {
    ?>
    <div class="categoryBoxes">
    <input type="checkbox" name="log" id="log2" value="all" onclick="mc_toggleCheckBoxes(this.checked,'categoryBoxes')" tabindex="<?php echo (++$tabIndex); ?>"> <b><?php echo $msg_percrates18; ?></b><br>
    <?php
    $q_zones = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "zones`.`id` AS `zid` FROM `" . DB_PREFIX . "zones`
               LEFT JOIN `" . DB_PREFIX . "countries`
               ON `" . DB_PREFIX . "zones`.`zCountry` = `" . DB_PREFIX . "countries`.`id`
               WHERE `enCountry`              = 'yes'
               ORDER BY `cName`,`zName`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($ZONES = mysqli_fetch_object($q_zones)) {
    ?>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="inZone[]" value="<?php echo $ZONES->zid; ?>"> <?php echo mc_cleanData($ZONES->cName); ?> - <?php echo mc_safeHTML($ZONES->zName); ?><br>
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
  <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_percrates9 : $msg_percrates3)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_percrates9 : $msg_percrates3)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=qtyrates'.(isset($_GET['service']) ? '&amp;service='.mc_digitSan($_GET['service']) : '').'\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form><br>
</div>

<div id="enabArea" style="display:none">
<form method="post" id="form2" action="?p=qtyrates">
<?php
if (!isset($EDIT->id)) {
?>
<div class="formFieldWrapper" id="zA_update">
  <div class="formLeft">
    <div class="categoryBoxes">
    <?php
    $q_zones = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "zones`.`id` AS `zid` FROM `" . DB_PREFIX . "zones`
               LEFT JOIN `" . DB_PREFIX . "countries`
               ON `" . DB_PREFIX . "zones`.`zCountry` = `" . DB_PREFIX . "countries`.`id`
               WHERE `enCountry`                = 'yes'
               ORDER BY `cName`,`zName`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
    ?>
    <input type="checkbox" name="log" id="log" value="all" onclick="mc_toggleCheckBoxes(this.checked,'categoryBoxes')" tabindex="<?php echo (++$tabIndex); ?>"> <b><?php echo $msg_percrates18; ?></b><br>
    <?php
    while ($ZONES = mysqli_fetch_object($q_zones)) {
    ?>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="inZone[]" value="<?php echo $ZONES->zid; ?>"> <?php echo mc_cleanData($ZONES->cName); ?> - <?php echo mc_safeHTML($ZONES->zName); ?><br>
    <?php
    }
    ?>
    </div>

    <label style="margin-top:10px"><?php echo $msg_percrates13; ?>:</label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enable" value="yes" checked="checked"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enable" value="no">

  </div>
  <br class="clear">
</div>
<?php
}
?>

<p style="text-align:center;padding-top:20px">
  <input type="hidden" name="enabdis" value="yes">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_percrates21); ?>" title="<?php echo mc_cleanDataEntVars($msg_percrates21); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <input class="btn btn-success" type="button" onclick="jQuery('#enabArea').hide();jQuery('#addArea').show();" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
</p>
</form><br>
</div>

<div class="fieldHeadWrapper">
  <p>
  <?php
  if ($cnt_rates > 0) {
  ?>
  <span style="float:right"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span>
  <?php
  }
  echo $msg_percrates4; ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=qtyrates"><?php echo $msg_percrates18; ?></option>
  <?php
  $q_zones = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "zones`.`id` AS `zid` FROM `" . DB_PREFIX . "zones`
             LEFT JOIN `" . DB_PREFIX . "countries`
             ON `" . DB_PREFIX . "zones`.`zCountry` = `" . DB_PREFIX . "countries`.`id`
             WHERE `enCountry`                = 'yes'
             ORDER BY `cName`,`zName`
             ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($ZONES = mysqli_fetch_object($q_zones)) {
  ?>
  <option value="?p=qtyrates&amp;zone=<?php echo $ZONES->zid; ?>"<?php echo (isset($_GET['zone']) && mc_digitSan($_GET['zone'])==$ZONES->zid ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($ZONES->cName); ?> - <?php echo mc_cleanData($ZONES->zName); ?></option>
  <?php
  }
  ?>
  </select>
</div>

<?php
$limit   = $page * RATES_PER_PAGE - (RATES_PER_PAGE);
$q_rates = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
           `" . DB_PREFIX . "qtyrates`.`id` AS `qid`,
           `" . DB_PREFIX . "zones`.`id` AS `zid`
           FROM `" . DB_PREFIX . "qtyrates`
           LEFT JOIN `" . DB_PREFIX . "zones`
           ON `" . DB_PREFIX . "qtyrates`.`inZone` = `" . DB_PREFIX . "zones`.`id`
           LEFT JOIN `" . DB_PREFIX . "countries`
           ON `" . DB_PREFIX . "zones`.`zCountry`  = `" . DB_PREFIX . "countries`.`id`
           ".(isset($_GET['zone']) ? 'WHERE `inZone`   = \''.mc_digitSan($_GET['zone']).'\'' : '')."
           ORDER BY `cName`,`zName`,`qtyFrom`,`qtyTo`
           LIMIT $limit,".SERVICES_PER_PAGE."
           ") or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  =  (isset($c->rows) ? number_format($c->rows,0,'.','') : '0');
if (mysqli_num_rows($q_rates)>0) {
  while ($RATES = mysqli_fetch_object($q_rates)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <b><?php echo mc_safeHTML($RATES->cName.' - '.$RATES->zName); ?></b><br><br>
      <?php echo $RATES->qtyFrom; ?> - <?php echo $RATES->qtyTo; ?> @ <?php echo (substr($RATES->rate,-1) == '%' ? $RATES->rate : mc_currencyFormat($RATES->rate)); ?>
    </div>
    <div class="panel-footer">
      <a href="?p=qtyrates&amp;edit=<?php echo $RATES->qid.(isset($_GET['service']) ? '&amp;service='.mc_digitSan($_GET['service']) : ''); ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=qtyrates&amp;del='.$RATES->qid.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
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
<span class="noData"><?php echo $msg_percrates12; ?></span>
<?php
}
?>


</div>
