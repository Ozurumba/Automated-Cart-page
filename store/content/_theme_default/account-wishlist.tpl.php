     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // ACCOUNT WISH LIST TEMPLATE FILE
     // If required you can recall any value from the account table via the $this->ACCOUNT array..
     // This is for advanced users only..to see the contents of the array use print_r.
     // print_r($this->ACCOUNT)
     // Use the mc_safeHTML($data) function for safe display of user content..
     ?>

     <div class="container maincontainer">
       <div class="row">
         <div class="col-lg-3 col-md-3 leftmenuwrapper">
           <?php
           // Load account menu..
           include(PATH . $this->THEME_FOLDER . '/account-menu.php');

           // LEFT MENU BOXES
           // Controlled in admin setting via left box controller
           // See docs for more help
           // Some options are disabled in account view, enable in 'control/system/accounts/*' pages

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
         <div class="col-lg-9 col-md-9 rightbodyarea margin_bottom_50">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-heart fa-fw"></i> <?php echo $this->TEXT[11]; ?>
             </div>
             <div class="panel-body">
               <?php echo $this->MESSAGE; ?><br>
               <a href="#" onclick="jQuery('.wishlisttext').slideToggle();return false"><i class="fa fa-file-text-o fa-fw"></i> <?php echo $this->TEXTW[12]; ?></a>
             </div>
           </div>

           <div class="panel panel-default wishlisttext" style="display:none">
             <div class="panel-body">
               <label><?php echo $this->TEXTW[13]; ?></label>
               <textarea rows="5" cols="20" name="wish" class="form-control"><?php echo mc_safeHTML($this->ACCOUNT['wishtext']); ?></textarea>
               <div class="buttons margin_top_10">
                 <button type="button" class="btn btn-primary" onclick="mc_wishTxt()"><i class="fa fa-check fa-fw"></i> <?php echo $this->TEXTW[14]; ?></button>
               </div>
             </div>
           </div>

           <div class="row wishlistitems" style="margin-top:10px">
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <?php
               // WISH LIST..
               // html/accounts/wishlist-product.htm
               // html/accounts/wishlist-wrapper.htm
               // html/nothing-to-show.htm
               echo $this->PRODUCTS;
               ?>
             </div>
           </div>

           <?php
           // Only show page numbers if at least 1 entry exists..
           if ($this->PAGINATION) {
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
       </div>

     </div>


