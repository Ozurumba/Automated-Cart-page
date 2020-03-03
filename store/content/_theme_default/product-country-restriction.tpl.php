<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}

// PRODUCT COUNTRY RESTRICTION TEMPLATE FILE
// If required you can recall any value from the product table via the $this->PDATA array..
// This is for advanced users only..to see the contents of the array use print_r.
// print_r($this->PDATA)
// Use the mc_safeHTML($data) function for safe display of user content..
?>
<div class="windowBody">

  <div class="panel panel-danger">
    <div class="panel-heading uppercase">
      <i class="fa fa-warning fa-fw"></i> <?php echo $this->TEXT[0][14]; ?>
    </div>
    <div class="panel-body">
      <div class="alert alert-info">
      <?php
      echo $this->TEXT[0][15];
      ?>
      </div>
      <hr>
      <?php
      echo $this->COUNTRIES;
      ?>
    </div>
  </div>

</div>