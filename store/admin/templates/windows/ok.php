<?php if (!defined('PARENT')) { die('Permission Denied'); }?>

<div id="windowcontent">

  <div class="ok">

    <i class="fa fa-check fa-fw"></i>

    <?php
    if (isset($winMSG)) {
    ?>
    <div><?php echo $winMSG; ?></div>
    <?php
    }
    ?>

  </div>

</div>