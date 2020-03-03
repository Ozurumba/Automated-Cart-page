<script>
//<![CDATA[
<?php
// Single box or multiple..
if (strpos(CALBOX,'|')===false) {
?>
jQuery(function() {
  jQuery('#<?php echo CALBOX; ?>').datepicker({
    changeMonth: true,
    changeYear: true,
    monthNamesShort: <?php echo trim($msg_cal5); ?>,
    dayNamesMin: <?php echo trim($msg_cal3); ?>,
    firstDay: '<?php echo $SETTINGS->jsWeekStart; ?>',
    dateFormat: '<?php echo mc_datePickerFormat($SETTINGS); ?>',
    isRTL: false
  });
});
<?php
} else {
$calsplit = explode('|',CALBOX);
foreach ($calsplit AS $cal) {
?>
jQuery(function() {
  jQuery('#<?php echo $cal; ?>').datepicker({
    changeMonth: true,
    changeYear: true,
    monthNamesShort: <?php echo trim($msg_cal5); ?>,
    dayNamesMin: <?php echo trim($msg_cal3); ?>,
    firstDay: '<?php echo $SETTINGS->jsWeekStart; ?>',
    dateFormat: '<?php echo mc_datePickerFormat($SETTINGS); ?>',
    isRTL: false
  });
});
<?php
}
}
?>
//]]>
</script>
