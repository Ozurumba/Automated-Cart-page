     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // LATEST PRODUCTS TEMPLATE FILE
     ?>

     <div class="container maincontainer">
       <div class="row">
         <div class="col-lg-3 col-md-3 leftmenuwrapper">
           <?php
           if ($this->CATEGORIES){
             // html/left-menu/cat-filter-link.htm
             // html/left-menu/cat-filter-wrapper.htm
             echo $this->CATEGORIES;
           }

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
               <i class="fa fa-calendar fa-fw"></i>
               <?php
               // Are the RSS feeds enabled?
               if ($this->SETTINGS['en_rss'] == 'yes') {
               ?>
               <span class="pull-right"><a href="<?php echo $this->FEED_URL; ?>" title="<?php echo $this->TEXT[1]; ?>" onclick="window.open(this);return false"><i class="fa fa-rss fa-fw rssfeed"></i></a></span>
               <?php
               }
               echo $this->TEXT[0];
               ?>
             </div>
             <div class="panel-body">
               <?php echo $this->MESSAGE; ?>
             </div>
           </div>
           <?php
           // FILTERS / LAYOUT CHANGE..
           if ($this->PRODUCTS && $this->P_COUNT > 0) {
           ?>
           <div class="row listfilters">
             <div class="col-lg-6 col-md-6 col-sm-6 hidden-xs">
               <div class="btn-group" role="group">
                 <button onclick="mc_layout(this, 'list')" class="btn btn-default hidden-xs<?php echo (MC_CATVIEW == 'list' ? ' buttonlayoutselection' : ''); ?>" type="button"><i class="fa fa-list-ul fa-fw"></i></button>
                 <button onclick="mc_layout(this, 'grid')" class="btn btn-default<?php echo (MC_CATVIEW == 'grid' ? ' buttonlayoutselection' : ''); ?>" type="button"><i class="fa fa-th fa-fw"></i></button>
               </div>
             </div>
             <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
               <div class="btn-group">
                 <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <?php echo $this->TEXT[4]; ?> <i class="fa fa-sort fa-fw"></i>
                 </button>
                 <ul class="dropdown-menu dropdown-menu-right">
                   <?php
                   // LATEST FILTER OPTIONS
                   // html/options-li.htm
                   echo $this->FILTER_OPTIONS;
                   ?>
                 </ul>
               </div>
             </div>
           </div>
           <?php
           }
           // LATEST OFFER PRODUCTS
           // html/categories/category-*.htm
           if ($this->PRODUCTS) {
             ?>
             <div class="<?php echo ($this->P_COUNT > 0 ? 'row' : '') . $this->LAYOUT; ?>">
             <?php
             echo $this->PRODUCTS;
             ?>
             </div>
             <?php
           }
           ?>
         </div>
       </div>

       <?php
       // Only show page numbers if products exist..
       if ($this->P_COUNT > 0 && $this->PAGINATION) {
       ?>
       <div class="row mswpages">
         <div class="col-lg-12">
         <?php echo $this->PAGINATION; ?>
         </div>
       </div>
       <?php
       }
       ?>
     </div>
