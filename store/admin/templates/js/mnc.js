function mc_sendToAPI(wapi, saleid){
  if (jQuery('input[name="api-trigger"]')) {
    jQuery('input[name="api-trigger"]').remove();
  }
  if (jQuery('input[name="api-sale"]')) {
    jQuery('input[name="api-sale"]').remove();
  }
  jQuery('#suform').append('<input type="hidden" name="api-trigger" value="' + wapi + '">');
  jQuery('#suform').append('<input type="hidden" name="api-sale" value="' + saleid + '">');
  jQuery('#suform').submit();
}

function mc_dualCheckStatus(st, id, box) {
  switch(st) {
    case true:
      jQuery('input[name="' + box + '[]"]').each(function() {
        if (jQuery(this).attr('value') == id) {
          jQuery(this).prop('checked', true);
        }
      });
      break;
    default:
      jQuery('input[name="' + box + '[]"]').each(function() {
        if (jQuery(this).attr('value') == id) {
          jQuery(this).prop('checked', false);
        }
      });
      break;
  }
}

function mc_setCheckStatus(sts, box) {
  jQuery('input[name="' + box + '"]').val((sts ? 'yes' : 'no'));
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

function mc_fieldCopyAccounts(atype) {
  switch(atype) {
    case 'billing':
      jQuery('#sstbox').html(jQuery('#bstbox').html());
      for (var i = 1; i < 7; i++) {
        switch(i) {
          case 5:
            jQuery('#sstbox input[name="bill[' + i + ']"]').attr('name','ship[5]');
            jQuery('#sstbox select[name="bill[' + i + ']"]').attr('name','ship[5]');
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
      jQuery('a[href="#two"]').tab('show');
      break;
    case 'shipping':
      jQuery('#bstbox').html(jQuery('#sstbox').html());
      for (var i = 1; i < 7; i++) {
        switch(i) {
          case 5:
            jQuery('#bstbox input[name="ship[' + i + ']"]').attr('name','bill[5]');
            jQuery('#bstbox select[name="ship[' + i + ']"]').attr('name','bill[5]');
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
      jQuery('a[href="#one"]').tab('show');
      break;
  }
}

function mc_fieldCopy(atype) {
  switch(atype) {
    case 'billing':
      for (var i = 1; i < 9; i++) {
        jQuery('input[name="ship_' + i + '"]').val(jQuery('input[name="bill_' + i + '"]').val());
      }
      jQuery('select[name="shipSetCountry"]').val(jQuery('select[name="bill_9"]').val());
      jQuery('a[href="#two"]').tab('show');
      break;
    case 'shipping':
      for (var i = 1; i < 9; i++) {
        jQuery('input[name="bill_' + i + '"]').val(jQuery('input[name="ship_' + i + '"]').val());
      }
      jQuery('select[name="bill_9"]').val(jQuery('select[name="shipSetCountry"]').val());
      jQuery('a[href="#one"]').tab('show');
      break;
  }
}

function mc_userBoxes(id) {
  jQuery('.pg_list_' + id + ' input:checkbox').each(function() {
    if (jQuery(this).is(':checked')) {
      jQuery(this).prop('checked', false);
    } else {
      jQuery(this).prop('checked', true);
    }
  });
}

function mc_chkCntDiv(fld,cter,butt,fm) {
  var cnt = 0;
  if (fm == '' || fm == null) {
    var fm = 'formField';
  }
  jQuery('#' + fm + ' input[name="' + fld + '[]"]:checked').each(function(){
    ++cnt;
  });
  var bnt = jQuery('#' + butt + ' .' + cter).html(parseInt(cnt / 2));
  if (cnt > 0) {
    jQuery('#' + butt).prop('disabled', false);
  } else {
    jQuery('#' + butt).prop('disabled', true);
  }
  return parseInt(cnt / 2);
}

function mc_chkCnt(fld,cter,butt,fm) {
  var cnt = 0;
  if (fm == '' || fm == null) {
    var fm = 'formField';
  }
  jQuery('#' + fm + ' input[name="' + fld + '[]"]:checked').each(function(){
    ++cnt;
  });
  var bnt = jQuery('#' + butt + ' .' + cter).html(cnt);
  if (cnt > 0) {
    jQuery('#' + butt).prop('disabled', false);
  } else {
    jQuery('#' + butt).prop('disabled', true);
  }
  return cnt;
}

function mc_MarkForDeletion(id, opt, pg) {
  switch(opt) {
    case 'ok':
      jQuery('#td_undo_' + id).remove();
      jQuery('#purchase_' + id + ' select[name="qty[]"]').val('1');
      jQuery('#purchase_' + id).css('border', '1px solid #dddddd');
      jQuery('#purchase_' + id + ' .table-responsive:first-child td:nth-child(3)').show();
      jQuery('#purchase_' + id + ' .table-responsive:first-child td:nth-child(4)').show();
      jQuery('#prodAttrArea_' + id).show();
      jQuery('#pWrapper_' + id).show();
      mc_displayPurchaseProductPrices(id, 'sales-' + pg);
      break;
    default:
      jQuery('#purchase_' + id).css('border', '2px solid #ff9999');
      jQuery('#purchase_' + id + ' .table-responsive:first-child td:nth-child(3)').hide();
      jQuery('#purchase_' + id + ' .table-responsive:first-child td:nth-child(4)').hide();
      jQuery('#highlight_' + id).html('0.00');
      jQuery('#prodAttrArea_' + id).hide();
      jQuery('#pWrapper_' + id).hide();
      jQuery('#purchase_' + id + ' .table-responsive:first-child td:nth-child(4)').after('<td id="td_undo_' + id + '" class="text-right"><a href="#" onclick="mc_MarkForDeletion(\'' + id + '\',\'ok\',\'' + pg + '\');return false"><i class="fa fa-undo fa-fw"></i></a></td>');
      break;
  }
}

function mc_showHideGroup(act) {
  switch(act) {
    case 'newg':
      jQuery('.newg').slideUp();
      break;
    case 'exg':
      jQuery('.newg').slideDown();
      break;
  }
}

function mc_menuButton(act) {
  switch(act) {
    case 'open':
      jQuery('.slidepanelbuttonleft i').attr('class', 'fa fa-chevron-left fa-fw');
      break;
    default:
      jQuery('.slidepanelbuttonleft i').attr('class', 'fa fa-navicon fa-fw');
      break;
  }
}

function mc_toggleMoreOptions(obj, id, diva) {
  switch(jQuery(obj).attr('class')) {
    case 'fa fa-long-arrow-down fa-fw':
      jQuery(obj).attr('class','fa fa-long-arrow-up fa-fw')
      jQuery('#' + (diva ? diva : 'prd_') + id).slideDown();
      break;
    default:
      jQuery(obj).attr('class', 'fa fa-long-arrow-down fa-fw')
      jQuery('#' + (diva ? diva : 'prd_') + id).slideUp();
      break;
  }
}

function mc_AttBox(act,box) {
  switch(act) {
    case 'add':
      jQuery('input[name="' + box + '[]"]').last().after('<input type="file" name="' + box + '[]" style="margin-top:10px">');
      break;
    default:
      var n = jQuery('input[name="' + box + '[]"]').length;
      if (n > 1) {
        jQuery('input[name="' + box + '[]"]').last().remove();
      }
      break;
  }
}

// Load currency display preference..
function mc_loadCurrencyDisplay(value) {
  var cur = value + '{PRICE}';
  switch(value) {
    case 'AUD':
      cur = '&#036;{PRICE}AUD';
      break;
    case 'GBP':
      cur = '&pound;{PRICE}';
      break;
    case 'USD':
      cur = '&#036;{PRICE}';
      break;
    case 'JPY':
      cur = '&#165;{PRICE}';
      break;
    case 'EUR':
      cur = '{PRICE}&euro;';
      break;
  }
  jQuery('input[name="currencyDisplayPref"]').val(cur);
}

// Toggle child & infant categories
function mc_toggleChildrenInfants() {
  jQuery("#catArea .child").each(function() {
    jQuery(jQuery(this)).toggle('slow');
  });
}

// Enable/disable status box..
function mc_disableEnableBox(checked, box) {
  if (!checked) {
    jQuery('#' + box).addClass('textarea_grey');
    jQuery('#' + box).prop('disabled', true);
    jQuery('#selectStat').addClass('select_grey');
    jQuery('#selectStat').prop('disabled', true);
  } else {
    jQuery('#' + box).removeClass('textarea_grey');
    jQuery('#' + box).prop('disabled', false);
    jQuery('#selectStat').removeClass('select_grey');
    jQuery('#selectStat').prop('disabled', false);
  }
}

// iBox window loader..
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

// Copy from shipping
function mc_copyFromShipping() {
  for (var i = 1; i < 9; i++) {
    if (jQuery('input[name="ship_' + i + '"]')) {
      jQuery('input[name="bill_' + i + '"]').val(jQuery('input[name="ship_' + i + '"]').val());
    }
  }
  if (jQuery('select[name="shipSetCountry"]')) {
    jQuery('select[name="bill_9"]').val(jQuery('select[name="shipSetCountry"]').val());
  }
}

// Scroll to..
function mc_ScrollToArea(divArea, moffst, poffst) {
  jQuery('html, body').animate({
    scrollTop : jQuery('#' + divArea).offset().top - moffst + poffst
  }, 2000);
}

// Confirm message..
function mc_confirmMessage(txt) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    return true;
  } else {
    return false;
  }
}

//--------------------------------------------
// Generate random string
//--------------------------------------------

function mc_genString(field) {
  var text = '';
  var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789#[]-*";
  for (var i = 0; i < 40; i++) {
    text += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  jQuery('#' + field).val(text);
}

// Confirm message with url..
function mc_confirmMessageUrl(txt, url) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    window.location = url;
  } else {
    return false;
  }
}

// Select only parents..
function mc_parentsOnly() {
  jQuery("#checkGrid input:checkbox").each(function() {
    if (jQuery(this).attr('id').substring(0, 4) == 'pnt_') {
      if (jQuery(this).is(':checked')) {
        jQuery(this).prop('checked', false);
      } else {
        jQuery(this).prop('checked', true);
      }
    }
  });
}

// Select only children for category list..
function mc_selectChildren(id, type) {
  switch(type) {
    case 'on':
      jQuery("#" + id + " input:checkbox").each(function() {
        jQuery(this).prop('checked', true);
      });
      break;
    case 'off':
      jQuery("#" + id + " input:checkbox").each(function() {
        jQuery(this).prop('checked', false);
      });
      break;
  }
}

// Select countries..
function mc_selectCountries() {
  for (var i = 0; i < document.forms['form'].elements.length; i++) {
    var e = document.forms['form'].elements[i];
    if ((e.name != 'log') && (e.name != 'clearLogo') && (e.type == 'checkbox') && (e.name != 'yes[]')) {
      e.checked = document.forms['form'].log.checked;
    }
  }
}

// Check/uncheck array of checkboxes..
function mc_selectAll(form) {
  for (var i = 0; i < document.forms['form'].elements.length; i++) {
    var e = document.forms['form'].elements[i];
    if ((e.name != 'log') && (e.name != 'clearLogo') && (e.name != 'table[]') && (e.name != 'rpref[]') && (e.type == 'checkbox')) {
      e.checked = document.forms['form'].log.checked;
    }
  }
}

// Check/uncheck array of checkboxes..
function mc_toggleCheckBoxes(checked, field) {
  if (checked) {
    jQuery('.' + field + ' input:checkbox').prop('checked', true);
  } else {
    jQuery('.' + field + ' input:checkbox').prop('checked', false);
  }
}

function mc_toggleCheckBoxesID(act, field) {
  if (act) {
    if (jQuery('#fmcnbutton')) {
      jQuery('#fmcnbutton').prop('disabled', false);
    }
    jQuery('#' + field + ' input:checkbox').prop('checked', true);
  } else {
    if (jQuery('#fmcnbutton')) {
      jQuery('#fmcnbutton').prop('disabled', true);
    }
    jQuery('#' + field + ' input:checkbox').prop('checked', false);
  }
}

// Toggles divs..
function mc_singleCheckBox(act,field) {
  if (act) {
    if (jQuery('#fmcnbutton')) {
      jQuery('#fmcnbutton').prop('disabled', false);
    }
  } else {
    var bxck = 0;
    jQuery('#' + field + ' input[type="checkbox"]:checked').each(function(){
      ++bxck;
    });
    if (bxck==0 && jQuery('#fmcnbutton')) {
      jQuery('#fmcnbutton').prop('disabled', true);
    }
  }
}

function mc_BBTags(type, box) {
  switch(type) {
    case 'bold':
      mc_InsertAtCursor(box, '[b]..[/b]');
      break;
    case 'italic':
      mc_InsertAtCursor(box, '[i]..[/i]');
      break;
    case 'underline':
      mc_InsertAtCursor(box, '[u]..[/u]');
      break;
    case 'url':
      mc_InsertAtCursor(box, '[url]http://www.example.com[/url]');
      break;
    case 'img':
      mc_InsertAtCursor(box, '[img]http://www.example.com/picture.png[/img]');
      break;
    case 'email':
      mc_InsertAtCursor(box, '[email]email@example.com[/email]');
      break;
    case 'youtube':
      mc_InsertAtCursor(box, '[youtube]abc123[/youtube]');
      break;
    case 'vimeo':
      mc_InsertAtCursor(box, '[vimeo]abc123[/vimeo]');
      break;
  }
}

// With thanks to Scott Klarr
// http://www.scottklarr.com
function mc_InsertAtCursor(field, text) {
  var txtarea = document.getElementById(field);
  var scrollPos = txtarea.scrollTop;
  var strPos = 0;
  var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? 'ff' : (document.selection ? 'ie' : false));
  if (br == 'ie') {
    txtarea.focus();
    var range = document.selection.createRange();
    range.moveStart('character', -txtarea.value.length);
    strPos = range.text.length;
  }
  if (br == 'ff') {
    strPos = txtarea.selectionStart;
  }
  var front = (txtarea.value).substring(0, strPos);
  var back = (txtarea.value).substring(strPos, txtarea.value.length);
  txtarea.value = front + text + back;
  strPos = strPos + text.length;
  if (br == 'ie') {
    txtarea.focus();
    var range = document.selection.createRange();
    range.moveStart('character', -txtarea.value.length);
    range.moveStart('character', strPos);
    range.moveEnd('character', 0);
    range.select();
  }
  if (br == 'ff') {
    txtarea.selectionStart = strPos;
    txtarea.selectionEnd = strPos;
    txtarea.focus();
  }
  txtarea.scrollTop = scrollPos;
}
