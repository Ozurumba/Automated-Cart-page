<?php if (!defined('INSTALL_DIR')) { exit; }
$e = 0;
?>
<div class="container">
<div class="row">

 <div class="col-lg-3">

   <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-cog fa-fw"></i> <b>INSTALL - STEP <?php echo $cmd; ?></b>
      </div>
      <div class="panel-body">
        <div class="progress">
          <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $perc_width; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $perc_width; ?>%"></div>
        </div>
        <p class="progvalue"><b><?php echo $progress; ?>%</b></p>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        You are installing:<br>
        <b><?php echo SCRIPT_NAME; ?></b><br><br>
        Current Version:<br>
        <b><?php echo SCRIPT_VERSION; ?></b>
      </div>
    </div>

 </div>

 <div class="col-lg-9">

   <div class="panel panel-default">
     <div class="panel-body">
       Looking good so far.<br><br>The next check we need to do is for some required modules/functions. It is recommended that all functions are available to help
       prevent issues occurring later. Most of these are enabled by default on most hosts, but if you own a VPS or dedicated server they may need adding. Don`t
       forget to reboot your server after install.
     </div>
   </div>

   <div class="panel panel-default">
     <div class="panel-heading upperc"><i class="fa fa-wrench fa-fw"></i> Required PHP Modules / Functions</div>
     <div class="panel-body">
       <div class="table-responsive">
          <table class="table table-striped table-hover">
          <tbody>
            <tr>
              <td class="bold">GD Graphics Library <span class="italic">(For auto thumbnail creation)</span></td>
              <td class="text-right"><i class="fa fa-<?php if (function_exists('imagecreatefromjpeg')) { echo 'check fa-fw mc_green'; } else { echo 'times fa-fw mc_red';++$e; }; ?>"></td>
            </tr>
            <tr>
              <td class="bold">CURL <span class="italic">(For remote operations with gateways, cleanTalk API &amp; admin software version check)</span></td>
              <td class="text-right"><i class="fa fa-<?php if (function_exists('curl_init')) { echo 'check fa-fw mc_green'; } else { echo 'times fa-fw mc_red';++$e; }; ?>"></td>
            </tr>
            <tr>
              <td class="bold">JSON <span class="italic">(For payment gateway handling, core system callbacks &amp; api`s)</span></td>
              <td class="text-right"><i class="fa fa-<?php if (function_exists('json_encode')) { echo 'check fa-fw mc_green'; } else { echo 'times fa-fw mc_red';++$e; }; ?>"></td>
            </tr>
            <tr>
              <td class="bold">SimpleXML <span class="italic">(For payment gateway handling &amp; api`s)</span></td>
              <td class="text-right"><i class="fa fa-<?php if (function_exists('simplexml_load_string')) { echo 'check fa-fw mc_green'; } else { echo 'times fa-fw mc_red';++$e; }; ?>"></td>
            </tr>
            <tr>
              <td class="bold">Password Hash API <span class="italic">(For secure login)</span></td>
              <td class="text-right"><i class="fa fa-<?php if (function_exists('password_hash')) { echo 'check fa-fw mc_green'; } else { echo 'times fa-fw mc_red';++$e; }; ?>"></td>
            </tr>
            <tr>
              <td class="bold">MySQLi Extension (MySQL Improved) <span class="italic">(For database operations and storage)</span></td>
              <td class="text-right"><i class="fa fa-<?php if (function_exists('mysqli_query')) { echo 'check fa-fw mc_green'; } else { echo 'times fa-fw mc_red';++$e; }; ?>"></td>
            </tr>
            <tr>
              <td class="bold">ZipArchive Class <span class="italic">(For batch PDF download)</span></td>
              <td class="text-right"><i class="fa fa-<?php if (class_exists('ZipArchive')) { echo 'check fa-fw mc_green'; } else { echo 'times fa-fw mc_red';++$e; }; ?>"></td>
            </tr>
            <?php
            if (MSW_PHP == 'old') {
            ?>
            <tr>
              <td class="bold">MyCrypt Library <span class="italic">(For encryption routines)</span></td>
              <td class="text-right"><i class="fa fa-<?php if (function_exists('mcrypt_decrypt')) { echo 'check fa-fw mc_green'; } else { echo 'times fa-fw mc_red';++$e; }; ?>"></td>
            </tr>
            <?php
            } else {
            ?>
            <tr>
              <td class="bold">OpenSSL Library <span class="italic">(For encryption routines)</span></td>
              <td class="text-right"><i class="fa fa-<?php if (extension_loaded('openssl')) { echo 'check fa-fw mc_green'; } else { echo 'times fa-fw mc_red';++$e; }; ?>"></td>
            </tr>
            <?php
            }
            ?>
          </tbody>
          </table>
        </div>
     </div>
   </div>

   <?php
   if ($e == 0) {
   ?>
   <div class="row">
     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
       <button onclick="window.location='?s=2'" class="btn btn-info" type="button"><i class="fa fa-chevron-left fa-fw"></i><span class="hidden-xs"> Previous</span></button>
     </div>
     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
       <button onclick="window.location='?s=4'" class="btn btn-primary" type="button"><span class="hidden-xs">Continue </span><i class="fa fa-chevron-right fa-fw"></i></button>
     </div>
   </div>
   <?php
   } else {
   ?>
   <div class="alert alert-danger">
     <i class="fa fa-warning fa-fw"></i> Please fix the <b><?php echo ($e==1 ? '1 error' : $e . ' errors'); ?></b> above and then <a href="#" onclick="window.location.reload();return false">refresh page</a>.<br><br>
     If you aren`t sure about installing modules / functions, please contact your host.
   </div>
   <?php
   }
   ?>

 </div>

</div>
</div>
