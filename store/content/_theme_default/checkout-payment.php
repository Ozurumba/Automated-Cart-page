         <?php
         // Checks template is loading via checkout system, so do not move..
         if (!defined('CHECKOUT_LOADED')) {
           exit;
         }

         // CHECKOUT PAYMENT ROUTINES TEMPLATE FILE
         ?>

         <div class="col-lg-9 col-md-9 rightbodyarea" id="mc_chk_payment" style="display:none !important">
           <div class="panel panel-default">
             <div class="panel-body">
               <?php echo (isset($this->ACCOUNT['id']) ? $this->TXT[7] : $this->PTEXT[14]); ?>
             </div>
           </div>

           <div class="row paymenttabs">
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <div class="btn-group" role="group" aria-label="...">
                 <div class="btn-group" role="group">
                   <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i class="fa fa-chevron-right fa-fw"></i> <?php echo $this->PTEXT[16]; ?>
                     &nbsp;&nbsp;<span class="caret"></span>
                   </button>
                   <ul class="dropdown-menu">
                     <li><a href="#" onclick="mc_chkShowNav(1);return false"><i class="fa fa-credit-card fa-fw"></i> <?php echo $this->PTEXT[1]; ?></a></li>
                     <?php
                     // Do we need to show the shipping options?
                     if ($this->SHOW_SHIPPING == 'yes') {
                     ?>
                     <li><a href="#" onclick="mc_chkShowNav(2);return false"><i class="fa fa-truck fa-fw"></i> <?php echo $this->PTEXT[2]; ?></a></li>
                     <li><a href="#" onclick="mc_chkShowNav(3);return false"><i class="fa fa-money fa-fw"></i> <?php echo $this->PTEXT[3]; ?></a></li>
                     <?php
                     }
                     ?>
                     <li><a href="#" onclick="mc_chkShowNav(4);return false"><i class="fa fa-gift fa-fw"></i> <?php echo $this->PTEXT[4]; ?></a></li>
                     <?php
                     // Only show for guest checkout..
                     if (!isset($this->ACCOUNT['id'])) {
                     ?>
                     <li class="liacc"><a href="#" onclick="mc_chkShowNav(5);return false"><i class="fa fa-user fa-fw"></i> <?php echo $this->PTEXT[5]; ?></a></li>
                     <?php
                     }
                     ?>
                     <li><a href="#" onclick="mc_chkShowNav(6);return false"><i class="fa fa-file-text-o fa-fw"></i> <?php echo $this->PTEXT[15]; ?></a></li>
                     <li><a href="#" onclick="mc_chkNav(7);return false"><i class="fa fa-check fa-fw"></i> <?php echo $this->PTEXT[6]; ?></a></li>
                   </ul>
                 </div>
                 <button type="button" class="btn btn-info" onclick="mc_reloadBasket('show')"><i class="fa fa-shopping-basket fa-fw"></i></button>
               </div>
             </div>
           </div>

           <form method="post" action="#" id="pform">
           <div class="row" style="margin-top:10px">
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 payoptarea">
               <div class="tab-content">
                 <div id="chkt1">
                   <div class="panel panel-default">
                     <div class="panel-heading uppercase">
                      <i class="fa fa-credit-card fa-fw"></i> <?php echo $this->PTEXT[1]; ?>
                     </div>
                     <div class="panel-body">
                       <div class="form-group">
                         <label><?php echo $this->TXT[6][4]; ?></label>
                         <input type="text" class="form-control" name="bill[nm]" value="<?php echo (isset($this->ADDR['bill']['nm']) ? mc_safeHTML($this->ADDR['bill']['nm']) : ''); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TXT[6][5]; ?></label>
                         <input type="text" class="form-control" name="bill[em]" value="<?php echo (isset($this->ADDR['bill']['em']) ? mc_safeHTML($this->ADDR['bill']['em']) : ''); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[12]; ?></label>
                         <select name="bill[country]" class="form-control" onchange="if(this.value!='0'){mc_initCheckout('no');}">
                         <?php
                         // COUNTRIES
                         // html/html-option-tags.htm
                         echo $this->COUNTRIES['bill'];
                         ?>
                         </select>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[13]; ?></label>
                         <input type="text" class="form-control" name="bill[1]" value="<?php echo (isset($this->ADDR['bill']['addr2']) ? mc_safeHTML($this->ADDR['bill']['addr2']) : ''); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[14]; ?></label>
                         <input type="text" class="form-control" name="bill[2]" value="<?php echo (isset($this->ADDR['bill']['addr3']) ? mc_safeHTML($this->ADDR['bill']['addr3']) : ''); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[15]; ?></label>
                         <input type="text" class="form-control" name="bill[3]" value="<?php echo (isset($this->ADDR['bill']['addr4']) ? mc_safeHTML($this->ADDR['bill']['addr4']) : ''); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[16]; ?></label>
                         <div id="bstbox">
                           <?php
                           // STATES / COUNTY / OTHER
                           // Loads input box or select..
                           // Modify lists in 'control/states/* (See readme)
                           // May change on page load..
                           ?>
                           <input type="text" class="form-control" name="bill[4]" value="<?php echo (isset($this->ADDR['bill']['addr5']) ? mc_safeHTML($this->ADDR['bill']['addr5']) : ''); ?>">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[17]; ?></label>
                         <input type="text" class="form-control" name="bill[5]" value="<?php echo (isset($this->ADDR['bill']['addr6']) ? mc_safeHTML($this->ADDR['bill']['addr6']) : ''); ?>">
                       </div>
                       <hr>
                       <div class="buttons">
                         <button type="button" class="btn btn-primary" onclick="mc_chkNav(2)"><?php echo $this->PTEXT[7]; ?> <i class="fa fa-arrow-circle-right fa-fw"></i></button>
                       </div>
                     </div>
                   </div>
                 </div>
                 <div id="chkt2" style="display:none">
                   <div class="panel panel-default">
                     <div class="panel-heading uppercase">
                      <i class="fa fa-truck fa-fw"></i> <?php echo $this->PTEXT[2]; ?>
                     </div>
                     <div class="panel-body">
                       <?php
                       // Hide fields if wish list purchase..
                       if ($this->WISH_PURCHASE == 'no') {
                       ?>
                       <div class="form-group">
                         <label><?php echo $this->TXT[6][6]; ?></label>
                         <div class="form-group input-group">
                           <span class="input-group-addon"><a href="#" onclick="mc_fieldCopyAccounts('shipping');return false" title="<?php echo mc_safeHTML($this->ADTXT[19]); ?>"><i class="fa fa-copy fa-fw"></i></a></span>
                           <input type="text" class="form-control" name="ship[nm]" value="<?php echo (isset($this->ADDR['ship']['nm']) ? mc_safeHTML($this->ADDR['ship']['nm']) : ''); ?>">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->TXT[6][7]; ?></label>
                         <input type="text" class="form-control" name="ship[em]" value="<?php echo (isset($this->ADDR['ship']['em']) ? mc_safeHTML($this->ADDR['ship']['em']) : ''); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[12]; ?></label>
                         <select name="ship[country]" class="form-control" onchange="if(this.value!='0'){mc_initCheckout('no');}">
                         <?php
                         // COUNTRIES
                         // html/html-option-tags.htm
                         echo $this->COUNTRIES['ship'];
                         ?>
                         </select>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[13]; ?></label>
                         <input type="text" class="form-control" name="ship[1]" value="<?php echo (isset($this->ADDR['ship']['addr2']) ? mc_safeHTML($this->ADDR['ship']['addr2']) : ''); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[14]; ?></label>
                         <input type="text" class="form-control" name="ship[2]" value="<?php echo (isset($this->ADDR['ship']['addr3']) ? mc_safeHTML($this->ADDR['ship']['addr3']) : ''); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[15]; ?></label>
                         <input type="text" class="form-control" name="ship[3]" value="<?php echo (isset($this->ADDR['ship']['addr4']) ? mc_safeHTML($this->ADDR['ship']['addr4']) : ''); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[16]; ?></label>
                         <div id="sstbox">
                           <?php
                           // STATES / COUNTY / OTHER
                           // Loads input box or select..
                           // Modify lists in 'control/states/* (See readme)
                           // May change on page load..
                           ?>
                           <input type="text" class="form-control" name="ship[4]" value="<?php echo (isset($this->ADDR['ship']['addr5']) ? mc_safeHTML($this->ADDR['ship']['addr5']) : ''); ?>">
                         </div>
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[17]; ?></label>
                         <input type="text" class="form-control" name="ship[5]" value="<?php echo (isset($this->ADDR['ship']['addr6']) ? mc_safeHTML($this->ADDR['ship']['addr6']) : ''); ?>">
                       </div>
                       <div class="form-group">
                         <label><?php echo $this->ADTXT[18]; ?></label>
                         <input type="text" class="form-control" name="ship[6]" value="<?php echo (isset($this->ADDR['ship']['addr7']) ? mc_safeHTML($this->ADDR['ship']['addr7']) : ''); ?>">
                       </div>
                       <?php
                       // Hidden vars for wish list purchase...
                       } else {
                       echo $this->WISH_MSG;
                       ?>
                       <input type="hidden" name="ship[country]" value="<?php echo $this->COUNTRY; ?>">
                       <?php
                       }
                       ?>
                       <hr>
                       <div class="buttons">
                         <button type="button" class="btn btn-info backbutton" onclick="mc_chkShowNav(1)"><i class="fa fa-arrow-circle-left fa-fw"></i> <?php echo $this->PTEXT[8]; ?></button>
                         <button type="button" class="btn btn-primary" onclick="mc_chkNav(3)"><?php echo $this->PTEXT[7]; ?> <i class="fa fa-arrow-circle-right fa-fw"></i></button>
                       </div>
                     </div>
                   </div>
                 </div>
                 <div id="chkt3" style="display:none">
                   <div class="panel panel-default">
                     <div class="panel-heading uppercase">
                      <i class="fa fa-money fa-fw"></i> <?php echo $this->PTEXT[3]; ?>
                     </div>
                     <div class="panel-body" id="mc_ship_options">
                       <div class="ship_sel">
                         <?php
                         // SHIPPING OPTIONS
                         // html/basket-checkout/basket-shipping.htm
                         // html/basket-checkout/basket-shipping-flat.htm
                         // html/basket-checkout/basket-shipping-percent.htm
                         // html/basket-checkout/basket-shipping-wrapper.htm
                         ?>
                         <label><?php echo $this->TXT[4]; ?></label>
                         <select name="ship_code" class="form-control" onchange="mc_shipOpts('no','<?php echo $this->SHOW_SHIPPING; ?>','no')">
                           <option value="0">&nbsp;</option>
                         </select>
                         <hr>
                       </div>
                       <div class="buttons">
                         <button type="button" class="btn btn-info backbutton" onclick="mc_chkShowNav(2)"><i class="fa fa-arrow-circle-left fa-fw"></i> <?php echo $this->PTEXT[8]; ?></button>
                         <button type="button" class="btn btn-primary" onclick="mc_chkNav(4)"><?php echo $this->PTEXT[7]; ?> <i class="fa fa-arrow-circle-right fa-fw"></i></button>
                       </div>
                     </div>
                   </div>
                 </div>
                 <div id="chkt4" style="display:none">
                   <div class="panel panel-default">
                     <div class="panel-heading uppercase">
                      <i class="fa fa-gift fa-fw"></i> <?php echo $this->PTEXT[4]; ?>
                     </div>
                     <div class="panel-body">
                       <label><?php echo $this->TXT[5]; ?></label>
                       <input type="text" name="coupon" class="form-control">
                       <span class="help-block coupon-help-block"><?php echo $this->PTEXT[22]; ?></span>
                       <hr>
                       <div class="buttons">
                         <button type="button" class="btn btn-info backbutton" onclick="mc_chkShowNav(<?php echo ($this->SHOW_SHIPPING == 'yes' ? '3' : '1'); ?>)"><i class="fa fa-arrow-circle-left fa-fw"></i> <?php echo $this->PTEXT[8]; ?></button>
                         <button type="button" class="btn btn-primary" onclick="mc_chkNav(5)"><?php echo $this->PTEXT[7]; ?> <i class="fa fa-arrow-circle-right fa-fw"></i></button>
                       </div>
                     </div>
                   </div>
                 </div>
                 <?php
                 // Only show for guest checkout..
                 if (!isset($this->ACCOUNT['id'])) {
                 ?>
                 <div id="chkt5" style="display:none">
                   <div class="panel panel-default">
                     <div class="panel-heading uppercase">
                      <i class="fa fa-user fa-fw"></i> <?php echo $this->PTEXT[5]; ?>
                     </div>
                     <div class="panel-body">
                       <label><?php echo $this->TXT[6][0]; ?></label>
                       <div class="alert alert-success alert-mcart">
                         <?php echo $this->TXT[6][1]; ?>
                       </div>
                       <select name="acc-open" class="form-control">
                         <option value="no" selected="selected"><?php echo $this->TXT[6][3]; ?></option>
                         <option value="yes"><?php echo $this->TXT[6][2]; ?></option>
                       </select>
                       <hr>
                       <div class="buttons">
                         <button type="button" class="btn btn-info backbutton" onclick="mc_chkShowNav(4)"><i class="fa fa-arrow-circle-left fa-fw"></i> <?php echo $this->PTEXT[8]; ?></button>
                         <button type="button" class="btn btn-primary" onclick="mc_chkNav(6)"><?php echo $this->PTEXT[7]; ?> <i class="fa fa-arrow-circle-right fa-fw"></i></button>
                       </div>
                     </div>
                   </div>
                 </div>
                 <?php
                 }
                 ?>
                 <div id="chkt6" style="display:none">
                   <div class="panel panel-default">
                     <div class="panel-heading uppercase">
                      <i class="fa fa-file-text-o fa-fw"></i> <?php echo $this->PTEXT[15]; ?>
                     </div>
                     <div class="panel-body">
                       <label><?php echo $this->PTEXT[18]; ?></label>
                       <textarea rows="5" cols="20" name="notes" class="form-control"></textarea>
                       <hr>
                       <div class="buttons">
                         <button type="button" class="btn btn-info backbutton notesbackbutton" onclick="mc_chkShowNav(<?php echo (!isset($this->ACCOUNT['id']) ? '5' : '4'); ?>)"><i class="fa fa-arrow-circle-left fa-fw"></i> <?php echo $this->PTEXT[8]; ?></button>
                         <button type="button" class="btn btn-primary" onclick="mc_chkNav(7)"><?php echo $this->PTEXT[7]; ?> <i class="fa fa-arrow-circle-right fa-fw"></i></button>
                       </div>
                     </div>
                   </div>
                 </div>
                 <div id="chkt7" style="display:none">
                   <div class="panel panel-default">
                     <div class="panel-heading uppercase">
                      <i class="fa fa-check fa-fw"></i> <?php echo $this->PTEXT[6]; ?>
                     </div>
                     <div class="panel-body paymenttotalsarea">
                       <div class="row">
                         <div class="col-lg-6">
                           <label><?php echo $this->TXT[8]; ?></label>
                           <?php
                           // PAYMENT METHODS SELECTIONS
                           // html/basket-checkout/basket-payment-methods.htm
                           // html/basket-checkout/basket-payment-methods-wrapper.htm
                           echo $this->PAYMENT_OPTIONS;
                           ?>
                           <div class="alert alert-info gatewayinfo">
                             <i class="fa fa-chevron-circle-right fa-fw"></i> <a rel="nofollow" href="<?php echo $this->GATE_URL; ?>" onclick="mc_Window(this.href, 600, 600, '');return false"><?php echo $this->TXT[9]; ?></a>
                           </div>
                           <div class="pay-icon hidden-xs">
                             <a rel="nofollow" href="<?php echo $this->GATE_URL; ?>" onclick="mc_Window(this.href, 600, 600, '');return false"><img src="<?php echo $this->DEF_ICON; ?>.png" alt=""></a>
                           </div>
                         </div>
                         <div class="col-lg-6">
                           <div class="table-responsive">
                             <table class="table table-striped">
                               <tbody>
                               <?php
                               // BASKET TOTALS
                               // html/basket-checkout/basket-option-total.htm
                               echo $this->TOTALS;
                               ?>
                               </tbody>
                             </table>
                           </div>
                           <div class="grandtotal">
                             <span class="pull-left"><?php echo $this->PTEXT[20]; ?></span>
                             <span class="grand"><?php echo $this->BASKET_TOTAL; ?></span>
                           </div>
                           <?php
                           // If currency converter is on and convertion isn`t payment currency, show message..
                           if ($this->CUR_MESSAGE) {
                           ?>
                           <div class="curconv">
                             <i class="fa fa-warning fa-fw"></i> <?php echo $this->CUR_MESSAGE; ?>
                           </div>
                           <?php
                           }
                           ?>
                         </div>
                       </div>
                       <hr>
                       <?php
                       // Are terms and conditions enabled?
                       if ($this->SETTINGS['tc'] == 'yes') {
                       ?>
                       <div class="tandc">
                         <div class="checkbox">
                           <label><input type="checkbox" name="tandc" value="yes"> <?php echo $this->TXT[11]; ?></label>
                         </div>
                       </div>
                       <?php
                       }
                       ?>
                       <div class="buttons">
                         <?php
                         // If wish list purchase, add hidden var..
                         if ($this->WISH_ID > 0) {
                         ?>
                         <input type="hidden" name="wish" value="<?php echo $this->WISH_ID; ?>">
                         <?php
                         }
                         ?>
                         <button type="button" class="btn btn-info backbutton" onclick="mc_chkShowNav(6)"><i class="fa fa-arrow-circle-left fa-fw"></i><span class="hidden-xs"> <?php echo $this->PTEXT[8]; ?></span></button>
                         <button type="button" class="btn btn-success" onclick="mc_chkNav('pay')"><i class="fa fa-check-circle fa-fw"></i> <?php echo $this->TXT[10]; ?></button>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
           </div>
           </form>
         </div>