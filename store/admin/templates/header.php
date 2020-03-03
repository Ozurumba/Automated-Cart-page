<?php if (!defined('PATH')) { exit; }
$helpTag = $cmd;
if (isset($_GET['s']) && ctype_digit($_GET['s'])) {
  $helpTag = 'settings' . $_GET['s'];
}
include(PATH . 'templates/menu.php');
include(PATH . 'templates/menu-panel.php');
if (ADMIN_HOMESCREEN_TILES) {
  if ($cmd == 'main' && (!isset($_SERVER['QUERY_STRING']) || empty($_SERVER['QUERY_STRING']) || isset($_GET['range']))) {
    define('LOAD_TILES', 1);
    $head_tile_counts = array(
      mc_rowCount('sales WHERE `saleConfirmation` = \'yes\''),
      mc_rowCount('accounts WHERE `type` = \'' . mc_safeSQL(ACC_TILE_PREF) . '\' AND `enabled` = \'yes\''),
      mc_rowCount('products')
    );
  }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $mc_global[1]; ?>" dir="<?php echo $mc_global[0]; ?>">
	<head>
    <meta charset="<?php echo $charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo ($pageTitle ? $pageTitle . ' ' : '') . (LICENCE_VER != 'unlocked' ? ' (Free Version)' : '') . (defined('DEV_BETA') && DEV_BETA != 'no' ? ' - BETA VERSION' : ''); ?></title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link href="templates/css/bootstrap.css" rel="stylesheet">
    <?php
    // JQPLOT
    if (in_array($cmd, array('stats','sales-trends','coupon-report','main','gift-report'))) {
    ?>
    <link rel="stylesheet" href="templates/css/jqplot.css">
    <?php
    }
    ?>
    <link href="templates/css/stylesheet.css" rel="stylesheet">
    <link href="templates/css/jquery-ui.css" rel="stylesheet">
    <link href="templates/css/menu.css" rel="stylesheet">

    <?php
    // DOWNLOAD MANAGER
    if (isset($loadElFinder)) {
    ?>
    <link rel="stylesheet" href="templates/css/elfinder/elfinder.full.css">
    <link rel="stylesheet" href="templates/css/elfinder/smoothness/smoothness.css">
		<link rel="stylesheet" href="templates/css/elfinder/theme.css">
    <?php
    }
    if (isset($loadiBox)) {
    ?>
    <link rel="stylesheet" href="templates/css/jquery.ibox.css">
    <?php
    }
    ?>

    <script src="templates/js/jquery.js"></script>
    <script src="templates/js/jquery-ui.js"></script>

    <?php
    if (isset($soundManager)) {
    ?>
    <script src="templates/js/plugins/soundmanager2.js"></script>
    <script>
    //<![CDATA[
    jQuery(document).ready(function() {
      soundManager.setup({
        url : 'templates/swf/'
      });
    });
    //]]>
    </script>
    <?php
    }

    if (isset($metaRefresh['time'])) {
    ?>
    <meta http-equiv="refresh" content="<?php echo $metaRefresh['time']; ?>;url=<?php echo $metaRefresh['url']; ?>">
    <?php
    }
    ?>

    <link rel="icon" href="favicon.ico">
	</head>

	<body>

  <div id="mshtmlwrapper" class="push">

    <div class="navbar navbar-default" id="msnavheader">

      <div class="container msheader">

        <div class="row visible-sm visible-md visible-lg">
          <div class="msheaderleft col-lg-5 col-md-5 col-sm-6">
            <button class="btn btn-info slidepanelbuttonleft menu-btn"><i class="fa fa-navicon fa-fw"></i></button> <a href="index.php"> <?php echo mc_cleanDataEntVars($msg_header8); ?></a>
          </div>
          <div class="msheaderright msheaderrighta col-lg-7 col-md-7 col-sm-6 text-right">
            <?php
            foreach ($msTopMenu AS $ntm) {
            ?>
            <a href="<?php echo $ntm['url']; ?>"<?php echo (isset($ntm['js_code']) && $ntm['js_code'] ? $ntm['js_code'] : '') . ($ntm['ext'] == 'yes' ? ' onclick="window.open(this);return false"' : ''); ?>><i class="fa <?php echo $ntm['icon']; ?> fa-fw" title="<?php echo $ntm['text']; ?>"></i> <span class="<?php echo $ntm['class']; ?>"><?php echo $ntm['text']; ?></span></a>
            <?php
            }
            ?>
          </div>
        </div>

        <div class="row visible-xs">
          <div class="msheaderleft col-xs-3">
            <button class="btn btn-info slidepanelbuttonleft menu-btn"><i class="fa fa-navicon fa-fw"></i></button>
          </div>
          <div class="msheadermiddle col-xs-6 text-center">
            <a href="index.php"><?php echo mc_cleanData($msg_header8); ?></a>
          </div>
          <div class="msheaderright col-xs-3 text-right">
            <div class="btn-group">
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-chevron-down fa-fw"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <?php
                foreach ($msTopMenu AS $ntm) {
                ?>
                <li><a href="<?php echo $ntm['url']; ?>"<?php echo (isset($ntm['js_code']) && $ntm['js_code'] ? $ntm['js_code'] : '') . ($ntm['ext'] == 'yes' ? ' onclick="window.open(this);return false"' : ''); ?>><i class="fa <?php echo $ntm['icon']; ?> fa-fw" title="<?php echo $ntm['text']; ?>"></i> <?php echo $ntm['text']; ?></a></li>
                <?php
                }
                ?>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="container min-height-container" id="mscontainer">

    <div class="row">

      <?php
      // Tiles - Homescreen only
      if (ADMIN_HOMESCREEN_TILES && defined('LOAD_TILES')) {
      // Only show tiles for global user and staff that have permissions for all areas..
      if ($sysCartUser[1] != 'restricted' || (in_array('sales', $sysCartUser[3]) && in_array('manage-products', $sysCartUser[3]) && (in_array('accounts', $sysCartUser[3]) || in_array('taccounts', $sysCartUser[3])))) {
      ?>
      <div class="container-fluid dashboardtiles hidden-sm hidden-xs">
        <div class="row">
          <div class="col-lg-4 col-md-4">
            <div class="panel panel-default">
              <div class="panel-heading">
                <i class="fa fa-shopping-basket fa-fw"></i> <?php echo $msg_header_tiles[0]; ?>
              </div>
              <div class="panel-body">
                <span class="pull-right">
                  <?php
                  if ($sysCartUser[1] != 'restricted' || in_array('sales-add', $sysCartUser[3])) {
                  ?>
                  <a href="?p=sales-add" class="btn btn-default btn-sm"><i class="fa fa-plus fa-fw"></i></a>
                  <?php
                  }
                  ?>
                  <a href="?p=sales" class="btn btn-default btn-sm"><i class="fa fa-arrow-right fa-fw"></i></a>
                </span>
                <span style="font-size:22px"><?php echo ($head_tile_counts[0] > 0 ? @number_format($head_tile_counts[0]) : '0'); ?></span>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-4">
            <div class="panel panel-default">
              <div class="panel-heading">
                <i class="fa fa-users fa-fw"></i> <?php echo $msg_header_tiles[1]; ?>
              </div>
              <div class="panel-body">
                <span class="pull-right">
                  <?php
                  if ($sysCartUser[1] != 'restricted' || in_array('add-account', $sysCartUser[3])) {
                  ?>
                  <a href="?p=add-account<?php echo (ACC_TILE_PREF == 'trade' ? '&amp;loadtype=trade' : ''); ?>" class="btn btn-default btn-sm"><i class="fa fa-plus fa-fw"></i></a>
                  <?php
                  }
                  ?>
                  <a href="?p=<?php echo (ACC_TILE_PREF == 'trade' ? 't' : ''); ?>accounts" class="btn btn-default btn-sm"><i class="fa fa-arrow-right fa-fw"></i></a>
                </span>
                <span style="font-size:22px"><?php echo ($head_tile_counts[1] > 0 ? @number_format($head_tile_counts[1]) : '0'); ?></span>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-4">
            <div class="panel panel-default">
              <div class="panel-heading">
                <i class="fa fa-cubes fa-fw"></i> <?php echo $msg_header_tiles[2]; ?>
              </div>
              <div class="panel-body">
                <span class="pull-right">
                  <?php
                  if ($sysCartUser[1] != 'restricted' || in_array('add-product', $sysCartUser[3])) {
                  ?>
                  <a href="?p=add-product" class="btn btn-default btn-sm"><i class="fa fa-plus fa-fw"></i></a>
                  <?php
                  }
                  ?>
                  <a href="?p=manage-products" class="btn btn-default btn-sm"><i class="fa fa-arrow-right fa-fw"></i></a>
                </span>
                <span style="font-size:22px"><?php echo ($head_tile_counts[2] > 0 ? @number_format($head_tile_counts[2]) : '0'); ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php
      }
      }
      ?>

      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
