     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // NEW PAGE TEMPLATE FILE
     // If required you can recall any value from the page table via the $this->PAGE array..
     // This is for advanced users only..to see the contents of the array use print_r.
     // print_r($this->PAGE)
     // Use the mc_safeHTML($data) function for safe display of user content..
     ?>

     <div class="container maincontainer">
       <div class="row">
         <?php
         // Show left column on new pages?
         if ($this->PAGE['leftColumn'] == 'yes') {
         ?>
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
         <?php
         }
         ?>
         <div<?php echo ($this->PAGE['leftColumn'] == 'yes' ? ' class="col-lg-9 col-md-9 rightbodyarea"' : ' class="col-lg-12 rightbodyarea"'); ?>>
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-file-text-o fa-fw"></i> <?php echo mc_safeHTML($this->PAGE['pageName']); ?>
             </div>
             <div class="panel-body">
               <?php echo $this->DATA; ?>
             </div>
           </div>
         </div>
       </div>

     </div>