<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
  <div id="content">

    <div class="row" style="margin-bottom:220px">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin_top_10">
        <div class="panel panel-default">
          <div class="panel-body vcheckarea">

            <div class="text-center">
              <img src="templates/images/doing-something.gif" alt="" title="">
              <span><?php echo $msg_admin3_0[36]; ?></span>
            </div>

          </div>
          <div class="panel-footer">
           <button class="btn btn-link" type="button" onclick="window.location = 'index.php'"><i class="fa fa-times fa-fw"></i> <span class="hidden-xs hidden-sm"><?php echo $msg_script11; ?></span></button>
          </div>
        </div>

      </div>
    </div>

  </div>

  <script>
  //<![CDATA[
  jQuery(document).ready(function() {
    setTimeout(function() {
      mc_VersionCheck();
    },3000);
  });
  //]]>
  </script>
