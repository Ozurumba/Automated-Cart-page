<?php if (!defined('PARENT')) { die('You do not have permission to view this file!!!'); } ?>
<!DOCTYPE html>

  <html lang="<?php echo $mc_global[1]; ?>" dir="<?php echo $mc_global[0]; ?>">

  <head>
    <meta charset="<?php echo $charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link href="templates/css/bootstrap.css" rel="stylesheet">
    <link href="templates/css/bootstrap-dialog.css" rel="stylesheet">
    <link href="templates/css/stylesheet.css" rel="stylesheet">
    <link href="templates/css/mc-login.css" rel="stylesheet">

    <link rel="shortcut icon" href="favicon.ico">

  </head>

  <body>

  <div class="container margin-top-container-login" id="mscontainer">

    <form method="post" action="index.php?p=login" onsubmit="return checkform()">
    <div class="row">
      <div class="col-md-4 col-md-offset-4 col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-3">
        <div class="login-panel panel panel-default">
          <div class="panel-heading">
            <span style="float:right"><i class="fa fa-lock fa-fw"></i></span>
            <h3 class="panel-title">- <?php echo $pageTitle; ?> -</h3>
          </div>
          <div class="panel-body">
            <fieldset>
              <div class="form-group">
                <input class="form-control" type="text" name="user" placeholder="<?php echo mc_cleanDataEntVars($msg_login); ?>" value="<?php echo (isset($_POST['user']) ? mc_safeHTML($_POST['user']) : ''); ?>" autofocus>
                <?php echo (isset($U_ERROR) ? '<span class="help-block error-block" style="text-align:left"><i class="fa fa-warning fa-fw"></i> ' . mc_safeHTML($U_ERROR) . '</span>' : ''); ?>
              </div>
              <div class="form-group">
                <input class="form-control" type="password" name="pass" placeholder="<?php echo mc_cleanDataEntVars($msg_login2); ?>" value="" autocomplete="off">
                <?php echo (isset($P_ERROR) ? '<span class="help-block error-block" style="text-align:left"><i class="fa fa-warning fa-fw"></i> ' . mc_safeHTML($P_ERROR) . '</span>' : ''); ?>
              </div>
              <?php
              // Is cookie set?
              if (ENABLE_LOGIN_COOKIE) {
              ?>
              <div class="form-group">
                <label><input type="checkbox" name="rm" value="1"> <?php echo $msg_login10; ?> <?php echo mc_displayHelpTip($msg_javascript402); ?></label>
              </div>
              <?php
              }
              ?>
              <input type="hidden" name="process" value="1">
              <button class="btn btn-lg btn-success btn-block" type="submit"><?php echo mc_cleanData($msg_login3); ?></button>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
    </form>

  </div>

  <script src="templates/js/jquery.js"></script>
  <script src="templates/js/bootstrap.js"></script>
  <script>
  //<![CDATA[
  function checkform() {
    var message = '';
    if (jQuery('input[name="user"]').val()=='') {
      message += '- <?php echo mc_filterJS($msg_login5); ?>\n';
    }
    if (jQuery('input[name="pass"]').val()=='') {
      message +='- <?php echo mc_filterJS($msg_login6); ?>\n';
    }
    if (message) {
      mc_alertBox(message);
      return false;
    }
  }
  //]]>
  </script>

  </body>

</html>
