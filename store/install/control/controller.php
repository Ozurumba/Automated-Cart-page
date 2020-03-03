<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

$cSets       = array();
$defaultSet  = 'utf8_general_ci';

// Get MySQL version..
$query       = @mysqli_query($GLOBALS["___msw_sqli"], "SELECT VERSION() AS `v`");
$VERSION     = @mysqli_fetch_object($query);

// Character sets..
$DCHARSET = @mysqli_query($GLOBALS["___msw_sqli"], "SHOW CHARACTER SET");
while ($CH  = mysqli_fetch_object($DCHARSET)) {
  if (is_object($CH)) {
    $CH_SET = (array)$CH;
    if (isset($CH_SET['Charset'])) {
      $DCOLL = @mysqli_query($GLOBALS["___msw_sqli"], "SHOW COLLATION LIKE '" . $CH_SET['Charset'] . "%'");
      while ($COL  = mysqli_fetch_object($DCOLL)) {
        if (is_object($COL)) {
          $COL_SET = (array) $COL;
          if (isset($COL_SET['Collation'])) {
            $cSets[] = $COL_SET['Collation'];
          }
        }
      }
    }
  }
}

if (!empty($cSets)) {
  sort($cSets);
}

if (isset($VERSION->v)) {
  $mysqlVer  = $VERSION->v;
} else {
  $mysqlVer  = 5;
}

?>