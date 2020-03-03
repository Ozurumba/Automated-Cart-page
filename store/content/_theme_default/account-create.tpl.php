     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // ACCOUNT CREATION TEMPLATE FILE
     ?>

     <div class="container maincontainer">
       <div class="row">
         <div class="col-lg-3 col-md-3 leftmenuwrapper">
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
         <div class="col-lg-9 col-md-9 rightbodyarea margin_bottom_50" id="formfield">
           <form method="post" action="#">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-pencil fa-fw"></i> <?php echo $this->TEXT[13]; ?>
             </div>
             <div class="panel-body">
               <?php echo $this->TEXTP[0]; ?>
             </div>
           </div>

           <div class="row">
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <ul class="nav nav-tabs">
                 <li class="active"><a href="#one" data-toggle="tab"><i class="fa fa-cog fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $this->TEXTP[1]; ?></span></a></li>
                 <li><a href="#two" data-toggle="tab"><i class="fa fa-credit-card fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $this->TEXTP[2]; ?></span></a></li>
                 <li><a href="#three" data-toggle="tab"><i class="fa fa-truck fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $this->TEXTP[3]; ?></span></a></li>
                 <li><a href="#four" data-toggle="tab"><i class="fa fa-lock fa-fw"></i><span class="hidden-sm hidden-xs"> <?php echo $this->TEXTP[4]; ?></span></a></li>
               </ul>
             </div>
           </div>

           <div class="row" style="margin-top:10px">
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <div class="tab-content">
                 <div class="tab-pane active in" id="one">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[6]; ?></label>
                         <input type="text" class="form-control" name="acc[name]" value="" onkeyup="mc_popFlds('nm', this.value)">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[7]; ?></label>
                         <input type="text" class="form-control" name="acc[em]" value="" onkeyup="mc_popFlds('em', this.value)">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[11]; ?></label>
                         <div class="checkbox">
                           <label><input type="checkbox" name="acc[news]" value="yes"></label>
                         </div>
                       </div>
                     </div>
                   </div>
                 </div>
                 <div class="tab-pane fade" id="two">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[23]; ?></label>
                         <div class="form-group input-group">
                           <span class="input-group-addon"><a href="#" onclick="mc_fieldCopyAccounts('billing');return false" title="<?php echo mc_safeHTML($this->TEXTP[20]); ?>"><i class="fa fa-copy fa-fw"></i></a></span>
                           <input type="text" class="form-control" name="bill[nm]" value="">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[24]; ?></label>
                         <input type="text" class="form-control" name="bill[em]" value="">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[12]; ?></label>
                         <select name="bill[country]" class="form-control" onchange="mc_stateLoaderSelect('bill', this.value, '0','no')">
                          <?php
                          // COUNTRIES
                          // html/html-option-tags.htm
                          echo $this->COUNTRIES['bill'];
                          ?>
                         </select>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[13]; ?></label>
                         <input type="text" class="form-control" name="bill[1]" value="">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[14]; ?></label>
                         <input type="text" class="form-control" name="bill[2]" value="">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[15]; ?></label>
                         <input type="text" class="form-control" name="bill[3]" value="">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[16]; ?></label>
                         <div id="bstbox">
                           <?php
                           // STATES / COUNTY / OTHER
                           // Loads input box or select..
                           // Modify lists in 'control/states/* (See readme)
                           // May change on page load..
                           ?>
                           <input type="text" class="form-control" name="bill[4]" value="">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[17]; ?></label>
                         <input type="text" class="form-control" name="bill[5]" value="">
                       </div>
                     </div>
                   </div>
                 </div>
                 <div class="tab-pane fade" id="three">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[25]; ?></label>
                         <div class="form-group input-group">
                           <span class="input-group-addon"><a href="#" onclick="mc_fieldCopyAccounts('shipping');return false" title="<?php echo mc_safeHTML($this->TEXTP[19]); ?>"><i class="fa fa-copy fa-fw"></i></a></span>
                           <input type="text" class="form-control" name="ship[nm]" value="">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[26]; ?></label>
                         <input type="text" class="form-control" name="ship[em]" value="">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[12]; ?></label>
                         <select name="ship[country]" class="form-control" onchange="mc_stateLoaderSelect('ship', this.value, '0','no')">
                          <?php
                          // COUNTRIES
                          // html/html-option-tags.htm
                          echo $this->COUNTRIES['ship'];
                          ?>
                         </select>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[13]; ?></label>
                         <input type="text" class="form-control" name="ship[1]" value="">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[14]; ?></label>
                         <input type="text" class="form-control" name="ship[2]" value="">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[15]; ?></label>
                         <input type="text" class="form-control" name="ship[3]" value="">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[16]; ?></label>
                         <div id="sstbox">
                           <?php
                           // STATES / COUNTY / OTHER
                           // Loads input box or select..
                           // Modify lists in 'control/states/* (See readme)
                           // May change on page load..
                           ?>
                           <input type="text" class="form-control" name="ship[4]" value="">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[17]; ?></label>
                         <input type="text" class="form-control" name="ship[5]" value="">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[18]; ?></label>
                         <input type="text" class="form-control" name="ship[6]" value="">
                       </div>
                     </div>
                   </div>
                 </div>
                 <div class="tab-pane fade" id="four">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[9]; ?></label>
                         <input type="password" class="form-control" name="acc[pass]" value="" autocomplete="off">
                         <span class="help-block"><?php echo $this->PASS_INSTRUCTION; ?></span>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[10]; ?></label>
                         <input type="password" class="form-control" name="acc[pass2]" value="">
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
           </div>

           <?php
           // DO NOT remove this field. It checks if bot has tried to submit form
           // If this field contains any value at all, the form fails.
           // Easy spam protection without captchas. :))
           // If spam is still a problem, enable the CleanTalk API
           ?>
           <input type="hidden" name="acc[blank]" value="">
           <button type="button" class="btn btn-primary" onclick="mc_create()"><i class="fa fa-check fa-fw"></i> <?php echo $this->TEXT[13]; ?></button>

           </form>
         </div>
       </div>

     </div>


