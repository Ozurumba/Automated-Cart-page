<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK) && $cnt>0) {
  echo mc_actionCompleted($msg_searchlog6);
}

$limit     = $page * SEARCH_LOGS_PER_PAGE - (SEARCH_LOGS_PER_PAGE);
$totalLogs = mc_rowCount('search_log', '', false);
$q_s = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
       count(*) AS `sr`,
       ROUND((count(*) / " . $totalLogs . ") * 100, " . STATS_DECIMAL_PLACES . ") AS `perc`
       FROM `" . DB_PREFIX . "search_log`
       ".(isset($_GET['zero']) ? 'WHERE `results` = \'0\'' : '')."
       GROUP BY `keyword`
       ORDER BY `perc` DESC
       LIMIT $limit,".SEARCH_LOGS_PER_PAGE."
       ") or die(mc_MySQLError(__LINE__,__FILE__));
$c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
?>

<div class="fieldHeadWrapper">
  <p><span style="float:right">
    <a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a>
    <?php
    if ($c->rows>0) {
    ?>
    <a href="?p=search-log&amp;export=yes<?php echo (isset($_GET['zero']) ? '&amp;zero=yes' : ''); ?>" title="<?php echo mc_cleanDataEntVars($msg_searchlog3); ?>"><i class="fa fa-save fa-fw"></i></a>
    <?php
    if ($uDel=='yes') {
      echo '<a href="?p=search-log&amp;clear=yes" title="'.mc_cleanDataEntVars($msg_searchlog2).'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>';
    }
    }
    ?>
  </span>
  <?php echo mc_cleanData($msg_javascript108); ?> (<?php echo number_format($c->rows); ?>):</p>
</div>

<?php
if (mysqli_num_rows($q_s)>0) {
?>
<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!=0){location=this.options[this.selectedIndex].value}">
    <option value="?p=search-log"><?php echo $msg_admin3_0[9]; ?></option>
    <option value="?p=search-log&amp;zero=yes"<?php echo (isset($_GET['zero']) ? ' selected="selected"' : ''); ?>><?php echo $msg_searchlog5; ?></option>
  </select>
</div>
<?php

while ($SEARCH = mysqli_fetch_object($q_s)) {
?>
<div class="panel panel-default">
  <div class="panel-body">
    <?php echo mc_safeHTML($SEARCH->keyword); ?>
  </div>
  <div class="panel-footer">
    <?php echo str_replace(array('{count}','{results}'),array(number_format($SEARCH->sr),number_format($SEARCH->results)),$msg_searchlog7); ?> / <b><?php echo $SEARCH->perc; ?>%</b>
  </div>
</div>
<?php
}
define('PER_PAGE',SEARCH_LOGS_PER_PAGE);
if ($c->rows>0 && $c->rows>PER_PAGE) {
  $PGS = new pagination(array($c->rows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
} else {
?>
<span class="noData"><?php echo (isset($_GET['zero']) ? $msg_searchlog11 : $msg_searchlog9); ?></span>
<?php
}
?>
</div>
