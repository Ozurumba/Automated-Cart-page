<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Build left display menu.
$leftBoxOrder   = $MCMENUCLS->menu_data();
$leftBoxDisplay = '';

// Construct menu..
for ($i = 0; $i < count($leftBoxOrder); $i++) {
  switch($leftBoxOrder[$i][0]) {
    case 'points':
      if (!isset($skipMenuBoxes['points']) && !defined('MC_TRADE_DISCOUNT')) {
        $leftBoxDisplay .= $MCMENUCLS->price_points(array(
          $leftBoxOrder[$i][1],
          $mc_search,
          $mc_leftmenu
        ));
      }
      break;
    case 'popular':
      if (!isset($skipMenuBoxes['popular'])) {
        $leftBoxDisplay .= $MCMENUCLS->popular_products($leftBoxOrder[$i][1]);
      }
      break;
    case 'recent':
      if (!isset($skipMenuBoxes['recent'])) {
        $leftBoxDisplay .= $MCMENUCLS->recently_viewed(array(
          $leftBoxOrder[$i][1],
          $msg_public_header9,
          $msg_public_header10,
          (isset($loggedInUser['id']) ? $loggedInUser : array())
        ));
      }
      break;
    case 'links':
      if (!isset($skipMenuBoxes['links'])) {
        $leftBoxDisplay .= $MCMENUCLS->new_pages($leftBoxOrder[$i][1]);
      }
      break;
    case 'brands':
      if (!isset($skipMenuBoxes['brands'])) {
        if (isset($brandCatDisplay) && isset($CAT->catname)) {
          $brandhtml = $MCMENUCLS->brands($brandCatDisplay, ($CAT->rwslug ? $CAT->rwslug : $MCRWR->title(mc_cleanData($CAT->catname))), (isset($thisParent->id) ? 'yes' : 'no'), array(
            $leftBoxOrder[$i][1],
            $mc_leftmenu
          ));
          // Load brands by default if category brands blank..
          if ($brandhtml == '' && $SETTINGS->showBrands == 'yes') {
            $leftBoxDisplay .= $MCMENUCLS->all_brands(array(
              $leftBoxOrder[$i][1],
              $mc_leftmenu
            ));
          } else {
            $leftBoxDisplay .= $brandhtml;
          }
        } else {
          // Load brands by default..
          if ($SETTINGS->showBrands == 'yes') {
            $leftBoxDisplay .= $MCMENUCLS->all_brands(array(
              $leftBoxOrder[$i][1],
              $mc_leftmenu
            ));
          }
        }
      }
      break;
    case 'tweets':
      if (!isset($skipMenuBoxes['tweets'])) {
        if (isset($twPar['twitter']['username'])) {
          $leftBoxDisplay .= $MCMENUCLS->tweets($twPar, $leftBoxOrder[$i][1]);
        }
      }
      break;
    case 'rss':
      if (!isset($skipMenuBoxes['rss'])) {
        $leftBoxDisplay .= $MCMENUCLS->rss($leftBoxOrder[$i][1]);
      }
      break;
    // Custom templates..
    default:
      if (!isset($skipMenuBoxes['pages'])) {
        if ($leftBoxOrder[$i][2] && substr($leftBoxOrder[$i][2], -4) == '.php' && substr($leftBoxOrder[$i][2], 0, 4) == 'box-' &&
            file_exists(THEME_FOLDER . '/customTemplates/' . $leftBoxOrder[$i][2])) {
          $tpl = mc_getSavant();
          $tpl->assign('TITLE', $leftBoxOrder[$i][1]);
          $leftBoxDisplay .= $tpl->fetch(THEME_FOLDER . '/customTemplates/' . $leftBoxOrder[$i][2]);
        }
      }
      break;
  }
}

?>