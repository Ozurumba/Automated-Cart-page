<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}

// ACCOUNT CODE HELP TEMPLATE FILE
// If required you can recall any value from the accounts table via the $this->ADATA array..
// This is for advanced users only..to see the contents of the array use print_r.
// print_r($this->ADATA)
// Use the mc_safeHTML($data) function for safe display of user content..
?>
<div class="windowBody">

  <div class="panel panel-default">
    <div class="panel-heading uppercase">
      <i class="fa fa-file-text-o fa-fw"></i> <?php echo $this->TXT[0][1]; ?>
    </div>
    <div class="panel-body">
      <?php
      // HTML code example..
      echo $this->CODE[0];
      ?>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading uppercase">
      <i class="fa fa-file-text-o fa-fw"></i> <?php echo $this->TXT[0][2]; ?>
    </div>
    <div class="panel-body">
      <?php
      // BB code example..
      echo $this->CODE[1];
      ?>
    </div>
  </div>

</div>