<?php

class mcBlock {

  public $settings;

  public function lock($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `downloadLock` = 'yes'
    WHERE `id`     = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function increment($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `restrictCount` = (`restrictCount`+1)
    WHERE `id`      = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function log($order, $allowed, $blocked) {
    $message = 'RESTRICTED ACCESS BLOCK @ ' . date('.j F Y H:i:s.') . mc_defineNewline();
    $message .= 'Invoice ID: ' . mc_saleInvoiceNumber($order->invoiceNo, $this->settings) . mc_defineNewline();
    $message .= 'Blocked IP(s): ' . implode(', ', $blocked) . mc_defineNewline();
    $message .= 'Allowed IP(s): ' . implode(', ', $allowed) . mc_defineNewline();
    $message .= '= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =' . mc_defineNewline();
    // Attempt to create log directory if it doesn`t exist..
    if (!is_dir(PATH . $this->settings->logFolderName)) {
      $oldumask = @umask(0);
      @mkdir(PATH . $this->settings->logFolderName, 0777);
      @umask($oldumask);
    }
    if (is_dir(PATH . $this->settings->logFolderName) && is_writeable(PATH . $this->settings->logFolderName)) {
      @file_put_contents(PATH . $this->settings->logFolderName . '/restricted-ip-log-S' . mc_saleInvoiceNumber($order->invoiceNo, $this->settings) . '.txt', $message, FILE_APPEND);
    }
  }

}

?>