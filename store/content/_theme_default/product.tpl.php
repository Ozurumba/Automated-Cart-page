     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // PRODUCT TEMPLATE FILE
     // If required you can recall any value from the category table via the $this->CDATA array..
     // This is for advanced users only..to see the contents of the array use print_r.
     // print_r($this->PDATA)
     // Use the mc_safeHTML($data) function for safe display of user content..
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
         <div class="col-lg-9 col-md-9 rightbodyarea" id="formfield">
           <form id="form" method="post" action="#">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-cube fa-fw"></i> <?php echo mc_safeHTML($this->NAME); ?>
             </div>
             <div class="panel-body productviewarea">
               <div class="row">
                 <div class="col-lg-3 col-md-4 pleft">
                   <a rel="pics-<?php echo $this->PRODUCT_ID; ?>" href="<?php echo $this->IMG_URL; ?>" class="mc_swipebox">
                     <img src="<?php echo $this->IMG; ?>" alt="<?php echo $this->NAME; ?>" title="<?php echo $this->NAME; ?>" class="img-responsive">
                   </a>
                   <?php
                   // ADDITIONAL PRODUCT PICTURES
                   // html/products/product-pictures.htm
                   // html/products/product-pictures-img.htm
                   echo $this->PICTURES;

                   // MP3 PREVIEWS (If applicable)
                   if ($this->MP3_PREVIEWS > 0) {
                   ?>
                   <div class="alert alert-info mp3preview">
                     <a href="#" onclick="mc_mp3Previews('open','<?php echo $this->PRODUCT_ID; ?>');return false"><i class="fa fa-music fa-fw"></i> <?php echo $this->TXT[14]; ?></a>
                   </div>
                   <?php
                   }

                   // ADDTHIS BOOKMARKING OPTION
                   // Add code in settings..
                   if ($this->SOCIAL['addthis']['code']) {
                   ?>
                   <div class="addthis_wrapper">
                     <div class="addthis_sharing_toolbox"></div>
				             <script src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $this->SOCIAL['addthis']['code']; ?>" async></script>
                   </div>
                   <?php
                   }

                   ?>
                 </div>
                 <div class="col-lg-9 col-md-8 pright">
                   <?php echo $this->TXT[8]; ?>: <?php echo $this->AVAILABILITY; ?><br>
                   <?php echo $this->TXT[10]; ?>: <?php echo ($this->PDATA['pCode'] ? $this->PDATA['pCode'] : 'N/A'); ?>
                   <hr>
                   <span class="price"><?php echo $this->PRICE; ?></span>
                   <?php

                   // Additional text below product price..
                   // Defined in admin general settings..
                   if ($this->PRODUCT_ADD_TEXT) {
                   ?>
                   <div class="addtxt"><?php echo $this->PRODUCT_ADD_TEXT; ?></div>
                   <?php
                   }

                   // FLAGS
                   // Offer, product expiry, min/max purchase, multi buy etc..
                   // html/products/product-flags-wrapper.htm
                   // html/products/product-flags-detail.htm
                   if (!empty($this->FLAGS)) {
                     echo $this->FLAGS;
                   }
                   ?>
                   <hr>

                   <?php
                   // BUY OPTIONS / PERSONALISATION
                   // Does this product have other attributes or personalisation?
                   if ($this->BUY_OPTIONS || $this->PERSONALISATION) {
                   ?>
                   <div class="row">
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                       <ul class="nav nav-tabs">
                         <?php
                         if ($this->BUY_OPTIONS) {
                         ?>
                         <li class="active"><a href="#bo_1" data-toggle="tab"><i class="fa fa-cart-arrow-down fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $this->TXT[22]; ?></span></a></li>
                         <?php
                         }
                         if ($this->PERSONALISATION) {
                         ?>
                         <li<?php echo ($this->BUY_OPTIONS ? '' : ' class="active"'); ?>><a href="#bo_2" data-toggle="tab"><i class="fa fa-user-plus fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $this->TXT[21]; ?></span></a></li>
                         <?php
                         }
                         ?>
                       </ul>
                     </div>
                   </div>

                   <div class="row" style="margin-top:10px">
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                       <div class="tab-content">
                         <?php
                         // BUY OPTIONS/ATTRIBUTES
                         if ($this->BUY_OPTIONS) {
                         ?>
                         <div class="tab-pane active in" id="bo_1">
                           <div class="panel panel-default">
                             <div class="panel-body">
                             <?php
                             // html/products/product-attribute-wrapper.htm
                             // html/products/product-attribute.htm
                             // html/products/product-attributes.htm
                             echo $this->BUY_OPTIONS;
                             ?>
                             </div>
                           </div>
                         </div>
                         <?php
                         }
                         // PERSONALISATION OPTIONS
                         if ($this->PERSONALISATION) {
                         ?>
                         <div class="tab-pane<?php echo ($this->BUY_OPTIONS ? ' fade' : ' active in'); ?>" id="bo_2">
                           <div class="panel panel-default">
                             <div class="panel-body">
                             <?php
                             // html/products/product-personalisation-input.htm
                             // html/products/product-personalisation-script.htm
                             // html/products/product-personalisation-select.htm
                             // html/products/product-personalisation-select-option.htm
                             // html/products/product-personalisation-textarea.htm
                             // html/products/product-personalisation-wrapper.htm
                             echo $this->PERSONALISATION;
                             ?>
                             </div>
                           </div>
                         </div>
                         <?php
                         }
                         ?>
                       </div>
                     </div>
                   </div>
                   <hr style="padding-top:0;margin-top:0">
                   <?php
                   }

                   // ADD TO BASKET BUTTON
                   // Only show add to cart button if something is in stock, checkout is enabled and cart purchase is enabled..
                   if ($this->PDATA['pStock'] > 0 && $this->SETTINGS['enableCheckout'] == 'yes' && $this->PDATA['pPurchase'] == 'yes') {
                   ?>
                   <div class="row">
                     <div class="col-lg-4 col-md-5 col-sm-4 col-xs-6">
                       <div class="form-group input-group">
                         <span class="input-group-addon"><?php echo $this->TXT[9]; ?></span>
                         <input onblur="mc_qtyCheck('<?php echo $this->PDATA['maxPurchaseQty']; ?>','<?php echo $this->PDATA['minPurchaseQty']; ?>','<?php echo $this->PDATA['pStock']; ?>')"<?php echo ($this->PRODUCT_STOCK > 0 ? ' ' : ' disabled="disabled" '); ?>type="text" class="form-control" name="qty" value="<?php echo ($this->PRODUCT_STOCK > 0 ? ($this->PDATA['minPurchaseQty'] > 0 ? $this->PDATA['minPurchaseQty'] : '1') : '0'); ?>"<?php echo ($this->PDATA['maxPurchaseQty'] > 0 ? ' maxlength="' . strlen($this->PDATA['maxPurchaseQty']) . '"' : ''); ?>>
                       </div>
                     </div>
                     <div class="col-lg-8 col-md-7 col-sm-8 col-xs-6 text-right">
                       <input type="hidden" name="wish[account]" value="<?php echo $this->WISH_PURCHASE[1]; ?>">
                       <button type="button" class="btn btn-success" onclick="mc_addToBasket('<?php echo $this->PRODUCT_ID; ?>','product')"><i class="fa fa-cart-plus fa-fw"></i><span class="hidden-xs"> <?php echo $this->TXT[16]; ?></span></button>
                     </div>
                   </div>
                   <?php
                   // LIMITED STOCK?
                   // If enabled, show message..
                   // html/products/product-limited-stock.htm
                   echo $this->HURRY_LIMITED;
                   }
                   ?>

                 </div>
              </div>
            </div>
           </div>
           </form>

           <div class="row">
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <ul class="nav nav-tabs">
                 <li class="active"><a href="#one" data-toggle="tab"><i class="fa fa-info-circle fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $this->TXT[3]; ?></span></a></li>
                 <li><a href="#two" data-toggle="tab"><i class="fa fa-video-camera fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $this->TXT[4]; ?></span></a></li>
                 <li><a href="#three" data-toggle="tab"><i class="fa fa-quote-left fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $this->TXT[20]; ?></span></a></li>
                 <li><a href="#four" data-toggle="tab"><i class="fa fa-envelope fa-fw"></i><span class="hidden-xs hidden-sm"> <?php echo $this->TXT[5]; ?></span></a></li>
               </ul>
             </div>
           </div>

           <div class="row" style="margin-top:10px">
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <div class="tab-content">
                 <div class="tab-pane active in" id="one">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <?php echo $this->DESC; ?>
                     </div>
                   </div>
                 </div>
                 <div class="tab-pane fade" id="two">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <?php
                       // Are product videos available?
                       if ($this->PRODUCT_VIDEOS) {
                         echo $this->PRODUCT_VIDEOS;
                       } else {
                       ?>
                       <i class="fa fa-minus-square fa-fw"></i> <?php echo $this->TXT[19][6]; ?>
                       <?php
                       }
                       ?>
                     </div>
                   </div>
                 </div>
                 <div class="tab-pane fade" id="three">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <?php
                       // DISQUS COMMENTS SYSTEM
                       // html/products/disqus.htm
                       if ($this->DISQUS) {
                         echo $this->DISQUS;
                       } else {
                       ?>
                       <i class="fa fa-minus-square fa-fw"></i> <?php echo $this->TXT[19][5]; ?>
                       <?php
                       }
                       ?>
                     </div>
                   </div>
                 </div>
                 <div class="tab-pane fade" id="four">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <div class="alert alert-success"><?php echo $this->TXT[19][10]; ?></div>

                       <?php
                       // If a visitor is logged in, we already have the name / email, so
                       //  only show if no visitor is logged in..
                       if (!isset($this->ACCOUNT['id'])) {
                       ?>
                       <label><?php echo $this->TXT[19][7]; ?></label>
                       <input type="text" name="que[nm]" value="" class="form-control" tabindex="1">

                       <label style="padding-top:10px"><?php echo $this->TXT[19][8]; ?></label>
                       <input type="text" name="que[em]" value="" class="form-control" tabindex="2">
                       <?php
                       }
                       ?>

                       <label<?php echo (!isset($this->ACCOUNT['id']) ? ' style="padding-top:10px"' : ''); ?>><?php echo $this->TXT[19][9]; ?></label>
                       <textarea rows="5" cols="2" name="que[msg]" tabindex="3" class="form-control"></textarea>

                       <div style="margin-top:10px">
                         <input type="hidden" name="que[blank]" value="">
                         <button type="button" class="btn btn-success" onclick="mc_prodEnquiry('<?php echo $this->PRODUCT_ID; ?>')"><i class="fa fa-arrow-right fa-fw"></i> <?php echo $this->TXT[19][11]; ?></button>
                       </div>

                     </div>
                   </div>
                 </div>
               </div>
             </div>
           </div>

           <?php
           // RELATED PRODUCTS && CUSTOMERS WHO BOUGHT THIS ALSO BOUGHT THAT
           if ($this->RELATED_PRODUCTS || $this->SALE_COMPARISON) {
           ?>
           <div class="row">
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <ul class="nav nav-tabs">
                 <?php
                 if ($this->SALE_COMPARISON) {
                 ?>
                 <li class="active"><a href="#sr_1" data-toggle="tab"><i class="fa fa-check fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $this->TXT[23]; ?></span></a></li>
                 <?php
                 }
                 if ($this->RELATED_PRODUCTS) {
                 ?>
                 <li<?php echo ($this->SALE_COMPARISON ? '' : ' class="active"'); ?>><a href="#sr_2" data-toggle="tab"><i class="fa fa-exchange fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $this->TXT[24]; ?></span></a></li>
                 <?php
                 }
                 ?>
               </ul>
             </div>
           </div>

           <div class="row" style="margin-top:10px">
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <div class="tab-content">
                 <?php
                 // CUSTOMERS WHO BOUGHT THIS ALSO BOUGHT THAT
                 if ($this->SALE_COMPARISON) {
                 ?>
                 <div class="tab-pane active in" id="sr_1">
                   <?php
                   // html/products/product-comparison-wrapper.htm
                   // html/products/product-comparison-item.htm
                   echo $this->SALE_COMPARISON;
                   ?>
                 </div>
                 <?php
                 }
                 // RELATED PRODUCTS
                 if ($this->RELATED_PRODUCTS) {
                 ?>
                 <div class="tab-pane<?php echo ($this->SALE_COMPARISON ? ' fade' : ' active in'); ?>" id="sr_2">
                   <?php
                   // html/products/product-related-wrapper.htm
                   // html/products/product-related-item.htm
                   echo $this->RELATED_PRODUCTS;
                   ?>
                 </div>
                 <?php
                 }
                 ?>
               </div>
             </div>
           </div>
           <?php
           }

           // PRODUCT TAGS
           // html/products/product-tags.htm
           echo $this->TAGS;
           ?>

           <hr>

           <div class="inventory">
             <?php echo $this->TXT[13]; ?>
           </div>

          </div>
       </div>

     </div>


