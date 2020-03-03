<?php if (!defined('PARENT')) { die('Permission Denied'); }
$sh_arr = array(
  'flatrate' => $msg_javascript438,
  'itemrate' => $msg_javascript577,
  'percent' => $msg_javascript439,
  'qtyrates' => $msg_admin3_0[55],
  'rates' => $msg_javascript33
);
$darr     = array('flatrate' => 'yes','itemrate' => 'yes','percent' => 'yes','qtyrates' => 'yes','rates' => 'yes');
$shipOpts = ($SETTINGS->shipopts ? unserialize($SETTINGS->shipopts) : $darr);
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_shipmanage3);
  // RELOAD SETTINGS..
  $SETTINGS = @mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"));
  $shipOpts = ($SETTINGS->shipopts ? unserialize($SETTINGS->shipopts) : $darr);
}
?>

<form method="post" id="form" action="?p=shipping">

<div class="row" style="margin-bottom:20px">

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#one" data-toggle="tab"><?php echo $msg_shipmanage; ?></a></li>
      <?php
      $lp = 0;
      if (!empty($shipOpts)) {
      ?>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench fa-fw"></i> <?php echo $msg_shipmanage2; ?> <span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-right" role="menu">
          <?php
          foreach ($shipOpts AS $k => $v) {
          ?>
          <li<?php echo ($v == 'no' ? ' class="disabled"' : ''); ?>><a href="?p=<?php echo $k; ?>"><?php echo '(' . (++$lp) . ') ' . $sh_arr[$k]; ?></a></li>
          <?php
          }
          ?>
        </ul>
      </li>
      <?php
      }
      ?>
    </ul>
  </div>

</div>

<?php
if (!empty($shipOpts)) {
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery("#sortable").sortable({
    update : function (data) {}
  });
});
//]]>
</script>
<?php
}
?>
<div id="sortable">
<?php
foreach ($shipOpts AS $k => $v) {
if (isset($msg_shipmanage6[$k])) {
?>
<div class="panel panel-default" style="cursor:move" title="<?php echo mc_cleanDataEntVars($msg_shipmanage7); ?>">
  <div class="panel-body">
    <b><?php echo $sh_arr[$k]; ?></b><br><br>
    <?php echo $msg_shipmanage6[$k]; ?>
  </div>
  <div class="panel-footer">
    <div class="checkbox" style="margin:0;padding:0">
      <label><input type="hidden" name="sp[]" value="<?php echo $k; ?>"><input type="checkbox" name="ship[<?php echo $k; ?>]" value="yes"<?php echo ($v == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_shipmanage5; ?></label>
    </div>
  </div>
</div>
<?php
}
}
?>
</div>

<p style="text-align:center;padding-top:20px">
  <input type="hidden" name="update" value="yes">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML($msg_shipmanage4); ?>" title="<?php echo mc_safeHTML($msg_shipmanage4); ?>">
</p>

</form>

</div>
