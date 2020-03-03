<?php

class affiliate {

  public $cfg;
  public $dt;
  public $json;
  
  public function ping($data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->cfg['affiliate']['url']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->json->encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Connection: Close',
      'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_CAINFO, PATH . 'control/gateways/certs/cacert.pem');
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $r = $this->json->decode(curl_exec($ch), true);
    curl_close($ch);
    return $r;
  }
  
  public function getcookie() {
    return (isset($_COOKIE[mc_encrypt(SECRET_KEY . MSW_AFF_SYS_KEY)]) ? $_COOKIE[mc_encrypt(SECRET_KEY . MSW_AFF_SYS_KEY)] : '');
  }
  
  public function setcookie() {
    setcookie(
      mc_encrypt(SECRET_KEY . MSW_AFF_SYS_KEY),
      $_GET[$this->cfg['affiliate']['param']],
      time() + 60 * 60 * 24 * MSW_AFF_SYS_DUR
    );
  }
  
  public function log($log) {
    if (isset($this->cfg['affiliate']['logs']) && $this->cfg['affiliate']['logs'] == 'yes' && $log) {
      $str  = 'Date/Time: ' . date('j F Y @ H:iA', time()) . mc_defineNewline();
      $str .= str_repeat('-', 50) . mc_defineNewline() . mc_defineNewline();
      $str .= $log . mc_defineNewline() . mc_defineNewline();
      file_put_contents(GLOBAL_PATH . 'logs/' . MSW_AFF_SYS_LOG_FILE, $str, FILE_APPEND);
    }
  }

}

?>