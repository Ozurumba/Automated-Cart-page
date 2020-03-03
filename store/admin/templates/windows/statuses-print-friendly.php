<?php if (!defined('PARENT')) { die('Permission Denied'); }
$q_stat = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`dateAdded`,'" . $SETTINGS->mysqlDateFormat . "') AS `adate`
          FROM `" . DB_PREFIX . "statuses`
          WHERE `saleID` = '".mc_digitSan($_GET['print'])."'
          ORDER BY `id` DESC
          ") or die(mc_MySQLError(__LINE__,__FILE__));
define('WINPARENT', 1);
include(PATH . 'templates/windows/header.php');
?>

<body>

<div class="container" id="mscontainer" style="margin-top:20px">

    <div class="row">

      <div id="content">

      <?php
      if (mysqli_num_rows($q_stat)>0) {
        while ($STATUS = mysqli_fetch_object($q_stat)) {
        ?>
        <div class="panel panel-default">
          <div class="panel-body">
            <?php echo mc_NL2BR(mc_cleanCustomTags(str_replace('&lt;br&gt;','<br>',mc_safeHTML($STATUS->statusNotes)), $mc_mailHTMLTags)); ?>
          </div>
          <div class="panel-footer">
            <?php echo mc_statusText($STATUS->orderStatus); ?> <i class="fa fa-clock-o fa-fw"></i> <?php echo $STATUS->adate; ?> @ <?php echo $STATUS->timeAdded; ?>
          </div>
        </div>
        <?php
        }
      } else {
      ?>
      <p class="noData"><?php echo $msg_salesupdate15; ?></p>
      <?php
      }
      ?>
      </div>

  </div>

  <script>
  //<![CDATA[
  jQuery(document).ready(function() {
    window.print();
  });
  //]]>
  </script>

</div>

<?php
include(PATH . 'templates/windows/footer.php');
?>