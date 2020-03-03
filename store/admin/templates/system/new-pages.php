<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('newpages','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
  $pos  = explode(',',$EDIT->linkPos);
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_newpages10);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_newpages11);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_newpages12);
}
?>

<form method="post" action="?p=newpages<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_newpages6 : $msg_newpages3); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_newpages2; ?>: <?php echo mc_displayHelpTip($msg_javascript202,'RIGHT'); ?></label>
    <input type="text" name="pageName" onblur="mc_slugSuggestions(this.value,'rwslug')" tabindex="<?php echo (++$tabIndex); ?>" maxlength="250" value="<?php echo (isset($EDIT->pageName) ? mc_safeHTML($EDIT->pageName) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_newpages4; ?>: <?php echo mc_displayHelpTip($msg_javascript203); ?></label>
    <input type="text" name="pageKeys" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pageKeys) ? mc_safeHTML($EDIT->pageKeys) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_newpages5; ?>: <?php echo mc_displayHelpTip($msg_javascript203,'LEFT'); ?></label>
    <input type="text" name="pageDesc" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pageDesc) ? mc_safeHTML($EDIT->pageDesc) : ''); ?>" class="box">
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper" id="ptWrap"<?php echo (!isset($EDIT->id) || isset($EDIT->id) && $EDIT->customTemplate=='' ? '' : ' style="display:none"'); ?>>
  <label><?php echo $msg_newpages8; ?>: <?php echo mc_displayHelpTip($msg_javascript204,'RIGHT'); ?></label>
  <?php
  if ($SETTINGS->enableBBCode == 'yes') {
    define('BB_BOX', 'ptTextArea');
    include(PATH . 'templates/bbcode-buttons.php');
  }
  ?>
  <textarea rows="5" cols="30" name="pageText" tabindex="<?php echo (++$tabIndex); ?>" id="ptTextArea"><?php echo (isset($EDIT->pageText) ? mc_safeHTML(mc_cleanData($EDIT->pageText))  : ''); ?></textarea>
  <br class="clear">
</div>

<?php
if ((!isset($EDIT->id) || (isset($EDIT->id) && $EDIT->id!=1))) {
?>
<div class="formFieldWrapper" id="ctWrap">
  <div class="formLeft">
   <label><?php echo $msg_newpages22; ?>: <?php echo mc_displayHelpTip($msg_javascript380,'RIGHT'); ?></label>
   <select name="customTemplate" onchange="if(this.value!=''){jQuery('#ptWrap').hide();}else{jQuery('#ptWrap').show();}">
   <option value="">- - - - - -</option>
   <?php
   if (is_dir(REL_PATH.THEME_FOLDER.'/customTemplates/')) {
     $showtmp = opendir(REL_PATH.THEME_FOLDER.'/customTemplates/');
     while (false!==($read=readdir($showtmp))) {
       if (substr($read,-8)=='.tpl.php') {
         echo '<option value="'.$read.'"'.(isset($EDIT->customTemplate) && $read==$EDIT->customTemplate ? ' selected="selected"' : '').'>'.$read.'</option>'.mc_defineNewline();
       }
     }
     closedir($showtmp);
   }
   ?>
   </select>

   <label style="margin-top:10px"><?php echo $msg_newpages18; ?>: <?php echo mc_displayHelpTip($msg_javascript332); ?></label>
   <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="linkExternal" value="yes"<?php echo (isset($EDIT->linkExternal) && $EDIT->linkExternal=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="linkExternal" value="no"<?php echo (isset($EDIT->linkExternal) && $EDIT->linkExternal=='no' ? ' checked="checked"' : (!isset($EDIT->linkExternal) ? ' checked="checked"' : '')); ?>>

   <label style="margin-top:10px"><?php echo $msg_newpages24; ?>: <?php echo mc_displayHelpTip($msg_javascript404,'LEFT'); ?></label>
    <select name="linkTarget">
    <option value="new"<?php echo (isset($EDIT->linkTarget) && $EDIT->linkTarget=='new' ? ' selected="selected"' : ''); ?>><?php echo $msg_newpages26; ?></option>
    <option value="same"<?php echo (isset($EDIT->linkTarget) && $EDIT->linkTarget=='same' ? ' selected="selected"' : ''); ?>><?php echo $msg_newpages25; ?></option>
    </select>
  </div>
  <br class="clear">
</div>
<?php
}
?>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_newpages14; ?>: <?php echo mc_displayHelpTip($msg_javascript308,'RIGHT'); ?></label>
    <?php echo $msg_newpages15; ?> <input type="checkbox" tabindex="<?php echo (++$tabIndex); ?>" name="linkPos[]" value="1"<?php echo (isset($EDIT->linkPos) && in_array(1,$pos) ? ' checked="checked"' : ''); ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $msg_newpages16; ?> <input type="checkbox" tabindex="<?php echo (++$tabIndex); ?>" name="linkPos[]" value="2"<?php echo (isset($EDIT->linkPos) && in_array(2,$pos) ? ' checked="checked"' : ''); ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $msg_newpages17; ?> <input type="checkbox" tabindex="<?php echo (++$tabIndex); ?>" name="linkPos[]" value="3"<?php echo (isset($EDIT->linkPos) && in_array(3,$pos) ? ' checked="checked"' : ''); ?>>

    <label style="margin-top:10px"><?php echo $msg_newpages27; ?>: <?php echo mc_displayHelpTip($msg_javascript418); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="landingPage" value="yes"<?php echo (isset($EDIT->landingPage) && $EDIT->landingPage=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="landingPage" value="no"<?php echo (isset($EDIT->landingPage) && $EDIT->landingPage=='no' ? ' checked="checked"' : (!isset($EDIT->landingPage) ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_newpages28; ?>: <?php echo mc_displayHelpTip($msg_javascript451,'LEFT'); ?></label>
    <?php echo $msg_newpages29; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="leftColumn" value="yes"<?php echo (isset($EDIT->leftColumn) && $EDIT->leftColumn=='yes' ? ' checked="checked"' : (!isset($EDIT->leftColumn) ? ' checked="checked"' : '')); ?>> <?php echo $msg_newpages30; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="leftColumn" value="no"<?php echo (isset($EDIT->leftColumn) && $EDIT->leftColumn=='no' ? ' checked="checked"' : ''); ?>>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_new_pages[0]; ?>: <?php echo mc_displayHelpTip($msg_javascript6,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="trade" value="yes"<?php echo (isset($EDIT->trade) && $EDIT->trade=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="trade" value="no"<?php echo (isset($EDIT->trade) && $EDIT->trade=='no' ? ' checked="checked"' : (!isset($EDIT->trade) ? ' checked="checked"' : '')); ?>>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_newpages31; ?>: <?php echo mc_displayHelpTip($msg_javascript470,'RIGHT'); ?></label>
    <input type="text" name="rwslug" onkeyup="mc_slugCleaner('rwslug')" tabindex="<?php echo (++$tabIndex); ?>" maxlength="250" value="<?php echo (isset($EDIT->rwslug) ? mc_safeHTML($EDIT->rwslug) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_newpages13; ?>: <?php echo mc_displayHelpTip($msg_javascript6,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enabled" value="yes"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='yes' ? ' checked="checked"' : (!isset($EDIT->enabled) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enabled" value="no"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='no' ? ' checked="checked"' : ''); ?>>

  </div>
</div>

<p style="text-align:center;padding-top:20px">
  <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="yes">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_newpages6 : $msg_newpages3)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_newpages6 : $msg_newpages3)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=newpages\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form><br>

<div class="fieldHeadWrapper" style="margin-top:20px">
  <p><span class="float" id="loader"></span><span class="float" id="loader_msg" style="display:none" onclick="jQuery(this).hide()"></span><?php echo $msg_newpages7; ?>:</p>
</div>
<?php
if (mc_rowCount('newpages')>0) {
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery("#sortable").sortable({
    update : function (data) {
      jQuery("#loader").load("index.php?p=newpages&order=yes&"+jQuery('#sortable').sortable('serialize'));
      jQuery('#loader_msg').show();
      jQuery('#loader_msg').html('<i class="fa fa-check fa-fw"></i>&nbsp;&nbsp;').fadeOut(6000);
    }
  });
});
//]]>
</script>
<?php
}
?>
<div id="sortable">
<?php
$q_npages = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "newpages`
            ORDER BY `orderBy`
            ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_npages)>0) {
  while ($NPAGES = mysqli_fetch_object($q_npages)) {
  ?>
  <div class="panel panel-default" id="pg-<?php echo $NPAGES->id; ?>" style="cursor:move" title="<?php echo mc_cleanDataEntVars($msg_cats20); ?>">
    <div class="panel-body">
     <b><?php echo mc_safeHTML($NPAGES->pageName); ?></b>
     <?php echo ($NPAGES->landingPage=='yes' ? '<br><br><i class="fa fa-check fa-fw"></i> '.$msg_newpages27 . ($NPAGES->trade == 'yes' ? ' (' . $msg_new_pages[1] . ')' : '') : ''); ?>
     <?php echo ($NPAGES->customTemplate ? '<br><br><i class="fa fa-check fa-fw"></i> '.$msg_newpages23 . $NPAGES->customTemplate : ''); ?>
    </div>
    <div class="panel-footer">
      <a href="<?php echo ($NPAGES->linkExternal=='yes' ? mc_cleanData(trim($NPAGES->pageText)) : '../?np='.$NPAGES->id); ?>" onclick="window.open(this);return false" title="<?php echo mc_safeHTML($NPAGES->pageName); ?>"><i class="fa fa-search fa-fw"></i></a>
      <a href="?p=newpages&amp;edit=<?php echo $NPAGES->id; ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($NPAGES->id>1 && $uDel=='yes' ? '<a href="?p=newpages&amp;del='.$NPAGES->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_newpages9; ?></span>
<?php
}
?>
</div>


</div>
