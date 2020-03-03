<?php if (!defined('PARENT') || !isset($_GET['ch'])) { die('Permission Denied'); } ?>

<div id="windowcontent">

<?php
$q_click = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`clickDate`,'" . $SETTINGS->mysqlDateFormat . "') AS `cDate`
           FROM `" . DB_PREFIX . "click_history`
           WHERE `purchaseID` = '".mc_digitSan($_GET['ch'])."'
           ORDER BY `clickDate` DESC,`clickTime` DESC
           ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_click)>0) {
while ($C = mysqli_fetch_object($q_click)) {
?>
<div class="panel panel-default">
  <div class="panel-body">
    <?php echo str_replace(array('{date}','{time}'),array($C->cDate,$C->clickTime),$msg_viewsale72).($C->clickIP ? ' (<a href="'.str_replace('{ip}',$C->clickIP,IP_LOOKUP).'" onclick="window.open(this);return false">' . $C->clickIP . '</a>)' : ''); ?>
  </div>
</div>
<?php
}
} else {
?>
<span class="noData"><?php echo $msg_viewsale71; ?></span>
<?php
}
?>

</div>

