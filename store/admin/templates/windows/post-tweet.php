<?php if (!defined('PARENT')) { exit; }
if (function_exists('curl_init') && version_compare(phpversion(), MSW_PHP_MIN_VER, '>')) {
?>
<div>

  <div class="fieldHeadWrapper">
    <p><i class="fa fa-edit fa-fw"></i> <?php echo $msg_admin_settings3_0[42]; ?></p>
  </div>

  <div>
   <textarea name="tweet" rows="4" cols="40" style="height:150px" class="form-control"></textarea><br>
   <button type="button" class="btn btn-primary" onclick="mc_apiHandler('tweet')"><i class="fa fa-twitter fa-fw"></i> <?php echo mc_safeHTML($msg_admin_settings3_0[42]); ?></button>
  </div>

</div>
<?php
} else {
?>
<div>

  <div class="fieldHeadWrapper">
    <i class="fa fa-warning fa-fw"></i> ERROR
  </div>

  <div style="padding-top:30px;border-top:1px dashed #ccc">
   One or more requirements are not met for this function to work.<br><br>
   <?php
   echo 'PHP <?php echo MSW_PHP_MIN_VER; ?> or higher (Required): ' . (version_compare(phpversion(), MSW_PHP_MIN_VER, '>') ? '<i class="fa fa-check fa-fw"></i>' : '<i class="fa fa-times fa-fw"></i>').'<br>';
   echo 'CURL functions enabled (Required): ' . (function_exists('curl_init') ? '<i class="fa fa-check fa-fw"></i>' : '<i class="fa fa-times fa-fw"></i>').'<br>';
   echo 'Open SSL enabled (Optional, but recommended): ' . (function_exists('openssl_open') ? '<i class="fa fa-check fa-fw"></i>' : '<i class="fa fa-times fa-fw"></i>');
   ?>
  </div>

</div>
<?php
}
?>