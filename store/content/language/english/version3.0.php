<?php

$mc_global = array(
  'ltr', // html direction
  'en', // html language
  'System Error',
  'An error has occurred, please try again later',
  'IP Address'
);

$mc_admin = array(
  'Store Settings',
  'Discount Options',
  'Logs',
  'Menu'
);

$mc_header = array(
  'Wish List',
  'Account',
  'Basket',
  'Categories',
  'Currency / Language Options',
  'Please choose your preferred currency &amp; language. If you are logged in your selection will be remembered.',
  'Select Currency',
  'Reload',
  'Select Language',
  'English',
  'Welcome Back, <b>{name}</b>',
  'View My Account'
);

$mc_category = array(
  'Product Expires<span class="date">{date}</span>',
  'Sort by',
  'No additional product images'
);

$mc_product = array(
  'Sorry, this product is currently out of stock',
  'This product currently has no preview images',
  'Please wait..',
  'Add to Basket',
  'Add to Wish List',
  'Comments are not available for this product',
  'No video is currently available for this product',
  'Name',
  'Email',
  'Comments',
  'See an error? Have problems? Seen this product cheaper elsewhere? Please let us know',
  'Send',
  'Product availability until: {date}',
  'We cannot ship this product to some countries. <a href="?pCRes={id}" onclick="mc_Window(this.href, \'400\', \'500\');return false">More Info</a>.',
  'Country Restrictions',
  'We regret that this product cannot currently be shipped to the following countries. Contact us to see if this will change in the future.',
  'An error has occurred, please try again later',
  'View &amp; Purchase',
  'Purchase Wish List Item'
);

$drop_shipping = array(
  'The following personalisation has been requested:',
  'Email sent to "{drop}" with the following information:'
);

$mc_sitemap = array(
  'Our sitemap is shown below, this shows all main site links.'
);

$mc_search = array(
  'Filter by Price',
  'Options',
  'Search',
  'Your search results are shown below.',
  'You must have an account and be logged in to save searches.<br><br>Click <a href="{url}">here</a> to create one.',
  '<i class="fa fa-check fa-fw"></i> Search Saved.',
  'This search is now viewable in your account area, thank you.',
  'This search already exists in your account area.<br><br><a href="{url}"><i class="fa fa-save fa-fw"></i> View Saved Searches</a>',
  '<br><br>Please note that searches are only kept for <b>{days}</b> days.<br><br><a href="{url}"><i class="fa fa-save fa-fw"></i> View Saved Searches</a>',
  'Enter Name/Reference. Max 50 Chars',
  'You must have an account and be logged in to save searches.<br><br>If you don`t have an account one will be created for you after your first purchase.'
);

$mc_wish = array(
  'You must have an account and be logged in to add items to your wish list.<br><br>If you have an account, click <a href="{url2}">here</a> to log in.<br><br>Or click <a href="{url}">here</a> to create a new account.',
  '<i class="fa fa-check fa-fw"></i> Product Saved to Wish List.',
  'You must have an account and be logged in to add items to your wish list.<br><br>If you have an account, click <a href="{url}">here</a> to log in.<br><br>If you don`t have an account one will be created for you after your first purchase.',
  'This product already exists in your wish list.<br><br><a href="{url}"><i class="fa fa-heart fa-fw"></i> View Wish List</a>',
  'This product is now viewable in your account wish list, thank you.<br><br><a href="{url}"><i class="fa fa-heart fa-fw"></i> View Wish List</a>'
);

$mc_giftcert = array(
  'Who is this gift certificate from?',
  'Who would you like to send this gift certificate to?',
  'Would you like to include a personal message?',
  'All name/email fields must be completed and valid'
);

$mc_brands = array(
  'Brands'
);

$mc_leftmenu = array(
  'All Brands',
  'All Categories',
  'All Price Points',
  'Filter by Brand',
  'Filter by Price',
  'Filter by Category'
);

$mc_sysmessage = array(
  'System Message',
  'Account Exists',
  'You appear to have an account with us.<br><br>If you elected to have an account created on checkout, please check your emails for your account password.<br><br>Thank you.<br><br><a class="btn btn-primary" href="{url}"><i class="fa fa-arrow-right fa-fw"></i> Go To Login Page</a>',
  'Account Successfully Closed',
  'Your account has been closed. We are sorry to see you go. Note that you can rejoin again at any time.',
  '0 Search Results',
  'No results matched your criteria, please click the button below to try an advanced search.<br><br><a class="btn btn-primary" href="{url}"><i class="fa fa-search fa-fw"></i> New Search</a>',
  'Store Error',
  'This product has not been assigned a category by the store admin team and therefore cannot load.<br><br>Please contact us as soon as possible about this error, thank you.',
  'Checkout cannot be completed because the wish list owner has not set his/her shipping country or zone.<br><br>Please contact us to resolve this issue or contact the owner of the wish list, thank you.'
);

$msg_emails28 = '[{website}] New Account';
$msg_emails29 = 'Shipping Confirmation from {store}';
$msg_emails30 = '[{website}] Your Account Requires Verification';
$msg_emails31 = '[{website}] Account Active';
$msg_emails32 = '[{website}] New Account Created';
$msg_emails33 = '[{website}] Password Reset';
$msg_emails34 = '[{website}] Message from Visitor';
$msg_emails35 = '[{website}] Message Confirmation';
$msg_emails36 = '[{website}] Your Account Has Been Created';
$msg_emails37 = '[{website}] Product Enquiry';
$msg_emails38 = '[{website}] Product Enquiry Confirmation';
$msg_emails39 = '[{website}] Account Closed';
$msg_emails40 = '[{website}] New Trade Account';
$msg_emails41 = '[{website}] Pre-Payment Order Notification';
$msg_emails42 = '[{website}] Wish List Purchase Completed';
$msg_emails43 = '[{website}] New Trade Order (#{invoice}) ({gateway})';
$msg_emails44 = 'Qty: {qty} @ {price} each';
$msg_emails45 = '{attr}: {val} ({cost})';
$msg_emails46 = '{attr}: {val}';
$msg_emails47 = '[{website}] CleanTalk API Block Report';
$msg_emails48 = '[{website}] Product Expiry Update Notification';

$msg_email_prod_expiry_string  = 'Price Reduction: {price}';
$msg_email_prod_expiry_string2 = 'Previous Price: {price}';
$msg_email_prod_expiry_string3 = 'New Price: {price}';
$msg_email_prod_expiry_string4 = 'Special Offer: {offer}';

$mc_checkout = array(
  'Payment / Checkout Message',
  'Enter account email',
  'Enter account password',
  'Login &amp; Continue',
  '{count} Item(s)',
  'Continue',
  'Checkout as Guest',
  'You don`t have to have an account to buy from us. Just click the button below.<br><br>You`ll have the chance to set up an account during checkout, this is totally optional.',
  'Clear All',
  'Qty',
  '<span class="exttxt">Extras</span>',
  'Checkout Items',
  'Sub Total is total before shipping and tax (if applicable) has been applied',
  'Shipping &amp; tax (if applicable), will be applied on checkout.',
  'Unexpected Cart Error',
  'Sub Total is total before tax (if applicable) has been applied',
  'Tax (if applicable), will be applied on checkout.',
  '<i class="fa fa-exclamation fa-fw"></i> Quantity exceeded, sorry only <b>{count}</b> in stock.',
  'Trade Discount',
  'Error. The minimum purchase quantity for this product is <b>{min}</b>.',
  'No quantity set, please try again',
  '<a href="{url}" class="btn btn-primary"><i class="fa fa-search fa-fw"></i> View Product</a>',
  'Quantity Rate Shipping',
  'Quantity Rate Shipping: <b>{percent}%</b> of the goods total',
  'Please confirm you accept our <a href="{url}" onclick="mc_Window(this.href, 600, 600, \'\');return false">Terms &amp; Conditions</a>',
  'Terms &amp; Conditions',
  'Thank you for your order.<br><br>Email confirmation should arrive shortly with details of your order, which will also appear in your account area if you have an account with us.<br><br>If this email does not arrive within 2hours, please contact us.',
  '<i class="fa fa-exclamation fa-fw"></i> Wish List Restriction.<br><br>Currently our system only allows a single wish list item to be purchased at a time.<br><br>Click <a href="{url}">here</a> to proceed to checkout.<br><br>If you selected the wrong item, clear your basket to continue.',
  '<i class="fa fa-exclamation fa-fw"></i> Wish List Restriction.<br><br>You have an item from a wish list in your basket. Wish list items must be purchased separately. If you don`t wish to checkout, remove this item from your basket.<br><br>Click <a href="{url}">here</a> to proceed to checkout.',
  'As this is a wish list order, the shipping address is hidden for security.<br><br>The shipping destination for this order is <b>{country}</b><br><br>Please continue to view shipping cost and options.',
  'Order Completed',
  'Download Only Order - Pending',
  'per item',
  'On Account',
  'Clear Basket',
  'A minimum checkout amount is required. Your basket must be at least {amount} before you can proceed. Please adjust quantities and reload page or add other products. Thank you.'
);

$msg_checkout_reorder = array(
  '<i class="fa fa-exclamation fa-fw"></i> Sale not found or permission denied, please check and try again.',
  '<i class="fa fa-exclamation fa-fw"></i> Permission denied. Check you are still logged in by refreshing your browser page.',
  '<i class="fa fa-exclamation fa-fw"></i> No items from this order can be added at this time. Search our site to see if the products are still listed. Sorry for any inconvenience.'
);

$msg_bbcode33   = 'Daily Motion Video Display';

$msg_storeform = array(
  '<i class="fa fa-exclamation fa-fw"></i> Please include valid name, email and message',
  '<i class="fa fa-check fa-fw"></i> Thank You',
  'Your message has been sent.<br><br>Please be patient while we deal with your request.',
  '<i class="fa fa-exclamation fa-fw"></i> Your IP is not permitted in our system'
);

$msg_shop_basket = array(
  '<i class="fa fa-exclamation fa-fw"></i> This product must be added to the basket from the product page as other options are required.<br><br>Click <a href="{url}"><b>here</b></a> to visit the product page.',
  'ea',
  '<i class="fa fa-exclamation fa-fw"></i> Please enter valid names and email addresses where applicable.',
  'This is a wish list purchase for: <a href="{url}">{name}</a>'
);

$msg_view_order = array(
  '<i class="fa fa-exclamation fa-fw"></i> Download file does not exist.<br><br>Please contact us as soon as possible.',
  'Guest Checkout',
  '<i class="fa fa-heart fa-fw"></i> This is an order from your wishlist.<br><br>Billing information is not shown for security.',
  '<i class="fa fa-heart fa-fw"></i> This is an order from a wishlist.<br><br>Shipping information is not shown for security.',
  'Additional Charges'
);

$msg_blog = array(
  'Published: {date}'
);

$msg_buynow = array(
  'Checking Product',
  'Please wait..'
);

$msg_platforms = array(
  'Desktop',
  'Tablet',
  'Mobile'
);

$msg_hurry_limited_stock_text = array(
  'HURRY, last {stock} available. Order <b>NOW</b> to avoid disappointment', // Category..
  'HURRY, last {stock} available. Order <b>NOW</b> to avoid disappointment'  // Product page..
);

$errorPages = array(
  '400' => 'Error - Bad Request',
  '401' => 'Permission Denied',
  '403' => 'Permission Denied',
  '404' => 'Page Not Found',
  '500' => 'Internal Server Error',
  'msg' => array(
    'Sorry for the inconvenience.<br><br>If this persists, please let us know, thank you.',
    'Return to {website}',
    'Contact Us'
  )
);

?>