     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('CHECKOUT_LOADED')) {
       exit;
     }

     // CHECKOUT TEMPLATE FILE
     ?>

     <div class="container maincontainer">
       <div class="row">
         <div class="col-lg-3 col-md-3 leftmenuwrapper hidden-xs hidden-sm">

           <div class="panel panel-default paymentstatpanel" style="display:none !important">
             <div class="panel-heading uppercase">
              <i class="fa fa-shopping-basket fa-fw"></i> <?php echo (isset($this->ACCOUNT['id']) ? $this->PTEXT[11] : $this->PTEXT[12]); ?>
             </div>
             <div class="panel-body">
               <div class="row">
                 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo $this->PTEXT[9]; ?></div>
                 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo $this->CSTAT['count']; ?></div>
               </div>
               <div class="row">
                 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo $this->PTEXT[10]; ?></div>
                 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo $this->CSTAT['total']; ?></div>
               </div>
               <div class="text-center">
                 <hr>
                 <div class="alert alert-success alert-mcart small-alert">
                   <i class="fa fa-exclamation fa-fw"></i> <?php echo ($this->SHOW_SHIPPING == 'yes' ? $this->TXT[0][13] : $this->TXT[0][16]); ?>
                 </div>
                 <div class="hidden-xs hidden-sm hidden-md text-center">
                   <hr>
                   <button type="button" class="btn btn-info" onclick="mc_reloadBasket('show')"><i class="fa fa-shopping-basket fa-fw"></i><span class="hidden-xs"> <?php echo $this->PTEXT[17]; ?></span></button>
                 </div>
               </div>
             </div>
           </div>

           <?php
           if (isset($this->ACCOUNT['id'])) {
             include(PATH . $this->THEME_FOLDER . '/account-menu.php');
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
         <?php
         // Checkout products...
         include(PATH . $this->THEME_FOLDER . '/checkout-products.php');

         // Checkout payment routine...hidden initially..
         include(PATH . $this->THEME_FOLDER . '/checkout-payment.php');
         ?>
       </div>

     </div>


