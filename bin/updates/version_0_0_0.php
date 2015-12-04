<?php

/**
 * Name the function with the version number, as this is how we'll verify the update.
 */
function update_version_0_0_0 ($forced = false) {
  $time = time();
  $hours = 60 * 60;

  // Create ItemTemplate table.
  $item_template_table = array();
  $item_template_table[] = "itid INT(11) UNSIGNED AUTO_INCREMENT";
  $item_template_table[] = "name_id VARCHAR(100) NOT NULL";
  $item_template_table[] = "name VARCHAR(255) NOT NULL";
  $item_template_table[] = "type VARCHAR(100) NOT NULL";
  $item_template_table[] = "icon VARCHAR(100) NOT NULL";
  $item_template_table[] = "size INT(10) UNSIGNED NOT NULL";
  $item_template_table[] = "durability INT(10) UNSIGNED NOT NULL";
  $item_template_table[] = "cost INT(10) UNSIGNED NOT NULL";
  $item_template_table[] = "capacity INT(10) UNSIGNED NOT NULL";
  $item_template_table[] = "PRIMARY KEY ( itid )";
  add_update_query( "CREATE TABLE IF NOT EXISTS item_templates (". implode(',', $item_template_table) .")" );

  // Create Item table.
  $item_table = array();
  $item_table[] = "iid INT(11) UNSIGNED AUTO_INCREMENT";
  $item_table[] = "itid INT(11) UNSIGNED NOT NULL";
  $item_table[] = "cid INT(11) UNSIGNED NOT NULL";
  $item_table[] = "name_id VARCHAR(100) NOT NULL";
  $item_table[] = "name VARCHAR(255) NOT NULL";
  $item_table[] = "type VARCHAR(100) NOT NULL";
  $item_table[] = "icon VARCHAR(100) NOT NULL";
  $item_table[] = "size INT(10) UNSIGNED NOT NULL";
  $item_table[] = "durability INT(10) UNSIGNED NOT NULL";
  $item_table[] = "cost INT(10) UNSIGNED NOT NULL";
  $item_table[] = "capacity INT(10) UNSIGNED NOT NULL";
  $item_table[] = "PRIMARY KEY ( iid )";
  add_update_query( "CREATE TABLE IF NOT EXISTS items (". implode(',', $item_table) .")" );

  

  // Add ItemTemplates.
  $item_templates = array();
  $item_templates[] = array(':name_id' => 'food', ':name' => 'Food', ':type' => 'consumable', ':icon' => '', ':size' => 1, ':durability' => 1, ':cost' => 5, ':capacity' => 0);
  $item_templates[] = array(':name_id' => 'backpack', ':name' => 'Backpack', ':type' => 'container', ':icon' => '', ':size' => 3, ':durability' => 3, ':cost' => 20, ':capacity' => 5);
  
  //$item_templates[] = array(':name_id' => '', ':name' => '', ':type' => '', ':icon' => '', ':size' => 0, ':durability' => 0, ':cost' => 0, ':capacity' => 0);
  foreach ($item_templates as $item_template) {
    add_update_query("INSERT INTO item_templates (name_id, name, type, icon, size, durability, cost, capacity) VALUES (:name_id, :name, :type, :icon, :size, :durability, :cost, :capacity)", $item_template);
  }
}