<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}

// PAYMENT INFORMATION (NONE GATEWAY) TEMPLATE FILE
?>
<div class="windowBody">

  <div class="panel panel-default">
    <div class="panel-heading uppercase">
      <i class="fa fa-info-circle fa-fw"></i> <?php echo $this->TEXT[0]; ?>: <b><?php echo $this->TEXT[1]; ?></b>
    </div>
    <div class="panel-body">
      <?php
      echo $this->TEXT[2];
      ?>
    </div>
  </div>

</div>