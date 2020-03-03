     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // ACCOUNT LOGIN TEMPLATE FILE
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
               <i class="fa fa-lock fa-fw"></i> <?php echo $this->TEXT[0]; ?>
             </div>
             <div class="panel-body">
               <?php
               // If account can be created manually, show link..
               if ($this->SETTINGS['en_create'] == 'yes') {
               ?>
               <div class="pull-right">
                 <a href="<?php echo $this->URL[5]; ?>"><i class="fa fa-pencil fa-fw"></i> <?php echo $this->TEXT[13]; ?></a>
               </div>
               <?php
               }
               echo $this->TEXT[2]; ?>
             </div>
           </div>
           <div class="panel panel-default">
             <div class="panel-body">
               <div class="form-group">
                 <label><?php echo $this->TEXT[3]; ?></label>
                 <div class="form-group input-group">
                   <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                   <input type="text" name="lg[e]" value="<?php echo mc_safeHTML($this->SS['email']); ?>" onkeypress="if(mc_KeyCode(event)==13){mc_login()}" class="form-control" autofocus>
                 </div>
               </div>
               <div class="form-group">
                 <label><?php echo $this->TEXT[4]; ?></label>
                 <div class="form-group input-group">
                   <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                   <input type="password" name="lg[p]" value="" class="form-control" autocomplete="off" onkeypress="if(mc_KeyCode(event)==13){mc_login()}">
                 </div>
               </div>
               <div class="form-group">
                 <?php
                 // DO NOT remove this field. It checks if bot has tried to submit form
                 ?>
                 <input type="hidden" name="lg[blank]" value="">
                 <button type="button" class="btn btn-primary" onclick="mc_login()"><i class="fa fa-sign-in fa-fw"></i> <?php echo $this->TEXT[5]; ?></button><br><br>
                 <a href="<?php echo $this->URL[7]; ?>"><?php echo $this->TEXT[6]; ?></a>
               </div>
             </div>
           </div>
           </form>
         </div>
       </div>

     </div>


