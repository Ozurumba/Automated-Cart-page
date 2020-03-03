     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // ACCOUNT PASSWORD RESET TEMPLATE FILE
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
         <div class="col-lg-9 col-md-9 rightbodyarea margin_bottom_50" id="formfield">
           <form method="post" action="#">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-unlock fa-fw"></i> <?php echo $this->TXT[0]; ?>
             </div>
             <div class="panel-body">
               <?php echo $this->TXT[5]; ?>
             </div>
           </div>
           <div class="panel panel-default">
             <div class="panel-body">
               <div class="form-group">
                 <label><?php echo $this->TXT[3]; ?></label>
                 <input type="password" name="np[1]" value="" class="form-control" autocomplete="off" autofocus>
                 <span class="help-block"><?php echo $this->PASS_INSTRUCTION; ?></span>
               </div>
               <div class="form-group">
                 <label><?php echo $this->TXT[4]; ?></label>
                 <input type="password" name="np[2]" value="" class="form-control" autocomplete="off">
               </div>
               <div class="form-group">
                 <?php
                 // DO NOT remove this field. It checks if bot has tried to submit form
                 ?>
                 <input type="hidden" name="np[blank]" value="">
                 <input type="hidden" name="np[token]" value="<?php echo mc_safeHTML($this->TOKEN); ?>">
                 <button type="button" class="btn btn-primary" onclick="mc_passreset()"><i class="fa fa-check fa-fw"></i> <?php echo $this->TXT[6]; ?></button><br><br>
               </div>
             </div>
           </div>
           </form>
         </div>
       </div>

     </div>


