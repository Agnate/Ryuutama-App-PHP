<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include anything we'll use here.
use \Kint;
require_once('../config.php');
require_once(SERVER_ROOT.'/public/vendor/autoload.php');
require_once(SERVER_ROOT.'/src/db.inc');
require_once(SERVER_ROOT.'/src/autoload.classes.inc');

d($_GET);

// If there's no path, show the API help.
if (empty($_GET['path'])) {
  print '<p>Here is some API help...</p>';
  exit;
}

// Get the path.
$path = $_GET['path'];

// Figure out the crude routing.
// Wildcards = %
// Example: item/%/stats
$routing = array();
$routing['item-template/%'] = array('callback' => 'test_func', 'arguments' => array(1));
$routing['test'] = array('callback' => 'test_func', 'arguments' => array(0));
$routing['test/test2'] = array('callback' => 'test_func', 'arguments' => array(1));
$routing['test/%'] = array('callback' => 'test_func', 'arguments' => array(1));
$routing['test/%/test2'] = array('callback' => 'test_func', 'arguments' => array(1));
$routing['test/%/test2/%'] = array('callback' => 'test_func', 'arguments' => array(3));
$routing['test/%/test2/%/test3'] = array('callback' => 'test_func', 'arguments' => array(4));


// Sort routing by the longest keys first.
uksort($routing, "sort_by_length");
function sort_by_length ($a, $b) {
  return strlen($b) - strlen($a);
}

// Convert the routing data.
$router_defaults = array('arguments' => array());
foreach ($routing as $route => $router) {
  // Add in router defaults.
  $routing[$route] = $router + $router_defaults;
  // Create the regex.
  $routing[$route]['segments'] = explode('/', $route);
  $segments = $routing[$route]['segments'];
  // Replace wildcards with regex selections.
  foreach ($segments as $skey => $segment) {
    if ($segment == '%') $segments[$skey] = '(.*)';
    else $segments[$skey] = '(' . $segment . ')';
  }
  // Create the simple regex.
  $routing[$route]['pattern'] = '/' . implode('\/', $segments) . '/';
}

d($routing);
$path_segments = explode('/', $path);
$path_segments_count = count($path_segments);
foreach ($routing as $route => $router) {
  if (empty($router['callback'])) continue;
  if (empty($router['pattern'])) continue;
  if (!preg_match($router['pattern'], $path, $matches)) continue;

  d($matches);

  // Replace wildcard segments with values from path.
  foreach ($router['segments'] as $key => $segment) {
    if ($segment != '%') continue;
    if (!isset($matches[$key + 1])) continue;
    $router['segments'][$key] = $matches[$key + 1];
  }

  // Loop through the arguments and replace the wildcards.
  $arguments = array_intersect_key($router['segments'], array_combine($router['arguments'], $router['arguments']));

  // Make the callback.
  call_user_func_array($router['callback'], $arguments);
}


function test_func ($cat) {
  print 'Wow! --> '.$cat.'<br>';
}

// Grab the path.
// $path = $_GET['path'];

// d($path);

// Get the item templates.
// $items = ItemTemplate::load_multiple(array());

// // Convert list to JSON.
// $json = array();
// foreach ($items as $item) {
//   $json[] = $item->json(false);
// }

// print json_encode($json);