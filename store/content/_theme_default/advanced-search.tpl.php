     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // ADVANCED SEARCH TEMPLATE FILE
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
           <form method="get" action="<?php echo $this->URL; ?>">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-search-plus fa-fw"></i> <?php echo $this->TEXT[0]; ?>
             </div>
             <div class="panel-body">
               <?php echo $this->TEXT[1]; ?>
             </div>
           </div>
           <div class="panel panel-default">
             <div class="panel-body">
               <label><?php echo $this->TEXT[2]; ?>:</label>
               <input type="text" class="form-control" name="q" value="<?php echo $this->TEXT[12]; ?>">
             </div>
           </div>
           <div class="panel panel-default">
             <div class="panel-body">
               <label><?php echo $this->TEXT[4]; ?>:</label>
               <select name="cat" onchange="if(this.value!='0'){mc_loadCatBrands(this.value)}" class="form-control">
                 <option value="0"><?php echo $this->TEXT[17][1]; ?></option>
                 <option value="0" disabled="disabled">- - - - - - - -</option>
                 <?php
                 // CATEGORIES
                 // html/html-option-tags.htm
                 echo $this->CATEGORIES;
                 ?>
               </select>

               <label style="margin-top:10px"><?php echo $this->TEXT[15]; ?>:</label>
               <select name="brand" class="form-control">
                 <option value="0"><?php echo $this->TEXT[17][0]; ?></option>
                 <option value="0" disabled="disabled">- - - - - - - -</option>
                 <?php
                 // BRANDS
                 // html/html-option-tags.htm
                 echo $this->BRANDS;
                 ?>
               </select>
             </div>
           </div>
           <div class="panel panel-default">
             <div class="panel-body">
               <label><?php echo $this->TEXT[3]; ?>:</label>
               <input type="text" class="form-control" name="from" id="from" value="">
               <input style="margin-top:5px" type="text" class="form-control" name="to" value="" id="to">
             </div>
           </div>
           <div class="panel panel-default">
             <div class="panel-body">
               <?php
               // Filters not shown for trade accounts..
               if (!defined('MC_TRADE_DISCOUNT')) {
               ?>
               <label><?php echo $this->TEXT[13]; ?>:</label>
               <div class="form-group">
                 <?php
                 // FILTERS
                 // Downloads box filter only displays if there is at least 1 product download..
                 if ($this->IS_DOWNLOADS=='yes') {
                 ?>
                 <div class="checkbox">
                   <label><input type="checkbox" name="download" value="yes"> <?php echo $this->TEXT[5]; ?></label>
                 </div>
                 <?php
                 }
                 ?>
                 <div class="checkbox">
                   <label><input type="checkbox" name="stock" value="yes"> <?php echo $this->TEXT[7]; ?></label>
                 </div>
                 <div class="checkbox">
                   <label><input type="checkbox" name="specials" value="yes"> <?php echo $this->TEXT[16]; ?></label>
                 </div>
               </div>
               <?php
               }
               ?>
               <label><?php echo $this->TEXT[14]; ?>:</label>
               <select name="sortby" class="form-control">
                 <?php
                 // FILTER / SORT BY
                 // html/html-option-tags.htm
                 echo $this->FILTER_OPTIONS;
                 ?>
               </select>
             </div>
           </div>
           <?php
           // Price points slider not shown for trade accounts..
           if (!defined('MC_TRADE_DISCOUNT')) {
           ?>
           <div class="panel panel-default">
             <div class="panel-body">
               <label style="margin-bottom:20px"><?php echo $this->TEXT[6]; ?>: <span class="sliderAmount"></span></label>
               <div id="slider-range"></div>
             </div>
           </div>
           <?php
           }
           ?>
           <div class="text-center margin_bottom_50">
             <input type="hidden" name="adv" value="1">
             <input type="hidden" name="price1" value="0.00">
             <input type="hidden" name="price2" value="0.00">
             <button type="submit" class="btn btn-success"><i class="fa fa-search-plus fa-fw"></i> <?php echo $this->TEXT[8]; ?></button>
           </div>
           </form>
         </div>
       </div>
     </div>