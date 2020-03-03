<?php if (!defined('PARENT')) { die('Permission Denied'); }
define('SALE_ADD', 1);
$salesToAdd = 0;
// Arrays..
if (!isset($_SESSION['add-phys-'.mc_encrypt(SECRET_KEY)])) {
  $_SESSION['add-phys-'.mc_encrypt(SECRET_KEY)] = array();
}
if (!isset($_SESSION['add-down-'.mc_encrypt(SECRET_KEY)])) {
  $_SESSION['add-down-'.mc_encrypt(SECRET_KEY)] = array();
}
?>
<div id="content">

<?php
define('CALBOX', 'pDate');
include(PATH . 'templates/js-loader/date-picker.php');
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  function mc_infoLoader(bill, ship, other) {
    if (bill['id'] != undefined) {
      jQuery('input[name="bill_1"]').val(bill['nm']);
      jQuery('input[name="bill_2"]').val(bill['em']);
      jQuery('input[name="bill_3"]').val(bill['addr2']);
      jQuery('input[name="bill_4"]').val(bill['addr3']);
      jQuery('input[name="bill_5"]').val(bill['addr4']);
      jQuery('input[name="bill_6"]').val(bill['addr5']);
      jQuery('input[name="bill_7"]').val(bill['addr6']);
      jQuery('select[name="bill_9"]').val(bill['addr1']);
    }
    if (ship['id'] != undefined) {
      jQuery('input[name="ship_1"]').val(ship['nm']);
      jQuery('input[name="ship_2"]').val(ship['em']);
      jQuery('input[name="ship_3"]').val(ship['addr2']);
      jQuery('input[name="ship_4"]').val(ship['addr3']);
      jQuery('input[name="ship_5"]').val(ship['addr4']);
      jQuery('input[name="ship_6"]').val(ship['addr5']);
      jQuery('input[name="ship_7"]').val(ship['addr6']);
      jQuery('input[name="ship_8"]').val(ship['addr7']);
      jQuery('select[name="ship_9"]').val(ship['addr1']);
    }
    jQuery('input[name="sale_type"]').val(other['type']);
    jQuery('input[name="ipAddress"]').val(other['ip']);
  }
  jQuery('input[name="acc_name"]').autocomplete({
	  source: 'index.php?p=ajax-ops&op=auto-name',
		dataType: 'json',
    minLength: <?php echo AUTO_COMPLETE_MIN_LENGTH; ?>,
		select: function(event,ui) {
		  jQuery('input[name="acc_email"]').val(ui.item.email);
      jQuery('input[name="account"]').val(ui.item.id);
      mc_infoLoader(ui.item.bill,ui.item.ship,ui.item.other);
		}
  });
	jQuery('input[name="acc_email"]').autocomplete({
	  source: 'index.php?p=ajax-ops&op=auto-email',
		dataType: 'json',
    minLength: <?php echo AUTO_COMPLETE_MIN_LENGTH; ?>,
		select: function(event,ui) {
		  jQuery('input[name="acc_name"]').val(ui.item.name);
			jQuery('input[name="account"]').val(ui.item.id);
      mc_infoLoader(ui.item.bill,ui.item.ship,ui.item.other);
		}
  });
});
//]]>
</script>

<div id="form_field">
<form method="post" id="form" action="?p=sales-add" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#pch_tang" data-toggle="tab"><i class="fa fa-cubes fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_viewsale2; ?></span></a></li>
      <li><a href="#pch_down" data-toggle="tab"><i class="fa fa-download fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_viewsale3; ?></span></a></li>
    </ul>
  </div>
</div>

<div class="row" style="margin-top:10px">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="tab-content">
      <div class="tab-pane active in" id="pch_tang">
        <?php
        include(PATH . 'templates/sales/sales-add/add-tangible.php');
        ?>
      </div>
      <div class="tab-pane fade" id="pch_down">
        <?php
        include(PATH . 'templates/sales/sales-add/add-downloads.php');
        ?>
      </div>
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
      include(PATH . 'templates/sales/sales-add/shipping-area.php');
      ?>
    </div>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <?php
    include(PATH . 'templates/sales/sales-add/totals-area.php');
    ?>
  </div>
</div>

<div class="row" style="margin-top:20px">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#one" data-toggle="tab"><i class="fa fa-user fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_add_sale[0]; ?></span></a></li>
      <li><a href="#two" data-toggle="tab"><i class="fa fa-credit-card fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_viewsale98; ?></span></a></li>
      <li><a href="#three" data-toggle="tab"><i class="fa fa-truck fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_admin_viewsale3_0[0]; ?></span></a></li>
      <li><a href="#four" data-toggle="tab"><i class="fa fa-cog fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_admin_viewsale3_0[1]; ?></span></a></li>
      <li><a href="#five" data-toggle="tab"><i class="fa fa-file-text-o fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $msg_viewsale6; ?></span></a></li>
    </ul>
  </div>
</div>

<div class="formFieldWrapper" style="margin-top:10px">
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="tab-content">
        <div class="tab-pane active in" id="one">
          <label><?php echo $msg_add_sale[1]; ?>:</label>
          <input type="text" class="box" name="acc_name" value="">

          <label style="margin-top:10px"><?php echo $msg_add_sale[2]; ?>:</label>
          <input type="text" class="box" name="acc_email" value="">

          <div class="acc_extras">
            <label style="margin-top:10px"><?php echo $msg_add_sale[3]; ?>:</label>
            <input type="checkbox" name="acc_create" value="yes" checked="checked"> <?php echo $msg_script5; ?>

            <label style="margin-top:10px"><?php echo $msg_add_sale[4]; ?>:</label>
            <input type="checkbox" name="acc_send" value="yes"> <?php echo $msg_script5; ?>
          </div>
        </div>
        <div class="tab-pane fade" id="two">
          <?php
          include(PATH . 'templates/sales/sales-add/billing-address.php');
          ?>
        </div>
        <div class="tab-pane fade" id="three">
          <?php
          include(PATH . 'templates/sales/sales-add/shipping-address.php');
          ?>
        </div>
        <div class="tab-pane fade" id="four">
          <?php
          include(PATH . 'templates/sales/sales-add/sales-options.php');
          ?>
        </div>
        <div class="tab-pane fade" id="five">
          <textarea rows="5" cols="20" name="saleNotes"></textarea>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_viewsale60; ?>:</p>
</div>

<div class="formFieldWrapper">
  <?php
  include(PATH . 'templates/sales/sales-add/status.php');
  ?>
</div>

<p style="text-align:center;margin:20px 0 50px 0">
  <input type="hidden" name="account" value="0">
  <input type="hidden" name="sale_type" value="personal">
  <input type="hidden" name="process_add_sale" id="process_add_sale" value="yes">
  <input<?php echo ($salesToAdd == 0 ? ' disabled="disabled" ' : ' '); ?>class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_addsale7); ?>" title="<?php echo mc_cleanDataEntVars($msg_addsale7); ?>">
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="mc_confirmMessageUrl('<?php echo mc_filterJS($msg_javascript45); ?>','?p=sales-add&amp;clear=yes')" value="<?php echo mc_cleanDataEntVars($msg_addsale10); ?>" title="<?php echo mc_cleanDataEntVars($msg_addsale10); ?>">
</p>

</form>
</div>

</div>
