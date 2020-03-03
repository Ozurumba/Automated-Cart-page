<?php if (!defined('PARENT')) { die('Permission Denied'); }
define('CALBOX', 'globalDiscountExpiry');
include(PATH . 'templates/js-loader/date-picker.php');
?>
<div id="content">

<?php
$tabIndex = 0;
if (isset($OK)) {
  echo mc_actionCompleted((isset($_GET['global']) ? $msg_settings213 : $msg_settings31));
  //Reload..
  $SETTINGS = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"))
              or die(mc_MySQLError(__LINE__,__FILE__));
}
$PDF = mc_getTableData('pdf', 'id', '1');
?>

<form method="post" id="form" action="?p=settings&amp;s=3<?php echo (isset($_GET['global']) ? '&amp;global=yes' : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_settings50; ?></p>
</div>

<div class="row" style="margin-bottom:20px">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#s_1" data-toggle="tab"><i class="fa fa-globe fa-fw"></i> <?php echo $msg_settings166; ?></a></li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-cog fa-fw"></i> <span class="hidden-xs"><?php echo $msg_admin_settings3_0[17]; ?> </span><span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-right center_dropdown" role="menu">
          <li><a href="#s_2" data-toggle="tab"><?php echo $msg_admin_settings3_0[18]; ?></a></li>
          <li><a href="#s_3" data-toggle="tab"><?php echo $msg_settings202; ?></a></li>
          <li><a href="#s_4" data-toggle="tab"><?php echo $msg_settings279; ?></a></li>
          <li><a href="#s_5" data-toggle="tab"><?php echo $msg_settings201; ?></a></li>
          <li><a href="#s_6" data-toggle="tab"><?php echo $msg_settings261; ?></a></li>
          <li><a href="#s_7" data-toggle="tab"><?php echo $msg_settings200; ?></a></li>
          <li><a href="#s_8" data-toggle="tab"><?php echo $msg_settings199; ?></a></li>
          <li><a href="#s_9" data-toggle="tab"><?php echo $msg_admin_settings3_0[19]; ?></a></li>
        </ul>
      </li>
    </ul>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="tab-content">
      <div class="tab-pane active in" id="s_1">
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings164; ?> (%): <?php echo mc_displayHelpTip($msg_javascript330,'RIGHT'); ?></label>
            <input type="text" name="globalDiscount" value="<?php echo ($SETTINGS->globalDiscount ? $SETTINGS->globalDiscount : '0'); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings165; ?>: <?php echo mc_displayHelpTip($msg_javascript331,'LEFT'); ?></label>
            <input type="text" name="globalDiscountExpiry" value="<?php echo ($SETTINGS->globalDiscountExpiry!='0000-00-00' ? mc_convertCalToSQLFormat($SETTINGS->globalDiscountExpiry, $SETTINGS) : ''); ?>" class="box" id="globalDiscountExpiry" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings177; ?>: <?php echo mc_displayHelpTip($msg_javascript357,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="clearSpecialOffers" value="yes"> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="clearSpecialOffers" value="no" checked="checked">
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="s_2">
        <div class="formFieldWrapper">
          <div class="formLeft">
          <label><?php echo $msg_settings49; ?>: <?php echo mc_displayHelpTip($msg_javascript186,'RIGHT'); ?></label>
          <select name="baseCurrency" tabindex="<?php echo ++$tabIndex; ?>" onchange="mc_loadCurrencyDisplay(this.value)">
            <?php
            foreach ($currencies AS $key => $value) {
              echo '<option value="'.$key.'"'.($key==$SETTINGS->baseCurrency ? ' selected="selected"' : '').($key=='' ? ' disabled="disabled"' : '').'>'.$value.'</option>'.mc_defineNewline();
            }
            ?>
            </select>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings18; ?>: <?php echo mc_displayHelpTip($msg_javascript16,'LEFT'); ?></label>
            <?php echo $msg_settings34; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="gatewayMode" value="test"<?php echo ($SETTINGS->gatewayMode=='test' ? ' checked="checked"' : ''); ?>> <?php echo $msg_settings33; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="gatewayMode" value="live"<?php echo ($SETTINGS->gatewayMode=='live' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings95; ?>: <?php echo mc_displayHelpTip($msg_javascript231,'RIGHT'); ?></label>
            <input type="text" name="currencyDisplayPref" value="<?php echo str_replace('&','&amp;',$SETTINGS->currencyDisplayPref); ?>" class="box" maxlength="100" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings83; ?>: <?php echo mc_displayHelpTip($msg_javascript227,'LEFT'); ?></label>
            <select name="shipCountry" tabindex="<?php echo ++$tabIndex; ?>">
            <option value="0">- - - - -</option>
            <?php
            $q_ctry   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`cName` FROM `" . DB_PREFIX . "countries`
                        WHERE `enCountry`  = 'yes'
                        ORDER BY `cName`
                        ") or die(mc_MySQLError(__LINE__,__FILE__));
            while ($CTY = mysqli_fetch_object($q_ctry)) {
            ?>
            <option value="<?php echo $CTY->id; ?>"<?php echo ($SETTINGS->shipCountry==$CTY->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CTY->cName); ?></option>
            <?php
            }
            ?>
            </select>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings132; ?>: <?php echo mc_displayHelpTip($msg_javascript274,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="pendingAsComplete" value="yes"<?php echo ($SETTINGS->pendingAsComplete=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="pendingAsComplete" value="no"<?php echo ($SETTINGS->pendingAsComplete=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings36; ?>: <?php echo mc_displayHelpTip($msg_javascript144,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableSSL" value="yes"<?php echo ($SETTINGS->enableSSL=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableSSL" value="no"<?php echo ($SETTINGS->enableSSL=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_3">
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings183; ?>: <?php echo mc_displayHelpTip($msg_javascript371,'RIGHT'); ?></label>
            <select name="showOutofStock">
            <?php
            foreach (array(
              'cat' => $msg_settings185,
              'yes' => $msg_settings186,
              'no'  => $msg_settings187
            ) AS $k => $v) {
            ?>
            <option value="<?php echo $k; ?>"<?php echo ($SETTINGS->showOutofStock==$k ? ' selected="selected"' : ''); ?>><?php echo $v; ?></option>
            <?php
            }
            ?>
            </select>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings274; ?>: <?php echo mc_displayHelpTip($msg_javascript493,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="showAttrStockLevel" value="yes"<?php echo ($SETTINGS->showAttrStockLevel=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="showAttrStockLevel" value="no"<?php echo ($SETTINGS->showAttrStockLevel=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings158; ?>: <?php echo mc_displayHelpTip($msg_javascript305,'RIGHT'); ?></label>
            <input type="text" name="freeShipThreshold" value="<?php echo ($SETTINGS->freeShipThreshold>0 ? $SETTINGS->freeShipThreshold : '0.00'); ?>" class="box" maxlength="50" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_admin_settings3_0[55]; ?>: <?php echo mc_displayHelpTip($msg_javascript405,'LEFT'); ?></label>
            <div class="categoryBoxes" id="freeshipCountries">
            <input type="checkbox" onclick="mc_toggleCheckBoxesID(this.checked,'freeshipCountries')" tabindex="<?php echo (++$tabIndex); ?>"> <b><?php echo $msg_settings204; ?></b><br>
            <?php
            $q_ctry   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
                        WHERE `enCountry`  = 'yes'
                        ORDER BY `cName`
                        ") or die(mc_MySQLError(__LINE__,__FILE__));
            while ($CTY = mysqli_fetch_object($q_ctry)) {
            ?>
            <input type="checkbox" tabindex="<?php echo (++$tabIndex); ?>" name="freeship[]" value="<?php echo $CTY->id; ?>"<?php echo ($CTY->freeship=='yes' ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CTY->cName); ?><br>
            <?php
            }
            ?>
            </div>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings273; ?>: <?php echo mc_displayHelpTip($msg_javascript492,'LEFT'); ?></label>
            <input type="text" name="productStockThreshold" style="margin-top:5px" value="<?php echo $SETTINGS->productStockThreshold; ?>" class="box" maxlength="5" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_4">
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings191; ?>: <?php echo mc_displayHelpTip($msg_javascript400,'RIGHT'); ?></label>
            <input type="text" name="downloadFolder" value="<?php echo mc_cleanData($SETTINGS->downloadFolder); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings197; ?>: <?php echo mc_displayHelpTip($msg_javascript383,'LEFT'); ?></label>
            <input type="text" name="globalDownloadPath" value="<?php echo mc_cleanData($SETTINGS->globalDownloadPath); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings178; ?>: <?php echo mc_displayHelpTip($msg_javascript362,'RIGHT'); ?></label>
            <input type="text" name="freeDownloadRestriction" value="<?php echo ($SETTINGS->freeDownloadRestriction ? $SETTINGS->freeDownloadRestriction : '0'); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings189; ?>: <?php echo mc_displayHelpTip($msg_javascript378,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="reduceDownloadStock" value="yes"<?php echo ($SETTINGS->reduceDownloadStock=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="reduceDownloadStock" value="no"<?php echo ($SETTINGS->reduceDownloadStock=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings280; ?>: <?php echo mc_displayHelpTip($msg_javascript547,'RIGHT'); ?></label>
            <input type="text" name="freeAltRedirect" value="<?php echo $SETTINGS->freeAltRedirect; ?>" maxlength="250" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings297; ?>: <?php echo mc_displayHelpTip($msg_javascript580,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="downloadRestrictIP" value="yes"<?php echo ($SETTINGS->downloadRestrictIP=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="downloadRestrictIP" value="no"<?php echo ($SETTINGS->downloadRestrictIP=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings298; ?>: <?php echo mc_displayHelpTip($msg_javascript581,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="downloadRestrictIPLog" value="yes"<?php echo ($SETTINGS->downloadRestrictIPLog=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="downloadRestrictIPLog" value="no"<?php echo ($SETTINGS->downloadRestrictIPLog=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings299; ?>: <?php echo mc_displayHelpTip($msg_javascript582,'LEFT'); ?></label>
            <input type="text" style="margin-bottom:5px" name="downloadRestrictIPLock" value="<?php echo ($SETTINGS->downloadRestrictIPLock ? $SETTINGS->downloadRestrictIPLock : '0'); ?>" maxlength="7" class="box" tabindex="<?php echo ++$tabIndex; ?>">
            <?php echo $msg_settings300; ?>: <input type="checkbox" name="downloadRestrictIPMail" value="yes"<?php echo ($SETTINGS->downloadRestrictIPMail=='yes' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings301; ?>: <?php echo mc_displayHelpTip($msg_javascript583.'<b>'.mc_getRealIPAddr().'</b>','RIGHT'); ?></label>
            <input type="text" name="downloadRestrictIPGlobal" value="<?php echo $SETTINGS->downloadRestrictIPGlobal; ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>

          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_5">
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings277; ?>: <?php echo mc_displayHelpTip($msg_javascript500,'RIGHT'); ?></label>
            <input type="text" name="autoClear" value="<?php echo $SETTINGS->autoClear; ?>" class="box" maxlength="3" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[44]; ?>: <?php echo mc_displayHelpTip($msg_javascript500,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="presalenotify" value="yes"<?php echo ($SETTINGS->presalenotify=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="presalenotify" value="no"<?php echo ($SETTINGS->presalenotify=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_admin_settings3_0[45]; ?>: <?php echo mc_displayHelpTip($msg_javascript500,'RIGHT'); ?></label>
            <input type="text" name="presaleemail" value="<?php echo mc_safeHTML($SETTINGS->presaleemail); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[54]; ?>: <?php echo mc_displayHelpTip($msg_javascript370,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="coupontax" value="yes"<?php echo ($SETTINGS->coupontax=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="coupontax" value="no"<?php echo ($SETTINGS->coupontax=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight" style="margin-top:10px">
            <label><?php echo $msg_settings272; ?>: <?php echo mc_displayHelpTip($msg_javascript491,'RIGHT'); ?></label>
            <input type="text" name="minCheckoutAmount" value="<?php echo ($SETTINGS->minCheckoutAmount>0 ? $SETTINGS->minCheckoutAmount : '0.00'); ?>" class="box" maxlength="50" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin3_0[57]; ?>: <?php echo mc_displayHelpTip($msg_javascript370,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="tc" value="yes"<?php echo ($SETTINGS->tc=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="tc" value="no"<?php echo ($SETTINGS->tc=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formLeft" style="margin-top:10px">
            <label><?php echo $msg_admin3_0[58]; ?>: <?php echo mc_displayHelpTip($msg_javascript491,'RIGHT'); ?></label>
            <?php
          if ($SETTINGS->enableBBCode == 'yes') {
            define('BB_BOX2', 'tctext');
            include(PATH . 'templates/bbcode-buttons.php');
          }
          ?>
            <textarea rows="5" cols="30" name="tctext" tabindex="<?php echo (++$tabIndex); ?>" id="tctext"><?php echo mc_safeHTML(mc_cleanData($SETTINGS->tctext)); ?></textarea>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings182; ?>: <?php echo mc_displayHelpTip($msg_javascript370,'LEFT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableCheckout" value="yes"<?php echo ($SETTINGS->enableCheckout=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableCheckout" value="no"<?php echo ($SETTINGS->enableCheckout=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_6">
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings224; ?>: <?php echo mc_displayHelpTip($msg_javascript452,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="offerInsurance" value="yes"<?php echo ($SETTINGS->offerInsurance=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="offerInsurance" value="no"<?php echo ($SETTINGS->offerInsurance=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings225; ?>: <?php echo mc_displayHelpTip($msg_javascript453,'LEFT'); ?></label>
            <input type="text" name="insuranceAmount" value="<?php echo ($SETTINGS->insuranceAmount ? $SETTINGS->insuranceAmount : '0.00'); ?>" class="box" maxlength="10" tabindex="<?php echo ++$tabIndex; ?>">
            <select name="insuranceFilter" style="margin-top:5px" onchange="if(this.value!='op4' && this.value!='op8'){jQuery('#insuranceValue').val('0.00')}">
             <option value="op1"<?php echo ($SETTINGS->insuranceFilter=='op1' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings226; ?></option>
             <option value="op2"<?php echo ($SETTINGS->insuranceFilter=='op2' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings227; ?></option>
             <option value="op3"<?php echo ($SETTINGS->insuranceFilter=='op3' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings228; ?></option>
             <option value="op4"<?php echo ($SETTINGS->insuranceFilter=='op4' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings229; ?></option>
             <option disabled="disabled">- - - - -</option>
             <option value="op5"<?php echo ($SETTINGS->insuranceFilter=='op5' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings230; ?></option>
             <option value="op6"<?php echo ($SETTINGS->insuranceFilter=='op6' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings231; ?></option>
             <option value="op7"<?php echo ($SETTINGS->insuranceFilter=='op7' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings232; ?></option>
             <option value="op8"<?php echo ($SETTINGS->insuranceFilter=='op8' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings233; ?></option>
            </select>
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings262; ?>: <?php echo mc_displayHelpTip($msg_javascript482,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="insuranceOptional" value="yes"<?php echo ($SETTINGS->insuranceOptional=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="insuranceOptional" value="no"<?php echo ($SETTINGS->insuranceOptional=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings263; ?>: <?php echo mc_displayHelpTip($msg_javascript483,'LEFT'); ?></label>
            <input type="text" name="insuranceValue" id="insuranceValue" value="<?php echo ($SETTINGS->insuranceValue>0 ? $SETTINGS->insuranceValue : '0.00'); ?>" class="box" maxlength="20" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>

        <div class="formFieldWrapper">
          <label><?php echo $msg_settings264; ?>: <?php echo mc_displayHelpTip($msg_javascript484,'RIGHT'); ?></label>
          <?php
          if ($SETTINGS->enableBBCode == 'yes') {
            define('BB_BOX', 'insuranceInfo');
            include(PATH . 'templates/bbcode-buttons.php');
          }
          ?>
          <textarea rows="5" cols="30" name="insuranceInfo" tabindex="<?php echo (++$tabIndex); ?>" id="insuranceInfo"><?php echo mc_safeHTML(mc_cleanData($SETTINGS->insuranceInfo)); ?></textarea>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_7">
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings35; ?>: <?php echo mc_displayHelpTip($msg_javascript143,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="logErrors" value="yes"<?php echo ($SETTINGS->logErrors=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="logErrors" value="no"<?php echo ($SETTINGS->logErrors=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings81; ?>: <?php echo mc_displayHelpTip($msg_javascript225,'LEFT'); ?></label>
            <input type="text" name="logFolderName" value="<?php echo mc_cleanData($SETTINGS->logFolderName); ?>" class="box" maxlength="50" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_8">
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_settings82; ?>: <?php echo mc_displayHelpTip($msg_javascript226,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enablePickUp" value="yes"<?php echo ($SETTINGS->enablePickUp=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enablePickUp" value="no"<?php echo ($SETTINGS->enablePickUp=='no' ? ' checked="checked"' : ''); ?>>
          </div>
          <div class="formRight">
            <label><?php echo $msg_settings203; ?>: <?php echo mc_displayHelpTip($msg_javascript405,'LEFT'); ?></label>
            <div class="categoryBoxes" id="localCountries">
            <input type="checkbox" onclick="mc_toggleCheckBoxesID(this.checked,'localCountries')" tabindex="<?php echo (++$tabIndex); ?>"> <b><?php echo $msg_settings204; ?></b><br>
            <?php
            $q_ctry   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
                        WHERE `enCountry`  = 'yes'
                        ORDER BY `cName`
                        ") or die(mc_MySQLError(__LINE__,__FILE__));
            while ($CTY = mysqli_fetch_object($q_ctry)) {
            ?>
            <input type="checkbox" tabindex="<?php echo (++$tabIndex); ?>" name="pickup[]" value="<?php echo $CTY->id; ?>"<?php echo ($CTY->localPickup=='yes' ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CTY->cName); ?><br>
            <?php
            }
            ?>
            </div>
          </div>
          <br class="clear">
        </div>
      </div>
      <div class="tab-pane fade" id="s_9">
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[20]; ?>: <?php echo mc_displayHelpTip($msg_javascript226,'RIGHT'); ?></label>
            <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="pdf[en]" value="yes"<?php echo ($SETTINGS->pdf=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="pdf[en]" value="no"<?php echo ($SETTINGS->pdf=='no' ? ' checked="checked"' : ''); ?>>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[21]; ?>: <?php echo mc_displayHelpTip($msg_javascript226,'RIGHT'); ?></label>
            <select name="pdf[dir]">
              <option value="ltr"<?php echo ($PDF->dir == 'ltr' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_settings3_0[24]; ?></option>
              <option value="rtl"<?php echo ($PDF->dir == 'rtl' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_settings3_0[25]; ?></option>
            </select>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[22]; ?>: <?php echo mc_displayHelpTip($msg_javascript226,'RIGHT'); ?></label>
            <select name="pdf[font]">
              <option value="courier"<?php echo ($PDF->font == 'courier' ? ' selected="selected"' : ''); ?>>Courier</option>
              <option value="dejavusans"<?php echo ($PDF->font == 'dejavusans' ? ' selected="selected"' : ''); ?>>Dejavusans (<?php echo $msg_admin_settings3_0[26]; ?>)</option>
              <option value="helvetica"<?php echo ($PDF->font == 'helvetica' ? ' selected="selected"' : ''); ?>>Helvetica</option>
              <option value="times"<?php echo ($PDF->font == 'times' ? ' selected="selected"' : ''); ?>>Times</option>
            </select>

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[23]; ?>: <?php echo mc_displayHelpTip($msg_javascript225,'LEFT'); ?></label>
            <input type="text" name="pdf[meta]" value="<?php echo mc_cleanData($PDF->meta); ?>" class="box" maxlength="20" tabindex="<?php echo ++$tabIndex; ?>">
          </div>
        </div>
        <div class="formFieldWrapper">
          <div class="formLeft">
            <label><?php echo $msg_admin_settings3_0[27]; ?>: <?php echo mc_displayHelpTip($msg_javascript225,'LEFT'); ?></label>
            <input type="text" name="pdf[company]" value="<?php echo mc_safeHTML(mc_cleanData($PDF->company)); ?>" class="box" maxlength="250" tabindex="<?php echo ++$tabIndex; ?>">

            <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[28]; ?>: <?php echo mc_displayHelpTip($msg_javascript225,'LEFT'); ?></label>
            <textarea rows="5" cols="20" name="pdf[address]" tabindex="<?php echo (++$tabIndex); ?>"><?php echo mc_safeHTML(mc_cleanData($PDF->address)); ?></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<p style="text-align:center">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($_GET['global']) ? $msg_settings212 : $msg_settings8)); ?>" title="<?php echo mc_safeHTML((isset($_GET['global']) ? $msg_settings212 : $msg_settings8)); ?>">
</p>
</form>


</div>
