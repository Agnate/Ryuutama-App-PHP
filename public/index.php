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
    
    <link rel="stylesheet" type="text/css" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="bower_components/datatables.net-responsive-bs/css/responsive.bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <script src="vendor/components/jquery/jquery.min.js"></script>
    <script src="vendor/twbs/bootstrap-sass/assets/javascripts/bootstrap.min.js"></script>
    <script src="js/jquery.nestable.js"></script>
    <script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="js/inventory.js"></script>

    
  </head>
  <body>
    <div id="main" class="container">
      <h1>Ryuutama</h1>

      <h2>Inventory</h2>
      <div id="inventory">
        <div class="controls">
          <button id="item-add" class="btn btn-primary" data-toggle="modal" data-target="#item-template-list"><span class="glyphicon glyphicon-plus-sign"></span><span class="label"> Add Item</span></button>
        </div>
        <div class="purse"></div>

        <div class="items">


          <!-- <div class="dd">
            <ol class="dd-list">
                <li class="dd-item dd3-item" data-id="13">
                    <div class="dd-handle dd3-handle">Drag</div><div class="dd3-content">Item 13</div>
                </li>
                <li class="dd-item dd3-item" data-id="14">
                    <div class="dd-handle dd3-handle">Drag</div><div class="dd3-content">Item 14</div>
                </li>
                <li class="dd-item dd3-item" data-id="15"><button data-action="collapse" type="button">Collapse</button><button data-action="expand" type="button" style="display: none;">Expand</button>
                    <div class="dd-handle dd3-handle">Drag</div><div class="dd3-content">Item 15</div>
                    <ol class="dd-list">
                        <li class="dd-item dd3-item" data-id="16">
                            <div class="dd-handle dd3-handle">Drag</div><div class="dd3-content">Item 16</div>
                        </li>
                        <li class="dd-item dd3-item" data-id="17">
                            <div class="dd-handle dd3-handle">Drag</div><div class="dd3-content">Item 17</div>
                        </li>
                        <li class="dd-item dd3-item" data-id="18">
                            <div class="dd-handle dd3-handle">Drag</div><div class="dd3-content">Item 18</div>
                        </li>
                    </ol>
                </li>
            </ol>
        </div> -->


        </div>
      </div>

      <!-- Item Template modal -->
      <div class="modal fade" id="item-template-list" tabindex="-1" role="dialog" aria-labelledby="AddItem">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="AddItem">Add item</h4>
            </div>
            <div class="modal-body">
              <table class="table table-striped table-condensed items">
                <thead>
                  <tr>
                    <th class="col-add"></th>
                    <th class="col-name">Name</th>
                    <th class="col-cost">Cost</th>
                    <th class="col-size">Size</th>
                    <!-- <th class="col-description">Description</th> -->
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($item_templates as $item): ?>
                    <tr>
                      <td class="col-add"></td>
                      <td class="col-name"><?php print $item->name; ?></td>
                      <td class="col-cost"><?php print $item->cost; ?></td>
                      <td class="col-size"><?php print $item->size; ?></td>
                      <!-- <td class="col-description"><?php // print $item->description; ?></td> -->
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-success">Add</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </body>
</html>