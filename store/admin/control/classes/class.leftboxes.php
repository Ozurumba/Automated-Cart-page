<?php

class mcBoxContr {

  public $settings;

  public function add() {
    $box = (isset($_POST['box']) ? $_POST['box'] : '');
    if ($box) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "boxes` (
        `tmp`
        ) VALUES (
        '" . mc_safeSQL($box) . "'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
        $ID = mysqli_insert_id($GLOBALS["___msw_sqli"]);
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "boxes` SET
        `orderby`  = `id`
        WHERE `id` = '{$ID}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function flag() {
    $st = (isset($_GET['flag']) ? $_GET['flag'] : 'yes');
    $id = (isset($_GET['id']) ? (int) $_GET['id'] : '0');
    switch($st) {
      case 'fa fa-flag fa-fw mc-green':
        $status = 'no';
        break;
      case 'fa fa-flag-o fa-fw':
        $status = 'yes';
        break;
      default:
        $status = 'yes';
        break;
    }
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "boxes` SET
    `status`   = '{$status}'
    WHERE `id` = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function update() {
    for ($i=0; $i<count($_POST['box']); $i++) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "boxes` SET
      `name`     = '" . mc_safeSQL($_POST['name'][$i]) . "',
      `orderby`  = '" . ($i + 1) . "'
      WHERE `id` = '{$_POST['box'][$i]}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "boxes`
    WHERE `id` NOT IN(" . mc_safeSQL(implode(',',$_POST['box'])) .")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mc_tableTruncationRoutine(array(
      'boxes'
    ));
  }

}

?>