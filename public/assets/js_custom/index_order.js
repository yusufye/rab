$(document).ready(function () {
  // filter

  $('#status').on('change', function () {
    dt_order.ajax.reload();
  });

  var addOrder = 'order/create';

  var dt_order = $('.datatables-orders').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: 'order',
      type: 'GET',
      data: function (d) {
        d.status = $('#status').val(); // Ambil nilai status terbaru
      }
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
        className: 'text-center',
        render: function (data, type, row) {
          return `<a href="#" class="btn-status-order" data-order-id="${row.id}">${data}</a>`;
        }
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

  $(document).on('click', '.btn-revise', function () {
    let reviseUrl = $(this).data('url');
    Swal.fire({
      title: '<h4>Apakah anda yakin akan merevisi dokomen ini ?</h4>',
      footer:
        'Setelah revisi berhasil, akan membentuk dokumen baru dengan status DRAFT dan dokumen ini tidak bisa dirubah kembali',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Save',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      }
    }).then(result => {
      if (result.isConfirmed) {
        window.location.href = reviseUrl;
      }
    });
  });
});
