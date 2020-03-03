<?php

class isbn {

  public $settings;

  // Tidy string..
  public function stringTidy($string) {
    $string = trim($string);
    if (substr($string, -1) == ',') {
      $string = substr_replace($string, '', -1);
    }
    return $string;
  }

  // ISBN lookup..
  public function isbnLookup() {
    global $msg_settings220, $msg_settings221, $msg_settings222, $msg_settings219;
    $data = array(
      'none',
      'none',
      'none',
      $msg_settings219
    );
    // API url..
    $api  = str_replace(array(
      '{KEY}',
      '{ISBN}'
    ), array(
      $this->settings->isbnAPI,
      $_GET['isbnLookup']
    ), ISBN_API_URL);
    // PHP 4 or 5..
    if (function_exists('simplexml_load_file')) {
      $BOOK = @simplexml_load_file($api);
      // Check for valid key..
      if (isset($BOOK->ErrorMessage[0]) && $BOOK->ErrorMessage[0] == 'Access key error') {
        return array(
          'key-error',
          'none',
          'none',
          $msg_settings221
        );
      }
      $title      = (isset($BOOK->BookList[0]->BookData[0]->Title) ? isbn::stringTidy($BOOK->BookList[0]->BookData[0]->Title) : '');
      $long_title = (isset($BOOK->BookList[0]->BookData[0]->TitleLong) ? isbn::stringTidy($BOOK->BookList[0]->BookData[0]->TitleLong) : '');
      $author     = (isset($BOOK->BookList[0]->BookData[0]->AuthorsText) ? isbn::stringTidy($BOOK->BookList[0]->BookData[0]->AuthorsText) : '');
      $summary    = (isset($BOOK->BookList[0]->BookData[0]->Summary) ? isbn::stringTidy($BOOK->BookList[0]->BookData[0]->Summary) : '');
      $publisher  = (isset($BOOK->BookList[0]->BookData[0]->PublisherText) ? isbn::stringTidy($BOOK->BookList[0]->BookData[0]->PublisherText) : '');
    } else {
      // If allow_url_fopen is off, lets try and switch it on for this operation..
      if (@ini_get('allow_url_fopen') == false) {
        @ini_set('allow_url_fopen', true);
      }
      $XML = @file_get_contents($api);
      if ($XML == '') {
        return array(
          'unavailable',
          'none',
          'none',
          $msg_settings222
        );
      }
      // Create XML from data..
      $PARSER = @xml_parser_create();
      @xml_parse_into_struct($PARSER, $XML, $structure, $val);
      @xml_parser_free($PARSER);
      // Get data..
      if (empty($structure)) {
        return $data;
      }
      $BOOK = isbn::getXMLInfo($structure);
      if (isset($BOOK['ErrorMessage']) && $BOOK['ErrorMessage'] == 'Access key error') {
        return array(
          'key-error',
          'none',
          'none',
          $msg_settings221
        );
      }
      $title      = (isset($BOOK['Title']) ? isbn::stringTidy($BOOK['Title']) : '');
      $long_title = (isset($BOOK['TitleLong']) ? isbn::stringTidy($BOOK['TitleLong']) : '');
      $author     = (isset($BOOK['AuthorsText']) ? isbn::stringTidy($BOOK['AuthorsText']) : '');
      $summary    = (isset($BOOK['Summary']) ? isbn::stringTidy($BOOK['Summary']) : '');
      $publisher  = (isset($BOOK['PublisherText']) ? isbn::stringTidy($BOOK['PublisherText']) : '');
    }
    if ($long_title) {
      $title = $long_title;
    }
    if ($title == '' && $author == '') {
      return $data;
    }
    // Return data..
    return array(
      str_replace(array(
        '{book}',
        '{author}'
      ), array(
        ($title ? $title : ''),
        ($author ? $author : '')
      ), $msg_settings220),
      ($summary ? $summary : 'none'),
      ($summary ? $summary . ($publisher ? mc_defineNewline() . mc_defineNewline() . $publisher : '') : 'none'),
      $msg_settings219
    );
    return $data;
  }

  // For PHP 4..
  public function getXMLInfo($xml) {
    $data = array();
    for ($i = 0; $i < count($xml); $i++) {
      switch($xml[$i]['tag']) {
        case 'TITLE';
          $data['Title'] = $xml[$i]['value'];
          break;
        case 'TITLELONG';
          $data['TitleLong'] = $xml[$i]['value'];
          break;
        case 'AUTHORSTEXT';
          $data['AuthorsText'] = $xml[$i]['value'];
          break;
        case 'SUMMARY';
          $data['Summary'] = $xml[$i]['value'];
          break;
        case 'PUBLISHERTEXT';
          $data['PublisherText'] = $xml[$i]['value'];
          break;
        case 'ERRORMESSAGE';
          $data['ErrorMessage'] = $xml[$i]['value'];
          break;
      }
    }
    return $data;
  }

}

?>