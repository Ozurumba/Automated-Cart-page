<?php if (!defined('PARENT')) { die('Permission Denied'); }
$q_phys = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,`" . DB_PREFIX . "purchases`.`id` AS `pcid`
          FROM `" . DB_PREFIX . "purchases`
          LEFT JOIN `" . DB_PREFIX . "products`
          ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
          WHERE `saleID`                      = '".mc_digitSan($_GET['print-personalisation'])."'
          AND `productType`                   = 'physical'
          ORDER BY `" . DB_PREFIX . "purchases`.`id`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
// Sale..
$SALE = mc_getTableData('sales','id',
         mc_digitSan($_GET['print-personalisation']),
         '',
         '*,DATE_FORMAT(`purchaseDate`,\''.$SETTINGS->mysqlDateFormat.'\') AS `pdate`'
        );
define('WINPARENT', 1);
include(PATH . 'templates/windows/header.php');
?>

	<body>

  <div class="container" id="mscontainer" style="margin-top:20px">

    <div class="row">

      <div id="content">

        <?php
        while ($PHYS = mysqli_fetch_object($q_phys)) {
          $code         = ($PHYS->pCode ? $PHYS->pCode : 'N/A');
          $weight       = ($PHYS->pWeight ? $PHYS->pWeight : 'N/A');
          $PHYS->pName  = ($PHYS->pName ? $PHYS->pName : $PHYS->deletedProductName);
          $isDel        = ($PHYS->deletedProductName ? '<span class="deletedItem">'.$msg_script53.'</span>' : '');

        $q_ps1 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "personalisation`
                 WHERE `productID` = '{$PHYS->pid}'
                 AND `enabled`     = 'yes'
                 ORDER BY `id`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
        if (mysqli_num_rows($q_ps1)>0) {
        ?>
        <div class="alert alert-info">
         <i class="fa fa-cube fa-fw"></i> <?php echo mc_safeHTML($PHYS->pName); ?> - <?php echo $code; ?>
        </div>

        <?php
        while ($PS = mysqli_fetch_object($q_ps1)) {
        $q_ps = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_pers`
                WHERE `purchaseID`       = '{$PHYS->pcid}'
                AND `saleID`             = '".mc_digitSan($_GET['print-personalisation'])."'
                AND `productID`          = '{$PHYS->pid}'
                AND `personalisationID`  = '{$PS->id}'
                ORDER BY `id`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
        $PERS_ITEM = mysqli_fetch_object($q_ps);
        if (isset($PERS_ITEM->visitorData) && $PERS_ITEM->visitorData!='' && $PERS_ITEM->visitorData != 'no-option-selected') {
        ?>
        <div class="panel panel-default">
          <div class="panel-body">
            <b><?php echo mc_persTextDisplay(mc_safeHTML($PS->persInstructions),true); ?></b>
            <hr>
            <?php echo mc_NL2BR(mc_safeHTML($PERS_ITEM->visitorData)); ?>
          </div>
        </div>
        <?php
        }
        }
        }
        }
        ?>
        <div class="alert alert-success">
          <?php echo str_replace(array('{date}','{buyer}','{order_no}'),array($SALE->pdate,mc_safeHTML($SALE->bill_1),mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS)),$msg_viewsale63); ?>
        </div>

      </div>

    </div>
  </div>

  <script>
  //<![CDATA[
  jQuery(document).ready(function() {
    window.print();
  });
  //]]>
  </script>

<?php
include(PATH . 'templates/windows/footer.php');
?>
