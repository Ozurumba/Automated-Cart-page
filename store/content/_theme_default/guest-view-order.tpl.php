     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // GUEST CHECKOUT ORDER TEMPLATE FILE
     // If required you can recall any value from the sale table via the $this->ORDER array..
     // This is for advanced users only..to see the contents of the array use print_r.
     // print_r($this->ORDER)
     // Use the mc_safeHTML($data) function for safe display of user content..
     ?>

     <div class="container maincontainer">
       <div class="row">
         <div class="col-lg-3 col-md-3 leftmenuwrapper">
           <div class="hidden-xs">
           <?php
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
         </div>
         <div class="col-lg-9 col-md-9 rightbodyarea vieworderarea">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-search fa-fw"></i> <?php echo $this->TEXTV[0]; ?> - #<?php echo mc_saleInvoiceNumber($this->ORDER['invoiceNo'], (object) $this->SETTINGS); ?>
             </div>
             <div class="panel-body">
               <?php echo $this->TEXTV[1]; ?>
             </div>
           </div>

           <div class="row">
             <div class="col-lg-6 address">
               <div class="panel panel-default">
                 <div class="panel-heading uppercase">
                   <i class="fa fa-credit-card fa-fw"></i> <?php echo $this->TEXTV[7]; ?>
                 </div>
                 <div class="panel-body">
                   <?php echo $this->BUILD['bill-address']; ?>
                 </div>
               </div>
             </div>
             <div class="col-lg-6 address">
               <div class="panel panel-default">
                 <div class="panel-heading uppercase">
                   <i class="fa fa-truck fa-fw"></i> <?php echo $this->TEXTV[8]; ?>
                 </div>
                 <div class="panel-body">
                   <?php echo $this->BUILD['ship-address']; ?>
                 </div>
               </div>
             </div>
           </div>

           <?php
           // SHIPPED ITEMS
           if ($this->BUILD['shipped']) {
           ?>
           <div class="panel panel-default">
             <div class="panel-heading uppercase nobottomborder">
               <i class="fa fa-truck fa-fw"></i> <?php echo $this->TEXTV[2]; ?>
             </div>
           </div>
           <?php
           // html/view-order/shipped.htm
           // html/view-order/attributes.htm
           // html/view-order/personalised.htm
           echo $this->BUILD['shipped'];
           }

           // DOWNLOADS
           if ($this->BUILD['downloads']) {
           ?>
           <div class="panel panel-default">
             <div class="panel-heading uppercase nobottomborder">
               <i class="fa fa-download fa-fw"></i> <?php echo $this->TEXTV[3]; ?>
             </div>
           </div>
           <?php
           // html/view-order/guest-downloads.htm
           echo $this->BUILD['downloads'];
           }

           // GIFT CERTS
           if ($this->BUILD['gift-certs']) {
           ?>
           <div class="panel panel-default">
             <div class="panel-heading uppercase nobottomborder">
               <i class="fa fa-gift fa-fw"></i> <?php echo $this->TEXTV[4]; ?>
             </div>
           </div>
           <?php
           // html/view-order/gift-certs.htm
           echo $this->BUILD['gift-certs'];
           }
           ?>

           <div class="row">
             <div class="col-lg-6 infoarea">
               <div class="panel panel-default">
                 <div class="panel-body">
                   <?php
                   // html/view-order/info-item.htm
                   echo $this->BUILD['info'];
                   ?>
                 </div>
               </div>
             </div>
             <div class="col-lg-6 totalarea">
               <div class="panel panel-default">
                 <div class="panel-body">
                   <div class="table-responsive">
                     <table class="table table-striped">
                       <tbody>
                         <?php
                         // html/view-order/total.htm
                         echo $this->BUILD['totals'];
                         ?>
                       </tbody>
                     </table>
                   </div>
                 </div>
               </div>
             </div>
           </div>

           <?php
           // SALE NOTES..
           if ($this->ORDER['saleNotes']) {
           ?>
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-file-text-o fa-fw"></i> <?php echo $this->TEXTV[11]; ?>
             </div>
             <div class="panel-body">
               <?php
               echo mc_NL2BR(mc_safeHTML($this->ORDER['saleNotes']));
               ?>
             </div>
           </div>
           <?php
           }

           // Are PDFs enabled?
           if ($this->SETTINGS['pdf'] == 'yes') {
           ?>
           <hr>

           <div class="text-center">
             <button class="btn btn-default" onclick="mc_gpdf('<?php echo $this->ORDER['buyCode']; ?>','<?php echo $this->ORDER['id']; ?>')"><i class="fa fa-file-pdf-o fa-fw"></i> <?php echo $this->TEXTV[5]; ?></button>
           </div>
           <?php
           }
           ?>

         </div>
       </div>

     </div>


