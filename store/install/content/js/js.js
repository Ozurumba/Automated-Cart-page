function _upgrading(stage) {
  jQuery.ajax({
    url: 'upgrade.php',
    data: 'upgrade=1&action=' + stage,
    dataType: 'json',
    success: function (data) {
      switch(data[0]) {
        case 'err':
          alert('Connection to database lost, upgrade aborted. Try running upgrade again.\n\nIf this persists, contact your web host.');
          break;
        default:
          if (data[0] == 'done') {
            window.location='upgrade.php?completed=yes';
          } else {
            if (stage == 'start') {
              jQuery('#op_start').html('<i class="fa fa-check fa-fw"></i>');
              jQuery('#op_1').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
            } else {
              jQuery('#op_' + stage + '_td').removeClass('mc_green');
              jQuery('#op_' + stage).html('<i class="fa fa-check fa-fw"></i>');
              jQuery('#op_' + data[0] + '_td').addClass('mc_green');
              jQuery('#op_' + data[0]).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
            }
            jQuery('.progvalue b').html(data[1] + '%');
            jQuery('.progress div').attr('aria-valuenow', data[1]);
            jQuery('.progress div').attr('style', 'width:' + data[1] + '%');
            _upgrading(data[0]);
          }
          break;
      }
    }
  });
  return false;
}

function _check() {
  if (jQuery('input[name="website"]').val() == '') {
    jQuery('input[name="website"]').focus();
    return false;
  }
  if (jQuery('input[name="email"]').val() == '') {
    jQuery('input[name="email"]').focus();
    return false;
  }
  return true
}

function _confm() {
  var confirmSub = confirm('Upgrading may take several minutes depending on the size of your database.\n\nClick "OK" to proceed..');
  if (confirmSub) {
    window.location = 'upgrade.php?upgrade=1';
  }
  return false;
}

function _warning() {
   var confirmSub = confirm('CONFIRM INSTALLATION\n\nPlease confirm you want to clean install this software?\n\nClick "OK" to proceed..');
   if (confirmSub) {
     return true;
   } else {
     return false;
   }
 }
