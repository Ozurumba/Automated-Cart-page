<?php

/*
  CUSTOM OPERATIONS (ADVANCED USERS ONLY)
  -----------------------------------------------------------------------------------------------

  Code here is executed ONLY after a successful gateway callback.

  You can use any sale parameters via the $SALE_ORDER object. (See callback-completed.php)

  You can also query the DB again based on the following:

  $SALE_ID   = ID
  $SALE_CODE = buyCode

  Emails can be sent with the mail class. Example:

  $MCMAIL->sendMail(array(
    'from_email' => 'from email here..',
    'from_name' => 'from name here..',
    'to_email' => 'to email here..',
    'to_name' => 'to name here..',
    'subject' => 'email subject here..',
    'replyto' => array(
      'name' => 'reply to name here..',
      'email' => 'reply to email here..'
    ),
    'template' => 'message body here..',
    'language' => 'english'
  ));
  $MCMAIL->smtpClose();

  NOTE: The 'control/classes/mailer/global-mail-tags.php' file must be loaded as below.

-------------------------------------------------------------------------------------------------*/

// Check parent loader..important..
if (!defined('PARENT')) {
  include(PATH.'control/system/headers/403.php');
  exit;
}

// MAIL..
if (!defined('MAIL_SWITCH')) {
  include(PATH . 'control/classes/mailer/global-mail-tags.php');
}

// Custom mail stuff here..
if (!in_array($SALE_ORDER->paymentMethod, array('bank','cod','cheque','phone'))) {
  // Payment completed via none gateway payment method, do something..
} else {
  // Payment completed via gateway, do something..
}

?>