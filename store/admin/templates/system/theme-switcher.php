<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('themes','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
define('CALBOX', 'from|to');
include(PATH.'templates/js-loader/date-picker.php');
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_themes7);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_themes8);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_themes9);
}
?>

<form method="post" action="?p=themes<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_themes2 : $msg_themes3); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_themes4; ?>: <?php echo mc_displayHelpTip($msg_javascript545,'RIGHT'); ?></label>
    <input type="text" name="from" id="from" tabindex="<?php echo (++$tabIndex); ?>" maxlength="250" value="<?php echo (isset($EDIT->from) && $EDIT->from!='0000-00-00' ? mc_convertMySQLDate($EDIT->from, $SETTINGS) : ''); ?>" class="box">
    <input type="text" name="to" id="to" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->to) && $EDIT->to!='0000-00-00' ? mc_convertMySQLDate($EDIT->to, $SETTINGS) : ''); ?>" class="box" style="margin-top:5px">

    <label style="margin-top:10px"><?php echo $msg_themes6; ?>: <?php echo mc_displayHelpTip($msg_javascript546); ?></label>
	  <select name="theme" tabindex="<?php echo ++$tabIndex; ?>">
    <?php
	  $found     = 0;
    if (is_dir(REL_PATH.'content')) {
      $showtheme = opendir(REL_PATH.'content');
      while (false!==($read=readdir($showtheme))) {
        if (is_dir(REL_PATH.'content/'.$read) && substr(strtolower($read),0,6)=='_theme' && $read!=$SETTINGS->theme) {
          echo '<option value="'.$read.'"'.(isset($EDIT->theme) && $read==$EDIT->theme ? ' selected="selected"' : '').'>'.$read.'</option>'.mc_defineNewline();
          ++$found;
        }
      }
      closedir($showtheme);
    }
	  // Show message if nothing found..
	  if ($found==0) {
	  ?>
	  <option value=""><?php echo $msg_themes12; ?></option>
	  <?php
	  }
    ?>
	  </select>

    <label style="margin-top:10px"><?php echo $msg_themes5; ?>: <?php echo mc_displayHelpTip($msg_javascript544,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enabled" value="yes"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='yes' ? ' checked="checked"' : (!isset($EDIT->enabled) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enabled" value="no"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='no' ? ' checked="checked"' : ''); ?>><br><br>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
  <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="yes">
  <input class="btn btn-primary"<?php echo ($found == 0 ? ' disabled="disabled" ' : ' '); ?>type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_themes2 : $msg_themes3)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_themes2 : $msg_themes3)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=themes\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form><br>

<div class="fieldHeadWrapper" style="margin-top:20px">
  <p><?php echo $msg_themes10; ?>:</p>
</div>

<?php
$q_themes = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "themes`
            ORDER BY `from`,`to`
            ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_themes)>0) {
  while ($THEMES = mysqli_fetch_object($q_themes)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
    <?php
    if ($THEMES->from==$THEMES->to) {
      echo str_replace(
       array('{date}','{theme}','{enabled}'),
       array(
        mc_convertMySQLDate($THEMES->from, $SETTINGS),
      $THEMES->theme,
      ($THEMES->enabled=='yes' ? $msg_script5 : $msg_script6)
       ),
       $msg_themes13
      );
    } else {
      echo str_replace(
       array('{from}','{to}','{theme}','{enabled}'),
       array(
        mc_convertMySQLDate($THEMES->from, $SETTINGS),
      mc_convertMySQLDate($THEMES->to, $SETTINGS),
      $THEMES->theme,
      ($THEMES->enabled=='yes' ? $msg_script5 : $msg_script6)
       ),
       $msg_themes14
      );
    }
    ?>
    </div>
    <div class="panel-footer">
    <a href="?p=themes&amp;edit=<?php echo $THEMES->id; ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? '<a href="?p=themes&amp;del='.$THEMES->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_themes11; ?></span>
<?php
}
?>


</div>
