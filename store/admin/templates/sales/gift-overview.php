<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK) && $cnt>0) {
  echo mc_actionCompleted($msg_giftoverview3);
}

$SQL = 'WHERE `active` = \'yes\'';
if (isset($_GET['filter'])) {
  switch($_GET['filter']) {
    case 'redeemed':
      $SQL = "WHERE `value` != `redeemed` AND `active` = 'yes'";
      break;
    case 'disabled':
      $SQL = "WHERE `enabled` = 'no' AND `active` = 'yes'";
      break;
  }
}
if (isset($_GET['keys'])) {
  $sKeys  = '%'.mc_safeSQL($_GET['keys']).'%';
  $sKeysD = mc_safeSQL($_GET['keys']);
  $SQL   .= ($SQL ? 'AND ' : 'WHERE ')."(`from_name` LIKE '{$sKeys}' OR `to_name` LIKE '{$sKeys}' OR `from_email` LIKE '{$sKeys}' OR `to_email` LIKE '{$sKeys}' OR `code` LIKE '{$sKeys}' OR `notes` LIKE '{$sKeys}' OR `dateAdded` = '{$sKeysD}')";
}
$q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS * FROM `" . DB_PREFIX . "giftcodes` $SQL ORDER BY `id` DESC LIMIT $limit,".PRODUCTS_PER_PAGE)
       or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  =  (isset($c->rows) ? $c->rows : '0');
if (mysqli_num_rows($q_p)>0) {
?>
<div class="fieldHeadWrapper">
  <p><span class="float"><a href="#" onclick="jQuery('#search').slideToggle();return false"><i class="fa fa-search fa-fw"></i></a> <a class="export_product_overview" href="?p=gift-overview&amp;export=<?php echo (isset($_GET['filter']) ? $_GET['filter'] : 'all').(isset($_GET['keys']) ? '&amp;keys='.mc_safeHTML($_GET['keys']) : ''); ?>" title="<?php echo mc_cleanDataEntVars($msg_giftoverview11); ?>"><i class="fa fa-save fa-fw"></i></a></span><?php echo mc_cleanDataEntVars($msg_giftoverview2); ?>:</p>
</div>

<form method="get" action="index.php">
<div class="formFieldWrapper" style="display:none" id="search">
  <input type="hidden" name="p" value="gift-overview">
  <select name="filter">
    <option value="all"><?php echo $msg_giftoverview8; ?></option>
    <option value="redeemed"<?php echo (isset($_GET['filter']) && $_GET['filter']=='redeemed' ? ' selected="selected"' : ''); ?>><?php echo $msg_giftoverview9; ?></option>
    <option value="disabled"<?php echo (isset($_GET['filter']) && $_GET['filter']=='disabled' ? ' selected="selected"' : ''); ?>><?php echo $msg_giftoverview6; ?></option>
  </select>

  <label style="margin-top:10px"><?php echo $msg_giftoverview10; ?>:</label>
  <input type="text" name="keys" value="<?php echo (isset($_GET['keys']) ? mc_safeHTML($_GET['keys']) : ''); ?>" class="box"><br>

  <input type="submit" class="btn btn-primary" value="<?php echo mc_cleanDataEntVars($msg_giftoverview7); ?>" title="<?php echo mc_cleanDataEntVars($msg_giftoverview7); ?>">
</div>
</form>

<?php
while ($GIFT = mysqli_fetch_object($q_p)) {
$finrep        = array(
  '{date}'     => date($SETTINGS->systemDateFormat,strtotime($GIFT->dateAdded)),
  '{value}'    => mc_currencyFormat($GIFT->value),
  '{redeemed}' => mc_currencyFormat($GIFT->redeemed),
  '{enabled}'  => ($GIFT->enabled=='yes' ? $msg_script5 : $msg_script6),
  '{from}'     => mc_safeHTML($GIFT->from_name),
  '{to}'       => mc_safeHTML($GIFT->to_name)
);
?>
<div class="panel panel-default">
  <div class="panel-heading">
    <i class="fa fa-gift fa-fw"></i> <?php echo mc_safeHTML($GIFT->code); ?>
  </div>
  <div class="panel-body">
   <?php echo strtr($msg_giftoverview4,$finrep); ?>
  </div>
  <div class="panel-footer">
   <a href="?p=gift&amp;viewGift=<?php echo $GIFT->code; ?>"><i class="fa fa-pencil fa-fw"></i></a>&nbsp;&nbsp;
   <?php
   if ($uDel=='yes') {
   ?>
   <a href="?p=gift-overview&amp;del=<?php echo $GIFT->id; ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
   <?php
   }
   ?>
  </div>
</div>
<?php
}
define('PER_PAGE',PRODUCTS_PER_PAGE);
if ($countedRows>0 && $countedRows > PER_PAGE) {
  $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
} else {
?>
<div class="fieldHeadWrapper">
  <p><?php echo mc_cleanDataEntVars($msg_giftoverview2); ?>:</p>
</div>
<span class="noData"><?php echo $msg_giftoverview5; ?></span>
<?php
}
?>
</div>
