         <?php
         // Checks template is loading via checkout system, so do not move..
         if (!defined('CHECKOUT_LOADED')) {
           exit;
         }

         // CHECKOUT PRODUCTS TEMPLATE FILE
         ?>
         <div class="col-lg-9 col-md-9 rightbodyarea" id="formfield">
           <form method="post" action="#">
           <?php
           if ($this->CART_COUNT > 0) {
             ?>
             <div class="panel panel-default">
               <div class="panel-heading uppercase">
                 <span class="pull-right no-transform"><a href="#" onclick="mc_clearBasket('<?php echo $this->TEXT_JS[1]; ?>');return false"><i class="fa fa-times fa-fw mc-red"></i><span class="hidden-xs"><?php echo $this->TXT[0][8]; ?></span></a></span>
                 <i class="fa fa-cubes fa-fw"></i> <?php echo str_replace('{count}',$this->CART_COUNT,$this->TXT[0][4]); ?>
               </div>
               <div class="panel-body">
                <?php echo $this->TXT[3]; ?>
               </div>
             </div>

             <?php
             // BASKET ITEMS
             // html/basket-checkout/basket-wrapper.htm
             // html/basket-checkout/basket-gift-info.htm
             // html/basket-checkout/basket-item.htm
             // html/basket-checkout/basket-item-add-text.htm
             // html/basket-checkout/basket-personalisation-wrapper.htm
             // html/basket-checkout/basket-personalisation-option.htm
             echo $this->BASKET_ITEMS;
             ?>

             <div class="panel panel-default baskettotalarea">
               <div class="panel-body">
                 <span class="pull-right"><b id="mc_btotal"><?php echo $this->BASKET_TOTAL; ?></b></span>
                 <?php echo $this->TXT[2]; ?>
               </div>
               <div class="panel-footer">
                 <i class="fa fa-exclamation fa-fw"></i> <?php echo ($this->SHOW_SHIPPING == 'yes' ? $this->TXT[0][12] : $this->TXT[0][15]); ?>
               </div>
             </div>

             <?php
             // Is min checkout restriction set?
             if ($this->MIN_CHECKOUT_RESTRICTION == 'no') {
               // If visitor is already logged in, just show continue button..
               if (isset($this->ACCOUNT['id'])) {
               ?>
               <div class="row margin_bottom_50">
                 <div class="col-lg-12 text-center">
                   <hr>
                   <button type="button" class="btn btn-success" onclick="mc_checkout('acc','no')"><?php echo $this->TXT[0][5]; ?> <i class="fa fa-chevron-circle-right fa-fw"></i></button>
                 </div>
               </div>
               <?php
               } else {
               ?>
               <div class="row">
                 <div class="col-lg-6 col-md-6">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <div class="form-group input-group">
                         <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                         <input type="text" name="chk[em]" value="" class="form-control" tabindex="1" placeholder="<?php echo mc_safeHTML($this->TXT[0][1]); ?>">
                       </div>
                       <div class="form-group input-group">
                         <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                         <input type="password" name="chk[pw]" value="" class="form-control" tabindex="1" placeholder="<?php echo mc_safeHTML($this->TXT[0][2]); ?>">
                       </div>
                       <hr>
                       <button type="button" class="btn btn-success" onclick="mc_checkout('acc','yes')"><?php echo $this->TXT[0][3]; ?> <i class="fa fa-chevron-circle-right fa-fw"></i></button>
                     </div>
                   </div>
                 </div>
                 <div class="col-lg-6 col-md-6">
                   <div class="panel panel-default">
                     <div class="panel-body">
                       <?php echo $this->TXT[0][7]; ?>
                       <hr>
                       <button type="button" class="btn btn-primary" onclick="mc_checkout('guest','no')"><?php echo $this->TXT[0][6]; ?> <i class="fa fa-chevron-circle-right fa-fw"></i></button>
                     </div>
                   </div>
                 </div>
               </div>
               <?php
               }
             } else{
             ?>
             <div class="alert alert-danger">
             <i class="fa fa-warning fa-fw"></i> <?php echo $this->MIN_CHECKOUT_AMNT; ?>
             </div>
             <?php
             }
           } else {
           ?>
           <div class="panel panel-danger">
             <div class="panel-heading uppercase">
               <i class="fa fa-cubes fa-fw"></i> <?php echo str_replace('{count}',0,$this->TXT[0][4]); ?>
             </div>
             <div class="panel-body empty-basket">
               <i class="fa fa-shopping-basket fa-fw"></i>
               <hr>
               <?php echo $this->TXT[1]; ?>
             </div>
           </div>
           <?php
           }
           ?>
           </form>
         </div>