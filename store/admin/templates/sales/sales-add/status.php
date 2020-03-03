  <?php
  if (!defined('SALE_ADD')) {
    exit;
  }
  ?>
  <label><?php echo $msg_viewsale61; ?>:</label>
  <input type="checkbox" name="writeEditStatus" value="yes" checked="checked" onclick="mc_disableEnableBox(this.checked,'editNotes')"> <?php echo $msg_viewsale52; ?>

  <label style="margin-top:10px"><?php echo $msg_viewsale56; ?>:</label>
  <select name="editStatus" id="selectStat">
  <?php
  $payStatuses = mc_loadDefaultStatuses();
  // Get last status..
  foreach ($payStatuses AS $key => $value) {
  ?>
  <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
  <?php
  }
  // Get additional payment statuses..
  $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                 WHERE `pMethod` IN('all')
                 ORDER BY `pMethod`,`statname`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($q_add_stats)>0) {
  ?>
  <option value="0" disabled="disabled">- - - - - - - - -</option>
  <?php
  }
  while ($ST = mysqli_fetch_object($q_add_stats)) {
  ?>
  <option value="<?php echo $ST->id; ?>"><?php echo mc_cleanData($ST->statname); ?></option>
  <?php
  }
  ?>
  </select>

  <label style="margin-top:10px"><?php echo $msg_viewsale62; ?></label>
  <textarea rows="5" cols="20" name="editNotes" id="editNotes"><?php echo mc_cleanDataEntVars($msg_addsale6); ?></textarea>