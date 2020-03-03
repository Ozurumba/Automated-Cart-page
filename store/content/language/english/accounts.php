<?php

//------------------------------------------------------------------------------
// LANGUAGE FILE
// Edit with care. Make a backup first before making changes.
//
// [1] Apostrophes should be escaped. eg: it\'s christmas.
// [2] Take care when editing arrays as they are spread across multiple lines
//
// If you make a mistake and you see a parse error, revert to backup file
//------------------------------------------------------------------------------


$public_accounts  = array(
  'Account Login',
  'My Account',
  'Please access your account below.',
  'Enter Account Email Address..',
  'Enter Account Password..',
  'Login',
  'Forgot Password?',
  'Account Menu',
  'Dashboard',
  'Profile',
  'Order History',
  'Wish List',
  'Saved Searches',
  'Create Account',
  'Logout',
  'Close Account',
  'Account Type',
  'Personal',
  'Trade',
  'Global Trade Discount',
  'Min Purchase Qty',
  'Max Purchase Qty',
  '{count} per product',
  '{percent}% on all products',
  'N/A',
  'Min Checkout Amount'
);

$public_accounts_dashboard = array(
  'Hi <b>{name}</b>,<br><br>Welcome to your account area. Use the links to manage your account. Your latest <b>{count}</b> orders are shown below.',
  'Personal Store Message'
);

$public_accounts_profile = array(
  'Please update your profile below.',
  'General',
  'Billing Address',
  'Shipping Address',
  'Password',
  'Update',
  'Your Name',
  'Your Email Address',
  'Enter Existing Password',
  'Enter New Password',
  'Retype New Password',
  'Receive Newsletter',
  'Country',
  'Address Line 1',
  'Address Line 2 (Optional)',
  'Town / Area',
  'County / State',
  'Post / Zip Code',
  'Phone Number',
  'Copy from Billing',
  'Copy from Shipping',
  'Billing Name',
  'Billing Email Address',
  'Shipping Name',
  'Shipping Email Address',
  'Close Account',
  'Please update your profile below.<br><br><b>Important</b>: If you are utilising your wish list and have deliverable items, make sure your profile shipping address is valid.',
  'Wish List Shipping Zone',
  'No zones available, please contact us'
);

$public_accounts_create = array(
  'Please create a new account below. All fields are required unless noted.',
  'General',
  'Billing Address',
  'Shipping Address',
  'Password',
  'Update',
  'Your Name',
  'Your Email Address',
  'Enter Existing Password',
  'Enter New Password',
  'Retype New Password',
  'Receive Newsletter <span class="bold">(Optional)</span>',
  'Country',
  'Address Line 1',
  'Address Line 2 (Optional)',
  'Town / Area',
  'County / State',
  'Post / Zip Code',
  'Phone Number',
  'Copy from Billing',
  'Copy from Shipping',
  'Min <b>{chars}</b> characters.',
  'Min <b>{chars}</b> characters. Must include at least <b>1</b> uppercase &amp; <b>1</b> lowercase letter, <b>1</b> number and <b>1</b> special character. eg: []#*!%()',
  'Billing Name',
  'Billing Email Address',
  'Shipping Name',
  'Shipping Email Address'
);

$public_accounts_validation = array(
  '<i class="fa fa-exclamation fa-fw"></i> Name &amp; valid email are required',
  '<i class="fa fa-exclamation fa-fw"></i> Billing fields are required',
  '<i class="fa fa-exclamation fa-fw"></i> Shipping fields are required',
  '<i class="fa fa-exclamation fa-fw"></i> Password is required',
  '<i class="fa fa-exclamation fa-fw"></i> Passwords do not match, please try again',
  '<i class="fa fa-exclamation fa-fw"></i> Invalid password, see the note below the password box',
  '<i class="fa fa-exclamation fa-fw"></i> Invalid password length, see the note below the password box',
  '<i class="fa fa-exclamation fa-fw"></i> An account already exists with this email address, please try again',
  '<i class="fa fa-check fa-fw"></i> Thank You',
  '<span class="badge">1</span> Please check your inbox at <b>{email}</b><br><br><span class="badge">2</span> Follow the instructions to activate your account',
  '<i class="fa fa-exclamation fa-fw"></i> A valid email and password are required',
  '<i class="fa fa-exclamation fa-fw"></i> No active account found, please check your details',
  '<i class="fa fa-exclamation fa-fw"></i> Current password entered does not match our records, please try again',
  'Your profile was successfully updated',
  '<i class="fa fa-exclamation fa-fw"></i> Please enter account email address',
  '<span class="badge">1</span> Please check your inbox at <b>{email}</b><br><br><span class="badge">2</span> Follow the instructions to reset your password',
  '<i class="fa fa-exclamation fa-fw"></i> Billing email address is invalid, please try again',
  '<i class="fa fa-exclamation fa-fw"></i> Shipping email address is invalid, please try again',
  '<i class="fa fa-exclamation fa-fw"></i> Please choose your preferred wish list shipping zone',
  '<i class="fa fa-exclamation fa-fw"></i> Your IP is not permitted in our system'
);

$public_accounts_history = array(
  'Your order history is shown below. If enabled, wish list purchases will also be shown below.',
  'There are no orders to display',
  'Unknown',
  'Invoice',
  'Sale Date',
  'Order Status',
  'Total',
  'N/A - WishList'
);

$public_accounts_wish = array(
  'Your wish list is shown below. Use the following public url to share your wish list with friends.<br><br><div class="wishcode">{url} <a href="{hurl}" onclick="mc_Window(this.href, 350, 600, \'\');return false"><i class="fa fa-info-circle fa-fw"></i></a> <a href="{url}" onclick="window.open(this);return false"><i class="fa fa-external-link fa-fw"></i></a></div>',
  'HTML Code (Websites)',
  'BB Code (Forums)',
  '<a href="{url}">{text}</a>',
  '[url={url}]{text}[/url]',
  'My Wish List @ {website}',
  'There are no wish list products to display',
  'Product',
  'Saved',
  'Delete',
  'Are you sure?',
  'Wish list entry successfully deleted',
  'Edit Wish List Intro Text',
  'Enter Intro Text (Appears at top of wish list if set. HTML is not allowed)',
  'Update &amp; Close'
);

$public_accounts_wish_public = array(
  'Filter by Category',
  'Sort by',
  'Title: A - Z',
  'Title: Z - A',
  'Price: Low - High',
  'Price: High - Low',
  'Date: Newest',
  'Date: Oldest',
  'Low Stock',
  'Wish List ({name})',
  'Wish List',
  'All Categories',
  'All Products',
  'Welcome to my wish list. Please use the links below to purchase items from this list, thank you.',
  'Edit This List'
);

$public_accounts_forgot = array(
  'Get New Password',
  'Continue',
  'Please use the form below to reset your account password',
  'Enter New Password',
  'Retype Password',
  'Thank you, please choose your new password below',
  'Update &amp; Reset Password',
  'Your password was successfully updated.<br><br>Please <a href="{url}">login</a> to your account.'
);

$public_accounts_saved = array(
  'Your saved searches are shown below.',
  'Your saved searches are shown below. Saved searches are kept for <b>{days}</b> days only.',
  'Name',
  'Save Date',
  'Delete',
  'There are no saved searches to display',
  'Are you sure?',
  'Saved search successfully deleted'
);

$public_accounts_view_order = array(
  'View Order',
  'Your order is show below. If you have questions about this order, use the contact option below.',
  'Shipped Goods',
  'Downloads',
  'Gift Certificates',
  'View PDF Invoice',
  'Back to Order History',
  'Billed To',
  'Shipped To (if applicable)',
  '<b>E</b>: ',
  '<b>T</b>: ',
  'Customer Notes',
  'This order has not been completed. At this time product downloads (if applicable) are not available. If this order is awaiting payment, please see the detail link by the payment method below for information.',
  'If you have not sent payment for this order, please see the detail link by the payment method below for information. If payment has been sent, this sale will be updated soon, thank you.',
  'RE-ORDER PRODUCTS',
  'To place this order again, click the button below to quickly add products to basket. Note that some prices may have changed since you last placed the order. Products out of stock will not be included. Thank you.'
);

$public_accounts_close = array(
 'Are you sure you would like to close your account? If you close your account you will no longer be able to view past orders.<br><br>To close your account, click the button below.',
 'Final confirmation\n\nAre you sure?',
 'YES, Please Close My Account'
);

$public_accounts_messages = array(
  'Account Verification',
  '<i class="fa fa-check fa-fw"></i> Your account was successfully verified, thank you.<br><br>Please <a href="{url}">login</a> to your account.',
  'Account Verification Error',
  '<i class="fa fa-times fa-fw"></i> Your account has already been verified or you clicked an invalid url.<br><br>Please try to <a href="{url}">login</a> to your account.'
);

$public_accounts_disabled = array(
  'Account Disabled',
  'Your account has been disabled by the store owner.',
  'Please contact us (via the message box below) as soon as possible to resolve this issue.<br><br>We apologise for any inconvenience.'
);

?>
