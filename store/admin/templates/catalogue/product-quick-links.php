<?php if (!defined('PARENT')) { die('Permission Denied'); }

  $qlProdID = (isset($thisProductID) ? $thisProductID : (isset($_GET['edit']) ? $_GET['edit'] : $_GET['product']));

  ?>
  <div>
    <div class="btn-group">
      <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-cog fa-fw"></i>
      </button>
      <ul class="dropdown-menu productquicklinks">
        <?php
        if (!in_array('product-pictures', $qLinksArr)) {
        ?>
        <li><a href="?p=product-pictures&amp;product=<?php echo mc_digitSan($qlProdID); ?>"><i class="fa fa-camera fa-fw"></i> <?php echo $msg_productadd38; ?></a></li>
        <?php
        }
        if (!in_array('product-copy', $qLinksArr)) {
        ?>
        <li><a href="?p=add-product&amp;copyp=<?php echo mc_digitSan($qlProdID); ?>"><i class="fa fa-copy fa-fw"></i> <?php echo $msg_productpictures16; ?></a></li>
        <?php
        }
        if (!in_array('product-edit', $qLinksArr)) {
        ?>
        <li><a href="?p=add-product&amp;edit=<?php echo mc_digitSan($qlProdID); ?>"><i class="fa fa-pencil fa-fw"></i> <?php echo $msg_productpictures15; ?></a></li>
        <?php
        }
        if ($P->pDownload=='no') {
        if (!in_array('product-attributes', $qLinksArr)) {
        ?>
        <li><a href="?p=product-attributes&amp;product=<?php echo mc_digitSan($qlProdID); ?>"><i class="fa fa-pencil-square-o fa-fw"></i> <?php echo $msg_productpictures17; ?></a></li>
        <?php
        }
        if (!in_array('copy-attributes', $qLinksArr)) {
        ?>
        <li><a href="?p=copy-attributes&amp;product=<?php echo mc_digitSan($qlProdID); ?>"><i class="fa fa-clone fa-fw"></i> <?php echo $msg_prodattributes24; ?></a></li>
        <?php
        }
        }
        if (!in_array('product-related', $qLinksArr)) {
        ?>
        <li><a href="?p=product-related&amp;product=<?php echo mc_digitSan($qlProdID); ?>"><i class="fa fa-exchange fa-fw"></i> <?php echo $msg_productpictures18; ?></a></li>
        <?php
        }
        if (PRODUCT_MP3_PREVIEWS && !in_array('product-mp3', $qLinksArr)) {
        ?>
        <li><a href="?p=product-mp3&amp;product=<?php echo mc_digitSan($qlProdID); ?>"><i class="fa fa-music fa-fw"></i> <?php echo $msg_productmanage19; ?></a></li>
        <?php
        }
        if ($P->pDownload=='no' && !in_array('product-personalisation', $qLinksArr)) {
        ?>
        <li><a href="?p=product-personalisation&amp;product=<?php echo mc_digitSan($qlProdID); ?>"><i class="fa fa-quote-left fa-fw"></i> <?php echo $msg_productmanage20; ?></a></li>
        <?php
        }
        ?>
        <li role="separator" class="divider"></li>
        <li><a href="?p=manage-products"><i class="fa fa-cubes fa-fw"></i> <?php echo $msg_javascript27; ?></a></li>
      </ul>
    </div>
    <span class="quicklinktext"><i class="fa fa-<?php echo $qLinksIcon; ?> fa-fw"></i> <?php echo mc_cleanData($P->pName); ?></span>
  </div>