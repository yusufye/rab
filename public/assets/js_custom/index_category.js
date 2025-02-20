$(document).ready(function () {
  var addMak = 'category/create';

  $('.datatables-orders').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: 'category',
      type: 'GET'
    },
    columns: [
      {
        data: 'category_name',
        orderable: true
      },

      {
        data: 'actions',
        searchable: false,
        orderable: false,
        className: 'text-center'
      }
    ],
    language: {
      sLengthMenu: '_MENU_',
      search: '',
      searchPlaceholder: 'Search Category'
      // info: 'Displaying _START_ to _END_ of _TOTAL_ entries'
    },
    order: [0, 'category_name'],
    dom:
      '<"card-header d-flex rounded-0 flex-wrap py-md-0"' +
      '<"me-5 ms-n2"f>' +
      '<"d-flex justify-content-start justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex align-items-start align-items-md-center justify-content-sm-center mb-1 mb-sm-0 gap-3"lB>>' +
      '>t' +
      '<"row mx-1"' +
      '<"col-sm-12 col-md-6"i>' +
      '<"col-sm-12 col-md-6"p>' +
      '>',
    buttons: [
      {
        text: '<i class="mdi mdi-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add</span>',
        className: 'add-new btn btn-primary ms-n1 waves-effect waves-light',
        action: function () {
          window.location.href = addMak;
        }
      }
    ],
    initComplete: function () {}
  });
});
