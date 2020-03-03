<?php if (!defined('PARENT')) { die('Permission Denied'); }?>
<div id="content">
<script>
//<![CDATA[
function mc_boxAdd(sel) {
  if (sel == 'close') {
    jQuery('#customBoxController').slideUp();
    return false;
  }
  mc_ShowSpinner();
  jQuery(document).ready(function() {
    jQuery.post('index.php?p=left-boxes&abox=yes', {
      box : sel
    },
    function(data) {
      if (data[0] == 'OK') {
        window.location.reload();
      }
    }, 'json');
  });
  return false;
}
function mc_boxFlag(id) {
  var fg = jQuery('#box_' + id + ' a[class="flag"] i').attr('class');
  switch(fg) {
    case 'fa fa-flag fa-fw mc-green':
      break;
    default:
      break;
  }
  jQuery('#box_' + id + ' a[class="flag"] i').attr('class','fa fa-refresh fa-fw fa-spin');
  jQuery(document).ready(function() {
    jQuery.ajax({
      url: 'index.php',
      data: 'p=left-boxes&flag=' + fg + '&id=' + id,
      dataType: 'json',
      cache: false,
      success: function (data) {
        switch(fg) {
          case 'fa fa-flag fa-fw mc-green':
            jQuery('#box_' + id + ' a[class="flag"] i').attr('class','fa fa-flag-o fa-fw')
            break;
          default:
            jQuery('#box_' + id + ' a[class="flag"] i').attr('class','fa fa-flag fa-fw mc-green')
            break;
        }
      }
    });
  });
  return false;
}
function mc_boxRem(id) {
  jQuery('#box_' + id).remove();
}
function mc_boxPos(id,pos) {
  switch(pos) {
    case 'up':
      var preD = jQuery('#box_' + id).prev().attr('id');
      jQuery('#box_' + id).insertBefore('#' + preD);
      break;
    case 'down':
      var nxtD = jQuery('#box_' + id).next().attr('id');
      jQuery('#box_' + id).insertAfter('#' + nxtD);
      break;
  }
  mc_boxEnDis('reload');
}
function mc_boxEnDis(opt) {
  switch(opt) {
    case 'load':
      jQuery('.boxarea tbody tr:first-child .up').addClass('disabled');
      jQuery('.boxarea tbody tr:last-child .down').addClass('disabled');
      break;
    case 'reload':
      jQuery('.boxarea tbody tr button').removeClass('disabled');
      jQuery('.boxarea tbody tr:first-child .up').addClass('disabled');
      jQuery('.boxarea tbody tr:last-child .down').addClass('disabled');
      break;
  }
}
jQuery(document).ready(function() {
  mc_boxEnDis('load');
});
//]]>
</script>
<?php
$tabIndex = 0;
if (isset($OK)) {
  echo mc_actionCompleted($msg_leftboxcont2);
}
?>

<form method="post" id="form" action="?p=left-boxes">
<div class="fieldHeadWrapper">
  <p><span class="pull-right">
  <a onclick="jQuery('#customBoxController').slideToggle();return false" href="#" title="<?php echo mc_cleanDataEntVars($msg_settings283); ?>"><i class="fa fa-plus fa-fw"></i></a>
  </span><?php echo $msg_settings208; ?></p>
</div>

<div class="formFieldWrapper" id="customBoxController" style="display:none;margin-top:10px">
  <div>
    <span style="display:block;padding-bottom:5px"><?php echo $msg_settings284; ?> <?php echo mc_displayHelpTip($msg_javascript555,'RIGHT'); ?></span>
    <p class="customBoxSelect">
      <select name="temp" onchange="if(this.value!='0'){mc_boxAdd(this.value)}">
      <option value="0">- - - - - -</option>
      <?php
      if (is_dir(REL_PATH . THEME_FOLDER . '/customTemplates/')) {
        $showbox = opendir(REL_PATH . THEME_FOLDER . '/customTemplates/');
        while (false!==($read=readdir($showbox))) {
          if (substr($read,-8)=='.tpl.php' && substr($read,0,3) == 'box') {
            ?>
            <option value="<?php echo $read; ?>"><?php echo $read; ?></option>
            <?php
          }
        }
        closedir($showbox);
      }
      ?>
      <option value="0">- - - - - -</option>
      <option value="close"><?php echo $msg_settings285; ?></option>
      </select>
    </p>
  </div>
</div>

<div class="formFieldWrapper" style="margin-top:10px">
  <div class="formLeft">
    <div class="table-responsive boxarea">
      <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th><?php echo $msg_left_box_menu[0]; ?></th>
          <th><?php echo $msg_left_box_menu[1]; ?></th>
          <th><?php echo $msg_left_box_menu[2]; ?></th>
          <?php
          if ($uDel=='yes') {
          ?>
          <th><?php echo $msg_left_box_menu[3]; ?></th>
          <?php
          }
          ?>
        </tr>
      </thead>
      <tbody>
      <?php
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "boxes`
           ORDER BY `orderby`
           ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($BX = mysqli_fetch_object($q)) {
        $isDef = 'no';
        switch($BX->ident) {
          case 'cat':
            $txt = ($BX->name ? $BX->name : $msg_public_header3);
            break;
          case 'points':
            $txt = ($BX->name ? $BX->name : $msg_public_header22);
            break;
          case 'popular':
            $txt = ($BX->name ? $BX->name : $msg_public_header15);
            break;
          case 'tweets':
            $txt = ($BX->name ? $BX->name : $msg_public_header34);
            break;
          case 'recent':
            $txt = ($BX->name ? $BX->name : $msg_public_header8);
            break;
          case 'links':
            $txt = ($BX->name ? $BX->name : $msg_public_header6);
            break;
          case 'brands':
            $txt = ($BX->name ? $BX->name : $msg_public_header13);
            break;
          case 'rss':
            $txt = ($BX->name ? $BX->name : $msg_public_header35);
            break;
          default:
            $isDef = 'yes';
            $txt   = ($BX->name ? $BX->name : '');
            break;
        }
        ?>
        <tr id="box_<?php echo $BX->id; ?>">
          <td><input type="hidden" name="box[]" value="<?php echo $BX->id; ?>"><input type="text" class="form-control" name="name[]" value="<?php echo mc_safeHTML($txt); ?>"></td>
          <td><a class="flag" href="#" onclick="mc_boxFlag('<?php echo $BX->id; ?>');return false"><i class="<?php echo ($BX->status == 'yes' ? 'fa fa-flag fa-fw mc-green' : 'fa fa-flag-o fa-fw'); ?>"></i></a></td>
          <td>
            <button type="button" class="btn btn-default up" onclick="mc_boxPos('<?php echo $BX->id; ?>','up')"><i class="fa fa-chevron-up fa-fw"></i></button>
            <button type="button" class="btn btn-default down" onclick="mc_boxPos('<?php echo $BX->id; ?>','down')"><i class="fa fa-chevron-down fa-fw"></i></button>
          </td>
          <?php
          if ($uDel=='yes') {
          ?>
          <td><?php echo ($isDef == 'yes' ? '<a href="#" onclick="mc_boxRem(\'' . $BX->id . '\');return false"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?></td>
          <?php
          }
          ?>
        </tr>
        <?php
      }
      ?>
      </tbody>
      </table>
    </div>
  </div>
</div>

<p style="text-align:center;padding-top:10px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_leftboxcont); ?>" title="<?php echo mc_cleanDataEntVars($msg_leftboxcont); ?>">
</p>

</form>

</div>