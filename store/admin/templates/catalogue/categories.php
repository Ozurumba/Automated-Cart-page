<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('categories','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
  $vis = ($EDIT->vis ? explode(',', $EDIT->vis) : array(1));
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_cats8);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_cats16);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_cats11);
}
if (isset($OK4)) {
  echo mc_actionCompleted($msg_cats28);
}
?>

<form method="post" action="?p=categories<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>" enctype="multipart/form-data">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->enCat) ? $msg_cats10 : $msg_cats2); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_cats3; ?>: <?php echo mc_displayHelpTip($msg_javascript39,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" onblur="mc_slugSuggestions(this.value,'rwslug')" type="text" id="catname" name="catname" value="<?php echo (isset($EDIT->catname) ? mc_safeHTML($EDIT->catname) : ''); ?>" maxlength="250" class="box">

    <label style="margin-top:10px"><?php echo $msg_cats13; ?>: <?php echo mc_displayHelpTip($msg_javascript40); ?></label>
    <select tabindex="<?php echo (++$tabIndex); ?>" name="type">
    <option value="new"><?php echo $msg_cats; ?></option>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              ".(isset($EDIT->id) ? 'AND `id`!= \''.$EDIT->id.'\'' : '')."
              ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_cats)>0) {
    ?>
    <optgroup label="<?php echo $msg_cats12; ?>">
    <?php
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <option value="<?php echo $CATS->id; ?>"<?php echo (isset($EDIT->childOf) && $EDIT->childOf==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_infs = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '2'
              AND `childOf`    = '{$CATS->id}'
              ".(isset($EDIT->id) ? 'AND `id`!= \''.$EDIT->id.'\'' : '')."
              ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_infs)>0) {
    while ($CHILDREN = mysqli_fetch_object($q_infs)) {
    ?>
    <option value="child-<?php echo $CHILDREN->id; ?>"<?php echo (isset($EDIT->childOf) && $EDIT->childOf==$CHILDREN->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    }
    }
    }
    ?>
    </optgroup>
    <?php
    }
    ?>
    </select>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="form-group">
    <label><?php echo $msg_cats31; ?>:</label>
    <div class="form-group input-group">
     <span class="input-group-addon"><a href="#" onclick="if(jQuery('#catname').val()!=''){jQuery('#titleBar').val(jQuery('#catname').val())}return false;" title="<?php echo mc_cleanDataEntVars($msg_cats32); ?>" class="a_normal"><i class="fa fa-copy fa-fw"></i></a></span>
     <input type="text" tabindex="<?php echo (++$tabIndex); ?>" name="titleBar" id="titleBar" value="<?php echo (isset($EDIT->titleBar) ? mc_safeHTML($EDIT->titleBar) : ''); ?>" maxlength="250" class="box addon-no-radius">
    </div>
  </div>
  <div class="formRight">
    <label><?php echo $msg_newpages31; ?>: <?php echo mc_displayHelpTip($msg_javascript471); ?></label>
    <input type="text" name="rwslug" onkeyup="mc_slugCleaner('rwslug')" tabindex="<?php echo (++$tabIndex); ?>" maxlength="250" value="<?php echo (isset($EDIT->rwslug) ? mc_safeHTML($EDIT->rwslug) : ''); ?>" class="box">
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_cats9; ?>: <?php echo mc_displayHelpTip($msg_javascript41,'RIGHT'); ?></label>
    <input type="text" tabindex="<?php echo (++$tabIndex); ?>" name="metaKeys" value="<?php echo (isset($EDIT->metaKeys) ? mc_safeHTML($EDIT->metaKeys) : ''); ?>" class="box">
  </div>
  <div class="formRight">
    <label><?php echo $msg_cats6; ?>: <?php echo mc_displayHelpTip($msg_javascript42); ?></label>
    <input type="text" tabindex="<?php echo (++$tabIndex); ?>" name="metaDesc" value="<?php echo (isset($EDIT->metaDesc) ? mc_safeHTML($EDIT->metaDesc) : ''); ?>" class="box">
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <label><?php echo $msg_cats19; ?>: <?php echo mc_displayHelpTip($msg_javascript43,'RIGHT'); ?></label>
  <?php
  if ($SETTINGS->enableBBCode == 'yes') {
    define('BB_BOX', 'comments');
    include(PATH . 'templates/bbcode-buttons.php');
  }
  ?>
  <textarea rows="5" tabindex="<?php echo (++$tabIndex); ?>" class="tarea" cols="30" name="comments" id="comments"><?php echo (isset($EDIT->comments) ? mc_safeHTML($EDIT->comments) : ''); ?></textarea>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_cats23; ?>: <?php echo mc_displayHelpTip($msg_javascript382,'RIGHT'); ?></label>
    <input type="file" tabindex="<?php echo (++$tabIndex); ?>" name="icon">
    <?php
    if (isset($EDIT->id) && $EDIT->imgIcon) {
    ?>
    <span class="small" id="resetArea"><br>( <a class="small" href="#" onclick="mc_deleteCategoryIcon('<?php echo $EDIT->imgIcon; ?>','<?php echo $EDIT->id; ?>');return false;" title="<?php echo mc_cleanDataEntVars($msg_cats24); ?>"><?php echo $msg_cats24; ?></a> )</span>
    <?php
    }
    ?>
  </div>
  <?php
  if (isset($EDIT->imgIcon) && $EDIT->imgIcon && @file_exists($SETTINGS->serverPath.'/'.PRODUCTS_FOLDER.'/'.$EDIT->imgIcon)) {
  ?>
  <div class="formLeft iconarea" style="margin-top:10px">
    <img id="iconImg" src="<?php echo REL_HTTP_PATH . PRODUCTS_FOLDER . '/' . $EDIT->imgIcon; ?>" alt="" title="">
  </div>
  <?php
  }
  ?>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_cats21; ?>: <?php echo mc_displayHelpTip($msg_javascript345,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enDisqus" value="yes"<?php echo (isset($EDIT->enDisqus) && $EDIT->enDisqus=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enDisqus" value="no"<?php echo (isset($EDIT->enDisqus) && $EDIT->enDisqus=='no' ? ' checked="checked"' : (!isset($EDIT->enDisqus) ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_cats26; ?>: <?php echo mc_displayHelpTip($msg_javascript408); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="showRelated" value="yes"<?php echo (isset($EDIT->showRelated) && $EDIT->showRelated=='yes' ? ' checked="checked"' : (!isset($EDIT->showRelated) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="showRelated" value="no"<?php echo (isset($EDIT->showRelated) && $EDIT->showRelated=='no' ? ' checked="checked"' : ''); ?>>

    <label style="margin-top:10px"><?php echo $msg_cats22; ?>: <?php echo mc_displayHelpTip($msg_javascript351); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="freeShipping" value="yes"<?php echo (isset($EDIT->freeShipping) && $EDIT->freeShipping=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="freeShipping" value="no"<?php echo (isset($EDIT->freeShipping) && $EDIT->freeShipping=='no' ? ' checked="checked"' : (!isset($EDIT->freeShipping) ? ' checked="checked"' : '')); ?>>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_admin_cats3_0[1]; ?>: <?php echo mc_displayHelpTip($msg_javascript570,'RIGHT'); ?></label>
    <div class="checkbox">
      <label><input type="checkbox" name="vis[]" value="1"<?php echo (isset($vis) && in_array(1, $vis) ? ' checked="checked"' : (!isset($vis) ? ' checked="checked"' : '')); ?>> <?php echo $msg_admin_cats3_0[2]; ?></label>
    </div>
    <div class="checkbox">
      <label><input type="checkbox" name="vis[]" value="2"<?php echo (isset($vis) && in_array(2, $vis) ? ' checked="checked"' : ''); ?>> <?php echo $msg_admin_cats3_0[3]; ?></label>
    </div>
    <div class="checkbox">
      <label><input type="checkbox" name="vis[]" value="3"<?php echo (isset($vis) && in_array(3, $vis) ? ' checked="checked"' : ''); ?>> <?php echo $msg_admin_cats3_0[4]; ?></label>
    </div>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_cats33; ?>: <?php echo mc_displayHelpTip($msg_javascript570,'RIGHT'); ?></label>
    <select name="theme" tabindex="<?php echo ++$tabIndex; ?>">
	<option value=""><?php echo $SETTINGS->theme; ?></option>
    <?php
    if (is_dir(REL_PATH.'content')) {
      $showtheme = opendir(REL_PATH.'content');
      while (false!==($read=readdir($showtheme))) {
        if (is_dir(REL_PATH.'content/'.$read) && substr(strtolower($read),0,6)=='_theme' && $read!=$SETTINGS->theme) {
          echo '<option value="'.$read.'"'.(isset($EDIT->theme) && $read==$EDIT->theme ? ' selected="selected"' : '').'>'.$read.'</option>'.mc_defineNewline();
        }
      }
      closedir($showtheme);
    }
	?>
	</select>
  </div>
  <div class="formRight">
    <label><?php echo $msg_cats14; ?>: <?php echo mc_displayHelpTip($msg_javascript44); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enCat" value="yes"<?php echo (isset($EDIT->enCat) && $EDIT->enCat=='yes' ? ' checked="checked"' : (!isset($EDIT->enCat) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enCat" value="no"<?php echo (isset($EDIT->enCat) && $EDIT->enCat=='no' ? ' checked="checked"' : ''); ?>>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->enCat) ? 'update' : 'process'); ?>" value="<?php echo (isset($EDIT->enCat) ? $EDIT->id : 'yes'); ?>">
 <input type="hidden" name="originalFolder" value="<?php echo (isset($EDIT->catFolder) ? $EDIT->catFolder : 'products'); ?>">
 <input type="hidden" name="icon" value="<?php echo (isset($EDIT->imgIcon) ? $EDIT->imgIcon : ''); ?>">
 <input type="hidden" name="level" value="<?php echo (isset($EDIT->catLevel) ? $EDIT->catLevel : ''); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->enCat) ? $msg_cats10 : $msg_cats2)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->enCat) ? $msg_cats10 : $msg_cats2)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=categories\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form>

<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><?php echo $msg_cats5; ?>:</p>
</div>

<form method="post" action="?p=categories">
<?php
$q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
          WHERE `catLevel` = '1'
          AND `childOf`    = '0'
          ORDER BY `orderBy`,`catName`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_cats)>0) {
  while ($CATS = mysqli_fetch_object($q_cats)) {
  ?>
  <div class="panel panel-default">
  <div class="panel-body">
    <input type="hidden" name="p[]" value="<?php echo $CATS->id; ?>">
    <?php echo mc_safeHTML($CATS->catname); ?> - <a href="?p=categories&amp;edit=<?php echo $CATS->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9).': '.mc_safeHTML($CATS->catname); ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=categories&amp;del='.$CATS->id.'&amp;parent=yes&amp;icon='.$CATS->imgIcon.'" title="'.mc_cleanDataEntVars($msg_script10).': '.mc_safeHTML($CATS->catname).'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript46).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    <div style="margin:5px 0 10px 0">
    <select name="parentOrder[]">
    <?php
    for ($i=1; $i<(mysqli_num_rows($q_cats)+1); $i++) {
    ?>
    <option value="<?php echo $i; ?>"<?php echo ($CATS->orderBy==$i ? ' selected="selected"' : ''); ?>><?php echo $i; ?></option>
    <?php
    }
    ?>
    </select>
    </div>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `orderBy`,`catName`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_children)>0) {
    ?>
    <hr>
    <?php
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <input type="hidden" name="c[]" value="<?php echo $CHILDREN->id; ?>">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-right"></i> <i class="fa fa-caret-right"></i> <?php echo mc_safeHTML($CHILDREN->catname); ?> - <a href="?p=categories&amp;edit=<?php echo $CHILDREN->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9).': '.mc_safeHTML($CATS->catname.'/'.$CHILDREN->catname); ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=categories&amp;del='.$CHILDREN->id.'&amp;parent=yes&amp;icon='.$CHILDREN->imgIcon.'" title="'.mc_cleanDataEntVars($msg_script10).': '.mc_safeHTML($CATS->catname.'/'.$CHILDREN->catname).'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_admin_cats3_0[0]).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    <div style="margin:10px 0 10px 20px">
    <select name="childOrder[]">
    <?php
    for ($i=1; $i<(mysqli_num_rows($q_children)+1); $i++) {
    ?>
    <option value="<?php echo $i; ?>"<?php echo ($CHILDREN->orderBy==$i ? ' selected="selected"' : ''); ?>><?php echo $i; ?></option>
    <?php
    }
    ?>
    </select>
    </div>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                 WHERE `catLevel` = '3'
                 AND `childOf`    = '{$CHILDREN->id}'
                 ORDER BY `orderBy`,`catName`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_infants)>0) {
    ?>
    <hr>
    <?php
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <input type="hidden" name="i[]" value="<?php echo $INFANTS->id; ?>">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-right"></i> <i class="fa fa-caret-right"></i> <i class="fa fa-caret-right"></i> <?php echo mc_safeHTML($INFANTS->catname); ?> - <a href="?p=categories&amp;edit=<?php echo $INFANTS->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9).': '.mc_safeHTML($CATS->catname.'/'.$CHILDREN->catname.'/'.$INFANTS->catname); ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=categories&amp;del='.$INFANTS->id.'&amp;parent=yes&amp;icon='.$INFANTS->imgIcon.'" title="'.mc_cleanDataEntVars($msg_script10).': '.mc_safeHTML($CATS->catname.'/'.$CHILDREN->catname.'/'.$INFANTS->catname).'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    <div style="margin:10px 0 10px 40px">
    <select name="infantOrder[]">
    <?php
    for ($i=1; $i<(mysqli_num_rows($q_infants)+1); $i++) {
    ?>
    <option value="<?php echo $i; ?>"<?php echo ($INFANTS->orderBy==$i ? ' selected="selected"' : ''); ?>><?php echo $i; ?></option>
    <?php
    }
    ?>
    </select>
    <br>
    </div>
    <?php
    }
    }
    }
    }
    ?>
  </div>
  </div>
  <?php
  }
  ?>
  <p style="text-align:center;margin:20px 0 0 0">
  <input type="hidden" name="process_order" value="yes">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_cats20); ?>" title="<?php echo mc_cleanDataEntVars($msg_cats20); ?>">
  </p>
  <?php
} else {
?>
<span class="noData"><?php echo $msg_cats4; ?></span>
<?php
}
?>
</form>


</div>
