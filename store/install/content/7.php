<?php if (!defined('INSTALL_DIR')) { exit; } ?>
<div class="container">
<div class="row">

 <div class="col-lg-3">

   <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-cog fa-fw"></i> <b>INSTALL - COMPLETED</b>
      </div>
      <div class="panel-body">
        <div class="progress">
          <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"></div>
        </div>
        <p class="progvalue"><b>100%</b></p>
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
       <i class="fa fa-check fa-fw"></i> All done! The installer ran with no issues and <?php echo SCRIPT_NAME; ?> is ready to go.
     </div>
   </div>

   <div class="alert alert-danger">
     <i class="fa fa-warning fa-fw"></i> DELETE or rename the 'install' folder in your cart directory NOW!!
   </div>

   <div class="panel panel-default">
     <div class="panel-body">
       Here are a few things worth considering:<br><br>
       <span class="badge">1</span> - Read the rest of the instructions on the <a href="../docs/install-2.html" onclick="window.open(this);return false">installation</a> docs page thoroughly.<br><br>
       <span class="badge">2</span> - Refer to the '<a href="../docs/install.html" onclick="window.open(this);return false">Getting Started</a>' section for help on getting started with your new cart.<br><br>
       <span class="badge">3</span> - If you have issues, see the '<a href="../docs/support.html" onclick="window.open(this);return false">Support Options</a>'. As with any new software, please be patient with it.<br><br>
       <span class="badge">4</span> - The system can be adapted into any existing layouts. <a href="../docs/language.html" onclick="window.open(this);return false">More info</a>.<br><br>
       <span class="badge">5</span> - Once you are happy the system is ready to go live, you should enable the <a href="../docs/system-1.html" onclick="window.open(this);return false">cache</a> to speed up page load times and reduce database queries.<br><br>
       <span class="badge">6</span> - If you like this software, a one time payment for the <a href="https://www.maiancart.com/purchase.html" onclick="window.open(this);return false">commercial version</a> offers many benefits.<br><br>
       I really hope you like <?php echo SCRIPT_NAME; ?> and thank you very much for trying it out.<br><br>
       If you have any comments or feedback please let me know.
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
