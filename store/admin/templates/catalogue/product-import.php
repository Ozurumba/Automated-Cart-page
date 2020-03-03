<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  unset($FIELD_MAPPING,$PRODUCT_OPTIONS);
  echo mc_actionCompleted(str_replace('{count}',$added,$msg_import10));
}

if (isset($FIELD_MAPPING)) {
?>
<form method="post" action="?p=product-import" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript302); ?>')">

<div class="fieldHeadWrapper">
  <p><span class="float"><a onclick="mc_Window(this.href,'<?php echo DIVWIN_FIELD_INFO_HEIGHT; ?>','<?php echo DIVWIN_FIELD_INFO_WIDTH; ?>',this.title);return false;" href="?p=product-import&amp;field=products" title="<?php echo mc_cleanDataEntVars($msg_import27); ?>"><i class="fa fa-info-circle fa-fw"></i></a></span><?php echo str_replace('{count}',count($fields),$msg_import9); ?>:</p>
</div>

<?php
for ($i=0; $i<count($fields); $i++) {
?>
<div class="panel panel-default" id="imp_<?php echo $i; ?>">
  <div class="panel-heading">
    <?php echo (PROD_IMPORT_HEAD_TXT_LIMIT > 0 && strlen($fields[$i]) > PROD_IMPORT_HEAD_TXT_LIMIT ? substr(mc_safeHTML($fields[$i]), 0, PROD_IMPORT_HEAD_TXT_LIMIT) . '...' : ($fields[$i] ? mc_safeHTML($fields[$i]) : '- - -')); ?>
  </div>
  <div class="panel-body" style="overflow-y:auto">
    <select name="dbFields[]">
     <option value="0"><?php echo $msg_import55; ?></option>
     <option value="0"><?php echo $msg_import53; ?></option>
     <?php
     if (!empty($fieldMapping_products)) {
       foreach ($fieldMapping_products AS $k => $v) {
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
 <input type="hidden" name="process-products-mapping" value="yes">
 <input type="hidden" name="file" value="<?php echo (isset($_SESSION['curImportFile']) ? $_SESSION['curImportFile'] : ''); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_import7); ?>" title="<?php echo mc_cleanDataEntVars($msg_import7); ?>">
</p>
<?php
} elseif (isset($PRODUCT_OPTIONS)) {
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_import16; ?>:</p>
</div>

<form method="post" action="?p=product-import" id="form" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript180); ?>')">
<div class="formFieldWrapper">
  <div class="formLeft">
    <div class="categoryBoxes" id="cats">
    <input type="checkbox" name="log" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'cats')" tabindex="<?php echo (++$tabIndex); ?>"> <b><?php echo $msg_productadd35; ?></b><br>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <p id="cat_<?php echo $CATS->id; ?>"><input onclick="if(this.checked){mc_selectChildren('cat_<?php echo $CATS->id; ?>','on')}else{mc_selectChildren('cat_<?php echo $CATS->id; ?>','off')}" tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="pCat[]" value="<?php echo $CATS->id; ?>"> <?php echo mc_safeHTML($CATS->catname); ?><br>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <span id="child_<?php echo $CHILDREN->id; ?>">
    &nbsp;&nbsp;<input onclick="if(this.checked){mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','on')}else{mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','off')}" tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="pCat[]" value="<?php echo $CHILDREN->id; ?>"> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="pCat[]" value="<?php echo $INFANTS->id; ?>"> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
    <?php
    }
    ?>
    </span>
    <?php
    }
    ?>
    </p>
    <?php
    }
    ?>
    </div>
  </div>
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_import17; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <div class="categoryBoxes" id="brands">
    <input type="checkbox" name="log2" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'brands')" tabindex="<?php echo (++$tabIndex); ?>"> <b><?php echo $msg_productadd50; ?></b><br>
    <?php
    $q_mans = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "brands`.`id` AS `bid` FROM `" . DB_PREFIX . "brands`
              LEFT JOIN `" . DB_PREFIX . "categories`
              ON `" . DB_PREFIX . "brands`.`bCat` = `" . DB_PREFIX . "categories`.`id`
              WHERE `enBrand`  = 'yes'
              AND `enCat`      = 'yes'
              ORDER BY `catname`,`name`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($BRAND = mysqli_fetch_object($q_mans)) {
      $parents = '';
      switch($BRAND->catLevel) {
        case '2':
          $CAT      = mc_getTableData('categories','id',$BRAND->childOf);
          $parents  = $CAT->catname.'/';
          break;
        case '3':
          $CAT      = mc_getTableData('categories','id',$BRAND->childOf);
          $CAT2     = mc_getTableData('categories','id',$CAT->childOf);
          $parents  = $CAT2->catname.'/'.$CAT->catname.'/';
          break;
      }
    ?>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="pBrand[]" value="<?php echo $BRAND->bid; ?>"> <?php echo mc_safeHTML($parents.$BRAND->catname.'/'.$BRAND->name); ?><br>
    <?php
    }
    ?>
    </div>
  </div>

  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_import26; ?>: <?php echo mc_displayHelpTip($msg_javascript347,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enDisqus" value="yes"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enDisqus" value="no" checked="checked">

    <label style="margin-top:10px"><?php echo $msg_productadd54; ?>: <?php echo mc_displayHelpTip($msg_javascript353,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="freeShipping" value="yes"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="freeShipping" value="no" checked="checked">

    <label style="margin-top:10px"><?php echo $msg_import51; ?>: <?php echo mc_displayHelpTip($msg_javascript403,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="pPurchase" value="yes" checked="checked"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="pPurchase" value="no">

    <label style="margin-top:10px"><?php echo $msg_import45; ?>: <?php echo mc_displayHelpTip($msg_javascript348,'LEFT'); ?></label>
    <?php echo $msg_import46; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="pEnable" value="yes" checked="checked"> <?php echo $msg_import13; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="pEnable" value="no">
  </div>

  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process-products" value="yes">
 <input type="hidden" name="file" value="<?php echo (isset($_SESSION['curImportFile']) ? $_SESSION['curImportFile'] : ''); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_import4); ?>" title="<?php echo mc_cleanDataEntVars($msg_import4); ?>">
</p>
<?php
} else {
?>
<form method="post" action="?p=product-import" enctype="multipart/form-data" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript180); ?>')">
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
    <input  style="margin-top:5px" type="text" name="enc" value="&quot;" class="box">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process-upload-products" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_import7); ?>" title="<?php echo mc_cleanDataEntVars($msg_import7); ?>">
</p>
<?php
}
?>
</form>

</div>
