<?php

/*
  MAIL TAGS FOR HTML EMAILS
  -------------------------------------------------------------------
  Key => Value pairs (BB code => HTML value etc)

  Don`t forget to add the classes to the HTML wrapper:
  content/language/english/email-templates/html-wrapper.html

  You must have a good understanding of HTML/PHP to make this work. :)
---------------------------------------------------------------------*/

$mc_mailHTMLTags = array(
  '[B]' => '<span class="bold">',
  '[/B]' => '</span>',
  '[I]' => '<span class="italic">',
  '[/I]' => '</span>',
  '[PN]' => '<span class="product_name_highlight">',
  '[/PN]' => '</span>'
);

?>