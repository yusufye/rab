$(document).ready(function () {
  var addOrder = 'order/create';

  $('.datatables-orders').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: 'order',
      type: 'GET'
    },
    columns: [
      {
        data: 'created_at',
        visible: false,
        orderable: true
      },
      {
        data: 'job_number',
        orderable: true,
        className: 'text-start'
      },
      {
        data: 'status',
        orderable: true,
        className: 'text-center'
      },
      {
        data: 'customer',
        orderable: true,
        className: 'text-start'
      },

      {
        data: 'price_formatted',
        orderable: true,
        className: 'text-end'
      },
      {
        data: 'biaya_operasional_formatted',
        orderable: true,
        className: 'text-end'
      },
      {
        data: 'profit_formatted',
        orderable: true,
        className: 'text-end'
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
      searchPlaceholder: 'Search Orders'
      // info: 'Displaying _START_ to _END_ of _TOTAL_ entries'
    },
    order: [0, 'desc'],
    dom:
      '<"card-header d-flex rounded-0 flex-wrap py-md-0"' +
      '<"me-5 ms-n2"f>' +
      '<"d-flex justify-content-start justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex align-items-start align-items-md-center justify-content-sm-center mb-1 mb-sm-0 gap-3"lB>>' +
      '>t' +
      '<"row mx-1"' +
      '<"col-sm-12 col-md-6"i>' +
      '<"col-sm-12 col-md-6"p>' +
      '>',
    buttons:
      isAdmin || isSuperAdmin
        ? [
            {
              text: '<i class="mdi mdi-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add</span>',
              className: 'add-new btn btn-primary ms-n1 waves-effect waves-light',
              action: function () {
                window.location.href = addOrder;
              }
            }
          ]
        : [],
    initComplete: function () {}
  });
});
