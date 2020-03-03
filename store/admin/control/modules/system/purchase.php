<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

$pageTitle = 'Purchase Full Version: ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/purchase.php');
include(PATH . 'templates/footer.php');

?>
