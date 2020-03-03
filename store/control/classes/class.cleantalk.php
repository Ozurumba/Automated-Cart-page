<?php

class cleanTalk extends jsonHandler {

  public $settings;
  public $social;

  private $api = array(
    'url' => 'https://moderate.cleantalk.org/api2.0'
  );

  public function check($data = array()) {

    $info = array(
      'user_agent' => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''),
      'referer' => (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '')
    );

    cleanTalk::log('Received Info: ' . print_r($info, true));

    $params = array(
      'method_name' => (isset($data['method']) ? $data['method'] : 'check_newuser'),
      'auth_key' => $this->social['ctalk']['key'],
      'sender_email' => $data['email'],
      'sender_nickname' => $data['name'],
      'agent' => 'php-1.1',
      'sender_ip' => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : (isset($data['ip']) ? $data['ip'] : '')),
      'js_on' => (isset($_POST['ct_ts']) && $_POST['ct_ts'] == date('Y') ? 1 : (isset($data['js']) ? $data['js'] : 0)),
      'submit_time' => (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_stime']) ? time() - (int) $_SESSION[mc_encrypt(SECRET_KEY) . '_stime'] : time()),
      'sender_info' => $this->encode($info)
    );

    if (isset($data['comms'])) {
      $params['message'] = $data['comms'];
    }

    $sendParams = $params;
    $sendParams['auth_key'] = 'NOT SHOWN FOR SECURITY';

    cleanTalk::log('Received Params: ' . print_r($sendParams, true));
    cleanTalk::log('Ping Url: ' . $this->api['url']);

    if (function_exists('curl_init')) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->api['url']);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encode($params));

      cleanTalk::log('Encoded Params: ' . $this->encode($params));

      // Receive server response ...
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      // Resolve 'Expect: 100-continue' issue
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Expect:'
      ));

      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

      $result = curl_exec($ch);

      curl_close($ch);

      cleanTalk::log('Result: ' . $result);

      return $this->decode($result);
    } else {
      cleanTalk::log('CURL is not enabled. Form data allowed as API was terminated. Please enable CURL functions on your server.');
      return array('allow' => 1);
    }
  }

  public function log($t) {
    if ($this->social['ctalk']['log'] == 'yes' && is_dir(GLOBAL_PATH . 'logs') && is_writeable(GLOBAL_PATH . 'logs') && function_exists('file_put_contents')) {
      $file = GLOBAL_PATH . 'logs/cleantalk-response-log.log';
      file_put_contents($file, $t . mc_defineNewline() . '- - - - - - - - - - - - - - - - - - - - - - - - -' . mc_defineNewline(), FILE_APPEND);
    }
  }

}

?>