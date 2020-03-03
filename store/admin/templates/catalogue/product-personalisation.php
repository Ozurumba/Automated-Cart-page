<?php if (!defined('PARENT') || !isset($_GET['product'])) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT  = mc_getTableData('personalisation','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_personalisation15);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_personalisation16);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_personalisation17);
}

$P = mc_getTableData('products','id',mc_digitSan($_GET['product']));
$thisProductID = mc_digitSan($_GET['product']);
?>

<div class="alert alert-info">
  <?php
  $qLinksArr  = array('product-personalisation');
  $qLinksIcon = 'quote-left';
  include(PATH . 'templates/catalogue/product-quick-links.php');
  ?>
</div>
<?php
if ($P->pDownload=='no') {
?>
<form method="post" id="form" action="?p=product-personalisation&amp;product=<?php echo mc_digitSan($_GET['product']).(isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_personalisation3 : $msg_personalisation2); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_personalisation5; ?>: <?php echo mc_displayHelpTip($msg_javascript298); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="persInstructions" value="<?php echo (isset($EDIT->persInstructions) ? mc_safeHTML($EDIT->persInstructions) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_personalisation11; ?>: <?php echo mc_displayHelpTip($msg_javascript300); ?></label>
    <textarea tabindex="<?php echo (++$tabIndex); ?>" name="persOptions" rows="5" cols="20" style="height:85px"><?php echo (isset($EDIT->persOptions) ? mc_safeHTML(str_replace('||',mc_defineNewline(),$EDIT->persOptions)) : ''); ?></textarea>

    <label style="margin-top:10px"><?php echo $msg_personalisation12; ?>: <?php echo mc_displayHelpTip($msg_javascript301,'LEFT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="persAddCost" value="<?php echo (isset($EDIT->persAddCost) ? mc_cleanData($EDIT->persAddCost) : '0.00'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_personalisation9; ?>: <?php echo mc_displayHelpTip($msg_javascript272,'LEFT'); ?></label>
    <?php echo $msg_personalisation13; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="boxType" value="input"<?php echo (isset($EDIT->boxType) && $EDIT->boxType=='input' ? ' checked="checked"' : (!isset($EDIT->boxType) ? ' checked="checked"' : '')); ?>> <?php echo $msg_personalisation19; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="boxType" value="textarea"<?php echo (isset($EDIT->boxType) && $EDIT->boxType=='textarea' ? ' checked="checked"' : ''); ?>>

    <label style="margin-top:10px"><?php echo $msg_personalisation6; ?>: <?php echo mc_displayHelpTip($msg_javascript299,'LEFT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="maxChars" value="<?php echo (isset($EDIT->maxChars) ? mc_cleanData($EDIT->maxChars) : '0'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_personalisation8; ?>: <?php echo mc_displayHelpTip($msg_javascript287,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="reqField" value="yes"<?php echo (isset($EDIT->reqField) && $EDIT->reqField=='yes' ? ' checked="checked"' : (!isset($EDIT->enable) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="reqField" value="no"<?php echo (isset($EDIT->reqField) && $EDIT->reqField=='no' ? ' checked="checked"' : ''); ?>>

    <label style="margin-top:10px"><?php echo $msg_personalisation14; ?>: <?php echo mc_displayHelpTip($msg_javascript303,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enabled" value="yes"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='yes' ? ' checked="checked"' : (!isset($EDIT->enable) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enabled" value="no"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='no' ? ' checked="checked"' : ''); ?>>

  </div>
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="<?php echo (isset($EDIT->id) ? $EDIT->id : 'yes'); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_personalisation3 : $msg_personalisation2)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_personalisation3 : $msg_personalisation2)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=product-personalisation&amp;product='.mc_digitSan($_GET['product']).'\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form>

<?php
$persCnt = mc_rowCount('personalisation WHERE `productID` = \''.mc_digitSan($_GET['product']).'\'');
?>

<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><span class="float" id="loader"></span><span class="float" id="loader_msg" style="display:none" onclick="jQuery(this).hide()"></span><?php echo $msg_personalisation7; ?> (<?php echo $persCnt; ?>):</p>
</div>
<?php
if ($persCnt > 0) {
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery("#sortable").sortable({
    update : function (data) {
      jQuery("#loader").load("index.php?p=product-personalisation&order=<?php echo mc_digitSan($_GET['product']); ?>&"+jQuery('#sortable').sortable('serialize'));
      jQuery('#loader_msg').show('slow');
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
$q_pe = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
        (SELECT count(*) FROM `" . DB_PREFIX . "purch_pers`
         WHERE `" . DB_PREFIX . "purch_pers`.`saleID` > 0
         AND `" . DB_PREFIX . "purch_pers`.`personalisationID` = `" . DB_PREFIX . "personalisation`.`id`
        ) AS `persSaleCnt`
        FROM `" . DB_PREFIX . "personalisation`
        WHERE `productID` = '".mc_digitSan($_GET['product'])."'
        ORDER BY `orderBy`
        ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_pe)>0) {
  while ($PRS = mysqli_fetch_object($q_pe)) {
  $find     = array('{options}','{max}','{cost}','{enabled}','{required}','{boxtype}');
  $replace  = array(
    ($PRS->persOptions != '' ? count(explode('||',$PRS->persOptions)) : 'N/A'),
    $PRS->maxChars,
    mc_currencyFormat($PRS->persAddCost),
    ($PRS->enabled=='yes' ? $msg_script5 : $msg_script6),
    ($PRS->reqField=='yes' ? $msg_script5 : $msg_script6),
    ($PRS->persOptions!='' ? 'N/A' : ($PRS->boxType=='input' ? $msg_personalisation13 : $msg_personalisation19))
  );
  $chop = explode('|',mc_cleanData($PRS->persInstructions));
  $stopPersDeletion = 'no';
  if ($PRS->persSaleCnt > 0) {
    $stopPersDeletion = 'yes';
  }
  ?>
  <div class="panel panel-default" id="pers-<?php echo $PRS->id; ?>" style="cursor:move">
    <div class="panel-body" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[15]); ?>">
      <?php echo $msg_product_personalisation[0] . ': ' . $chop[0]; ?><br>
      <?php echo $msg_product_personalisation[1] . ': ' . (isset($chop[1]) ? $chop[1] : $chop[0]); ?>
      <hr>
      <?php echo str_replace($find,$replace,$msg_personalisation18); ?>
    </div>
    <div class="panel-footer">
      <a href="?p=product-personalisation&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;edit=<?php echo $PRS->id; ?>"><i class="fa fa-pencil fa-fw"></i></a>
      <a href="../?pd=<?php echo (int) $_GET['product']; ?>" onclick="window.open(this);return false"><i class="fa fa-search fa-fw"></i></a>
      <?php
      if ($uDel=='yes') {
        switch($stopPersDeletion) {
          case 'yes':
            echo ' <a href="#" onclick="alert(\''.mc_filterJS($msg_product_personalisation[2]).'\');return false"><i class="fa fa-times fa-fw mc-red"></i></a>';
            break;
          case 'no':
            echo ' <a href="?p=product-personalisation&amp;product='.mc_digitSan($_GET['product']).'&amp;del='.$PRS->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>';
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
<span class="noData"><?php echo $msg_personalisation10; ?></span>
<?php
}
} else {
?>
<span class="noData"><?php echo $msg_personalisation20; ?></span>
<?php
}
?>
</div>


</div>
