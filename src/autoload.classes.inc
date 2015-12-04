<?php

// Define any constants.
define('CLASSES_DIR', SERVER_ROOT.'/src/classes');

// Add in any class requirements. Not using namespaces, so this has to be done manually.
require_once(CLASSES_DIR . '/Entity.class.inc');
add_requires(CLASSES_DIR . '/Entity');

// Loop through the specified directory and require any files inside that do not start with "__" (two underscores).
function add_requires ($dir, $ignore_autoload = true) {
  chdir($dir);

  foreach (glob("[!__]*.{php,inc}", GLOB_BRACE) as $filename) {
    if ($ignore_autoload && $filename == 'autoload.php') continue;
    require_once($filename);
  }

  $dir_count = explode('/', $dir);
  $dir_count = count($dir_count);
  $up_level = '';

  for( $i = 0; $i < $dir_count; $i++) {
    $up_level .= '../';
  }

  chdir($up_level);
}