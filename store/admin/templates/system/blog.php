<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('blog','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
?>
<div id="content">

<?php
$tabIndex = 0;
if (isset($OK)) {
  echo mc_actionCompleted($msg_blog5);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_blog6);
}
if (isset($OK3)) {
  echo mc_actionCompleted($msg_blog7);
}
define('CALBOX', 'published|autodelete' . (isset($EDIT->id) ? '|created' : ''));
include(PATH.'templates/js-loader/date-picker.php');
?>

<form method="post" id="form" action="?p=blog<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($_GET['edit']) ? $msg_blog8 : $msg_blog9); ?></p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_blog; ?>:</label>
    <input type="text" name="title" value="<?php echo (isset($EDIT->title) ? mc_safeHTML($EDIT->title) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

    <label style="margin-top:10px"><?php echo $msg_blog2; ?>:</label>
    <?php
    if ($SETTINGS->enableBBCode == 'yes') {
      define('BB_BOX', 'message');
      include(PATH . 'templates/bbcode-buttons.php');
    }
    ?>
    <textarea rows="5" cols="30" name="message" id="message" style="height:100px" tabindex="<?php echo ++$tabIndex; ?>"><?php echo (isset($EDIT->message) ? mc_safeHTML($EDIT->message) : ''); ?></textarea><br>

    <?php
    if (isset($EDIT->id)) {
    ?>
    <label><?php echo $msg_blog14; ?>: <?php echo mc_displayHelpTip($msg_javascript6,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enabled" value="yes"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enabled" value="no"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='no' ? ' checked="checked"' : ''); ?>>
    <?php
    }
    ?>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <?php
    if (isset($EDIT->id)) {
    ?>
    <label><?php echo $msg_blog12; ?>:</label>
    <input type="text" name="created" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->created) && $EDIT->created > 0 ? mc_convertMySQLDate(date('Y-m-d',$EDIT->created), $SETTINGS) : ''); ?>" class="box" id="created">
    <?php
    }
    ?>
    <label<?php echo (isset($EDIT->id) ? ' style="margin-top:10px"' : ''); ?>><?php echo $msg_blog3; ?>:</label>
    <input type="text" name="published" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->published) && $EDIT->published > 0 ? mc_convertMySQLDate(date('Y-m-d',$EDIT->published), $SETTINGS) : mc_convertMySQLDate(date('Y-m-d'), $SETTINGS)); ?>" class="box" id="published">

    <label style="margin-top:10px"><?php echo $msg_blog4; ?>:</label>
    <input type="text" name="autodelete" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->autodelete) && $EDIT->autodelete > 0 ? mc_convertMySQLDate(date('Y-m-d',$EDIT->autodelete), $SETTINGS) : ''); ?>" class="box" id="autodelete">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($_GET['edit']) ? 'update' : 'process'); ?>" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars((isset($_GET['edit']) ? $msg_blog8 : $msg_blog9)); ?>" title="<?php echo mc_cleanDataEntVars((isset($_GET['edit']) ? $msg_blog8 : $msg_blog9)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=blog\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form><br>

<div class="fieldHeadWrapper" style="margin-top:20px">
  <p><?php echo $msg_blog10; ?>:</p>
</div>

<?php
$q  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "blog`
      ORDER BY `created` DESC
      ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q)>0) {
  while ($BLG = mysqli_fetch_object($q)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
     <b><?php echo mc_safeHTML($BLG->title); ?></b>
     <?php echo ($BLG->created > 0 ? '<br><br><i class="fa fa-calendar fa-fw"></i> '.$msg_blog12 . ': ' . mc_convertMySQLDate(date('Y-m-d', $BLG->created), $SETTINGS) : ''); ?>
     <?php echo ($BLG->published > 0 ? '<br><br><i class="fa fa-calendar fa-fw"></i> '.$msg_blog3 . ': ' . mc_convertMySQLDate(date('Y-m-d', $BLG->published), $SETTINGS) : ''); ?>
     <br><br><i class="fa fa-calendar fa-fw"></i> <?php echo $msg_blog13 . ': ' . ($BLG->autodelete > 0 ? mc_convertMySQLDate(date('Y-m-d', $BLG->autodelete), $SETTINGS) : 'N/A'); ?>
     <br><br><?php echo $msg_blog14; ?>: <?php echo ($BLG->enabled == 'yes' ? $msg_script5 : $msg_script6); ?>
    </div>
    <div class="panel-footer">
      <a href="?p=blog&amp;edit=<?php echo $BLG->id; ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? '<a href="?p=blog&amp;del='.$BLG->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_blog11; ?></span>
<?php
}
?>

</div>
