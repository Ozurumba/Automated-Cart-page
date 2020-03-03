<?php

class newsletter {

  public $settings;
  public $dl;

  public function updateNewsTemplate() {
    $_POST        = mc_safeImport($_POST);
    $_GET['load'] = (int) $_GET['load'];
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "newstemplates` SET
    `name`     = '{$_POST['from']}',
    `email`    = '{$_POST['email']}',
    `subject`  = '{$_POST['subject']}',
    `html`     = '{$_POST['html']}',
    `plain`    = '{$_POST['plain']}'
    WHERE `id` = '{$_GET['load']}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function searchTemplates() {
    $ar  = array();
    $q   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "newstemplates`
           WHERE (LOWER(`subject`) LIKE '%" . strtolower(mc_safeSQL($_GET['term'])) . "%'
            OR LOWER(`html`) LIKE '%" . strtolower(mc_safeSQL($_GET['term'])) . "%'
            OR LOWER(`plain`) LIKE '%" . strtolower(mc_safeSQL($_GET['term'])) . "%'
           )
           ORDER BY `subject`
           ");
    while ($T = mysqli_fetch_object($q)) {
      $ar[] = array(
        'value' => $T->id,
        'label' => mc_cleanData($T->subject)
      );
    }
    return $ar;
  }

  public function deleteNewsTemplate() {
    $_GET['del'] = (int) $_GET['del'];
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "newstemplates` WHERE `id` = '{$_GET['del']}'") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'newstemplates'
    ));
    return $rows;
  }

  public function addNewsTemplate() {
    $_POST = mc_safeImport($_POST);
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "newstemplates`
         WHERE `subject` = '{$_POST['subject']}'
         LIMIT 1
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    $TMP = mysqli_fetch_object($q);
    if (!isset($TMP->id)) {
      mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "newstemplates` (
      `name`,
      `email`,
      `subject`,
      `html`,
      `plain`
      ) VALUES (
      '{$_POST['from']}',
      '{$_POST['email']}',
      '{$_POST['subject']}',
      '{$_POST['html']}',
      '{$_POST['plain']}'
      )") or die(mc_MySQLError(__LINE__, __FILE__));
      return 'OK';
    } else {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "newstemplates` SET
      `name`    = '{$_POST['from']}',
      `email`   = '{$_POST['email']}',
      `subject` = '{$_POST['subject']}',
      `html`    = '{$_POST['html']}',
      `plain`   = '{$_POST['plain']}'
      WHERE `id` = '{$TMP->id}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      return 'UP';
    }
  }

  public function resetNewsletter() {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
    `newsletter`  = 'no'
    WHERE `id`    = '" . mc_digitSan($_GET['reset']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function exportNewsletter() {
    global $msg_news_letter;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    $SQL  = '';
    $appd = 'all';
    if (isset($_GET['type']) && in_array($_GET['type'],array('personal','trade'))) {
      $SQL  = 'AND `type` = \'' . mc_safeSQL($_GET['type']) . '\'';
      $appd = $_GET['type'];
    }
    $q_l = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `name`,`email` FROM `" . DB_PREFIX . "accounts`
           WHERE `enabled` = 'yes'
           AND `verified` = 'yes'
           AND `newsletter` = 'yes'
           $SQL
           ORDER BY `name`,`email`
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    $separator = ',';
    $csvFile   = PATH . 'import/newsletter-' . date('d-m-Y-His') . '.csv';
    $data      = '';
    while ($AC = mysqli_fetch_object($q_l)) {
      $data .= mc_cleanCSV($AC->name, $separator) . $separator . mc_cleanCSV($AC->email, $separator) . mc_defineNewline();
    }
    if ($data) {
      $this->dl->write($csvFile, $msg_news_letter[4] . mc_defineNewline() . trim($data));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    }
  }

}

?>