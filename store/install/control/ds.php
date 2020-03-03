<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//========================
// DEMO STORE ITEMS
//========================

$find = array(
  '{prefix}',
  '{date}',
  '{script}',
  '{version}'
);
$rep = array(
  DB_PREFIX,
  date('Y-m-d'),
  SCRIPT_NAME,
  SCRIPT_VERSION
);

foreach (
  array(
    'categories','attributes','attr_groups','brands','personalisation','pictures','price_points',
    'products','prod_brand','prod_category','zones','zone_areas','services','rates','news_ticker',
    'prod_relation', 'giftcerts'
  ) AS $ins_demo) {
  @mysqli_query($GLOBALS["___msw_sqli"], "TRUNCATE TABLE `" . DB_PREFIX . $ins_demo . "`");
  $qDT = mysqli_query($GLOBALS["___msw_sqli"], str_replace($find,$rep,@file_get_contents(PATH . 'control/sql/demo/' . $ins_demo . '.sql')));
  if (!$qDT) {
    $data[] = DB_PREFIX . $ins_demo;
    mc_logDBError(DB_PREFIX . $ins_demo,((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)),((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)),__LINE__,__FILE__,'Insert');
  }
}

?>