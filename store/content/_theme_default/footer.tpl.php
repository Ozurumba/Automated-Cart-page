    <?php
    // Checks template is loading via system, so do not move..
    if (!defined('PARENT')) {
      exit;
    }
    ?>
    <footer>

      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-4 text-left div1">
            <?php
            // NEW PAGE FOOTER BAR LINKS - LEFT
            // html/footer-bar-link.htm
            echo $this->LEFT_LINKS;
            ?>
          </div>
          <div class="col-lg-4 col-md-4 text-left div2">
            <?php
            // NEW PAGE FOOTER BAR LINKS - MIDDLE
            // html/footer-bar-link.htm
            echo $this->MIDDLE_LINKS;
            ?>
          </div>
          <div class="col-lg-4 col-md-4 text-left div3" id="msgfootfield">
            <form method="post" action="#">
              <div class="form-group">
                <label><?php echo $this->TEXT[1]; ?>:</label>
                <div class="form-group input-group">
                  <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                  <input type="text" name="msg[nm]" value="<?php echo mc_safeHTML($this->ACC['name']); ?>" class="form-control" placeholder="<?php echo mc_safeHTML($this->TEXT[3]); ?>">
                </div>
              </div>
              <div class="form-group">
                <div class="form-group input-group">
                  <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                  <input type="text" name="msg[em]" value="<?php echo mc_safeHTML($this->ACC['email']); ?>" class="form-control" placeholder="<?php echo mc_safeHTML($this->TEXT[4]); ?>">
                </div>
              </div>
              <div class="form-group">
                <textarea name="msg[msg]" rows="4" cols="20" placeholder="<?php echo mc_safeHTML($this->TEXT[5]); ?>" class="form-control"></textarea>
              </div>
              <div class="form-group">
                <?php
                // DO NOT remove this field. It checks if bot has tried to submit form
                // If this field contains any value at all, the form fails.
                // Easy spam protection without captchas. :))
                // If spam is still a problem, enable the CleanTalk API
                ?>
                <input type="hidden" name="msg[blank]" value="">
                <button type="button" class="btn btn-success btn-block" onclick="mc_send()"><i class="fa fa-paper-plane fa-fw"></i> <?php echo $this->TEXT[2]; ?></button>
              </div>
            </form>
            <?php
            // SOCIAL LINKS..
            if ($this->SOCIAL_LINKS) {
            ?>
            <div class="social">
            <?php
            // html/social/social-link.htm
            echo $this->SOCIAL_LINKS;
            ?>
            </div>
            <?php
            }
            ?>
          </div>
        </div>
      </div>

    </footer>

    <div class="bottombar">
      <?php
      // Please don`t remove the footer unless you have purchased a licence..
	    // https://www.maiancart.com/purchase.html
      echo $this->FOOTER;
      ?>
    </div>

  </div>

  <?php
  // If Stripe is enabled, load JS API for Stripe..
  if ($this->STRIPE_EN == 'yes') {
    include(PATH . $this->THEME_FOLDER . '/gateway-api/stripe.php');
  }
  
  // OFF CANVAS MENU
  // html/left-menu/categories.htm
  // html/left-menu/categories-children.htm
  // html/left-menu/categories-infants.htm
  // html/left-menu/categories-gift.htm
  ?>
  <nav class="pushy pushy-left">
    <div class="pushy-content">
      <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <?php
        echo $this->SLIDE_PANEL;
        ?>
      </div>
    </div>
  </nav>
  <div class="site-overlay"></div>
  
  <script src="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/js/jquery.js"></script>
  <script src="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/js/bootstrap.js"></script>
  <script src="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/js/plugins/bootbox.js"></script>
  <script src="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/js/plugins/jquery.pushy.js"></script>
  <script src="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/js/mc-cart.js"></script>
  <script src="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/js/mc-functions.js"></script>
  
  <?php
  // JS MODULES
  // Only required on certain pages
  echo $this->MODULES;
  
  // Stripe recommend loading their api on all pages. So, if stripe is enabled, load api..
  if ($this->STRIPE_EN == 'yes') {
  ?>
  <script src="https://js.stripe.com/v3"></script>
  <?php
  }
  
  // Mobile / Tablet adjustments..
  // Triggered on refresh on individual platform, not by resizing browser on computer. :)
  if (!defined('CHECKOUT_LOADED') && defined('MC_PLATFORM_DETECTION') && in_array(MC_PLATFORM_DETECTION, array('mobile','tablet'))) {
  ?>
  <script>
  //<![CDATA[
    jQuery(document).ready(function() {
      mc_mobileDetection('<?php echo MC_PLATFORM_DETECTION; ?>');
    });
  //]]>
  </script>
  <?php
  }
  
  // Action spinner, DO NOT REMOVE
  ?>
  <div class="overlaySpinner" style="display:none"></div>

  </body>
</html>