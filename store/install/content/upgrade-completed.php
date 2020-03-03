<?php if (!defined('INSTALL_DIR')) { exit; } ?>
<div class="container">
<div class="row">

 <div class="col-lg-3">

   <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-cog fa-fw"></i> <b>UPGRADE - COMPLETED</b>
      </div>
      <div class="panel-body">
        <div class="progress">
          <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"></div>
        </div>
        <p style="text-align:center;font-size:20px"><b>100%</b></p>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        You are upgrading:<br>
        <b><?php echo SCRIPT_NAME; ?></b><br><br>
        Latest Build:<br>
        <b><?php echo SCRIPT_VERSION; ?></b><br><br>
        Version Installed:<br>
        <b><?php echo $SETTINGS->softwareVersion; ?></b>
      </div>
    </div>

 </div>

 <div class="col-lg-9">

   <div class="panel panel-default">
     <div class="panel-body">
       <i class="fa fa-check fa-fw"></i> All done! Upgrade is now complete.
     </div>
   </div>

   <div class="alert alert-danger">
     <i class="fa fa-warning fa-fw"></i> DELETE or rename the 'install' folder in your cart directory NOW!!
   </div>

   <div class="panel panel-default">
     <div class="panel-body">
       I really hope you are liking <?php echo SCRIPT_NAME; ?> and thanks for upgrading.
     </div>
   </div>

   <div class="row">
     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
       <button onclick="window.location='../index.php'" class="btn btn-info" type="button"><i class="fa fa-search fa-fw"></i><span class="hidden-xs"> View Store</span></button>
     </div>
     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
       <button onclick="window.location='../admin/index.php'" class="btn btn-primary" type="button"><span class="hidden-xs">Admin CP </span><i class="fa fa-lock fa-fw"></i></button>
     </div>
   </div>

 </div>

</div>
</div>
