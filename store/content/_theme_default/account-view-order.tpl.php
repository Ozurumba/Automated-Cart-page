     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // ACCOUNT ORDER TEMPLATE FILE
     // If required you can recall any value from the account table via the $this->ACCOUNT array..
     // This is for advanced users only..to see the contents of the array use print_r.
     // print_r($this->ACCOUNT)
     // If required you can recall any value from the sale table via the $this->ORDER array..
     // This is for advanced users only..to see the contents of the array use print_r.
     // print_r($this->ORDER)
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
         <div class="col-lg-9 col-md-9 rightbodyarea vieworderarea">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-search fa-fw"></i> <?php echo $this->TEXTV[0]; ?> - #<?php echo mc_saleInvoiceNumber($this->ORDER['invoiceNo'], (object) $this->SETTINGS); ?>
             </div>
             <div class="panel-body">
               <?php echo $this->TEXTV[1]; ?>
             </div>
           </div>

           <?php
           // If order is pending, show message..
           if ($this->ORDER['paymentStatus'] == 'pending') {
           ?>
           <div class="alert alert-danger">
             <i class="fa fa-warning fa-fw"></i> <?php echo $this->TEXTV[12]; ?>
           </div>
           <?php
           }

           // If order is set to shipping and wasn`t a gateway payment, show message..
           if ($this->ORDER['paymentStatus'] == 'shipping' && in_array($this->ORDER['paymentMethod'], array('bank','cod','cheque','cash'))) {
           ?>
           <div class="alert alert-danger">
             <i class="fa fa-warning fa-fw"></i> <?php echo $this->TEXTV[13]; ?>
           </div>
           <?php
           }
           ?>

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
           // html/view-order/downloads.htm
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
                   // Show re-order buttonif enabled. Not shown for wish list purchases..
                   if ($this->SETTINGS['salereorder'] == 'yes' && $this->IS_WISH == 'no') {
                   ?>
                   <hr>
                   <?php echo $this->TEXTV[15]; ?><br><br>
                   <button class="btn btn-default btn-sm" onclick="mc_reOrder('<?php echo $this->ORDER['id']; ?>')"><i class="fa fa-check-square-o fa-fw"></i> <?php echo $this->TEXTV[14]; ?></button>
                   <?php
                   }
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

           // STATUSES
           if ($this->BUILD['statuses']) {
           ?>
           <hr>
           <div class="table-responsive statusarea">
             <table class="table table-striped">
               <tbody>
                 <?php
                 // html/view-order/status.htm
                 echo $this->BUILD['statuses'];
                 ?>
               </tbody>
             </table>
           </div>
           <?php
           }
           ?>

           <hr>

           <div class="text-center">
             <?php
             // Are PDFs enabled? This won`t be shown for a recipient viewing a wish list order (if enabled)..
             if ($this->SETTINGS['pdf'] == 'yes' && $this->SHOW_PDF == 'yes') {
             ?>
             <button class="btn btn-default" onclick="mc_pdf('<?php echo $this->ORDER['id']; ?>')"><i class="fa fa-file-pdf-o fa-fw"></i> <?php echo $this->TEXTV[5]; ?></button><br><br>
             <?php
             }
             ?>
             <a href="<?php echo $this->URL[2]; ?>"><i class="fa fa-arrow-left fa-fw"></i> <?php echo $this->TEXTV[6]; ?></a>
           </div>

         </div>
       </div>

     </div>


