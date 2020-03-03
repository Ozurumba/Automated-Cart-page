<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}
// CHECKOUT CONNECTION TEMPLATE FILE
// Be careful when editing this file as it could break your payment routines
?>
<!DOCTYPE html>
<html lang="<?php echo $this->LANG; ?>" dir="<?php echo $this->DIR; ?>">
  <head>
    <meta charset="<?php echo $this->CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $this->TITLE; ?></title>
    <base href="<?php echo $this->BASE_PATH; ?>/">
    <link rel="stylesheet" href="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/css/style.css" type="text/css">
    <?php
    // TOKEN TRANSMISSION GATEWAYS OR GET REDIRECTS..
    if (in_array($this->DATA[0], array('refresh','redirect'))) {
    ?>
    <meta http-equiv="refresh" content="5;url=<?php echo $this->DATA[1]; ?>">
    <?php
    }
    ?>
    <link rel="icon" href="<?php echo $this->BASE_PATH; ?>/favicon.ico">
  </head>

  <body>

      <div class="container" style="margin-top:50px">
          <div class="row">
              <div class="col-lg-12">
                  <div class="login-panel panel panel-default connectionarea">
                      <div class="panel-heading uppercase">
                        <i class="fa fa-chevron-right fa-fw"></i> <?php echo $this->TXT[0]; ?>
                      </div>
                      <div class="panel-body">
                        <img src="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/images/doing-something.gif" alt=""><br><br>
                        <?php echo $this->TXT[1]; ?>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <?php
      // FORM TRANSMISSION GATEWAYS..
      if ($this->DATA[0] == 'form') {
      echo $this->DATA[1];
      ?>
      <script src="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/js/jquery.js"></script>
      <script>
      //<![CDATA[
      jQuery(document).ready(function() {
        setTimeout(function() {
          if (jQuery('#gateway').html()) {
            jQuery('#gateway').submit();
          } else {
            alert('No form data to send, check gateway parameters');
          }
        }, 3500);
      });
      //]]>
      </script>
      <?php
      }
      ?>

  </body>

</html>
