<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}

// CHECKOUT RESPONSE TEMPLATE FILE
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
  <meta http-equiv="refresh" content="<?php echo $this->META[1]; ?>;url=<?php echo $this->META[0]; ?>">
  <link rel="icon" href="<?php echo $this->BASE_PATH; ?>/favicon.ico">
</head>

<body>

    <div class="container" style="margin-top:50px">
        <div class="row">
            <div class="col-lg-12">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                      <h3 class="panel-title"><i class="fa fa-shopping-basket fa-fw"></i> <?php echo mc_safeHTML($this->SETTINGS['website']); ?></h3>
                    </div>
                    <div class="panel-body">
					            <?php echo $this->TXT[0]; ?><br><br>
                      <img src="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/images/doing-something.gif" alt=""><br><br>
					            <?php echo $this->TXT[1]; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
