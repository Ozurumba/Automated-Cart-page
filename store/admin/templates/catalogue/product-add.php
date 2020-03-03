<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit']) && $_GET['edit']!='batch-mode') {
  $EDIT          = mc_getTableData('products','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
  $CTS           = mc_getProductCategories(mc_digitSan($_GET['edit']),false);
  $BRD           = mc_getProductBrands(mc_digitSan($_GET['edit']),false);
}
if (isset($_GET['edit']) && $_GET['edit']=='batch-mode' && isset($_POST['productIDs'])) {
  define('BATCH_EDIT_MODE',1);
  if (isset($_POST['productsUpdated'])) {
    $batchCount = $_POST['productsUpdated'];
  } else {
    $batchCount = count($_POST['productIDs']);
  }
}
if (isset($_GET['copyp'])) {
  $EDIT = new stdclass();
  $COPY = mc_getTableData('products','id',mc_digitSan($_GET['copyp']));
  $CTS  = mc_getProductCategories(mc_digitSan($_GET['copyp']),false);
  $BRD  = mc_getProductBrands(mc_digitSan($_GET['copyp']),false);
  foreach ($COPY AS $key => $value) {
    $EDIT->$key = $value;
  }
}
?>
<div id="content">
<script>
//<![CDATA[
function checkForm() {
  if (jQuery('#pName').val()=='') {
    mc_alertBox('<?php echo mc_cleanDataEntVars($msg_javascript384); ?>');
    jQuery('#pName').focus();
    return false;
  }
}
function codeChecker() {
  // Check product code..
  if (jQuery('input[name="pCode"]').val()!='') {
    mc_ShowSpinner();
    jQuery(document).ready(function() {
      jQuery.ajax({
        url: 'index.php',
        data: 'p=add-product&codeCheck='+jQuery('input[name="pCode"]').val()<?php echo (isset($_GET['edit']) ? '+\'&edit='.mc_digitSan($_GET['edit']).'\'' : ''); ?>,
        dataType: 'json',
        success: function (data) {
          mc_CloseSpinner();
          if (data == 'exists') {
            jQuery('input[name="pCode"]').css('background', 'url(templates/images/error.png) no-repeat 98% 50%');
          } else {
            jQuery('input[name="pCode"]').css('background', 'url(templates/images/ok.png) no-repeat 98% 50%');
          }
        }
      });
    });
  } else {
    jQuery('input[name="pCode"]').focus();
  }
  return false;
}
//]]>
</script>
<?php
define('CALBOX', 'pOfferExpiry|expiry'.(isset($EDIT->id) || isset($_GET['copyp']) || defined('BATCH_EDIT_MODE') ? '|batch_field_20' : ''));
include(PATH.'templates/js-loader/date-picker.php');
if (isset($OK)) {
  echo mc_actionCompleted($msg_productadd13);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_productadd21);
}
if (isset($OK3)) {
  unset($_SESSION['batchFieldPrefs']);
  echo mc_actionCompleted(str_replace(array('{count}','{fields}'),array($_POST['productsUpdated'],$fields),$msg_productadd80));
}
?>

<?php
if (defined('BATCH_EDIT_MODE')) {
?>
<div class="alert alert-warning">
<?php echo str_replace('{count}',$batchCount,$msg_productmanage53).'<hr>'; ?>
<div style="height: 100px;overflow:auto">
<?php
$batch_ids = (isset($_POST['productsUpdated']) ? $_POST['productIDs'] : implode(',',$_POST['productIDs']));
$q_prod = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `pName` FROM `" . DB_PREFIX . "products`
          WHERE `id` IN(" . $batch_ids . ")
          ORDER BY `pName`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
while ($B_P = mysqli_fetch_object($q_prod)) {
?>
<div><?php echo mc_safeHTML($B_P->pName); ?></div>
<?php
}
?>
</div>
</div>
<?php
}
if (isset($_GET['edit']) && $_GET['edit']!='batch-mode') {
// Clear batch session..
if (isset($_SESSION['batchFieldPrefs'])) {
  unset($_SESSION['batchFieldPrefs']);
}

$P = mc_getTableData('products','id',mc_digitSan($_GET['edit']));
$thisProductID = mc_digitSan($_GET['edit']);
?>
<div class="alert alert-info">
  <?php
  $qLinksArr  = array('product-edit');
  if (isset($_GET['copyp'])) {
    $qLinksArr  = array('product-copy');
  }
  $qLinksIcon = 'pencil';
  include(PATH . 'templates/catalogue/product-quick-links.php');
  ?>
</div>
<?php
}
if (isset($_GET['copyp'])) {
?>
<div class="alert alert-info">
  <?php
  $P = mc_getTableData('products','id',mc_digitSan($_GET['copyp']));
  echo '<i class="fa fa-copy fa-fw"></i> ' . mc_cleanData($P->pName); ?>
</div>
<?php
}
?>

<form method="post" id="form" action="?p=add-product<?php echo (isset($EDIT->id) && !isset($_GET['copyp']) ? '&amp;edit='.$EDIT->id : '').(defined('BATCH_EDIT_MODE') ? '&amp;edit=batch-mode' : '').(isset($_GET['copyp']) ? '&amp;copyp='.mc_digitSan($_GET['copyp']) : ''); ?>"<?php echo (!defined('BATCH_EDIT_MODE') ? ' enctype="multipart/form-data" ' : ' '); ?>onsubmit="return checkForm()">
<?php
if (!defined('BATCH_EDIT_MODE')) {
?>
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) && !isset($_GET['copyp']) ? $msg_productadd15 : $msg_productadd2); ?>:</p>
</div>
<div class="formFieldWrapper">
  <div class="formLeft">
    <?php
    if ($SETTINGS->isbnAPI) {
    ?>
    <label><?php echo $msg_productadd73; ?>:</label>
    <div class="form-group input-group" style="margin-bottom:0;padding-bottom:0">
      <span class="input-group-addon"><a class="isbn" href="#" onclick="mc_isbnLookup();return false" title="<?php echo mc_cleanDataEntVars($msg_productadd72); ?>"><i class="fa fa-book fa-fw"></i></a></span>
      <input type="text" maxlength="250" onblur="mc_slugSuggestions(this.value,'rwslug')" name="pName" id="pName" value="<?php echo (isset($EDIT->pName) ? mc_safeHTML($EDIT->pName) : ''); ?>" class="box addon-no-radius" tabindex="<?php echo (++$tabIndex); ?>">
    </div>
    <?php
    } else {
    ?>
    <label><?php echo $msg_productadd4; ?>:</label>
    <input type="text" maxlength="250" onblur="mc_slugSuggestions(this.value,'rwslug')" name="pName" id="pName" value="<?php echo (isset($EDIT->pName) ? mc_safeHTML($EDIT->pName) : ''); ?>" class="box" tabindex="<?php echo (++$tabIndex); ?>">
    <?php
    }
    ?>
  </div>
</div>
<?php
}
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd61; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('checkGrid','pCat','omitpCat'); ?> <?php echo mc_displayHelpTip($msg_javascript70,'RIGHT'); ?></label>
    <div id="omitpCat"<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pCat',$_SESSION['batchFieldPrefs']) ? '' : ' style="display:none"'); ?>><?php echo $msg_admin_product3_0[12]; ?></div>
    <div class="categoryAddBoxes" style="width:100%<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pCat',$_SESSION['batchFieldPrefs']) ? ';display:none' : ''); ?>" id="checkGrid">
    <input type="checkbox" id="all" name="log" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'checkGrid')" tabindex="<?php echo (++$tabIndex); ?>"> <b><?php echo $msg_productadd35; ?></b> (<a href="#" title="<?php echo mc_cleanDataEntVars($msg_productadd74); ?>" onclick="mc_parentsOnly();return false"><?php echo $msg_productadd74; ?></a>)<br>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              ".(SHOW_DISABLED_CATS_ADD_PRODUCT ? 'AND `enCat` = \'yes\'' : '')."
              ORDER BY `catname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <p id="cat_<?php echo $CATS->id; ?>"><input id="pnt_<?php echo $CATS->id; ?>" onclick="if(this.checked){mc_selectChildren('cat_<?php echo $CATS->id; ?>','on')}else{mc_selectChildren('cat_<?php echo $CATS->id; ?>','off')}" tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="pCat[]" value="<?php echo $CATS->id; ?>"<?php echo (isset($CTS) && !empty($CTS) && in_array($CATS->id,$CTS) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?><br>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `childOf`    = '{$CATS->id}'
                  AND `enCat`      = 'yes'
                  ".(SHOW_DISABLED_CATS_ADD_PRODUCT ? 'AND `enCat` = \'yes\'' : '')."
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <span id="child_<?php echo $CHILDREN->id; ?>">
    &nbsp;&nbsp;<input id="cld_<?php echo $CHILDREN->id; ?>" onclick="if(this.checked){mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','on')}else{mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','off')}" tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="pCat[]" value="<?php echo $CHILDREN->id; ?>"<?php echo (isset($CTS) && !empty($CTS) && in_array($CHILDREN->id,$CTS) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ".(SHOW_DISABLED_CATS_ADD_PRODUCT ? 'AND `enCat` = \'yes\'' : '')."
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input id="inf_<?php echo $INFANTS->id; ?>" tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="pCat[]" value="<?php echo $INFANTS->id; ?>"<?php echo (isset($CTS) && !empty($CTS) && in_array($INFANTS->id,$CTS) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
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

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_1','pBrand','omitpBrand'); ?> <?php echo mc_displayHelpTip($msg_javascript162); ?></label>
    <div id="omitpBrand"<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pBrand',$_SESSION['batchFieldPrefs']) ? '' : ' style="display:none"'); ?>><?php echo $msg_admin_product3_0[13]; ?></div>
    <div class="categoryAddBoxes" id="batch_field_1"<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pBrand',$_SESSION['batchFieldPrefs']) ? ' style="display:none"' : ''); ?>>
    <input type="checkbox" name="log2" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'batch_field_1')" tabindex="<?php echo (++$tabIndex); ?>"> <b><?php echo $msg_productadd50; ?></b><br>
    <?php
    $brandLoop = array();
    $q_mans = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "brands`.`id` AS `bid` FROM `" . DB_PREFIX . "brands`
              LEFT JOIN `" . DB_PREFIX . "categories`
              ON `" . DB_PREFIX . "brands`.`bCat` = `" . DB_PREFIX . "categories`.`id`
              WHERE `enBrand`               = 'yes'
              ORDER BY `catname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($BRAND = mysqli_fetch_object($q_mans)) {
      $parents = '';
      if ($BRAND->bCat=='all') {
        $parents = '';
      } else {
          switch($BRAND->catLevel) {
            case '2':
            $CAT      = mc_getTableData('categories','id',$BRAND->childOf);
            $parents  = $CAT->catname . ' / ';
            break;
            case '3':
            $CAT      = mc_getTableData('categories','id',$BRAND->childOf);
            $CAT2     = mc_getTableData('categories','id',$CAT->childOf);
            $parents  = $CAT2->catname . ' / ' . $CAT->catname . ' / ';
            break;
          }
      }
      $brandLoop[$BRAND->bid] = mc_safeHTML($parents . ($BRAND->catname ? $BRAND->catname . ' / ' : '') . $BRAND->name);
    }
    if (!empty($brandLoop)) {
      asort($brandLoop, SORT_STRING);
      foreach ($brandLoop AS $brID => $brNm) {
      ?>
      <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="pBrand[]" value="<?php echo $brID; ?>"<?php echo (isset($BRD) && !empty($BRD) && in_array($brID,$BRD) ? ' checked="checked"' : ''); ?>> <?php echo $brNm; ?><br>
      <?php
      }
    }
    ?>
    </div>
  </div>
  <br class="clear">
</div>

<?php
if (!defined('BATCH_EDIT_MODE')) {
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd65; ?>:</p>
</div>

<div class="formFieldWrapper">
  <label><?php echo $msg_productadd64; ?>: <a href="#" onclick="if(jQuery('#desc').val()!=''){jQuery('#short_desc').val(jQuery('#desc').val())}return false;" title="<?php echo mc_cleanDataEntVars($msg_productadd71); ?>" class="a_normal"><?php echo $msg_productadd71; ?></a> <?php echo mc_displayHelpTip($msg_javascript386,'RIGHT'); ?></label>
  <textarea class="shortarea" rows="5" cols="30" id="short_desc" name="pShortDescription" tabindex="<?php echo (++$tabIndex); ?>"><?php echo (isset($EDIT->pShortDescription) ? mc_safeHTML(mc_cleanData($EDIT->pShortDescription)) : ''); ?></textarea>

  <label style="margin-top:10px"><?php echo $msg_productadd6; ?>: <a href="#" onclick="if(jQuery('#short_desc').val()!=''){jQuery('#desc').val(jQuery('#short_desc').val())}return false;" title="<?php echo mc_cleanDataEntVars($msg_productadd70); ?>" class="a_normal"><?php echo $msg_productadd70; ?></a> <?php echo mc_displayHelpTip($msg_javascript71,'RIGHT'); ?></label>
  <?php
  if ($SETTINGS->enableBBCode == 'yes') {
    define('BB_BOX', 'desc');
    include(PATH . 'templates/bbcode-buttons.php');
  }
  ?>
  <textarea rows="5" cols="30" class="tarea" id="desc" name="pDescription" tabindex="<?php echo (++$tabIndex); ?>"><?php echo (isset($EDIT->pDescription) ? mc_safeHTML(mc_cleanData($EDIT->pDescription)) : ''); ?></textarea>
  <br class="clear">
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd63; ?>:</p>
</div>

<div class="formFieldWrapper">
  <label><?php echo $msg_productadd75; ?>: <a href="#" onclick="if(jQuery('#pName').val()!=''){jQuery('#pTitle').val(jQuery('#pName').val())}return false;" title="<?php echo mc_cleanDataEntVars($msg_productadd76); ?>" class="a_normal"><?php echo $msg_productadd76; ?></a> <?php echo mc_displayHelpTip($msg_javascript449,'RIGHT'); ?></label>
  <input class="box" type="text" maxlength="250" id="pTitle" name="pTitle" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pTitle) ? mc_safeHTML($EDIT->pTitle) : ''); ?>">

  <label style="margin-top:10px"><?php echo $msg_productadd18; ?>: <?php echo mc_displayHelpTip($msg_javascript78,'RIGHT'); ?></label>
  <input class="box" type="text" id="keys" name="pMetaKeys" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pMetaKeys) ? mc_safeHTML($EDIT->pMetaKeys) : ''); ?>">

  <label style="margin-top:10px"><?php echo $msg_productadd19; ?>: <?php echo mc_displayHelpTip($msg_javascript79,'RIGHT'); ?></label>
  <input class="box" type="text" id="mdesc" name="pMetaDesc" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pMetaDesc) ? mc_safeHTML($EDIT->pMetaDesc) : ''); ?>">

  <label style="margin-top:10px"><?php echo $msg_newpages31; ?>: <?php echo mc_displayHelpTip($msg_javascript472,'RIGHT'); ?></label>
  <input type="text" name="rwslug" onkeyup="mc_slugCleaner('rwslug')" tabindex="<?php echo (++$tabIndex); ?>" maxlength="250" value="<?php echo (isset($EDIT->rwslug) ? mc_safeHTML($EDIT->rwslug) : ''); ?>" class="box">

  <label style="margin-top:10px"><?php echo $msg_productadd11; ?>: <a href="#" onclick="if(jQuery('#keys').val()!=''){jQuery('#tags').val(jQuery('#keys').val())}return false;" title="<?php echo mc_cleanDataEntVars($msg_productadd34); ?>" class="a_normal"><?php echo $msg_productadd34; ?></a> | <a href="#" onclick="if(jQuery('#mdesc').val()!=''){mc_createTagsFromField('mdesc')}return false;" title="<?php echo mc_cleanDataEntVars($msg_productadd37); ?>" class="a_normal"><?php echo $msg_productadd37; ?></a> | <a href="#" onclick="if(jQuery('#desc').val()!=''){mc_createTagsFromField('desc')}return false" title="<?php echo mc_cleanDataEntVars($msg_productadd36); ?>" class="a_normal"><?php echo $msg_productadd36; ?></a> <?php echo mc_displayHelpTip($msg_javascript76,'LEFT'); ?></label>
  <textarea rows="5" cols="30" id="tags" name="pTags" style="height:80px" tabindex="<?php echo (++$tabIndex); ?>"><?php echo (isset($EDIT->pTags) ? mc_safeHTML($EDIT->pTags) : ''); ?></textarea>
  <br class="clear">
</div>
<?php
}
if (!defined('BATCH_EDIT_MODE')) {
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd55; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productadd8; ?>: <?php echo mc_displayHelpTip($msg_javascript72,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input onclick="if(this.checked){jQuery('#loadPers').hide()}" type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="pDownload" value="yes"<?php echo (isset($EDIT->pDownload) && $EDIT->pDownload=='yes' ? ' checked="checked"' : (!isset($EDIT->pDownload) && IS_PRODUCT_DOWNLOAD=='yes' ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input onclick="if(this.checked){jQuery('#loadPers').show()}" tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="pDownload" value="no"<?php echo (isset($EDIT->pDownload) && $EDIT->pDownload=='no' ? ' checked="checked"' : (!isset($EDIT->pDownload) && IS_PRODUCT_DOWNLOAD=='no' ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_productadd9; ?>: <?php echo mc_displayHelpTip($msg_javascript73); ?></label>
    <div class="form-group input-group">
      <span class="input-group-addon"><a href="#" onclick="mc_loadLocalFiles();return false" title="<?php echo mc_cleanDataEntVars($msg_productadd66); ?>"><i class="fa fa-file fa-fw"></i></a></span>
      <input type="text" id="pDownloadPath" name="pDownloadPath" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pDownloadPath) ? mc_cleanData($EDIT->pDownloadPath) : ''); ?>" class="box addon-no-radius">
    </div>

    <div class="localFile" id="localFile" style="display:none">
     <div class="error-block pad-error-block" id="fileError" style="display:none"><?php echo $msg_productadd68; ?> <a href="#" onclick="jQuery('#fileError').hide();jQuery('#localFile').hide();return false" title="<?php echo mc_cleanDataEntVars($msg_script8); ?>"><i class="fa fa-times fa-fw mc-red"></i></a></div>
     <div id="fileList">
     <label style="margin-top:10px"><?php echo $msg_productadd67; ?>: <a href="#" onclick="jQuery('#localFile').hide();return false" title="<?php echo mc_cleanDataEntVars($msg_script8); ?>"><i class="fa fa-times fa-fw mc-red"></i></a></label>
      <select name="pathlocator" id="pathlocator" onchange="if(this.value!='0'){jQuery('#pDownloadPath').val(this.value);jQuery('#localFile').hide()}">
       <option>1</option>
      </select>
     </div>
    </div>

    <label style="margin-top:10px"><?php echo $msg_productadd10; ?>: <?php echo mc_displayHelpTip($msg_javascript74,'LEFT'); ?></label>
    <input type="text" name="pDownloadLimit" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pDownloadLimit) ? mc_cleanData($EDIT->pDownloadLimit) : '0'); ?>" class="box">
  </div>
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd56; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productadd29; ?>: <?php echo mc_displayHelpTip($msg_javascript110,'RIGHT'); ?></label>
    <div class="form-group input-group">
      <span class="input-group-addon"><i class="fa fa-youtube fa-fw"></i></span>
      <input type="text" name="pVideo" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pVideo) ? mc_cleanData($EDIT->pVideo) : ''); ?>" class="box addon-no-radius">
    </div>

    <label style="margin-top:10px"><?php echo $msg_admin_product3_0[3]; ?>: <?php echo mc_displayHelpTip($msg_javascript110,'RIGHT'); ?></label>
    <div class="form-group input-group">
      <span class="input-group-addon"><i class="fa fa-vimeo fa-fw"></i></span>
      <input type="text" name="pVideo2" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pVideo2) ? mc_cleanData($EDIT->pVideo2) : ''); ?>" class="box addon-no-radius">
    </div>

    <label style="margin-top:10px"><?php echo $msg_admin_product3_0[4]; ?>: <?php echo mc_displayHelpTip($msg_javascript110,'RIGHT'); ?></label>
    <div class="form-group input-group">
      <span class="input-group-addon"><i class="fa fa-video-camera fa-fw"></i></span>
      <input type="text" name="pVideo3" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pVideo3) ? mc_cleanData($EDIT->pVideo3) : ''); ?>" class="box addon-no-radius">
    </div>

    <label style="margin-top:10px"><?php echo $msg_productadd53; ?>: <?php echo mc_displayHelpTip($msg_javascript344,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enDisqus" value="yes"<?php echo (isset($EDIT->enDisqus) && $EDIT->enDisqus=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enDisqus" value="no"<?php echo (isset($EDIT->enDisqus) && $EDIT->enDisqus=='no' ? ' checked="checked"' : (!isset($EDIT->enDisqus) ? ' checked="checked"' : '')); ?>>
  </div>
  <br class="clear">
</div>
<?php
} else {
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_productmanage54; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_2','pDownload').$msg_productadd8; ?>: <?php echo mc_displayHelpTip($msg_javascript72,'RIGHT'); ?></label>
    <div id="batch_field_2"<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pDownload',$_SESSION['batchFieldPrefs']) ? ' style="display:none"' : ''); ?>><?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="pDownload" value="yes"<?php echo (isset($EDIT->pDownload) && $EDIT->pDownload=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input onclick="if(this.checked){jQuery('#loadPers').show()}" tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="pDownload" value="no"<?php echo (isset($EDIT->pDownload) && $EDIT->pDownload=='no' ? ' checked="checked"' : (!isset($EDIT->pDownload) ? ' checked="checked"' : '')); ?>></div>

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('batch_field_3','pDownloadLimit').$msg_productadd10; ?>: <?php echo mc_displayHelpTip($msg_javascript74); ?></label>
    <input id="batch_field_3" type="text" name="pDownloadLimit" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pDownloadLimit) ? mc_cleanData($EDIT->pDownloadLimit) : '0'); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pDownloadLimit',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('batch_field_4','enDisqus').$msg_productadd53; ?>: <?php echo mc_displayHelpTip($msg_javascript344,'LEFT'); ?></label>
    <div id="batch_field_4"<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('enDisqus',$_SESSION['batchFieldPrefs']) ? ' style="display:none"' : ''); ?>><?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enDisqus" value="yes"<?php echo (isset($EDIT->enDisqus) && $EDIT->enDisqus=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enDisqus" value="no"<?php echo (isset($EDIT->enDisqus) && $EDIT->enDisqus=='no' ? ' checked="checked"' : (!isset($EDIT->enDisqus) ? ' checked="checked"' : '')); ?>></div>
  </div>
  <br class="clear">
</div>

<?php
}
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd81; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_5','countryRestrictions').$msg_productadd82; ?>: <?php echo mc_displayHelpTip($msg_javascript459,'RIGHT'); ?></label>
    <div class="categoryBoxes" id="batch_field_5"<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('countryRestrictions',$_SESSION['batchFieldPrefs']) ? ' style="display:none"' : ''); ?>>
    <?php
    $ctRest   = (isset($EDIT->countryRestrictions) && $EDIT->countryRestrictions ? unserialize($EDIT->countryRestrictions) : array());
    $q_ctry   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
                WHERE `enCountry`  = 'yes'
                ORDER BY `cName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($COUNTRY = mysqli_fetch_object($q_ctry)) {
    ?>
    <input type="checkbox" name="countryRestrictions[]" value="<?php echo $COUNTRY->id; ?>"<?php echo (in_array($COUNTRY->id,$ctRest) ? ' checked="checked"' : ''); ?>> <?php echo mc_cleanData($COUNTRY->cName); ?><br>
    <?php
    }
    ?>
    </div>
  </div>
</div>
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_6','checkoutTextDisplay').$msg_productadd83; ?>: <?php echo mc_displayHelpTip($msg_javascript460,'LEFT'); ?></label>
    <input id="batch_field_6" type="text" name="checkoutTextDisplay" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->checkoutTextDisplay) ? mc_safeHTML($EDIT->checkoutTextDisplay) : ''); ?>" maxlength="100" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('checkoutTextDisplay',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('batch_field_7','minPurchaseQty').$msg_productadd79; ?>: <?php echo mc_displayHelpTip($msg_javascript454); ?></label>
    <input id="batch_field_7" type="text" name="minPurchaseQty" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->minPurchaseQty) ? $EDIT->minPurchaseQty : '0'); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('minPurchaseQty',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('batch_field_19','maxPurchaseQty').$msg_productadd88; ?>: <?php echo mc_displayHelpTip($msg_javascript497,'LEFT'); ?></label>
    <input id="batch_field_19" type="text" name="maxPurchaseQty" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->maxPurchaseQty) ? $EDIT->maxPurchaseQty : '0'); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('maxPurchaseQty',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">
  </div>
  <br class="clear">
</div>
<?php
// Any drop shippers?
if (mc_rowCount('dropshippers') > 0) {
?>
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('dropship1','dropshipping').$msg_admin_product3_0[1]; ?>: <?php echo mc_displayHelpTip($msg_javascript460,'LEFT'); ?></label>
    <select name="dropshipping" id="dropship1">
     <option value="0">- - - - - -</option>
     <?php
     $q_ds = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`name` FROM `" . DB_PREFIX . "dropshippers`
             WHERE `enable`  = 'yes'
             ORDER BY `name`
             ") or die(mc_MySQLError(__LINE__,__FILE__));
     while ($DS = mysqli_fetch_object($q_ds)) {
     ?>
     <option value="<?php echo $DS->id; ?>"<?php echo (isset($EDIT->dropshipping) && $EDIT->dropshipping==$DS->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($DS->name); ?></option>
     <?php
     }
     ?>
    </select>
  </div>
  <br class="clear">
</div>
<?php
}
?>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd57; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_8','pStock').$msg_productadd43; ?>: <?php echo mc_displayHelpTip($msg_javascript83,'RIGHT'); ?></label>
    <input id="batch_field_8" type="text" name="pStock" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pStock) ? $EDIT->pStock : '1'); ?>" maxlength="7" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pStock',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('batch_field_9','pStockNotify').$msg_productadd20; ?>: <?php echo mc_displayHelpTip($msg_javascript80); ?></label>
    <input id="batch_field_9" type="text" name="pStockNotify" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pStockNotify) ? mc_cleanData($EDIT->pStockNotify) : '0'); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pStockNotify',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('batch_field_18','pAvailableText').$msg_productadd85; ?>: <?php echo mc_displayHelpTip($msg_javascript473); ?></label>
    <input id="batch_field_18" type="text" name="pAvailableText" tabindex="<?php echo (++$tabIndex); ?>" maxlength="250" value="<?php echo (isset($EDIT->pAvailableText) ? mc_cleanData($EDIT->pAvailableText) : ''); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pAvailableText',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">
  </div>
  <br class="clear">
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd86; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_10','pVisits').$msg_productadd31; ?>: <?php echo mc_displayHelpTip($msg_javascript151); ?></label>
    <input id="batch_field_10" type="text" name="pVisits" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pVisits) ? mc_cleanData($EDIT->pVisits) : '0'); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pVisits',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('pCode','pCode').$msg_productadd45; ?>: <?php echo mc_displayHelpTip($msg_javascript68); ?></label>
    <div class="form-group input-group" id="pCode" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pCode',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">
      <span class="input-group-addon"><a href="#" onclick="codeChecker();return false"><i class="fa fa-question-circle fa-fw"></i></a></span>
      <input type="text" name="pCode" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pCode) ? mc_safeHTML($EDIT->pCode) : ''); ?>" class="box addon-no-radius">
    </div>

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('pCube','pCube').$msg_productadd87; ?>: <?php echo mc_displayHelpTip($msg_javascript481,'LEFT'); ?></label>
    <input type="text" name="pCube" id="pCube" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pCube) && $EDIT->pCube>0 ? mc_safeHTML($EDIT->pCube) : ''); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pCube',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('pGuardian','pGuardian').$msg_admin_product3_0[17]; ?>: <?php echo mc_displayHelpTip($msg_javascript481,'LEFT'); ?></label>
    <input type="text" name="pGuardian" id="pGuardian" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pGuardian) && $EDIT->pGuardian>0 ? mc_safeHTML($EDIT->pGuardian) : ''); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pGuardian',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">
  </div>
  <br class="clear">
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd58; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_13','pPrice').$msg_productadd44; ?>: <?php echo mc_displayHelpTip($msg_javascript114); ?></label>
    <input id="batch_field_13" type="text" name="pPrice" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pPrice) ? mc_formatPrice($EDIT->pPrice) : '0.00'); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pPrice',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('batch_field_21','pPurPrice').$msg_admin_product3_0[14]; ?>: <?php echo mc_displayHelpTip($msg_javascript114); ?></label>
    <input id="batch_field_21" type="text" name="pPurPrice" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pPurPrice) ? mc_formatPrice($EDIT->pPurPrice) : '0.00'); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pPurPrice',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <?php
    if (!isset($EDIT->id)) {
    ?>
    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('expiry','expiry').$msg_admin_product3_0[0]; ?>: <?php echo mc_displayHelpTip($msg_javascript217,'LEFT'); ?></label>
    <input type="text" name="expiry" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->expiry) && mc_checkValidDate($EDIT->expiry)!='0000-00-00' ? mc_convertMySQLDate($EDIT->expiry, $SETTINGS) : ''); ?>" class="box" id="expiry" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('expiry',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">
    <?php
    } else {
    ?>
    <div class="form-group">
      <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('expiry','expiry').$msg_admin_product3_0[0]; ?>: <?php echo mc_displayHelpTip($msg_javascript217,'LEFT'); ?></label>
      <div class="form-group input-group">
       <span class="input-group-addon"><a href="index.php?p=add-product&amp;prod_expiry=<?php echo $EDIT->id; ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_PEXPIRY_HEIGHT; ?>','<?php echo DIVWIN_PEXPIRY_WIDTH; ?>',this.title);return false;"><i class="fa fa-cog fa-fw"></i></a></span>
       <input type="text" name="expiry" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->expiry) && mc_checkValidDate($EDIT->expiry)!='0000-00-00' ? mc_convertMySQLDate($EDIT->expiry, $SETTINGS) : ''); ?>" class="box addon-no-radius" id="expiry" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('expiry',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">
      </div>
    </div>
    <?php
    }
    ?>

  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_12','pWeight').$msg_productadd42; ?>: <?php echo mc_displayHelpTip($msg_javascript82,'RIGHT'); ?></label>
    <input id="batch_field_12" type="text" name="pWeight" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pWeight) ? $EDIT->pWeight : '0'); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pWeight',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('batch_field_15','freeShipping').$msg_productadd54; ?>: <?php echo mc_displayHelpTip($msg_javascript352,'RIGHT'); ?></label>
    <div id="batch_field_15"<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('freeShipping',$_SESSION['batchFieldPrefs']) ? ' style="display:none"' : ''); ?>><?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="freeShipping" value="yes"<?php echo (isset($EDIT->freeShipping) && $EDIT->freeShipping=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="freeShipping" value="no"<?php echo (isset($EDIT->freeShipping) && $EDIT->freeShipping=='no' ? ' checked="checked"' : (!isset($EDIT->freeShipping) ? ' checked="checked"' : '')); ?>></div>

  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_14','pOffer').$msg_productadd39; ?>: <?php echo mc_displayHelpTip($msg_javascript216); ?></label>
    <input id="batch_field_14" type="text" name="pOffer" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pOffer) && $EDIT->pOffer>0 ? mc_formatPrice($EDIT->pOffer) : '0.00'); ?>" class="box" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pOffer',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('pOfferExpiry','pOfferExpiry').$msg_productadd40; ?>: <?php echo mc_displayHelpTip($msg_javascript217,'LEFT'); ?></label>
    <input type="text" name="pOfferExpiry" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pOfferExpiry) && mc_checkValidDate($EDIT->pOfferExpiry)!='0000-00-00' ? mc_convertMySQLDate($EDIT->pOfferExpiry, $SETTINGS) : ''); ?>" class="box" id="pOfferExpiry" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pOfferExpiry',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">

  </div>
  <br class="clear">
</div>

<?php
if (isset($_GET['copyp']) || isset($EDIT->id) || defined('BATCH_EDIT_MODE')) {
?>
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_20','pDateAdded').$msg_admin_product3_0[5]; ?>: <?php echo mc_displayHelpTip($msg_javascript352,'RIGHT'); ?></label>
    <input type="text" name="pDateAdded" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->pDateAdded) && mc_checkValidDate($EDIT->pDateAdded)!='0000-00-00' ? mc_convertMySQLDate($EDIT->pDateAdded, $SETTINGS) : ''); ?>" class="box" id="batch_field_20" style="<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pDateAdded',$_SESSION['batchFieldPrefs']) ? 'display:none' : ''); ?>">
  </div>
  <br class="clear">
</div>
<?php
}
?>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo mc_hideShowBatchOperation('batch_field_16','pPurchase').$msg_productadd69; ?>: <?php echo mc_displayHelpTip($msg_javascript401); ?></label>
    <div id="batch_field_16"<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pPurchase',$_SESSION['batchFieldPrefs']) ? ' style="display:none"' : ''); ?>><?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="pPurchase" value="yes"<?php echo (isset($EDIT->pPurchase) && $EDIT->pPurchase=='yes' ? ' checked="checked"' : (!isset($EDIT->pPurchase) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="pPurchase" value="no"<?php echo (isset($EDIT->pPurchase) && $EDIT->pPurchase=='no' ? ' checked="checked"' : ''); ?>></div>

    <label style="margin-top:10px"><?php echo mc_hideShowBatchOperation('batch_field_17','pEnable').$msg_productadd12; ?>: <?php echo mc_displayHelpTip($msg_javascript77); ?></label>
    <div id="batch_field_17"<?php echo (isset($_SESSION['batchFieldPrefs']) && in_array('pEnable',$_SESSION['batchFieldPrefs']) ? ' style="display:none"' : ''); ?>><?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="pEnable" value="yes"<?php echo (isset($EDIT->pEnable) && $EDIT->pEnable=='yes' ? ' checked="checked"' : (!isset($EDIT->pEnable) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="pEnable" value="no"<?php echo (isset($EDIT->pEnable) && $EDIT->pEnable=='no' ? ' checked="checked"' : ''); ?>></div>
  </div>
  <br class="clear">
</div>

<?php
if (!isset($_GET['copyp']) && !isset($EDIT->id) && !defined('BATCH_EDIT_MODE')) {
?>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd60; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
   <label><?php echo $msg_productadd59; ?>: <?php echo mc_displayHelpTip($msg_javascript376,'RIGHT'); ?></label>
    <input type="file" name="image[]">

    <label style="margin-top:10px"><?php echo $msg_productadd62; ?>: <?php echo mc_displayHelpTip($msg_javascript377); ?></label>
    <select name="folder" id="folderList">
      <option value="products"><?php echo PRODUCTS_FOLDER; ?>/ <?php echo $msg_productpictures12; ?></option>
      <?php
      if (is_dir(REL_HTTP_PATH.PRODUCTS_FOLDER)) {
        $dir = opendir(REL_HTTP_PATH.PRODUCTS_FOLDER);
        while (false!==($read=readdir($dir))) {
          if (!in_array($read,array('.','..')) && is_dir(REL_HTTP_PATH.PRODUCTS_FOLDER.'/'.$read)) {
          ?>
          <option value="<?php echo $read; ?>"<?php echo (isset($EDIT->catFolder) && $EDIT->catFolder==$read ? ' selected="selected"' : ''); ?>><?php echo PRODUCTS_FOLDER; ?>/<?php echo $read; ?></option>
          <?php
          }
        }
        closedir($dir);
      }
      ?>
    </select><br>
    <button type="button" onclick="mc_createPictureFolder('<?php echo mc_filterJS($msg_javascript152); ?>','<?php echo PRODUCTS_FOLDER; ?>')" class="btn btn-default" name="<?php echo mc_cleanDataEntVars($msg_salesupdate13); ?>"><i class="fa fa-folder fa-fw"></i></button>
  </div>
  <br class="clear">
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd77; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft" id="addimgboxes">
    <input type="file" name="addimg[]" value="">

    <div style="margin-top:10px">
      <button type="button" class="btn btn-primary btn-xs" onclick="mc_AttBox('add','addimg')"><i class="fa fa-plus fa-fw"></i></button>
      <button type="button" class="btn btn-success btn-xs" onclick="mc_AttBox('minus','addimg')"><i class="fa fa-minus fa-fw"></i></button>
    </div>
  </div>
</div>
<?php
} else {
if (isset($_GET['copyp'])) {
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd60; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productadd59; ?>: <?php echo mc_displayHelpTip($msg_javascript376,'RIGHT'); ?></label>
    <input type="file" name="image[]" value="">

    <label style="margin-top:10px"><?php echo $msg_productadd62; ?>: <?php echo mc_displayHelpTip($msg_javascript377); ?></label>
    <select name="folder" id="folderList">
      <option value="<?php echo basename(PRODUCTS_FOLDER); ?>"><?php echo PRODUCTS_FOLDER; ?>/ <?php echo $msg_productpictures12; ?></option>
      <?php
      if (is_dir(REL_HTTP_PATH . PRODUCTS_FOLDER)) {
        $dir = opendir(REL_HTTP_PATH . PRODUCTS_FOLDER);
        while (false!==($read=readdir($dir))) {
          if (!in_array($read,array('.','..')) && is_dir(REL_HTTP_PATH . PRODUCTS_FOLDER . '/' . $read)) {
          ?>
          <option value="<?php echo $read; ?>"<?php echo (isset($EDIT->catFolder) && $EDIT->catFolder==$read ? ' selected="selected"' : ''); ?>><?php echo PRODUCTS_FOLDER; ?>/<?php echo $read; ?></option>
          <?php
          }
        }
        closedir($dir);
      }
      ?>
    </select><br>
    <button type="button" onclick="mc_createPictureFolder('<?php echo mc_filterJS($msg_javascript152); ?>','<?php echo PRODUCTS_FOLDER; ?>')" class="btn btn-default" name="<?php echo mc_cleanDataEntVars($msg_salesupdate13); ?>"><i class="fa fa-folder fa-fw"></i></button>
  </div>
  <br class="clear">
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_productadd77; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft" id="addimgboxes">
    <input type="file" name="addimg[]" value="">

    <div style="margin-top:10px">
      <button type="button" class="btn btn-primary btn-xs" onclick="mc_AttBox('add','addimg')"><i class="fa fa-plus fa-fw"></i></button>
      <button type="button" class="btn btn-success btn-xs" onclick="mc_AttBox('minus','addimg')"><i class="fa fa-minus fa-fw"></i></button>
    </div>
  </div>
</div>
<?php
}
}

if (isset($_GET['copyp'])) {
?>
<div class="formFieldWrapper" id="copyOptionsArea">
  <label><?php echo $msg_productadd26; ?>: <?php echo mc_displayHelpTip($msg_javascript105); ?></label>
  <b><?php echo $msg_productadd78; ?></b>: <input onclick="mc_toggleCheckBoxesID(this.checked,'copyOptionsArea')" type="checkbox" name="checker" value="yes">&nbsp;&nbsp;&nbsp;
  <?php echo $msg_productadd27; ?>: <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" id="copyPictures" name="copyPictures" value="yes">&nbsp;&nbsp;&nbsp;
  <?php echo $msg_productadd28; ?>: <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" id="copyAttributes" name="copyAttributes" value="yes">&nbsp;&nbsp;&nbsp;
  <?php echo $msg_productadd47; ?>: <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" id="copyRelated" name="copyRelated" value="yes">&nbsp;&nbsp;&nbsp;
  <?php echo $msg_productadd48; ?>: <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" id="copyMP3" name="copyMP3" value="yes">&nbsp;&nbsp;&nbsp;
  <span id="loadPers"<?php echo (isset($EDIT->pDownload) && $EDIT->pDownload=='yes' ? ' style="display:none"' : ''); ?>><?php echo $msg_productadd51; ?>: <input tabindex="<?php echo (++$tabIndex); ?>" id="copyPersonalisation" type="checkbox" name="copyPersonalisation" value="yes"></span>
</div>
<?php
}
?>

<p style="text-align:center;padding-top:20px">
<?php
if (isset($EDIT->id) && !isset($_GET['copyp'])) {
?>
<input type="hidden" name="update" value="yes">
<input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_productadd15); ?>" title="<?php echo mc_cleanDataEntVars($msg_productadd15); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input class="btn btn-success" type="button" onclick="window.location='?p=manage-products'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
<?php
} else {
if (defined('BATCH_EDIT_MODE')) {
?>
<input type="hidden" name="productIDs" value="<?php echo (isset($_POST['productsUpdated']) ? $_POST['productIDs'] : implode(',',$_POST['productIDs'])); ?>">
<input type="hidden" name="productsUpdated" value="<?php echo $batchCount; ?>">
<input class="btn btn-primary" type="submit" value="<?php echo str_replace('{count}',$batchCount,mc_cleanDataEntVars($msg_productmanage55)); ?>" title="<?php echo str_replace('{count}',$batchCount,mc_cleanDataEntVars($msg_productmanage55)); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input class="btn btn-success" type="button" onclick="window.location='?p=manage-products'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
<?php
} else {
?>
<input type="hidden" name="process" value="yes">
<input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_productadd2); ?>" title="<?php echo mc_cleanDataEntVars($msg_productadd2); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input class="btn btn-success" type="button" onclick="window.location='?p=manage-products'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
<?php
}
}
?>
</p>
</form>


</div>
