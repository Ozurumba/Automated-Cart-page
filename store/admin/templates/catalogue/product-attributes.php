<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT     = mc_getTableData('attributes','attrGroup',mc_digitSan($_GET['edit']),' ORDER BY `id`');
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
  $GROUP    = mc_getTableData('attr_groups','id',mc_digitSan($_GET['edit']));
  $attRows  = mc_rowCount('attributes WHERE `productID` = \''.mc_digitSan($_GET['product']).'\' AND `attrGroup` = \''.mc_digitSan($_GET['edit']).'\'');
}
$MCPRODUCT  = mc_getTableData('products','id',mc_digitSan($_GET['product']));
$thisProductID = mc_digitSan($_GET['product']);
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',$ret[0],$msg_prodattributes13));
}
if (isset($OK2) || isset($_GET['ok2'])) {
  echo mc_actionCompleted($msg_prodattributes17);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_prodattributes14);
}
if (isset($OK4)) {
  echo mc_actionCompleted($msg_prodattributes16);
}
if (isset($OK5)) {
  echo mc_actionCompleted($msg_prodattributes20);
}
if (isset($OK6) && $cnt>0) {
  echo mc_actionCompleted($msg_prodattributes22);
}

$P = mc_getTableData('products','id',mc_digitSan($_GET['product']));
?>

<div class="alert alert-info">
  <?php
  $qLinksArr  = array('product-attributes');
  $qLinksIcon = 'pencil-square-o';
  include(PATH . 'templates/catalogue/product-quick-links.php');
  ?>
</div>

<form method="post" action="?p=product-attributes&amp;product=<?php echo mc_digitSan($_GET['product']).(isset($_GET['edit']) ? '&amp;edit='.mc_digitSan($_GET['edit']) : ''); ?>">

<div class="fieldHeadWrapper">
  <p><?php echo $msg_prodattributes3; ?></p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_admin3_0[17]; ?>: <?php echo mc_displayHelpTip($msg_javascript428,'RIGHT'); ?></label>
    <select name="exgroup" onchange="if(this.value!=0){mc_showHideGroup('newg')}else{mc_showHideGroup('exg')}">
    <option value="0">- - - - - - -</option>
    <?php
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attr_groups`
         WHERE `productID` = '".mc_digitSan($_GET['product'])."'
         ORDER BY `orderBy`,`groupName`
         ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($AG = mysqli_fetch_object($q)) {
    ?>
    <option value="<?php echo $AG->id; ?>"<?php echo (isset($EDIT->attrGroup) && $EDIT->attrGroup==$AG->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($AG->groupName); ?></option>
    <?php
    }
    ?>
    </select>

    <div>
      <div class="newg"<?php echo (isset($EDIT->attrGroup) && $EDIT->attrGroup > 0 ? ' style="display:none"' : ''); ?>>
        <label style="margin-top:10px"><?php echo $msg_prodattributes8; ?>: <?php echo mc_displayHelpTip($msg_javascript350,'RIGHT'); ?></label>
        <input type="text" name="group" class="box" tabindex="<?php echo (++$tabIndex); ?>" maxlength="100">
      </div>

      <label style="margin-top:10px"><?php echo $msg_prodattributes29; ?>: <?php echo mc_displayHelpTip($msg_javascript428,'RIGHT'); ?></label>
      <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="allowMultiple" value="yes"<?php echo (isset($GROUP->allowMultiple) && $GROUP->allowMultiple=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="allowMultiple" value="no"<?php echo (isset($GROUP->allowMultiple) && $GROUP->allowMultiple=='no' ? ' checked="checked"' : (!isset($GROUP->allowMultiple) ? ' checked="checked"' : '')); ?>>

      <label style="margin-top:10px"><?php echo $msg_prodattributes31; ?>: <?php echo mc_displayHelpTip($msg_javascript434,'RIGHT'); ?></label>
      <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="isRequired" value="yes"<?php echo (isset($GROUP->isRequired) && $GROUP->isRequired=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="isRequired" value="no"<?php echo (isset($GROUP->isRequired) && $GROUP->isRequired=='no' ? ' checked="checked"' : (!isset($GROUP->isRequired) ? ' checked="checked"' : '')); ?>>
    </div>
  </div>
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_prodattributes4; ?></p>
</div>

<div class="formFieldWrapper">

  <div class="table-responsive attributearea">
  <table class="table table-striped table-hover">
  <thead>
    <tr>
      <th><?php echo $msg_prodattributes9; ?></th>
      <th><?php echo $msg_prodattributes10; ?></th>
      <th><?php echo $msg_prodattributes15; ?></th>
      <th><?php echo $msg_prodattributes5; ?></th>
      <th><?php echo $msg_prodattributes11; ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
    if (isset($_GET['edit'])) {
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attributes`
         WHERE `attrGroup` = '".mc_digitSan($_GET['edit'])."'
         ORDER BY `orderBy`
         ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($ATT = mysqli_fetch_object($q)) {
    ?>
    <tr>
      <td><input type="hidden" name="attid[]" value="<?php echo $ATT->id; ?>"><input type="text" name="name[]" maxlength="100" class="box" value="<?php echo mc_safeHTML($ATT->attrName); ?>"></td>
      <td><input type="text" name="cost[]" class="box" value="<?php echo mc_safeHTML($ATT->attrCost); ?>"></td>
      <td><input type="text" name="weight[]" class="box" value="<?php echo mc_safeHTML($ATT->attrWeight); ?>"></td>
      <td><input type="text" name="stock[]" class="box" value="<?php echo mc_safeHTML($ATT->attrStock); ?>"></td>
      <td>
      <select name="order[]">
      <?php
      for ($i=1; $i<$attRows+1; $i++) {
      ?>
      <option value="<?php echo $i; ?>"<?php echo ($ATT->orderBy==$i ? ' selected="selected"' : ''); ?>><?php echo $i; ?></option>
      <?php
      }
      ?>
      </select>
      </td>
    </tr>
    <?php
    }
    } else {
    ?>
    <tr>
      <td><input type="text" name="name[]" maxlength="100" class="box" value="<?php echo (isset($EDIT->id) ? mc_safeHTML($EDIT->attrName) : ''); ?>"></td>
      <td><input type="text" name="cost[]" class="box" value="<?php echo (isset($EDIT->id) ? mc_safeHTML($EDIT->attrCost) : '0.00'); ?>"></td>
      <td><input type="text" name="weight[]" class="box" value="<?php echo (isset($EDIT->id) ? mc_safeHTML($EDIT->attrWeight) : '0'); ?>"></td>
      <td><input type="text" name="stock[]" class="box" value="<?php echo (isset($EDIT->id) ? mc_safeHTML($EDIT->attrStock) : '1'); ?>"></td>
      <td>
      <select name="order[]">
       <option value="1">1</option>
      </select>
      </td>
    </tr>
    <?php
    }
    ?>
  </tbody>
  </table>

  <div style="margin:10px 0 10px 0;text-align:center;">
    <button type="button" class="btn btn-primary btn-xs" onclick="mc_manageAttributeBoxes('add')"><i class="fa fa-plus fa-fw"></i></button>
    <button type="button" class="btn btn-success btn-xs" onclick="mc_manageAttributeBoxes('minus')"><i class="fa fa-minus fa-fw"></i></button>
  </div>

  </div>

</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="<?php echo (isset($EDIT->id) ? $EDIT->id : 'yes'); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_prodattributes23 : $msg_prodattributes6)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_prodattributes23 : $msg_prodattributes6)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=product-attributes&amp;product=' . (int) $_GET['product'] . '\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form>

<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery("#sortable").sortable({
    update : function (data) {
      jQuery("#loader").load("index.php?p=product-attributes&order=<?php echo mc_digitSan($_GET['product']); ?>&"+jQuery('#sortable').sortable('serialize'));
      jQuery('#loader_msg').show('slow');
      jQuery('#loader_msg').html('<i class="fa fa-check fa-fw"></i>&nbsp;&nbsp;').fadeOut(6000);
    }
  });
});
//]]>
</script>

<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><span class="float" id="loader"></span><span class="float" id="loader_msg" style="display:none" onclick="jQuery(this).hide()"></span><?php echo $msg_prodattributes4; ?>:</p>
</div>

<div id="sortable">
<?php
$q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attr_groups`
              WHERE `productID` = '".mc_digitSan($_GET['product'])."'
              ORDER BY `orderBy`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_products)>0) {
  while ($AG = mysqli_fetch_object($q_products)) {
  ?>
  <div class="panel panel-default" id="attr-<?php echo $AG->id; ?>" style="cursor:move">
    <div class="panel-heading" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[15]); ?>">
      <i class="fa fa-folder fa-fw"></i> <b><?php echo mc_safeHTML($AG->groupName); ?></b>
    </div>
    <div class="panel-body">
      <?php
      $stopAttDeletion = 'no';
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
           (SELECT count(*) FROM `" . DB_PREFIX . "purch_atts`
            WHERE `" . DB_PREFIX . "purch_atts`.`saleID` > 0
            AND `" . DB_PREFIX . "purch_atts`.`attributeID` = `" . DB_PREFIX . "attributes`.`id`
           ) AS `attrSaleCnt`
           FROM `" . DB_PREFIX . "attributes`
           WHERE `attrGroup` = '{$AG->id}'
           AND `productID` = '".mc_digitSan($_GET['product'])."'
           ORDER BY `orderBy`
           ") or die(mc_MySQLError(__LINE__,__FILE__));
      if (mysqli_num_rows($q)>0) {
      while ($ATTR = mysqli_fetch_object($q)) {
      if ($ATTR->attrSaleCnt > 0) {
        $stopAttDeletion = 'yes';
      }
      ?>
      <div class="attrname" id="atnm_<?php echo $ATTR->id; ?>"><?php echo mc_safeHTML($ATTR->attrName).($ATTR->attrCost ? ' (+'.mc_currencyFormat($ATTR->attrCost).')' : ''); ?></div>
      <div class="attrinfo" id="atif_<?php echo $ATTR->id; ?>"><?php echo str_replace(array('{weight}','{stock}'),array($ATTR->attrWeight,$ATTR->attrStock),$msg_prodattributes21); ?></div>
      <?php
      }
      } else {
      ?>
      <span class="none"><?php echo $msg_prodattributes18; ?></span>
      <?php
      }
      ?>
    </div>
    <div class="panel-footer">
     <a href="?p=product-attributes&amp;edit=<?php echo $AG->id; ?>&amp;product=<?php echo mc_digitSan($_GET['product']); ?>"><i class="fa fa-pencil fa-fw"></i></a>
     <?php
     if ($uDel == 'yes') {
       switch($stopAttDeletion) {
         case 'yes':
           echo ' <a href="#" onclick="alert(\''.mc_filterJS($msg_product_attributes[0]).'\');return false"><i class="fa fa-times fa-fw mc-red"></i></a>';
           break;
         case 'no':
           echo ' <a href="?p=product-attributes&amp;del='.$AG->id.'&amp;product='.mc_digitSan($_GET['product']).'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>';
           break;
       }
     }
    ?>
    </div>
  </div>
  <?php
  }
} else {
  ?>
  <span class="noData"><?php echo $msg_prodattributes12; ?></span>
  <?php
}
?>
</div>

</div>
