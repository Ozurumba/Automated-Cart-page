     <?php
     // Checks template is loading via system, so do not move..
     if (!defined('PARENT')) {
       exit;
     }

     // ACCOUNT PROFILE TEMPLATE FILE
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
         <div class="col-lg-9 col-md-9 rightbodyarea margin_bottom_50" id="formfield">
           <form method="post" action="#">
           <div class="panel panel-default">
             <div class="panel-heading uppercase">
               <i class="fa fa-user fa-fw"></i> <?php echo $this->TEXT[9]; ?>
             </div>
             <div class="panel-body">
               <?php echo ($this->SETTINGS['en_wish'] == 'yes' ? $this->TEXTP[26] : $this->TEXTP[0]); ?>
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
                         <input type="text" class="form-control" name="acc[name]" value="<?php echo mc_safeHTML($this->ACCOUNT['name']); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[7]; ?></label>
                         <input type="text" class="form-control" name="acc[em]" value="<?php echo mc_safeHTML($this->ACCOUNT['email']); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[11]; ?></label>
                         <div class="checkbox">
                           <label><input type="checkbox" name="acc[newsletter]" value="yes"<?php echo ($this->ACCOUNT['newsletter'] == 'yes' ? ' checked="checked"' : ''); ?>></label>
                         </div>
                       </div>
                     </div>
                   </div>
                 </div>
                 <div class="tab-pane fade" id="two">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[21]; ?></label>
                         <div class="form-group input-group">
                           <span class="input-group-addon"><a href="#" onclick="mc_fieldCopyAccounts('billing');return false" title="<?php echo mc_safeHTML($this->TEXTP[20]); ?>"><i class="fa fa-copy fa-fw"></i></a></span>
                           <input type="text" class="form-control" name="bill[nm]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['bill']['nm']); ?>">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[22]; ?></label>
                         <input type="text" class="form-control" name="bill[em]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['bill']['em']); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[12]; ?></label>
                         <select name="bill[country]" class="form-control" onchange="mc_stateLoaderSelect('bill', this.value, '<?php echo $this->ACCOUNT['id']; ?>','no')">
                          <?php
                          // COUNTRIES
                          // html/html-option-tags.htm
                          echo $this->COUNTRIES['bill'];
                          ?>
                         </select>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[13]; ?></label>
                         <input type="text" class="form-control" name="bill[1]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['bill']['addr2']); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[14]; ?></label>
                         <input type="text" class="form-control" name="bill[2]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['bill']['addr3']); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[15]; ?></label>
                         <input type="text" class="form-control" name="bill[3]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['bill']['addr4']); ?>">
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
                           <input type="text" class="form-control" name="bill[4]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['bill']['addr5']); ?>">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[17]; ?></label>
                         <input type="text" class="form-control" name="bill[5]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['bill']['addr6']); ?>">
                       </div>
                     </div>
                   </div>
                 </div>
                 <div class="tab-pane fade" id="three">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[23]; ?></label>
                         <div class="form-group input-group">
                           <span class="input-group-addon"><a href="#" onclick="mc_fieldCopyAccounts('shipping');return false" title="<?php echo mc_safeHTML($this->TEXTP[19]); ?>"><i class="fa fa-copy fa-fw"></i></a></span>
                           <input type="text" class="form-control" name="ship[nm]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['ship']['nm']); ?>">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[24]; ?></label>
                         <input type="text" class="form-control" name="ship[em]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['ship']['em']); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[12]; ?></label>
                         <select name="ship[country]" class="form-control" onchange="mc_stateLoaderSelect('ship', this.value, '<?php echo $this->ACCOUNT['id']; ?>','yes');mc_wishZone(this.value)">
                          <?php
                          // COUNTRIES
                          // html/html-option-tags.htm
                          echo $this->COUNTRIES['ship'];
                          ?>
                         </select>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[13]; ?></label>
                         <input type="text" class="form-control" name="ship[1]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['ship']['addr2']); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[14]; ?></label>
                         <input type="text" class="form-control" name="ship[2]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['ship']['addr3']); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[15]; ?></label>
                         <input type="text" class="form-control" name="ship[3]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['ship']['addr4']); ?>">
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
                           <input type="text" class="form-control" name="ship[4]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['ship']['addr5']); ?>">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[17]; ?></label>
                         <input type="text" class="form-control" name="ship[5]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['ship']['addr6']); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[18]; ?></label>
                         <input type="text" class="form-control" name="ship[6]" value="<?php echo mc_safeHTML($this->BILL_SHIP_FLDS['ship']['addr7']); ?>">
                       </div>
                     </div>
                   </div>
                   <?php
                   // If wish lists are enabled, show shipping zones..
                   if ($this->SETTINGS['en_wish'] == 'yes') {
                   ?>
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[27]; ?></label>
                         <select name="ship[zone]" class="form-control">
                          <option value="0">- - - -</option>
                          <?php
                          // ZONES
                          // html/accounts/zone-opt-group.htm
                          // html/accounts/zone-option.htm
                          echo $this->ZONES;
                          ?>
                         </select>
                       </div>
                     </div>
                   </div>
                   <?php
                   }
                   ?>
                 </div>
                 <div class="tab-pane fade" id="four">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[8]; ?></label>
                         <input type="password" class="form-control" name="acc[old]" value="" onkeyup="mc_passFld()">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[9]; ?></label>
                         <input type="password" class="form-control" name="acc[pass]" value="" disabled="disabled">
                         <span class="help-block"><?php echo $this->PASS_INSTRUCTION; ?></span>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TEXTP[10]; ?></label>
                         <input type="password" class="form-control" name="acc[pass2]" value="" disabled="disabled">
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
           </div>

           <?php
           // DO NOT remove this field. It checks if bot has tried to submit form
           ?>
           <input type="hidden" name="acc[blank]" value="">
           <button type="button" class="btn btn-primary" onclick="mc_profile()"><i class="fa fa-check fa-fw"></i> <?php echo $this->TEXTP[5]; ?></button>

           <?php
           // Show close link..
           if ($this->SETTINGS['en_close'] == 'yes') {
           ?>
           <div class="text-right"><a class="mc-red" href="<?php echo $this->URL[7]; ?>"><i class="fa fa-times fa-fw mc-red"></i><?php echo $this->TEXTP[25]; ?></a></div>
           <?php
           }
           ?>

           </form>
         </div>
       </div>

     </div>


