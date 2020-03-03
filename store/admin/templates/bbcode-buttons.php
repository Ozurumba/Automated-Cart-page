<?php if (!defined('PARENT')) { exit; }
if (defined('BB_BOX2')) {
  $box = BB_BOX2;
}
if (defined('BB_BOX')) {
  $box = BB_BOX;
}
?>
<div class="bbButtons">
  <button class="btn btn-info btn-sm" type="button" onclick="mc_BBTags('bold','<?php echo $box; ?>')"><i class="fa fa-bold fa-fw"></i></button>
  <button class="btn btn-info btn-sm" type="button" onclick="mc_BBTags('italic','<?php echo $box; ?>')"><i class="fa fa-italic fa-fw"></i></button>
  <button class="btn btn-info btn-sm" type="button" onclick="mc_BBTags('underline','<?php echo $box; ?>')"><i class="fa fa-underline fa-fw"></i></button>
  <button class="btn btn-info btn-sm" type="button" onclick="mc_BBTags('url','<?php echo $box; ?>')"><i class="fa fa-link fa-fw"></i></button>
  <button class="btn btn-info btn-sm" type="button" onclick="mc_BBTags('email','<?php echo $box; ?>')"><i class="fa fa-envelope-o fa-fw"></i></button>
  <div class="mobilebreakpoint">
   <button class="btn btn-info btn-sm" type="button" onclick="mc_BBTags('img','<?php echo $box; ?>')"><i class="fa fa-picture-o fa-fw"></i></button>
   <button class="btn btn-info btn-sm" type="button" onclick="mc_BBTags('youtube','<?php echo $box; ?>')"><i class="fa fa-youtube fa-fw"></i></button>
   <button class="btn btn-info btn-sm" type="button" onclick="mc_BBTags('vimeo','<?php echo $box; ?>')"><i class="fa fa-play fa-fw"></i></button>
   <button class="btn btn-success btn-sm" type="button" onclick="window.open('index.php?bbCode=yes','_blank')"><i class="fa fa-question fa-fw"></i></button>
  </div>
</div>