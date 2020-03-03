<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  unset($FIELD_MAPPING,$PRODUCT_OPTIONS);
  echo mc_actionCompleted(str_replace(array('{count}','{count2}'),array($added[0],$added[1]),$msg_import11));
}

if (isset($FIELD_MAPPING)) {
?>
<form method="post" action="?p=product-attributes-import" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript302); ?>')">

<div class="fieldHeadWrapper">
  <p><span class="float"><a onclick="mc_Window(this.href,'<?php echo DIVWIN_FIELD_INFO_HEIGHT; ?>','<?php echo DIVWIN_FIELD_INFO_WIDTH; ?>',this.title);return false;" href="?p=product-import&amp;field=attributes" title="<?php echo mc_cleanDataEntVars($msg_import27); ?>"><i class="fa fa-info-circle fa-fw"></i></a></span><?php echo str_replace('{count}',count($fields),$msg_import9); ?>:</p>
</div>

<?php
for ($i=0; $i<count($fields); $i++) {
?>
<div class="panel panel-default" id="imp_<?php echo $i; ?>">
  <div class="panel-heading">
    <?php echo (PROD_IMPORT_HEAD_TXT_LIMIT > 0 && strlen($fields[$i]) > PROD_IMPORT_HEAD_TXT_LIMIT ? substr(mc_safeHTML($fields[$i]), 0, PROD_IMPORT_HEAD_TXT_LIMIT) . '...' : mc_safeHTML($fields[$i])); ?>
  </div>
  <div class="panel-body" style="overflow-y:auto">
    <select name="dbFields[]">
      <option value="0"><?php echo $msg_import55; ?></option>
      <option value="0"><?php echo $msg_import53; ?></option>
      <?php
      if (!empty($fieldMapping_vars)) {
        foreach ($fieldMapping_vars AS $k => $v) {
        ?>
        <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
        <?php
        }
      }
      ?>
    </select>
  </div>
</div>
<?php
}
?>

<p style="text-align:center;padding:20px 0 20px 0">
 <input type="hidden" name="process-attributes-mapping" value="yes">
 <input type="hidden" name="file" value="<?php echo (isset($_SESSION['curImportFile']) ? $_SESSION['curImportFile'] : ''); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_import7); ?>" title="<?php echo mc_cleanDataEntVars($msg_import7); ?>">
</p>
<?php
} elseif (isset($PRODUCT_OPTIONS)) {
?>
<form method="post" action="?p=product-attributes-import" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript304); ?>')">

<div class="fieldHeadWrapper">
  <p><?php echo $msg_import3; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_import14; ?>:</label>
    <select name="category" onchange="mc_getCategoryList(this.value,'product')" tabindex="<?php echo (++$tabIndex); ?>">
    <option value="0">- - - - - -</option>
    <?php
    $getFirstCat     = 0;
    $getFirstCatMan  = '';
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <option value="<?php echo $CATS->id; ?>"><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <option value="<?php echo $CHILDREN->id; ?>">- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <option value="<?php echo $INFANTS->id; ?>">&nbsp;&nbsp;- <?php echo mc_safeHTML($INFANTS->catname); ?></option>
    <?php
    }
    }
    }
    ?>
    </select>

    <label style="margin-top:10px"><?php echo $msg_import15; ?>:</label>
    <div class="productList" id="prds">
      <i class="fa fa-warning fa-fw"></i> <?php echo $msg_import57; ?>
    </div>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_import56; ?>: <?php echo mc_displayHelpTip($msg_javascript416,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" maxlength="100" type="text" class="box" name="attrGroup" value="">

    <label style="margin-top:10px"><?php echo $msg_prodattributes29; ?>: <?php echo mc_displayHelpTip($msg_javascript428); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="allowMultiple" value="yes"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="allowMultiple" value="no" checked="checked">

    <label style="margin-top:10px"><?php echo $msg_prodattributes31; ?>: <?php echo mc_displayHelpTip($msg_javascript434,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="isRequired" value="yes"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="isRequired" value="no" checked="checked">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process-attributes" value="yes">
 <input type="hidden" name="file" value="<?php echo (isset($_SESSION['curImportFile']) ? $_SESSION['curImportFile'] : ''); ?>">
 <input class="btn btn-primary" disabled="disabled" type="submit" value="<?php echo mc_cleanDataEntVars($msg_import12); ?>" title="<?php echo mc_cleanDataEntVars($msg_import12); ?>">
</p>
<?php
} else {
?>
<form method="post" action="?p=product-attributes-import" enctype="multipart/form-data" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript180); ?>')">

<div class="fieldHeadWrapper">
  <p><?php echo $msg_import18; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_import8; ?>: <?php echo mc_displayHelpTip($msg_javascript179,'RIGHT'); ?></label>
    <input type="file" name="file">

    <label style="margin-top:10px"><?php echo $msg_import5; ?>: <?php echo mc_displayHelpTip($msg_javascript176); ?></label>
    <input type="text" name="lines" value="5000" class="box">

    <label style="margin-top:10px"><?php echo $msg_import6; ?>: <?php echo mc_displayHelpTip($msg_javascript177,'LEFT'); ?></label>
    <input type="text" name="del" value="&#044;" class="box">
    <input style="margin-top:5px" type="text" name="enc" value="&quot;" class="box">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process-upload" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_import7); ?>" title="<?php echo mc_cleanDataEntVars($msg_import7); ?>">
</p>
<?php
}
?>
</form>

</div>
