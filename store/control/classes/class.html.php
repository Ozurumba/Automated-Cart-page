<?php

class mcHtml {

  public $settings;

  public function loadStates($area, $value = '', $arr = array(), $tmp = array()) {
    if (!isset($tmp['box'])) {
      $box = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-states-input.htm');
    } else {
      $box = mc_loadTemplateFile($tmp['box']);
    }
    if (!isset($tmp['option'])) {
      $option = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-states-select-option.htm');
    } else {
      $option = mc_loadTemplateFile($tmp['option']);
    }
    if (!isset($tmp['select'])) {
      $select = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-states-select.htm');
    } else {
      $select = mc_loadTemplateFile($tmp['select']);
    }
    if (!empty($arr)) {
      $html = '';
      foreach ($arr AS $id => $val) {
        $html .= str_replace(array('{value}', '{text}','{selected}'), array($id, $val, ($value == $id ? ' selected="selected"': '')), $option);
      }
      if ($html) {
        return str_replace(array('{options}','{field}'), array($html, $area), $select);
      }
    }
    return str_replace(array('{value}', '{field}'), array($value, $area), $box);
  }

  public function loadShippingCountries($val = '', $set = 0) {
    $string = '';
    $q_ctry = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
              WHERE `enCountry`  = 'yes'
              ORDER BY `cName`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($CTY = mysqli_fetch_object($q_ctry)) {
      $slect  = '';
      if (isset($_GET['country']) && $_GET['country'] == $CTY->id) {
        $slect = ' selected="selected"';
      }
      if ($set > 0 && $set == $CTY->id) {
        $slect = ' selected="selected"';
      }
      $string .= str_replace(array(
        '{value}',
        '{selected}',
        '{name}',
        '{theme_folder}'
      ), array(
        $CTY->id,
        $slect,
        mc_safeHTML($CTY->cName),
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/html-option-tags.htm') . mc_defineNewline());
    }
    return trim($string);
  }

}

?>