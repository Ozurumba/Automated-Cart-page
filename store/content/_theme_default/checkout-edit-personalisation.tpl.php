<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}

// CHECKOUT PERSONALISATION EDIT TEMPLATE FILE
?>
<div class="windowBody" id="cpe_<?php echo $_GET['ppCE']; ?>">

  <form method="post" action="#">
  <div class="panel panel-default">
    <div class="panel-heading uppercase">
      <i class="fa fa-pencil fa-fw"></i> <?php echo $this->TEXT[0]; ?>
    </div>
    <div class="panel-body">
      <?php
      // PERSONALISATION OPTIONS
      // html/products/product-personalisation-input.htm
      // html/products/product-personalisation-script.htm
      // html/products/product-personalisation-select.htm
      // html/products/product-personalisation-select-option.htm
      // html/products/product-personalisation-textarea.htm
      // html/products/product-personalisation-wrapper.htm
      echo $this->OPTIONS[0];
      ?>
    </div>
    <div class="panel-footer">
      <button type="button" class="btn btn-primary" onclick="mc_chkPers('<?php echo $_GET['ppCE']; ?>')"><i class="fa fa-check fa-fw"></i> <?php echo $this->TEXT[1]; ?></button>
    </div>
  </div>
  </form>

</div>