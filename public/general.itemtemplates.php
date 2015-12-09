<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['token']) || $_GET['token'] != 'superbanana2oihp2r') exit;

// Include anything we'll use here.
use \Kint;
require_once('../../config.php');
require_once(SERVER_ROOT.'/public/vendor/autoload.php');
require_once(SERVER_ROOT.'/src/db.inc');
require_once(SERVER_ROOT.'/src/autoload.classes.inc');

// Get the item templates.
$items = ItemTemplate::load_multiple(array());

// Convert list to JSON.
$json = array();
foreach ($items as $item) {
  $json[] = $item->json(false);
}

print json_encode($json);