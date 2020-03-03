<?php if (!defined('PARENT')) { die('Permission Denied'); }
$ORDER = mc_getTableData('sales', 'id', mc_digitSan($_GET['sale']));
if (!isset($ORDER->id)) {
  die('Order ID invalid');
}
if ($ORDER->wishlist > 0) {
  $W_AC = mc_getTableData('accounts','id', $ORDER->wishlist);
  if (isset($W_AC->id)) {
    $ORDER->bill_1 = $W_AC->name;
    $ORDER->bill_2 = $W_AC->email;
    $ORDER->account = $W_AC->id;
    define('MC_IS_WISH', 1);
  }
}
?>
<div id="content">
<script>
//<![CDATA[
function mc_checkform() {
  var message = '';
  if (jQuery('#text').val()=='' || jQuery('#title').val()=='') {
    message = '- <?php echo mc_cleanDataEntVars($msg_javascript156); ?>';
  }
  if (message) {
    mc_alertBox(message);
    if (jQuery('#title').val()=='') {
      jQuery('#title').focus();
    } else {
      jQuery('#text').focus();
    }
    return false;
  } else {
    return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>');
  }
}
jQuery(document).ready(function() {
  jQuery('input[name="search_statuses"]').autocomplete({
	  source: 'index.php?p=sales-update&search=yes',
		minLength: 3,
		select: function(event, ui) {
      jQuery('#statusesShow').hide();
      if (ui.item.value > 0) {
        mc_ShowSpinner();
        mc_statusOption(ui.item.label, ui.item.text);
      }
		}
  });
});
//]]>
</script>

<?php
if (isset($_GET['newacc'])) {
  echo mc_actionCompleted($msg_add_sale[5]);
}
if (isset($OK)) {
  echo mc_actionCompleted($msg_salesupdate17);
}
if (isset($DEL) && $cnt>0) {
  echo mc_actionCompleted($msg_salesupdate18);
}
$payStatuses = mc_loadDefaultStatuses();
$find        = array('{NAME}','{ORDER}','{WEBSITE_NAME}','{WEBSITE_URL}');
$replace     = array(
  mc_cleanData($ORDER->bill_1),
  mc_saleInvoiceNumber(mc_digitSan($ORDER->invoiceNo), $SETTINGS),
  mc_cleanData($SETTINGS->website),
  $SETTINGS->ifolder
);
?>

<div class="alert alert-info">
  <?php
  $qLinksArr  = array('xxx');
  $qLinksIcon = 'cube';
  $saleID     = (int) $_GET['sale'];
  include(PATH . 'templates/sales/sales-quick-links.php');
  ?>
</div>

<div id="form_field">
<form method="post" id="form" action="?p=sales-update&amp;sale=<?php echo mc_digitSan($_GET['sale']); ?>" enctype="multipart/form-data" onsubmit="return mc_checkform()">
<div class="fieldHeadWrapper" id="mswhead">
  <p>
  <span class="float">
   <a href="#" onclick="if (jQuery('#text').val()!=''){mc_addNewStatus('<?php echo mc_filterJS($msg_javascript318); ?>');return false}else{mc_alertBox('<?php echo mc_filterJS($msg_javascript324); ?>');jQuery('#text').focus();return false}"><i class="fa fa-save fa-fw" title="<?php echo mc_cleanDataEntVars($msg_salesupdate23); ?>"></i></a>
   <a href="#" onclick="jQuery('#statusesShow').slideToggle();return false"><i class="fa fa-search fa-fw" title="<?php echo mc_cleanDataEntVars($msg_salesupdate24); ?>"></i></a>
   <a href="?p=sales-statuses&amp;sale=<?php echo mc_digitSan($_GET['sale']); ?>" title="<?php echo mc_cleanDataEntVars($msg_salesupdate25); ?>"><i class="fa fa-pencil fa-fw" title="<?php echo mc_cleanDataEntVars($msg_salesupdate25); ?>"></i></a>
  </span>
  <?php echo $msg_salesupdate2; ?>
  </p>
</div>

<div class="formFieldWrapper" style="display:none" id="statusesShow">
  <input type="text" class="box" name="search_statuses" value="" placeholder="<?php echo mc_cleanDataEntVars($msg_order_status_update[1]); ?>">
</div>

<?php
if (defined('MC_IS_WISH')) {
?>
<div class="alert alert-warning alert-dismissable" style="margin-bottom:10px">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <i class="fa fa-heart fa-fw"></i> <?php echo $msg_sales_update[0]; ?>
</div>
<?php
}
?>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_admin3_0[25]; ?></label>
    <input class="box" type="text" id="title" name="title" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo str_replace(array('{website}','{order}'),array(mc_cleanData($SETTINGS->website),mc_saleInvoiceNumber(mc_digitSan($ORDER->invoiceNo), $SETTINGS)),mc_cleanDataEntVars($msg_salesupdate19)); ?>">

    <label style="margin-top:10px"><?php echo $msg_admin3_0[26]; ?></label>
    <textarea rows="5" tabindex="<?php echo (++$tabIndex); ?>" cols="30" id="text" name="text"><?php echo mc_safeHTML(str_replace($find,$replace,file_get_contents(MCLANG_REL.'email-templates/admin/order-updated.txt'))); ?></textarea>
    <span id="helpBlock" class="help-block"><?php echo $msg_salesupdate42; ?>: {ORDER} <?php echo mc_cleanDataEntVars($msg_salesupdate41); ?>, {DOWNLOADS} <?php echo mc_cleanDataEntVars($msg_salesupdate40); ?></span>

    <label style="margin-top:10px"><?php echo str_replace('{buyer}',mc_cleanData($ORDER->bill_1),$msg_salesupdate11); ?></label>
    <?php echo $msg_script5; ?> <input onclick="if(this.checked){jQuery('#copym').slideDown()}" tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="email" value="yes" checked="checked">
    <?php echo $msg_script6; ?> <input onclick="if(this.checked){jQuery('#copym').slideUp()}" tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="email" value="no">

    <div id="copym">
      <label style="margin-top:10px"><?php echo $msg_salesupdate12; ?></label>
      <input type="text" tabindex="<?php echo (++$tabIndex); ?>" class="box" name="copy_email" value="<?php echo mc_cleanData($ORDER->orderCopyEmails); ?>">
    </div>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft" id="addimgboxes">
    <label><?php echo $msg_salesupdate4; ?>: <?php echo mc_displayHelpTip($msg_javascript130,'RIGHT'); ?></label>
    <?php
    if ($SETTINGS->smtp=='yes') {
    ?>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="file" name="attachment[]">
    <div style="margin-top:10px">
      <button type="button" class="btn btn-primary" onclick="mc_AttBox('add','attachment')"><i class="fa fa-plus fa-fw"></i></button>
      <button type="button" class="btn btn-success" onclick="mc_AttBox('minus','attachment')"><i class="fa fa-minus fa-fw"></i></button>
    </div>
    <?php
    } else {
    echo $msg_salesupdate20;
    }
    ?>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_salesupdate9; ?>: <?php echo mc_displayHelpTip($msg_javascript132); ?></label>
    <?php echo $msg_script5; ?> <input onclick="if(this.checked){jQuery('#savetofolder').slideDown()}" tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="save" value="yes"<?php echo (SAVE_ATTACHMENTS_TO_SERVER=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input onclick="if(this.checked){jQuery('#savetofolder').slideUp()}" tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="save" value="no"<?php echo (SAVE_ATTACHMENTS_TO_SERVER=='no' || !in_array(SAVE_ATTACHMENTS_TO_SERVER,array('yes','no')) ? ' checked="checked"' : ''); ?>>

    <div id="savetofolder"<?php echo (SAVE_ATTACHMENTS_TO_SERVER=='no' ? ' style="display:none"' : ''); ?>>
    <label style="margin-top:10px"><?php echo $msg_salesupdate10; ?>: <?php echo mc_displayHelpTip($msg_javascript133,'LEFT'); ?></label>
    <select name="folder" id="folderList" tabindex="<?php echo (++$tabIndex); ?>">
      <option value="<?php echo ATTACH_FOLDER; ?>"><?php echo $msg_salesupdate14; ?></option>
      <?php
      if (is_dir(PATH.ATTACH_FOLDER)) {
        $dir = opendir(PATH.ATTACH_FOLDER);
        while (false!==($read=readdir($dir))) {
          if (!in_array($read,array('.','..')) && is_dir(PATH.ATTACH_FOLDER.'/'.$read)) {
          ?>
          <option value="<?php echo $read; ?>"><?php echo ATTACH_FOLDER; ?>/<?php echo $read; ?></option>
          <?php
          }
        }
        closedir($dir);
      }
      ?>
    </select><br>
    <button class="btn btn-default" type="button" onclick="mc_createAttachmentFolder('<?php echo mc_filterJS($msg_javascript152); ?>', '<?php echo ATTACH_FOLDER; ?>')"><i class="fa fa-folder fa-fw"></i></button>
    </div>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_salesupdate5; ?>: <?php echo mc_displayHelpTip($msg_javascript131,'RIGHT'); ?></label>
    <select name="status" id="status" tabindex="<?php echo (++$tabIndex); ?>">
    <?php
    if ($ORDER->saleConfirmation=='no') {
    ?>
    <option value=""<?php echo (isset($lastStatus->orderStatus) && $lastStatus->orderStatus=='' ? ' selected="selected"' : ''); ?>>N/A</option>
    <?php
    // Get last status..
    }
    $lastStatus = mc_getTableData('statuses','saleID',mc_digitSan($_GET['sale']),'ORDER BY id DESC','orderStatus');
    foreach ($payStatuses AS $key => $value) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($lastStatus->orderStatus) && $lastStatus->orderStatus==$key ? ' selected="selected"' : ''); ?>><?php echo $value; ?></option>
    <?php
    }
    // Get additional payment statuses..
    $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                   WHERE `pMethod` IN('all','".$ORDER->paymentMethod."')
                   ORDER BY `pMethod`,`statname`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_add_stats)>0) {
    ?>
    <option value="0" disabled="disabled">- - - - - - - - -</option>
    <?php
    }
    while ($ST = mysqli_fetch_object($q_add_stats)) {
    ?>
    <option value="<?php echo $ST->id; ?>"<?php echo (isset($lastStatus->orderStatus) && $lastStatus->orderStatus==$ST->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($ST->statname); ?></option>
    <?php
    }
    ?>
    </select>

    <label style="margin-top:10px"><?php echo $msg_order_status_update[0]; ?>: <?php echo mc_displayHelpTip($msg_javascript131,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="visacc" value="yes"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="visacc" value="no" checked="checked">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="1">
 <input type="hidden" name="vaccount" value="<?php echo $ORDER->account; ?>">
 <input type="hidden" name="invoice" value="<?php echo mc_digitSan($_GET['sale']); ?>">
 <input type="hidden" name="invoiceNo" value="<?php echo $ORDER->invoiceNo; ?>">
 <input type="hidden" name="buyCode" value="<?php echo $ORDER->buyCode; ?>">
 <input type="hidden" name="orderDownloads" value="<?php echo (mc_rowCount('purchases WHERE saleID = \''.mc_digitSan($_GET['sale']).'\' AND productType = \'download\'')>0 ? 'yes' : 'no'); ?>">
 <input type="hidden" name="buyer" value="<?php echo mc_cleanData($ORDER->bill_1); ?>">
 <input type="hidden" name="bill_2" value="<?php echo $ORDER->bill_2; ?>">
 <input type="hidden" name="saleConfirm" value="<?php echo $ORDER->saleConfirmation; ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_salesupdate6); ?>" title="<?php echo mc_cleanDataEntVars($msg_salesupdate6); ?>">
</p>
</form>
</div>
<?php
$q_stat = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
          `" . DB_PREFIX . "statuses`.`id` AS `statID`,
          DATE_FORMAT(`dateAdded`,'" . $SETTINGS->mysqlDateFormat . "') AS `adate`
          FROM `" . DB_PREFIX . "statuses`
          LEFT JOIN `" . DB_PREFIX . "accounts`
          ON `" . DB_PREFIX . "statuses`.`account` = `" . DB_PREFIX . "accounts`.`id`
          WHERE `saleID` = '" . mc_digitSan($_GET['sale']) . "'
          ORDER BY `" . DB_PREFIX . "statuses`.`id` DESC
          ") or die(mc_MySQLError(__LINE__,__FILE__));
?>
<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><?php echo (mysqli_num_rows($q_stat)>0 ? '<span class="float"><a href="?p=sales-update&amp;print='.mc_digitSan($_GET['sale']).'" title="'.mc_cleanDataEntVars($msg_salesupdate22).'" onclick="window.open(this);return false"><i class="fa fa-print fa-fw"></i></a></span>' : ''); ?><?php echo $msg_salesupdate3; ?> (<?php echo mysqli_num_rows($q_stat); ?>):</p>
</div>
<?php
if (mysqli_num_rows($q_stat)>0) {
  while ($STATUS = mysqli_fetch_object($q_stat)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <?php echo mc_NL2BR(mc_cleanCustomTags(mc_safeHTML($STATUS->statusNotes), $mc_mailHTMLTags)); ?>
      <hr>
      <p style="text-align:right"><?php echo mc_statusText($STATUS->orderStatus); ?><br><br>
      <b><i class="fa fa-users fa-fw"></i> <?php echo mc_cleanData($STATUS->adminUser) . '</b> (' .$STATUS->adate; ?> @ <?php echo $STATUS->timeAdded; ?> -&gt; <?php echo mc_safeHTML(($ORDER->account > 0 ? $STATUS->name : $ORDER->bill_1)); ?>)</p>
      <?php
      // Attachments..
      $q_attach = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attachments`
                  WHERE `saleID`  = '" . mc_digitSan($_GET['sale']) . "'
                  AND `statusID`  = '{$STATUS->statID}'
                  ORDER BY `id`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
      if (mysqli_num_rows($q_attach)>0) {
        ?>
        <hr>
        <?php
        while ($ATTACHMENT = mysqli_fetch_object($q_attach)) {
        ?>
        <div><a class="attachment_file" <?php echo ($ATTACHMENT->isSaved=='yes' ? 'href="'.$ATTACHMENT->attachFolder.'/'.$ATTACHMENT->fileName : 'href="#" onclick="javascript:mc_alertBox(\''.mc_filterJS($msg_javascript155).'\');return false'); ?>" title="<?php echo mc_safeHTML($ATTACHMENT->fileName); ?>"><i class="fa fa-paperclip fa-fw"></i> <?php echo ($ATTACHMENT->isSaved=='yes' ? $ATTACHMENT->attachFolder.'/'.$ATTACHMENT->fileName : $ATTACHMENT->fileName); ?></a> <?php echo ($ATTACHMENT->fileSize>0 ? '('.mc_fileSizeConversion($ATTACHMENT->fileSize).')' : ''); ?></div>
        <?php
        }
      }
      ?>
    </div>
    <div class="panel-footer">
      <a href="?p=sales-update&amp;statnotes=<?php echo $STATUS->statID; ?>" title="<?php echo mc_cleanDataEntVars($msg_salesupdate33); ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_FIELD_INFO_HEIGHT; ?>','<?php echo DIVWIN_FIELD_INFO_WIDTH; ?>',this.title); return false"><i class="fa fa-pencil fa-fw"></i></a>
      <?php
      if ($uDel=='yes') {
      ?>
      <a href="?p=sales-update&amp;sale=<?php echo mc_digitSan($_GET['sale']); ?>&amp;delete=<?php echo $STATUS->statID; ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
      <?php
      }
      ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<p class="noData"><?php echo $msg_salesupdate15; ?></p>
<?php
}
?>

</div>
