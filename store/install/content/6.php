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

   <form method="post" action="?s=6">
   <div class="panel panel-default">
     <div class="panel-body">
       <i class="fa fa-check fa-fw"></i> Store information successfully updated.<br><br>
       You can install the demo cart items if you quickly want to see if the system is working, see below.<br><br>
       Clicking the button below will complete the installation.
     </div>
   </div>

   <?php
   if (file_exists(INSTALL_DIR . PRODUCTS_FOLDER . '/demo/img_1-1.jpg')) {
   ?>
   <div class="panel panel-default">
     <div class="panel-heading upperc"><i class="fa fa-shopping-cart fa-fw"></i> Install Demo Store</div>
     <div class="panel-body">
       <label>Do you want to install the demo products? Click <a href="http://www.maiansoftware.com/demos/mcart/" onclick="window.open(this);return false">here</a> to view demo.</label>
       <div class="checkbox">
         <label><input type="checkbox" name="demo" value="yes"> Yes, install demo products</label>
       </div>
       <hr>
       <span class="italic"><i class="fa fa-info-circle fa-fw"></i> This can be useful if you want to quickly test the store is working without having to add your own data. Demo items can be removed at any time. If you don`t
       want to install the demo items they will be deleted fom your setup.</span>
     </div>
   </div>
   <?php
   }
   ?>

   <div class="row">
     <div class="col-lg-12 text-right">
       <button class="btn btn-primary" type="submit"><span class="hidden-xs">Complete Installation </span><i class="fa fa-chevron-right fa-fw"></i></button>
     </div>
     <input type="hidden" name="finish" value="yes">
   </div>

   </form>

 </div>

</div>
</div>
