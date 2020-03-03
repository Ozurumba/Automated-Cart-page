function loadTopMenu() {
  var html  = '<ul class="nav navbar-top-links navbar-right hidden-xs">';
  html     += ' <li><a href="https://www.maiancart.com/purchase.html" onclick="window.open(this);return false"><i class="fa fa-shopping-cart fa-fw"></i> Purchase Licence</a></li>';
  html     += ' <li><a href="bugs.html"><i class="fa fa-bug fa-fw"></i> Bug Reports</a></li>';
  html     += ' <li><a href="upgrades.html"><i class="fa fa-history fa-fw"></i> Upgrades</a></li>';
  html     += ' <li><a href="support.html"><i class="fa fa-life-saver fa-fw"></i> Support</a></li>';
  html     += ' <li><a href="https://www.maianscriptworld.co.uk/" onclick="window.open(this);return false"><i class="fa fa-external-link fa-fw"></i> Other Software</a></li>';
  html     += '</ul>';
  jQuery('div[class="navbar-header"]').after(html);
}

function loadOverviewNav() {
  var html = '<select onchange="if(this.value!=0){location=this.options[this.selectedIndex].value}" class="form-control">';
  html     += '<option value="0">- - - - - - - - -</option>';
  html     += '<option value="overview-2.html">Categories</option>';
  html     += '<option value="overview-3.html">Products</option>';
  html     += '<option value="overview-4.html">Search</option>';
  html     += '<option value="overview-5.html">Gift Certificates</option>';
  html     += '<option value="overview-6.html">Recently Viewed</option>';
  html     += '<option value="overview-7.html">RSS Feeds</option>';
  html     += '<option value="overview-8.html">Accounts</option>';
  html     += '<option value="overview-9.html">PopUp Basket</option>';
  html     += '<option value="overview-10.html">Checkout Routines</option>';
  html     += '<option value="overview-11.html">Currency Converter</option>';
  html     += '<option value="overview-12.html">Language Switcher</option>';
  html     += '</select>'
  jQuery('#overview_nav').html(html);
}

function loadLeftMenu() {
  var html  = '<ul class="nav" id="side-menu">';
  html     += '  <li><a href="index.html"><i class="fa fa-dashboard fa-fw"></i> Docs Main Page</a></li>';
  html     += '  <li><a href="versions.html"><i class="fa fa-search-plus fa-fw"></i> Free v Commercial</a></li>';
  html     += '  <li><a href="white.html"><i class="fa fa-tag fa-fw"></i> White Label Licence</a></li>';
  html     += '  <li><a href="install.html"><i class="fa fa-cog fa-fw"></i> Maian Cart Setup</a></li>';
  html     += '  <li><a href="overview.html"><i class="fa fa-mouse-pointer fa-fw"></i> Maian Cart Overview</a></li>';
  html     += '  <li><a href="payment-options.html"><i class="fa fa-credit-card fa-fw"></i> Payment Options</a></li>';
  html     += '  <li><a href="language.html"><i class="fa fa-file-text-o fa-fw"></i> Templates/Language</a></li>';
  html     += '  <li><a href="plugins.html"><i class="fa fa-wrench fa-fw"></i> Plugins &amp; Features</a></li>';
  html     += '  <li><a href="maian-affiliate.html"><i class="fa fa-money fa-fw"></i> Maian Affiliate Integration</a></li>';
  html     += '  <li><a href="faq.html"><i class="fa fa-question-circle fa-fw"></i> F.A.Q</a></li>';
  html     += '  <li><a href="info.html"><i class="fa fa-info-circle fa-fw"></i> Software Info</a></li>';
  html     += '</ul>';
  jQuery('div[class="sidebar-collapse"]').html(html);
}

function loadFooter() {
  var d     = new Date();
  var year  = d.getFullYear();
  var html  = '<hr><a href="https://www.facebook.com/msworlduk/" onclick="window.open(this);return false"><img src="content/images/facebook.png" alt="Maian Script World on Facebook"></a>';
  html     += '<a href="https://twitter.com/#!/maianscripts" onclick="window.open(this);return false"><img src="content/images/twitter.png" alt="Maian Script World on Twitter"></a>';
  html     += '<a href="http://www.dailymotion.com/maianmedia" onclick="window.open(this);return false"><img src="content/images/videos.png" alt="Maian Script World on DailyMotion"></a>';
  html     += '<a href="https://www.maiancart.com/rss.html" onclick="window.open(this);return false"><img src="content/images/rssfeeds.png" alt="Maian Cart Updates"></a>';
  html     += '<p>Powered by <a href="https://www.maiancart.com" onclick="window.open(this);return false">Maian Cart</a><br>&copy; '+'2006-'+year+' Maian Script World. All Rights Reserved. <a href="disclaimer.html">Disclaimer</a></p>';
  jQuery('div[class="row footerArea"]').html(html);
}

function mc_Window(w_url, w_height, w_width, w_title) {
  if (w_height > 0) {
    iBox.showURL(w_url, '',{
      width  : w_width,
      height : w_height
    });
  } else {
    iBox.showURL(w_url, '');
  }
}