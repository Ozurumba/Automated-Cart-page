<?php if (!defined('PARENT') || !isset($_GET['shipLabel'])) { die('Permission Denied'); }
$SALE = mc_getTableData('sales', 'id', mc_digitSan($_GET['shipLabel']));
if (isset($SALE->id)) {
?>

<div class="container" id="mscontainer" style="margin-top:20px">

    <div class="row">

      <div id="content">

        <div class="panel panel-default">
          <?php
          if ($SETTINGS->cReturns) {
          ?>
          <div class="panel-heading shippinghead">
            <?php echo mc_NL2BR(mc_safeHTML($SETTINGS->cReturns)); ?>
          </div>
          <?php
          }
          ?>
          <div class="panel-body shippingaddress">
            <p><i class="fa fa-truck fa-fw"></i></p>
            <?php
            foreach (array('ship_1','ship_3','ship_4','ship_5','ship_6','ship_7') AS $f) {
              if ($f) {
                if ($f=='ship_7') {
                  echo '<div>' . strtoupper(mc_safeHTML($SALE->{$f})) . '</div>';
                } else {
                  echo '<div>' . ucwords(strtolower(mc_safeHTML($SALE->{$f}))) . '</div>';
                }
              }
            }
            ?>
            <div class="country"><?php echo mc_getShippingCountry($SALE->shipSetCountry); ?></div>
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
</div>

<?php
} else {
?>
<span class="nodata">Sale does not exist</span>
<?php
}
?>