(function ($) {
  
  $(document).ready(function () {
    $('#item-template-list table.items').DataTable();
    // Select rows in table by clicking on them.
    $('#item-template-list table.items tbody').on('click', 'tr', function () {
      $(this).toggleClass('success');
    });
  });

})(jQuery);