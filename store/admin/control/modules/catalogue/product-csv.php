<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

if (isset($_POST['process'])) {
}

$pageTitle = $msg_productadd3 . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/product-csv.php');
include(PATH . 'templates/footer.php');

?>
