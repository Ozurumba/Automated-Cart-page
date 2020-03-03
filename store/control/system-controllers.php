<?php

if (!defined('PARENT') || !isset($SETTINGS->id)) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// LOAD INCLUDE FILES
mc_fileController();
include(PATH . 'control/languages.php');
include(PATH . 'control/system/core/sys-controller.php');
include(PATH . 'control/engine/Savant3.php');
include(PATH . 'control/currencies.php');
include(PATH . 'control/classes/class.mobile-detection.php');
include(PATH . 'control/classes/class.page.php');
include(PATH . 'control/classes/class.json.php');
include(PATH . 'control/classes/class.system.php');
include(PATH . 'control/classes/class.products.php');
include(PATH . 'control/classes/class.currencies.php');
include(PATH . 'control/classes/class.cart.php');
include(PATH . 'control/classes/class.gift.php');
include(PATH . 'control/classes/class.parser.php');
include(PATH . 'control/classes/mailer/class.send.php');
include(PATH . 'control/classes/class.bbCode.php');
include(PATH . 'control/gateways/class.handler.php');
include(PATH . 'control/classes/class.accounts.php');
include(PATH . 'control/classes/class.shipping.php');
include(PATH . 'control/classes/class.checkout.php');
include(PATH . 'control/classes/class.social.php');
include(PATH . 'control/classes/class.leftmenu.php');

// DECLARE CLASS OBJECTS
$MCJSON              = new jsonHandler();
$MCPDTC              = new Mobile_Detect();
$MCMAIL              = new mcMail();
$MCPARSER            = new mcDataParser();
$MCPROD              = new mcProducts();
$MCSYS               = new mcSystem();
$MCCRV               = new curConverter();
$MCCART              = new shoppingCart();
$MCGIFT              = new giftCertificate();
$MCBB                = new bbCode_Parser();
$MCACC               = new mcAccounts();
$MCCKO               = new mcCheckout();
$MCSOCIAL            = new mcSocial();
$MCMENUCLS           = new mcLeftMenu();
$MCSHIP              = new mcShipping();
$MCSYS->settings     = $SETTINGS;
$MCSYS->cache        = $MCCACHE;
$MCSYS->rwr          = $MCRWR;
$MCPROD->settings    = $SETTINGS;
$MCPROD->cache       = $MCCACHE;
$MCPROD->system      = $MCSYS;
$MCPROD->social      = $MCSOCIAL;
$MCPROD->rwr         = $MCRWR;
$MCCRV->settings     = $SETTINGS;
$MCCRV->settings     = $SETTINGS;
$MCCART->settings    = $SETTINGS;
$MCCART->rwr         = $MCRWR;
$MCGIFT->settings    = $SETTINGS;
$MCACC->settings     = $SETTINGS;
$MCACC->json         = $MCJSON;
$MCACC->rwr          = $MCRWR;
$MCACC->products     = $MCPROD;
$MCMAIL->parser      = $MCPARSER;
$MCCKO->settings     = $SETTINGS;
$MCCKO->products     = $MCPROD;
$MCCKO->cart         = $MCCART;
$MCCKO->rwr          = $MCRWR;
$MCCKO->account      = $MCACC;
$MCSOCIAL->json      = $MCJSON;
$MCSOCIAL->cache     = $MCCACHE;
$MCSOCIAL->settings  = $SETTINGS;
$MCSOCIAL->rwr       = $MCRWR;
$MCBB->settings      = $SETTINGS;
$MCMENUCLS->settings = $SETTINGS;
$MCMENUCLS->system   = $MCSYS;
$MCMENUCLS->products = $MCPROD;
$MCMENUCLS->rwr      = $MCRWR;
$MCMENUCLS->cache    = $MCCACHE;
$MCMENUCLS->account  = $MCACC;
$MCSHIP->settings    = $SETTINGS;
$MCSHIP->products    = $MCPROD;
$MCSHIP->cart        = $MCCART;
$MCSHIP->rwr         = $MCRWR;
$MCSHIP->checkout    = $MCCKO;

?>