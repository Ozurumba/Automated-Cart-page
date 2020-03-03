<?php
// Checks template is loading via system, so do not move..
if (!defined('PARENT')) {
  exit;
}

// SYSTEM OFFLINE TEMPLATE FILE
?>

<!DOCTYPE html>
<html lang="<?php echo $this->LANG; ?>" dir="<?php echo $this->DIR; ?>">
	<head>
    <meta charset="<?php echo $this->CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $this->TITLE; ?></title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <base href="<?php echo $this->BASE_PATH; ?>/">

    <link rel="stylesheet" href="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/css/style.css" type="text/css">

    <link rel="icon" href="<?php echo $this->BASE_PATH; ?>/favicon.ico">

  </head>

	<body>

  <div class="container maincontainer">
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-heading offlinehead">
            <i class="fa fa-minus-circle fa-fw"></i> <?php echo $this->TEXT[1]; ?>
          </div>
          <div class="panel-body">
            <?php echo $this->TEXT[0]; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  </body>

</html>