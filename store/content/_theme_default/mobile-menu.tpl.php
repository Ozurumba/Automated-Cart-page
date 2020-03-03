               <?php
               // Checks template is loading via system, so do not move..
               if (!defined('PARENT')) {
                 exit;
               }

               // ACCOUNT MENU - SMALL / EXTRA SMALL SCREENS ONLY
               ?>
               <div class="mc_mobilemenu">
                 <div class="alert alert-info" style="margin-bottom:10px;padding:10px">
                   <a href="<?php echo $this->URL[0]; ?>"><i class="fa fa-dashboard fa-fw"></i> <?php echo $this->TEXT[8]; ?></a>
                 </div>

                 <div class="alert alert-info" style="margin-bottom:10px;padding:10px">
                   <a href="<?php echo $this->URL[1]; ?>"><i class="fa fa-user fa-fw"></i> <?php echo $this->TEXT[9]; ?></a>
                 </div>

                 <div class="alert alert-info" style="margin-bottom:10px;padding:10px">
                   <a href="<?php echo $this->URL[2]; ?>"><i class="fa fa-calendar fa-fw"></i> <?php echo $this->TEXT[10]; ?></a>
                 </div>

                 <?php
                 // Is wish list system enabled? Not enabled at all for trade accounts
                 if ($this->SETTINGS['en_wish'] == 'yes' && !defined('MC_TRADE_DISCOUNT')) {
                 ?>
                 <div class="alert alert-info" style="margin-bottom:10px;padding:10px">
                   <a href="<?php echo $this->URL[3]; ?>"><i class="fa fa-heart fa-fw"></i> <?php echo $this->TEXT[11]; ?></a><br>
                 </div>
                 <?php
                 }
                 ?>

                 <div class="alert alert-info" style="margin-bottom:10px;padding:10px">
                   <a href="<?php echo $this->URL[4]; ?>"><i class="fa fa-search-plus fa-fw"></i> <?php echo $this->TEXT[12]; ?></a><br>
                 </div>

                 <div class="alert alert-info" style="margin-bottom:10px;padding:10px">
                   <a href="<?php echo $this->URL[6]; ?>"><i class="fa fa-unlock fa-fw"></i> <?php echo $this->TEXT[14]; ?></a>
                 </div>

                 <?php
                 // Show additional info if logged in..
                 if (isset($this->ACCOUNT['type'])) {
                 ?>
                 <hr>
                 <div class="well account_info">
                   <b><?php echo $this->TEXT[16]; ?></b>:<br><?php echo ($this->ACCOUNT['type'] == 'personal' ? $this->TEXT[17] : $this->TEXT[18]); ?>
                   <?php
                   // This info only shows for trade accounts..
                   if ($this->ACCOUNT['type'] == 'trade' && isset($this->TRADE[0])) {
                   ?>
                   <br>
                   <b><?php echo $this->TEXT[19]; ?></b>:<br><?php echo $this->TRADE[0]; ?><br>
                   <b><?php echo $this->TEXT[20]; ?></b>:<br><?php echo $this->TRADE[1]; ?><br>
                   <b><?php echo $this->TEXT[21]; ?></b>:<br><?php echo $this->TRADE[2]; ?>
                   <?php
                   }
                   ?>
                 </div>
                 <?php
                 }
                 ?>
               </div>