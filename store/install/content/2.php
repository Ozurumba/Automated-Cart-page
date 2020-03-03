<?php if (!defined('INSTALL_DIR')) { exit; }
$e = 0;
$dirs = array(
  'admin/attachments/',
  'admin/import/',
  'content/_theme_default/images/banners/',
  'content/_theme_default/cache/',
  'content/products/',
  'product-downloads',
  'logs'
);
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
       Thank you.<br><br>Ok, next lets check some directory permissions. Read/write permissions are required on certain directories. If you are not selling
       downloadable products, add permissions to 'product-downloads' for the installer. The 'product-downloads' directory can be removed after installation if not required.
     </div>
   </div>

   <div class="panel panel-default">
     <div class="panel-heading upperc"><i class="fa fa-folder fa-fw"></i> Permissions</div>
     <div class="panel-body">
       <div class="table-responsive">
          <table class="table table-striped table-hover">
          <tbody>
            <?php
            foreach ($dirs AS $d) {
            $perms = (is_dir(INSTALL_DIR . $d) && is_writeable(INSTALL_DIR . $d) ? 'yes' : 'no');
            ?>
            <tr>
              <td class="bold"><?php echo $d; ?></td>
              <td class="text-right"><i class="fa fa-<?php echo ($perms == 'yes' ? 'check' : 'times'); ?> fa-fw <?php echo ($perms == 'yes' ? 'mc_green' : 'mc_red'); ?>"></i></td>
            </tr>
            <?php
            if ($perms == 'no') {
               ++$e;
             }
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
       <button onclick="window.location='index.php'" class="btn btn-info" type="button"><i class="fa fa-chevron-left fa-fw"></i><span class="hidden-xs"> Previous</span></button>
     </div>
     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
       <button onclick="window.location='?s=3'" class="btn btn-primary" type="button"><span class="hidden-xs">Continue </span><i class="fa fa-chevron-right fa-fw"></i></button>
     </div>
   </div>
   <?php
   } else {
   ?>
   <div class="alert alert-danger">
     <i class="fa fa-warning fa-fw"></i> Please fix the <b><?php echo ($e==1 ? '1 error' : $e . ' errors'); ?></b> above and then <a href="#" onclick="window.location.reload();return false">refresh page</a>. If you aren`t sure about permissions, click <a href="http://www.google.co.uk/search?hl=en&amp;q=ftp+permissions" onclick="window.open(this);return false">here</a>.
   </div>
   <?php
   }
   ?>

 </div>

</div>
</div>
