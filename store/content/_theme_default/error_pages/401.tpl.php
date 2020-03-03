<!DOCTYPE html>
<html lang="<?php echo $this->LANG; ?>" dir="<?php echo $this->DIR; ?>">
	<head>
    <meta charset="<?php echo $this->CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>401 - <?php echo $this->TEXT['401']; ?></title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <base href="<?php echo $this->BASE_PATH; ?>/">

    <link rel="stylesheet" href="<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/css/style.css" type="text/css">

    <link rel="icon" href="<?php echo $this->BASE_PATH; ?>/favicon.ico">

    <style>
    body {
      background: url(<?php echo $this->BASE_PATH . '/' . $this->THEME_FOLDER; ?>/images/error-bg.png);
    }
    .error-template {
      padding: 40px 15px;
      text-align: center;
    }
    .error-actions {
      margin-top:30px;
      margin-bottom:15px;
    }
    .error-actions .btn {
      margin-right:10px;
    }
    h1 {
      font-size: 25px;
      font-weight: bold;
      margin-bottom: 30px;
    }
    </style>
	</head>

	<body>

  <div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="error-template">
                <h1>401 - <?php echo $this->TEXT['401']; ?></h1>

                <div class="error-details">
                  <?php echo $this->TEXT['msg'][0]; ?>
                </div>
                <div class="error-actions">
                  <a href="<?php echo $this->BASE_PATH; ?>" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left fa-fw"></i> <span class="hidden-xs"><?php echo str_replace('{website}',mc_safeHTML($this->SETTINGS['website']),$this->TEXT['msg'][1]); ?></span></a>
                  <a href="mailto:<?php echo $this->SETTINGS['email']; ?>" class="btn btn-default btn-sm"><i class="fa fa-envelope fa-fw"></i> <?php echo $this->TEXT['msg'][2]; ?></a>
                </div>
            </div>
        </div>
    </div>
  </div>

  </body>

</html>