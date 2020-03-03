<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_settings43);
}

?>
<form method="post" id="form" action="?p=payment-methods&amp;conf=<?php echo $_GET['conf']; ?>">
<?php
// Load payment method..
if (file_exists(PATH.'templates/catalogue/payment-methods/'.$_GET['conf'].'.php')) {
  include(PATH.'templates/catalogue/payment-methods/'.$_GET['conf'].'.php');
} else {
  echo '<p>Failed to load template: <b>templates/catalogue/payment-methods/'.$_GET['conf'].'.php</b></p>';
}
?>
</form>

</div>
