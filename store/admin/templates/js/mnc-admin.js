function mc_stateLoaderSelect(typ, vl, id) {
  switch(typ) {
    case 'bill':
      mc_stateLoader(
        vl,
        jQuery('select[name="ship[country]"]').val(),
        id
      );
      break;
    case 'ship':
      mc_stateLoader(
        jQuery('select[name="bill[country]"]').val(),
        vl,
        id
      );
      break;
  }
}

function mswPanel(panl) {
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=ajax-ops&op=menupanel&pnl=' + panl,
      dataType: 'json',
      cache: false,
      success: function (data) {
      }
    });
  });
  return false;
}

function mc_stateLoader(cnty, cnty2, acc) {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'p=ajax-ops&op=stateload&bc=' + cnty + '&sc=' + cnty2 + '&ac=' + acc,
      dataType: 'json',
      cache: false,
      success: function (data) {
        mc_CloseSpinner();
        jQuery('#bstbox').html(data['bill_addr']);
        jQuery('#sstbox').html(data['ship_addr']);
      }
    });
  });
  return false;
}

function mc_genTracker() {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=marketing&gentrack=yes',
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        if (data[0] == 'OK') {
          jQuery('input[name="code"]').val(data[1]);
        }
      }
    });
  });
  return false;
}

function mc_testMail() {
  jQuery('#mail_test_area').html('<p style="height:200px">&nbsp;</p>');
  jQuery('#mail_test_area').css('background', 'url(templates/images/doing-something.gif) no-repeat 50% 50%');
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=ajax-ops&op=mailtest',
      dataType: 'json',
      success: function(data) {
        if (data['msg']) {
          jQuery('#mail_test_area').css('background-image', 'none');
          jQuery('#mail_test_area').html('<p style="text-align:center;margin-top:30px"><i class="fa fa-check fa-fw bigfont"></i><br><br>' + data['msg'] + '</p>');
        }
      }
    });
  });
  return false;
}

function mc_apiHandler(ajob) {
  jQuery(document).ready(function() {
    switch(ajob) {
      case 'tweet':
        var tweeted = jQuery('textarea[name="tweet"]').val();
        if (tweeted == '') {
          jQuery('textarea[name="tweet"]').focus();
          return false;
        }
        jQuery('textarea[name="tweet"]').css({
         'background' : 'url(templates/images/loading.gif) no-repeat 50% 50%',
         'color'      : '#c0c0c0'
        });
        jQuery.post('index.php?p=ajax-ops&op=tweet', {
          tweet : tweeted
        },
        function(data) {
          jQuery('textarea[name="tweet"]').css('background','url(templates/images/generating-ok.png) no-repeat 50% 50%');
          jQuery('textarea[name="tweet"]').val('');
          setTimeout(function() {
            jQuery('textarea[name="tweet"]').css('background','none');
            jQuery('textarea[name="tweet"]').focus();
          }, 2000);
          // Did something go wrong?
          if (data[0] == 'ERR') {
            mc_alertBox(data[1]);
          }
        },'json');
        break;
    }
  });
  return false;
}

function mc_saveRestrictedIPs(sale) {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.post('index.php?p=downloads&saveIP=' + sale, {
      ips_update : jQuery('textarea[name="ips"]').val()
    },
    function(data) {
      mc_CloseSpinner();
      jQuery('#ipRestrictionBox').hide();
    }, 'json');
  });
  return false;
}

function mc_newsReset(txt, id) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    mc_ShowSpinner();
    jQuery(document).ready(function() {
      jQuery.ajax({
        url: 'index.php',
        data: 'p=newsletter&reset=' + id,
        dataType: 'json',
        success: function(data) {
          mc_CloseSpinner();
          if (data[0] == 'OK') {
            jQuery('#acc_' + id).slideUp();
          }
        }
      });
    });
    return false;
  } else {
    return false;
  }
}

function mc_checkAddAcc() {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?p=add-account&chkmail=yes',
      data: jQuery("#content > form").serialize(),
      cache: false,
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        switch(data['msg']) {
          case 'OK':
            jQuery('#addform').submit();
            break;
          case 'err':
            mc_alertBox(data['text']);
            break;
        }
      }
    });
  });
  return false;
}

function mc_updateProdExp(id) {
  jQuery('textarea[name="exp[text]"]').css('background', '#fff url(templates/images/loading.gif) no-repeat 50% 50%');
  jQuery(document).ready(function() {
    jQuery.post('index.php?p=add-product&prod_expiry=' + id, {
      e_text : jQuery('textarea[name="exp[text]"]').val(),
      e_price : jQuery('input[name="exp[price]"]').val(),
      e_send : jQuery('input[name="send_' + id + '"]').val(),
      e_special : jQuery('input[name="special_' + id + '"]').val()
    },
    function(data) {
      if (data[0] == 'OK') {
        jQuery('textarea[name="exp[text]"]').css('background-image', 'none');
        jQuery('.alert-warning').slideDown();
      }
    }, 'json');
  });
  return false;
}

function mc_updateAccountMessage(id) {
  jQuery('textarea[name="msg"]').css('background', '#fff url(templates/images/loading.gif) no-repeat 50% 50%');
  jQuery(document).ready(function() {
    jQuery.post('index.php?p=accounts&message=' + id, {
      msg: jQuery('textarea[name="msg"]').val(),
      exp: jQuery('input[name="exp"]').val()
    },
    function(data) {
      if (data[0] == 'OK') {
        jQuery('textarea[name="msg"]').css('background-image', 'none');
        jQuery('.alert-warning').slideDown();
      }
    }, 'json');
  });
  return false;
}

function mc_updateAccountStatus(id) {
  jQuery('textarea[name="reason"]').css('background', '#fff url(templates/images/loading.gif) no-repeat 50% 50%');
  jQuery(document).ready(function() {
    jQuery.post('index.php?p=accounts&accstatus=' + id, {
      reason: jQuery('textarea[name="reason"]').val(),
      status: jQuery('input[name="up_accchk_status_' + id + '"]').val()
    },
    function(data) {
      if (data[0] == 'OK') {
        jQuery('textarea[name="reason"]').css('background-image', 'none');
        jQuery('.alert-warning').slideDown();
      }
    }, 'json');
  });
  return false;
}

function mc_updateProductNotes(id) {
  jQuery('textarea[name="nts"]').css('background', '#fff url(templates/images/loading.gif) no-repeat 50% 50%');
  jQuery(document).ready(function() {
    jQuery.post('index.php?p=manage-products&notes=' + id, {
      notes: jQuery('textarea[name="nts"]').val()
    },
    function(data) {
      if (data[0] == 'OK') {
        jQuery('textarea[name="nts"]').css('background-image', 'none');
        jQuery('.alert-warning').slideDown();
      }
    }, 'json');
  });
  return false;
}

function mc_updateAccountNotes(id) {
  jQuery('textarea[name="nts"]').css('background', '#fff url(templates/images/loading.gif) no-repeat 50% 50%');
  jQuery(document).ready(function() {
    jQuery.post('index.php?p=accounts&notes=' + id, {
      notes: jQuery('textarea[name="nts"]').val()
    },
    function(data) {
      if (data[0] == 'OK') {
        jQuery('textarea[name="nts"]').css('background-image', 'none');
        jQuery('.alert-warning').slideDown();
      }
    }, 'json');
  });
  return false;
}

function mc_updateStock(id, stk, txt) {
  var constk = prompt(txt, stk);
  if (constk) {
    mc_ShowSpinner();
    jQuery(document).ready(function() {
      jQuery.ajax({
        url: 'index.php',
        data: 'p=manage-products&stockUpdate=' + id + '&stock=' + constk,
        dataType: 'json',
        success: function(data) {
          if (data[0] == 'OK') {
            jQuery('#stock_' + id).html(constk);
            mc_CloseSpinner();
          }
        }
      });
    });
    return false;
  } else {
    return false;
  }
}

//--------------------------------------------
// Auto Path Fill
//--------------------------------------------

function mc_autoPath(field) {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=settings&autoFillPath=yes',
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        if (data[0] == 'OK') {
          jQuery('input[name="' + field + '"]').val(data[1]);
        }
      }
    });
  });
  return false;
}

//--------------------------------------------
// Resend Gift Certificates
//--------------------------------------------

function mc_resendGiftCert(sale, purchase, gift, txt, wd, ht) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    mc_ShowSpinner();
    jQuery(document).ready(function() {
      jQuery.ajax({
        url: 'index.php',
        data: 'p=sales-view&resendGiftCert=' + sale + '&purID=' + purchase + '&gift=' + gift,
        dataType: 'json',
        success: function(data) {
          mc_CloseSpinner();
          if (data[0] == 'OK') {
            mc_Window(
              '?p=sales-view&resendGiftCert=' + sale + '&purID=' + purchase + '&gift=' + gift + '&ok=yes',
              ht,
              wd,
              ''
            );
          }
        }
      });
    });
  }
  return false;
}

//--------------------------------------------
// Sale deletion
//--------------------------------------------

// Confirm message..
function mc_confSaleDeletion(txt, id) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    mc_ShowSpinner();
    jQuery(document).ready(function() {
      jQuery.ajax({
        url: 'index.php',
        data: 'p=sales&ssdel=' + id,
        dataType: 'json',
        success: function(data) {
          if (data[0] == 'OK') {
            jQuery('#salearea_' + id).slideUp();
          }
          mc_CloseSpinner();
        }
      });
    });
    return false;
  } else {
    return false;
  }
}

//--------------------------------------------
// Next invoice number
//--------------------------------------------

function mc_nextInvoice() {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=sales-view&nextInvoiceNo=yes',
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        if (data[0] == 'OK') {
          jQuery('#invoiceNo').val(data[1]);
        }
      }
    });
  });
  return false;
}

//--------------------------------------------
// Url Slugs
//--------------------------------------------

function mc_slugSuggestions(value,fld) {
  jQuery('input[name="' + fld + '"]').css('background','url(templates/images/loading-box.gif) no-repeat 98% 50%');
  jQuery(document).ready(function() {
    jQuery.post('index.php?p=newpages&urlSlug=yes', {
      slug: value
    },
    function(data) {
      jQuery('input[name="' + fld + '"]').css('background-image','none');
      jQuery('input[name="' + fld + '"]').val(data[0]);
    },'json');
  });
  return false;
}

function mc_slugCleaner(fld) {
  var str = jQuery('input[name="' + fld + '"]').val();
  var slug = str.replace('/', '-');
  var slug = slug.replace(' ', '-');
  var newslug = slug.toString().toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^\u0100-\uFFFF\w\-]/g,'-') // UTF8 char fix..
    .replace(/\-\-+/g, '-')
    .replace(/^-+/, '-')
    .replace(/-+$/, '-');
  jQuery('input[name="' + fld + '"]').val(newslug);
}

//--------------------------------------------
// Batch Updating Product
//--------------------------------------------

function mc_batchAddField(type, id, field, sha, omt) {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=add-product&batchRoutines=' + type + '||' + id + '||' + field,
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        switch(data[0]) {
          case 'include':
            jQuery('#' + id).show();
            if (omt) {
              jQuery('#' + omt).hide();
            }
            break;
          case 'exclude':
            jQuery('#' + id).hide();
            if (omt) {
              jQuery('#' + omt).show();
            }
            break;
        }
      }
    });
  });
  return false;
}

//--------------------------------------------
// ISBN Lookup
//--------------------------------------------

function mc_isbnLookup() {
  if (jQuery('#pName').val() == '') {
    jQuery('#pName').focus();
    return false;
  }
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=add-product&isbnLookup=' + jQuery('#pName').val(),
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        switch(data['name']) {
          case 'key-error':
            mc_alertBox(data['text']);
            break;
          case 'unavailable':
            mc_alertBox(data['text']);
            break;
          case 'none':
            mc_alertBox(data['text']);
            break;
          default:
            if (data['name'] != 'none') {
              jQuery('#pName').val(data['name']);
            } else {
              jQuery('#pName').val('');
            }
            if (data['full_desc'] != 'none') {
              jQuery('#desc').val(data['full_desc']);
            } else {
              jQuery('#desc').val('');
            }
            if (data['short_desc'] != 'none') {
              jQuery('#short_desc').val(data['short_desc']);
            } else {
              jQuery('#short_desc').val('');
            }
            break;
        }
      }
    });
  });
  return false;
}

//--------------------------------------------
// Attribute boxes
//--------------------------------------------

function mc_manageAttributeBoxes(act) {
  var hmany = jQuery('.attributearea tbody tr').length;
  switch(act) {
    case 'add':
      jQuery('.attributearea tbody tr').last().after(jQuery('.attributearea tbody tr').last().clone());
      jQuery('.attributearea tbody tr input[name="name[]"]').last().val('');
      jQuery('.attributearea tbody tr input[name="cost[]"]').last().val('0.00');
      jQuery('.attributearea tbody tr input[name="weight[]"]').last().val('0');
      jQuery('.attributearea tbody tr input[name="stock[]"]').last().val('1');
      jQuery('.attributearea tbody tr select[name="order[]"]').append('<option value="' + parseInt(hmany + 1) + '">' + parseInt(hmany + 1) + '</option>');
      jQuery('.attributearea tbody tr input[name="attid[]"]').last().val('nb' + parseInt(hmany + 1));
      jQuery('.attributearea tbody tr select[name="order[]"]').last().val(parseInt(hmany + 1));
      break;
    case 'minus':
      if (hmany > 1) {
        jQuery('.attributearea tbody tr').last().remove();
        jQuery('.attributearea tbody option[value="' + hmany + '"]').remove();
      }
      break;
  }
}

//--------------------------------
// Attribute Operations
//--------------------------------

function mc_addAttributeToSale(value, purchase, product, manual) {
  if (value == '0') {
    return false;
  }
  if (value == 'close') {
    mc_remAttrDropDown(purchase);
    return false;
  }
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'p=sales-view&addAttribute=' + value + '&product=' + product,
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        var html = '<tr id="attr_' + purchase + '_' + value + '">';
        html += ' <td><input type="hidden" name="attr_qty[' + purchase + '][' + value + ']" value="' + data['qty'] + '"><input type="hidden" name="attr_id[' + purchase + '][' + value + ']" value="' + data['id'] + '"><input type="text" class="box" name="attr[' + purchase + '][' + value + ']" value="' + data['name'] + '"></td>';
        html += ' <td><div class="form-group input-group">';
        html += ' <input type="text" class="box addon-no-radius-right" name="attr_cost[' + purchase + '][' + value + ']" value="' + data['cost'] + '">';
        html += ' <span class="input-group-addon"><a href="#" onclick="mc_hideAttrBox(\'' + purchase + '\',\'' + value + '\');return false"><i class="fa fa-times fa-fw mc-red"></i></a>';
        html += '</div></td></tr>';
        jQuery('#sel_' + purchase + '_' + value).hide();
        jQuery('#attsel_' + purchase).hide();
        if (jQuery('#prodAttrArea_' + purchase + ' .table').html()) {
          jQuery('#prodAttrArea_' + purchase + ' .table tbody tr').last().after(html);
        } else {
          jQuery('#prodAttrArea_' + purchase + ' hr').after('<div class="table-responsive"><table class="table"><tbody>' + html + '</tbody></div></div>');
        }
        jQuery('#s_' + purchase).val('0');
        jQuery('#alinks_' + purchase).show();
      }
    });
  });
  return false;
}

function mc_hideAttrBox(purchase, attr) {
  jQuery('#attr_' + purchase + '_' + attr).remove();
  // Have all attributes been removed? If yes, clear div completely..
  if (jQuery('#prodAttrArea_' + purchase + ' table tr').length == 0) {
    jQuery('#prodAttrArea_' + purchase + ' .table-responsive').remove();
  }
  jQuery('#sel_' + purchase + '_' + attr).show();
}

function mc_remAttrDropDown(purchase) {
  jQuery('#attsel_' + purchase).slideUp();
  jQuery('#alinks_' + purchase).show();
}

function mc_hideAttr(purchase) {
  jQuery('#attsel_' + purchase).slideDown();
  jQuery('#alinks_' + purchase).hide();
}

//--------------------------------------------
// Loads preview window via jquery post
//--------------------------------------------

function mc_loadPreviewWindow(box, page) {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.post('index.php?prevTxtBox=' + page, {
        boxdata: jQuery('#' + box).val()
      },
      function(data) {
        mc_CloseSpinner();
        if (data['resp'] == 'OK') {
          jQuery('#iframe').html('<iframe id="ifm" src="index.php?prevTxtBox=show&d=' + Date.now() + '"></iframe><br><a href="#" onclick="jQuery(\'#iframe\').slideUp(function(){jQuery(\'#ifm\').remove()});return false"><i class="fa fa-times fa-fw"></i></a>');
          jQuery('#ifm').attr('src', jQuery('#ifm').attr('src'));
          jQuery('#iframe').slideDown();
        }
      }, 'json');
  });
  return false;
}

//--------------------------------------------
// Load local files
//--------------------------------------------

function mc_loadLocalFiles() {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=add-product&showLocalFiles=yes',
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        if (data[0] != 'ERR') {
          jQuery('#pathlocator').html(data[0]);
          jQuery('div[class="localFile"]').show();
        } else {
          jQuery('#fileError').show();
          jQuery('#fileList').hide();
          jQuery('div[class="localFile"]').show();
        }
      }
    });
  });
  return false;
}

//--------------------------------------------
// Delete category icon
//--------------------------------------------

function mc_deleteCategoryIcon(icon, id) {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'p=categories&removeIcon=' + id + '&icon=' + icon,
      dataType: 'json',
      success: function(data) {
        if (data[0] == 'OK') {
          jQuery('.iconarea').hide();
          jQuery('#resetArea').remove();
          mc_CloseSpinner();
        }
      }
    });
  });
  return false;
}

//--------------------------------------------
// ONE CLICK ENABLE/DISABLE PRODUCT
//--------------------------------------------

function mc_enableDisableProduct(id) {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=manage-products&changeStatus=' + id,
      dataType: 'json',
      cache: false,
      success: function(data) {
        mc_CloseSpinner();
        jQuery('#endis_' + id).html(data['status']);
      }
    });
  });
  return false;
}

//--------------------------------------------
// EDIT STATUS MANAGEMENT
//--------------------------------------------

function mc_updateStatus(id) {
  jQuery('textarea[name="notes"]').css('background', '#fff url(templates/images/loading.gif) no-repeat 50% 50%');
  jQuery(document).ready(function() {
    jQuery.post('index.php?p=sales-update&statnotes=' + id, {
      notes: jQuery('textarea[name="notes"]').val(),
      sts: jQuery('#stopt option:selected').val(),
      visa: jQuery('input[name="up_chk_status_' + id + '"]').val()
    },
    function(data) {
      if (data[0] == 'OK') {
        jQuery('textarea[name="notes"]').css('background-image', 'none');
        jQuery('div[class="alert alert-warning"]').slideDown();
      }
    }, 'json');
  });
  return false;
}

//--------------------------------------------
// RELOAD PRICES ON QTY CHANGE
//--------------------------------------------

function mc_displayPurchaseProductPrices(id, url) {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?p=' + url + '&ppreload=' + id,
      data: jQuery("#form_field > form").serialize(),
      cache: false,
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        jQuery('#price_' + id).val(data['price']);
        if (jQuery('#attr_' + id)) {
          jQuery('#attr_' + id).val(data['attr']);
          jQuery('#attrh_' + id).val(data['attr']);
        }
        if (jQuery('#pers_' + id)) {
          jQuery('#pers_' + id).val(data['pers']);
          jQuery('#persh_' + id).val(data['pers']);
        }
        jQuery('#highlight_' + id).html(data['hlite']);
      }
    });
  });
  return false;
}

//--------------------------------------------
// STATUS TEXT MANAGEMENT
//--------------------------------------------

function mc_addNewStatus(txt) {
  var usrsel = prompt(txt);
  if (usrsel) {
    if (jQuery('input[name="sref"]')) {
      jQuery('input[name="sref"]').remove();
    }
    jQuery('#copym').append('<input type="hidden" name="sref" value="' + usrsel + '">');
    mc_addOrderStatus();
  } else {
    return false;
  }
  return false;
}

function mc_addOrderStatus() {
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      type: 'POST',
      url:  'index.php?p=sales-update&sstatus=add',
      data: jQuery("#form_field > form").serialize(),
      cache: false,
      dataType: 'json',
      success: function (data) {
        mc_CloseSpinner();
        switch(data['message']) {
          case 'OK':
            break;
          default:
            if (data['message']) {
              mc_alertBox(data['message']);
            }
            break;
        }
      }
    });
  });
  return false;
}

function mc_statusOption(sbj, txt) {
  setTimeout(function() {
    mc_CloseSpinner()
    jQuery('input[name="title"]').val(sbj);
    jQuery('textarea[name="text"]').val(txt);
    jQuery('input[name="search_statuses"]').val('');
  }, 1000);
}

//--------------------------------------------
// NEWSLETTER UPDATES
//--------------------------------------------

function mc_SaveMailTemplate() {
  if (jQuery('input[name="subject"]').val() == '') {
    jQuery('input[name="subject"]').focus();
    return false;
  }
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?p=newsletter&saveTemplate=yes',
      data: jQuery("#content > form").serialize(),
      cache: false,
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        mc_alertBox('"' + jQuery('input[name="subject"]').val() + '"\n\n' + data[0]);
      }
    });
  });
  return false;
}

//--------------------------------------------------
// RE-CALCULATE TOTALS
// Recalculates totals when viewing or adding sale
//--------------------------------------------------

function mc_recalculateManualTotals() {
  mc_ShowSpinner();
  jQuery('#process_add_sale').val('refresh-prices');
  jQuery(document).ready(function() {
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?p=sales-add',
      data: jQuery("#form_field > form").serialize(),
      cache: false,
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        jQuery('#subTotal').val(data['sub']);
        jQuery('#taxPaid').val(data['tax']);
        jQuery('#grandTotal').val(data['grand']);
        jQuery('#globalTotal').val(data['global']);
        jQuery('#manualDiscount').val(data['manual']);
        jQuery('#couponTotal').val(data['coupon']);
        jQuery('#cartWeight').val(data['weight']);
        jQuery('#process_add_sale').val('yes');
      }
    });
  });
  return false;
}

function mc_recalculateTotals(id) {
  mc_ShowSpinner();
  jQuery('#process_load').val('refresh-prices');
  jQuery(document).ready(function() {
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?p=sales-view&sale=' + id,
      data: jQuery("#form_field > form").serialize(),
      cache: false,
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        jQuery('#subTotal').val(data['sub']);
        jQuery('#taxPaid').val(data['tax']);
        jQuery('#grandTotal').val(data['grand']);
        jQuery('#globalTotal').val(data['global']);
        jQuery('#manualDiscount').val(data['manual']);
        jQuery('#couponTotal').val(data['coupon']);
        jQuery('#cartWeight').val(data['weight']);
        jQuery('#process_load').val('yes');
      }
    });
  });
  return false;
}

//--------------------------------------------
// LOAD SHIPPING PRICE
// Loads shipping service price into box
//--------------------------------------------

function mc_loadShippingPrice(sub_total) {
  jQuery(document).ready(function() {
    if (jQuery('#setShipRateID').val() == 'pickup' || jQuery('#setShipRateID').val() == 'na') {
      jQuery('#shipTotal').val('0.00');
      return false;
    }
    mc_ShowSpinner();
    jQuery.get('index.php', {
      p: 'sales-view',
      service: jQuery('#setShipRateID').val(),
      price: sub_total,
      weight: jQuery('#cartWeight').val(),
      qtys: jQuery('select[name="qty[]"]').serializeArray(),
      pids: jQuery('input[name="prod_id[]"]').serializeArray()
    },
    function(data) {
      mc_CloseSpinner();
      jQuery('#shipTotal').val(data['price']);
    }, 'json');
  });
}

//--------------------------------------------
// SET ZONE TAX
// Updates zone tax rate if area/zone changed
//--------------------------------------------

function mc_setZoneTax() {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.get('index.php', {
      p: 'sales-view',
      z: jQuery('#shipSetArea').val()
    },
    function(data) {
      mc_CloseSpinner();
      jQuery('#tax-span').html(data['taxrate']);
      jQuery('#taxRate').val(data['taxrate']);
    }, 'json');
  });
}

//--------------------------------------------
// RELOAD COUNTRIES
// Reloads countries on sales page
//--------------------------------------------

function mc_reloadCountry() {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.get('index.php', {
      p: 'sales-view',
      c: jQuery('#shipSetCountry').val()
    },
    function(data) {
      mc_CloseSpinner();
      jQuery('#shipSetArea').html('');
      jQuery('#setShipRateID').html('');
      jQuery('#tax-span').html(data['taxrate']);
      jQuery('#taxRate').val(data['taxrate']);
      jQuery('#shipSetArea').html(data['areas']);
      jQuery('#setShipRateID').html(data['services']);
    }, 'json');
  });
}


//--------------------------------------------
// CREATE TAGS FROM FIELDS
// Auto creates tags from product fields
//--------------------------------------------

function mc_createTagsFromField(field) {
  jQuery(document).ready(function() {
    if (jQuery('#' + field).val() == '') {
      return false;
    }
    mc_ShowSpinner();
    jQuery.post('index.php', {
      p: 'load-related-products',
      'create-tags': jQuery('#' + field).val(),
      field: field
    },
    function(data) {
      mc_CloseSpinner();
      jQuery('#tags').val(data['text'])
    }, 'json');
  });
}

//--------------------------------------------
// CREATE PICTURE FOLDER
// Creates picture folders
//--------------------------------------------

function mc_createPictureFolder(txt, fldr) {
  var usrsel = prompt(txt);
  if (usrsel) {
    mc_createPicFolder(usrsel, fldr);
  } else {
    return false;
  }
}

function mc_createPicFolder(folder, pfolder) {
  jQuery(document).ready(function() {
    if (folder == '') {
      return false;
    }
    mc_ShowSpinner();
    jQuery.get('index.php', {
      p       : 'product-pictures',
      newfldr : folder
    },
    function(data) {
      mc_CloseSpinner();
      if (data['status'] == 'error') {
        mc_alertBox(data['error']);
      } else {
        jQuery('#folderList').append('<option value="' + data['folder'] + '">' + pfolder + '/' + data['folder'] + '</option>');
        jQuery('#folderList').val(data['folder']);
        mc_alertBox(data['ok']);
      }
    }, 'json');
  });
}

//--------------------------------------------
// CREATE ATTACHMENTS FOLDERS
// Creates attachment folders
//--------------------------------------------

function mc_createAttachmentFolder(txt, fldr) {
  var usrsel = prompt(txt);
  if (usrsel) {
    mc_createFolder(usrsel, fldr);
  } else {
    return false;
  }
}

function mc_createFolder(folder, afolder) {
  jQuery(document).ready(function() {
    if (folder == '') {
      return false;
    }
    mc_ShowSpinner();
    jQuery.get('index.php', {
      p       : 'sales-update',
      newfldr : folder
    },
    function(data) {
      mc_CloseSpinner();
      if (data['status'] == 'error') {
        mc_alertBox(data['error']);
      } else {
        jQuery('#folderList').append('<option value="' + data['folder'] + '">' + afolder + '/' + data['folder'] + '</option>');
        jQuery('#folderList').val(data['folder']);
        mc_alertBox(data['ok']);
      }
    }, 'json');
  });
}

//--------------------------------------------
// LOAD PRODUCTS / CATEGORIES
// Loads category products
//--------------------------------------------

function mc_getCategoryList(id, sel) {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'p=ajax-ops&op=catlist&cat=' + id,
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        jQuery('div[class="productList"]').html(data[0]);
      }
    });
  });
}

function mc_attCntCheck() {
  var cnt = 0;
  jQuery('#prds input[name="product[]"]:checked').each(function() {
    ++cnt;
  });
  if (cnt > 0) {
    jQuery('input[class="btn btn-primary"]').prop('disabled', false);
  } else {
    jQuery('input[class="btn btn-primary"]').prop('disabled', true);
  }
}

function mc_loadProducts(product, current, page) {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'p=load-related-products&cur=' + current + '&pg=' + page + '&pr=' + product + '&sale=' + (page == 'sale' || page == 'saled' ? current : '0') + '&dl=' + (page == 'saled' ? 'yes' : 'no'),
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        jQuery('#products').hide();
        jQuery('#products').html(data[0]);
        jQuery('#products').show();
      }
    });
  });
}

function mc_VersionCheck() {
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'versionCheck=yes&ck=yes',
      dataType: 'json',
      success: function(data) {
        jQuery('.vcheckarea').html(data['html']);
      }
    });
  });
}

function mc_loadHomeProducts(product) {
  jQuery(document).ready(function() {
    mc_ShowSpinner();
    jQuery.ajax({
      url: 'index.php',
      data: 'p=settings&s=4&reload=yes&pr=' + product,
      dataType: 'json',
      success: function(data) {
        mc_CloseSpinner();
        if (data[0]) {
          jQuery('#prodList').html(data[0] + '<a href="#" onclick="mc_catSlctr(\'list\');return false"><i class="fa fa-times fa-fw mc_red"></i></a>');
          jQuery('#prodList').show();
          mc_catSlctr('box');
        }
      }
    });
  });
}

function mc_loadPDF(id,ptype) {
  mc_ShowSpinner();
  switch(id) {
    case 'pdf-inv-batch':
    case 'pdf-slip-batch':
      jQuery(document).ready(function() {
        jQuery.ajax({
          type: 'POST',
          url: 'index.php?p=ajax-ops&op=' + id,
          data: jQuery('#' + ptype + ' > form').serialize(),
          cache: false,
          dataType: 'json',
          success: function (data) {
            mc_CloseSpinner();
            window.location = data['rdr'];
          }
        });
      });
      break;
    default:
      jQuery(document).ready(function() {
        jQuery.ajax({
          url: 'index.php',
          data: 'p=ajax-ops&op=' + ptype + '&pdf=' + id,
          dataType: 'json',
          success: function(data) {
            mc_CloseSpinner();
            window.location = data['rdr'];
          }
        });
      });
      break;
  }
  return false;
}

function mc_CloseSpinner() {
  jQuery('body').css({
    'opacity': '1.0'
  });
  jQuery('div[class="overlaySpinner"]').hide();
}

function mc_ShowSpinner() {
  jQuery('body').css({
    'opacity': '0.7'
  });
  jQuery('.overlaySpinner').css({
    'left': '50%',
    'top': '50%',
    'position': 'fixed',
    'margin-left': -jQuery('.overlaySpinner').outerWidth() / 2,
    'margin-top': -jQuery('.overlaySpinner').outerHeight() / 2
  });
  jQuery('div[class="overlaySpinner"]').show();
}

//--------------------------------------------
// ALERT BOX
//--------------------------------------------

function mc_alertBox(text) {
  alert(text);
}