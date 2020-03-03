     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // GIFT CERTIFICATE TEMPLATE FILE
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
           <form method="post" action="#">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-gift fa-fw"></i> <?php echo $this->TXT[0]; ?>
             </div>
             <div class="panel-body">
               <?php echo $this->TXT[1]; ?>
             </div>
           </div>
           <?php
           // GIFT CERTIFICATES
           // html/products/gift-certificates-wrapper.htm
           // html/products/gift-certificate.htm
           // content/products/gift-*.gif
           echo $this->GIFT_CERTIFICATES;
           ?>
           <div class="panel panel-default">
             <div class="panel-heading">
               <i class="fa fa-user fa-fw"></i> <?php echo $this->TXT[9][0]; ?>
             </div>
             <div class="panel-body">
               <label><?php echo $this->TXT[4]; ?></label>
               <input type="text" name="gift[fn]" value="<?php echo $this->FROM['name']; ?>" class="form-control" tabindex="1">

               <label style="padding-top:10px"><?php echo $this->TXT[5]; ?></label>
               <input type="text" name="gift[fe]" value="<?php echo $this->FROM['email']; ?>" class="form-control" tabindex="2">
             </div>
           </div>

           <div class="panel panel-default">
             <div class="panel-heading">
               <i class="fa fa-envelope-o fa-fw"></i> <?php echo $this->TXT[9][1]; ?>
             </div>
             <div class="panel-body">
               <label><?php echo $this->TXT[6]; ?></label>
               <input type="text" name="gift[tn]" value="" class="form-control" tabindex="3">

               <label style="padding-top:10px"><?php echo $this->TXT[7]; ?></label>
               <input type="text" name="gift[te]" value="" class="form-control" tabindex="4">
             </div>
           </div>

           <div class="panel panel-default">
             <div class="panel-heading">
               <i class="fa fa-quote-left fa-fw"></i> <?php echo $this->TXT[8]; ?>
             </div>
             <div class="panel-body">
               <textarea rows="5" cols="2" name="gift[msg]" tabindex="5" class="form-control"></textarea>
             </div>
           </div>

           <div class="text-center margin_bottom_50">
             <button type="button" class="btn btn-success" onclick="mc_addGift('<?php echo ($this->FROM['name'] ? 'no' : 'yes'); ?>')"><i class="fa fa-cart-plus fa-fw"></i> <?php echo $this->TXT[3]; ?></button>
           </div>
           </form>
         </div>
       </div>
     </div>
