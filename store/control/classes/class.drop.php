<?php

class dropShipper {

  public $settings;
  public $sale;
  public $purprod;
  public $lang;
  public $restrictions;

  private $inclusions = array(
    'phone' => 'no',
    'email' => 'no'
  );

  public function shiporder() {
    $o   = array('------------------------------------------' . mc_defineNewline());
    $p   = array();
    $o[] = ($this->purprod->pName ? '(' . $this->purprod->pCode . ') ' : '') . $this->purprod->pName;
    $a   = mc_saleAttributes(
      $this->sale->id,
      $this->purprod->prid,
      $this->purprod->pid,
      false,
      0,
      true
    );
    if (!empty($a)) {
      $o[] = implode(mc_defineNewline(), $a);
    }
    $q_ps = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_pers`
            WHERE `purchaseID` = '{$this->purprod->prid}'
            ORDER BY `id`
            ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($PS = mysqli_fetch_object($q_ps)) {
      $PLD = mc_getTableData('personalisation', 'id', $PS->personalisationID);
      $p[] = mc_persTextDisplay(mc_cleanData($PLD->persInstructions),true).': ' . mc_cleanData($PS->visitorData);
    }
    if (!empty($p)) {
      $o[] = mc_defineNewline() . $this->lang[0] . mc_defineNewline() . mc_defineNewline() . implode(mc_defineNewline(), $p);
    }
    return implode(mc_defineNewline(), $o);
  }

  public function shipaddress() {
    $ship = array();
    $arr  = array(1,3,4,5,6,7,'shipSetCountry');
    if ($this->inclusions['phone'] == 'yes') {
      $arr[] = 8;
    }
    if ($this->inclusions['email'] == 'yes') {
      $arr[] = 2;
    }
    foreach ($arr AS $i) {
      $s = 'ship_' . $i;
      if (isset($this->sale->$s) && trim($this->sale->$s)) {
        $ship[] = mc_safeHTML($this->sale->$s);
      } else {
        if ($i == 'shipSetCountry') {
          $ship[] = mc_getShippingCountry($this->sale->shipSetCountry);
        }
      }
    }
    return (!empty($ship) ? implode(mc_defineNewline(), $ship) : 'N/A');
  }

  public function shipmethod() {
    return mc_getShippingService(mc_getShippingServiceFromRate($this->sale->setShipRateID));
  }

}

?>
