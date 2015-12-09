<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['token']) || $_GET['token'] != 'superbanana2oihp2r') exit;

// Get character ID from $_GET.
if (!isset($_GET['cid'])) exit;

// Include anything we'll use here.
use \Kint;
require_once('../../config.php');
require_once(SERVER_ROOT.'/public/vendor/autoload.php');
require_once(SERVER_ROOT.'/src/db.inc');
require_once(SERVER_ROOT.'/src/autoload.classes.inc');

// Get the character's inventory.
$cid = $_GET['cid'];
$items = Item::load_multiple(array('cid' => $cid));

// Convert list to JSON.
$json = array();
foreach ($items as $item) {
  $json[] = $item->json(false);
}

print json_encode($json);