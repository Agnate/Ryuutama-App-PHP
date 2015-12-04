<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use \Kint;

require_once('../config.php');
require_once(SERVER_ROOT.'/public/vendor/autoload.php');
require_once(SERVER_ROOT.'/src/db.inc');
require_once(SERVER_ROOT.'/src/autoload.classes.inc');

$item_templates = ItemTemplate::load_multiple(array());

d($item_templates);

$items = Item::load_multiple(array());

d($items);

?><html>
  <head>
    <title>Ryuutama</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="vendor/components/jquery/jquery.min.js"></script>
    <script src="js/jquery.nestable.js"></script>
    <script src="js/inventory.js"></script>
  </head>
  <body>
    <h1>Ryuutama</h1>

    <h2>Inventory</h2>
    <div id="inventory">
      <div class="controls">
        <button id="item-add" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span><span class="label"> Add Item</span></button>
      </div>
      <div class="purse"></div>

      <div class="items">

      </div>
    </div>
  </body>
</html>