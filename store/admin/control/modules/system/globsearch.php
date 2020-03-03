<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Perms..
if ($sysCartUser[1] != 'global' && (!in_array('sales', $sysCartUser[3]) && !in_array('manage-products', $sysCartUser[3]) && !in_array('accounts', $sysCartUser[3]) && !in_array('users', $sysCartUser[3]))) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'system/global-search.php');
include(MCLANG . 'sales/view-sales.php');
include(MCLANG . 'sales/sales-view.php');
include(MCLANG . 'sales/sales-incomplete.php');
include(MCLANG . 'catalogue/product-manage.php');
include(MCLANG . 'catalogue/product-attributes.php');
include(MCLANG . 'accounts/accounts.php');
include(MCLANG . 'system/users.php');

$pageTitle = mc_cleanDataEntVars($msg_globalsearch) . ': ' . $pageTitle;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/global-search.php');
include(PATH . 'templates/footer.php');

?>