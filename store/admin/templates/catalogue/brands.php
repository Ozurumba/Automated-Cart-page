<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT  = mc_getTableData('brands','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_brand5);
}
if (isset($OKB)) {
  echo mc_actionCompleted(str_replace('{count}',$count,$msg_brand14));
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_brand9);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_brand10);
}
?>

<form method="post" id="form" action="?p=brands<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>"<?php echo (!isset($_GET['edit']) ? ' enctype="multipart/form-data"' : ''); ?>>
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->enBrand) ? $msg_brand6 : $msg_brand); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_brand2; ?>: <?php echo mc_displayHelpTip($msg_javascript159,'RIGHT'); ?></label>
    <input type="text" name="name" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->name) ? mc_cleanData($EDIT->name) : ''); ?>" maxlength="250" class="box">
    <?php
    if (!isset($_GET['edit'])) {
    ?>
    <label style="margin-top:10px"><?php echo $msg_brand13; ?>: <?php echo mc_displayHelpTip($msg_javascript316,'RIGHT'); ?></label>
    <input type="file" name="file" tabindex="<?php echo (++$tabIndex); ?>">
    <?php
    }
    ?>
    <label style="margin-top:10px"><?php echo $msg_brand8; ?>: <?php echo mc_displayHelpTip($msg_javascript160,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enBrand" value="yes"<?php echo (isset($EDIT->enBrand) && $EDIT->enBrand=='yes' ? ' checked="checked"' : (!isset($EDIT->enBrand) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enBrand" value="no"<?php echo (isset($EDIT->enBrand) && $EDIT->enBrand=='no' ? ' checked="checked"' : ''); ?>>

  </div>
</div>

<div class="formFieldWrapper">
	<div class="formLeft">
    <?php
    if (!isset($EDIT->id)) {
    ?>
    <input type="checkbox" name="bCat[]" tabindex="<?php echo (++$tabIndex); ?>" value="all" onclick="if(this.checked){jQuery('#catBrandList').hide()}else{jQuery('#catBrandList').show()}"> <b><?php echo $msg_productadd35; ?></b><br>
	  <?php
    } else {
    ?>
	  <input type="radio" name="bCat[]" tabindex="<?php echo (++$tabIndex); ?>" value="all"<?php echo (isset($EDIT->bCat) && $EDIT->bCat=='all' ? ' checked="checked"' : ''); ?>> <b><?php echo $msg_productadd35; ?></b><br>
	  <?php
	  }
    ?>
    <div id="catBrandList">
	  <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
            WHERE `catLevel` = '1'
            AND `childOf`    = '0'
            AND `enCat`      = 'yes'
            ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <p id="cat_<?php echo $CATS->id; ?>"><input onclick="if(this.checked){mc_selectChildren('cat_<?php echo $CATS->id; ?>','on')}else{mc_selectChildren('cat_<?php echo $CATS->id; ?>','off')}" tabindex="<?php echo (++$tabIndex); ?>" type="<?php echo (isset($EDIT->id) ? 'radio' : 'checkbox'); ?>" name="bCat[]" value="<?php echo $CATS->id; ?>"<?php echo (isset($EDIT->bCat) && $EDIT->bCat==$CATS->id ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?><br>
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
    &nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" onclick="if(this.checked){mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','on')}else{mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','off')}" type="<?php echo (isset($EDIT->id) ? 'radio' : 'checkbox'); ?>" name="bCat[]" value="<?php echo $CHILDREN->id; ?>"<?php echo (isset($EDIT->bCat) && $EDIT->bCat==$CHILDREN->id ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                 WHERE `catLevel` = '3'
                 AND `childOf`    = '{$CHILDREN->id}'
                 AND `enCat`      = 'yes'
                 ORDER BY `catname`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" type="<?php echo (isset($EDIT->id) ? 'radio' : 'checkbox'); ?>" name="bCat[]" value="<?php echo $INFANTS->id; ?>"<?php echo (isset($EDIT->bCat) && $EDIT->bCat==$INFANTS->id ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
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
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->enBrand) ? 'update' : 'process'); ?>" value="<?php echo (isset($EDIT->enBrand) ? $EDIT->id : 'yes'); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->enBrand) ? $msg_brand6 : $msg_brand)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->enBrand) ? $msg_brand6 : $msg_brand)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=brands\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form>

<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><span style="float:right"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span><?php echo $msg_brand4; ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=brands"><?php echo $msg_brand17; ?></option>
  <?php
  $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
            WHERE `catLevel` = '1'
            AND `childOf`    = '0'
            AND `enCat`      = 'yes'
            ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CATS = mysqli_fetch_object($q_cats)) {
  ?>
  <option value="?p=brands&amp;cat=<?php echo $CATS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
  <?php
  $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                WHERE `catLevel` = '2'
                AND `enCat`      = 'yes'
                AND `childOf`    = '{$CATS->id}'
                ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CHILDREN = mysqli_fetch_object($q_children)) {
  ?>
  <option value="?p=brands&amp;cat=<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
  <?php
  $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
               WHERE `catLevel` = '3'
               AND `childOf`    = '{$CHILDREN->id}'
               AND `enCat`      = 'yes'
               ORDER BY `catname`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($INFANTS = mysqli_fetch_object($q_infants)) {
  ?>
  <option value="?p=brands&amp;cat=<?php echo $INFANTS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;- <?php echo mc_safeHTML($INFANTS->catname); ?></option>
  <?php
  }
  }
  }
  ?>
  </select>
</div>

<form method="post" id="form2" action="?p=brands" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">
<?php
if (mc_rowCount('brands')>0) {
  $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
            WHERE `enCat` = 'yes'
            ".(isset($_GET['cat']) ? 'AND `id` = \''.mc_digitSan($_GET['cat']).'\' OR `childOf` = \''.mc_digitSan($_GET['cat']).'\'' : '')."
            ORDER BY `catname`
            ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CATS = mysqli_fetch_object($q_cats)) {
  $q_man = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "brands`
           WHERE `bCat` IN('{$CATS->id}','all')
           ORDER BY `name`
           ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($q_man)>0) {
  $parents = '';
  switch($CATS->catLevel) {
    case '2':
      $CAT      = mc_getTableData('categories','id',$CATS->childOf);
      $parents  = $CAT->catname.'/';
      break;
    case '3':
      $INF      = mc_getTableData('categories','id',$CATS->childOf);
      $CAT      = mc_getTableData('categories','id',$INF->childOf);
      $parents  = $CAT->catname.'/'.$INF->catname.'/';
      break;
  }
  $rCount = 0;
  ?>
  <div class="panel panel-default" id="brands_<?php echo $CATS->id; ?>">
    <div class="panel-heading">
      <?php echo ($uDel=='yes' ? ' <input onclick="if(this.checked){mc_selectChildren(\'brands_'.$CATS->id.'\',\'on\')}else{mc_selectChildren(\'brands_'.$CATS->id.'\',\'off\')}" type="checkbox" name="delcats[]" value="'.$CATS->id.'"> ' : ''); ?><?php echo ($parents ? $parents : '') . mc_safeHTML($CATS->catname); ?>
    </div>
    <div class="panel-body">
      <?php
      while ($BRAND = mysqli_fetch_object($q_man)) {
      ?>
      <?php echo ($uDel=='yes' ? ' <input type="checkbox" name="delete[]" value="'.$BRAND->id.'"> ' : ''); ?><a href="?p=brands&amp;edit=<?php echo $BRAND->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9.': '.$BRAND->name); ?>"><?php echo mc_safeHTML($BRAND->name); ?></a> <?php echo ($BRAND->bCat=='all' ? $msg_brand19 : ''); ?><br>
      <?php
      }
      ?>
    </div>
  </div>
  <?php
  }
  }
  if ($uDel=='yes') {
  ?>
  <p style="text-align:center;margin:20px 0 0 0">
  <input type="hidden" name="process_del" value="yes">
  <input class="btn btn-danger" type="submit" value="<?php echo mc_cleanDataEntVars($msg_brand18); ?>" title="<?php echo mc_cleanDataEntVars($msg_brand18); ?>">
  </p>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_brand3; ?></span>
<?php
}
?>
</form>


</div>
