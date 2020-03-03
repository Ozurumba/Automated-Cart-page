<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT          = mc_getTableData('zones','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
  $zoneAreaData  = array();
  $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "zone_areas` WHERE `inZone` = '{$EDIT->id}' ORDER BY `areaName`")
       or die(mc_MySQLError(__LINE__,__FILE__));
  while ($Z_AREAS = mysqli_fetch_object($q)) {
    $zoneAreaData[] = mc_cleanData($Z_AREAS->areaName);
  }
 }
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_zones8);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_zones11);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_zones10);
}
?>

<form method="post" action="?p=zones<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>" id="form" enctype="multipart/form-data">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_zones9 : $msg_zones3); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_zones5; ?>: <?php echo mc_displayHelpTip($msg_javascript47,'RIGHT'); ?></label>
    <input tabindex="1" type="text" name="zName" value="<?php echo (isset($EDIT->zName) ? mc_cleanData($EDIT->zName) : ''); ?>" class="box" maxlength="250">

    <label style="margin-top:10px"><?php echo $msg_zones6; ?> (%): <?php echo mc_displayHelpTip($msg_javascript48,'RIGHT'); ?></label>
    <input tabindex="3" type="text" name="zRate" value="<?php echo (isset($EDIT->zRate) ? mc_cleanData($EDIT->zRate) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_zones7; ?>: <?php echo mc_displayHelpTip($msg_javascript49,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="4" type="radio" name="zShipping" value="yes"<?php echo (isset($EDIT->zShipping) && $EDIT->zShipping=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="5" type="radio" name="zShipping" value="no"<?php echo (isset($EDIT->zShipping) && $EDIT->zShipping=='no' ? ' checked="checked"' : (!isset($EDIT->zShipping) ? ' checked="checked"' : '')); ?>>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <?php
    if (isset($EDIT->id)) {
    ?>
    <select tabindex="2" name="zCountry">
    <?php
    $q_ctry   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
                WHERE `enCountry`  = 'yes'
                ORDER BY `cName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($COUNTRY = mysqli_fetch_object($q_ctry)) {
    ?>
    <option value="<?php echo $COUNTRY->id; ?>"<?php echo (isset($EDIT->zCountry) && $EDIT->zCountry==$COUNTRY->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($COUNTRY->cName); ?></option>
    <?php
    }
    ?>
    </select>
    <?php
    } else {
    ?>
    <div class="categoryBoxes">
    <input type="checkbox" name="log" id="log" value="all" onclick="mc_selectAll()" tabindex="<?php echo (++$tabIndex); ?>"> <b><?php echo $msg_zones17; ?></b><br>
    <?php
    $q_ctry   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
                WHERE `enCountry`  = 'yes'
                ORDER BY `cName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($COUNTRY = mysqli_fetch_object($q_ctry)) {
    ?>
    <input type="checkbox" name="zCountry[]" value="<?php echo $COUNTRY->id; ?>"> <?php echo mc_cleanData($COUNTRY->cName); ?><br>
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

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_zones20; ?>: <?php echo mc_displayHelpTip($msg_javascript373,'RIGHT'); ?></label>
    <textarea rows="5" cols="30" id="zones2" name="zones2" tabindex="5"><?php echo (isset($EDIT->id) && !empty($zoneAreaData) ? mc_safeHTML(implode(mc_defineNewline(),$zoneAreaData)) : ''); ?></textarea>

    <label style="margin-top:10px"><?php echo $msg_zones13; ?>: <?php echo mc_displayHelpTip($msg_javascript112); ?></label>
    <input tabindex="5" type="file" name="zones">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
  <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="yes">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_zones9 : $msg_zones3)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_zones9 : $msg_zones3)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=zones\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form>

<div class="fieldHeadWrapper"  style="margin-top:30px">
  <p><span style="float:right"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span><?php echo $msg_zones4; ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none;overflow-y:auto">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=zones"><?php echo $msg_zones17; ?></option>
  <?php
  $q_countries = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
                 WHERE `enCountry` = 'yes'
                 ORDER BY `cName`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CTRY = mysqli_fetch_object($q_countries)) {
  ?>
  <option value="?p=zones&amp;country=<?php echo $CTRY->id; ?>"<?php echo (isset($_GET['country']) && mc_digitSan($_GET['country'])==$CTRY->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($CTRY->cName); ?></option>
  <?php
  }
  ?>
  </select>
</div>

<?php
$limit   = $page * ZONES_PER_PAGE - (ZONES_PER_PAGE);
$q_zones = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,`" . DB_PREFIX . "zones`.`id` AS `zid` FROM `" . DB_PREFIX . "zones`
           LEFT JOIN `" . DB_PREFIX . "countries`
           ON `" . DB_PREFIX . "zones`.`zCountry` = `" . DB_PREFIX . "countries`.`id`
           ".(isset($_GET['country']) ? 'WHERE `zCountry` = \''.mc_digitSan($_GET['country']).'\'' : 'WHERE `enCountry` = \'yes\'')."
           ORDER BY `cName`,`zName`
           LIMIT $limit,".ZONES_PER_PAGE."
           ") or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  =  (isset($c->rows) ? $c->rows : '0');
if (mysqli_num_rows($q_zones)>0) {
  while ($ZONES = mysqli_fetch_object($q_zones)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <b><?php echo mc_cleanData($ZONES->zName); ?> / <?php echo mc_cleanData($ZONES->cName); ?></b><br><br>
      <?php echo $ZONES->zRate; ?>% / <?php echo $msg_zones15; ?>: <?php echo ($ZONES->zShipping=='yes' ? $msg_script5 : $msg_script6); ?>
      <hr>
      <p id="sZ<?php echo $ZONES->zid; ?>">
      <?php
      $zAString = '';
      $q_area = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "zone_areas`
                WHERE `inZone` = '{$ZONES->zid}'
                ORDER BY `areaName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
      $rCount = 0;
      $cutOff = ZONE_AREA_DISPLAY_LIMIT;
      $rCt    = 0;
      while ($AREA = mysqli_fetch_object($q_area)) {
        $rCt = ++$rCount;
        $zAString .= mc_safeHTML($AREA->areaName);
        if ($rCt<=$cutOff) {
          echo mc_safeHTML($AREA->areaName);
          if ($rCt!=$cutOff && $rCt!=mysqli_num_rows($q_area)) {
            echo ', ';
          }
        }
        if ($rCt!=mysqli_num_rows($q_area)) {
          $zAString .= ', ';
        }
      }
      ?>
      </p>
      <p id="sZ2<?php echo $ZONES->zid; ?>" style="display:none">
      <?php
      echo $zAString;
      ?>
      </p>
      <?php
      if ($cutOff<mysqli_num_rows($q_area)) {
      ?>
      <br><div style="float:right"><a href="#" onclick="jQuery('#sZ<?php echo $ZONES->zid; ?>').toggle();jQuery('#sZ2<?php echo $ZONES->zid; ?>').toggle();return false" title="<?php echo mc_cleanDataEntVars($msg_zones19); ?>"><i class="fa fa-search fa-fw"></i></a></div>
      <?php
      }
      ?>
    </div>
    <div class="panel-footer">
      <a href="?p=zones&amp;edit=<?php echo $ZONES->zid; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9).': '.mc_safeHTML($ZONES->zName); ?>"><i class="fa fa-pencil fa-fw"></i></a>
      <?php echo ($uDel=='yes' ? ' <a href="?p=zones&amp;del='.$ZONES->zid.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
     </div>
  </div>
  <?php
  }
  define('PER_PAGE', ZONES_PER_PAGE);
  if ($countedRows>0 && $countedRows > PER_PAGE) {
    $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
    echo $PGS->display();
  }
} else {
?>
<span class="noData"><?php echo $msg_zones12.(isset($_GET['country']) ? ' '.$msg_zones18 : ''); ?></span>
<?php
}
?>


</div>
