<?php if (!defined('WINPARENT')) { die('Permission Denied'); }?>
<!DOCTYPE html>
<html lang="<?php echo $mc_global[1]; ?>" dir="<?php echo $mc_global[0]; ?>">
	<head>
    <meta charset="<?php echo $charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $pageTitle; ?></title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link href="templates/css/bootstrap.css" rel="stylesheet">
    <link href="templates/css/stylesheet.css" rel="stylesheet">
    <link href="templates/css/jquery-ui.css" rel="stylesheet">

    <?php
    if (defined('SALE_INVOICE')) {
    ?>
    <link href="templates/css/mc-invoice.css" rel="stylesheet">
    <?php
    }
    if (defined('SALE_PACKING_SLIP')) {
    ?>
    <link href="templates/css/mc-packing-slip.css" rel="stylesheet">
    <?php
    }
    ?>

    <script src="templates/js/jquery.js"></script>
    <script src="templates/js/jquery-ui.js"></script>

    <link rel="icon" href="favicon.ico">
	</head>