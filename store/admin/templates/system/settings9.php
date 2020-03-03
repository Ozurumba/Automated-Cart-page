<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT  = mc_getTableData('banners','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
  $CTGY  = ($EDIT->bannerCats ? explode(',',$EDIT->bannerCats) : array());
}
define('CALBOX', 'from|to');
include(PATH . 'templates/js-loader/date-picker.php');
?>
<div id="content">

<?php
$tabIndex = 0;
if (isset($OK)) {
  echo mc_actionCompleted($msg_settings126);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_settings127);
}
if (isset($OK3)) {
  echo mc_actionCompleted($msg_settings129);
}
if (isset($OK4) && $cnt>0) {
  echo mc_actionCompleted($msg_settings128);
}
?>

<form method="post" id="form" action="?p=settings&amp;s=9<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>" enctype="multipart/form-data">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_settings116; ?></p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_settings124; ?>: <?php echo mc_displayHelpTip($msg_javascript258,'RIGHT'); ?></label>
    <input type="text" name="text" value="<?php echo (isset($EDIT->id) ? mc_safeHTML($EDIT->bannerText) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

    <label style="margin-top:10px"><?php echo $msg_settings119; ?>: <?php echo mc_displayHelpTip($msg_javascript256,'RIGHT'); ?></label>
    <input type="text" name="url" value="<?php echo (isset($EDIT->id) ? mc_safeHTML($EDIT->bannerUrl) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

    <label style="margin-top:10px"><?php echo $msg_settings157; ?>: <?php echo mc_displayHelpTip($msg_javascript255,'RIGHT'); ?></label>
    <input type="file" name="image" tabindex="<?php echo ++$tabIndex; ?>">

    <label style="margin-top:10px"><?php echo $msg_settings247; ?>: <?php echo mc_displayHelpTip($msg_javascript474); ?></label>
    <div class="categoryBoxes">
    <input type="checkbox" name="log" tabindex="<?php echo (++$tabIndex); ?>" value="all" onclick="mc_selectAll()"> <b><?php echo $msg_productadd35; ?></b><br>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <p id="cat_<?php echo $CATS->id; ?>"><input onclick="if(this.checked){mc_selectChildren('cat_<?php echo $CATS->id; ?>','on')}else{mc_selectChildren('cat_<?php echo $CATS->id; ?>','off')}" tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="bannerCats[]" value="<?php echo $CATS->id; ?>"<?php echo (isset($EDIT->id) && in_array($CATS->id,$CTGY) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?><br>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <span id="child_<?php echo $CHILDREN->id; ?>">
    &nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" onclick="if(this.checked){mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','on')}else{mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','off')}" type="checkbox" name="bannerCats[]" value="<?php echo $CHILDREN->id; ?>"<?php echo (isset($EDIT->id) && in_array($CHILDREN->id,$CTGY) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="bannerCats[]" value="<?php echo $INFANTS->id; ?>"<?php echo (isset($EDIT->id) && in_array($INFANTS->id,$CTGY) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
    <?php
    }
    ?>
    </span>
    <?php
    }
    ?>
    </p>
    <?php
    }
    ?>
    </div>

    <label style="margin-top:10px"><?php echo $msg_settings249; ?>: <?php echo mc_displayHelpTip($msg_javascript476,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="bannerHome" value="yes"<?php echo (isset($EDIT->bannerHome) && $EDIT->bannerHome=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="bannerHome" value="no"<?php echo (isset($EDIT->bannerHome) && $EDIT->bannerHome=='no' ? ' checked="checked"' : (!isset($EDIT->bannerHome) ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_settings250; ?>: <?php echo mc_displayHelpTip($msg_javascript477,'LEFT'); ?></label>
    <input id="from" type="text" name="bannerFrom" value="<?php echo (isset($EDIT->bannerFrom) && $EDIT->bannerFrom!='0000-00-00' ? mc_enterDatesBox($EDIT->bannerFrom) : ''); ?>" class="box">
    <input id="to" style="margin-top:5px" type="text" name="bannerTo" value="<?php echo (isset($EDIT->bannerTo) && $EDIT->bannerTo!='0000-00-00' ? mc_enterDatesBox($EDIT->bannerTo) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_admin_settings3_0[65]; ?>: <?php echo mc_displayHelpTip($msg_javascript475,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="trade" value="yes"<?php echo (isset($EDIT->trade) && $EDIT->trade=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="trade" value="no"<?php echo (isset($EDIT->trade) && $EDIT->trade=='no' ? ' checked="checked"' : (!isset($EDIT->trade) ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_settings248; ?>: <?php echo mc_displayHelpTip($msg_javascript475,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="bannerLive" value="yes"<?php echo (isset($EDIT->bannerLive) && $EDIT->bannerLive=='yes' ? ' checked="checked"' : (!isset($EDIT->bannerLive) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="bannerLive" value="no"<?php echo (isset($EDIT->bannerLive) && $EDIT->bannerLive=='no' ? ' checked="checked"' : ''); ?>>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update_banners' : 'process_banners'); ?>" value="<?php echo (isset($EDIT->id) ? $EDIT->id : 'yes'); ?>">
 <input type="hidden" name="old_img" value="<?php echo (isset($EDIT->id) ? $EDIT->bannerFile : ''); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo (isset($EDIT->id) ? mc_cleanDataEntVars($msg_settings118) : mc_cleanDataEntVars($msg_settings120)); ?>" title="<?php echo (isset($EDIT->id) ? mc_cleanDataEntVars($msg_settings118) : mc_cleanDataEntVars($msg_settings120)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="btn btn-success" onclick="window.location=\'?p=settings&amp;s=9\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form><br><br>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_settings123; ?>:</p>
</div>

<p id="loader" style="display:none"></p>
<?php
if (mc_rowCount('banners')>0) {
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery("#sortable").sortable({
    update : function (data) {
      jQuery("#loader").load("index.php?p=settings&s=9&order=yes&"+jQuery('#sortable').sortable('serialize'));
    }
  });
});
//]]>
</script>
<?php
}
?>

<div id="sortable">
<?php
$q_banners = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "banners`
             ORDER BY `bannerOrder`
             ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_banners)>0) {
  while ($BANNERS = mysqli_fetch_object($q_banners)) {
  if ($BANNERS->trade=='yes') {
    $fldr = str_replace('{theme}',TRADE_THEME_FOLDER,BANNER_FOLDER);
  } else {
    $fldr = str_replace('{theme}',THEME_FOLDER,BANNER_FOLDER);
  }
  ?>
  <div class="panel panel-default banners" id="ban-<?php echo $BANNERS->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_cats20); ?>">
    <div class="panel-body">
      <img src="../<?php echo $fldr; ?>/<?php echo $BANNERS->bannerFile; ?>" alt="<?php echo mc_safeHTML($BANNERS->bannerText); ?>" title="<?php echo mc_safeHTML($BANNERS->bannerText); ?>" class="img-responsive">
      <div class="info" style="cursor:move">
        <?php echo $msg_settings251; ?>: <?php echo ($BANNERS->bannerText ? mc_safeHTML($BANNERS->bannerText) : 'N/A'); ?><br>
        <?php echo $msg_settings252; ?>: <?php echo ($BANNERS->bannerUrl ? mc_safeHTML($BANNERS->bannerUrl) : 'N/A'); ?><br>
        <?php echo $msg_settings253; ?>: <?php echo ($BANNERS->bannerHome=='yes' ? $msg_script5 : $msg_script6); ?><br>
        <?php echo $msg_settings254; ?>: <?php echo ($BANNERS->bannerCats ? count(explode(',',$BANNERS->bannerCats)) : 'N/A'); ?><br>
        <?php echo $msg_settings255; ?>: <?php echo ($BANNERS->bannerFrom!='0000-00-00' && $BANNERS->bannerTo!='0000-00-00' ? mc_enterDatesBox($BANNERS->bannerFrom).' - '.mc_enterDatesBox($BANNERS->bannerTo) : 'N/A'); ?><br>
        <?php echo $msg_admin_settings3_0[66]; ?>: <?php echo ($BANNERS->trade=='yes' ? $msg_script5 : $msg_script6); ?><br>
        <?php echo $msg_settings256; ?>: <?php echo ($BANNERS->bannerLive=='yes' ? $msg_settings71 : $msg_settings72); ?><br>
      </div>
    </div>
    <div class="panel-footer">
      <a href="?p=settings&amp;s=9&amp;edit=<?php echo $BANNERS->id; ?>"><i class="fa fa-pencil fa-fw"></i></a>
      <?php
      if ($uDel=='yes') {
      ?>
      <a href="?p=settings&amp;s=9&amp;del=<?php echo $BANNERS->id; ?>&amp;file=<?php echo $fldr; ?>/<?php echo $BANNERS->bannerFile; ?>" title="<?php echo mc_cleanDataEntVars($msg_script10); ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
      <?php
      }
      ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_settings125; ?></span>
<?php
}
?>
</div>


</div>
