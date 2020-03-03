     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // SEARCH RESULTS TEMPLATE FILE
     ?>

     <div class="container maincontainer">
       <div class="row">
         <div class="col-lg-3 col-md-3 leftmenuwrapper">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
              <i class="fa fa-link fa-fw"></i> <?php echo $this->TEXT[6][1]; ?>
             </div>
             <div class="panel-body lineheight_25">
               <?php
               // Only visitors logged in can save searches..
               if ($this->P_COUNT > 0) {
               ?>
               <a rel="nofollow" href="#" title="<?php echo $this->TEXT[3]; ?>" onclick="mc_saveSearch('<?php echo $this->TEXT[6][9]; ?>','<?php echo $this->IS_LOGGED_IN; ?>');return false"><i class="fa fa-save fa-fw"></i> <?php echo $this->TEXT[3]; ?></a><br>
               <?php
               }
               ?>
               <a rel="nofollow" href="<?php echo $this->ADVANCED_SEARCH_URL; ?>" title="<?php echo $this->TEXT[5]; ?>"><i class="fa fa-search-plus fa-fw"></i> <?php echo $this->TEXT[5]; ?></a>
             </div>
           </div>

           <?php
           // Search filter categories..
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
               <i class="fa fa-search fa-fw"></i> <?php echo $this->TEXT[7]; ?>
             </div>
             <div class="panel-body">
               <?php echo $this->TEXT[6][3]; ?>
             </div>
           </div>
           <?php
           if ($this->P_COUNT > 0 && $this->SEARCH_RESULTS) {
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
                   <?php echo $this->TEXT[8]; ?> <i class="fa fa-sort fa-fw"></i>
                 </button>
                 <ul class="dropdown-menu dropdown-menu-right">
                   <?php
                   // SPECIALS FILTER OPTIONS
                   // html/options-li.htm
                   echo $this->FILTER_OPTIONS;
                   ?>
                 </ul>
               </div>
             </div>
           </div>
           <?php
           }
           // SEARCH_RESULTS
           // html/categories/category-*.htm
           if ($this->SEARCH_RESULTS) {
             ?>
             <div class="row<?php echo $this->LAYOUT; ?>">
             <?php
             echo $this->SEARCH_RESULTS;
             ?>
             </div>
             <?php
           }
           ?>
         </div>
       </div>

       <?php
       // Only show page numbers if products exist for category..
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


