<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Restriction Message..
if (isset($_GET['restriction'])) {

?>
<div id="content">

  <div class="panel panel-danger">
    <div class="panel-heading">
      <i class="fa fa-warning fa-fw"></i> FREE VERSION RESTRICTION
    </div>
    <div class="panel-body">
      You have reached a free version restriction limit. A few restrictions are imposed in the free version of this system, which are detailed <a href="https://www.maiancart.com/features.html" onclick="window.open(this);return false">here</a>.<br><br>
      If you would like to remove these restrictions, a commercial licence is required. Here are the main benefits of supporting this software:<br><br>

      <i class="fa fa-check fa-fw"></i> <b>FREE upgrades for life. You only pay once regardless of features in future versions.</b><br><br>

      <i class="fa fa-check fa-fw"></i> <b>FREE 3 months <a href="https://www.maiancart.com/free-support.html" onclick="window.open(this);return false">personal support</a>.</b><br><br>

      <i class="fa fa-check fa-fw"></i> <b>All features unlocked &amp; unlimited.</b><br><br>

      <i class="fa fa-check fa-fw"></i> <b>Commercial upgrade also includes copyright removal.</b><br><br>

      <i class="fa fa-check fa-fw"></i> <b>Notifications when new versions are released.</b><br><br>

      <i class="fa fa-check fa-fw"></i> <b>No subscriptions or recurring billing.</b><br><br>

      If this doesn`t interest you, you may continue to use this free version as long as you like. This message will keep appearing should you exceed any restrictions.<br><br>
      To buy a commercial licence, click the button below. Thank you.
    </div>
    <div class="panel-footer">
      <button class="btn btn-primary" onclick="window.location='https://www.maiancart.com/purchase.html'"><i class="fa fa-shopping-cart fa-fw"></i> Buy Commercial Licence</button>
    </div>
  </div>

</div>
<?php
}

?>
