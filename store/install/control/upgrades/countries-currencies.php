<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// COUNTRIES / CURRENCIES
//==========================

if (mswCheckColumn('countries', 'iso4217') == 'no') {

  // Get current countries that are enabled or where local pickup is enabled..
  $cnt = array(
    array(),
    array()
  );
  if (mswCheckTable('countries') == 'yes') {

    mc_upgradeLog('Completed: Building country data for enabled and localpickup');

    $q = @mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`enCountry`,`localPickup`,`cISO` FROM `" . DB_PREFIX . "countries`
         WHERE `enCountry` = 'yes'
         OR `localPickup`  = 'yes'
         ORDER BY `id`
         ");
    while ($C = @mysqli_fetch_object($q)) {
      if ($C->enCountry == 'yes') {
        $cnt[0][] = "'{$C->cISO}'";
      }
      if ($C->localPickup == 'yes') {
        $cnt[1][] = "'{$C->cISO}'";
      }
    }

  }

  // Create new table with iso4217 / ISO_2 info..
  include(PATH . 'control/upgrades/appendix/countries.php');

  // Update countries that were enabled or had local pickup enabled..
  if (!empty($cnt[0])) {
    mc_upgradeLog('Completed: Updating enabled countries: ' . print_r($cnt[0], true));
    @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "countries` set
    `enCountry` = 'yes'
    WHERE `cISO` IN(" . implode(',', $cnt[0]) . ")
    ");
  }
  if (!empty($cnt[1])) {
    mc_upgradeLog('Completed: Updating enabled local pickup countries: ' . print_r($cnt[1], true));
    @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "countries` set
    `localPickup` = 'yes'
    WHERE `cISO` IN(" . implode(',', $cnt[1]) . ")
    ");
  }

}

if (mswCheckColumn('countries', 'freeship') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "countries` add column `freeship` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: freeship column added to `' . DB_PREFIX . 'countries` table');
}

// Currencies display preference..
if (mswCheckColumn('currencies', 'currencyDisplayPref') == 'no') {

  // Add new column..
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "currencies` add column `currencyDisplayPref` varchar(100) not null default ''");

  // Update display preference for popular currencies..
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "currencies` set `currencyDisplayPref` = '&pound;{PRICE}' where `currency` = 'GBP' LIMIT 1");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "currencies` set `currencyDisplayPref` = '{PRICE}&amp;euro;' where `currency` = 'EUR' LIMIT 1");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "currencies` set `currencyDisplayPref` = '{PRICE}&#165;' where `currency` = 'JPY' LIMIT 1");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "currencies` set `currencyDisplayPref` = 'US&#036;{PRICE}' where `currency` = 'USD' LIMIT 1");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "currencies` set `currencyDisplayPref` = 'HK&#036;{PRICE}' where `currency` = 'HKD' LIMIT 1");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "currencies` set `currencyDisplayPref` = '&#036;{PRICE}AUD' where `currency` = 'AUD' LIMIT 1");

  // Make sure base currency is 1 for converter..
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "currencies` set `rate` = '1' where `currency` = '{$SETTINGS->baseCurrency}' LIMIT 1");

  mc_upgradeLog('Completed: Updating `' . DB_PREFIX . 'currencies` table');

}

// Main currency display preference..
if (!property_exists($SETTINGS, 'currencyDisplayPref')) {

  // Add new column..
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `currencyDisplayPref` varchar(100) not null default '' after `baseCurrency`");

  // Update currency display preference options..
  $display = array(
    'GBP' => '&pound;{PRICE}',
    'EUR' => '{PRICE}&amp;euro;',
    'JPY' => '{PRICE}&#165;',
    'USD' => 'US&#036;{PRICE}',
    'HKD' => 'HK&#036;{PRICE}',
    'AUD' => '&#036;{PRICE}AUD'
  );
  if (in_array($SETTINGS->baseCurrency, array_keys($display))) {
    @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "settings` set `currencyDisplayPref` = '" . $display[$SETTINGS->baseCurrency] . "' LIMIT 1");
  } else {
    @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "settings` set `currencyDisplayPref` = '" . $SETTINGS->baseCurrency . "{PRICE}' LIMIT 1");
  }

  mc_upgradeLog('Completed: Currency display preferences updated. Base currency is: ' . $SETTINGS->baseCurrency);

}

if (mswCheckColumnType('countries', 'id', 'auto_increment') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "countries` add primary key(`id`)");
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "countries` change column `id` `id` int(4) not null auto_increment first");
  mc_upgradeLog('Completed: id column changed in `' . DB_PREFIX . 'countries` table');
}

if (mswCheckColumnType('currencies', 'rate', 'float') == 'yes') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "currencies` change column `rate` `rate` varchar(20) not null default '' after `currency`");
  mc_upgradeLog('Completed: rate column changed in `' . DB_PREFIX . 'currencies` table');
}

?>