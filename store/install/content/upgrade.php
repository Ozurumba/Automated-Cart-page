<?php if (!defined('INSTALL_DIR')) { exit; } ?>
<div class="container">
<div class="row">

 <div class="col-lg-3">

   <?php
   if (isset($_GET['upgrade']) || SCRIPT_VERSION > $SETTINGS->softwareVersion) {
   ?>
   <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-cog fa-fw"></i> <b>UPGRADE PROGRESS</b>
      </div>
      <div class="panel-body">
        <div class="progress">
          <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div>
        </div>
        <p class="progvalue"><b>0%</b></p>
      </div>
    </div>
   <?php
   } else {
   if (!isset($_GET['completed'])) {
   ?>
   <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-hourglass fa-fw"></i> <b>VERSION CHECK</b>
      </div>
      <div class="panel-body">
        The latest version is: <b>v<?php echo SCRIPT_VERSION; ?></b><br><br>
        Your installed version is: <b>v<?php echo $SETTINGS->softwareVersion; ?></b>
      </div>
   </div>

   <div class="panel panel-default">
      <div class="panel-body">
        <i class="fa fa-warning fa-fw"></i> <a href="../docs/info3.html"onclick="window.open(this);return false">Check Upgrade Appendix</a>
      </div>
    </div>

   <div class="panel panel-default">
      <div class="panel-body">
        Clean Install? <a href="index.php">Click Here</a>
      </div>
    </div>
   <?php
   } else {
   ?>
   <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-cog fa-fw"></i> <b>UPGRADE - COMPLETED</b>
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
        You are upgrading:<br>
        <b><?php echo SCRIPT_NAME; ?></b><br><br>
        Latest Build:<br>
        <b><?php echo SCRIPT_VERSION; ?></b><br><br>
        Version Installed:<br>
        <b><?php echo $SETTINGS->softwareVersion; ?></b>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        <i class="fa fa-warning fa-fw"></i> <a href="../docs/info3.html"onclick="window.open(this);return false">Check Upgrade Appendix</a>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        Clean Install? <a href="index.php">Click Here</a>
      </div>
    </div>
    <?php
    }
    }
    ?>

 </div>

 <div class="col-lg-9">

   <?php
   if (isset($_GET['upgrade'])) {
   ?>
   <div class="panel panel-default">
     <div class="panel-body">
       Upgrading..this may take several minutes.....<br><br>
       <i class="fa fa-warning fa-fw"></i> <b>DO NOT REFRESH SCREEN</b>!
     </div>
   </div>

   <div class="panel panel-default">
     <div class="panel-heading upperc"><i class="fa fa-cogs fa-fw"></i> Upgrade Operations</div>
     <div class="panel-body upgradepanel">
       <div class="table-responsive">
          <table class="table table-striped table-hover">
          <tbody>
            <?php
            for ($i = 0; $i < count($ops); $i++) {
              if ($i < 1) {
              ?>
              <tr>
                <td id="op_start_td" class="bold mc_green"><?php echo $ops[$i]; ?></td>
                <td id="op_start" class="text-right"><i class="fa fa-spinner fa-spin fa-fw"></i></td>
              </tr>
              <?php
              } else {
              ?>
              <tr>
                <td id="op_<?php echo $i; ?>_td" class="bold"><?php echo $ops[$i]; ?></td>
                <td id="op_<?php echo $i; ?>" class="text-right">Please wait..</td>
              </tr>
              <?php
              }
            }
            ?>
          </tbody>
          </table>
        </div>
     </div>
   </div>

   <?php
   } else {
   if (SCRIPT_VERSION > $SETTINGS->softwareVersion) {
   ?>

   <div class="panel panel-default">
     <div class="panel-body">
       <i class="fa fa-refresh fa-fw"></i> This upgrade routine will update your database to <b><?php echo SCRIPT_VERSION; ?></b>.<br><br>
       <span style="color:red">Please make sure you have read the upgrade appendix before upgrading to see how your current store may be affected.</span>
     </div>
   </div>

   <div class="panel panel-default">
     <div class="panel-heading upperc"><i class="fa fa-database fa-fw"></i> Database to Upgrade</div>
     <div class="panel-body">
       <div class="table-responsive">
          <table class="table table-striped table-hover">
          <tbody>
            <tr>
              <td class="bold">Database Name</td>
              <td><?php echo mc_safeHTML(DB_NAME); ?></td>
            </tr>
          </tbody>
          </table>
        </div>
     </div>
   </div>

   <div class="nav">
     <button onclick="_confm()" class="btn btn-primary" type="button"><span class="hidden-xs">Upgrade Database</span> <i class="fa fa-chevron-right fa-fw"></i></button>
   </div>

   <?php
   } else {
   ?>
   <div class="panel panel-default">
     <div class="panel-body">
       <i class="fa fa-check fa-fw"></i> Your installation appears to be up to date.
     </div>
   </div>

   <?php
   if (isset($_GET['completed'])){
   ?>
   <div class="panel panel-warning">
     <div class="panel-heading upperc"><i class="fa fa-refresh fa-fw"></i> RE-RUN UPGRADE</div>
     <div class="panel-body">
       If the upgrade didn`t run properly, you can try running it again by doing the following:<br><br>
       <span class="badge">1</span> - Log into your database and access your &quot;<b><?php echo DB_PREFIX; ?>settings</b>&quot; table.<br><br>
       <span class="badge">2</span> - Edit the &quot;<b>softwareVersion</b>&quot; column value and change it back to the previous version you had. If you are unsure, set it to 2.0.<br><br>
       <span class="badge">3</span> - Re-run upgrade routine. Any updates already done will be ignored.<br><br>
       If issues persist, please <a href="mailto:msworld67@gmail.com?subject=<?php echo str_replace(' ','%20',SCRIPT_NAME); ?>%20Upgrade%20Issues">contact me</a>.
     </div>
   </div>
   <?php
   } else {
   ?>
   <div class="panel panel-warning">
     <div class="panel-heading upperc"><i class="fa fa-refresh fa-fw"></i> RE-RUN UPGRADE</div>
     <div class="panel-body">
       If for some reason you would like to run the upgrade operation again, please do the following:<br><br>
       <span class="badge">1</span> - Log into your database and access your &quot;<b><?php echo DB_PREFIX; ?>settings</b>&quot; table.<br><br>
       <span class="badge">2</span> - Edit the &quot;<b>softwareVersion</b>&quot; column value and change it back to the previous version you had. If you are unsure, set it to 2.0.<br><br>
       <span class="badge">3</span> - Re-run upgrade routine. Any updates already done will be ignored.<br><br>
       If you are having some problems, please <a href="mailto:msworld67@gmail.com?subject=<?php echo str_replace(' ','%20',SCRIPT_NAME); ?>%20Upgrade%20Issues">contact me</a>.
     </div>
   </div>
   <?php
   }
   }
   }
   ?>

 </div>

</div>
</div>
