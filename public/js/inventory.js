(function ($) {

  // Get the item templates.
  // var item_templates = $.ajax({
  //   dataType: "json",
  //   url: '/processing/',
  //   data: data,
  //   success: success
  // });
  
  $(document).ready(function () {
    $('#item-template-list table.items').DataTable({
      colReorder: true,
      "columnDefs": [
        { type: "html", targets: 1 }
      ]
    });
    // Select rows in table by clicking on them.
    $('#item-template-list table.items tbody').on('click', 'tr', function (event) {
      $(this).toggleClass('success');
      var $template = $(this).find('.item-template');
      if ($(event.target).get(0) != $template.get(0)) {
        $template.prop('checked', !$template.prop('checked'));
      }
    });
  });

})(jQuery);