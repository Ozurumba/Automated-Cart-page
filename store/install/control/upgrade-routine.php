<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

$upgradeOperations = true;

if (isset($SETTINGS->id)) {

  $ops[] = 'Adding New MySQL Tables';
  $ops[] = 'Apply Updates Prior to 2.1 (If applicable)';
  $ops[] = 'Updating Countries / Currency Settings';
  $ops[] = 'Updating Settings';
  $ops[] = 'Updating System Data';
  $ops[] = 'Updating Products / Categories';
  $ops[] = 'Updating Payment Methods / Gateways';
  $ops[] = 'Updating Sales / Purchase Data';
  $ops[] = 'Updating Account System';
  $ops[] = 'Updating Social Network APIs';
  $ops[] = 'Updating Layout Preferences';
  $ops[] = 'Updating Database Indexes';
  $ops[] = 'User Updates and Admin Optimisations';
  $ops[] = 'Database Cleanup / Finish';

  $divide = count($ops);

  if (isset($_GET['action'])) {
    switch($_GET['action']) {

      //==================================
      // ADD NEW MYSQL TABLES
      //==================================

      case 'start':

        mc_upgradeLog('Upgrade routine started');

        include(PATH . 'control/upgrades/tables.php');

        echo $MCJSON->encode(array(
          '1',
          @number_format((100 / $divide), 0)
        ));

        break;

      //==================================
      // OPERATIONS
      //==================================

      case $_GET['action']:

        // Pause for 3 seconds between operations..helps prevent database timeouts..
        sleep(3);

        switch($_GET['action']) {

          //==============================================
          // APPLY UPDATES PRIOR TO 2.1 (IF APPLICABLE)
          //==============================================

          case '1':

            include(PATH . 'control/upgrades/legacy.php');

            break;

          //=======================================
          // UPDATE COUNTRIES / CURRENCY SETTINGS
          //=======================================

          case '2':

            include(PATH . 'control/upgrades/countries-currencies.php');

            break;

          //==================================
          // UPDATE SETTINGS
          //==================================

          case '3':

            include(PATH . 'control/upgrades/settings.php');

            break;

          //==================================
          // SYSTEM DATA UPDATES
          //==================================

          case '4':

            include(PATH . 'control/upgrades/sysdata.php');

            break;

          //==================================
          // UPDATE PRODUCTS / CATEGORIES
          //==================================

          case '5':

            include(PATH . 'control/upgrades/products.php');

            break;

          //====================================
          // UPDATE PAYMENT METHODS / GATEWAY
          //====================================

          case '6':

            include(PATH . 'control/upgrades/payment-methods.php');

            break;

          //====================================
          // SALES / PURCHASE DATA
          //====================================

          case '7':

            include(PATH . 'control/upgrades/sales.php');

            break;

          //====================================
          // ACCOUNT SYSTEM
          //====================================

          case '8':

            include(PATH . 'control/upgrades/accounts.php');

            break;

          //====================================
          // SOCIAL PLUGINS
          //====================================

          case '9':

            include(PATH . 'control/upgrades/social.php');

            break;

          //====================================
          // LAYOUT PREFERENCES
          //====================================

          case '10':

            include(PATH . 'control/upgrades/layout.php');

            break;

          //====================================
          // UPDATE INDEXES
          //====================================

          case '11':

            include_once(PATH . 'control/upgrades/indexes.php');

            break;

          //=========================================
          // USER UPDATES AND ADMIN OPTIMISATIONS
          //=========================================

          case '12':

            include(PATH . 'control/upgrades/users.php');

            break;

          //====================================
          // DATABASE CLEANUP / FINISH
          //====================================

          case '13':

            include(PATH . 'control/upgrades/finish.php');

            break;
        }

        //==================================
        // ARE WE DONE??
        //==================================

        if ($_GET['action'] == ($divide - 1)) {
          mc_upgradeLog('Upgrade routine completed');
          include(PATH . 'control/version.php');
          $arr = array(
            'done',
            0
          );
        } else {
          $sum = number_format((($_GET['action'] + 1) / $divide) * 100, 0);
          $arr = array(
            ($_GET['action'] + 1),
            $sum
          );
        }

        echo $MCJSON->encode($arr);

        break;
    }

    exit;
  }

} else {

  echo $MCJSON->encode(array('err','err'));
  exit;

}

?>