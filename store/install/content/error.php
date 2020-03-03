<?php if (!defined('INSTALL_DIR')) { exit; } ?>
<div class="container">
<div class="row">

 <div class="col-lg-3">

   <div class="panel panel-warning">
      <div class="panel-heading">
        <i class="fa fa-times fa-fw"></i> <b>ERROR</b>
      </div>
      <div class="panel-body">
        Installation Failed
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

   <?php
   switch($code) {
     case 'old':
       ?>
       <div class="panel panel-default">
         <div class="panel-body">
           The PHP version you have installed on your server is too old and this software cannot run.<br><br>PHP v<?php echo MSW_PHP_MIN_VER; ?> or higher is required.<br><br>
           <b>Your Version:</b> <?php echo phpVersion(); ?><br><br>
           Please <a href="http://php.net/downloads.php" onclick="window.open(this);return false">upgrade</a> your installation.
         </div>
       </div>
       <?php
       break;
     case 'sdata':
     case 'tables':
       ?>
       <div class="panel panel-default">
         <div class="panel-body">
           An error occurred during the install process and the installer has terminated. This may be due to server issues or in some cases, bugs within MySQL.<br><br>
           The following options are now available:<br><br>
           <span class="badge">1</span> - <a href="index.php?s=4">Re-run</a> the installer again.<br><br>
           <span class="badge">2</span> - Run the database setup manually via the instructions on the <a href="../docs/install-2.html">installation</a> documentation page.
         </div>
       </div>
       <div class="panel panel-warning">
         <div class="panel-heading upperc"><i class="fa fa-pencil fa-fw"></i> ADVANCED USERS ONLY - SEND ERROR REPORT</div>
         <div class="panel-body">
           I would be grateful if you would send me an error report so that I am aware of this issue and can improve the installer. This is optional, but if you would like to send me the report,
           please do the following:<br><br>
           <span class="badge">1</span> - Make sure the store "logs" folder has write permissions.<br><br>
           <span class="badge">2</span> - Enable relevant debug log in 'install/control/config.php'.<br><br>
           <span class="badge">3</span> - <a href="index.php?s=4">Re-run</a> the installer again to re-produce the error.<br><br>
           <span class="badge">4</span> - <a href="mailto:msworld67@gmail.com?subject=<?php echo str_replace(' ','%20',SCRIPT_NAME); ?>%20Error%20Report">E-mail</a> me a copy of the log file created.<br><br>
           Thank you very very much and sorry for the inconvenience.
         </div>
       </div>
       <?php
       break;
   }
   ?>

 </div>

</div>
</div>
