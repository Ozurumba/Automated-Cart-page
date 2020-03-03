<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('campaigns','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
  $CRS  = ($EDIT->categories ? explode(',',$EDIT->categories) : array());
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_coupons10);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_coupons11);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_coupons12);
}
define('CALBOX', 'cExpiry');
include(PATH.'templates/js-loader/date-picker.php');
?>

<form method="post" id="form" action="?p=discount-coupons<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_coupons9 : $msg_coupons2); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_coupons3; ?>: <?php echo mc_displayHelpTip($msg_javascript55,'RIGHT'); ?></label>
    <input type="text" name="cName" tabindex="<?php echo (++$tabIndex); ?>" maxlength="250" value="<?php echo (isset($EDIT->cName) ? mc_safeHTML($EDIT->cName) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_coupons4; ?>: <?php echo mc_displayHelpTip($msg_javascript56,'RIGHT'); ?></label>
    <input type="text" name="cDiscountCode" tabindex="<?php echo (++$tabIndex); ?>" maxlength="50" value="<?php echo (isset($EDIT->cDiscountCode) ? $EDIT->cDiscountCode : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_coupons7; ?>: <?php echo mc_displayHelpTip($msg_javascript59,'LEFT'); ?></label>
    <input type="text" name="cDiscount" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->cDiscount) ? $EDIT->cDiscount : '0.00'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_coupons6; ?>: <?php echo mc_displayHelpTip($msg_javascript58,'LEFT'); ?></label>
    <input type="text" name="cUsage" tabindex="<?php echo (++$tabIndex); ?>" id="cUsage" value="<?php echo (isset($EDIT->cUsage) ? $EDIT->cUsage : '0'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_coupons28; ?>: <?php echo mc_displayHelpTip($msg_javascript334,'LEFT'); ?></label>
    <input type="text" name="cExpiry" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->cExpiry) && $EDIT->cExpiry!='0000-00-00' ? mc_convertMySQLDate($EDIT->cExpiry, $SETTINGS) : ''); ?>" id="cExpiry" class="box">

    <label style="margin-top:10px"><?php echo $msg_coupons5; ?>: <?php echo mc_displayHelpTip($msg_javascript57,'LEFT'); ?></label>
    <input type="text" name="cMin" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->cMin) ? $EDIT->cMin : '0.00'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_coupons22; ?>: <?php echo mc_displayHelpTip($msg_javascript63); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="cLive" value="yes"<?php echo (isset($EDIT->cLive) && $EDIT->cLive=='yes' ? ' checked="checked"' : (!isset($EDIT->cLive) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="cLive" value="no"<?php echo (isset($EDIT->cLive) && $EDIT->cLive=='no' ? ' checked="checked"' : ''); ?>>
  </div>
  <br class="clear">
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_coupons31; ?></p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <div class="categoryBoxes">
    <input type="checkbox" name="log" tabindex="<?php echo (++$tabIndex); ?>" value="all" onclick="mc_selectAll()"> <b><?php echo $msg_productadd35; ?></b><br>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <p id="cat_<?php echo $CATS->id; ?>"><input onclick="if(this.checked){mc_selectChildren('cat_<?php echo $CATS->id; ?>','on')}else{mc_selectChildren('cat_<?php echo $CATS->id; ?>','off')}" tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="cat[]" value="<?php echo $CATS->id; ?>"<?php echo (isset($EDIT->id) && in_array($CATS->id,$CRS) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?><br>
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
    &nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" onclick="if(this.checked){mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','on')}else{mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','off')}" type="checkbox" name="cat[]" value="<?php echo $CHILDREN->id; ?>"<?php echo (isset($EDIT->id) && in_array($CHILDREN->id,$CRS) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="cat[]" value="<?php echo $INFANTS->id; ?>"<?php echo (isset($EDIT->id) && in_array($INFANTS->id,$CRS) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
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
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="yes">
 <input class="btn btn-primary" type="submit" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_coupons9 : $msg_coupons2)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_coupons9 : $msg_coupons2)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=discount-coupons\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form><br>

<div class="fieldHeadWrapper" style="margin-top:20px">
  <p><?php echo $msg_coupons13; ?>:</p>
</div>

<?php
$limit   = $page * CAMPAIGNS_PER_PAGE - (CAMPAIGNS_PER_PAGE);
$q_discounts = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,DATE_FORMAT(`cExpiry`,'" . $SETTINGS->mysqlDateFormat . "') AS `edate` FROM `" . DB_PREFIX . "campaigns`
               ORDER BY `cName`
               LIMIT $limit,".CAMPAIGNS_PER_PAGE."
               ") or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  = (isset($c->rows) ? number_format($c->rows,0,'.','') : '0');
if (mysqli_num_rows($q_discounts)>0) {
  while ($DISCOUNT = mysqli_fetch_object($q_discounts)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <b><?php echo mc_safeHTML($DISCOUNT->cName); ?></b><br><br>
      <?php
      switch($DISCOUNT->cDiscount) {
        case 'freeshipping':
          echo str_replace(array('{code}','{expiry}','{usage}','{min}','{discount}','{enabled}'),array($DISCOUNT->cDiscountCode,($DISCOUNT->cExpiry!='0000-00-00' ? $DISCOUNT->edate : 'N/A'),$DISCOUNT->cUsage,mc_currencyFormat(($DISCOUNT->cMin>0 ? $DISCOUNT->cMin : '0.00')),(strpos($DISCOUNT->cDiscount,'%')===FALSE && !in_array($DISCOUNT->cDiscount,array('freeshipping','notax')) ? mc_currencyFormat($DISCOUNT->cDiscount) : (in_array($DISCOUNT->cDiscount,array('freeshipping','notax')) ? mc_getDiscountType($DISCOUNT->cDiscount) : $DISCOUNT->cDiscount)),($DISCOUNT->cLive=='yes' ? $msg_script5 : $msg_script6)),$msg_coupons29);
          break;
        case 'notax':
          echo str_replace(array('{code}','{expiry}','{usage}','{min}','{discount}','{enabled}'),array($DISCOUNT->cDiscountCode,($DISCOUNT->cExpiry!='0000-00-00' ? $DISCOUNT->edate : 'N/A'),$DISCOUNT->cUsage,mc_currencyFormat(($DISCOUNT->cMin>0 ? $DISCOUNT->cMin : '0.00')),(strpos($DISCOUNT->cDiscount,'%')===FALSE && !in_array($DISCOUNT->cDiscount,array('freeshipping','notax')) ? mc_currencyFormat($DISCOUNT->cDiscount) : (in_array($DISCOUNT->cDiscount,array('freeshipping','notax')) ? mc_getDiscountType($DISCOUNT->cDiscount) : $DISCOUNT->cDiscount)),($DISCOUNT->cLive=='yes' ? $msg_script5 : $msg_script6)),$msg_coupons30);
          break;
        default:
          echo str_replace(array('{code}','{expiry}','{usage}','{min}','{discount}','{enabled}','{cats}'),array($DISCOUNT->cDiscountCode,($DISCOUNT->cExpiry!='0000-00-00' ? $DISCOUNT->edate : 'N/A'),$DISCOUNT->cUsage,mc_currencyFormat(($DISCOUNT->cMin>0 ? $DISCOUNT->cMin : '0.00')),(strpos($DISCOUNT->cDiscount,'%')===FALSE && !in_array($DISCOUNT->cDiscount,array('freeshipping','notax')) ? mc_currencyFormat($DISCOUNT->cDiscount) : (in_array($DISCOUNT->cDiscount,array('freeshipping','notax')) ? mc_getDiscountType($DISCOUNT->cDiscount) : $DISCOUNT->cDiscount)),($DISCOUNT->cLive=='yes' ? $msg_script5 : $msg_script6),($DISCOUNT->categories ? $msg_script5.', '.count(explode(',',$DISCOUNT->categories)) : $msg_script6)),$msg_coupons21);
          break;
      }
    ?>
    </div>
    <div class="panel-footer">
     <a href="?p=coupon-report&amp;code=<?php echo $DISCOUNT->id; ?>"><i class="fa fa-bar-chart fa-fw"></i></a>&nbsp;&nbsp;
     <a href="?p=discount-coupons&amp;edit=<?php echo $DISCOUNT->id; ?>"><i class="fa fa-pencil fa-fw"></i></a>&nbsp;&nbsp;
     <?php
     if ($uDel=='yes') {
     ?>
     <a href="?p=discount-coupons&amp;del=<?php echo $DISCOUNT->id; ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
     <?php
     }
     ?>
    </div>
  </div>
  <?php
  }
  define('PER_PAGE',CAMPAIGNS_PER_PAGE);
  if ($countedRows>0 && $countedRows > PER_PAGE) {
    $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
    echo $PGS->display();
  }
} else {
?>
<span class="noData"><?php echo $msg_coupons14; ?></span>
<?php
}
?>


</div>
