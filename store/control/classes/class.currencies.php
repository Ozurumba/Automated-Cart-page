<?php

class curConverter {

  public $settings;
  public $endpoints = array(
    'http' => 'http://api.fixer.io/latest?base={BASE}',
    'https' => 'https://api.fixer.io/latest?base={BASE}'
  );
  private $logErrs = 'no';

  // Load Currency Rates..
  public function getCurrencyRates() {
    $rates = array();
    $rs = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `currency`,`rate`
          FROM `" . DB_PREFIX . "currencies`
          ORDER BY `currency`
          ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($RATES = mysqli_fetch_object($rs)) {
      $rates[$RATES->currency] = $RATES->rate;
    }
    return $rates;
  }

  // Perform the actual conversion..
  public function convert($amount, $currency, $rate) {
    if ($currency == $this->settings->baseCurrency) {
      return mc_formatPrice($amount);
    }
    return mc_formatPrice($amount * $rate);
  }

  // Get data from Fixer.io JSON API
  public function downloadExchangeRates() {
    $useEndPoint = (mc_detectSSLConnection($this->settings) == 'yes' ? $this->endpoints['https'] : $this->endpoints['http']);
    $useEndPoint = str_replace('{BASE}', $this->settings->baseCurrency, $useEndPoint);
    // Ping JSON API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $useEndPoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $json = json_decode(curl_exec($ch), true);
    curl_close ($ch);
    // Update..
    if (!empty($json['rates'])) {
      $q = @mysqli_query($GLOBALS["___msw_sqli"], "SELECT `currency` FROM `" . DB_PREFIX . "currencies` WHERE `enableCur` = 'yes' ORDER BY `currency`");
      while ($CUR = @mysqli_fetch_object($q)) {
        if (isset($json['rates'][$CUR->currency])) {
          @mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "currencies` SET
          `rate`            = '" . mc_safeSQL(@number_format($json['rates'][$CUR->currency], 2, '.', '')) . "'
          WHERE `currency`  = '{$CUR->currency}'
          ");
        }
      }
    } else {
      if ($this->logErrs == 'yes') {
        file_put_contents(GLOBAL_PATH . 'logs/currency_converter_errors.log', 'No rates could be retrieved @ ' . date('j F Y : H:iA') . PHP_EOL, FILE_APPEND);
      }
    }
    // Make sure no rates are less than 0..
    @mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "currencies` SET
    `rate`      = '0'
    WHERE `rate` < 0
    ");
  }

  // Converts iso code to name..
  public function getCurrencyName($isoCode) {
    global $currencyConversion;
    return (isset($currencyConversion[$isoCode]) ? $currencyConversion[$isoCode] : '');
  }

}

?>