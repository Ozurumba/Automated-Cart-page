<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT  = mc_getTableData('services','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace(array('{count}','{zones}'),array($run[0],$run[1]),$msg_services8));
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_services11);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_services10);
}
?>

<form method="post" id="form" action="?p=services<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>" enctype="multipart/form-data">
<div class="fieldHeadWrapper">
  <p>
  <?php echo (isset($EDIT->id) ? $msg_services9 : $msg_services3); ?>:</p>
</div>

<div id="fieldCloneArea">

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_services5; ?>: <?php echo mc_displayHelpTip($msg_javascript101,'RIGHT'); ?> / <?php echo $msg_services6; ?>: <?php echo mc_displayHelpTip($msg_javascript102); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" maxlength="250" name="sName" value="<?php echo (isset($EDIT->sName) ? $EDIT->sName : ''); ?>" class="box">
    <input style="margin-top:5px" tabindex="<?php echo (++$tabIndex); ?>" type="text" maxlength="250" name="sEstimation" value="<?php echo (isset($EDIT->sEstimation) ? $EDIT->sEstimation : ''); ?>" class="box">

    <?php
    if (!isset($EDIT->id)) {
    ?>
    <label style="margin-top:10px"><?php echo $msg_services19; ?>: <?php echo mc_displayHelpTip($msg_javascript499,'RIGHT'); ?></label>
    <input tabindex="5" type="file" name="file">
    <?php
    }
    ?>

    <label style="margin-top:10px"><?php echo $msg_services14; ?>: <?php echo mc_displayHelpTip($msg_javascript103,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" name="sSignature" value="yes"<?php echo (isset($EDIT->sSignature) && $EDIT->sSignature=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="sSignature" value="no"<?php echo (isset($EDIT->sSignature) && $EDIT->sSignature=='no' ? ' checked="checked"' : (!isset($EDIT->sSignature) ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_services18; ?>: <?php echo mc_displayHelpTip($msg_javascript429); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" name="enableCOD" value="yes"<?php echo (isset($EDIT->enableCOD) && $EDIT->enableCOD=='yes' ? ' checked="checked"' : (!isset($EDIT->enableCOD) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enableCOD" value="no"<?php echo (isset($EDIT->enableCOD) && $EDIT->enableCOD=='no' ? ' checked="checked"' : ''); ?>>

  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <?php
    if (isset($EDIT->id)) {
    ?>
    <select name="inZone" tabindex="<?php echo (++$tabIndex); ?>" >
    <?php
    $q_zones = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "zones`.`id` AS `zid` FROM `" . DB_PREFIX . "zones`
               LEFT JOIN `" . DB_PREFIX . "countries`
               ON `" . DB_PREFIX . "zones`.`zCountry` = `" . DB_PREFIX . "countries`.`id`
               WHERE `enCountry`                = 'yes'
               ORDER BY `cName`,`zName`
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
    <input type="checkbox" name="log" id="log" value="all" onclick="mc_toggleCheckBoxes(this.checked,'categoryBoxes')" tabindex="<?php echo (++$tabIndex); ?>" > <b><?php echo $msg_services16; ?></b><br>
    <?php
    $q_zones = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "zones`.`id` AS `zid` FROM `" . DB_PREFIX . "zones`
               LEFT JOIN `" . DB_PREFIX . "countries`
               ON `" . DB_PREFIX . "zones`.`zCountry` = `" . DB_PREFIX . "countries`.`id`
               WHERE `enCountry`                = 'yes'
               ORDER BY `cName`,`zName`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($ZONES = mysqli_fetch_object($q_zones)) {
    ?>
    <input type="checkbox" name="inZone[]" value="<?php echo $ZONES->zid; ?>" tabindex="<?php echo (++$tabIndex); ?>" > <?php echo mc_cleanData($ZONES->cName); ?> - <?php echo mc_safeHTML($ZONES->zName); ?><br>
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
  <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_services9 : $msg_services3)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_services9 : $msg_services3)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=services'.(isset($_GET['zone']) ? '&amp;zone='.mc_digitSan($_GET['zone']) : '').'\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form>

<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><span style="float:right"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span><?php echo $msg_services4; ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none;overflow-y:auto">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=services"><?php echo $msg_services16; ?></option>
  <?php
  $q_zones = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "zones`.`id` AS `zid` FROM `" . DB_PREFIX . "zones`
             LEFT JOIN `" . DB_PREFIX . "countries`
             ON `" . DB_PREFIX . "zones`.`zCountry` = `" . DB_PREFIX . "countries`.`id`
             WHERE `enCountry`                = 'yes'
             ORDER BY `cName`,`zName`
             ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($ZONES = mysqli_fetch_object($q_zones)) {
  ?>
  <option value="?p=services&amp;zone=<?php echo $ZONES->zid; ?>"<?php echo (isset($_GET['zone']) && mc_digitSan($_GET['zone'])==$ZONES->zid ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($ZONES->cName); ?> - <?php echo mc_cleanData($ZONES->zName); ?></option>
  <?php
  }
  ?>
  </select>
</div>

<?php
$limit      = $page * SERVICES_PER_PAGE - (SERVICES_PER_PAGE);
$q_services = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,`" . DB_PREFIX . "services`.`id` AS `sid`,`" . DB_PREFIX . "zones`.`id` AS `zid`
              FROM `" . DB_PREFIX . "services`
              LEFT JOIN `" . DB_PREFIX . "zones`
              ON `" . DB_PREFIX . "services`.`inZone` = `" . DB_PREFIX . "zones`.`id`
              LEFT JOIN `" . DB_PREFIX . "countries`
              ON `" . DB_PREFIX . "zones`.`zCountry`  = `" . DB_PREFIX . "countries`.`id`
              ".(isset($_GET['zone']) ? 'WHERE `inZone`   = \''.mc_digitSan($_GET['zone']).'\'' : '')."
              ORDER BY `cName`,`zName`,`sName`
              LIMIT $limit,".SERVICES_PER_PAGE."
              ") or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  =  (isset($c->rows) ? number_format($c->rows,0,'.','') : '0');
if (mysqli_num_rows($q_services)>0) {
  while ($SERVICES = mysqli_fetch_object($q_services)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <b><?php echo mc_cleanData($SERVICES->cName.' / '.$SERVICES->zName).' / '.mc_safeHTML($SERVICES->sName); ?></b><br><br>
      <?php echo $SERVICES->sEstimation; ?><br>
      <?php echo $msg_services14.': '.($SERVICES->sSignature=='yes' ? $msg_script5 : $msg_script6); ?><br>
      <?php echo $msg_services18.': '.($SERVICES->enableCOD=='yes' ? $msg_script5 : $msg_script6); ?>
    </div>
    <div class="panel-footer">
      <a href="?p=services&amp;edit=<?php echo $SERVICES->sid.(isset($_GET['zone']) ? '&amp;zone='.mc_digitSan($_GET['zone']) : ''); ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=services&amp;del='.$SERVICES->sid.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
  define('PER_PAGE',SERVICES_PER_PAGE);
  if ($countedRows>0 && $countedRows>PER_PAGE) {
    $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
    echo $PGS->display();
  }
} else {
?>
<span class="noData"><?php echo $msg_services12; ?></span>
<?php
}
?>


</div>
