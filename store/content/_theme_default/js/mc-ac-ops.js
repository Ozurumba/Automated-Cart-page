function mc_wishTxt() {
  jQuery('textarea[name="wish"]').addClass('wishboxloader');
  jQuery(document).ready(function() {
    jQuery.post('index.php?acop=wishtext', {
      wtxt : jQuery('textarea[name="wish"]').val()
    },
    function(data) {
      jQuery('textarea[name="wish"]').removeClass('wishboxloader');
      jQuery('.wishlisttext').slideUp();
    },'json');
  });
  return false;
}

function mc_wishZone(id) {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'acop=wishzone',
      dataType: 'json',
      cache: false,
      success: function (data) {
        mc_CloseSpinner();
        jQuery('select[name="ship[zone]"]').html(data['html']);
      }
    });
  });
  return false;
}

function mc_newpass() {
  if (jQuery('input[name="np[e]"]').val() == '') {
    jQuery('input[name="np[e]"]').focus();
    return false;
  }
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?acop=newpass',
      data: jQuery('#formfield > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        mc_CloseSpinner();
        if (data['msg'] == 'ok') {
          jQuery('#formfield input[type="text"]').val('');
        }
        mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
      }
    });
  });
  return false;
}

function mc_pdf(id) {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'acop=pdf&id=' + id,
      dataType: 'json',
      cache: false,
      success: function (data) {
        mc_CloseSpinner();
        switch(data['msg']) {
          case 'ok':
            window.location = data['rdr'];
            break;
          case 'err':
            mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], 'err');
            break;
        }
      }
    });
  });
  return false;
}

function mc_dl(id) {
  var cur  = jQuery('#dl_' + id).attr('class');
  var curi = jQuery('#dl_' + id + ' i').attr('class');
  jQuery('#dl_' + id).attr('class','btn btn-success btn-sm');
  jQuery('#dl_' + id + ' i').attr('class','fa fa-check fa-fw');
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'acop=dl&id=' + id,
      dataType: 'json',
      cache: false,
      success: function (data) {
        mc_CloseSpinner();
        jQuery('#dl_' + id).attr('class', cur);
        jQuery('#dl_' + id + ' i').attr('class', curi);
        switch(data['msg']) {
          case 'ok':
            window.location = data['rdr'];
            break;
          case 'err':
            mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], 'err');
            break;
        }
      }
    });
  });
  return false;
}

function mc_saveSearch(txt,logged) {
  jQuery(document).ready(function() {
    switch(logged) {
      case 'yes':
        var inval = prompt(txt);
        break;
      default:
        var inval = true;
        break;
    }
    if (inval) {
      mc_ShowSpinner();
      jQuery.post('index.php?acop=search', {
          name : inval
        },
        function(data) {
          mc_CloseSpinner();
          mc_showDialog(data['text'][1], data['text'][0], data['msg']);
        },
        'json'
      );
    }
  });
  return false;
}

function mc_delWish(id,txt) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    jQuery(document).ready(function() {
      mc_ShowSpinner();
      jQuery.ajax({
        url: 'index.php',
        data: 'acop=del-wish-list&id=' + id,
        dataType: 'json',
        cache: false,
        success: function (data) {
          mc_CloseSpinner();
          if (data['msg'] == 'ok') {
            jQuery('#wl_' + id).remove();
            var divl = jQuery('.wishlistitems .col-lg-12 tbody tr').length;
            if (divl == 0) {
              var divl = jQuery('.wishlistitems .col-lg-12').html(data['html']);
            }
          }
          mc_showDialog(data['text'][1], data['text'][0], data['msg']);
        }
      });
    });
    return false;
  } else {
    return false;
  }
}

function mc_delSearch(id,txt) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    jQuery(document).ready(function() {
      mc_ShowSpinner();
      jQuery.ajax({
        url: 'index.php',
        data: 'acop=del-saved-search&id=' + id,
        dataType: 'json',
        cache: false,
        success: function (data) {
          mc_CloseSpinner();
          if (data['msg'] == 'ok') {
            jQuery('#ss_' + id).remove();
            var divl = jQuery('.savedsearches .col-lg-12 tbody tr').length;
            if (divl == 0) {
              var divl = jQuery('.savedsearches .col-lg-12').html(data['html']);
            }
          }
          mc_showDialog(data['text'][1], data['text'][0], data['msg']);
        }
      });
    });
    return false;
  } else {
    return false;
  }
}

function mc_passreset() {
  if (jQuery('input[name="np[1]"]').val() == '') {
    jQuery('input[name="np[1]"]').focus();
    return false;
  }
  if (jQuery('input[name="np[2]"]').val() == '') {
    jQuery('input[name="np[2]"]').focus();
    return false;
  }
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?acop=passreset',
      data: jQuery('#formfield > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        mc_CloseSpinner();
        if (data['msg'] == 'ok') {
          jQuery('#formfield input[type="password"]').val('');
        }
        mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
      }
    });
  });
  return false;
}

function mc_profile() {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?acop=profile',
      data: jQuery('#formfield > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        mc_CloseSpinner();
        mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
      }
    });
  });
  return false;
}

function mc_login() {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?acop=login',
      data: jQuery('#formfield > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        mc_CloseSpinner();
        if (data['msg'] == 'ok') {
          jQuery('#formfield input[type="text"]').val('');
          jQuery('#formfield input[type="password"]').val('');
          window.location = data['url'];
        } else {
          mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
        }
      }
    });
  });
  return false;
}

function mc_close(txt) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    jQuery(document).ready(function() {
      mc_ShowSpinner();
      jQuery.ajax({
        url: 'index.php',
        data: 'acop=close',
        dataType: 'json',
        cache: false,
        success: function (data) {
          switch(data['msg']) {
            case 'ok':
              window.location = data['rdr'];
              break;
            case 'err':
              mc_CloseSpinner();
              mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], 'err');
              break;
          }
        }
      });
    });
    return false;
  } else {
    return false;
  }
}

function mc_create() {
  if (jQuery('input[name="ct_ts"]')) {
    jQuery('input[name="ct_ts"]').remove();
  }
  var d = new Date();
  jQuery('#formfield form').append('<input type="hidden" name="ct_ts" value="' + d.getFullYear() + '">');
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?acop=create',
      data: jQuery('#formfield > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        mc_CloseSpinner();
        if (data['msg'] == 'ok') {
          jQuery('#formfield input[type="text"]').val('');
          jQuery('#formfield input[type="password"]').val('');
        }
        mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
      }
    });
  });
  return false;
}