<?php if (!defined('PATH')) { exit; } ?>
  <script src="content/js/jquery.js"></script>
  <script src="content/js/js.js"></script>
  <script src="content/js/bootstrap.js"></script>
  <?php
  if (isset($_GET['upgrade'])) {
  ?>
  <script>
  //<![CDATA[
  jQuery(document).ready(function() {
    _upgrading('start');
  });
  //]]>
  </script>
  <?php
  }
  ?>

</body>

</html>
