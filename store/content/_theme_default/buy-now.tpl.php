<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}
// BUY NOW LOADING FILE
// DO NOT change the Ajax routines unless you know what you are doing!
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
  <link rel="icon" href="<?php echo $this->BASE_PATH; ?>/favicon.ico">
</head>

<body>

    <div class="container" id="buynow_container" style="margin-top:50px">
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
        <form method="post" action="#">
          <input type="hidden" name="qty" value="1">
        </form>
    </div>

    <script src="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/js/jquery.js"></script>
    <script>
    //<![CDATA[
    function mc_buyNow(id) {
      jQuery.ajax({
        type: 'POST',
        url: 'index.php?cart-ops=buynow&id=<?php echo $this->ID; ?>&loc=buynow',
        data: jQuery("#buynow_container > form").serialize(),
        cache: false,
        dataType: 'json',
        success: function (data) {
          switch(data['msg']) {
            case 'err':
              jQuery('#buynow_container .panel-heading').html('<i class="fa fa-warning fa-fw"></i> ' + data['text'][0]);
              jQuery('#buynow_container .panel-body').html(data['text'][1]);
              break;
            case 'rdr':
              window.location = data['url'];
              break;
          }
        }
      });
      return false;
    }
    jQuery(document).ready(function() {
      setTimeout(function() {
        mc_buyNow('<?php echo $this->ID; ?>');
      }, 1000);
    });
    //]]>
    </script>

</body>

</html>
