<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK) && $cnt>0) {
  echo mc_actionCompleted(str_replace('{count}',@number_format($cnt),$msg_entrylog5));
}

$limit  = $page * LOGS_PER_PAGE - (LOGS_PER_PAGE);
$SQL    = '';
$type   = (isset($_GET['type']) && in_array($_GET['type'],array('admin','personal','trade')) ? $_GET['type'] : 'all');
if (isset($_GET['keys']) && $_GET['keys']) {
  if (strtolower($_GET['keys']) == strtolower(USERNAME)) {
    $SQL = 'WHERE `el`.`userid` = \'0\'';
  } else {
    $SQL = 'WHERE (`acc`.`name` LIKE \'%' . mc_safeSQL($_GET['keys']) . '%\') OR (`usr`.`userName` LIKE \'%' . mc_safeSQL($_GET['keys']) . '%\')';
  }
}
$q_l = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS `el`.*,
       `acc`.`name` AS `accName`,
       `usr`.`userName` AS `usrName`,
       DATE_FORMAT(DATE(`el`.`logdatetime`),'" . $SETTINGS->mysqlDateFormat . "') AS `ldate`,
       TIME(`el`.`logdatetime`) AS `ltime`,
       `el`.`ip` AS `logIP`,
       `el`.`type` AS `logType`
       FROM `" . DB_PREFIX . "entry_log` AS `el`
       LEFT JOIN `" . DB_PREFIX . "accounts` `acc` ON `acc`.`id` = `el`.`userid` AND `el`.`type` IN('personal','trade')
       LEFT JOIN `" . DB_PREFIX . "users` `usr` ON `usr`.`id` = `el`.`userid` AND `el`.`type` IN('admin')
       " . ($SQL == '' && $type != 'all' ? 'WHERE `el`.`type` = \'' . mc_safeSQL($type) . '\'' : ''). "
       $SQL
       ORDER BY `el`.`id` DESC
       LIMIT $limit," . LOGS_PER_PAGE
       ) or die(mc_MySQLError(__LINE__,__FILE__));
$c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows = (isset($c->rows) ? $c->rows : '0');
?>
<div class="fieldHeadWrapper">
  <p>
  <?php
  if ($countedRows > 0) {
  ?>
  <span class="float">
  <a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a>
  <a href="#" onclick="jQuery('#sbox').slideToggle();return false"><i class="fa fa-search fa-fw"></i></a>
  <a href="?p=entry-log&amp;export=<?php echo $type . (isset($_GET['keys']) ? '&amp;keys=' . urlencode(mc_safeHTML($_GET['keys'])) : ''); ?>" title="<?php echo mc_cleanDataEntVars($msg_entrylog4); ?>"><i class="fa fa-save fa-fw"></i></a>
  <?php echo ($uDel=='yes' ? '<a href="?p=entry-log&amp;reset=' . $type . (isset($_GET['keys']) ? '&amp;keys=' . urlencode(mc_safeHTML($_GET['keys'])) : '') . '" title="' . mc_cleanDataEntVars($msg_entrylog3) . '" onclick="return mc_confirmMessage(\'' . mc_filterJS($msg_javascript268) . '\')"><i class="fa fa-times fa-fw mc-red"></i></a> ' : ''); ?>
  </span>
  <?php
  }
  echo mc_cleanData($msg_javascript99); ?> (<?php echo @number_format($c->rows); ?>):</p>
</div>

<form method="get" action="index.php">
<div class="formFieldWrapper" id="sbox" style="display:none">
 <input type="hidden" name="p" value="entry-log">
 <input type="text" name="keys" class="box" value="<?php echo (isset($_GET['keys']) ? mc_safeHTML($_GET['keys']) : ''); ?>"><br>
 <input type="hidden" name="type" value="<?php echo $type; ?>">
 <button type="submit" class="btn btn-primary"><?php echo $msg_entrylog11; ?></button>
</div>
</form>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=entry-log"><?php echo $msg_entrylog2; ?></option>
  <option value="?p=entry-log&amp;type=admin"<?php echo ($type == 'admin' ? ' selected="selected"' : ''); ?>><?php echo $msg_entrylog8; ?></option>
  <option value="?p=entry-log&amp;type=personal"<?php echo ($type == 'personal' ? ' selected="selected"' : ''); ?>><?php echo $msg_entrylog9; ?></option>
  <option value="?p=entry-log&amp;type=trade"<?php echo ($type == 'trade' ? ' selected="selected"' : ''); ?>><?php echo $msg_entrylog10; ?></option>
  </select>
</div>

<?php
if ($countedRows > 0) {
while ($LOG = mysqli_fetch_object($q_l)) {
?>
<div class="panel panel-default">
  <div class="panel-body">
    <?php
    switch($LOG->userid) {
      case '0':
        echo mc_safeHTML(USERNAME);
        break;
      default:
        switch($LOG->logType) {
          case 'admin':
            echo mc_safeHTML($LOG->usrName);
            break;
          default:
            echo mc_safeHTML($LOG->accName);
            break;
        }
        break;
    }
    ?>
  </div>
  <div class="panel-footer">
    <i class="fa fa-clock-o fa-fw"></i> <?php echo $LOG->ldate; ?> @ <?php echo $LOG->ltime; ?>
    <?php
    if ($LOG->logIP) {
    ?>
    / <a href="<?php echo str_replace('{ip}',$LOG->logIP,IP_LOOKUP); ?>" onclick="window.open(this);return false"><?php echo $LOG->logIP; ?></a>
    <?php
    }
    ?>
  </div>
</div>
<?php
}
} else {
?>
<span class="noData"><?php echo $msg_entrylog6; ?></span>
<?php
}

define('PER_PAGE',LOGS_PER_PAGE);
if ($countedRows > 0 && $countedRows > PER_PAGE) {
  $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
?>
</div>
