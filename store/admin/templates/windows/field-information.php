<?php if (!defined('PARENT') || !isset($_GET['field'])) { die('Permission Denied'); }
?>
<div>

<?php
foreach (($_GET['field'] == 'products' ? $fieldMapping_products : ($_GET['field'] == 'account' ? $fieldMapping_accounts : $fieldMapping_vars)) AS $k => $v) {
?>
<div class="panel panel-default">
  <div class="panel-heading">
    <?php
    echo $v;
    ?>
  </div>
  <div class="panel-body">
    <?php
    switch($k) {
      // Products..
      case 'pName':             echo $msg_import28;  break;
      case 'pDescription':      echo $msg_import29;  break;
      case 'pShortDescription': echo $msg_import54;  break;
      case 'pMetaKeys':         echo $msg_import30;  break;
      case 'pMetaDesc':         echo $msg_import31;  break;
      case 'pTags':             echo $msg_import32;  break;
      case 'rwslug':            echo $msg_import57;  break;
      case 'pDownload':         echo $msg_import33;  break;
      case 'pDownloadPath':     echo $msg_import34;  break;
      case 'pDownloadLimit':    echo $msg_import35;  break;
      case 'pVideo':            echo $msg_import36;  break;
      case 'pVideo2':           echo $msg_admin_import3_0[0];  break;
      case 'pVideo3':           echo $msg_admin_import3_0[1];  break;
      case 'pStock':            echo $msg_import37;  break;
      case 'minPurchaseQty':    echo $msg_import58;  break;
      case 'maxPurchaseQty':    echo $msg_import59;  break;
      case 'pStockNotify':      echo $msg_import38;  break;
      case 'pVisits':           echo $msg_import39;  break;
      case 'pCode':             echo $msg_import40;  break;
      case 'pWeight':           echo $msg_import41;  break;
      case 'pPrice':            echo $msg_import42;  break;
      case 'pOffer':            echo $msg_import43;  break;
      case 'pOfferExpiry':      echo $msg_import44;  break;
      case 'pPurchase':         echo $msg_import56;  break;
      // Attributes..
      case 'attrName':          echo $msg_import47;  break;
      case 'attrCost':          echo $msg_import49;  break;
      case 'attrStock':         echo $msg_import48;  break;
      case 'attrWeight':        echo $msg_import50;  break;
      // Accounts..
      case 'name':              echo $msg_accimport8; break;
      case 'email':             echo $msg_accimport9; break;
      case 'pass':              echo $msg_accimport10; break;
    }
    ?>
  </div>
</div>
<?php
}
?>

</div>