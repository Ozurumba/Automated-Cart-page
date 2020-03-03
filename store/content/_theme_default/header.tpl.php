<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->LANG; ?>" dir="<?php echo $this->DIR; ?>">
	<head>
    <meta charset="<?php echo $this->CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->TITLE; ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="<?php echo $this->META_KEYS; ?>">
    <meta name="description" content="<?php echo $this->META_DESC; ?>">
    <base href="<?php echo $this->BASE_PATH; ?>/">
    <link rel="stylesheet" href="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/css/menu.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/css/style.css" type="text/css">
    <?php
    // Main RSS Enabled?
    if ($this->SETTINGS['en_rss'] == 'yes') {
    ?>
    <link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo $this->RSS_LINK; ?>">
    <?php
    }
    // CSS / Meta modules that are only required on certain pages
    echo $this->MODULES;
    // Structured Meta Data..
    echo $this->STRUCTURED_META_DATA;
    ?>
    <link rel="icon" href="<?php echo $this->BASE_PATH; ?>/favicon.ico">
	</head>

	<body>

  <div id="mshtmlwrapper" class="push">

    <?php
    // Only show very top bar if user logged in..
    if (isset($this->ACCOUNT['id'])) {
    ?>
    <div class="topaccbar">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
           <span class="pull-right">
             <i class="fa fa-lock fa-fw"></i> <a href="#" onclick="mc_accMenu();return false"><?php echo $this->TEXT[10][11]; ?></a>
           </span>
           <span class="hidden-xs"><?php echo $this->GREETING_MSG; ?></span>
          </div>
        </div>
      </div>
    </div>
    <?php
    }

    // OPTIONS PANEL
    // Closed by default.
    ?>
    <div class="top_optionswrapper" style="display:none">
      <div class="container">
        <div class="text"><?php echo $this->TEXT[10][5]; ?></div>
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="text-left cur">
              <div class="btn-group">
                <button type="button" class="btn btn-info"><i class="fa fa-money fa-fw"></i><span class="hidden-xs"> <?php echo $this->TEXT[10][6]; ?></span></button>
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <?php
                  // CURRENCIES
                  // html/options-li.htm
                  echo $this->OPTS['currencies'];
                  ?>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="text-right lang">
              <div class="btn-group">
                <button type="button" class="btn btn-info"><i class="fa fa-flag fa-fw"></i><span class="hidden-xs"> <?php echo $this->TEXT[10][8]; ?></span></button>
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <?php
                  // LANGUAGES
                  // html/options-li.htm
                  echo $this->OPTS['languages'];
                  ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
    // NAV TOP BAR..
    ?>
    <div class="topbar">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-7 col-xs-6">
            <div class="btn-group">
							<button class="btn btn-default btn-small menu-btn" data-toggle="dropdown">
								<span id="mmwrap"><i class="fa fa-tags fa-fw"></i> <span class="hidden-xs"><?php echo $this->TEXT[10][3]; ?></span> </span><i id="lmitag" class="fa fa-angle-right fa-fw"></i>
							</button>
              <button id="panelbutton" type="button" class="btn btn-info btn-small" title="<?php echo mc_safeHTML($this->TEXT[10][4]); ?>" onclick="mc_optionsPanel('open','yes')">
                <i class="fa fa-cogs fa-fw"></i>
              </button>
						</div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-5 col-xs-6 text-right">
            <form method="get" id="mc_sc_area" action="<?php echo $this->BASE_PATH; ?>">
            <div class="input-group searchbox">
              <input type="text" name="q" value="<?php echo (isset($_GET['q']) ? mc_safeHTML($_GET['q']) : ''); ?>" placeholder="<?php echo $this->TEXT[4]; ?>" class="form-control">
              <div class="input-group-btn">
                <button class="btn btn-info borders" type="submit"><i class="fa fa-search fa-fw"></i></button>
                <button class="btn btn-success dropdown-toggle hidden-xs" aria-expanded="false" aria-haspopup="true" data-toggle="dropdown" type="button">
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li><a href="<?php echo $this->ADVANCED_SEARCH_URL; ?>"><?php echo $this->TEXT[5]; ?></a></li>
                </ul>
              </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <?php
    // NAV LINKS BAR..
    // If banners exist, set the bottom margin to 0 so banners align flush with header
    ?>
    <div class="navbar navbar-default" id="msnavheader"<?php echo ($this->BANNERS ? ' style="margin-bottom:0"' : ''); ?>>
      <div class="container msheader">

        <?php
        // SEEN ON LARGE / MEDIUMS SCREENS ONLY
        ?>
        <div class="row hidden-xs hidden-sm">
          <div class="col-lg-4 col-md-3 logo">
            <a href="<?php echo $this->BASE_PATH; ?>"><i class="fa fa-shopping-bag fa-fw"></i> <?php echo $this->SETTINGS['website']; ?></a>
          </div>
          <div class="col-lg-8 col-md-9 text-right options">
            <?php
            // Only show account link on nav bar if not logged in..
            if (!isset($this->ACCOUNT['id'])) {
            ?>
            <a href="<?php echo $this->ACCOUNT_URL; ?>"><i class="fa fa-user fa-fw"></i> <?php echo $this->TEXT[10][1]; ?></a>
            <?php
            }
            // Is wish list enabled?
            if ($this->SETTINGS['en_wish'] == 'yes' && !defined('MC_TRADE_DISCOUNT')) {
            ?>
            <a href="<?php echo $this->WISH_LIST_URL; ?>"><i class="fa fa-heart fa-fw"></i> <?php echo $this->TEXT[10][0]; ?></a>
            <?php
            }
            // Is checkout enabled?
            if (!defined('CHECKOUT_LOADED') && $this->SETTINGS['enableCheckout'] == 'yes') {
            ?>
            <a href="#" onclick="mc_shoppingBasket();return false" class="checkout_link"><i class="fa fa-shopping-basket fa-fw"></i> <?php echo $this->TEXT[10][2]; ?> (<span><?php echo $this->CART_COUNT; ?></span>)</a>
            <?php
            }
            // Specials screen is not shown for trade accounts..
            if (!defined('MC_TRADE_DISCOUNT')) {
            ?>
            <a href="<?php echo $this->SPECIALS_URL; ?>"><i class="fa fa-star fa-fw"></i> <?php echo $this->TEXT[8]; ?></a>
            <?php
            }
            ?>
            <a href="<?php echo $this->LATEST_URL; ?>"><i class="fa fa-calendar fa-fw"></i> <?php echo $this->TEXT[6]; ?></a>
            <?php
            // Is sitemap enabled?
            if ($this->SETTINGS['en_sitemap'] == 'yes') {
            ?>
            <a href="<?php echo $this->SITEMAP_URL; ?>"><i class="fa fa-sitemap fa-fw"></i> <?php echo $this->TEXT[9]; ?></a>
            <?php
            }
            ?>
          </div>
        </div>

        <?php
        // SEEN ON SMALL / EXTRA SMALL SCREENS ONLY
        ?>
        <div class="row hidden-md hidden-lg mobilebar">
          <div class="col-xs-9 logo">
            <a href="index.php"><i class="fa fa-shopping-bag fa-fw"></i> <?php echo $this->SETTINGS['website']; ?></a>
          </div>
          <div class="col-xs-3 text-right">
            <div class="btn-group">
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-chevron-down fa-fw"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <?php
                // Only show account link on nav bar if not logged in..
                if (!isset($this->ACCOUNT['id'])) {
                ?>
                <li><a href="#" onclick="mc_accMenu('<?php echo (isset($this->ACCOUNT['id']) ? 'yes' : 'no'); ?>','<?php echo $this->ACCOUNT_URL; ?>');return false"><i class="fa fa-user fa-fw"></i> <?php echo $this->TEXT[10][1]; ?></a></li>
                <?php
                }
                // Is wish list enabled?
                if ($this->SETTINGS['en_wish'] == 'yes' && !defined('MC_TRADE_DISCOUNT')) {
                ?>
                <li><a href="<?php echo $this->WISH_LIST_URL; ?>"><i class="fa fa-heart fa-fw"></i> <?php echo $this->TEXT[10][0]; ?></a></li>
                <?php
                }
                // Is checkout enabled?
                if ($this->SETTINGS['enableCheckout'] == 'yes') {
                ?>
                <li><a href="#" onclick="mc_shoppingBasket();return false" class="checkout_link"><i class="fa fa-shopping-basket fa-fw"></i> <?php echo $this->TEXT[10][2]; ?> (<span><?php echo $this->CART_COUNT; ?></span>)</a></li>
                <?php
                }
                ?>
                <li><a href="<?php echo $this->SPECIALS_URL; ?>"><i class="fa fa-star fa-fw"></i> <?php echo $this->TEXT[8]; ?></a></li>
                <li><a href="<?php echo $this->LATEST_URL; ?>"><i class="fa fa-calendar fa-fw"></i> <?php echo $this->TEXT[6]; ?></a></li>
                <li class="hidden-sm hidden-md hidden-lg"><a href="<?php echo $this->ADVANCED_SEARCH_URL; ?>"><i class="fa fa-search fa-fw"></i> <?php echo $this->TEXT[3]; ?></a></li>
                <?php
                // Is sitemap enabled?
                if ($this->SETTINGS['en_sitemap'] == 'yes') {
                ?>
                <li><a href="<?php echo $this->SITEMAP_URL; ?>"><i class="fa fa-sitemap fa-fw"></i> <?php echo $this->TEXT[9]; ?></a></li>
                <?php
                }
                ?>

              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>

    <?php
    // HEADER BANNER SLIDER/ROTATOR
    // html/header-slider.htm
    // html/header-slider-img.htm
	  echo $this->BANNERS;

    // NEWS TICKER / MARQUEE
    // html/ticker-wrapper.htm
    // html/ticker-news-item.htm
    echo $this->NEWS_TICKER;

    // BREADCRUMBS
    if ($this->BREADCRUMBS) {
    ?>
    <div class="container breadcontainer hidden-xs">
     <div class="row">
       <div class="col-lg-12">
         <?php echo $this->BREADCRUMBS; ?>
       </div>
     </div>
    </div>
    <?php
    }
    ?>
