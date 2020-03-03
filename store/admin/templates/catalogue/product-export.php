<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($return) && $return == 'none') {
?>
<div class="alert alert-danger alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <i class="fa fa-warning fa-fw"></i> <?php echo $msg_productexport3; ?>
</div>
<?php
}
?>

<form method="post" id="form" action="?p=product-export">
<div class="row">
  <div class="col-lg-6 col-md-6">
    <div class="fieldHeadWrapper">
      <p><?php echo $msg_productexport4; ?>:</p>
    </div>

    <div class="formFieldWrapper">
      <div class="formLeft">
        <div class="categoryBoxes" id="cats">
        <input type="checkbox" name="log" tabindex="<?php echo (++$tabIndex); ?>" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'cats')" checked="checked"> <b><?php echo $msg_productadd35; ?></b><br>
        <?php
        $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '1'
                  AND `childOf`    = '0'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
        while ($CATS = mysqli_fetch_object($q_cats)) {
        ?>
        <p id="cat_<?php echo $CATS->id; ?>"><input onclick="if(this.checked){mc_selectChildren('cat_<?php echo $CATS->id; ?>','on')}else{mc_selectChildren('cat_<?php echo $CATS->id; ?>','off')}" tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="category[]" value="<?php echo $CATS->id; ?>" checked="checked"> <?php echo mc_safeHTML($CATS->catname); ?><br>
        <?php
        $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
                      WHERE `catLevel` = '2'
                      AND `enCat`      = 'yes'
                      AND `childOf`    = '{$CATS->id}'
                      ORDER BY `catname`
                      ") or die(mc_MySQLError(__LINE__,__FILE__));
        while ($CHILDREN = mysqli_fetch_object($q_children)) {
        ?>
        <span id="child_<?php echo $CHILDREN->id; ?>">
        &nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" onclick="if(this.checked){mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','on')}else{mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','off')}" type="checkbox" name="category[]" value="<?php echo $CHILDREN->id; ?>" checked="checked"> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
        <?php
        $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
                      WHERE `catLevel` = '3'
                      AND `childOf`    = '{$CHILDREN->id}'
                      AND `enCat`      = 'yes'
                      ORDER BY `catname`
                      ") or die(mc_MySQLError(__LINE__,__FILE__));
        while ($INFANTS = mysqli_fetch_object($q_infants)) {
        ?>
        &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="category[]" value="<?php echo $INFANTS->id; ?>" checked="checked"> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
        <?php
        }
        ?>
        </span>
        <?php
        }
        ?>
        </p>
        <?php
        }
        ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-6">
    <div class="fieldHeadWrapper">
      <p><?php echo $msg_productmanage60; ?>:</p>
    </div>

    <div class="formFieldWrapper">
      <div class="formLeft">
       <div class="categoryBoxes" id="fields">
        <input type="checkbox" name="log" tabindex="<?php echo (++$tabIndex); ?>" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'fields')" checked="checked"> <b><?php echo $msg_productmanage61; ?></b><br>
        <?php
        // Fields to show..
        $exFlds              = array(
         'pName'             => $msg_productadd4,
         'pTitle'            => $msg_productadd75,
         'pBrands'           => $msg_productadd32,
         'pMetaKeys'         => $msg_productadd18,
         'pMetaDesc'         => $msg_productadd19,
         'pTags'             => $msg_productadd11,
         'rwslug'            => $msg_newpages31,
         'pDescription'      => $msg_productadd6,
         'pShortDescription' => $msg_productadd64,
         'pCode'             => $msg_productadd7,
         'pStock'            => $msg_productadd43,
         'minPurchaseQty'    => $msg_productadd79,
         'maxPurchaseQty'    => $msg_productadd88,
         'expiry'            => $msg_admin_product3_0[0],
         'pWeight'           => $msg_productadd42,
         'pPrice'            => $msg_productadd44,
         'pPurPrice'         => $msg_admin_product3_0[15],
         //'pInsurance'        => $msg_admin_product3_0[9],
         'pOffer'            => $msg_productadd39,
         'pOfferExpiry'      => $msg_productadd40,
         'pDownloadPath'     => $msg_productadd9,
         'pVisits'           => $msg_productadd84,
         'pVideo'            => $msg_admin_product3_0[6],
         'pVideo2'           => $msg_admin_product3_0[7],
         'pVideo3'           => $msg_admin_product3_0[8],
         'dropshipping'      => $msg_admin_product3_0[2],
         'pDateAdded'        => $msg_admin_product3_0[10],
         'pCube'             => $msg_admin_product3_0[11],
         'pGuardian'         => $msg_admin_product3_0[17]
        );
        if (mc_rowCount('dropshippers') == 0) {
          unset($exFlds['dropshipping']);
        }
        if ($SETTINGS->cubeUrl == '' || $SETTINGS->cubeAPI == '') {
          unset($exFlds['pCube']);
        }
        if ($SETTINGS->guardianUrl == '' || $SETTINGS->guardianAPI == '') {
          unset($exFlds['pGuardian']);
        }
        foreach ($exFlds AS $k => $v) {
        ?>
        <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="fields[]" value="<?php echo $k; ?>" checked="checked"> <?php echo mc_safeHTML($v); ?><br>
        <?php
        }
        ?>
       </div>
      </div>
      <br class="clear">
    </div>
  </div>
</div>

<p style="text-align:center;padding:20px 0 30px 0">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_productexport2); ?>" title="<?php echo mc_cleanDataEntVars($msg_productexport2); ?>">
</p>
</form>

</div>
