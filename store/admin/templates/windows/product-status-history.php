<?php if (!defined('PARENT') || !isset($_GET['status_view'])) { die('Permission Denied'); }
$q_stat = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`dateAdded`,'" . $SETTINGS->mysqlDateFormat . "') AS `adate`
          FROM `" . DB_PREFIX . "statuses`
          WHERE `saleID` = '".mc_digitSan($_GET['status_view'])."'
          ORDER BY `id` DESC
          ") or die(mc_MySQLError(__LINE__,__FILE__));
define('WINPARENT', 1);
?>

<div>

<div class="panel panel-default">
  <div class="panel-heading" style="text-transform: uppercase">
    <i class="fa fa-history fa-fw"></i> <?php echo $msg_admin_viewsale3_0[22]; ?>
  </div>
  <div class="panel-body">
    <?php echo str_replace(array('{count}','{sale}'),array(@number_format(mysqli_num_rows($q_stat)),mc_digitSan($_GET['status_view'])),$msg_admin_viewsale3_0[23]); ?>
  </div>
</div>

<?php
if (mysqli_num_rows($q_stat)>0) {
  while ($STATUS = mysqli_fetch_object($q_stat)) {
    ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <?php echo mc_NL2BR(str_replace('&lt;br&gt;','<br>',mc_safeHTML($STATUS->statusNotes))); ?>
      </div>
      <div class="panel-footer">
        <?php echo mc_statusText($STATUS->orderStatus); ?> <i class="fa fa-clock-o fa-fw"></i> <?php echo $STATUS->adate; ?> @ <?php echo $STATUS->timeAdded; ?>
      </div>
    </div>
    <?php
  }
}
?>
</div>