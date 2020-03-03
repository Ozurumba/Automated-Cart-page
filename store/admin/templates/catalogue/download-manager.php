<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<div class="fieldHeadWrapper">
  <p><?php echo $msg_dlmanager2; ?>:</p>
</div>

<?php
// Check download path..
if (is_dir($SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder) &&
  is_writeable($SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder)) {
?>
<div id="elfinder"><!-- Load Elfinder --></div>
<p style="text-align:right;font-size:10px;padding:15px 0 0 0"><?php echo $msg_script3; ?>: <a href="http://elfinder.org/" onclick="window.open(this);return false" title="elFinder">elFinder</a></p>
<?php
} else {
?>
<p class="error">
  <?php echo str_replace(
    array('{sfolder}','{spath}','{path}'),
    array($SETTINGS->downloadFolder,
      $SETTINGS->globalDownloadPath,
      $SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder
    ),
    $msg_dlmanager7
  );
  ?>
</p>
<?php
}
?>

</div>
