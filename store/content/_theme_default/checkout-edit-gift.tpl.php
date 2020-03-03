<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}

// CHECKOUT GIFT CERTIFICATE EDIT TEMPLATE FILE
?>
<div class="windowBody" id="cgf_<?php echo $this->GIFTID; ?>">

  <form method="post" action="#">
  <div class="panel panel-default">
    <div class="panel-heading uppercase">
      <i class="fa fa-pencil fa-fw"></i> <?php echo $this->TEXT[3]; ?>
    </div>
    <div class="panel-body">
      <div class="giftarea">
        <label><?php echo $this->TEXT[4]; ?></label>
        <input type="text" name="gift[fn]" onkeyup="mc_clearGiftFlag()" value="<?php echo mc_safeHTML($this->GIFT['from_name']); ?>" class="form-control" tabindex="1">

        <label style="padding-top:10px"><?php echo $this->TEXT[5]; ?></label>
        <input type="text" name="gift[fe]" onkeyup="mc_clearGiftFlag()" value="<?php echo mc_safeHTML($this->GIFT['from_email']); ?>" class="form-control" tabindex="2">

        <label style="padding-top:10px"><?php echo $this->TEXT[6]; ?></label>
        <input type="text" name="gift[tn]" onkeyup="mc_clearGiftFlag()" value="<?php echo mc_safeHTML($this->GIFT['to_name']); ?>" class="form-control" tabindex="3">

        <label style="padding-top:10px"><?php echo $this->TEXT[7]; ?></label>
        <input type="text" name="gift[te]" onkeyup="mc_clearGiftFlag()" value="<?php echo mc_safeHTML($this->GIFT['to_email']); ?>" class="form-control" tabindex="4">

        <label style="padding-top:10px"><?php echo $this->TEXT[8]; ?></label>
        <textarea rows="5" cols="2" name="gift[msg]" onkeyup="mc_clearGiftFlag()" tabindex="5" class="form-control"><?php echo mc_safeHTML($this->GIFT['message']); ?></textarea>
      </div>
    </div>
    <div class="panel-footer">
      <button type="button" class="btn btn-primary" onclick="mc_chkGift('<?php echo $this->GIFTID; ?>')"><i class="fa fa-check fa-fw"></i> <?php echo $this->TEXT[1]; ?></button>
    </div>
  </div>
  </form>

</div>