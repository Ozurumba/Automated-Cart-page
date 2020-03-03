<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// ACCOUNTS
//==========================

if (mc_rowCount('accounts') == 0) {

  // Port sale data to accounts table..
  @mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "accounts` (`name`,`created`,`email`,`pass`,`enabled`,`verified`,`ip`,`language`,`newsletter`)
  SELECT `bill_1`,`purchaseDate`,`bill_2`,sha1(concat(curdate(),curtime(),rand(5))),'yes','yes',`ipAddress`,'english',`optInNewsletter`
  FROM `" . DB_PREFIX . "sales` WHERE `saleConfirmation` = 'yes' and `bill_1` != '' and `bill_2` != '' GROUP BY `bill_2` ORDER BY `id`");

  mc_upgradeLog('Completed: data ported to `' . DB_PREFIX . 'accounts` table for ' . @number_format(@mysqli_affected_rows($GLOBALS["___msw_sqli"])) . ' accounts');

  // Update sales table account column with new IDs
  @mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales`SET `account` = (SELECT `id` FROM `" . DB_PREFIX . "accounts` WHERE `" . DB_PREFIX . "accounts`.`email` = `" . DB_PREFIX . "sales`.`bill_2`)");

  mc_upgradeLog('Completed: account id updated in `' . DB_PREFIX . 'sales` table for ' . @number_format(@mysqli_affected_rows($GLOBALS["___msw_sqli"])) . ' accounts');

  // Create address book entries for billing..
  @mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "addressbook` (`nm`,`em`,`addr1`,`addr2`,`addr3`,`addr4`,`addr5`,`addr6`,`type`)
  SELECT `bill_1`,`bill_2`,`bill_9`,`bill_3`,`bill_4`,`bill_5`,`bill_6`,`bill_7`,'bill'
  FROM `" . DB_PREFIX . "sales` WHERE `saleConfirmation` = 'yes' and `bill_1` != '' and `bill_2` != ''
  GROUP BY `bill_2` ORDER BY `id`");

  mc_upgradeLog('Completed: address book data ported to `' . DB_PREFIX . 'addressbook` table for ' . @number_format(@mysqli_affected_rows($GLOBALS["___msw_sqli"])) . ' accounts for billing fields');

  // Create address book entries for shipping..
  @mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "addressbook` (`nm`,`em`,`addr1`,`addr2`,`addr3`,`addr4`,`addr5`,`addr6`,`type`)
  SELECT `ship_1`,`ship_2`,`shipSetCountry`,`ship_3`,`ship_4`,`ship_5`,`ship_6`,`ship_7`,'ship'
  FROM `" . DB_PREFIX . "sales` WHERE `saleConfirmation` = 'yes' and `ship_1` != '' and `ship_2` != ''
  GROUP BY `ship_2` ORDER BY `id`");

  mc_upgradeLog('Completed: address book data ported to `' . DB_PREFIX . 'addressbook` table for ' . @number_format(@mysqli_affected_rows($GLOBALS["___msw_sqli"])) . ' accounts for shipping fields');

  // Update address book account id..
  @mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "addressbook` SET `account` = (SELECT `id` FROM `" . DB_PREFIX . "accounts` WHERE `" . DB_PREFIX . "accounts`.`email` = `em`)");

  mc_upgradeLog('Completed: account id updated in `' . DB_PREFIX . 'address book` table for ' . @number_format(@mysqli_affected_rows($GLOBALS["___msw_sqli"])) . ' accounts');

  // Drop newsletter column from sales & settings
  if (mswCheckColumn('sales', 'optInNewsletter') == 'yes') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` drop column `optInNewsletter`");
    mc_upgradeLog('Completed: optInNewsletter column added to `' . DB_PREFIX . 'sales` table');
  }

  if (mswCheckColumn('settings', 'optInNewsletter') == 'yes') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `optInNewsletter`");
    mc_upgradeLog('Completed: optInNewsletter column added to `' . DB_PREFIX . 'settings` table');
  }

  // Drop newsletter table
  if (mswCheckTable('newsletter') == 'yes') {
    @mysqli_query($GLOBALS["___msw_sqli"], "drop table `" . DB_PREFIX . "newsletter`");
    mc_upgradeLog('Completed: `' . DB_PREFIX . 'newsletter` table dropped');
  }

  mc_upgradeLog('Completed: account system created and updated');

}

if (mswCheckColumnType('accounts', 'pass', '40') == 'yes') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "accounts` change column `pass` `pass` varchar(250) not null default '' after `email`");
  mc_upgradeLog('Completed: pass column changed in `' . DB_PREFIX . 'accounts` table');
}

?>