<?php if (!defined('PATH')) { exit; }

$msTopMenu = array();

if (LICENCE_VER == 'locked' || defined('LIC_DEV')) {
  $msTopMenu[] = array(
    'url' => 'index.php?p=purchase',
    'icon' => 'fa-shopping-cart',
    'text' => 'Purchase',
    'class' => 'hidden-sm hidden-xs hidden-md',
    'ext' => 'no'
  );
}

$msTopMenu[] = array(
  'url' => 'index.php',
  'icon' => 'fa-dashboard',
  'text' => mc_cleanDataEntVars($msg_header9),
  'class' => 'hidden-sm hidden-xs',
  'ext' => 'no'
);

if (mc_tweetPerms($sysCartUser) == 'yes' && $SETTINGS->tweet == 'yes') {
  $msTopMenu[] = array(
    'url' => 'index.php?tweet=yes',
    'icon' => 'fa-twitter',
    'text' => mc_cleanDataEntVars($msg_admin3_0[52]),
    'class' => 'hidden-sm hidden-xs',
    'ext' => 'no',
    'js_code' => ' onclick="mc_Window(this.href, 300, 450, \'\');return false;"'
  );
}

$msTopMenu[] = array(
  'url' => '../',
  'icon' => 'fa-desktop',
  'text' => mc_cleanDataEntVars($msg_header16),
  'class' => 'hidden-sm hidden-xs',
  'ext' => 'yes'
);

if (DISPLAY_HELP_LINK) {
  $msTopMenu[] = array(
    'url' => (isset($helpPages[$helpTag]) ? '../docs/' . $helpPages[$helpTag] . '.html' : '../docs/index.html'),
    'icon' => 'fa-question-circle',
    'text' => mc_cleanDataEntVars($msg_header13),
    'class' => 'hidden-sm hidden-xs',
    'ext' => 'yes'
  );
}

$msTopMenu[] = array(
  'url' => 'index.php?p=logout',
  'icon' => 'fa-unlock',
  'text' => mc_cleanDataEntVars($msg_javascript37),
  'class' => 'hidden-sm hidden-xs',
  'ext' => 'no'
);

?>