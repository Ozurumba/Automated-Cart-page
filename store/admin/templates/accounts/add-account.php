<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT       = mc_getTableData('accounts', 'id', mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
  $addrFields = $MCACC->addresses($EDIT->id);
}
?>
<div id="content">
<script>
//<![CDATA[
function mc_genAccKey() {
  jQuery('input[name="pass"]').css('background', '#fff url(templates/images/loading-box.gif) no-repeat 99% 50%');
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=add-account&passgen=yes',
      dataType: 'json',
      success: function (data) {
        jQuery('input[name="pass"]').css('background-image', 'none');
        jQuery('input[name="pass"]').val(data[0]);
        jQuery('#kgen').html(data[0]);
      }
    });
  });
  return false;
}
function mc_setAccType(valu) {
  switch(valu) {
    case 'personal':
      jQuery('#tradeopts').hide();
      break;
    case 'trade':
      jQuery('#tradeopts').slideDown();
      break;
  }
}
jQuery(document).ready(function() {
  mc_stateLoader(
    jQuery('select[name="bill[country]"]').val(),
    jQuery('select[name="ship[country]"]').val(),
    '<?php echo (isset($EDIT->id) ? $EDIT->id : '0'); ?>'
  );
});
//]]>
</script>

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_addccts);
  $_POST['pass'] = '';
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_addccts2);
  $_POST['pass'] = '';
}
// Add errors..
if (isset($AER)) {
  echo mc_actionCompletedError($AER);
  $EDIT = new stdclass();
  foreach (array('name','email','enablelog','notes') AS $a_key) {
    $EDIT->{$a_key} = $_POST[$a_key];
  }
}
?>
<form method="post" id="addform" action="?p=add-account<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_addccts4 : $msg_addccts3); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_addccts5; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="name" value="<?php echo (isset($EDIT->name) ? mc_safeHTML($EDIT->name) : ''); ?>" class="box" maxlength="200">

    <label style="margin-top:10px"><?php echo $msg_addccts6; ?>: <?php echo mc_displayHelpTip($msg_javascript270); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="email" value="<?php echo (isset($EDIT->email) ? mc_safeHTML($EDIT->email) : ''); ?>" class="box" maxlength="250">

    <label style="margin-top:10px"><?php echo $msg_addccts7; ?>:<span id="kgen"></span> <?php echo mc_displayHelpTip($msg_javascript271,'LEFT'); ?></label>
    <div class="form-group input-group">
      <span class="input-group-addon"><a href="#" onclick="mc_genAccKey();return false" title="<?php echo mc_safeHTML($msg_addccts14); ?>"><i class="fa fa-key fa-fw"></i></a></span>
      <input tabindex="<?php echo (++$tabIndex); ?>" onkeyup="jQuery('#kgen').html('')" type="password" name="pass" value="<?php echo (isset($_POST['pass']) ? mc_safeHTML($_POST['pass']) : ''); ?>" class="box addon-no-radius">
    </div>

    <label style="margin-top:10px"><?php echo $msg_addccts26; ?>: <?php echo mc_displayHelpTip($msg_javascript270); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="newsletter" value="yes"<?php echo (isset($EDIT->newsletter) && $EDIT->newsletter=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="newsletter" value="no"<?php echo (isset($EDIT->newsletter) && $EDIT->newsletter=='no' ? ' checked="checked"' : (!isset($EDIT->newsletter) ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_addccts8; ?>: <?php echo mc_displayHelpTip($msg_javascript270); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enablelog" value="yes"<?php echo (isset($EDIT->enablelog) && $EDIT->enablelog=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enablelog" value="no"<?php echo (isset($EDIT->enablelog) && $EDIT->enablelog=='no' ? ' checked="checked"' : (!isset($EDIT->enablelog) ? ' checked="checked"' : '')); ?>>

  </div>
  <br class="clear">
</div>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#one" data-toggle="tab"><i class="fa fa-credit-card fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $msg_addccts24; ?></span></a></li>
      <li><a href="#two" data-toggle="tab"><i class="fa fa-truck fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $msg_addccts25; ?></span></a></li>
      <li><a href="#three" data-toggle="tab"><i class="fa fa-file-text-o fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $msg_addccts9; ?></span></a></li>
      <?php
      if ($SETTINGS->en_wish == 'yes') {
      ?>
      <li><a href="#four" data-toggle="tab"><i class="fa fa-heart fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $msg_addccts45; ?></span></a></li>
      <?php
      }
      ?>
    </ul>
  </div>
</div>

<div class="formFieldWrapper" style="margin-top:10px">
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="tab-content">
        <div class="tab-pane active in" id="one">
          <div class="form-group">
            <label><?php echo $msg_addccts27; ?></label>
            <div class="form-group input-group">
              <span class="input-group-addon"><a href="#" title="<?php echo mc_safeHTML($msg_addccts23); ?>" onclick="mc_fieldCopyAccounts('shipping');return false"><i class="fa fa-copy fa-fw"></i></a></span>
              <input class="form-control" name="bill[nm]" value="<?php echo (isset($addrFields['bill']['nm']) ? mc_safeHTML($addrFields['bill']['nm']) : ''); ?>">
            </div>
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts28; ?></label>
            <input class="form-control" name="bill[em]" value="<?php echo (isset($addrFields['bill']['em']) ? mc_safeHTML($addrFields['bill']['em']) : ''); ?>">
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts15; ?></label>
            <select name="bill[country]" class="form-control" onchange="mc_stateLoaderSelect('bill', this.value, '<?php echo (isset($EDIT->id) ? $EDIT->id : '0'); ?>')">
              <?php
              $q_c = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
                     WHERE `enCountry` = 'yes'
                     ORDER BY `cName`
                     ") or die(mc_MySQLError(__LINE__,__FILE__));
              while ($C = mysqli_fetch_object($q_c)) {
              ?>
              <option value="<?php echo $C->id; ?>"<?php echo (isset($addrFields['bill']['addr1']) && $addrFields['bill']['addr1']==$C->id ? ' selected="selected"' : (!isset($addrFields['bill']['addr1']) && $SETTINGS->shipCountry == $C->id ? ' selected="selected"' : '')); ?>><?php echo mc_cleanData($C->cName); ?></option>
              <?php
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts16; ?></label>
            <input class="form-control" name="bill[2]" value="<?php echo (isset($addrFields['bill']['addr2']) ? mc_safeHTML($addrFields['bill']['addr2']) : ''); ?>">
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts17; ?></label>
            <input class="form-control" name="bill[3]" value="<?php echo (isset($addrFields['bill']['addr3']) ? mc_safeHTML($addrFields['bill']['addr3']) : ''); ?>">
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts18; ?></label>
            <input class="form-control" name="bill[4]" value="<?php echo (isset($addrFields['bill']['addr4']) ? mc_safeHTML($addrFields['bill']['addr4']) : ''); ?>">
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts19; ?></label>
            <div id="bstbox">
              <input class="form-control" name="bill[5]" value="<?php echo (isset($addrFields['bill']['addr5']) ? mc_safeHTML($addrFields['bill']['addr5']) : ''); ?>">
            </div>
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts20; ?></label>
            <input class="form-control" name="bill[6]" value="<?php echo (isset($addrFields['bill']['addr6']) ? mc_safeHTML($addrFields['bill']['addr6']) : ''); ?>">
          </div>
        </div>
        <div class="tab-pane fade" id="two">
          <div class="form-group">
            <label><?php echo $msg_addccts29; ?></label>
            <div class="form-group input-group">
              <span class="input-group-addon"><a href="#" title="<?php echo mc_safeHTML($msg_addccts22); ?>" onclick="mc_fieldCopyAccounts('billing');return false"><i class="fa fa-copy fa-fw"></i></a></span>
              <input class="form-control" name="ship[nm]" value="<?php echo (isset($addrFields['ship']['nm']) ? mc_safeHTML($addrFields['ship']['nm']) : ''); ?>">
            </div>
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts30; ?></label>
            <input class="form-control" name="ship[em]" value="<?php echo (isset($addrFields['ship']['em']) ? mc_safeHTML($addrFields['ship']['em']) : ''); ?>">
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts15; ?></label>
            <select name="ship[country]" class="form-control" onchange="mc_stateLoaderSelect('ship', this.value, '<?php echo (isset($EDIT->id) ? $EDIT->id : '0'); ?>')">
              <?php
              $q_c = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
                     WHERE `enCountry` = 'yes'
                     ORDER BY `cName`
                     ") or die(mc_MySQLError(__LINE__,__FILE__));
              while ($C = mysqli_fetch_object($q_c)) {
              ?>
              <option value="<?php echo $C->id; ?>"<?php echo (isset($addrFields['ship']['addr1']) && $addrFields['ship']['addr1']==$C->id ? ' selected="selected"' : (!isset($addrFields['ship']['addr1']) && $SETTINGS->shipCountry == $C->id ? ' selected="selected"' : '')); ?>><?php echo mc_cleanData($C->cName); ?></option>
              <?php
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts16; ?></label>
            <input class="form-control" name="ship[2]" value="<?php echo (isset($addrFields['ship']['addr2']) ? mc_safeHTML($addrFields['ship']['addr2']) : ''); ?>">
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts17; ?></label>
            <input class="form-control" name="ship[3]" value="<?php echo (isset($addrFields['ship']['addr3']) ? mc_safeHTML($addrFields['ship']['addr3']) : ''); ?>">
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts18; ?></label>
            <input class="form-control" name="ship[4]" value="<?php echo (isset($addrFields['ship']['addr4']) ? mc_safeHTML($addrFields['ship']['addr4']) : ''); ?>">
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts19; ?></label>
            <div id="sstbox">
              <input class="form-control" name="ship[5]" value="<?php echo (isset($addrFields['ship']['addr5']) ? mc_safeHTML($addrFields['ship']['addr5']) : ''); ?>">
            </div>
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts20; ?></label>
            <input class="form-control" name="ship[6]" value="<?php echo (isset($addrFields['ship']['addr6']) ? mc_safeHTML($addrFields['ship']['addr6']) : ''); ?>">
          </div>
          <div class="form-group">
            <label><?php echo $msg_addccts21; ?></label>
            <input class="form-control" name="ship[7]" value="<?php echo (isset($addrFields['ship']['addr7']) ? mc_safeHTML($addrFields['ship']['addr7']) : ''); ?>">
          </div>
        </div>
        <div class="tab-pane fade" id="three">
          <textarea tabindex="<?php echo (++$tabIndex); ?>" name="notes" rows="5" cols="20"><?php echo (isset($EDIT->notes) ? mc_safeHTML($EDIT->notes) : ''); ?></textarea>
        </div>
        <?php
        if ($SETTINGS->en_wish == 'yes') {
        ?>
        <div class="tab-pane fade" id="four">
          <label><?php echo $msg_addccts46; ?></label>
          <textarea tabindex="<?php echo (++$tabIndex); ?>" name="wishtext" rows="5" cols="20"><?php echo (isset($EDIT->wishtext) ? mc_safeHTML($EDIT->wishtext) : ''); ?></textarea>
        </div>
        <?php
        }
        ?>
      </div>
    </div>
  </div>
</div>

<?php
// Show on edit screen only and only if trackers exist..
if (isset($_GET['edit']) && mc_rowCount('tracker') > 0) {
?>
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_addccts44; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
     <input class="form-control" name="trackcode" value="<?php echo (isset($EDIT->trackcode) ? mc_safeHTML($EDIT->trackcode) : ''); ?>">
  </div>
  <br class="clear">
</div>
<?php
}
?>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_addccts31; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <?php echo $msg_addccts32; ?> <input onclick="mc_setAccType(this.value)" type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="type" value="personal"<?php echo (isset($EDIT->type) && $EDIT->type == 'personal' ? ' checked="checked"' : (!isset($EDIT->type) && !isset($_GET['loadtype']) ? ' checked="checked"' : '')); ?>> <?php echo $msg_addccts33; ?> <input onclick="mc_setAccType(this.value)" tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="type" value="trade"<?php echo (isset($EDIT->type) && $EDIT->type == 'trade' ? ' checked="checked"' : (isset($_GET['loadtype']) ? ' checked="checked"' : '')); ?>>
   </div>
  <br class="clear">
</div>

<div id="tradeopts" class="formFieldWrapper"<?php echo ((!isset($EDIT->type) || isset($EDIT->type) && $EDIT->type == 'personal') && !isset($_GET['loadtype']) ? ' style="display:none"' : ''); ?>>
  <div class="formLeft">
    <label><?php echo $msg_addccts34; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <div class="form-group input-group">
      <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="tradediscount" value="<?php echo (isset($EDIT->tradediscount) ? mc_safeHTML($EDIT->tradediscount) : ''); ?>" class="box addon-no-radius-right" maxlength="5">
      <span class="input-group-addon"><i class="fa fa-percent fa-fw"></i></span>
    </div>
    <label style="margin-top:10px"><?php echo $msg_addccts36; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="minqty" value="<?php echo (isset($EDIT->minqty) ? mc_safeHTML($EDIT->minqty) : ''); ?>" class="box" maxlength="10">

    <label style="margin-top:10px"><?php echo $msg_addccts37; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="maxqty" value="<?php echo (isset($EDIT->maxqty) ? mc_safeHTML($EDIT->maxqty) : ''); ?>" class="box" maxlength="10">

    <label style="margin-top:10px"><?php echo $msg_addccts47; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="mincheckout" value="<?php echo (isset($EDIT->mincheckout) ? mc_safeHTML($EDIT->mincheckout) : ''); ?>" class="box" maxlength="20">

    <label style="margin-top:10px"><?php echo $msg_addccts38; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="stocklevel" value="<?php echo (isset($EDIT->stocklevel) ? mc_safeHTML($EDIT->stocklevel) : ''); ?>" class="box" maxlength="10">
  </div>
  <br class="clear">
</div>

<?php
// Show on add screen only..
if (!isset($_GET['edit'])) {
?>
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_addccts10; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="send" value="yes"<?php echo (isset($_POST['send']) && $_POST['send'] == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="send" value="no"<?php echo (isset($_POST['send']) && $_POST['send'] == 'no' ? ' checked="checked"' : (!isset($_POST['send']) ? ' checked="checked"' : '')); ?>>
   </div>
  <br class="clear">
</div>
<?php
}

// Show on edit screen only..
if (isset($_GET['edit'])) {
?>
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_addccts41; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <?php echo $msg_addccts42; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enabled" value="yes"<?php echo (isset($EDIT->enabled) && $EDIT->enabled == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_addccts43; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enabled" value="no"<?php echo (isset($EDIT->enabled) && $EDIT->enabled == 'no' ? ' checked="checked"' : ''); ?>>
   </div>
  <br class="clear">
</div>
<?php
}
?>

<p style="text-align:center;padding-top:20px">
 <?php
 if (isset($_GET['edit'])) {
 ?>
 <input type="hidden" name="opass" value="<?php echo mc_safeHTML($EDIT->pass); ?>">
 <?php
 }
 ?>
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="<?php echo (isset($EDIT->id) ? $EDIT->id : 'yes'); ?>">
 <input class="btn btn-primary" type="button" onclick="return mc_checkAddAcc()" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_addccts4 : $msg_addccts3)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_addccts4 : $msg_addccts3)); ?>">
 <?php
 $link = 'accounts';
 if (isset($EDIT->id)) {
   if ($EDIT->verified == 'no' || $EDIT->enabled == 'no') {
     $link = 'daccounts';
   }
   if ($EDIT->type == 'trade') {
     $link = 'taccounts';
   }
 }
 ?>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location='?p=<?php echo $link; ?>'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
</p>
</form>

</div>
