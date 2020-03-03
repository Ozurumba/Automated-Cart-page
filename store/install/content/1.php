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

    <div class="panel panel-default">
      <div class="panel-body">
        Upgrading? <a href="upgrade.php">Click Here</a>
      </div>
    </div>

 </div>

 <div class="col-lg-9">

   <div class="panel panel-default">
     <div class="panel-body">
       Thank you for trying out <?php echo SCRIPT_NAME; ?>, I hope you like it and enjoy using it.<br><br>
       This installation system will guide you through the install procedure.<br><br>
       To begin, please confirm your database connection (<b>control/connect.php</b>). You should also rename your secret key to something unique for security:
     </div>
   </div>

   <div class="panel panel-default">
     <div class="panel-heading upperc"><i class="fa fa-database fa-fw"></i> Connection Details</div>
     <div class="panel-body">
       <div class="table-responsive">
          <table class="table table-striped table-hover">
          <tbody>
            <tr>
              <td class="bold">Database Host:</td>
              <td class="text-right"><?php if (defined('DB_HOST') && DB_HOST != 'Host name goes here..') { echo mc_safeHTML(DB_HOST); } else { echo '<i class="fa fa-times fa-fw mc_red"></i>';++$e;} ?></td>
            </tr>
            <tr>
              <td class="bold">Database Name:</td>
              <td class="text-right"><?php if (defined('DB_NAME') && DB_NAME != 'Database name goes here..') { echo mc_safeHTML(DB_NAME); } else { echo '<i class="fa fa-times fa-fw mc_red"></i>';++$e;} ?></td>
            </tr>
            <tr>
              <td class="bold">Database User:</td>
              <td class="text-right"><?php if (defined('DB_USER') && DB_USER != 'Database user goes here..') { echo mc_safeHTML(DB_USER); } else { echo '<i class="fa fa-times fa-fw mc_red"></i>';++$e;} ?></td>
            </tr>
            <tr>
              <td class="bold">Database Pass:</td>
              <td class="text-right"><?php if (defined('DB_PASS') && DB_PASS != 'Database password goes here..') { echo mc_safeHTML(DB_PASS); } else { echo '<i class="fa fa-times fa-fw mc_red"></i>';++$e;} ?></td>
            </tr>
            <tr>
              <td class="bold">Database Table Prefix:</td>
              <td class="text-right"><?php if (defined('DB_PREFIX')) { echo mc_safeHTML(DB_PREFIX); } else { echo '<i class="fa fa-times fa-fw mc_red"></i>';++$e;} ?></td>
            </tr>
          </tbody>
          </table>
        </div>
     </div>
   </div>

   <div class="panel panel-default">
     <div class="panel-heading upperc"><i class="fa fa-lock fa-fw"></i> Security Checks</div>
     <div class="panel-body">
       <div class="table-responsive">
          <table class="table table-striped table-hover">
          <tbody>
            <tr>
              <td class="bold">Secret Key Renamed:</td>
              <td class="text-right"><i class="fa fa-<?php if (defined('LIC_DEV') || SECRET_KEY != 'abc12345') { echo 'check fa-fw mc_green'; } else { echo 'times fa-fw mc_red';++$e; }; ?>"></td>
            </tr>
          </tbody>
          </table>
        </div>
     </div>
   </div>

   <?php
   if ($e == 0 || defined('LIC_DEV')) {
   ?>
   <div class="nav">
     <button onclick="window.location='?s=2'" class="btn btn-primary" type="button"><span class="hidden-xs">Continue</span> <i class="fa fa-chevron-right fa-fw"></i></button>
   </div>
   <?php
   } else {
   ?>
   <div class="alert alert-danger">
     <i class="fa fa-warning fa-fw"></i> Please fix the <b><?php echo ($e==1 ? '1 error' : $e . ' errors'); ?></b> above and then <a href="#" onclick="window.location.reload();return false">refresh page</a>.
   </div>
   <?php
   }
   ?>

 </div>

</div>
</div>
