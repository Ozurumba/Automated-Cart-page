     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // SITEMAP TEMPLATE FILE
     ?>

     <div class="container maincontainer">
       <div class="row">
         <div class="col-lg-3 col-md-3 leftmenuwrapper">
           <?php
           // LEFT MENU BOXES
           // Controlled in admin setting via left box controller
           // See docs for more help

           // PRICE POINTS
           // html/left-menu/price-points-wrapper.htm
           // html/left-menu/price-points-link.htm

           // BRANDS
           // html/left-menu/brands-wrapper.htm
           // html/left-menu/brands-link.htm

           // MOST POPULAR PRODUCTS
           // html/left-menu/most-popular-wrapper.htm
           // html/left-menu/most-popular-links.htm

           // RECENTLY VIEWED ITEMS
           // html/left-menu/most-recent-wrapper.htm
           // html/left-menu/most-recent-links.htm

           // NEW PAGES / OTHER LINKS..
           // html/left-menu/new-pages-wrapper.htm
           // html/left-menu/new-page-links.htm

           // LATEST TWEETS..
           // html/left-menu/latest-tweets.htm

           // RSS SCROLLER..
           // html/left-menu/rss-scroller.htm

           // CUSTOM BOXES..
           // customTemplates/box**.tpl.php
           echo $this->LEFT_MENU_BOXES;

           ?>
         </div>
         <div class="col-lg-9 col-md-9 rightbodyarea">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-sitemap fa-fw"></i>
               <?php echo $this->TEXT[0]; ?>
             </div>
             <div class="panel-body">
               <?php echo $this->TEXT[1][0]; ?>
             </div>
           </div>
           <?php
           // SITEMAP
           // html/sitemap/*
           echo $this->SITEMAP;
           ?>
         </div>
       </div>
     </div>


