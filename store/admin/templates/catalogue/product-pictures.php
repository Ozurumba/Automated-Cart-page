<?php if (!defined('PARENT') || !isset($_GET['product']) || mc_digitSan($_GET['product']) == 0) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',$run,$msg_productpictures8));
}
if (isset($OK2) && $cnt>0) {
  echo mc_actionCompleted($msg_productpictures10);
}
if (isset($OK3)) {
  echo mc_actionCompleted($msg_product_pictures[3]);
}

$P = mc_getTableData('products','id',mc_digitSan($_GET['product']));
$thisProductID = mc_digitSan($_GET['product']);
?>

<div class="alert alert-info">
  <?php
  $qLinksArr  = array('product-pictures');
  $qLinksIcon = 'camera';
  include(PATH . 'templates/catalogue/product-quick-links.php');
  ?>
</div>

<form method="post" action="?p=product-pictures&amp;product=<?php echo mc_digitSan($_GET['product']); ?>" enctype="multipart/form-data">
<div class="fieldHeadWrapper">
  <p><span class="float"><a onclick="jQuery('#addOptions').hide();jQuery('#standard').show();jQuery('#remote').hide();return false" href="#" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[11]); ?>"><i class="fa fa-image fa-fw"></i> <?php echo $msg_admin3_0[11]; ?></a> <a onclick="jQuery('#addOptions').hide();jQuery('#standard').hide();jQuery('#remote').show();return false" href="#" title="<?php echo mc_cleanDataEntVars($msg_productpictures22); ?>"><i class="fa fa-globe fa-fw"></i> <?php echo $msg_productpictures22; ?></a></span><?php echo $msg_productpictures7; ?>:</p>
</div>

<div id="standard">
<div class="formFieldWrapper">
  <div class="formLeft" id="addimgboxes">
    <input type="file" name="image[]">

    <div style="margin-top:10px">
      <button type="button" class="btn btn-primary btn-xs" onclick="mc_AttBox('add','image')"><i class="fa fa-plus fa-fw"></i></button>
      <button type="button" class="btn btn-success btn-xs" onclick="mc_AttBox('minus','image')"><i class="fa fa-minus fa-fw"></i></button>
    </div>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productpictures13; ?>: <?php echo mc_displayHelpTip($msg_javascript199); ?></label>
    <select name="folder" id="folderList">
      <option value="products"><?php echo PRODUCTS_FOLDER; ?>/ <?php echo $msg_productpictures12; ?></option>
      <?php
      if (is_dir(REL_HTTP_PATH.PRODUCTS_FOLDER)) {
        $dir = opendir(REL_HTTP_PATH.PRODUCTS_FOLDER);
        while (false!==($read=readdir($dir))) {
          if (!in_array($read,array('.','..')) && is_dir(REL_HTTP_PATH.PRODUCTS_FOLDER.'/'.$read)) {
          ?>
          <option value="<?php echo $read; ?>"<?php echo (isset($EDIT->catFolder) && $EDIT->catFolder==$read ? ' selected="selected"' : ''); ?>><?php echo PRODUCTS_FOLDER; ?>/<?php echo $read; ?></option>
          <?php
          }
        }
        closedir($dir);
      }
      ?>
    </select><br>
    <button type="button" onclick="mc_createPictureFolder('<?php echo mc_filterJS($msg_javascript152); ?>','<?php echo PRODUCTS_FOLDER; ?>')" class="btn btn-default" name="<?php echo mc_cleanDataEntVars($msg_salesupdate13); ?>"><i class="fa fa-folder fa-fw"></i></button>
  </div>
</div>

</div>

<div class="formFieldWrapper" id="remote" style="display:none">
  <div class="formLeft">
    <label><?php echo $msg_productpictures23; ?>: <?php echo mc_displayHelpTip($msg_javascript446,'RIGHT'); ?></label>
    <input type="file" name="remote">
  </div>
</div>

<p style="text-align:center;padding-top:20px">
  <input type="hidden" name="process" value="yes">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_productpictures7); ?>" title="<?php echo mc_cleanDataEntVars($msg_productpictures7); ?>">
</p>
</form>

<form method="post" action="?p=product-pictures&amp;product=<?php echo mc_digitSan($_GET['product']); ?>">
<?php
$SQL     = '';
if (isset($_GET['f'])) {
  switch($_GET['f']) {
    case 'local':
      $SQL = 'AND `remoteserver` = \'no\'';
      break;
    case 'remote':
      $SQL = 'AND `remoteserver` = \'yes\'';
      break;
  }
}
$mainImg = mc_getTableData('pictures','product_id',mc_digitSan($_GET['product']),'AND `displayImg` = \'yes\'');
$q_pics = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "pictures`.`id` AS `pid`
          FROM `" . DB_PREFIX . "pictures`
          LEFT JOIN `" . DB_PREFIX . "products`
          ON `" . DB_PREFIX . "pictures`.`product_id` = `" . DB_PREFIX . "products`.`id`
          WHERE `product_id` = '".mc_digitSan($_GET['product'])."'
          $SQL
          ORDER BY `" . DB_PREFIX . "pictures`.`displayImg`,`" . DB_PREFIX . "pictures`.`id`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_pics)>0) {
?>
<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><span class="float"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span><?php echo $msg_productpictures4; ?> (<?php echo mysqli_num_rows($q_pics); ?>):</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!=0){location=this.options[this.selectedIndex].value}">
    <option value="?p=product-pictures&amp;product=<?php echo mc_digitSan($_GET['product']); ?>"><?php echo $msg_admin3_0[12]; ?></option>
    <option value="?p=product-pictures&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;f=local"<?php echo (isset($_GET['f']) && $_GET['f'] == 'local' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin3_0[13]; ?></option>
    <option value="?p=product-pictures&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;f=remote"<?php echo (isset($_GET['f']) && $_GET['f'] == 'remote' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin3_0[14]; ?></option>
  </select>
</div>

<?php
$run = 0;
while ($PICS = mysqli_fetch_object($q_pics)) {
++$run;
$split = explode(',',$PICS->dimensions);
?>
<div class="panel panel-default">
  <input type="hidden" name="picIDs[]" value="<?php echo $PICS->pid; ?>">
  <div class="panel-body productimg">
    <?php
    if ($PICS->remoteServer=='no') {
    ?>
    <a onclick="mc_Window(this.href,0,0,'');return false" href="<?php echo $SETTINGS->ifolder.'/'.PRODUCTS_FOLDER.'/'.($PICS->folder ? mc_imageDisplayPath($PICS->folder).'/' : '').$PICS->picture_path; ?>" title="<?php echo mc_safeHTML($PICS->pName); ?>"><img class="imgborder" src="<?php echo $SETTINGS->ifolder.'/'.PRODUCTS_FOLDER.'/'.($PICS->folder ? mc_imageDisplayPath($PICS->folder).'/' : '').$PICS->thumb_path; ?>" alt="<?php echo mc_safeHTML($PICS->pName); ?>" title="<?php echo mc_safeHTML($PICS->pName); ?>"></a>
    <?php
    } else {
    ?>
    <a href="<?php echo $PICS->remoteImg; ?>" title="<?php echo mc_safeHTML($PICS->pName); ?>" onclick="mc_Window(this.href,0,0,'');return false"><img class="imgborder" src="<?php echo ($PICS->remoteThumb ? $PICS->remoteThumb : $PICS->remoteImg); ?>" alt="<?php echo mc_safeHTML($PICS->pName); ?>" title="<?php echo mc_safeHTML($PICS->pName); ?>"></a>
    <?php
    }
    ?>
    <div class="radio">
      <label><input type="radio" name="mainImg" value="<?php echo $PICS->pid; ?>"<?php echo ($PICS->displayImg=='yes' ? ' checked="checked"' : (!isset($mainImg->id) && $run==1 ? ' checked="checked"' : '')); ?>> <?php echo $msg_productpictures20; ?></label>
    </div>
    <hr>
    <label><?php echo $msg_product_pictures[0]; ?></label>
    <input type="text" name="title[<?php echo $PICS->pid; ?>]" value="<?php echo mc_safeHTML($PICS->pictitle); ?>" class="form-control">
    <label style="margin-top:10px"><?php echo $msg_product_pictures[1]; ?></label>
    <input type="text" name="alt[<?php echo $PICS->pid; ?>]" value="<?php echo mc_safeHTML($PICS->picalt); ?>" class="form-control">
  </div>
  <div class="panel-footer">
    <?php
    if ($PICS->remoteServer=='no') {
      echo $split[0].' x '.$split[1]; ?>px @ <?php echo mc_fileSizeConversion(@filesize($SETTINGS->serverPath.'/'.PRODUCTS_FOLDER.'/'.($PICS->folder ? mc_imageDisplayPath($PICS->folder).'/' : '').$PICS->picture_path));
    } else {
      echo $msg_productpictures24;
    }

    if ($uDel=='yes') {
    ?>
    <span class="link"><a href="?p=product-pictures&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;delete=<?php echo $PICS->pid; ?>&amp;path=<?php echo ($PICS->folder ? mc_imageDisplayPath($PICS->folder).'/' : '').$PICS->picture_path; ?>&amp;thumb=<?php echo ($PICS->folder ? mc_imageDisplayPath($PICS->folder).'/' : '').$PICS->thumb_path; ?>&amp;di=<?php echo $PICS->displayImg; ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a></span>
    <?php
    }
    ?>
  </div>
</div>
<?php
}
?>
<p style="text-align:center;margin-top:20px">
  <input type="hidden" name="process_pics" value="1">
  <input type="submit" value="<?php echo mc_cleanDataEntVars($msg_product_pictures[2]); ?>" title="<?php echo mc_cleanDataEntVars($msg_product_pictures[2]); ?>" class="btn btn-primary">
</p>
<?php
} else {
?>
<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><span class="float"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span><?php echo $msg_productpictures4; ?> (0):</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!=0){location=this.options[this.selectedIndex].value}">
    <option value="?p=product-pictures&amp;product=<?php echo mc_digitSan($_GET['product']); ?>"><?php echo $msg_admin3_0[12]; ?></option>
    <option value="?p=product-pictures&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;f=local"<?php echo (isset($_GET['f']) && $_GET['f'] == 'local' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin3_0[13]; ?></option>
    <option value="?p=product-pictures&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;f=remote"<?php echo (isset($_GET['f']) && $_GET['f'] == 'remote' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin3_0[14]; ?></option>
  </select>
</div>

<span class="noData"><?php echo $msg_productpictures11; ?></span>
<?php
}
?>
</form>

</div>