<?php

/* CALLBACK OPS - ACCOUNTS
----------------------------------*/

if (!defined('ACC_FLAG_YES')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

include(MCLANG . 'accounts.php');

// PROCESS..
if (mswIsValidEmail($SALE_ORDER->bill_2)) {

  //DOES AN ACCOUNT ALREADY EXIST FOR EMAIL?
  $usr = $MCACC->user(array(
    'email' => $SALE_ORDER->bill_2
  ));

  if (!isset($usr['id'])) {

    // LOG..
    $GATEWAY->writeLog($SALE_ID, 'Account doesn`t exist. Creating account for "' . $SALE_ORDER->bill_2 . '".');

    // PREPARE FORM DATA..
    $form  = array(
      'name' => $SALE_ORDER->bill_1,
      'email' => $SALE_ORDER->bill_2,
      'news' => 'yes',
      'bill' => array(
        'nm' => $SALE_ORDER->bill_1,
        'em' => $SALE_ORDER->bill_2,
        '1' => $SALE_ORDER->bill_9,
        '2' => $SALE_ORDER->bill_3,
        '3' => $SALE_ORDER->bill_4,
        '4' => $SALE_ORDER->bill_5,
        '5' => $SALE_ORDER->bill_6,
        '6' => $SALE_ORDER->bill_7
      ),
      'ship' => array(
        'nm' => $SALE_ORDER->ship_1,
        'em' => $SALE_ORDER->ship_2,
        '1' => $SALE_ORDER->shipSetCountry,
        '2' => $SALE_ORDER->ship_3,
        '3' => $SALE_ORDER->ship_4,
        '4' => $SALE_ORDER->ship_5,
        '5' => $SALE_ORDER->ship_6,
        '6' => $SALE_ORDER->ship_7,
        '7' => $SALE_ORDER->ship_8
      ),
      'pass' => substr(sha1($SALE_ORDER->bill_2 . time()), 3, ($SETTINGS->minPassValue > 0 ? $SETTINGS->minPassValue : '8')),
      'enabled' => 'yes',
      'verified' => 'yes'
    );

    // CREATE ACCOUNT..
    $code = $MCACC->create($form);

    // SEND EMAIL..
    if ($code[1] > 0) {

      $nOID = $code[1];
      $sbj  = str_replace('{website}', $SETTINGS->website, $msg_emails36);
      $msg  = MCLANG . 'email-templates/accounts/order-account-creation.txt';
      $MCMAIL->addTag('{NAME}', $SALE_ORDER->bill_1);
      $MCMAIL->addTag('{PASS}', $form['pass']);
      $MCMAIL->addTag('{EMAIL}', $SALE_ORDER->bill_2);
      $MCMAIL->sendMail(array(
        'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
        'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
        'to_email' => $SALE_ORDER->bill_2,
        'to_name' => $SALE_ORDER->bill_1,
        'subject' => $sbj,
        'replyto' => array(
          'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
        ),
        'template' => $msg,
        'language' => $SETTINGS->languagePref
      ));
      $MCMAIL->smtpClose();

      // LOG..
      $GATEWAY->writeLog($SALE_ID, 'Account successfully created and activated for "' . $SALE_ORDER->bill_1 . '/' . $SALE_ORDER->bill_2 . '".');

      // UPDATE EXISTING SALES SO THEY ARE VISIBLE FOR EMAIL..
      $up = $MCACC->saleAcc($SALE_ORDER->bill_2, $code[1]);
      if ($up > 0) {
        $GATEWAY->writeLog($SALE_ID, @number_format($up) . ' previous sales updated to account ID ' . $code[1] . ' where email is "' . $SALE_ORDER->bill_2 . '".');
      }

    } else {

      // LOG..
      $GATEWAY->writeLog($SALE_ID, 'Database error occurred when creating account for "' . $SALE_ORDER->bill_2 . '". Check database logs. Maybe connection drop?');

    }

  } else {

    // LOG..
    $nOID = $usr['id'];
    $GATEWAY->writeLog($SALE_ID, 'Account already exists for "' . $SALE_ORDER->bill_2 . '". Account creation terminated.');

  }

  // Update sale with account ID..
  if (isset($nOID)) {
    $GATEWAY->writeLog($SALE_ID, 'Sale will be updated with account id: ' . $nOID);
  }

} else {

  // LOG..
  $GATEWAY->writeLog($SALE_ID, 'Email address "' . $SALE_ORDER->bill_2 . '" has failed validation. Account creation terminated.');

}

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Account creation operations completed');


?>