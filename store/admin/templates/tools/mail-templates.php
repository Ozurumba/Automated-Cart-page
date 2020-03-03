<?php if (!defined('PARENT')) { die('Permission Denied'); }
$appd = 'all';
if (isset($_GET['type']) && in_array($_GET['type'],array('personal','trade'))) {
  $appd = $_GET['type'];
}
?>

<div id="content">
<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_newsletter37);
}
if (isset($_GET['deldone'])) {
  echo mc_actionCompleted($msg_newsletter38);
}
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_newsletter33; ?> (<?php echo mc_rowCount('newstemplates'); ?>):</p>
</div>

<form method="post" action="?p=newsletter-templates&amp;type=<?php echo $appd . (isset($_GET['id']) ? '&amp;update='.mc_digitSan($_GET['id']).'&amp;id='.mc_digitSan($_GET['id']) : ''); ?>">

<div class="formFieldWrapper">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="0">- - - - - -</option>
  <?php
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "newstemplates` ORDER BY `subject`")
           or die(mc_MySQLError(__LINE__,__FILE__));
  while ($TP = mysqli_fetch_object($query)) {
  ?>
  <option value="?p=newsletter-templates&amp;type=<?php echo $appd; ?>&amp;id=<?php echo $TP->id; ?>"<?php echo (isset($_GET['id']) && $_GET['id']==$TP->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($TP->subject); ?></option>
  <?php
  }
  ?>
  </select>
</div>
<?php
if (isset($_GET['id'])) {
$TP = mc_getTableData('newstemplates','id',mc_digitSan($_GET['id']));
?>
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_newsletter14; ?>:</label>
    <input type="text" name="from" id="from" value="<?php echo mc_safeHTML($TP->name); ?>" class="box" tabindex="<?php echo (++$tabIndex); ?>">

    <label style="margin-top:10px"><?php echo $msg_newsletter15; ?>:</label>
    <input type="text" name="email" id="email" value="<?php echo mc_safeHTML($TP->email); ?>" class="box" tabindex="<?php echo (++$tabIndex); ?>">
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
 <label><?php echo $msg_newsletter18; ?>:</label>
  <input type="text" name="subject" id="subject" value="<?php echo mc_safeHTML($TP->subject); ?>" class="box" tabindex="<?php echo (++$tabIndex); ?>">
  <span id="helpBlock" class="help-block" style="text-align:left"><?php echo $msg_newsletter25; ?></span>
</div>

<div class="formFieldWrapper" id="iframe" style="display:none">
</div>

<div class="formFieldWrapper">
  <label><?php echo $msg_newsletter16; ?> (<a href="#" onclick="mc_loadPreviewWindow('html','newsletter');return false" title="<?php echo mc_cleanDataEntVars($msg_script68); ?>"><?php echo mc_cleanData($msg_script68); ?></a>):</label>
  <textarea name="html" id="html" rows="5" class="tarea" cols="20"><?php echo mc_safeHTML($TP->html); ?></textarea>
  <span id="helpBlock" class="help-block" style="text-align:left"><?php echo $msg_newsletter26; ?></span>
</div>

<div class="formFieldWrapper">
  <label><?php echo $msg_newsletter17; ?>:</label>
  <textarea name="plain" id="plain" rows="5" class="tarea" cols="20"><?php echo mc_safeHTML($TP->plain); ?></textarea>
  <span id="helpBlock" class="help-block" style="text-align:left"><?php echo $msg_newsletter26; ?></span>
</div>

<p style="text-align:center;padding:10px 0 0 0">
  <input type="hidden" name="process" value="yes">
  <input class="btn btn-primary" type="submit" name="update" value="<?php echo mc_cleanDataEntVars($msg_newsletter36); ?>" title="<?php echo mc_cleanDataEntVars($msg_newsletter36); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <button class="btn btn-success" type="button" onclick="window.location='?p=newsletter-mail&amp;type=<?php echo $appd; ?>'"><?php echo $msg_script11; ?></button>
  <?php
  if (isset($_GET['id']) && $uDel == 'yes') {
  ?>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <button class="btn btn-danger" type="button" onclick="return mc_confirmMessageUrl('<?php echo mc_filterJS($msg_javascript45); ?>','?p=newsletter-templates&amp;del=<?php echo mc_digitSan($_GET['id']); ?>')"><?php echo $msg_newsletter35; ?></button>
  <?php
  }
  ?>
</p>
<?php
} else {
?>
<button class="btn btn-success" type="button" onclick="window.location='?p=newsletter-mail&amp;type=<?php echo $appd; ?>'"><?php echo $msg_script11; ?></button>
<?php
}
?>
</form>


</div>
