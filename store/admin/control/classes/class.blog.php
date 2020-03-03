<?php

class blog {

  public $settings;

  // Add Blog..
  public function add() {
    $_POST = mc_safeImport($_POST);
    $dates = array(
      'created' => date('Y-m-d'),
      'published' => (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['published'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['published'], $this->settings) : date('Y-m-d')),
      'autodelete' => (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['autodelete'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['autodelete'], $this->settings) : '0000-00-00')
    );
    // Publish date can`t be in the past..
    if (strtotime($dates['published']) < strtotime(date('Y-m-d'))) {
      $dates['published'] = date('Y-m-d');
    }
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT IGNORE INTO `" . DB_PREFIX . "blog` (
    `title`,
	  `message`,
	  `created`,
	  `published`,
	  `autodelete`,
	  `enabled`
    ) VALUES (
    '{$_POST['title']}',
    '{$_POST['message']}',
    '" . strtotime($dates['created']) . "',
    '" . strtotime($dates['published']) . "',
    '" . strtotime($dates['autodelete']) . "',
    'yes'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  // Update blog..
  public function update() {
    $_POST = mc_safeImport($_POST);
    $dates = array(
      'created' => date('Y-m-d'),
      'published' => (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['published'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['published'], $this->settings) : date('Y-m-d')),
      'autodelete' => (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['autodelete'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['autodelete'], $this->settings) : '0000-00-00')
    );
    $enabled = (isset($_POST['enabled']) && in_array($_POST['enabled'], array('yes','no')) ? $_POST['enabled'] : 'no');
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "blog` SET
    `title` = '{$_POST['title']}',
	  `message` = '{$_POST['message']}',
	  `created` = '" . strtotime($dates['created']) . "',
	  `published` = '" . strtotime($dates['published']) . "',
	  `autodelete` = '" . strtotime($dates['autodelete']) . "',
	  `enabled` = '{$enabled}'
    WHERE `id` = '" . (int) $_GET['edit'] . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  // Delete blog..
  public function delete() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "blog` WHERE `id` = '" . mc_digitSan($_GET['del']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'blog'
    ));
    return $rows;
  }

}

?>