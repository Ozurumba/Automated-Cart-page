<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// PRODUCTS / CATEGORIES
//==========================

// New category columns..
if (mswCheckColumn('categories', 'rwslug') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "categories` add column `rwslug` varchar(250) not null default ''");
  mc_upgradeLog('Completed: rwslug column added to `' . DB_PREFIX . 'categories` table');
}
if (mswCheckColumn('categories', 'theme') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "categories` add column `theme` varchar(200) not null default ''");
  mc_upgradeLog('Completed: theme column added to `' . DB_PREFIX . 'categories` table');
}
if (mswCheckColumn('categories', 'vis') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "categories` add column `vis` varchar(30) not null default ''");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "categories` set `vis` = '1'");
  mc_upgradeLog('Completed: vis column added to `' . DB_PREFIX . 'categories` table');
}

// New product columns..
if (mswCheckColumn('products', 'pNotes') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `pNotes` text default null");
  mc_upgradeLog('Completed: pNotes column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'rwslug') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `rwslug` varchar(250) not null default ''");
  mc_upgradeLog('Completed: rwslug column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'pAvailableText') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `pAvailableText` varchar(250) not null default ''");
  mc_upgradeLog('Completed: pAvailableText column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'pCube') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `pCube` int(10) not null default '0'");
  mc_upgradeLog('Completed: pCube column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'pGuardian') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `pGuardian` int(10) not null default '0' after `pCube`");
  mc_upgradeLog('Completed: pGuardian column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'pMultiBuy') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `pMultiBuy` int(10) not null default '0' after `pOffer`");
  mc_upgradeLog('Completed: pMultiBuy column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'maxPurchaseQty') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `maxPurchaseQty` int(10) not null default '0' after `minPurchaseQty`");
  mc_upgradeLog('Completed: maxPurchaseQty column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'dropshipping') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `dropshipping` int(8) not null default '0'");
  mc_upgradeLog('Completed: dropshipping column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'expiry') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `expiry` date not null default '0000-00-00'");
  mc_upgradeLog('Completed: expiry column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'exp_price') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `exp_price` varchar(10) not null default ''");
  mc_upgradeLog('Completed: exp_price column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'exp_special') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `exp_special` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: exp_special column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'exp_send') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `exp_send` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: exp_send column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'exp_text') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` `exp_text` text default null");
  mc_upgradeLog('Completed: exp_text column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'pVideo2') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `pVideo2` varchar(250) not null default '' after `pVideo`");
  mc_upgradeLog('Completed: pVideo2 column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'pPurPrice') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `pPurPrice` varchar(20) not null default '0.00'");
  mc_upgradeLog('Completed: pPurPrice column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'pVideo3') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `pVideo3` varchar(250) not null default '' after `pVideo2`");
  mc_upgradeLog('Completed: pVideo3 column added to `' . DB_PREFIX . 'products` table');

  // Clear current video field..
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "products` set `pVideo` = ''");
  if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
    mc_upgradeLog('Completed: pVideo field cleared in products `' . DB_PREFIX . 'products` table');
  }
}

// Port brands to new table..
if (mswCheckTable('prod_brand') == 'yes' && mc_rowCount('prod_brand') == 0 && mswCheckColumn('products', 'pBrands') == 'yes') {
  $q = @mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`pBrands` FROM `" . DB_PREFIX . "products`
       WHERE `pBrands` IS NOT NULL
       AND `pBrands` != ''
       ORDER BY `id`
       ");
  while ($PB = @mysqli_fetch_object($q)) {
    $cBr = explode(',', $PB->pBrands);
    foreach ($cBr AS $prdBrnds) {
      if ((int) $prdBrnds > 0) {
        @mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_brand` (
        `product`,`brand`
        ) VALUES (
        '{$PB->id}','" . (int) $prdBrnds . "'
        )");
      }
    }
  }

  // Drop brands from products table..
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` drop column `pBrands`");
  mc_upgradeLog('Completed: brands ported to new table. pBrands column dropped from `' . DB_PREFIX . 'products` table');
}

// Convert any old line breaks
@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "products` set
`pDescription` = REPLACE(`pDescription`,'<br />','<br>'),
`pShortDescription` = REPLACE(`pShortDescription`,'<br />','<br>')
");
mc_upgradeLog('Completed: old line breaks converted in `' . DB_PREFIX . 'products` table');

if (mswCheckColumn('pictures', 'pictitle') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "pictures` add column `pictitle` text default null");
  mc_upgradeLog('Completed: pictitle column added to `' . DB_PREFIX . 'pictures` table');
}
if (mswCheckColumn('pictures', 'picalt') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "pictures` add column `picalt` text default null");
  mc_upgradeLog('Completed: picalt column added to `' . DB_PREFIX . 'pictures` table');
}

?>