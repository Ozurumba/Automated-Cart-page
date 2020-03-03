<?php if (!defined('PARENT')) { die('Permission Denied'); }
$q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `c` FROM `" . DB_PREFIX . "accounts`
     WHERE `enabled` = 'yes'
     AND `type`     IN('personal','trade')
     ") or die(mc_MySQLError(__LINE__,__FILE__));
$aCount = mysqli_fetch_object($q);
$q2 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `c` FROM `" . DB_PREFIX . "accounts`
      WHERE `enabled` = 'yes'
      AND `type`     IN('personal')
      ") or die(mc_MySQLError(__LINE__,__FILE__));
$aCount2 = mysqli_fetch_object($q2);
$q3 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `c` FROM `" . DB_PREFIX . "accounts`
      WHERE `enabled` = 'yes'
      AND `type`     IN('trade')
      ") or die(mc_MySQLError(__LINE__,__FILE__));
$aCount3 = mysqli_fetch_object($q3);
?>
<div id="content">

<script>
//<![CDATA[
function mc_mailSend() {
  if (jQuery('input[name="subject"]').val() == '') {
    jQuery('input[name="subject"]').focus();
    return false;
  }
  if (jQuery('textarea[name="msg"]').val() == '') {
    jQuery('textarea[name="msg"]').focus();
    return false;
  }
  var confirmSub = confirm('<?php echo mc_filterJS($msg_javascript45); ?>');
  if (confirmSub) {
    jQuery(document).ready(function() {
      mc_ShowSpinner();
      jQuery.ajax({
        type: 'POST',
        url: 'index.php?p=mail-accounts',
        data: jQuery("#content > form").serialize(),
        cache: false,
        dataType: 'json',
        success: function (data) {
          mc_CloseSpinner();
          mc_ScrollToArea('mshtmlwrapper',0,0);
          setTimeout(function() {
            jQuery('div[class="alert alert-warning alert-dismissable"] span').html(data[0]);
            jQuery('div[class="alert alert-warning alert-dismissable"]').slideDown();
          }, 1500);
        }
      });
    });
    return false;
  }
  return false;
}
//]]>
</script>

<div class="alert alert-warning alert-dismissable" style="display:none">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <i class="fa fa-check fa-fw"></i> <span></span>
</div>

<form method="post" action="#">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_mailaccnts; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_mailaccnts2; ?>: <?php echo mc_displayHelpTip($msg_javascript270); ?></label>
    <input<?php echo (!isset($aCount->c) || $aCount->c == 0 ? ' disabled="disabled" ' : ' '); ?>tabindex="<?php echo (++$tabIndex); ?>" type="text" name="subject" value="" class="box">

    <label style="margin-top:10px"><?php echo $msg_mailaccnts3; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <textarea<?php echo (!isset($aCount->c) || $aCount->c == 0 ? ' disabled="disabled" ' : ' '); ?>tabindex="<?php echo (++$tabIndex); ?>" name="msg" rows="5" cols="20"></textarea>
    <span class="help-block" style="text-align:left !important"><?php echo $msg_mailaccnts5; ?></span>

    <label><?php echo $msg_mailaccnts7; ?></label>
    <select name="type">
      <option value="all"><?php echo $msg_mailaccnts8; ?> (<?php echo @number_format($aCount->c); ?>)</option>
      <option value="personal"><?php echo $msg_mailaccnts9; ?> (<?php echo @number_format($aCount2->c); ?>)</option>
      <option value="trade"><?php echo $msg_mailaccnts10; ?> (<?php echo @number_format($aCount3->c); ?>)</option>
    </select>

  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input class="btn btn-primary"<?php echo (!isset($aCount->c) || $aCount->c == 0 ? ' disabled="disabled" ' : ' '); ?>onclick="mc_mailSend()" type="button" value="<?php echo mc_safeHTML($msg_mailaccnts4); ?>" title="<?php echo mc_safeHTML($msg_mailaccnts4); ?>">
</p>
</form>

</div>
