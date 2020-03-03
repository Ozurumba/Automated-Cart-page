<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
$tabIndex = 0;
if (isset($OK)) {
  echo mc_actionCompleted($msg_settings31);
  //Reload..
  $SETTINGS = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"))
              or die(mc_MySQLError(__LINE__,__FILE__));
}
include(GLOBAL_PATH . 'control/classes/class.social.php');
$MCSOC  = new mcSocial();
$api    = $MCSOC->params();
?>

<form method="post" id="form" action="?p=settings&amp;s=6">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_settings25; ?></p>
</div>

<div class="row" style="margin-bottom:20px">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#m_1" data-toggle="tab"><i class="fa fa-envelope fa-fw"></i> <?php echo $msg_admin_settings3_0[56]; ?></a></li>
      <li><a href="#m_2" data-toggle="tab"><i class="fa fa-cog fa-fw"></i> <?php echo $msg_admin_settings3_0[57]; ?></a></li>
    </ul>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="tab-content">
      <div class="tab-pane active in" id="m_1">
       <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $mc_37_admin_settings[1]; ?>:</label>
            <select name="mailpref" tabindex="<?php echo ++$tabIndex; ?>">
              <option value="smtp"<?php echo ($SETTINGS->mailpref == 'smtp' ? ' selected="selected"' : ''); ?>><?php echo $mc_37_admin_settings[2]; ?></option>
              <option value="php"<?php echo ($SETTINGS->mailpref == 'php' ? ' selected="selected"' : ''); ?>><?php echo $mc_37_admin_settings[3]; ?></option>
            </select>
            
            <label style="margin-top:10px"><?php echo $msg_settings30; ?>: <?php echo mc_displayHelpTip($msg_javascript38,'LEFT'); ?></label>
            <input type="text" name="smtp_host" value="<?php echo mc_cleanData($SETTINGS->smtp_host); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_settings28; ?>: <?php echo mc_displayHelpTip($msg_javascript25,'RIGHT'); ?></label>
            <input type="text" name="smtp_port" value="<?php echo $SETTINGS->smtp_port; ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_admin3_0[0]; ?>: <?php echo mc_displayHelpTip($msg_javascript25,'RIGHT'); ?></label>
            <select name="smtp_security" tabindex="<?php echo ++$tabIndex; ?>">
              <option value="">- - - -</option>
              <option value="tls"<?php echo ($SETTINGS->smtp_security == 'tls' ? ' selected="selected"' : ''); ?>>TLS</option>
              <option value="ssl"<?php echo ($SETTINGS->smtp_security == 'ssl' ? ' selected="selected"' : ''); ?>>SSL</option>
            </select>
            
            <label style="margin-top:10px"><?php echo $mc_37_admin_settings[0]; ?>:</label>
            <select name="smtp_insec" tabindex="<?php echo ++$tabIndex; ?>">
              <option value="yes"<?php echo ($SETTINGS->smtp_insec == 'yes' ? ' selected="selected"' : ''); ?>><?php echo $msg_script5; ?></option>
              <option value="no"<?php echo ($SETTINGS->smtp_insec == 'no' ? ' selected="selected"' : ''); ?>><?php echo $msg_script6; ?></option>
            </select>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings26; ?>: <?php echo mc_displayHelpTip($msg_javascript23,'RIGHT'); ?></label>
            <input type="text" name="smtp_user" value="<?php echo mc_cleanData($SETTINGS->smtp_user); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings27; ?>: <?php echo mc_displayHelpTip($msg_javascript24,'LEFT'); ?></label>
            <input type="password" name="smtp_pass" value="<?php echo mc_safeHTML($SETTINGS->smtp_pass); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin3_0[45]; ?>: <?php echo mc_displayHelpTip($msg_javascript23,'RIGHT'); ?></label>
            <input type="text" name="smtp_from" value="<?php echo mc_cleanData($SETTINGS->smtp_from); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_admin3_0[46]; ?>: <?php echo mc_displayHelpTip($msg_javascript24,'LEFT'); ?></label>
            <input type="text" name="smtp_email" value="<?php echo mc_safeHTML($SETTINGS->smtp_email); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin3_0[47]; ?>: <?php echo mc_displayHelpTip($msg_javascript22,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="smtp_debug" value="yes"<?php echo ($SETTINGS->smtp_debug=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="smtp_debug" value="no"<?php echo ($SETTINGS->smtp_debug=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="m_2">
       <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><a href="http://cleantalk.org/?pid=161167" onclick="window.open(this);return false"><?php echo $msg_admin_settings3_0[58]; ?></a>: <?php echo mc_displayHelpTip($msg_javascript38,'LEFT'); ?></label>
            <input type="password" name="api[ctalk][key]" value="<?php echo (isset($api['ctalk']['key']) ? mc_safeHTML($api['ctalk']['key']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[59]; ?>: <?php echo mc_displayHelpTip($msg_javascript25,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[ctalk][enable]" value="yes"<?php echo (isset($api['ctalk']['enable']) && $api['ctalk']['enable'] == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[ctalk][enable]" value="no"<?php echo (isset($api['ctalk']['enable']) && $api['ctalk']['enable'] == 'no' ? ' checked="checked"' : (!isset($api['ctalk']['enable']) ? ' checked="checked"' : '')); ?>>

          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[60]; ?>: <?php echo mc_displayHelpTip($msg_javascript25,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[ctalk][log]" value="yes"<?php echo (isset($api['ctalk']['log']) && $api['ctalk']['log'] == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[ctalk][log]" value="no"<?php echo (isset($api['ctalk']['log']) && $api['ctalk']['log'] == 'no' ? ' checked="checked"' : (!isset($api['ctalk']['log']) ? ' checked="checked"' : '')); ?>>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[61]; ?>: <?php echo mc_displayHelpTip($msg_javascript25,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[ctalk][mail]" value="yes"<?php echo (isset($api['ctalk']['mail']) && $api['ctalk']['mail'] == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[ctalk][mail]" value="no"<?php echo (isset($api['ctalk']['mail']) && $api['ctalk']['mail'] == 'no' ? ' checked="checked"' : (!isset($api['ctalk']['mail']) ? ' checked="checked"' : '')); ?>>

          </div>
          <br class="clear">
        </div>
      </div>
    </div>
  </div>
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_settings8); ?>" title="<?php echo mc_cleanDataEntVars($msg_settings8); ?>">
 <button style="margin-left: 50px" onclick="mc_Window('index.php?tmail=yes', 500, 500, '')" class="btn btn-success" type="button"><i class="fa fa-envelope fa-fw"></i> <?php echo $msg_smtp_settings[0]; ?></button>
</p>
</form>


</div>
