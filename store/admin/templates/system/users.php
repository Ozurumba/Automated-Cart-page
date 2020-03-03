<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('users','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_users11);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_users12);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_users13);
}
?>

<form method="post" action="?p=users<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_users3 : $msg_users2); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_users4; ?>: <?php echo mc_displayHelpTip($msg_javascript259,'RIGHT'); ?></label>
    <input type="text" name="userName" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->userName) ? mc_cleanData($EDIT->userName) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_admin_users3_0[0]; ?>: <?php echo mc_displayHelpTip($msg_javascript259,'RIGHT'); ?></label>
    <input type="text" name="userEmail" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->userEmail) ? mc_cleanData($EDIT->userEmail) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_users5; ?>: <?php echo mc_displayHelpTip($msg_javascript260,'RIGHT'); ?></label>
    <input type="text" name="userPass" tabindex="<?php echo (++$tabIndex); ?>" value="" class="box">

    <label style="margin-top:10px"><?php echo $msg_admin_users3_0[1]; ?>: <?php echo mc_displayHelpTip($msg_javascript261,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="userNotify" value="yes"<?php echo (isset($EDIT->userNotify) && $EDIT->userNotify=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input type="radio" name="userNotify" tabindex="4" value="no"<?php echo (isset($EDIT->userNotify) && $EDIT->userNotify=='no' ? ' checked="checked"' : (!isset($EDIT->userNotify) ? ' checked="checked"' : '')); ?>>

  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_users6; ?>: <?php echo mc_displayHelpTip($msg_javascript261,'RIGHT'); ?></label>
    <?php echo $msg_users7; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="userType" value="admin"<?php echo (isset($EDIT->userType) && $EDIT->userType=='admin' ? ' checked="checked"' : ''); ?>> <?php echo $msg_users8; ?> <input type="radio" name="userType" tabindex="4" value="restricted"<?php echo (isset($EDIT->userType) && $EDIT->userType=='restricted' ? ' checked="checked"' : (!isset($EDIT->userType) ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_users10; ?>: <?php echo mc_displayHelpTip($msg_javascript263,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="userPriv" value="yes"<?php echo (isset($EDIT->userPriv) && $EDIT->userPriv=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input type="radio" tabindex="6" name="userPriv" value="no"<?php echo (isset($EDIT->userPriv) && $EDIT->userPriv=='no' ? ' checked="checked"' : (!isset($EDIT->userPriv) ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_admin_users3_0[2]; ?>: <?php echo mc_displayHelpTip($msg_javascript264,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="tweet" value="yes"<?php echo (isset($EDIT->tweet) && $EDIT->tweet=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="7" type="radio" name="tweet" value="no"<?php echo (isset($EDIT->tweet) && $EDIT->tweet=='no' ? ' checked="checked"' : (!isset($EDIT->tweet) ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_users16; ?>: <?php echo mc_displayHelpTip($msg_javascript264,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enableUser" value="yes"<?php echo (isset($EDIT->enableUser) && $EDIT->enableUser=='yes' ? ' checked="checked"' : (!isset($EDIT->enableUser) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="7" type="radio" name="enableUser" value="no"<?php echo (isset($EDIT->enableUser) && $EDIT->enableUser=='no' ? ' checked="checked"' : ''); ?>>
  </div>
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_users9; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <div class="userPageListArea">
    <?php
    $ap = array();
    if (isset($EDIT->accessPages)) {
     $ap = explode('|',$EDIT->accessPages);
    }
    if (!empty($slidePanelLeftMenu)) {
      foreach (array_keys($slidePanelLeftMenu) AS $smk) {
        if (!empty($slidePanelLeftMenu[$smk]['links'])) {
          if ($smk != 'system') {
          ?>
          <hr>
          <?php
          }
          ?>
          <div style="margin-bottom:10px"><input type="checkbox" name="c_<?php echo $smk; ?>" onclick="mc_userBoxes('<?php echo $smk; ?>')"> <i class="fa fa-<?php echo $slidePanelLeftMenu[$smk][1]; ?> fa-fw"></i> <b><?php echo $slidePanelLeftMenu[$smk][0]; ?></b></div>
          <div class="pg_list_<?php echo $smk; ?>">
          <?php
          if ($smk == 'system') {
          ?>
          <hr>
          <?php
          }
          for ($i=0; $i<count($slidePanelLeftMenu[$smk]['links']); $i++) {
            // Shift 3 places..
            $pg = substr($slidePanelLeftMenu[$smk]['links'][$i]['url'], 3);
            // If settings, shift further..
            if (substr($pg, 0, 8) == 'settings') {
              // If ampersand, other settings, else main..
              if (strpos($pg, '&') !== false) {
                $pg = 'settings_' . substr($pg, 15);
              } else {
                $pg = 'settings';
              }
            }
            ?>
            &nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="pages[]" value="<?php echo $pg; ?>"<?php echo (in_array($pg, $ap) ? ' checked="checked"' : ''); ?>> <?php echo $slidePanelLeftMenu[$smk]['links'][$i]['name']; ?><br>
            <?php
          }
          ?>
          </div>
          <?php
        }
      }
    }
    ?>
    </div>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="<?php echo (isset($EDIT->id) ? $EDIT->id : 'yes'); ?>">
 <?php
 if (isset($EDIT->id)) {
 ?>
 <input type="hidden" name="userPass2" value="<?php echo $EDIT->userPass; ?>">
 <?php
 }
 ?>
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_users3 : $msg_users2)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_users3 : $msg_users2)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=users\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form><br>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_users14; ?>:</p>
</div>

<?php
$q_users = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "users`
           ORDER BY `userName`
           ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_users)>0) {
  while ($USERS = mysqli_fetch_object($q_users)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
    <b><?php echo mc_safeHTML($USERS->userName); ?></b> <?php echo ($USERS->userEmail ? '(' . mc_safeHTML($USERS->userEmail) . ')' : ''); ?><br><br>
    <?php echo str_replace(array('{type}','{del_priv}','{enabled}','{notify}','{tweet}'),array(mc_userManagementType($USERS->userType),($USERS->userPriv=='yes' ? $msg_script5 : $msg_script6),($USERS->enableUser=='yes' ? $msg_script5 : $msg_script6),($USERS->userNotify=='yes' ? $msg_script5 : $msg_script6),($USERS->tweet=='yes' ? $msg_script5 : $msg_script6)),$msg_users17); ?>
    </div>
    <div class="panel-footer">
    <a href="?p=users&amp;edit=<?php echo $USERS->id; ?>"><i class="fa fa-pencil fa-fw"></i></a> <?php echo ($uDel=='yes' ? '<a href="?p=users&amp;del='.$USERS->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_users15; ?></span>
<?php
}
?>


</div>
