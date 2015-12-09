<?php

abstract class Entity {

  // Private vars
  static $fields_int;
  static $db_table = '';
  static $primary_key = '';
  static $default_class = '';
  static $partials = array('name');


  function __construct($data = array()) {
    // Save values to object.
    if (count($data)) {
      foreach ($data as $key => $value) {
        if (property_exists($this, $key)) {
          $this->{$key} = $value;
        }
      }
    }

    // Set some more defaults.
    if (!empty(static::$fields_int)) {
      foreach (static::$fields_int as $field) {
        if (empty($this->{$field})) $this->{$field} = 0;
        else if (!empty($this->{$field}) && !is_int($this->{$field})) $this->{$field} = (int)$this->{$field};
      }
    }
  }

  protected function extract_from_template ($template) {
    if (!empty($template)) {
      $tempdata = get_object_vars($template);

      foreach ($tempdata as $key => $value) {
        if (property_exists($this, $key)) {
          $this->{$key} = $value;
        }
      }
    }
  }

  protected function get_db () {
    $class = get_class($this);
    return $class::$db_table;
  }

  public function save () {
    // If we don't have a database table, we're done.
    if ( static::$db_table == '' ) return FALSE;
    if ( empty(static::$primary_key) ) return FALSE;

    // Get database values to save out.
    $data = call_user_func('get_object_vars', $this);

    // If there's no $pid, it means it's a new lifeform.
    $is_new = empty($data[static::$primary_key]);
    if ( $is_new ) {
      unset($data[static::$primary_key]);
    }

    // Generate the database tokens.
    $tokens = array();
    $new_data = array();
    foreach ($data as $key => &$value) {
      if ($value === null) continue;

      $tokens[$key] = ':'.$key;
      $new_data[':'.$key] = $value;
    }

    // New object
    if ($is_new) {
      $query = "INSERT INTO ". static::$db_table ." (". implode(', ', array_keys($tokens)) .") VALUES (". implode(", ", array_values($tokens)) .")";
      $query = pdo_prepare($query);
      $success = $query->execute($new_data);

      // if (!$success) {
      //   d("INSERT INTO ". static::$db_table ." (". implode(', ', array_keys($tokens)) .") VALUES (". implode(", ", array_values($tokens)) .")");
      //   d($query->errorInfo());
      // }
      
      // Save the $primary_key.
      $this->{static::$primary_key} = get_pdo()->lastInsertId(static::$primary_key);
    }
    // Existing object
    else {
      $sets = array();
      foreach( $tokens as $key => $token ) {
        if ( $key == static::$primary_key ) continue;
        $sets[] = $key .'='. $token;
      }
      
      $query = "UPDATE ". static::$db_table ." SET ". implode(', ', $sets) ." WHERE ". static::$primary_key.'='.$tokens[static::$primary_key];
      $query = pdo_prepare($query);
      $success = $query->execute($new_data);
    }

    return $success;
  }

  public function delete () {
    // If we don't have a database table, we're done.
    if ( static::$db_table == '' ) return FALSE;
    if ( empty(static::$primary_key) ) return FALSE;

    $data = array(
      ':primarykey' => $this->{static::$primary_key},
    );

    // Delete the entry based on the primary key.
    $query = "DELETE FROM ".static::$db_table." WHERE ".static::$primary_key."=:primarykey";
    $query = pdo_prepare($query);
    $result = $query->execute($data);

    $info = array(
      'success' => ($result !== false),
      'result' => $result,
    );

    // If it was an error, return the error.
    if ( $result === false ) {
      $info['error'] = $query->errorInfo();
    }

    return $info;
  }

  public function json ($encode = true, $exclude = array()) {
    $vars = get_object_vars($this);
    // Remove any variables that begin with an underscore.
    foreach ($vars as $key => $value) {
      if (in_array($key, $exclude))
        unset($vars[$key]);
      
      if (strpos($key, '_') === 0)
        unset($vars[$key]);
    }
    $vars['php_class'] = get_class($this);
    return !$encode ? $vars : json_encode($vars);
  }

  /**
       ______________  ________________
      / ___/_  __/   |/_  __/  _/ ____/
      \__ \ / / / /| | / /  / // /     
     ___/ // / / ___ |/ / _/ // /___   
    /____//_/ /_/  |_/_/ /___/\____/   
                                       
   */

  public static function load ($data, $find_partials = false) {
    // If we don't have a database table, we're done.
    if (static::$db_table == '') {
      return FALSE;
    }

    // Generate the database tokens.
    $tokens = array();
    $new_data = array();
    foreach ($data as $key => &$value) {
      if (is_array($value)) {
        $tokens[$key] = array();
        $count = 0;
        foreach ($value as $subvalue) {
          $count++;
          $tokens[$key][] = ':'.$key.'_i'.$count;
          $new_data[':'.$key.'_i'.$count] = $subvalue;
        }
      }
      else {
        $tokens[$key] = ':'.$key;
        $new_data[':'.$key] = ($find_partials && in_array($key, static::$partials)) ? '%'.$value.'%' : $value;  
      } 
    }

    $where = array();
    foreach ($tokens as $key => $token) {
      // If this is an array of tokens, put it into an IN statement.
      if (is_array($token)) $where[] = $key .' IN ('. implode(',', $token) .')';
      // Else if need to look up partials, do that.
      else if ($find_partials && in_array($key, static::$partials)) $where[] = $key .' LIKE '. $token;
      // Otherwise just find the value given.
      else $where[] = $key .'='. $token;
    }

    if (count($where) <= 0) return FALSE;

    $query = "SELECT * FROM ". static::$db_table ." WHERE ". implode(' AND ', $where) ." LIMIT 1";
    $query = pdo_prepare($query);

    if (static::$default_class != '' && class_exists(static::$default_class)) {
      $query->setFetchMode(PDO::FETCH_CLASS, static::$default_class, array());
    }
    
    $query->execute($new_data);

    if ($query->rowCount() <= 0) {
      return array();
    }

    $row = $query->fetch();

    // Get the class name and load it up into there.
    if (is_object($row)) {
      if (!property_exists($row, 'class_name') || !class_exists($row->class_name)) {
        return $row;
      }

      $new_row = new $row->class_name ( $row );
    }
    // If it's not an object, it has to be an array.
    else {
      if (!isset($row['class_name']) || !class_exists($row['class_name'])) {
        return $row;
      }

      $new_row = new $row['class_name'] ($row);
    }

    // If the new class is not a subclass (aka, not in the same lineage of classes), we shouldn't use it.
    // (NOTE: If I upgrade to PHP 5.5, I can use Reflection, which might be smarter)
    if (static::$default_class != '' && !is_subclass_of($new_row, static::$default_class)) {
      return $row;
    }

    return $new_row;
  }

  /**
   * Loads multiple rows of data based on the query $data provided.
   * $data -> An array of exact values that the query will search for.
   * $special -> The text here is tacted onto the end of the query. It's useful for things like "order by" and "limit".
   */
  public static function load_multiple ($data, $special = "") {
    // If we don't have a database table, we're done.
    if (static::$db_table == '') return FALSE;

    // Generate the database tokens.
    $tokens = array();
    $new_data = array();
    foreach ($data as $key => &$value) {
      if (is_array($value)) {
        $tokens[$key] = array();
        $count = 0;
        foreach ($value as $subvalue) {
          $count++;
          $tokens[$key][] = ':'.$key.'_i'.$count;
          $new_data[':'.$key.'_i'.$count] = $subvalue;
        }
      }
      else {
        $tokens[$key] = ':'.$key;
        $new_data[':'.$key] = $value;  
      } 
    }

    $where = array();
    foreach ($tokens as $key => $token) {
      if (is_array($token)) $where[] = $key .' IN ('. implode(',', $token) .')';
      else $where[] = $key .'='. $token;
    }

    $query = "SELECT * FROM ". static::$db_table .(count($where) > 0 ? " WHERE ". implode(' AND ', $where) : "") .(!empty($special) ? " ".$special : "");
    $query = pdo_prepare($query);

    if (static::$default_class != '' && class_exists(static::$default_class)) {
      $query->setFetchMode(PDO::FETCH_CLASS, static::$default_class, array());
    }
    
    $query->execute($new_data);

    $rows = array();
    if ($query->rowCount() > 0) {
      while ($row = $query->fetch()) {
        // Get the class name and load it up into there.
        if (is_object($row) && property_exists($row, 'class_name') && class_exists($row->class_name)) {
          $row = new $row->class_name ($row);
        }
        // Else if it's an array, load it.
        else if (is_array($row) && isset($row['class_name']) && class_exists($row['class_name'])) {
          $row = new $row['class_name'] ($row);
        }

        $rows[] = $row;
      }
    }

    return $rows;
  }

  /**
   * Extract an entity or list of entities from the JSON.
   */
  public static function extract ($json) {
    $data = json_decode($json);
    if ($data === null) return $data;

    // If this is not an array, treat it as a single entity.
    if (!is_array($json)) {
      return static::extract_entity($json);
    }

    // Otherwise try to process a batch.
    $list = array();
    foreach ($data as $entity) {
      $data[] = static::extract_entity($entity);
    }
    return $list;
  }

  protected static function extract_entity ($data) {
    if (empty($data['php_class']) || !class_exists($data['php_class'])) return FALSE;
    return new $data['php_class'] ($data);
  }
}