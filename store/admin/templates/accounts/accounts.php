<?php if (!defined('PARENT')) { die('Permission Denied'); }
// Set filter here..
$sqlFilter  = '';
if (isset($_GET['keys']) && $_GET['keys']) {
  $stm       = mc_safeSQL($_GET['keys']);
  $sqlFilter = "AND `name` LIKE '%" . $stm . "%' OR `email` LIKE '%" . $stm . "%' OR `notes` LIKE '%" . $stm . "%' OR `reason` LIKE '%" . $stm . "%'";
}
$sqlOrder   = '`name`';
if (isset($_GET['orderby'])) {
  switch($_GET['orderby']) {
    case 'name_asc':     $sqlOrder   = '`name`';                      break;
    case 'name_desc':    $sqlOrder   = '`name` DESC';                 break;
    case 'email_asc':    $sqlOrder   = '`email`';                     break;
    case 'email_desc':   $sqlOrder   = '`email` DESC';                break;
    case 'orders_asc':   $sqlOrder   = '`saleCount` DESC';            break;
    case 'orders_desc':  $sqlOrder   = '`saleCount`';                 break;
    case 'revenue_asc':  $sqlOrder   = '`salesRevenue` * 1000 DESC';  break;
    case 'revenue_desc': $sqlOrder   = '`salesRevenue` * 1000';       break;
  }
}
?>
<div id="content">
<script>
//<![CDATA[
function confirmMessage_Add(txt) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    return true;
  } else {
    return false;
  }
}
//]]>
</script>

<?php
if (isset($_GET['deleted'])) {
  echo mc_actionCompleted(str_replace('{count}',(int) $_GET['deleted'],$msg_accounts));
}

$limit   = $page * ACCOUNTS_PER_PAGE - (ACCOUNTS_PER_PAGE);
$query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
         DATE_FORMAT(`created`,'" . $SETTINGS->mysqlDateFormat . "') AS `cdate`,
         (SELECT count(*) FROM `" . DB_PREFIX . "sales`
          WHERE `" . DB_PREFIX . "sales`.`account` = `" . DB_PREFIX . "accounts`.`id`
          AND `" . DB_PREFIX . "sales`.`saleConfirmation` = 'yes'
          AND `" . DB_PREFIX . "sales`.`type` = 'personal'
         ) AS `saleCount`,
         (SELECT SUM(`grandTotal`) FROM `" . DB_PREFIX . "sales`
          WHERE `" . DB_PREFIX . "sales`.`account` = `" . DB_PREFIX . "accounts`.`id`
          AND `" . DB_PREFIX . "sales`.`saleConfirmation` = 'yes'
          AND `" . DB_PREFIX . "sales`.`type` = 'personal'
         ) AS `salesRevenue`
         FROM `" . DB_PREFIX . "accounts`
         WHERE `enabled` = 'yes'
         AND `verified` = 'yes'
         AND `type` = 'personal'
         $sqlFilter
         ORDER BY $sqlOrder
         LIMIT $limit,".ACCOUNTS_PER_PAGE."
         ") or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  = (isset($c->rows) ? $c->rows : '0');
?>

<div class="fieldHeadWrapper">
  <p>
   <span class="float">
    <a href="?p=add-account"><i class="fa fa-plus fa-fw"></i></a>
    <?php
    if ($countedRows > 0) {
    ?>
    <a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a>
    <a href="#" onclick="jQuery('#filters2').slideToggle();return false"><i class="fa fa-search fa-fw"></i></a>
    <a href="?p=accounts&amp;export=<?php echo (isset($_GET['orderby']) ? mc_safeHTML($_GET['orderby']) : 'all') . (isset($_GET['keys']) ? '&amp;keys=' . mc_safeHTML($_GET['keys']) : ''); ?>"><i class="fa fa-save fa-fw"></i></a>
    <?php
    }
    ?>
   </span><?php echo $msg_accounts2; ?> (<?php echo @number_format($countedRows); ?>):
  </p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select name="orderby" onchange="if(this.value!=0){location=this.options[this.selectedIndex].value}">
    <option value="?p=accounts&amp;orderby=name_asc"<?php echo (isset($_GET['orderby']) && $_GET['orderby'] == 'name_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_accounts19; ?></option>
    <option value="?p=accounts&amp;orderby=name_desc"<?php echo (isset($_GET['orderby']) && $_GET['orderby'] == 'name_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_accounts20; ?></option>
    <option value="?p=accounts&amp;orderby=email_asc"<?php echo (isset($_GET['orderby']) && $_GET['orderby'] == 'email_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_accounts21; ?></option>
    <option value="?p=accounts&amp;orderby=email_desc"<?php echo (isset($_GET['orderby']) && $_GET['orderby'] == 'email_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_accounts22; ?></option>
    <option value="?p=accounts&amp;orderby=orders_asc"<?php echo (isset($_GET['orderby']) && $_GET['orderby'] == 'orders_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_accounts23; ?></option>
    <option value="?p=accounts&amp;orderby=orders_desc"<?php echo (isset($_GET['orderby']) && $_GET['orderby'] == 'orders_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_accounts24; ?></option>
    <option value="?p=accounts&amp;orderby=revenue_asc"<?php echo (isset($_GET['orderby']) && $_GET['orderby'] == 'revenue_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_accounts25; ?></option>
    <option value="?p=accounts&amp;orderby=revenue_desc"<?php echo (isset($_GET['orderby']) && $_GET['orderby'] == 'revenue_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_accounts26; ?></option>
  </select>
</div>

<div class="formFieldWrapper" id="filters2" style="display:none">
  <form method="get" action="index.php">
  <p>
  <input type="hidden" name="p" value="accounts"><input type="text" name="keys" class="box" placeholder="<?php echo mc_cleanDataEntVars($msg_accounts28); ?>" value="<?php echo (isset($_GET['keys']) ? mc_safeHTML($_GET['keys']) : ''); ?>">
  <input style="margin-top:10px" type="submit" class="btn btn-primary" value="<?php echo mc_cleanDataEntVars($msg_accounts27); ?>">
  <input style="margin:10px 0 0 20px" type="button" onclick="window.location='?p=accounts'" class="btn btn-success" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
  </p>
  </form>
</div>

<div id="formField">
<form method="post" action="?p=accounts">
<?php
if ($countedRows > 0) {
while ($ACCOUNTS = mysqli_fetch_object($query)) {
?>
<div class="panel panel-default" id="accarea_<?php echo $ACCOUNTS->id; ?>">
  <div class="panel-body">

    <div class="table-responsive hidden-xs">
      <table class="table <?php echo ($uDel == 'yes' ? 'accitemtable' : 'accitemtablenodel'); ?>" style="margin:0;padding:0">
      <tbody>
        <tr>
          <?php
          if ($uDel == 'yes') {
          ?>
          <td><input type="checkbox" name="del[]" onclick="mc_chkCnt('del','counter','button')" value="<?php echo $ACCOUNTS->id; ?>"></td>
          <?php
          }
          ?>
          <td><i class="fa fa-user fa-fw"></i> <?php echo mc_safeHTML($ACCOUNTS->name); ?></td>
          <td><?php echo mc_safeHTML($ACCOUNTS->email); ?></td>
          <td><?php echo str_replace('{count}',@number_format($ACCOUNTS->saleCount),$msg_accounts5); ?></td>
          <td><?php echo mc_currencyFormat(mc_formatPrice($ACCOUNTS->salesRevenue,true)); ?></td>
        </tr>
      </tbody>
      </table>
    </div>

    <div class="hidden-sm hidden-md hidden-lg">
    <?php
    if ($uDel == 'yes') {
    ?>
    <input type="checkbox" name="del[]" onclick="mc_chkCnt('del','counter','button')" value="<?php echo $ACCOUNTS->id; ?>">
    <?php
    }
    ?>
    <i class="fa fa-user fa-fw"></i> <?php echo mc_safeHTML($ACCOUNTS->name); ?><br><br>
    <?php echo mc_safeHTML($ACCOUNTS->email); ?><br>
    <?php echo str_replace('{count}',@number_format($ACCOUNTS->saleCount),$msg_accounts5); ?><br>
    <?php echo mc_currencyFormat(mc_formatPrice($ACCOUNTS->salesRevenue,true)); ?>

    </div>

    <div id="prd_<?php echo $ACCOUNTS->id; ?>" style="display:none">
    <hr>
    <?php echo $msg_accounts11; ?>: <?php echo $ACCOUNTS->cdate; ?><br>
    <?php echo $msg_accounts12; ?>: <a href="?p=accounts&amp;accstatus=<?php echo $ACCOUNTS->id; ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_NOTES_HEIGHT; ?>','<?php echo DIVWIN_NOTES_WIDTH; ?>',this.title);return false;"><?php echo ($ACCOUNTS->enabled == 'yes' ? $msg_accounts13 : $msg_accounts14); ?></a><br>
    <?php echo $msg_accounts15; ?>: <?php echo ($ACCOUNTS->ip ? $ACCOUNTS->ip : 'N/A'); ?>
    <hr>
    <a href="?p=accounts&amp;message=<?php echo $ACCOUNTS->id; ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_NOTES_HEIGHT; ?>','<?php echo DIVWIN_NOTES_WIDTH; ?>',this.title);return false;"><i class="fa fa-bullhorn fa-fw"></i> <?php echo $msg_accounts29; ?></a>
    </div>
  </div>
  <div class="panel-footer">
    <a href="?p=add-account&amp;edit=<?php echo $ACCOUNTS->id; ?>" title="<?php echo mc_safeHTML($msg_accounts6); ?>"><i class="fa fa-pencil fa-fw"></i></a>
    <a href="?p=sales&amp;ahis=<?php echo $ACCOUNTS->id; ?>" title="<?php echo mc_safeHTML($msg_accounts7); ?>"><i class="fa fa-shopping-basket fa-fw"></i></a>
    <a href="?p=accounts&amp;notes=<?php echo $ACCOUNTS->id; ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_NOTES_HEIGHT; ?>','<?php echo DIVWIN_NOTES_WIDTH; ?>',this.title);return false;" title="<?php echo mc_safeHTML($msg_accounts8); ?>"><i class="fa fa-file-text<?php echo ($ACCOUNTS->notes == null || $ACCOUNTS->notes=='' ? '-o' : ''); ?> fa-fw"></i></a>
    &nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-down fa-fw" style="cursor:pointer" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[10]); ?>" onclick="mc_toggleMoreOptions(this,'<?php echo $ACCOUNTS->id; ?>')"></i>
  </div>
</div>
<?php
}

if ($uDel == 'yes') {
?>
<p style="padding:10px 0 10px 10px">
  <input type="checkbox" name="all" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'formField');mc_chkCntDiv('del','counter','button')">&nbsp;&nbsp;&nbsp;
  <button type="submit" onclick="return confirmMessage_Add('<?php echo mc_filterJS($msg_javascript45); ?>')" name="delacc" disabled="disabled" class="btn btn-danger" id="button"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_accounts3); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-times fa-fw"></i></span> (<span class="counter">0</span>)</button>
</p>
<?php
}
?>

</form>
</div>
<?php
define('PER_PAGE', ACCOUNTS_PER_PAGE);
if ($countedRows>0 && $countedRows>PER_PAGE) {
  $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
} else {
?>
<p class="noData"><?php echo $msg_accounts4; ?></p>
</form>
</div>
<?php
}
?>

</div>
