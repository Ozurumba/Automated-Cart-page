<?php if (!defined('PARENT') || !isset($_GET['tmail'])) { exit; }
if ($SETTINGS->email != 'email@example.com') {
  $emails = array($SETTINGS->email);
} else {
  $emails = array();
}
if ($SETTINGS->addEmails) {
  foreach (explode(',',$SETTINGS->addEmails) AS $oe) {
    $emails[] = trim($oe);
  }
}
?>
<div id="windowcontent">

  <div class="fieldHeadWrapper">
    <i class="fa fa-envelope-o fa-fw"></i> <?php echo $msg_smtp_settings[0]; ?>
  </div>

  <div id="mail_test_area">
    <?php
    if (!empty($emails)) {
      echo $msg_smtp_settings[1];
      ?><br><br>
      <?php
      foreach ($emails AS $e) {
      ?>
      <i class="fa fa-envelope fa-fw"></i> <?php echo $e; ?><br>
      <?php
      }
      echo '<br>'.$msg_smtp_settings[2].'<br><br>';
      ?>
      <p style="text-align:center">
       <button type="button" class="btn btn-info btn-sm" onclick="mc_testMail()"><i class="fa fa-envelope-o fa-fw"></i> <?php echo $msg_smtp_settings[0]; ?></button>
      </p>
      <?php
    } else {
      echo '<span class="mm_red"><i class="fa fa-warning fa-fw"></i> '.$msg_smtp_settings[3].'</span>';
    }
    ?>
  </div>

</div>