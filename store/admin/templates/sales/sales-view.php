<?php if (!defined('PARENT') || !isset($SALE->paymentStatus)) { die('Permission Denied'); }
if (in_array($SALE->paymentStatus, array('','pending'))) {
  define('INCPL_SALE', 1);
}
?>
<div id="content">

<?php
define('SALE_EDIT', 1);

if (isset($OK)) {
  echo mc_actionCompleted($msg_viewsale11);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_viewsale10);
}
if (isset($OK_ADD)) {
  echo mc_actionCompleted($msg_viewsale77);
}

define('CALBOX', 'pDate');
include(PATH . 'templates/js-loader/date-picker.php');

?>

<div class="alert alert-info">
  <?php
  $qLinksArr  = array('xxx');
  $qLinksIcon = 'cube';
  $saleID     = (int) $_GET['sale'];
  include(PATH . 'templates/sales/sales-quick-links.php');
  ?>
</div>

<?php
$tg_cnt = mc_sumCount('purchases WHERE `saleID` = \''.mc_digitSan($_GET['sale']).'\' AND `productType` = \'physical\' AND `saleConfirmation` = \'' . (defined('INCPL_SALE') ? 'no' : 'yes') . '\'','productQty');
$dl_cnt = mc_sumCount('purchases WHERE `saleID` = \''.mc_digitSan($_GET['sale']).'\' AND `productType` = \'download\' AND `saleConfirmation` = \'' . (defined('INCPL_SALE') ? 'no' : 'yes') . '\'','productQty');
$gt_cnt = mc_sumCount('purchases WHERE `saleID` = \''.mc_digitSan($_GET['sale']).'\' AND `productType` = \'virtual\' AND `saleConfirmation` = \'' . (defined('INCPL_SALE') ? 'no' : 'yes') . '\'','productQty');
?>

<div id="form_field">
<form method="post" id="form" action="?p=sales-view&amp;sale=<?php echo mc_digitSan($_GET['sale']); ?>" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <ul class="nav nav-tabs">
      <?php
      if ($tg_cnt > 0) {
      $act = 'yes';
      ?>
      <li class="active"><a href="#pch_tang" data-toggle="tab"><i class="fa fa-cubes fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_viewsale2; ?></span></a></li>
      <?php
      }
      if ($dl_cnt > 0) {
      if (!isset($act)) {
        $act2 = true;
      }
      ?>
      <li<?php echo (isset($act2) ? ' class="active"' : ''); ?>><a href="#pch_down" data-toggle="tab"><i class="fa fa-download fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_viewsale3; ?></span></a></li>
      <?php
      }
      if ($gt_cnt > 0) {
      if (!isset($act) && !isset($act2)) {
        $act3 = true;
      }
      ?>
      <li<?php echo (isset($act3) ? ' class="active"' : ''); ?>><a href="#pch_gift" data-toggle="tab"><i class="fa fa-gift fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_viewsale117; ?></span></a></li>
      <?php
      }
      ?>
    </ul>
  </div>
</div>

<div class="row" style="margin-top:10px">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="tab-content">
      <?php
      if ($tg_cnt > 0) {
      $act = 'yes';
      ?>
      <div class="tab-pane active in" id="pch_tang">
        <?php
        include(PATH . 'templates/sales/sales-view/purchased-tangible.php');
        ?>
      </div>
      <?php
      }
      if ($dl_cnt > 0) {
      if (!isset($act)) {
        $act2 = true;
      }
      ?>
      <div class="tab-pane<?php echo (isset($act2) ? ' active in' : ' fade'); ?>" id="pch_down">
        <?php
        include(PATH . 'templates/sales/sales-view/purchased-downloads.php');
        ?>
      </div>
      <?php
      }
      if ($gt_cnt > 0) {
      if (!isset($act) && !isset($act2)) {
        $act3 = true;
      }
      ?>
      <div class="tab-pane<?php echo (isset($act3) ? ' active in' : ' fade'); ?>" id="pch_gift">
        <?php
        include(PATH . 'templates/sales/sales-view/purchased-gifts.php');
        ?>
      </div>
      <?php
      }
      ?>
    </div>
  </div>
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_viewsale24; ?>:</p>
</div>

<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <div class="formFieldWrapper shiparealeft">
      <?php
      include(PATH . 'templates/sales/sales-view/shipping-area.php');
      ?>
    </div>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <?php
    include(PATH . 'templates/sales/sales-view/totals-area.php');
    ?>
  </div>
</div>

<?php
// Legacy addresses..
if ($SALE->buyerAddress) {
  $addchop = explode(mc_defineNewline(),$SALE->buyerAddress);
  for ($i=0; $i<7; $i++) {
    if (isset($addchop[$i])) {
      $f          = 'ship_'.($i+3);
      $f2         = 'bill_'.($i+3);
      $SALE->$f   = $addchop[$i];
      $SALE->$f2  = $addchop[$i];
    }
  }
}
?>

<div class="row" style="margin-top:20px">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#one" data-toggle="tab"><i class="fa fa-credit-card fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_viewsale98; ?></span></a></li>
      <li><a href="#two" data-toggle="tab"><i class="fa fa-truck fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_admin_viewsale3_0[0]; ?></span></a></li>
      <li><a href="#three" data-toggle="tab"><i class="fa fa-cog fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_admin_viewsale3_0[1]; ?></span></a></li>
      <li><a href="#four" data-toggle="tab"><i class="fa fa-file-text-o fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_viewsale6; ?></span></a></li>
    </ul>
  </div>
</div>

<div class="formFieldWrapper" style="margin-top:10px">
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="tab-content">
        <div class="tab-pane active in" id="one">
          <?php
          include(PATH . 'templates/sales/sales-view/billing-address.php');
          ?>
        </div>
        <div class="tab-pane fade" id="two">
          <?php
          include(PATH . 'templates/sales/sales-view/shipping-address.php');
          ?>
        </div>
        <div class="tab-pane fade" id="three">
          <?php
          include(PATH . 'templates/sales/sales-view/sales-options.php');
          ?>
        </div>
        <div class="tab-pane fade" id="four">
          <textarea rows="5" cols="20" name="saleNotes"><?php echo mc_safeHTML($SALE->saleNotes); ?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="fieldHeadWrapper">
  <p><span class="pull-right" style="text-transform:none"><a href="?p=sales-view&amp;status_view=<?php echo (int) $_GET['sale']; ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_FIELD_INFO_HEIGHT; ?>','<?php echo DIVWIN_FIELD_INFO_WIDTH; ?>',this.title);return false"><i class="fa fa-history fa-fw"></i> <span class="hidden-xs hidden-sm"><?php echo $msg_admin_viewsale3_0[22]; ?></span></a></span><?php echo $msg_viewsale60; ?>:</p>
</div>

<div class="formFieldWrapper">
  <?php
  include(PATH . 'templates/sales/sales-view/status.php');
  ?>
</div>

<p style="text-align:center;margin:20px 0 50px 0">
  <input type="hidden" name="bill_mail" value="<?php echo mc_safeHTML($SALE->bill_2); ?>">
  <input type="hidden" name="s_acc" value="<?php echo mc_safeHTML($SALE->account); ?>">
  <input type="hidden" name="s_type" value="<?php echo mc_safeHTML($SALE->type); ?>">
  <input type="hidden" name="process" id="process_load" value="yes">
  <input type="hidden" name="saleConfirm" value="<?php echo $SALE->saleConfirmation; ?>">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_viewsale44); ?>" title="<?php echo mc_cleanDataEntVars($msg_viewsale44); ?>">
</p>

</form>
</div>

</div>
