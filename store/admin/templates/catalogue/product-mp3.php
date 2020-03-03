<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('mp3','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
$folder  = (isset($_GET['folder']) ? 'content/mp3/'.$_GET['folder'] : 'content/mp3');
$mp3     = array();
$fname   = array();
$allwd   = explode(',', ALLOWED_MUSIC_EXTENSIONS);
if (!isset($_GET['edit'])) {
  //---------------------
  // Get folders..
  //---------------------
  if (is_dir(REL_PATH.'content/mp3')) {
    $dir = opendir(REL_PATH.'content/mp3');
    while (false!==($read=readdir($dir))) {
      if (!in_array($read,array('.','..'))) {
        if (is_dir(REL_PATH . 'content/mp3/' . $read)) {
          $fname[] = $read;
        }
      }
    }
    closedir($dir);
  }
  //-------------------
  // Read mp3 files..
  //-------------------
  if (is_dir(REL_PATH.$folder)) {
    $dir2 = opendir(REL_PATH.$folder);
    while (false!==($read2=readdir($dir2))) {
      $info = pathinfo(REL_PATH . $folder . $read2);
      if (isset($info['extension']) && in_array($info['extension'], $allwd)) {
        if (mc_rowCount('mp3 WHERE `product_id` = \'' . mc_digitSan($_GET['product']) . '\' AND CONCAT(`fileFolder`,\'/\',`filePath`) = \'' . $folder . '/' . mc_safeSQL($read2) . '\'') == 0) {
          $mp3[] = $read2;
        }
      }
    }
    closedir($dir2);
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',$run,$msg_productmp34));
}
if (isset($OK2) && $cnt>0) {
  echo mc_actionCompleted($msg_productmp35);
}
if (isset($OK3)) {
  echo mc_actionCompleted($msg_productmp313);
}

$P = mc_getTableData('products', 'id', mc_digitSan($_GET['product']));
$thisProductID = mc_digitSan($_GET['product']);
?>

<div class="alert alert-info">
  <?php
  $qLinksArr  = array('product-mp3');
  $qLinksIcon = 'music';
  include(PATH . 'templates/catalogue/product-quick-links.php');
  ?>
</div>

<form method="post" id="form" action="?p=product-mp3&amp;product=<?php echo mc_digitSan($_GET['product']).(isset($_GET['folder']) ? '&amp;folder='.$_GET['folder'] : '').(isset($_GET['edit']) ? '&amp;edit='.mc_digitSan($_GET['edit']) : ''); ?>">
<div class="fieldHeadWrapper">
  <?php
  if (!isset($_GET['edit'])) {
  ?>
  <span class="float">
    <a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-folder-open fa-fw"></i></a>
  </span>
  <?php
  } echo (!empty($mp3) && !isset($EDIT->id) ? '<input type="checkbox" name="log" value="all" onclick="mc_toggleCheckBoxes(this.checked,\'formFieldWrapper\')" title="'.mc_cleanDataEntVars($msg_productmp39).'">&nbsp;&nbsp;' : ''); ?><?php echo (isset($EDIT->id) ? $msg_productmp312 : $msg_productmp32); ?>:
</div>

<?php
if (!isset($_GET['edit'])) {
?>
<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}" style="text-transform:none">
  <option value="0"><?php echo $msg_productmp33; ?></option>
  <option value="0">- - - - - - - -</option>
  <option value="index.php?p=product-mp3&amp;product=<?php echo mc_digitSan($_GET['product']); ?>">content/mp3/</option>
  <?php
  if (!empty($fname)) {
  foreach ($fname AS $fd) {
  ?>
  <option value="index.php?p=product-mp3&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;folder=<?php echo $fd; ?>"<?php echo (isset($_GET['folder']) && $_GET['folder']==$fd ? ' selected="selected"' : ''); ?>>content/mp3/<?php echo $fd; ?>/</option>
  <?php
  }
  }
  ?>
  </select>
</div>
<?php
}

if (empty($mp3) && !isset($_GET['edit'])) {
?>
<span class="noData"><?php echo str_replace('{folder}',mc_safeHTML($folder),$msg_productmp36); ?></span>
<?php
} else {
if (isset($EDIT->id)) {
$msid = md5($EDIT->fileFolder . '/' . $EDIT->filePath);
?>
<div class="formFieldWrapper">
  <div class="formLeft">
    <i class="fa fa-music fa-fw"></i> <?php echo $EDIT->fileFolder.'/'.$EDIT->filePath; ?>
    <div class="form-group input-group musicfile" style="margin:10px 0 0 0" id="ms_<?php echo $msid; ?>">
       <span class="input-group-addon"><a class="musicplay" href="#" onclick="mc_musicPlayer('<?php echo REL_PATH . $EDIT->fileFolder.'/'.$EDIT->filePath; ?>', 'ms_<?php echo $msid; ?>');return false"><i class="fa fa-play fa-fw"></i></a></span>
       <input type="text" name="fileName" value="<?php echo mc_cleanData($EDIT->fileName); ?>" class="box addon-no-radius" placeholder="<?php echo mc_safeHTML($msg_productmp311); ?>">
     </div>
  </div>
</div>
<?php
} else {
sort($mp3);
foreach ($mp3 AS $m) {
$m_n  = mc_mp3Clean($m);
$key  = md5($m);
$msid = md5($folder . '/' . $m);
?>
<div class="formFieldWrapper">
  <div class="formLeft">
     <input type="checkbox" name="mp3[<?php echo $key; ?>]" value="<?php echo $m; ?>">
     <i class="fa fa-music fa-fw"></i> <?php echo $folder.'/'.$m; ?>
     <div class="form-group input-group musicfile" style="margin:10px 0 0 0" id="ms_<?php echo $msid; ?>">
       <span class="input-group-addon"><a class="musicplay" href="#" onclick="mc_musicPlayer('<?php echo REL_PATH . $folder.'/'.$m; ?>', 'ms_<?php echo $msid; ?>');return false"><i class="fa fa-play fa-fw"></i></a></span>
       <input type="text" name="fileName[<?php echo $key; ?>]" value="<?php echo mc_safeHTML($m_n); ?>" class="box addon-no-radius" placeholder="<?php echo mc_safeHTML($msg_productmp311); ?>">
     </div>
  </div>
</div>
<?php
}
}
?>
<p style="text-align:center;padding-top:20px">
  <input type="hidden" name="folder" value="<?php echo mc_safeHTML($folder); ?>">
  <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="yes">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_productmp312 : $msg_productmp37)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_productmp312 : $msg_productmp37)); ?>">
  <?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=product-mp3&amp;product='.mc_digitSan($_GET['product']).'\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
<?php
}
?>
</form>

<?php
if (!isset($_GET['edit'])) {
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery("#sortable").sortable({
    update : function (data) {
      jQuery("#loader").load("index.php?p=product-mp3&product=<?php echo mc_digitSan($_GET['product']); ?>&order=yes&"+jQuery('#sortable').sortable('serialize'));
      jQuery('#loader_msg').show('slow');
      jQuery('#loader_msg').html('<i class="fa fa-check fa-fw"></i>&nbsp;&nbsp;').fadeOut(6000);
    }
  });
});
//]]>
</script>

<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><span class="float" id="loader"></span><span class="float" id="loader_msg" style="display:none" onclick="jQuery(this).hide()"></span><?php echo $msg_productmp38; ?>:</p>
</div>

<div id="sortable">
<?php
$q_mp = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "mp3`
        WHERE `product_id` = '".mc_digitSan($_GET['product'])."'
        ORDER BY `orderBy`
        ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_mp)>0) {
  while ($MP3 = mysqli_fetch_object($q_mp)) {
  $msid = md5($MP3->id);
  ?>
  <div class="panel panel-default" id="mp3-<?php echo $MP3->id; ?>" style="cursor:move">
    <div class="panel-body" title="<?php echo mc_cleanDataEntVars($msg_productmp314); ?>">
      <b><?php echo $MP3->fileName; ?></b>
      <hr>
      <i class="fa fa-music fa-fw"></i> <?php echo $MP3->fileFolder.'/'.$MP3->filePath; ?>
    </div>
    <div class="panel-footer musicfile" id="msm_<?php echo $msid; ?>">
      <a class="musicplay" href="#" onclick="mc_musicPlayer('<?php echo REL_PATH . $MP3->fileFolder.'/'.$MP3->filePath; ?>', 'msm_<?php echo $msid; ?>');return false"><i class="fa fa-play fa-fw"></i></a> <a href="?p=product-mp3&amp;edit=<?php echo $MP3->id; ?>&amp;product=<?php echo mc_digitSan($_GET['product']); ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=product-mp3&amp;product='.mc_digitSan($_GET['product']).'&amp;del='.$MP3->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_productmp310; ?></span>
<?php
}
?>
</div>
<?php
}
?>


</div>
