<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['load'])) {
  $loadID = (int) $_GET['load'];
  $TP     = mc_getTableData('newstemplates','id',$loadID);
}
$SQL  = '';
$appd = 'all';
if (isset($_GET['type']) && in_array($_GET['type'],array('personal','trade'))) {
  $SQL  = 'AND `type` = \'' . mc_safeSQL($_GET['type']) . '\'';
  $appd = $_GET['type'];
}
switch($appd) {
  case 'all':
    $txt = $msg_news_letter[1];
    break;
  case 'personal':
    $txt = $msg_news_letter[2];
    break;
  case 'trade':
    $txt = $msg_news_letter[3];
    break;
}
$howManyAcc = mc_rowCount('accounts',' WHERE `enabled` = \'yes\' AND `verified` = \'yes\' AND `newsletter` = \'yes\' ' . $SQL);
?>
<div id="content">
<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery('input[name="new_temp"]').autocomplete({
	  source: 'index.php?p=newsletter-mail&search=yes',
		minLength: 3,
		select: function(event, ui) {
      jQuery('#newsTemplates').slideUp(function() {
        if (ui.item.value > 0) {
          window.location = 'index.php?p=newsletter-mail&load=' + ui.item.value;
        }
      });
		}
  });
});
//]]>
</script>

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',number_format($sent),$msg_newsletter20));
}
?>

<div class="fieldHeadWrapper" id="mswhead">
  <p><span class="float">
   <a href="#" onclick="mc_SaveMailTemplate();return false"><i class="fa fa-save fa-fw"></i></a>
   <a href="#" onclick="<?php echo (mc_rowCount('newstemplates') > 0 ? 'jQuery(\'#newsTemplates\').slideToggle();' : 'mc_alertBox(\'' . mc_filterJS($msg_javascript468) . '\');'); ?>return false"><i class="fa fa-search fa-fw"></i></a>
   <a href="?p=newsletter-templates&amp;type=<?php echo $appd; ?>" title="<?php echo mc_cleanDataEntVars($msg_newsletter29); ?>"><i class="fa fa-pencil fa-fw"></i></a>
  </span>
  <?php echo mc_cleanData($msg_newsletter11); ?> (<?php echo $txt; ?>) (<?php echo @number_format($howManyAcc); ?>):</p>
</div>

<div class="formFieldWrapper" id="newsTemplates" style="display:none">
  <input type="text" class="box" name="new_temp" value="" placeholder="<?php echo mc_cleanDataEntVars($msg_admin3_0[27]); ?>">
</div>

<?php
if ($SETTINGS->smtp == 'yes') {
if ($howManyAcc > 0) {
$htmlMessage   = '';
$plainMessage  = '';
if (isset($TP->html)) {
  $htmlMessage = mc_cleanData($TP->html);
} else {
  if (file_exists(MCLANG . 'default-newsletter/html-message.html')) {
    $htmlMessage = str_replace('{charset}',$mail_charset,file_get_contents(MCLANG . 'default-newsletter/html-message.html'));
  }
}
if (isset($TP->plain)) {
  $plainMessage = mc_cleanData($TP->plain);
} else {
  if (file_exists(MCLANG . 'default-newsletter/plain-text-message.txt')) {
    $plainMessage = file_get_contents(MCLANG . 'default-newsletter/plain-text-message.txt');
  }
}
?>
<form method="post" action="?p=newsletter-mail&amp;type=<?php echo $appd . (isset($_GET['load']) ? '&amp;load=' . $loadID : ''); ?>" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')" enctype="multipart/form-data">

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_newsletter14; ?>:</label>
    <input type="text" name="from" id="from" value="<?php echo mc_safeHTML((isset($TP->name) ? $TP->name : $SETTINGS->website)); ?>" class="box" tabindex="<?php echo (++$tabIndex); ?>">

    <label style="margin-top:10px"><?php echo $msg_newsletter15; ?>:</label>
    <input type="text" name="email" id="email" value="<?php echo mc_safeHTML((isset($TP->email) ? $TP->email : $SETTINGS->email)); ?>" class="box" tabindex="<?php echo (++$tabIndex); ?>">
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <label><?php echo $msg_newsletter18; ?>:</label>
  <input type="text" name="subject" id="subject" value="<?php echo mc_safeHTML((isset($TP->subject) ? $TP->subject : $msg_newsletter21)); ?>" class="box" tabindex="<?php echo (++$tabIndex); ?>">
  <span id="helpBlock" class="help-block" style="text-align:left"><?php echo $msg_newsletter25; ?></span>
</div>

<div class="formFieldWrapper" id="iframe" style="display:none">
</div>

<div class="formFieldWrapper">
  <label><?php echo $msg_newsletter16; ?> (<a href="#" onclick="mc_loadPreviewWindow('html','newsletter');return false" title="<?php echo mc_cleanDataEntVars($msg_script68); ?>"><?php echo mc_cleanData($msg_script68); ?></a>): <?php echo mc_displayHelpTip(str_replace('{lang}',$SETTINGS->languagePref,$msg_javascript387),'RIGHT'); ?></label>
  <textarea name="html" id="html" rows="5" class="tarea" cols="20"><?php echo mc_safeHTML($htmlMessage); ?></textarea>
  <span id="helpBlock" class="help-block" style="text-align:left"><?php echo $msg_newsletter26; ?></span>
</div>

<div class="formFieldWrapper">
  <label><?php echo $msg_newsletter17; ?>: <?php echo mc_displayHelpTip(str_replace('{lang}',$SETTINGS->languagePref,$msg_javascript388),'RIGHT'); ?></label>
  <textarea name="plain" id="plain" rows="5" class="tarea" cols="20"><?php echo mc_safeHTML($plainMessage); ?></textarea>
  <span id="helpBlock" class="help-block" style="text-align:left"><?php echo $msg_newsletter26; ?></span>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_newsletter22; ?>: <?php echo mc_displayHelpTip($msg_javascript389,'RIGHT'); ?></label>
    <input type="file" name="attachment[]">

    <div style="margin-top:10px">
      <button type="button" class="btn btn-primary btn-xs" onclick="mc_AttBox('add','attachment')"><i class="fa fa-plus fa-fw"></i></button>
      <button type="button" class="btn btn-success btn-xs" onclick="mc_AttBox('minus','attachment')"><i class="fa fa-minus fa-fw"></i></button>
    </div>

  </div>
  <br class="clear">
</div>

<?php
if (isset($TP->id)) {
?>
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_newsletter32; ?>: <?php echo mc_displayHelpTip($msg_javascript469,'RIGHT'); ?></label>
    <input type="checkbox" name="updateTemp" value="yes" checked="checked">
  </div>
  <br class="clear">
</div>
<?php
}
?>

<p style="text-align:center;padding:20px 0 20px 0">
 <input type="hidden" name="process" value="yes">
 <input type="hidden" name="atype" value="<?php echo $appd; ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_newsletter19); ?>" title="<?php echo mc_cleanDataEntVars($msg_newsletter19); ?>">
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location='?p=newsletter'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
</p>

</form>
<?php
} else {
?>
<span class="noData"><?php echo $msg_newsletter5; ?></span>
<?php
}
} else {
?>
<span class="noData"><?php echo $msg_newsletter12; ?></span>
<?php
}
?>
</div>
