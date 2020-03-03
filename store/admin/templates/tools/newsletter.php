<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
$SQL  = '';
$appd = 'all';
if (isset($_GET['type']) && in_array($_GET['type'],array('personal','trade'))) {
  $SQL  = 'AND `type` = \'' . mc_safeSQL($_GET['type']) . '\'';
  $appd = $_GET['type'];
}

$limit  = $page * EMAILS_PER_PAGE - (EMAILS_PER_PAGE);
$q_l = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS * FROM `" . DB_PREFIX . "accounts`
       WHERE `enabled` = 'yes'
       AND `verified` = 'yes'
       AND `newsletter` = 'yes'
       $SQL
       ORDER BY `name`,`email`
       LIMIT $limit,".EMAILS_PER_PAGE."
       ") or die(mc_MySQLError(__LINE__,__FILE__));
$c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
?>
<div class="fieldHeadWrapper">
  <p>
  <span style="float:right">
    <a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a>
    <a href="?p=newsletter-mail&amp;type=<?php echo $appd; ?>" title="<?php echo mc_cleanDataEntVars($msg_newsletter11); ?>"><i class="fa fa-envelope fa-fw"></i></a>
    <a href="?p=newsletter&amp;export=yes&amp;type=<?php echo $appd; ?>" title="<?php echo mc_cleanDataEntVars($msg_newsletter2); ?>"><i class="fa fa-save fa-fw"></i></a>
  </span>
  <?php echo $msg_newsletter4; ?> (<?php echo @number_format($c->rows); ?>):</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select name="type" onchange="if(this.value!=0){location=this.options[this.selectedIndex].value}">
    <option value="?p=newsletter&amp;type=all"><?php echo $msg_news_letter[1]; ?> (<?php echo mc_rowCount('accounts',' WHERE `enabled` = \'yes\' AND `verified` = \'yes\' AND `newsletter` = \'yes\''); ?>)</option>
    <option value="?p=newsletter&amp;type=personal"<?php echo (isset($_GET['type']) && $_GET['type'] == 'personal' ? ' selected="selected"' : ''); ?>><?php echo $msg_news_letter[2]; ?> (<?php echo mc_rowCount('accounts',' WHERE `enabled` = \'yes\' AND `verified` = \'yes\' AND `newsletter` = \'yes\' AND `type` = \'personal\''); ?>)</option>
    <option value="?p=newsletter&amp;type=trade"<?php echo (isset($_GET['type']) && $_GET['type'] == 'trade' ? ' selected="selected"' : ''); ?>><?php echo $msg_news_letter[3]; ?> (<?php echo mc_rowCount('accounts',' WHERE `enabled` = \'yes\' AND `verified` = \'yes\' AND `newsletter` = \'yes\' AND `type` = \'trade\''); ?>)</option>
  </select>
</div>

<?php
if (mysqli_num_rows($q_l)>0) {
while ($ACCNT = mysqli_fetch_object($q_l)) {
?>
<div class="panel panel-default" id="acc_<?php echo $ACCNT->id; ?>">
  <div class="panel-body">
    <i class="fa fa-user fa-fw"></i> <?php echo mc_safeHTML($ACCNT->name); ?><br>
    <?php echo mc_safeHTML($ACCNT->email); ?>
  </div>
  <div class="panel-footer">
    <a href="?p=add-account&amp;edit=<?php echo $ACCNT->id; ?>" title="<?php echo mc_safeHTML($msg_script9); ?>"><i class="fa fa-pencil fa-fw"></i></a>
    <?php
    if ($uDel=='yes') {
    ?>
    <a href="#" onclick="mc_newsReset('<?php echo mc_filterJS($msg_news_letter[0]); ?>','<?php echo $ACCNT->id; ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
    <?php
    }
    ?>
  </div>
</div>
<?php
}
define('PER_PAGE',EMAILS_PER_PAGE);
if ($c->rows>0 && $c->rows>PER_PAGE) {
  $PGS = new pagination(array($c->rows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
} else {
?>
<span class="noData"><?php echo $msg_newsletter5; ?></span>
<?php
}
?>
</div>
