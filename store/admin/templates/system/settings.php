<?php if (!defined('PARENT')) { die('Permission Denied'); } 
$cfg = mc_getCFG('affiliate');
?>
<div id="content">
<script>
//<![CDATA[
function mc_clearCache() {
  var confirmSub = confirm('<?php echo mc_filterJS($msg_javascript); ?>');
  if (confirmSub) {
    mc_ShowSpinner();
    jQuery(document).ready(function() {
      jQuery.ajax({
        url: 'index.php',
        data: 'p=settings&clearcache=yes',
        dataType: 'json',
        cache: false,
        success: function (data) {
          if (data[0] == 'OK') {
            mc_CloseSpinner();
          }
        }
      });
    });
    return false;
  } else {
    return false;
  }
}
//]]>
</script>

<?php
$tabIndex = 0;
if (isset($OK)) {
  echo mc_actionCompleted($msg_settings31);
  //Reload..
  $SETTINGS = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"))
              or die(mc_MySQLError(__LINE__,__FILE__));
}
// Unserialize data..
$SLIDER = ($SETTINGS->searchSlider ? unserialize($SETTINGS->searchSlider) : array());
include(GLOBAL_PATH . 'control/classes/class.social.php');
$MCSOC  = new mcSocial();
$api    = $MCSOC->params();
?>

<form method="post" id="form" action="?p=settings">

<div class="fieldHeadWrapper">
  <p><?php echo $msg_admin_settings3_0[3]; ?></p>
</div>

<div class="row" style="margin-bottom:20px">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#s_1" data-toggle="tab"><i class="fa fa-cog fa-fw"></i> <?php echo $msg_javascript106; ?></a></li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench fa-fw"></i> <span class="hidden-xs"><?php echo $msg_admin_settings3_0[0]; ?> </span><span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-right center_dropdown" role="menu">
          <li><a href="#s_15" data-toggle="tab"><?php echo $msg_admin_settings3_0[1]; ?></a></li>
          <li><a href="#s_13" data-toggle="tab"><?php echo $msg_settings122; ?></a></li>
          <li><a href="#s_3" data-toggle="tab"><?php echo $msg_settings142; ?></a></li>
          <li><a href="#s_2" data-toggle="tab"><?php echo $msg_settings140; ?></a></li>
          <li><a href="#s_10" data-toggle="tab"><?php echo $msg_settings144; ?></a></li>
          <li><a href="#s_6" data-toggle="tab"><?php echo $msg_settings217; ?></a></li>
          <li><a href="#s_7" data-toggle="tab"><?php echo $msg_settings258; ?></a></li>
          <li><a href="#s_4" data-toggle="tab"><?php echo $lng_37_affiliate[0]; ?></a></li>
          <li><a href="#s_8" data-toggle="tab"><?php echo $msg_settings143; ?></a></li>
          <li><a href="#s_12" data-toggle="tab"><?php echo $msg_settings168; ?></a></li>
          <li><a href="#s_9" data-toggle="tab"><?php echo $msg_settings238; ?></a></li>
          <li><a href="#s_5" data-toggle="tab"><?php echo $msg_settings160; ?></a></li>
          <li><a href="#s_16" data-toggle="tab"><?php echo $msg_admin_settings3_0[30]; ?></a></li>
        </ul>
      </li>
    </ul>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="tab-content">
      <div class="tab-pane active in" id="s_1">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_settings2; ?>: <?php echo mc_displayHelpTip($msg_javascript3,'RIGHT'); ?></label>
            <input type="text" name="website" value="<?php echo mc_cleanData($SETTINGS->website); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_settings4; ?>: <?php echo mc_displayHelpTip($msg_javascript5,'RIGHT'); ?></label>
            <input type="text" name="ifolder" value="<?php echo mc_cleanData($SETTINGS->ifolder); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_settings60; ?>: <?php echo mc_displayHelpTip($msg_javascript200,'LEFT'); ?></label>
            <input type="text" name="serverPath" value="<?php echo mc_cleanData($SETTINGS->serverPath); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_settings13; ?>: <?php echo mc_displayHelpTip($msg_javascript10,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo ++$tabIndex; ?>" name="en_modr" value="yes"<?php echo ($SETTINGS->en_modr=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_modr" value="no"<?php echo ($SETTINGS->en_modr=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings9; ?>: <?php echo mc_displayHelpTip($msg_javascript7,'RIGHT'); ?></label>
            <input type="text" name="metaKeys" value="<?php echo mc_safeHTML($SETTINGS->metaKeys); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings10; ?>: <?php echo mc_displayHelpTip($msg_javascript8,'LEFT'); ?></label>
            <input type="text" name="metaDesc" value="<?php echo mc_safeHTML($SETTINGS->metaDesc); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings6; ?>: <?php echo mc_displayHelpTip($msg_javascript11,'RIGHT'); ?></label>
            <select name="languagePref" tabindex="<?php echo ++$tabIndex; ?>">
            <?php
            if (is_dir(REL_PATH.'content/language/')) {
              $showlang = opendir(REL_PATH.'content/language/');
              while (false!==($read=readdir($showlang))) {
                if (is_dir(REL_PATH.'content/language/'.$read) && !in_array($read,array('.','..'))) {
                  echo '<option value="'.$read.'"'.($read==$SETTINGS->languagePref ? ' selected="selected"' : '').'>'.$read.'</option>'.mc_defineNewline();
                }
              }
              closedir($showlang);
            }
            ?>
            </select>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings245; ?>: <?php echo mc_displayHelpTip($msg_javascript467); ?></label>
            <select name="theme" tabindex="<?php echo ++$tabIndex; ?>">
            <?php
            if (is_dir(REL_PATH.'content/')) {
              $showthm = opendir(REL_PATH.'content/');
              while (false!==($read=readdir($showthm))) {
                if (is_dir(REL_PATH.'content/'.$read) && substr(strtolower($read),0,6)=='_theme') {
                  echo '<option'.($read==$SETTINGS->theme ? ' selected="selected"' : '').'>'.$read.'</option>'.mc_defineNewline();
                }
              }
              closedir($showthm);
            }
            ?>
            </select>
            <label  style="margin-top:10px"><?php echo $msg_admin_settings3_0[50]; ?>: <?php echo mc_displayHelpTip($msg_javascript467); ?></label>
            <select name="theme2" tabindex="<?php echo ++$tabIndex; ?>">
            <?php
            if (is_dir(REL_PATH.'content/')) {
              $showthm = opendir(REL_PATH.'content/');
              while (false!==($read=readdir($showthm))) {
                if (is_dir(REL_PATH.'content/'.$read) && substr(strtolower($read),0,6)=='_theme') {
                  echo '<option'.($read==$SETTINGS->theme2 ? ' selected="selected"' : '').'>'.$read.'</option>'.mc_defineNewline();
                }
              }
              closedir($showthm);
            }
            ?>
            </select>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings145; ?>: <?php echo mc_displayHelpTip($msg_javascript109,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enEntryLog" value="yes"<?php echo ($SETTINGS->enEntryLog=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enEntryLog" value="no"<?php echo ($SETTINGS->enEntryLog=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings159; ?>: <?php echo mc_displayHelpTip($msg_javascript317,'LEFT'); ?></label>
            <input type="text" name="adminFolderName" value="<?php echo mc_cleanData($SETTINGS->adminFolderName); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings150; ?>: <?php echo mc_displayHelpTip($msg_javascript288,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="smartQuotes" value="yes"<?php echo ($SETTINGS->smartQuotes=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="smartQuotes" value="no"<?php echo ($SETTINGS->smartQuotes=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings195; ?>: <?php echo mc_displayHelpTip($msg_javascript385,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableBBCode" value="yes"<?php echo ($SETTINGS->enableBBCode=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableBBCode" value="no"<?php echo ($SETTINGS->enableBBCode=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_2">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_settings87; ?>: <?php echo mc_displayHelpTip($msg_javascript230,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="activateEmails" value="yes"<?php echo ($SETTINGS->activateEmails=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="activateEmails" value="no"<?php echo ($SETTINGS->activateEmails=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings3; ?>: <?php echo mc_displayHelpTip($msg_javascript4,'LEFT'); ?></label>
            <input type="text" name="email" value="<?php echo mc_cleanData($SETTINGS->email); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings141; ?>: <?php echo mc_displayHelpTip($msg_javascript282,'RIGHT'); ?></label>
            <input type="text" name="addEmails" value="<?php echo mc_cleanData($SETTINGS->addEmails); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_3">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><a href="http://php.net/manual/en/function.date.php" onclick="window.open(this);return false" title="<?php echo mc_cleanDataEntVars($msg_settings102); ?>"><?php echo $msg_settings102; ?></a>: <?php echo mc_displayHelpTip($msg_javascript233,'RIGHT'); ?></label>
            <input type="text" name="systemDateFormat" value="<?php echo $SETTINGS->systemDateFormat; ?>" class="box" maxlength="10" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><a href="http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html#function_date-format" onclick="window.open(this);return false" title="<?php echo mc_cleanDataEntVars($msg_settings99); ?>"><?php echo $msg_settings99; ?></a>: <?php echo mc_displayHelpTip($msg_javascript234,'LEFT'); ?></label>
            <input type="text" name="mysqlDateFormat" value="<?php echo $SETTINGS->mysqlDateFormat; ?>" class="box" maxlength="30" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings96; ?>: <?php echo mc_displayHelpTip($msg_javascript235,'RIGHT'); ?></label>
            <select name="jsDateFormat" tabindex="<?php echo ++$tabIndex; ?>">
            <?php
            foreach (array('DD-MM-YYYY','DD/MM/YYYY','YYYY-MM-DD','YYYY/MM/DD','MM-DD-YYYY','MM/DD/YYYY') AS $jsf) {
            ?>
            <option value="<?php echo $jsf; ?>"<?php echo ($SETTINGS->jsDateFormat==$jsf ? ' selected="selected"' : ''); ?>><?php echo $jsf; ?></option>
            <?php
            }
            ?>
            </select>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings97; ?>: <?php echo mc_displayHelpTip($msg_javascript236,'LEFT'); ?></label>
            <?php echo $msg_settings103; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="jsWeekStart" value="0"<?php echo ($SETTINGS->jsWeekStart=='0' ? ' checked="checked"' : ''); ?>> <?php echo $msg_settings104; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="jsWeekStart" value="1"<?php echo ($SETTINGS->jsWeekStart=='1' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings100; ?>: <?php echo mc_displayHelpTip(str_replace('{offset}',date('j F Y').' @ '.date('H:iA'),$msg_javascript237),'RIGHT'); ?></label>
            <select name="timezone" tabindex="<?php echo ++$tabIndex; ?>">
            <?php
            include(REL_PATH.'control/timezones.php');
            foreach ($timezones AS $tK => $tV) {
            ?>
            <option value="<?php echo $tK; ?>"<?php echo ($SETTINGS->timezone==$tK ? ' selected="selected"' : ''); ?>><?php echo $tV; ?></option>
            <?php
            }
            ?>
            </select>
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_4">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $lng_37_affiliate[1]; ?></label>
            <input type="text" name="cfg[affiliate][url]" value="<?php echo (isset($cfg['affiliate']['url']) ? $cfg['affiliate']['url'] : ''); ?>" class="box">
          </div>
          <div class="formRight">
            <label><?php echo $lng_37_affiliate[2]; ?></label>
            <input type="text" name="cfg[affiliate][api]" value="<?php echo (isset($cfg['affiliate']['api']) ? $cfg['affiliate']['api'] : ''); ?>" class="box">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $lng_37_affiliate[3]; ?></label>
            <input type="text" name="cfg[affiliate][product]" value="<?php echo (isset($cfg['affiliate']['product']) ? $cfg['affiliate']['product'] : ''); ?>" class="box">
          </div>
          <div class="formRight">
            <label><?php echo $lng_37_affiliate[4]; ?></label>
            <input type="text" name="cfg[affiliate][param]" value="<?php echo (isset($cfg['affiliate']['param']) ? $cfg['affiliate']['param'] : ''); ?>" class="box">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $lng_37_affiliate[5]; ?></label>
            <input type="text" name="cfg[affiliate][commission]" value="<?php echo (isset($cfg['affiliate']['commission']) ? $cfg['affiliate']['commission'] : ''); ?>" class="box">
          </div>
          <div class="formRight">
            <label><?php echo $lng_37_affiliate[7]; ?></label>
            <input type="radio" name="cfg[affiliate][logs]" value="yes"<?php echo (isset($cfg['affiliate']['logs']) && $cfg['affiliate']['logs'] == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script5; ?>
            <input type="radio" name="cfg[affiliate][logs]" value="no"<?php echo (isset($cfg['affiliate']['logs']) && $cfg['affiliate']['logs'] == 'no' ? ' checked="checked"' : (!isset($cfg['affiliate']['logs']) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?>
          </div>
          <br class="clear">
        </div>
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $lng_37_affiliate[6]; ?></label>
            <input type="radio" name="cfg[affiliate][en]" value="yes"<?php echo (isset($cfg['affiliate']['en']) && $cfg['affiliate']['en'] == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script5; ?>
            <input type="radio" name="cfg[affiliate][en]" value="no"<?php echo (isset($cfg['affiliate']['en']) && $cfg['affiliate']['en'] == 'no' ? ' checked="checked"' : (!isset($cfg['affiliate']['en']) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?>
          </div>
          <div class="formRight">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_5">
        <div class="fieldHeadWrapper" style="margin-top:10px">
          <p><?php echo $msg_admin_settings3_0[12]; ?></p>
        </div>

        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <div class="form-group" style="margin-bottom:0">
              <?php
              foreach ($socialSites AS $ss_k) {
              ?>
              <div class="form-group input-group" style="margin-bottom:5px">
                <span class="input-group-addon"><a href="<?php echo $ss_k[2]; ?>" onclick="window.open(this);return false"><i class="fa fa-<?php echo $ss_k[0]; ?> fa-fw" title="<?php echo mc_safeHTML($ss_k[1]); ?>"></i></a></span>
                <input type="text" name="api[links][<?php echo $ss_k[0]; ?>]" value="<?php echo (isset($api['links'][$ss_k[0]]) ? mc_safeHTML($api['links'][$ss_k[0]]) : ''); ?>" class="box addon-no-radius" tabindex="<?php echo ++$tabIndex; ?>">
              </div>
              <?php
              }
              ?>
            </div>
          </div>
        </div>

        <div class="fieldHeadWrapper">
          <p><?php echo $msg_admin_settings3_0[8]; ?></p>
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings265; ?>: <?php echo mc_displayHelpTip($msg_javascript485,'RIGHT'); ?></label>
            <input type="text" name="api[twitter][username]" value="<?php echo (isset($api['twitter']['username']) ? mc_safeHTML($api['twitter']['username']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_settings266; ?>: <?php echo mc_displayHelpTip($msg_javascript486,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="twitterLatest" value="yes"<?php echo ($SETTINGS->twitterLatest=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="twitterLatest" value="no"<?php echo ($SETTINGS->twitterLatest=='no' ? ' checked="checked"' : ''); ?>>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[11]; ?>: <?php echo mc_displayHelpTip($msg_javascript485,'RIGHT'); ?></label>
            <input type="text" name="tweetlimit" value="<?php echo mc_cleanData($SETTINGS->tweetlimit); ?>" maxlength="5" class="box" tabindex="<?php echo ++$tabIndex; ?>">

          </div>
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><a href="https://dev.twitter.com/" onclick="window.open(this);return false"><?php echo $msg_admin_settings3_0[4]; ?></a>: <?php echo mc_displayHelpTip($msg_javascript328,'LEFT'); ?></label>
            <input type="text" name="api[twitter][conkey]" value="<?php echo (isset($api['twitter']['conkey']) ? mc_safeHTML($api['twitter']['conkey']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[5]; ?>: <?php echo mc_displayHelpTip($msg_javascript328,'LEFT'); ?></label>
            <input type="password" name="api[twitter][consecret]" value="<?php echo (isset($api['twitter']['consecret']) ? mc_safeHTML($api['twitter']['consecret']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[6]; ?>: <?php echo mc_displayHelpTip($msg_javascript328,'LEFT'); ?></label>
            <input type="text" name="api[twitter][token]" value="<?php echo (isset($api['twitter']['token']) ? mc_safeHTML($api['twitter']['token']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[7]; ?>: <?php echo mc_displayHelpTip($msg_javascript328,'LEFT'); ?></label>
            <input type="password" name="api[twitter][key]" value="<?php echo (isset($api['twitter']['key']) ? mc_safeHTML($api['twitter']['key']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[41]; ?>: <?php echo mc_displayHelpTip($msg_javascript486,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="tweet" value="yes"<?php echo ($SETTINGS->tweet=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="tweet" value="no"<?php echo ($SETTINGS->tweet=='no' ? ' checked="checked"' : ''); ?>>

          </div>
          <br class="clear">
        </div>

        <div class="fieldHeadWrapper">
          <p><?php echo $msg_settings175; ?></p>
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><a href="http://www.disqus.com" onclick="window.open(this);return false" title="<?php echo mc_cleanDataEntVars($msg_settings173); ?>"><?php echo $msg_settings173; ?></a>: <?php echo mc_displayHelpTip($msg_javascript342,'RIGHT'); ?></label>
            <input type="text" name="api[disqus][disname]" value="<?php echo (isset($api['disqus']['disname']) ? mc_safeHTML($api['disqus']['disname']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>" maxlength="250">

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[10]; ?>: <?php echo mc_displayHelpTip($msg_javascript342,'RIGHT'); ?></label>
            <input type="text" name="api[disqus][discat]" value="<?php echo (isset($api['disqus']['discat']) ? (int) $api['disqus']['discat'] : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>" maxlength="250">
          </div>
          <br class="clear">
        </div>

        <div class="fieldHeadWrapper">
          <p><?php echo $msg_admin_settings3_0[47]; ?></p>
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[48]; ?>: <?php echo mc_displayHelpTip($msg_javascript486,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[struct][fb]" value="yes"<?php echo (isset($api['struct']['fb']) && $api['struct']['fb'] == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[struct][fb]" value="no"<?php echo (isset($api['struct']['fb']) && $api['struct']['fb'] == 'no' ? ' checked="checked"' : (!isset($api['struct']['fb']) ? ' checked="checked"' : '')); ?>>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[49]; ?>: <?php echo mc_displayHelpTip($msg_javascript486,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[struct][google]" value="yes"<?php echo (isset($api['struct']['google']) && $api['struct']['google'] == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[struct][google]" value="no"<?php echo (isset($api['struct']['google']) && $api['struct']['google'] == 'no' ? ' checked="checked"' : (!isset($api['struct']['google']) ? ' checked="checked"' : '')); ?>>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[46]; ?>: <?php echo mc_displayHelpTip($msg_javascript486,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[struct][twitter]" value="yes"<?php echo (isset($api['struct']['twitter']) && $api['struct']['twitter'] == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="api[struct][twitter]" value="no"<?php echo (isset($api['struct']['twitter']) && $api['struct']['twitter'] == 'no' ? ' checked="checked"' : (!isset($api['struct']['twitter']) ? ' checked="checked"' : '')); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="fieldHeadWrapper">
          <p><?php echo $msg_settings63; ?></p>
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><a href="https://www.addthis.com" onclick="window.open(this);return false"><?php echo $msg_admin_settings3_0[9]; ?>:</a> <?php echo mc_displayHelpTip($msg_javascript485,'RIGHT'); ?></label>
            <input type="text" name="api[addthis][code]" value="<?php echo (isset($api['addthis']['code']) ? mc_safeHTML($api['addthis']['code']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_6">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><a href="https://isbndb.com/" onclick="window.open(this);return false" title="<?php echo mc_cleanDataEntVars($msg_settings218); ?>"><?php echo $msg_settings218; ?></a>: <?php echo mc_displayHelpTip($msg_javascript419,'RIGHT'); ?></label>
            <input type="text" name="isbnAPI" value="<?php echo mc_cleanData($SETTINGS->isbnAPI); ?>" maxlength="50" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_7">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_settings259; ?>: <?php echo mc_displayHelpTip($msg_javascript479,'RIGHT'); ?></label>
            <input type="text" name="cubeUrl" value="<?php echo mc_cleanData($SETTINGS->cubeUrl); ?>" maxlength="250" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_settings260; ?>: <?php echo mc_displayHelpTip($msg_javascript480,'LEFT'); ?></label>
            <input type="password" name="cubeAPI" value="<?php echo mc_cleanData($SETTINGS->cubeAPI); ?>" maxlength="250" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[70]; ?>: <?php echo mc_displayHelpTip($msg_javascript479,'RIGHT'); ?></label>
            <input type="text" name="guardianUrl" value="<?php echo mc_cleanData($SETTINGS->guardianUrl); ?>" maxlength="250" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[71]; ?>: <?php echo mc_displayHelpTip($msg_javascript480,'LEFT'); ?></label>
            <input type="password" name="guardianAPI" value="<?php echo mc_cleanData($SETTINGS->guardianAPI); ?>" maxlength="250" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_8">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_settings12; ?>: <?php echo mc_displayHelpTip($msg_javascript9,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_rss" value="yes"<?php echo ($SETTINGS->en_rss=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_rss" value="no"<?php echo ($SETTINGS->en_rss=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings101; ?>: <?php echo mc_displayHelpTip($msg_javascript238,'LEFT'); ?></label>
            <input type="text" name="rssFeedLimit" value="<?php echo $SETTINGS->rssFeedLimit; ?>" class="box" maxlength="3" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings275; ?>: <?php echo mc_displayHelpTip($msg_javascript495,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="rssScroller" value="yes"<?php echo ($SETTINGS->rssScroller=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="rssScroller" value="no"<?php echo ($SETTINGS->rssScroller=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings276; ?>: <?php echo mc_displayHelpTip($msg_javascript496,'LEFT'); ?></label>
            <input type="text" name="rssScrollerUrl" value="<?php echo $SETTINGS->rssScrollerUrl; ?>" class="box" maxlength="250" tabindex="<?php echo ++$tabIndex; ?>">
            <input type="text" name="rssScrollerLimit" style="margin-top:5px" value="<?php echo $SETTINGS->rssScrollerLimit; ?>" class="box" maxlength="3" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_9">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_settings239; ?>: <?php echo mc_displayHelpTip($msg_javascript461,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_sitemap" value="yes"<?php echo ($SETTINGS->en_sitemap=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_sitemap" value="no"<?php echo ($SETTINGS->en_sitemap=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_10">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_settings89; ?>: <?php echo mc_displayHelpTip($msg_javascript232,'RIGHT'); ?></label>
            <input type="text" name="productsPerPage" value="<?php echo $SETTINGS->productsPerPage; ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings107; ?>: <?php echo mc_displayHelpTip($msg_javascript247,'LEFT'); ?></label>
            <select name="minInvoiceDigits" tabindex="<?php echo ++$tabIndex; ?>">
            <?php
            foreach (range(0,20) AS $limits) {
            ?>
            <option value="<?php echo $limits; ?>"<?php echo ($SETTINGS->minInvoiceDigits==$limits ? ' selected="selected"' : ''); ?>><?php echo $limits; ?></option>
            <?php
            }
            ?>
            </select>
            <label style="margin-top:8px"><?php echo $msg_settings257; ?>:</label>
            <input type="text" name="invoiceNo" value="<?php echo $SETTINGS->invoiceNo; ?>" class="box" maxlength="11" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings88; ?>: <?php echo mc_displayHelpTip($msg_javascript241,'RIGHT'); ?></label>
            <input type="text" name="saleComparisonItems" value="<?php echo $SETTINGS->saleComparisonItems; ?>" class="box" maxlength="6" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings151; ?>: <?php echo mc_displayHelpTip($msg_javascript289,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="hitCounter" value="yes"<?php echo ($SETTINGS->hitCounter=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="hitCounter" value="no"<?php echo ($SETTINGS->hitCounter=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings90; ?>: <?php echo mc_displayHelpTip($msg_javascript243,'RIGHT'); ?></label>
            <input type="text" name="mostPopProducts" value="<?php echo $SETTINGS->mostPopProducts; ?>" class="box" maxlength="5" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings214; ?>: <?php echo mc_displayHelpTip($msg_javascript417,'LEFT'); ?></label>
            <?php echo $msg_settings215; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="mostPopPref" value="hits"<?php echo ($SETTINGS->mostPopPref=='hits' ? ' checked="checked"' : ''); ?>> <?php echo $msg_settings216; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="mostPopPref" value="sales"<?php echo ($SETTINGS->mostPopPref=='sales' ? ' checked="checked"' : ''); ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $msg_settings236; ?> <input type="checkbox" name="excludeFreePop" value="yes"<?php echo ($SETTINGS->excludeFreePop=='yes' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings91; ?> <?php echo $msg_settings172; ?>: <?php echo mc_displayHelpTip($msg_javascript244,'RIGHT'); ?></label>
            <input type="text" name="latestProdLimit" value="<?php echo $SETTINGS->latestProdLimit; ?>" class="box" maxlength="5" tabindex="<?php echo ++$tabIndex; ?>">
            <select name="latestProdDuration" style="margin-top:5px">
             <option value="days"<?php echo ($SETTINGS->latestProdDuration=='days' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings169; ?></option>
             <option value="months"<?php echo ($SETTINGS->latestProdDuration=='months' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings170; ?></option>
             <option value="years"<?php echo ($SETTINGS->latestProdDuration=='years' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings171; ?></option>
            </select>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings163; ?>: <?php echo mc_displayHelpTip($msg_javascript329,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableRecentView" value="yes"<?php echo ($SETTINGS->enableRecentView=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableRecentView" value="no"<?php echo ($SETTINGS->enableRecentView=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings188; ?>: <?php echo mc_displayHelpTip($msg_javascript374,'RIGHT'); ?></label>
            <input type="text" name="maxProductChars" value="<?php echo $SETTINGS->maxProductChars; ?>" class="box" maxlength="8" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings209; ?>: <?php echo mc_displayHelpTip($msg_javascript407,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="parentCatHomeDisplay" value="yes"<?php echo ($SETTINGS->parentCatHomeDisplay=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="parentCatHomeDisplay" value="no"<?php echo ($SETTINGS->parentCatHomeDisplay=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings234; ?>: <?php echo mc_displayHelpTip($msg_javascript456,'RIGHT'); ?></label>
            <input type="text" name="freeTextDisplay" value="<?php echo $SETTINGS->freeTextDisplay; ?>" class="box" maxlength="10" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings237; ?>: <?php echo mc_displayHelpTip($msg_javascript458,'LEFT'); ?></label>
            <input type="text" name="priceTextDisplay" value="<?php echo $SETTINGS->priceTextDisplay; ?>" class="box" maxlength="100" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[69]; ?>: <?php echo mc_displayHelpTip($msg_javascript456,'RIGHT'); ?></label>
            <input type="text" name="hurrystock" value="<?php echo $SETTINGS->hurrystock; ?>" class="box" maxlength="7" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings286; ?>: <?php echo mc_displayHelpTip($msg_javascript556,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="menuCatCount" value="yes"<?php echo ($SETTINGS->menuCatCount=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="menuCatCount" value="no"<?php echo ($SETTINGS->menuCatCount=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings295; ?>: <?php echo mc_displayHelpTip($msg_javascript573,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="menuBrandCount" value="yes"<?php echo ($SETTINGS->menuBrandCount=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="menuBrandCount" value="no"<?php echo ($SETTINGS->menuBrandCount=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <?php echo $msg_settings287; ?><br>
            <select name="catGiftPos" style="margin-top:5px">
            <option value="start"<?php echo ($SETTINGS->catGiftPos=='start' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings288; ?></option>
            <option value="end"<?php echo ($SETTINGS->catGiftPos=='end' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings289; ?></option>
            <option value="0" disabled="disabled">- - - - - -</option>
            <optgroup label="<?php echo mc_cleanDataEntVars($msg_settings290); ?>">
            <?php
            $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                      WHERE `catLevel` = '1'
                      AND `childOf`    = '0'
                      AND `enCat`      = 'yes'
                      ORDER BY `catname`
                      ") or die(mc_MySQLError(__LINE__,__FILE__));
            while ($CATS = mysqli_fetch_object($q_cats)) {
            ?>
            <option value="<?php echo $CATS->id; ?>"<?php echo ($SETTINGS->catGiftPos==$CATS->id ? ' selected="selected"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?></option>
              <?php
              $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                            WHERE `catLevel` = '2'
                            AND `enCat`      = 'yes'
                            AND `childOf`    = '{$CATS->id}'
                            ORDER BY `catname`
                            ") or die(mc_MySQLError(__LINE__,__FILE__));
              while ($CHILDREN = mysqli_fetch_object($q_children)) {
              ?>
              <option value="<?php echo $CHILDREN->id; ?>"<?php echo ($SETTINGS->catGiftPos==$CHILDREN->id ? ' selected="selected"' : ''); ?>>&nbsp;<?php echo mc_safeHTML($CHILDREN->catname); ?></option>
                <?php
                $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                             WHERE `catLevel` = '3'
                             AND `childOf`    = '{$CHILDREN->id}'
                             AND `enCat`      = 'yes'
                             ORDER BY `catname`
                             ") or die(mc_MySQLError(__LINE__,__FILE__));
                while ($INFANTS = mysqli_fetch_object($q_infants)) {
                ?>
                <option value="<?php echo $INFANTS->id; ?>"<?php echo ($SETTINGS->catGiftPos==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;<?php echo mc_safeHTML($INFANTS->catname); ?></option>
                <?php
                }
              }
            }
            ?>
            </optgroup>
            </select>
            <br>
            <?php echo $msg_settings291; ?> <?php echo mc_displayHelpTip($msg_javascript571,'RIGHT'); ?><br>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="showBrands" value="yes"<?php echo ($SETTINGS->showBrands=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="showBrands" value="no"<?php echo ($SETTINGS->showBrands=='no' ? ' checked="checked"' : ''); ?>>
            <br class="clear">
          </div>
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[51]; ?>: <?php echo mc_displayHelpTip($msg_javascript556,'RIGHT'); ?></label>
            <?php echo $msg_admin_settings3_0[53]; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="layout" value="list"<?php echo ($SETTINGS->layout=='list' ? ' checked="checked"' : ''); ?>>  <?php echo $msg_admin_settings3_0[52]; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="layout" value="grid"<?php echo ($SETTINGS->layout=='grid' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_12">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_settings167; ?>: <?php echo mc_displayHelpTip($msg_javascript335,'RIGHT'); ?></label>
            <input type="text" name="savedSearches" value="<?php echo $SETTINGS->savedSearches; ?>" class="box" maxlength="6" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings92; ?>: <?php echo mc_displayHelpTip($msg_javascript242,'LEFT'); ?></label>
            <input type="text" name="searchLowStockLimit" value="<?php echo $SETTINGS->searchLowStockLimit; ?>" class="box" maxlength="5" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="fieldHeadWrapper">
          <p><?php echo $msg_settings271; ?></p>
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings268; ?></label>
            <input type="text" name="searchSlider[min]" value="<?php echo (isset($SLIDER['min']) ? $SLIDER['min'] : '0'); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
            <label style="margin-top:10px"><?php echo $msg_settings269; ?></label>
            <input type="text" name="searchSlider[max]" value="<?php echo (isset($SLIDER['max']) ? $SLIDER['max'] : '300'); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
            <label style="margin-top:10px"><?php echo $msg_settings270; ?></label>
            <input type="text" name="searchSlider[start]" value="<?php echo (isset($SLIDER['start']) ? $SLIDER['start'] : '5'); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
            <input type="text" name="searchSlider[end]" style="margin-top:5px" value="<?php echo (isset($SLIDER['end']) ? $SLIDER['end'] : '100'); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings296; ?>: <?php echo mc_displayHelpTip($msg_javascript576,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="searchTagsOnly" value="yes"<?php echo ($SETTINGS->searchTagsOnly=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="searchTagsOnly" value="no"<?php echo ($SETTINGS->searchTagsOnly=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings146; ?>: <?php echo mc_displayHelpTip($msg_javascript285,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enSearchLog" value="yes"<?php echo ($SETTINGS->enSearchLog=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enSearchLog" value="no"<?php echo ($SETTINGS->enSearchLog=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_13">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_settings179; ?>: <?php echo mc_displayHelpTip($msg_javascript365,'RIGHT'); ?></label>
            <input type="text" name="thumbWidth" value="<?php echo $SETTINGS->thumbWidth; ?>" class="box" maxlength="4" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings180; ?>: <?php echo mc_displayHelpTip($msg_javascript366,'LEFT'); ?></label>
            <input type="text" name="thumbHeight" value="<?php echo $SETTINGS->thumbHeight; ?>" class="box" maxlength="4" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings181; ?>: <?php echo mc_displayHelpTip($msg_javascript367,'RIGHT'); ?></label>
            <input type="text" name="thumbQuality" value="<?php echo $SETTINGS->thumbQuality; ?>" class="box" maxlength="3" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings190; ?>: <?php echo mc_displayHelpTip($msg_javascript379,'LEFT'); ?></label>
            <select name="thumbQualityPNG">
            <?php
            foreach (range(0,9) AS $r) {
            ?>
            <option value="<?php echo $r; ?>"<?php echo ($SETTINGS->thumbQualityPNG==$r ? ' selected="selected"' : ''); ?>><?php echo $r; ?></option>
            <?php
            }
            ?>
            </select>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings281; ?>: <?php echo mc_displayHelpTip($msg_javascript548,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="aspectRatio" value="yes"<?php echo ($SETTINGS->aspectRatio=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="aspectRatio" value="no"<?php echo ($SETTINGS->aspectRatio=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings292; ?>: <?php echo mc_displayHelpTip($msg_javascript572,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="renamePics" value="yes"<?php echo ($SETTINGS->renamePics=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="renamePics" value="no"<?php echo ($SETTINGS->renamePics=='no' ? ' checked="checked"' : ''); ?>>
            <label style="margin-top:5px"><?php echo $msg_settings293; ?>:</label> <input type="text" name="tmbPrefix" value="<?php echo $SETTINGS->tmbPrefix; ?>" class="box" maxlength="100" tabindex="<?php echo ++$tabIndex; ?>">
            <label style="margin-top:5px"><?php echo $msg_settings294; ?>:</label> <input type="text" name="imgPrefix" value="<?php echo $SETTINGS->imgPrefix; ?>" class="box" maxlength="100" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_14">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_settings106; ?>: <?php echo mc_displayHelpTip($msg_javascript246,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableZip" value="yes"<?php echo ($SETTINGS->enableZip=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableZip" value="no"<?php echo ($SETTINGS->enableZip=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings135; ?>: <?php echo mc_displayHelpTip($msg_javascript277,'LEFT'); ?></label>
            <input type="text" name="zipCreationLimit" value="<?php echo $SETTINGS->zipCreationLimit; ?>" class="box" maxlength="100" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings136; ?>: <?php echo mc_displayHelpTip($msg_javascript278,'RIGHT'); ?></label>
            <input type="text" name="zipLimit" value="<?php echo $SETTINGS->zipLimit; ?>" class="box" maxlength="3" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings137; ?>: <?php echo mc_displayHelpTip($msg_javascript279,'LEFT'); ?></label>
            <input type="text" name="zipTimeOut" value="<?php echo $SETTINGS->zipTimeOut; ?>" class="box" maxlength="6" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings138; ?> (MB): <?php echo mc_displayHelpTip($msg_javascript280,'RIGHT'); ?></label>
            <input type="text" name="zipMemoryLimit" value="<?php echo $SETTINGS->zipMemoryLimit; ?>" class="box" maxlength="5" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings139; ?>: <?php echo mc_displayHelpTip($msg_javascript281,'LEFT'); ?></label>
            <input type="text" name="zipAdditionalFolder" value="<?php echo $SETTINGS->zipAdditionalFolder; ?>" class="box" maxlength="50" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_15">
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[15]; ?>: <?php echo mc_displayHelpTip($msg_javascript109,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_create" value="yes"<?php echo ($SETTINGS->en_create=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_create" value="no"<?php echo ($SETTINGS->en_create=='no' ? ' checked="checked"' : ''); ?>>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[16]; ?>: <?php echo mc_displayHelpTip($msg_javascript109,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_create_mail" value="yes"<?php echo ($SETTINGS->en_create_mail=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_create_mail" value="no"<?php echo ($SETTINGS->en_create_mail=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[13]; ?>: <?php echo mc_displayHelpTip($msg_javascript109,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_wish" value="yes"<?php echo ($SETTINGS->en_wish=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_wish" value="no"<?php echo ($SETTINGS->en_wish=='no' ? ' checked="checked"' : ''); ?>>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[68]; ?>: <?php echo mc_displayHelpTip($msg_javascript109,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="salereorder" value="yes"<?php echo ($SETTINGS->salereorder=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="salereorder" value="no"<?php echo ($SETTINGS->salereorder=='no' ? ' checked="checked"' : ''); ?>>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[29]; ?>: <?php echo mc_displayHelpTip($msg_javascript109,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_close" value="yes"<?php echo ($SETTINGS->en_close=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="en_close" value="no"<?php echo ($SETTINGS->en_close=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[2]; ?>: <?php echo mc_displayHelpTip($msg_javascript3,'RIGHT'); ?></label>
            <input type="text" name="minPassValue" value="<?php echo mc_cleanData($SETTINGS->minPassValue); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[14]; ?>: <?php echo mc_displayHelpTip($msg_javascript109,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="forcePass" value="yes"<?php echo ($SETTINGS->forcePass=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="forcePass" value="no"<?php echo ($SETTINGS->forcePass=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <?php
          /*
          ?>
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[64]; ?>: <?php echo mc_displayHelpTip($msg_javascript570,'RIGHT'); ?></label>
            <select name="tradetheme" tabindex="<?php echo ++$tabIndex; ?>">
              <option value="">- - - - - - -</option>
              <?php
              if (is_dir(REL_PATH.'content')) {
                $showtheme = opendir(REL_PATH.'content');
                while (false!==($read=readdir($showtheme))) {
                  if (is_dir(REL_PATH.'content/'.$read) && substr(strtolower($read),0,6)=='_theme' && $read != $SETTINGS->theme) {
                    echo '<option value="'.$read.'"'.($read == $SETTINGS->tradetheme ? ' selected="selected"' : '').'>'.$read.'</option>'.mc_defineNewline();
                  }
                }
                closedir($showtheme);
              }
            ?>
            </select>
          </div>
          <?php
          */
          ?>
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[67]; ?>: <?php echo mc_displayHelpTip($msg_javascript10,'LEFT'); ?></label>
            <input type="hidden" name="tradetheme" value="">
            <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo ++$tabIndex; ?>" name="tradeship" value="yes"<?php echo ($SETTINGS->tradeship=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="tradeship" value="no"<?php echo ($SETTINGS->tradeship=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <?php
        if (!HIDE_FORCE_PASS_RESET) {
        ?>
        <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label style="color:red"><?php echo $msg_admin_settings3_0[62]; ?>: <?php echo mc_displayHelpTip($msg_javascript109,'RIGHT'); ?></label>
            <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="forcePassReset" value="yes" onclick="if(this.checked){mc_alertBox('<?php echo mc_filterJS($msg_admin_settings3_0[63]); ?>')}">
          </div>
          <br class="clear">
        </div>
        <?php
        }
        ?>
      </div>
      <div class="tab-pane fade" id="s_16">
       <div class="formFieldWrapper" style="margin-top:10px">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[31]; ?>: <?php echo mc_displayHelpTip($msg_javascript109,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="cache" value="yes"<?php echo ($SETTINGS->cache=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="cache" value="no"<?php echo ($SETTINGS->cache=='no' ? ' checked="checked"' : ''); ?>>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[32]; ?>: <?php echo mc_displayHelpTip($msg_javascript109,'RIGHT'); ?></label>
            <select name="cachetime">
              <?php
              foreach (
                array(
                  0 => $msg_admin_settings3_0[35],
                  30 => '30 ' . $msg_admin_settings3_0[36],
                  60 => '1 ' . $msg_admin_settings3_0[38],
                  120 => '2 ' . $msg_admin_settings3_0[38],
                  240 => '4 ' . $msg_admin_settings3_0[38],
                  360 => '6 ' . $msg_admin_settings3_0[38],
                  1440 => $msg_admin_settings3_0[37]
                ) AS $ctk => $ctv) {
              ?>
              <option value="<?php echo $ctk; ?>"<?php echo ($SETTINGS->cachetime == $ctk ? ' selected="selected"' : ''); ?>><?php echo $ctv; ?></option>
              <?php
              }
              ?>
            </select>
            <hr>
            <button type="button" class="btn btn-danger" onclick="mc_clearCache()"><i class="fa fa-times fa-fw"></i> <?php echo $msg_admin_settings3_0[33]; ?></button>
            <div class="help-block" style="text-align:left"><br><?php echo $msg_admin_settings3_0[34]; ?>:<br>content/** theme **/<?php echo basename($MCCACHE->cache_options['cache_dir']); ?>/</div>
          </div>
          <br class="clear">
        </div>
      </div>
    </div>
  </div>
</div>

<p style="text-align:center;padding-top:10px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_settings8); ?>" title="<?php echo mc_cleanDataEntVars($msg_settings8); ?>">
</p>
</form>

</div>
