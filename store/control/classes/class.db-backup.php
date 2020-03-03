<?php

class dbBackup {

  public $database = null;
  public $compress = false;
  public $hexValue = false;
  public $filename = null;
  public $file = null;
  public $isWritten = false;
  public $settings;

  public function __construct($filepath, $compress = false) {
    $this->compress = $compress;
    if (!dbBackup::setOutputFile($filepath)) {
      return false;
    }
    $this->schema_tables = mswDBSchemaArray();
    return dbBackup::setDatabase(DB_NAME);
  }

  public function setDatabase($db) {
    $this->database = $db;
    if (!((bool) mysqli_query($GLOBALS["___msw_sqli"], "USE `" . $this->database . "`"))) {
      return false;
    }
    return true;
  }

  public function getDatabase() {
    return $this->database;
  }

  public function setCompress($compress) {
    if ($this->isWritten) {
      return false;
    }
    $this->compress = $compress;
    dbBackup::openFile($this->filename);
    return true;
  }

  public function getCompress() {
    return $this->compress;
  }

  public function setOutputFile($filepath) {
    if ($this->isWritten) {
      return false;
    }
    $this->filename = $filepath;
    $this->file     = dbBackup::openFile($this->filename);
    return $this->file;
  }

  public function getOutputFile() {
    return $this->filename;
  }

  public function getTableStructure($table) {
    if (!dbBackup::setDatabase($this->database)) {
      return false;
    }
    $structure = '--' . mc_defineNewline();
    $structure .= '-- Table structure for table `' . $table . '` ' . mc_defineNewline();
    $structure .= '--' . mc_defineNewline() . mc_defineNewline();
    $structure .= 'DROP TABLE IF EXISTS `' . $table . '`;' . mc_defineNewline();
    $structure .= "CREATE TABLE `" . $table . "` (" . mc_defineNewline();
    $records = mysqli_query($GLOBALS["___msw_sqli"], 'SHOW FIELDS FROM `' . $table . '`');
    if (mysqli_num_rows($records) == 0) {
      return false;
    }
    while ($record = mysqli_fetch_assoc($records)) {
      $structure .= '`' . $record['Field'] . '` ' . $record['Type'];
      if (!empty($record['Default'])) {
        $structure .= ' DEFAULT \'' . $record['Default'] . '\'';
      }
      if (strcmp($record['Null'], 'YES') != 0) {
        $structure .= ' NOT NULL';
      }
      if (!empty($record['Extra'])) {
        $structure .= ' ' . $record['Extra'];
      }
      $structure .= "," . mc_defineNewline();
    }
    $structure = substr_replace(trim($structure), '', -1);
    $structure .= dbBackup::getSqlKeysTable($table);
    $structure .= mc_defineNewline() . ")";
    $records = mysqli_query($GLOBALS["___msw_sqli"], "SHOW TABLE STATUS LIKE '" . $table . "'");
    if ($record = mysqli_fetch_assoc($records)) {
      if (!empty($record['Engine'])) {
        $structure .= ' ENGINE=' . $record['Engine'];
      }
      if (!empty($record['Auto_increment'])) {
        $structure .= ' AUTO_INCREMENT=' . $record['Auto_increment'];
      }
    }

    $structure .= ";" . mc_defineNewline() . mc_defineNewline() . "-- --------------------------------------------------------" . mc_defineNewline() . mc_defineNewline();
    dbBackup::saveToFile($this->file, $structure);
  }

  public function mc_getTableData($table, $hexValue = true) {
    if (!dbBackup::setDatabase($this->database)) {
      return false;
    }
    $data = '--' . mc_defineNewline();
    $data .= '-- Dumping data for table `' . $table . '`' . mc_defineNewline();
    $data .= '--' . mc_defineNewline() . mc_defineNewline();
    $records    = mysqli_query($GLOBALS["___msw_sqli"], 'SHOW FIELDS FROM `' . $table . '`');
    $num_fields = mysqli_num_rows($records);
    if ($num_fields == 0) {
      return false;
    }
    $selectStatement = "SELECT ";
    $insertStatement = "INSERT INTO `$table` (";
    $hexField        = array();
    for ($x = 0; $x < $num_fields; $x++) {
      $record = mysqli_fetch_assoc($records);
      if (($hexValue) && (dbBackup::isTextValue($record['Type']))) {
        $selectStatement .= 'HEX(`' . $record['Field'] . '`)';
        $hexField[$x] = true;
      } else {
        $selectStatement .= '`' . $record['Field'] . '`';
        $insertStatement .= '`' . $record['Field'] . '`';
        $insertStatement .= ", ";
        $selectStatement .= ", ";
      }
    }
    $insertStatement = substr($insertStatement, 0, -2) . ') VALUES';
    $selectStatement = substr($selectStatement, 0, -2) . ' FROM `' . $table . '`';
    $records         = mysqli_query($GLOBALS["___msw_sqli"], $selectStatement);
    $num_rows        = mysqli_num_rows($records);
    $num_fields      = (($___mysqli_tmp = mysqli_num_fields($records)) ? $___mysqli_tmp : false);
    if ($num_rows > 0) {
      $data .= $insertStatement;
      for ($i = 0; $i < $num_rows; $i++) {
        $record = mysqli_fetch_assoc($records);
        $data .= ' (';
        for ($j = 0; $j < $num_fields; $j++) {
          $field_name = ((($___mysqli_tmp = mysqli_fetch_field_direct($records, $j)->name) && (!is_null($___mysqli_tmp))) ? $___mysqli_tmp : false);
          if (isset($hexField[$j]) && $hexField[$j] && (strlen($record[$field_name]) > 0)) {
            $data .= "0x" . $record[$field_name];
          } else {
            $data .= "'" . str_replace('\"', '"', mc_safeSQL($record[$field_name])) . "'";
          }
          $data .= ',';
        }
        $data = substr($data, 0, -1) . ")";
        $data .= ($i < ($num_rows - 1)) ? ',' : ';';
        $data .= mc_defineNewline();
        if (strlen($data) > 1048576) {
          dbBackup::saveToFile($this->file, $data);
          $data = '';
        }
      }
      $data .= mc_defineNewline() . "-- --------------------------------------------------------" . mc_defineNewline() . mc_defineNewline();
      dbBackup::saveToFile($this->file, $data);
    }
  }

  public function getDatabaseStructure() {
    $structure = '';
    $records   = mysqli_query($GLOBALS["___msw_sqli"], 'SHOW TABLES');
    if (mysqli_num_rows($records) == 0) {
      return false;
    }
    while ($record = mysqli_fetch_row($records)) {
      $structure .= dbBackup::getTableStructure($record[0]);
    }
    return true;
  }

  public function getDatabaseData($hexValue = true) {
    $records = mysqli_query($GLOBALS["___msw_sqli"], 'SHOW TABLES');
    if (mysqli_num_rows($records) == 0) {
      return false;
    }
    while ($record = mysqli_fetch_row($records)) {
      if (in_array($record[0], $this->schema_tables)) {
        dbBackup::mc_getTableData($record[0], $hexValue);
      }
    }
  }

  public function getMySQLVersion() {
    $query   = @mysqli_query($GLOBALS["___msw_sqli"], "SELECT VERSION() AS v");
    $VERSION = @mysqli_fetch_object($query);
    return (isset($VERSION->v) ? $VERSION->v : 'Unknown');
  }

  public function doDump() {
    $header = '#--------------------------------------------------------' . mc_defineNewline();
    $header .= '# MYSQL DATABASE SCHEMATIC' . mc_defineNewline();
    $header .= '# Store: ' . mc_cleanData($this->settings->website) . mc_defineNewline();
    $header .= '# Maian Cart Version: v' . SCRIPT_VERSION . mc_defineNewline();
    $header .= '# Date Created: ' . date(mc_backupDateFormat($this->settings, true)) . ' @ ' . date("H:i:s") . mc_defineNewline();
    $header .= '# MySQL Version: ' . dbBackup::getMySQLVersion() . mc_defineNewline();
    $header .= '#--------------------------------------------------------' . mc_defineNewline() . mc_defineNewline();
    dbBackup::saveToFile($this->file, $header . 'SET FOREIGN_KEY_CHECKS = 0;' . mc_defineNewline() . mc_defineNewline());
    dbBackup::getDatabaseStructure();
    dbBackup::getDatabaseData($this->hexValue);
    dbBackup::saveToFile($this->file, 'SET FOREIGN_KEY_CHECKS = 1;' . mc_defineNewline() . mc_defineNewline());
    dbBackup::closeFile($this->file);
    return true;
  }

  public function writeDump($filename) {
    if (!dbBackup::setOutputFile($filename)) {
      return false;
    }
    dbBackup::doDump();
    dbBackup::closeFile($this->file);
    return true;
  }

  public function getSqlKeysTable($table) {
    $primary         = '';
    $sqlKeyStatement = '';
    $unique          = array();
    $index           = array();
    $fulltext        = array();
    $results         = mysqli_query($GLOBALS["___msw_sqli"], "SHOW KEYS FROM `{$table}`");
    if (mysqli_num_rows($results) == 0) {
      return false;
    }
    while ($row = mysqli_fetch_object($results)) {
      if (($row->Key_name == 'PRIMARY') AND ($row->Index_type == 'BTREE')) {
        if ($primary == '') {
          $primary = "  PRIMARY KEY  (`{$row->Column_name}`";
        } else {
          $primary .= ", `{$row->Column_name}`";
        }
      }
      if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '0') AND ($row->Index_type == 'BTREE')) {
        if (!isset($unique[$row->Key_name])) {
          $unique[$row->Key_name] = "  UNIQUE KEY `{$row->Key_name}` (`{$row->Column_name}`";
        } else {
          $unique[$row->Key_name] .= ", `{$row->Column_name}`";
        }
      }
      if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'BTREE')) {
        if (!isset($index[$row->Key_name])) {
          $index[$row->Key_name] = "  KEY `{$row->Key_name}` (`{$row->Column_name}`";
        } else {
          $index[$row->Key_name] .= ", `{$row->Column_name}`";
        }
      }
      if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'FULLTEXT')) {
        if (!isset($fulltext[$row->Key_name])) {
          $fulltext[$row->Key_name] = "  FULLTEXT `{$row->Key_name}` (`{$row->Column_name}`";
        } else {
          $fulltext[$row->Key_name] .= ", `{$row->Column_name}`";
        }
      }
    }
    if ($primary != '') {
      $sqlKeyStatement .= "," . mc_defineNewline();
      $primary .= ")";
      $sqlKeyStatement .= $primary;
    }
    if (is_array($unique)) {
      foreach ($unique AS $keyName => $keyDef) {
        $sqlKeyStatement .= "," . mc_defineNewline();
        $keyDef .= ")";
        $sqlKeyStatement .= $keyDef;
      }
    }
    if (is_array($index)) {
      foreach ($index AS $keyName => $keyDef) {
        $sqlKeyStatement .= "," . mc_defineNewline();
        $keyDef .= ")";
        $sqlKeyStatement .= $keyDef;
      }
    }
    if (is_array($fulltext)) {
      foreach ($fulltext AS $keyName => $keyDef) {
        $sqlKeyStatement .= "," . mc_defineNewline();
        $keyDef .= ")";
        $sqlKeyStatement .= $keyDef;
      }
    }
    return $sqlKeyStatement;
  }

  public function isTextValue($field_type) {
    switch($field_type) {
      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':
      case 'binary':
      case 'varbinary':
      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        return true;
        break;
      default:
        return false;
    }
  }

  public function openFile($filename) {
    $file = false;
    if ($this->compress) {
      $file = gzopen($filename, 'w9');
    } else {
      $file = fopen($filename, 'ab');
    }
    return $file;
  }

  public function saveToFile($file, $data) {
    if ($this->compress) {
      if ($file) {
        gzwrite($file, $data);
      }
    } else {
      if ($file) {
        fwrite($file, $data);
      }
    }
    $this->isWritten = true;
  }

  public function closeFile($file) {
    if ($this->compress) {
      if ($file) {
        gzclose($file);
      }
    } else {
      if ($file) {
        fclose($file);
      }
    }
  }

}

?>