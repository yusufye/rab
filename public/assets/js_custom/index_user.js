/**
 * Page User List
 */
 
$(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });


    var addUser = 'user/create';

      $('.datatables-users').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
          url: 'ajax_list_users',
          type: 'GET',
          dataSrc: 'data',
          data: function (d) {
            },
          },
          columns: [
            {
                data: 'name',
                orderable: true,
            },
            {
                data: 'email',
                orderable: true,
            },
            {
                data: 'roles',
                orderable: true,
            },
            {
                data: 'email_verified_at',
                orderable: true,
                render: function (data, type, full, meta) {
                  var $active = full['email_verified_at'];
      
                  if($active == null){
                    return '<span class="badge rounded-pill bg-label-danger">' + 'Inactive' + '</span>';
                  }else{
                    return '<span class="badge rounded-pill bg-label-success">' + 'Active' + '</span>';
                  }
                }
            },
            {
              data: 'actions',
              orderable:false,
            },
            {
              data: 'created_at',
              visible:false,
            }
          ],
          language: {
              sLengthMenu: '_MENU_',
              search: '', 
              searchPlaceholder: 'Search User',
              info: 'Displaying _START_ to _END_ of _TOTAL_ entries'
          },
          order:[4,'desc'],
          dom:
              '<"card-header d-flex rounded-0 flex-wrap py-md-0"' +
              '<"me-5 ms-n2"f>' +
              '<"d-flex justify-content-start justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex align-items-start align-items-md-center justify-content-sm-center mb-1 mb-sm-0 gap-3"lB>>' +
              '>t' +
              '<"row mx-1"' +
              '<"col-sm-12 col-md-6"i>' +
              '<"col-sm-12 col-md-6"p>' +
              '>',  
              buttons:[
                    {
                      text: '<i class="mdi mdi-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add</span>',
                      className: 'add-new btn btn-primary ms-n1 waves-effect waves-light',
                      action: function () {
                        window.location.href = addUser;
                      }
                    }
              ],
          initComplete: function () {
          }
      });

});