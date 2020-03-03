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

   <form method="post" action="?s=4" id="form" onsubmit="return _warning()">
   <div class="panel panel-default">
     <div class="panel-body">
       Before the installer adds the database tables and information, please specify your MySQL database version and preferred character set for MySQL operations. If you aren`t sure of this, leave the
       settings as they are.
     </div>
   </div>

   <div class="panel panel-default">
     <div class="panel-heading upperc"><i class="fa fa-database fa-fw"></i> MySQL Version</div>
     <div class="panel-body">
       <div class="radio">
         <label><input type="radio" name="mysql_version" value="MySQL5" <?php echo ((int) $mysqlVer>=5 ? ' checked="checked"' : ''); ?>> MySQL5 (Recommended)</label>
       </div>
       <div class="radio">
         <label><input type="radio" name="mysql_version" value="MySQL4" <?php echo ((int) $mysqlVer<5 ? ' checked="checked"' : ''); ?>> MySQL4</label>
       </div>
     </div>
   </div>

   <div class="panel panel-default">
     <div class="panel-heading upperc"><i class="fa fa-database fa-fw"></i> Character Set</div>
     <div class="panel-body">
       <select name="charset" class="form-control">
       <?php
       if (!empty($cSets)) {
         foreach ($cSets AS $set) {
         ?>
         <option value="<?php echo $set; ?>"<?php echo ($set == $defaultSet ? ' selected="selected"' : ''); ?>><?php echo $set; ?></option>
         <?php
         }
       } else {
         ?>
         <option value="<?php echo $defaultSet; ?>" selected="selected"><?php echo $defaultSet; ?></option>
         <?php
       }
       ?>
      </select>
     </div>
   </div>

   <div class="row">
     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
       <button onclick="window.location='?s=3'" class="btn btn-info" type="button"><i class="fa fa-chevron-left fa-fw"></i><span class="hidden-xs"> Previous</span></button>
     </div>
     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
       <button class="btn btn-primary" type="submit"><span class="hidden-xs">Install Tables </span><i class="fa fa-chevron-right fa-fw"></i></button>
     </div>
     <input type="hidden" name="tables" value="yes">
   </div>
   </form>

 </div>

</div>
</div>
