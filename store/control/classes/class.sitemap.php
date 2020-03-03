<?php

class sitemap extends mcProducts {

  public $settings;
  public $rwr;
  public $cache;

  public function catlist() {
    $html = '';
    // Cached?
    $mCache = $this->cache->cache_options['cache_dir'] . '/sitemap-cat-' . MC_CACHE_FLAG . $this->cache->cache_options['cache_ext'];
    if ($this->cache->cache_options['cache_enable'] == 'yes' && file_exists($mCache)) {
      if ($this->cache->cache_exp($this->cache->cache_time($mCache)) == 'load') {
        return mc_loadTemplateFile($mCache);
      }
    }
    // Parents..
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
         WHERE `catLevel` = '1'
         AND `childOf`    = '0'
         AND `enCat`      = 'yes'
         AND " . MC_CATG_PMS_SQL . "
         ORDER BY `orderBy`
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($CATS = mysqli_fetch_object($q)) {
      $data  = '';
      $url = $this->rwr->url(array(
        $this->rwr->config['slugs']['cat'] . '/' . $CATS->id . '/1/' . ($CATS->rwslug ? $CATS->rwslug : $this->rwr->title($CATS->catname)),
        'c=' . $CATS->id
      ));
      // Parent products..
      $html .= str_replace(array(
        '{url}',
        '{category}'
      ), array(
        $url,
        mc_safeHTML($CATS->catname)
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/sitemap/cat-view-parent.htm'));
      // Children..
      $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                    WHERE `catLevel` = '2'
                    AND `childOf`    = '{$CATS->id}'
                    AND `enCat`      = 'yes'
                    AND " . MC_CATG_PMS_SQL . "
                    ORDER BY `orderBy`
                    ") or die(mc_MySQLError(__LINE__, __FILE__));
      if (mysqli_num_rows($q_children) > 0) {
        while ($CHILDREN = mysqli_fetch_object($q_children)) {
          $infants  = '';
          // Infants..
          $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                       WHERE `catLevel` = '3'
                       AND `childOf`    = '{$CHILDREN->id}'
                       AND `enCat`      = 'yes'
                       AND " . MC_CATG_PMS_SQL . "
                       ORDER BY `orderBy`
                       ") or die(mc_MySQLError(__LINE__, __FILE__));
          if (mysqli_num_rows($q_infants) > 0) {
            while ($INFANTS = mysqli_fetch_object($q_infants)) {
              $url = $this->rwr->url(array(
                $this->rwr->config['slugs']['cat'] . '/' . $INFANTS->id . '/1/' . ($INFANTS->rwslug ? $INFANTS->rwslug : $this->rwr->title($INFANTS->catname)),
                'c=' . $INFANTS->id
              ));
              // Parent products..
              $infants .= str_replace(array(
                '{url}',
                '{category}'
              ), array(
                $url,
                mc_safeHTML($INFANTS->catname)
              ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/sitemap/cat-view-infant.htm'));
            }
          }
          $url = $this->rwr->url(array(
            $this->rwr->config['slugs']['cat'] . '/' . $CHILDREN->id . '/1/' . ($CHILDREN->rwslug ? $CHILDREN->rwslug : $this->rwr->title($CHILDREN->catname)),
            'c=' . $CHILDREN->id
          ));
          $data .= str_replace(array(
            '{url}',
            '{category}',
            '{infants}'
          ), array(
            $url,
            mc_safeHTML($CHILDREN->catname),
            $infants
          ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/sitemap/cat-view-child.htm'));
        }
      }
      // Build for this group..
      if ($data) {
        $html .= str_replace(array(
          '{data}'
        ), array(
          $data
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/sitemap/cat-view-wrapper.htm'));
      }
    }
    if ($html) {
      $this->cache->cache_file($mCache, $html);
    }
    return $html;
  }

  public function extras() {
    global $msg_script75, $msg_public_header36;
    // Cached?
    $mCache = $this->cache->cache_options['cache_dir'] . '/sitemap-extra-' . MC_CACHE_FLAG . $this->cache->cache_options['cache_ext'];
    if ($this->cache->cache_options['cache_enable'] == 'yes' && file_exists($mCache)) {
      if ($this->cache->cache_exp($this->cache->cache_time($mCache)) == 'load') {
        return mc_loadTemplateFile($mCache);
      }
    }
    $html   = '';
    $data   = '';
    $incr   = 0;
    if (defined('MC_TRADE_DISCOUNT')) {
      $hmGift = 0;
    } else {
      $hmGift = mc_rowCount('giftcerts WHERE `enabled` = \'yes\'');
    }
    $link = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/sitemap/cat-view-pages.htm');
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "newpages`
             WHERE `enabled`     = 'yes'
             AND `landingPage`   = 'no'
             " . (defined('MC_TRADE_DISCOUNT') ? 'AND `trade` IN(\'yes\',\'no\')' : 'AND `trade` IN(\'no\')') . "
             ORDER BY `pageName`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    if (mysqli_num_rows($query) > 0) {
      $html .= str_replace(array(
        '{other}'
      ), array(
        $msg_script75
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/sitemap/cat-view-other.htm'));
      while ($LINKS = mysqli_fetch_object($query)) {
        ++$incr;
        if ($LINKS->linkExternal == 'yes') {
          $url    = trim($LINKS->pageText);
          $target = ($LINKS->linkTarget == 'new' ? ' onclick="window.open(this);return false"' : '');
        } else {
          $url = $this->rwr->url(array(
            $this->rwr->config['slugs']['npg'] . '/' . $LINKS->id . '/' . ($LINKS->rwslug ? $LINKS->rwslug : $this->rwr->title($LINKS->pageName)),
            'np=' . $LINKS->id
          ));
          $target = '';
        }
        $data .= str_replace(array(
          '{url}',
          '{text}',
          '{target}'
        ), array(
          $url,
          mc_safeHTML($LINKS->pageName),
          $target
        ), $link);
      }
    }
    // Pop gift certs on the end..
    if ($hmGift > 0) {
      $data .= str_replace(array(
        '{url}',
        '{text}',
        '{target}'
      ), array(
        $this->rwr->url(array('gift')),
        $msg_public_header36,
        ''
      ), $link);
    }
    $tmp = $html . ($data ? str_replace(array(
      '{data}'
    ), array(
      $data
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/sitemap/cat-view-wrapper.htm'))
      : '');
    if ($tmp) {
      $this->cache->cache_file($mCache, $tmp);
    }
    return $tmp;
  }

}

?>