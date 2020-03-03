function mc_popFlds(fld, vle) {
  jQuery('input[name="bill[' + fld + ']"]').val(vle);
  jQuery('input[name="ship[' + fld + ']"]').val(vle);
}

function mc_clearReqFlag(id) {
  jQuery('#' + id).removeClass('highlightReq');
  if (jQuery('#perbx')) {
    jQuery('#perbx').remove();
  }
}

function mc_clearGiftFlag() {
  if (jQuery('#gftbx')) {
    jQuery('#gftbx').remove();
  }
}

function mc_prefill(flds) {
  // Billing..
  jQuery('input[name="bill[nm]"]').val(flds[0]['nm']);
  jQuery('input[name="bill[em]"]').val(flds[0]['em']);
  jQuery('select[name="bill[country]"]').val(flds[0]['addr1']);
  jQuery('input[name="bill[1]"]').val(flds[0]['addr2']);
  jQuery('input[name="bill[2]"]').val(flds[0]['addr3']);
  jQuery('input[name="bill[3]"]').val(flds[0]['addr4']);
  if (jQuery('input[name="bill[4]"]').html()) {
    jQuery('input[name="bill[4]"]').val(flds[0]['addr5']);
  } else {
    jQuery('select[name="bill[4]"]').val(flds[0]['addr5']);
  }
  jQuery('input[name="bill[5]"]').val(flds[0]['addr6']);
  // Shipping..
  jQuery('input[name="ship[nm]"]').val(flds[1]['nm']);
  jQuery('input[name="ship[em]"]').val(flds[1]['em']);
  jQuery('select[name="ship[country]"]').val(flds[1]['addr1']);
  jQuery('input[name="ship[1]"]').val(flds[1]['addr2']);
  jQuery('input[name="ship[2]"]').val(flds[1]['addr3']);
  jQuery('input[name="ship[3]"]').val(flds[1]['addr4']);
  if (jQuery('input[name="ship[4]"]').html()) {
    jQuery('input[name="ship[4]"]').val(flds[1]['addr5']);
  } else {
    jQuery('select[name="ship[4]"]').val(flds[1]['addr5']);
  }
  jQuery('input[name="ship[5]"]').val(flds[1]['addr6']);
  jQuery('input[name="ship[6]"]').val(flds[1]['addr7']);
}

function mc_reloadBasket(opt) {
  switch(opt) {
    case 'show':
      jQuery('#mc_chk_payment').hide();
      jQuery('#formfield').fadeIn(600);
      jQuery('.paymentstatpanel').hide();
      break;
    case 'hide':
      jQuery('#mc_chk_payment').fadeIn(600);
      jQuery('#formfield').hide();
      jQuery('.paymentstatpanel').show();
      break;
  }
}

function mc_methSel() {
  var mcval = jQuery('select[name="payment-type"]').val();
  if (mcval) {
    var mccur = jQuery('.paymenttotalsarea .gatewayinfo a').attr('href');
    var mcbase = jQuery('base').attr('href');
    if (mccur.indexOf('help=') == -1) {
      var newurl = mcbase + 'help/' + mcval;
    } else {
      var newurl = mcbase + '?help=' + mcval;
    }
    var icon = jQuery('.paymenttotalsarea .pay-icon a img').attr('src');
    if (icon) {
      var icon2 = icon.split('/');
      var icon3 = icon2[icon2.length-1];
      var icon4 = icon.replace(icon3, mcval + '.png');
      jQuery('.paymenttotalsarea .pay-icon a img').attr('src', icon4);
    }
    jQuery('.paymenttotalsarea .gatewayinfo a').attr('href', newurl);
    jQuery('.paymenttotalsarea .pay-icon a').attr('href', newurl);
  }
}

function mc_chkShowNav(num) {
  for (var i = 1; i < 8; i++) {
    if (i != num) {
      jQuery('#chkt' + i).hide();
    }
  }
  switch(num) {
    case 1:
      jQuery('#chkt' + num).fadeIn(500);
      break;
    default:
      jQuery('#chkt' + num).fadeIn(500);
      break;
  }
}

function mc_reloadTotals(dt) {
  if (dt['totals'] != undefined && jQuery('.paymenttotalsarea').html()) {
    jQuery('.paymenttotalsarea .table tbody').html(dt['totals']);
  }
  if (dt['grand'] != undefined && jQuery('.grandtotal').html()) {
    jQuery('.grandtotal .grand').html(dt['grand']);
  }
  if (dt['hidden'] != undefined) {
    if (jQuery('#mc_hid_boxes').html()) {
      jQuery('#mc_hid_boxes').remove();
    }
    jQuery('#mc_chk_payment form').append('<div style="display:none !important" id="mc_hid_boxes">' + dt['hidden'] + '</div>');
  }
}

function mc_passFld() {
  var pval = jQuery('input[name="acc[old]"]').val();
  if (pval) {
    jQuery('input[name="acc[pass]"]').prop('disabled', false);
    jQuery('input[name="acc[pass2]"]').prop('disabled', false);
  } else {
    jQuery('input[name="acc[pass]"]').prop('disabled', true);
    jQuery('input[name="acc[pass2]"]').prop('disabled', true);
  }
}

function mc_fieldCopyAccounts(atype) {
  switch(atype) {
    case 'shipping':
      jQuery('#sstbox').html(jQuery('#bstbox').html());
      for (var i = 1; i < 7; i++) {
        switch(i) {
          case 4:
            jQuery('#sstbox input[name="bill[' + i + ']"]').attr('name','ship[4]');
            jQuery('#sstbox select[name="bill[' + i + ']"]').attr('name','ship[4]');
            jQuery('input[name="ship[' + i + ']"]').val(jQuery('input[name="bill[' + i + ']"]').val());
            jQuery('select[name="ship[' + i + ']"]').val(jQuery('select[name="bill[' + i + ']"]').val());
            break;
          default:
            jQuery('input[name="ship[' + i + ']"]').val(jQuery('input[name="bill[' + i + ']"]').val());
            break;
        }
      }
      jQuery('input[name="ship[nm]"]').val(jQuery('input[name="bill[nm]"]').val());
      jQuery('input[name="ship[em]"]').val(jQuery('input[name="bill[em]"]').val());
      jQuery('select[name="ship[country]"]').val(jQuery('select[name="bill[country]"]').val());
      break;
    case 'billing':
      jQuery('#bstbox').html(jQuery('#sstbox').html());
      for (var i = 1; i < 7; i++) {
        switch(i) {
          case 4:
            jQuery('#bstbox input[name="ship[' + i + ']"]').attr('name','bill[4]');
            jQuery('#bstbox select[name="ship[' + i + ']"]').attr('name','bill[4]');
            jQuery('input[name="bill[' + i + ']"]').val(jQuery('input[name="ship[' + i + ']"]').val());
            jQuery('select[name="bill[' + i + ']"]').val(jQuery('select[name="ship[' + i + ']"]').val());
            break;
          default:
            jQuery('input[name="bill[' + i + ']"]').val(jQuery('input[name="ship[' + i + ']"]').val());
            break;
        }
      }
      jQuery('input[name="bill[nm]"]').val(jQuery('input[name="ship[nm]"]').val());
      jQuery('input[name="bill[em]"]').val(jQuery('input[name="ship[em]"]').val());
      jQuery('select[name="bill[country]"]').val(jQuery('select[name="ship[country]"]').val());
      break;
  }
}

function mc_musicPlayer(msfile, msid) {
  jQuery(document).ready(function() {
    soundManager.stopAll();
    jQuery('.musicfile').each(function() {
      if (jQuery(this).attr('id') != msid) {
        jQuery('#' + jQuery(this).attr('id') + ' a.musicplay i').attr('class', 'fa fa-play fa-fw');
      }
    });
    switch(jQuery('#' + msid + ' a.musicplay i').attr('class')) {
      case 'fa fa-play fa-fw':
        jQuery('#' + msid + ' a.musicplay i').attr('class', 'fa fa-stop fa-fw');
        soundManager.createSound({
          id  : msid,
          url : msfile,
          onfinish  : function() {
            jQuery('#' + msid + ' a.musicplay i').attr('class', 'fa fa-play fa-fw');
          }
        });
        soundManager.play(msid);
        break;
      default:
        jQuery('#' + msid + ' a.musicplay i').attr('class', 'fa fa-play fa-fw');
        break;
    }
  });
}

function mc_qtyCheck(max, min, stock) {
  var qtyval = parseInt(jQuery('input[name="qty"]').val());
  if (qtyval) {
    if (qtyval < 0) {
      var qtyval = '1';
    }
    if (min > 0 && qtyval < min) {
      qtyval = min;
    } else if (max > 0 && qtyval > max) {
      qtyval = max;
    } else if (stock > 0 && qtyval > stock) {
      qtyval = stock;
    }
    jQuery('input[name="qty"]').val(qtyval);
  }
  if (qtyval == '0') {
    jQuery('input[name="qty"]').val((min > 0 ? min : '1'));
  }
}

function mc_perCounter(id, maxc, typ) {
  switch(typ) {
    case 'input':
      var blen = jQuery('#per_counter_limit_' + id + ' input[name="personalisation[' + id + ']"]').val().length;
      jQuery('#per_counter_limit_' + id + ' span').html(blen + '/' + maxc);
      mc_clearReqFlag('personalisation_' + id);
      break;
    case 'textarea':
      var blen = jQuery('#per_counter_limit_' + id + ' textarea[name="personalisation[' + id + ']"]').val().length;
      jQuery('#per_counter_limit_' + id + ' span').html(blen + '<br><br>' + maxc);
      mc_clearReqFlag('personalisation_' + id);
      break;
  }
}

function mc_optionsPanel(act, panel) {
  switch(act) {
    case 'open':
      if (panel == 'yes') {
        jQuery('#panelbutton').attr('onclick', 'mc_optionsPanel(\'close\',\'yes\')');
        jQuery('#panelbutton i').attr('class', 'fa fa-arrow-up fa-fw');
      }
      jQuery('.top_optionswrapper').slideDown();
      break;
    default:
      if (panel == 'yes') {
        jQuery('#panelbutton').attr('onclick', 'mc_optionsPanel(\'open\',\'yes\')');
        jQuery('#panelbutton i').attr('class', 'fa fa-cogs fa-fw');
      }
      jQuery('.top_optionswrapper').slideUp();
      break;
  }
}

function mc_menuButton(act) {
  switch(act) {
    case 'open':
      jQuery('#leftpanelbutton #lmitag').attr('class', 'fa fa-arrow-left fa-fw');
      jQuery('#mmwrap').hide();
      break;
    default:
      jQuery('#leftpanelbutton #lmitag').attr('class', 'fa fa-angle-right fa-fw');
      jQuery('#mmwrap').show();
      break;
  }
}

function mc_Window(w_url, w_height, w_width, w_title) {
  if (w_height > 0) {
    iBox.showURL(w_url, '',{
      width  : w_width,
      height : w_height
    });
  } else {
    iBox.showURL(w_url, '');
  }
}

function mc_alertBox(text) {
  alert(text);
}

function mc_CloseSpinner() {
  jQuery('body').css({'opacity' : '1.0'});
  jQuery('div[class="overlaySpinner"]').hide();
}

function mc_ShowSpinner() {
  jQuery('body').css({'opacity' : '0.7'});
  jQuery('.overlaySpinner').css({
    'left' : '50%',
    'top' : '50%',
    'position' : 'fixed',
    'margin-left' : -jQuery('.overlaySpinner').outerWidth()/2,
    'margin-top' : -jQuery('.overlaySpinner').outerHeight()/2
  });
  jQuery('div[class="overlaySpinner"]').show();
}

function mc_showDialog(msg, txt, type) {
  if (jQuery('.bootbox')) {
    jQuery('.bootbox').remove();
  }
  if (jQuery('.modal-backdrop')) {
    jQuery('.modal-backdrop').remove();
  }
  switch(type) {
    case 'err':
      bootbox.dialog({
        message: msg,
        title: '<i class="fa fa-warning fa-fw"></i> ' + txt,
        className: 'mc-box-error',
        onEscape  : true,
        backdrop  : true
      });
      break;
    default:
      bootbox.dialog({
        message: msg,
        title: txt,
        className: 'mc-box-ok',
        onEscape  : true,
        backdrop  : true
      });
      break;
  }
}

function mc_accMenu() {
  mc_Window('index.php?mobileMenu=yes', 450, 500, '');
}

function mc_mobileDetection(curpf) {
  switch(curpf) {
    case 'mobile':
    case 'tablet':
      if (jQuery('.leftmenuwrapper') && jQuery('.rightbodyarea')) {
        var cls = jQuery('.leftmenuwrapper').detach();
        jQuery('.rightbodyarea').after(cls);
        if (jQuery('div[class="row mswpages"]').html()) {
          var pgh = jQuery('div[class="row mswpages"]').detach();
          jQuery('.leftmenuwrapper').prepend(pgh);
        }
      }
      break;
  }
}

function mc_KeyCode(e) {
  var unicode = (e.keyCode ? e.keyCode : e.charCode);
  return unicode;
}