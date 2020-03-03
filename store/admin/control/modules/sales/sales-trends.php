<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'sales/view-sales.php');

$pageTitle     = mc_cleanDataEntVars($msg_stats21) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales-trends.php');
include(PATH . 'templates/footer.php');

?>
