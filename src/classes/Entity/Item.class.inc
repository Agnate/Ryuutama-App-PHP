<?php

class Item extends Entity {
  // Fields
  public $iid;
  public $itid; // ItemTemplate ID
  public $cid;
  public $name_id;
  public $name;
  public $type;
  public $icon;
  public $size;
  public $cost;
  public $capacity;

  // Protected
  protected $_description;
  
  // Private vars
  static $fields_int = array('size', 'cost', 'capacity');
  static $db_table = 'items';
  static $default_class = 'Item';
  static $primary_key = 'iid';
  static $partials = array('name', 'name_id');

  
  function __construct($data = array(), ItemTemplate $template = null) {
    // Preload template values.
    parent::extract_from_template($template);

    // Perform regular constructor.
    parent::__construct($data);
  }
}