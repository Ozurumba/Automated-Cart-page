<?php

class pagination {

  public function __construct($data = array(), $query, $admin = 'no', $sts = '') {
    $this->total = $data[0];
    $this->start = 0;
    $this->text  = $data[1];
    $this->query = $query;
    $this->split = (defined('MC_PLATFORM_DETECTION') && MC_PLATFORM_DETECTION == 'mobile' ? 3 : 5);
    $this->page  = $data[2];
    $this->admin = $admin;
    $this->modr  = (isset($sts->en_modr) ? $sts->en_modr : 'no');
    $this->flag  = (isset($data[3]) ? explode(',', $data[3]) : array());
  }

  public function perpage() {
    return PER_PAGE;
  }

  public function qstring() {
    $qstring = array();
    if (!empty($_GET)) {
      foreach ($_GET AS $k => $v) {
        if (is_array($v)) {
          foreach ($v AS $v2) {
            $qstring[] = $k . '[]=' . urlencode($v2);
          }
        } else {
          $merge = array_merge($this->flag, array(
            'p',
            'next',
            'deleted'
          ));
          if (!in_array($k, $merge)) {
            $qstring[] = $k . '=' . urlencode($v);
          }
        }
      }
    }
    return (!empty($qstring) ? '&amp;' . implode('&amp;', $qstring) : '');
  }

  public function setUrl($page) {
    switch($this->admin) {
      case 'yes':
        return $this->query . $page . pagination::qstring();
        break;
      default:
        switch($this->modr) {
          case 'yes':
            return str_replace('{page}', $page, $this->query);
            break;
          default:
            return $this->query . $page . pagination::qstring();
            break;
        }
        break;
    }
  }
  
  public function tmp($file) {
    if ($this->admin == 'yes') {
      return mc_loadTemplateFile(PATH . 'templates/html/pagination/' . $file);
    }
    return mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/pagination/' . $file);
  }

  public function display() {
    $html = array();
    // How many pages?
    $this->num_pages = ceil($this->total / pagination::perpage());
    // If pages less than or equal to 1, display nothing..
    if ($this->num_pages <= 1) {
      return $html;
    }
    // Build pages..
    $current_page = $this->page;
    $begin        = $current_page - $this->split;
    $end          = $current_page + $this->split;
    if ($begin < 1) {
      $begin = 1;
      $end   = $this->split * 2;
    }
    if ($end > $this->num_pages) {
      $end   = $this->num_pages;
      $begin = $end - ($this->split * 2);
      $begin++;
      if ($begin < 1) {
        $begin = 1;
      }
    }
    if ($current_page != 1) {
      $html[] = str_replace(array('{text}','{url}'), array(mc_safeHTML($this->text[0]),pagination::setUrl(1)), pagination::tmp('previous-first.htm'));
      $html[] = str_replace(array('{text}','{url}'), array(mc_safeHTML($this->text[1]),pagination::setUrl(($current_page - 1))), pagination::tmp('previous-last.htm'));
    } else {
      $html[] = pagination::tmp('previous-first-disabled.htm');
      $html[] = pagination::tmp('previous-last-disabled.htm');
    }
    for ($i = $begin; $i <= $end; $i++) {
      $html[] = str_replace(array('{page}','{url}'), array($i,($i != $current_page ? pagination::setUrl($i) : '#')), pagination::tmp(($i != $current_page ? 'page.htm' : 'page-current.htm')));
    }
    if ($current_page != $this->num_pages) {
      $html[] = str_replace(array('{text}','{url}'), array(mc_safeHTML($this->text[2]),pagination::setUrl(($current_page + 1))), pagination::tmp('next-last.htm'));
      $html[] = str_replace(array('{text}','{url}'), array(mc_safeHTML($this->text[3]),pagination::setUrl($this->num_pages)), pagination::tmp('next-first.htm'));
    } else {
      $html[] = pagination::tmp('next-last-disabled.htm');
      $html[] = pagination::tmp('next-first-disabled.htm');
    }
    return str_replace('{pages}', implode(mc_defineNewline(), $html), pagination::tmp('wrapper.htm'));
  }

}

?>