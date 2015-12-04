<?php

class ItemTemplate extends Entity {
  // Fields
  public $itid;
  public $name_id;
  public $name;
  public $type;
  public $icon;
  public $size;
  public $cost;
  public $capacity;
  
  // Private vars
  static $fields_int = array('size', 'cost', 'capacity');
  static $db_table = 'item_templates';
  static $default_class = 'ItemTemplate';
  static $primary_key = 'itid';

  
  function __construct($data = array()) {
    // Perform regular constructor.
    parent::__construct( $data );
  }
}