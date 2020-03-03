<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}

// PRODUCT DESCRIPTION TEMPLATE FILE
// If required you can recall any value from the product table via the $this->PDATA array..
// This is for advanced users only..to see the contents of the array use print_r.
// print_r($this->PDATA)
// Use the mc_safeHTML($data) function for safe display of user content..
?>
<div class="windowBody">

<div class="panel panel-default">
    <div class="panel-heading uppercase">
      <i class="fa fa-file-text-o fa-fw"></i> <?php echo $this->TEXT[0]; ?>
    </div>
    <div class="panel-body">
      <?php
      echo $this->TEXT[1];
      ?>
    </div>
    <div class="panel-footer">
      <?php
      echo $this->TEXT[2];
      ?>
    </div>
  </div>

</div>