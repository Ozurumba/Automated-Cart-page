<?php

/*
 ACCOUNT LOADER
-------------------------------------------*/

if (!defined('PARENT') || !isset($loggedInUser['id'])) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// User params..
if ($loggedInUser['params']) {

  $loggedInUserParams = $MCACC->params(0, array(), $loggedInUser['params'], 'load');

  // Layout..
  if (isset($loggedInUserParams['layout']) && in_array($loggedInUserParams['layout'], array('grid','list'))) {
    $SETTINGS->layout = $loggedInUserParams['layout'];
  }

}

// Set category permissions for accounts..
$mc_catPermissions = '1,' . ($loggedInUser['type'] == 'personal' ? '2' : '3');
$mc_catSQL         = "(LOCATE('1', `vis`) > 0 OR LOCATE('" . ($loggedInUser['type'] == 'personal' ? '2' : '3') . "',`vis`) > 0)";
$mc_cacheFlag      = $loggedInUser['type'];

?>