function mc_flor(type, vl, ancr) {
  jQuery(document).ready(function() {
    switch(type) {
      case 'brands':
      case 'points':
      case 'cat':
        if (vl.substring(0,4) == 'http') {
          window.location = vl;
        } else {
          mc_ShowSpinner();
          jQuery.ajax({
            url: 'index.php',
            data: 'mc_sys_filters=' + vl + '&t=' + type,
            dataType: 'json',
            cache: false,
            success: function (data) {
              window.location.reload();
            }
          });
        }
        break;
      case 'sort':
        jQuery('.listfilters li').removeClass('active');
        jQuery('.listfilters li a i[class="fa fa-check fa-fw"]').remove();
        jQuery(vl).parent().addClass('active');
        jQuery(vl).append(' <i class="fa fa-check fa-fw"></i>');
        mc_ShowSpinner();
        jQuery.ajax({
          url: 'index.php',
          data: 'mc_sys_sort=' + ancr,
          dataType: 'json',
          cache: false,
          success: function (data) {
            window.location.reload();
          }
        });
        break;
    }
  });
  return false;
}

function mc_layout(bobj, btype) {
  jQuery('.listfilters button').removeClass('buttonlayoutselection');
  jQuery(bobj).addClass('buttonlayoutselection');
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'mc_sys_layout=' + btype,
      dataType: 'json',
      cache: false,
      success: function (data) {
        window.location.reload();
      }
    });
  });
}

function mc_Panel(panl) {
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'cart-ops=menu-panel&pnl=' + panl,
      dataType: 'json',
      cache: false,
      success: function (data) {
      }
    });
  });
  return false;
}

function mc_wishList(id) {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'acop=wish&id=' + id,
      dataType: 'json',
      cache: false,
      success: function (data) {
        mc_CloseSpinner();
        mc_showDialog(data['text'][1], data['text'][0], data['msg']);
      }
    });
  });
  return false;
}

function mc_send() {
  if (jQuery('input[name="msg[nm]"]').val() == '') {
    jQuery('input[name="msg[nm]"]').focus();
    return false;
  }
  if (jQuery('input[name="msg[em]"]').val() == '') {
    jQuery('input[name="msg[em]"]').focus();
    return false;
  }
  if (jQuery('textarea[name="msg[msg]"]').val() == '') {
    jQuery('textarea[name="msg[msg]"]').focus();
    return false;
  }
  if (jQuery('input[name="ct_ts"]')) {
    jQuery('input[name="ct_ts"]').remove();
  }
  var d = new Date();
  jQuery('#msgfootfield form').append('<input type="hidden" name="ct_ts" value="' + d.getFullYear() + '">');

  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?store-msg=yes',
      data: jQuery('#msgfootfield > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        mc_CloseSpinner();
        if (data['msg'] == 'ok') {
          jQuery('#msgfootfield input[type="text"]').val('');
          jQuery('#msgfootfield textarea[name="msg[msg]"]').val('');
        }
        mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
      }
    });
  });
  return false;
}

function mc_mp3Previews(act, id) {
  switch(act) {
    case 'close':
      soundManager.stopAll();
      jQuery('div[class="mp3previewList"]').remove();
      break;
    default:
      jQuery(document).ready(function() {
        mc_ShowSpinner();
        jQuery.ajax({
          url: 'index.php',
          data: 'pMP3=' + id,
          cache: false,
          dataType: 'json',
          success: function (data) {
            mc_CloseSpinner();
            switch(data['msg']) {
              case 'err':
                mc_showDialog(data['text'][1], data['text'][0], 'err');
                break;
              case 'ok':
                if (jQuery('div[class="mp3previewList"]').html()) {
                  jQuery('div[class="mp3previewList"]').remove();
                }
                jQuery('body').append(data['html']).animate({
                  bottom: '300px'
                }, 1500);
                break;
            }
          }
        });
      });
      break;
  }
}

function mc_prodEnquiry(id) {
  if (jQuery('input[name="que[nm]"]') && jQuery('input[name="que[nm]"]').val() == '') {
    jQuery('input[name="que[nm]"]').focus();
    return false;
  }
  if (jQuery('input[name="que[em]"]') && jQuery('input[name="que[em]"]').val() == '') {
    jQuery('input[name="que[em]"]').focus();
    return false;
  }
  if (jQuery('textarea[name="que[msg]"]').val() == '') {
    jQuery('textarea[name="que[msg]"]').focus();
    return false;
  }
  jQuery(document).ready(function() {
   mc_ShowSpinner();
   jQuery.post('index.php?pd=' + id + '&penq=' + id, {
     nm : jQuery('input[name="que[nm]"]').val(),
     em : jQuery('input[name="que[em]"]').val(),
     msg : jQuery('textarea[name="que[msg]"]').val(),
     bk : jQuery('input[name="que[blank]"]').val()
   },
   function(data) {
      mc_CloseSpinner();
      switch(data['msg']) {
        case 'ok':
          jQuery('#four input[type="text"]').val('');
          jQuery('#four textarea[name="que[msg]"]').val('');
          mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
          break;
        case 'err':
          mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
          break;
      }
   },'json');
  });
  return false;
}

function mc_removeBasketItem(code) {
  jQuery('#td-' + code + ' i').attr('class', 'fa fa-refresh fa-spin fa-fw');
  jQuery('#mb-' + code + ' .panel-body').css('opacity', '0.5');
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'cart-ops=delete&code=' + code,
      dataType: 'json',
      cache: false,
      success: function (data) {
        switch(data['msg']) {
          case 'ok':
            jQuery('#mb-' + code).slideUp();
            jQuery('.dialog-total span').html(data['cost']);
            jQuery('.checkout_link span').html(data['count']);
            break;
          case 'empty':
            jQuery('.bootbox .modal-body').html(data['html']);
            jQuery('.checkout_link span').html(data['count']);
            if (jQuery('span[class="clearallbasket"]').html()) {
              jQuery('span[class="clearallbasket"]').remove();
            }
            break;
        }
      }
    });
  });
  return false;
}

function mc_gpdf(code, id) {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'vOrder=' + id + '-' + code + '&pdfshow=yes',
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

function mc_gdl(id, code, sid) {
  var cur  = jQuery('#dl_' + id).attr('class');
  var curi = jQuery('#dl_' + id + ' i').attr('class');
  jQuery('#dl_' + id).attr('class','btn btn-success btn-sm');
  jQuery('#dl_' + id + ' i').attr('class','fa fa-check fa-fw');
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'vOrder=' + sid + '-' + code + '&pdl=' + id,
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

function mc_shipOpts(slt, enbl, wishl) {
  // Is shipping off?
  if (enbl == 'no') {
    return false;
  }
  var shipID = (jQuery('#hid-zone').val() ? jQuery('#hid-zone').val() : jQuery('select[name="ship_code"]').val());
  var cntyID = (jQuery('input[name="ship[country]"').val() ? jQuery('input[name="ship[country]"').val() : jQuery('select[name="ship[country]"]').val());
  if (shipID > 0 && cntyID > 0) {
    if (slt == 'no') {
      mc_ShowSpinner();
    }
    jQuery(document).ready(function() {
      jQuery.ajax({
        url: 'index.php',
        data: 'cart-ops=shipping&a=' + shipID + '&c=' + cntyID,
        dataType: 'json',
        cache: false,
        success: function (data) {
          if (slt == 'no') {
            mc_CloseSpinner();
          }
          if (wishl == 'yes') {
            data['msg'] = 'wish-ok';
          }
          switch(data['msg']) {
            case 'ok':
              if (jQuery('#wishServices')) {
                jQuery('#wishServices').remove();
              }
              jQuery('div[class="shippingOptionsWrapper"]').remove();
              jQuery('select[name="ship_code"]').after(data['html']);
              jQuery('div[class="shippingOptionsWrapper"] label').html(data['txt']);
              if (jQuery('#da1').html()) {
                jQuery('#da1').remove();
              }
              if (jQuery('#da2').html()) {
                jQuery('#da2').remove();
              }
              break;
            case 'wish-ok':
              jQuery('div[class="shippingOptionsWrapper"]').remove();
              jQuery('div[class="ship_sel"]').after('<div id="wishServices">' + data['html'] + '</div>');
              jQuery('div[class="shippingOptionsWrapper"] label').html(data['txt']);
              jQuery('div[class="shippingOptionsWrapper"] hr').hide();
              break;
            case 'free-err':
              mc_reloadBasket('show');
              mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
              break;
            case 'err':
              if (slt == 'no') {
                mc_showDialog(data['txt'][2], data['txt'][1], data['txt'][0]);
              }
              break;
          }
        }
      });
    });
    return false;
  }
  return false;
}

function mc_insurance() {
  var istat = jQuery('.inslink i').attr('class');
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'cart-ops=insurance&st=' + istat,
      dataType: 'json',
      cache: false,
      success: function (data) {
        mc_CloseSpinner();
        switch(data['msg']) {
          case 'ok':
            mc_reloadTotals(data);
            jQuery('.inslink').html('<i class="' + data['class'] + '"></i> ' + data['html']);
            break;
          case 'err':
            mc_showDialog(data['txt'][2], data['txt'][1], data['msg']);
            break;
        }
      }
    });
  });
  return false;
}

function mc_stateLoaderSelect(typ, vl, id, slt) {
  switch(typ) {
    case 'bill':
      mc_stateLoader(
        vl,
        jQuery('select[name="ship[country]"]').val(),
        id,
        slt
      );
      break;
    case 'ship':
      mc_stateLoader(
        jQuery('select[name="bill[country]"]').val(),
        vl,
        id,
        slt
      );
      break;
  }
}

function mc_stateLoader(cnty, cnty2, acc, slt) {
  jQuery(document).ready(function() {
    if (slt == 'no') {
      mc_ShowSpinner();
    }
    jQuery.ajax({
      url: 'index.php',
      data: 'cart-ops=stateload&bc=' + cnty + '&sc=' + cnty2 + '&ac=' + acc,
      dataType: 'json',
      cache: false,
      success: function (data) {
        if (slt == 'no') {
          mc_CloseSpinner();
        }
        jQuery('#bstbox').html(data['bill_addr']);
        jQuery('#sstbox').html(data['ship_addr']);
      }
    });
  });
  return false;
}

function mc_initCheckout(silt) {
  if (silt == 'no') {
    mc_ShowSpinner();
  }
  jQuery(document).ready(function() {
    var fbill = jQuery('select[name="bill[country]"]').val();
    var fship = jQuery('select[name="ship[country]"]').val();
    jQuery.ajax({
      url: 'index.php',
      data: 'cart-ops=checkout-init&bill=' + fbill + '&ship=' + fship,
      dataType: 'json',
      cache: false,
      success: function (data) {
        if (silt == 'no') {
          mc_CloseSpinner();
        }
        jQuery('#bstbox').html(data['bill_addr']);
        jQuery('#sstbox').html(data['ship_addr']);
        jQuery('div[class="shippingOptionsWrapper"]').remove();
        switch(data['msg']) {
          case 'ok':
          case 'wish-ok':
            if (jQuery('#da2').html()) {
              jQuery('#da2').remove();
            }
            if (data['ship_ops']) {
              switch(data['msg']) {
                case 'ok':
                  if (jQuery('#hid-zone')) {
                    jQuery('#hid-zone').remove();
                  }
                  if (jQuery('#wishServices')) {
                    jQuery('#wishServices').remove();
                  }
                  jQuery('select[name="ship_code"]').html(data['ship_ops']);
                  jQuery('#chkt3 div[class="buttons"]').show();
                  jQuery('div[class="ship_sel"]').show();
                  jQuery('select[name="ship_code"]').show();
                  mc_shipOpts('yes','yes','no');
                  break;
                case 'wish-ok':
                  jQuery('#mc_ship_options').append('<input id="hid-zone" type="hidden" name="ship_code" value="' + data['zone'] + '">');
                  jQuery('div[class="ship_sel"]').hide();
                  mc_shipOpts('yes','yes','yes');
                  break;
              }
            } else {
              jQuery('#chkt3 div[class="buttons"]').hide();
              jQuery('select[name="ship_code"]').hide();
              jQuery('#chkt3 label').after('<div class="alert alert-danger" id="da2">' + data['txt'][0] + '</div>');
            }
            break;
          case 'err':
            mc_reloadBasket('show');
            mc_showDialog(data['txt'][1], data['txt'][0], (data['ship_ops'] ? data['ship_ops'] : 'err'));
            break;
        }
        // Data reload..
        mc_reloadTotals(data);
      }
    });
  });
  return false;
}

function mc_deleteBasketItem(slot) {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'cart-ops=delete-check-item&id=' + slot,
      dataType: 'json',
      cache: false,
      success: function (data) {
        if (data['cnt'] > 0) {
          mc_CloseSpinner();
          jQuery('#bsk-' + slot).slideUp();
          jQuery('#mc_btotal').html(data['total']);
          jQuery('.checkout_link span').html(data['count']);
        } else {
          window.location.reload();
        }
      }
    });
  });
  return false;
}

function mc_chkNav(nav, dir) {
  if (dir == 'back') {
    jQuery('a[href="#chkt' + nav + '"]').tab('show');
    return false;
  }
  // Check for stripe..
  switch(nav) {
    case 'pay':
      if (jQuery('select[name="payment-type"]').val() == 'stripe') {
        mc_ShowSpinner();
        mc_Stripe();
        return false;
      }
      break;
    default:
      // Ok, continue..
      break;
  }
  // Remove flag classes..
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?cart-ops=checkout-ops&nav=' + nav,
      data: jQuery('#mc_chk_payment > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        switch(data['msg']) {
          case 'err':
            mc_CloseSpinner();
            mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
            switch(data['nav']) {
              case 'basket':
                mc_reloadBasket('show');
                break;
              default:
                mc_chkShowNav(data['nav']);
                break;
            }
            break;
          case 'err-guest-exists':
            mc_CloseSpinner();
            mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], 'err');
            mc_reloadBasket('show');
            break;
          case 'cty-restr':
            mc_CloseSpinner();
            mc_reloadBasket('show');
            mc_showDialog(data['txt'][0], data['txt'][1], data['ship_ops']);
            var mcflags = data['flags'].split('#####');
            if (mcflags.length > 0) {
              for (var i = 0; i < mcflags.length; i++) {
                jQuery('#' + mcflags[i]).addClass('restrflagged');
              }
            }
            break;
          case 'ok':
            var tabNav = data['nav'];
            switch(tabNav) {
              // Billing address..
              case 2:
                mc_CloseSpinner();
                mc_chkShowNav(tabNav);
                break;
              // Shipping address..
              case 3:
                switch(data['msg']) {
                  case 'ok':
                    if (jQuery('#da1').html()) {
                      jQuery('#da1').remove();
                    }
                    if (jQuery('#da2').html()) {
                      jQuery('#da2').remove();
                    }
                    mc_chkShowNav(tabNav);
                    if (data['ship_ops']) {
                      jQuery('select[name="ship_code"]').html(data['ship_ops']);
                      jQuery('#chkt3 div[class="buttons"]').show();
                      jQuery('select[name="ship_code"]').show();
                    } else {
                      jQuery('#chkt3 div[class="buttons"]').hide();
                      jQuery('select[name="ship_code"]').hide();
                      jQuery('#chkt3 label').after('<div class="alert alert-danger" id="da1">' + data['txt'][0] + '</div>');
                    }
                    // If ship option isn`t selected, do slow scroll..
                    if (jQuery('select[name="ship_code"]').val() == '0') {
                      mc_CloseSpinner();
                    } else {
                      mc_CloseSpinner();
                    }
                    break;
                }
                break;
              // Shipping cost..
              case 4:
                if (nav == 2 && tabNav == 4) {
                  mc_chkShowNav(tabNav);
                  mc_CloseSpinner();
                } else {
                  mc_CloseSpinner();
                  mc_chkShowNav(tabNav);
                }
                mc_reloadTotals(data);
                break;
              // Coupon..
              case 5:
                mc_CloseSpinner();
                mc_chkShowNav(tabNav);
                break;
              // Account..
              case 6:
                mc_CloseSpinner();
                mc_chkShowNav(tabNav);
                break;
              // Notes..
              case 7:
                mc_CloseSpinner();
                mc_chkShowNav(tabNav);
                break;
              // Pay..
              case 'pay':
                if (data['redir']) {
                  window.location = data['redir'];
                } else {
                  mc_CloseSpinner();
                }
                break;
            }
            break;
        }
        // Data reload..
        mc_reloadTotals(data);
      }
    });
  });
  return false;
}

function mc_qty(id,act) {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'cart-ops=update-basket&id=' + id + '&qty=' + parseInt(jQuery('#box-' + id).val()) + '&act=' + act,
      dataType: 'json',
      cache: false,
      success: function (data) {
        mc_CloseSpinner();
        switch(data['msg']) {
          case 'err':
            mc_showDialog(data['text'][1], data['text'][0], 'err');
            break;
          case 'ok':
            if (data['min-checkout'] == 'yes') {
              window.location.reload();
            } else {
              if (data['qty'] > 0) {
                jQuery('#bsk-' + id).attr('class','notclearxx');
                jQuery('#bsk-' + id).html('');
                jQuery('#bsk-' + id).html(data['html']).fadeIn(500);
                jQuery('#mc_btotal').html(data['total']);
                jQuery('#box-' + id).val(data['qty']);
                jQuery('#qty-' + id).html(data['qty']);
                jQuery('.checkout_link span').html(data['count']);
                if (jQuery('div[class="shippingOptionsWrapper"]')) {
                  jQuery('div[class="shippingOptionsWrapper"]').remove();
                  jQuery('select[name="ship_code"]').val('0');
                }
              } else {
                mc_deleteBasketItem(id);
              }
            }
            break;
        }
        // Data reload..
        mc_reloadTotals(data);
      }
    });
  });
}

function mc_reOrder(id) {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'cart-ops=reorder&sale=' + id,
      dataType: 'json',
      cache: false,
      success: function (data) {
        mc_CloseSpinner();
        switch(data['msg']) {
          case 'ok':
            mc_showDialog(data['html'], data['txt'], 'ok');
            jQuery('.checkout_link span').html(data['count']);
            break;
          default:
            mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
            break;
        }
      }
    });
  });
  return false;
}

function mc_addToBasket(id, page) {
  var dest = (page == 'product' ? 'product' : 'category');
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?cart-ops=add&id=' + id + '&loc=' + dest,
      data: jQuery('#formfield > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        mc_CloseSpinner();
        switch(data['msg']) {
          case 'rdr':
            window.location = data['url'];
            break;
          case 'err':
            mc_showDialog(data['text'][1], data['text'][0], 'err');
            if (data['highlight']) {
              switch(data['highlight']) {
                case 'attr':
                  jQuery('#' + data['id']).addClass('highlightReq');
                  jQuery('a[href="#bo_1"]').tab('show');
                  break;
                case 'pers':
                  jQuery('#' + data['id']).addClass('highlightReq');
                  jQuery('a[href="#bo_2"]').tab('show');
                  break;
              }
            }
            break;
          case 'ok':
            mc_showDialog(data['html'], data['txt'], 'ok');
            jQuery('.checkout_link span').html(data['count']);
            break;
        }
        // Data reload..
        mc_reloadTotals(data);
      }
    });
  });
  return false;
}

function mc_chkGift(prod) {
  jQuery(document).ready(function() {
   var bef = jQuery('#cgf_' + prod + ' div[class="panel-footer"] button i').attr('class');
   jQuery('#cgf_' + prod + ' div[class="panel-footer"] button i').attr('class','fa fa-refresh fa-spin fa-fw');
   jQuery.ajax({
      type: 'POST',
      url: 'index.php?cart-ops=gift-edit&prd=' + prod,
      data: jQuery('#cgf_' + prod + ' > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        switch(data['msg']) {
          case 'err':
            if (jQuery('#gftbx')) {
              jQuery('#gftbx').remove();
            }
            jQuery('#cgf_' + prod + ' div[class="panel-body"] div[class="giftarea"]').append('<div id="gftbx" class="alert alert-danger gifterr"><i class="fa fa-warning fa-fw"></i> ' + data['txt'] + '</div>');
            jQuery('#cgf_' + prod + ' div[class="panel-footer"] button i').attr('class', bef);
            break;
          case 'ok':
            iBox.hide();
            mc_ShowSpinner();
            setTimeout(function () {
              jQuery('#bsk-' + prod).attr('class','notclearxx');
              jQuery('#bsk-' + prod).html('');
              jQuery('#bsk-' + prod).html(data['html']).fadeIn(500);
              jQuery('#mc_btotal').html(data['total']);
              mc_CloseSpinner();
            }, 3000);
            break;
        }
      }
    });
  });
  return false;
}

function mc_chkPers(prod) {
  jQuery(document).ready(function() {
   var bef = jQuery('#cpe_' + prod + ' div[class="panel-footer"] button i').attr('class');
   jQuery('#cpe_' + prod + ' div[class="panel-footer"] button i').attr('class','fa fa-refresh fa-spin fa-fw');
   jQuery.ajax({
      type: 'POST',
      url: 'index.php?cart-ops=pers-edit&prd=' + prod,
      data: jQuery('#cpe_' + prod + ' > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        switch(data['msg']) {
          case 'err':
            if (jQuery('#perbx')) {
              jQuery('#perbx').remove();
            }
            jQuery('#cpe_' + prod + ' div[class="panel-body"] div[class="personalisation"]').before('<div id="perbx" class="alert alert-danger perserr"><i class="fa fa-warning fa-fw"></i> ' + data['txt'] + '</div>');
            jQuery('#' + data['id']).addClass('highlightReq');
            jQuery('#cpe_' + prod + ' div[class="panel-footer"] button i').attr('class', bef);
            break;
          case 'ok':
            iBox.hide();
            mc_ShowSpinner();
            setTimeout(function () {
              jQuery('#bsk-' + prod).attr('class','notclearxx');
              jQuery('#bsk-' + prod).html('');
              jQuery('#bsk-' + prod).html(data['html']).fadeIn(500);
              jQuery('#mc_btotal').html(data['total']);
              mc_CloseSpinner();
            }, 3000);
            break;
        }
      }
    });
  });
  return false;
}

function mc_addGift(rst) {
  var fields = ['fn','fe','tn','te'];
  for (var i = 0; i<fields.length; i++) {
    if (jQuery('#formfield input[name="gift[' + fields[i] + ']"]').val() == '') {
      jQuery('#formfield input[name="gift[' + fields[i] + ']"]').focus();
      return false;
    }
  }
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?cart-ops=gift',
      data: jQuery('#formfield > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        mc_CloseSpinner();
        switch(data['msg']) {
          case 'err':
            mc_showDialog(data['html'], data['text'][0], 'err');
            break;
          case 'ok':
            if (rst == 'yes') {
              jQuery('#formfield input[name="gift[fn]"]').val('');
              jQuery('#formfield input[name="gift[fe]"]').val('');
            }
            jQuery('#formfield input[name="gift[tn]"]').val('');
            jQuery('#formfield input[name="gift[te]"]').val('');
            jQuery('#formfield textarea[name="gift[msg]"]').val('');
            mc_showDialog(data['html'], data['txt'], 'ok');
            jQuery('.checkout_link span').html(data['count']);
            break;
        }
        // Data reload..
        mc_reloadTotals(data);
      }
    });
  });
  return false;
}

function mc_loadCatBrands(cat) {
  jQuery(document).ready(function() {
   jQuery('select[name="brand"]').addClass('fspinner_select');
   jQuery.ajax({
    url: 'index.php',
    data: 'p=advanced-search&loadCatBrands=' + cat,
    dataType: 'json',
    cache: false,
    success: function (data) {
      jQuery('select[name="brand"]').removeClass('fspinner_select');
      if (data['brands'] != 'none') {
        jQuery('select[name="brand"]').html(data['brands']);
      } else {
        jQuery('select[name="brand"]').html('<option value="0">- - - - - -</option>');
      }
    }
   });
  });
  return false;
}

function mc_clearRecentView(text) {
  var confirmSub = confirm(text);
  if (confirmSub) {
    mc_ShowSpinner();
    jQuery(document).ready(function() {
      jQuery.ajax({
        url: 'index.php',
        data: 'clearView=yes',
        dataType: 'json',
        cache: false,
        success: function (data) {
          if (data[0] == 'OK') {
            mc_CloseSpinner();
            jQuery('.recentview').hide();
          }
        }
      });
    });
    return false;
  } else {
    return false;
  }
}

function mc_clearAllDialogBasket() {
  jQuery('.dialogcartwrapper .table-responsive .trash i').each(function() {
    jQuery(this).attr('class', 'fa fa-refresh fa-spin fa-fw');
  });
  jQuery('.dialogcartwrapper .panel-body').each(function() {
    jQuery(this).css('opacity', '0.5');
  });
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'cart-ops=clear-dialog-basket',
      dataType: 'json',
      cache: false,
      success: function (data) {
        jQuery('.bootbox .modal-body').html(data['html']);
        jQuery('.checkout_link span').html(data['count']);
        if (jQuery('span[class="clearallbasket"]').html()) {
          jQuery('span[class="clearallbasket"]').remove();
        }
      }
    });
  });
  return false;
}

function mc_clearBasket(txt) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    jQuery(document).ready(function() {
      mc_ShowSpinner();
      jQuery.ajax({
        url: 'index.php',
        data: 'cart-ops=clear-basket',
        dataType: 'json',
        cache: false,
        success: function (data) {
          mc_CloseSpinner();
          if (data['msg'] == 'ok') {
            window.location.reload();
          }
        }
      });
    });
    return false;
  } else {
    return false;
  }
}

function mc_checkout(vis, pfill) {
  switch(vis) {
    case 'acc':
      if (jQuery('input[name="chk[em]"]').val() == '') {
        jQuery('input[name="chk[em]"]').focus();
        return false;
      }
      if (jQuery('input[name="chk[pw]"]').val() == '') {
        jQuery('input[name="chk[pw]"]').focus();
        return false;
      }
      break;
  }
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?cart-ops=checkout&v=' + vis + '&pf=' + pfill,
      data: jQuery('#formfield > form').serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        switch(data['msg']) {
          case 'ok':
            jQuery('.paymentstatpanel .panel-body .row:first-child div:nth-child(2)').html(data['count']);
            jQuery('.paymentstatpanel .panel-body .row:nth-child(2) div:nth-child(2)').html(data['sub']);
            // For accounts, pre-fill all fields and load shipping option..
            // Also, reload payment methods..
            switch(pfill) {
              case 'yes':
                mc_prefill(data['fields']);
                if (jQuery('div[class="payment_methods"]')) {
                  jQuery('div[class="payment_methods"]').remove();
                }
                jQuery('.gatewayinfo').before(data['methods']);
                jQuery('.gatewayinfo a').attr('href', data['meth-url']);
                jQuery('.pay-icon a').attr('href', data['meth-url']);
                jQuery('.pay-icon a img').attr('src', data['meth-img'] + '.png');
                // If account tab is enabled, don`t show it..
                if (jQuery('.liacc').html()){
                  jQuery('.liacc').hide();
                  jQuery('.notesbackbutton').attr('onclick', 'mc_chkShowNav(4)');
                }
                break;
              default:
                break;
            }
            mc_reloadBasket('hide');
            // Remove flags..
            jQuery('.cartwrapper div').each(function(){
              jQuery(this).removeClass('restrflagged');
            });
            mc_CloseSpinner();
            break;
          default:
            mc_CloseSpinner();
            mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], data['msg']);
            break;
        }
        // Data reload..
        mc_reloadTotals(data);
      }
    });
  });
  return false;
}

function mc_shoppingBasket() {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'cart-ops=build-basket',
      dataType: 'json',
      cache: false,
      success: function (data) {
        mc_CloseSpinner();
        switch(data['msg']) {
          case 'ok':
            mc_showDialog(data['html'], data['txt'], 'ok');
            break;
          default:
            mc_showDialog(data['text'][1], data['text'][0], 'err');
            break;
        }
      }
    });
  });
  return false;
}