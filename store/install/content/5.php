<?php if (!defined('INSTALL_DIR')) { exit; } ?>
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

   <form method="post" action="?s=5" id="form" onsubmit="return _check()">
   <div class="panel panel-default">
     <div class="panel-body">
       <i class="fa fa-check fa-fw"></i> Tables successfully created.
     </div>
   </div>

   <div class="panel panel-default">
     <div class="panel-heading upperc"><i class="fa fa-shopping-basket fa-fw"></i> Enter Store Information</div>
     <div class="panel-body">
       <div class="form-group">
         <label>Store Name</label>
         <div class="form-group input-group">
           <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
           <input type="text" name="website" class="form-control" maxlength="250" autofocus>
         </div>
       </div>
       <div class="form-group">
         <label>E-Mail Address</label>
         <div class="form-group input-group">
           <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
           <input type="text" name="email" class="form-control" maxlength="250">
         </div>
       </div>
     </div>
   </div>

   <div class="row">
     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
       <button onclick="window.location='?s=4'" class="btn btn-info" type="button"><i class="fa fa-chevron-left fa-fw"></i><span class="hidden-xs"> Previous</span></button>
     </div>
     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
       <button class="btn btn-primary" type="submit"><span class="hidden-xs">Update &amp; Continue </span><i class="fa fa-chevron-right fa-fw"></i></button>
     </div>
     <input type="hidden" name="storeInfo" value="yes">
   </div>

   </form>

 </div>

</div>
</div>
