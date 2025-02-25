/**
 * Page User List
 */

'use strict';

// Datatable (jquery)
$(function () {
  let borderColor, bodyBg, headingColor;

  if (isDarkStyle) {
    borderColor = config.colors_dark.borderColor;
    bodyBg = config.colors_dark.bodyBg;
    headingColor = config.colors_dark.headingColor;
  } else {
    borderColor = config.colors.borderColor;
    bodyBg = config.colors.bodyBg;
    headingColor = config.colors.headingColor;
  }

     // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

});




/**
 * Add new role Modal JS
 */

const selectAllCheckboxEditRole = document.querySelector('.selectAllCheckboxEditRole');
const selectAllCheckboxAddRole = document.querySelector('.selectAllCheckboxAddRole');
const checkboxListEditRole = document.querySelectorAll('[type="checkbox"]:not(.selectAllCheckboxEditRole)');
const checkboxListAddRole = document.querySelectorAll('[type="checkbox"]:not(.selectAllCheckboxAddRole)');

selectAllCheckboxEditRole.addEventListener('change',function(event) {
    checkboxListEditRole.forEach(function(checkbox) {
        checkbox.checked = event.target.checked;
    });
});

selectAllCheckboxAddRole.addEventListener('change', function(event) {
    checkboxListAddRole.forEach(function(checkbox) {
        checkbox.checked = event.target.checked;
    });
});

$('.formSuccessSubmit').each(function () {
  var message = $(this).data('message');
  if (message) {
      Swal.fire({
          icon: 'success',
          title: `Successfully ${message}!`,
          text: `${message} Successfully.`,
          customClass: {
              confirmButton: 'btn btn-success'
          }
      });
  }
});

$('.formGagalSubmit').each(function () {
  var message = $(this).data('message');
  if (message) {
      Swal.fire({
          icon: 'error',
          title: `Cancelled`,
          text: `${message}`,
          customClass: {
              confirmButton: 'btn btn-success'
          }
      });
  }
});

$('.role-edit-modal').on('click', function() {
  var roleId = $(this).data('role-id');

  $.ajax({
      url: '/roles-and-permission',
      method: 'GET',
      data: { roleId: roleId },
      success: function(response) {
          if(response){
              $('#role_id').val(response.roles[0].id);
              $('#modalRoleNameEdit').val(response.roles[0].name);
              $.each(response.permissions_data, function(index, permission) {
                  var permissionName = permission.name.toLowerCase();
                  $('input[value="' + permissionName + '"]').prop('checked', true);
              });
          }
      },
      error: function(xhr, status, error) {
          console.error('Terjadi kesalahan:', error);
      }
  });
});


 
$(document).ready(function() {
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
                data: 'roles',
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
          ],
          language: {
              sLengthMenu: '_MENU_',
              search: '', 
              searchPlaceholder: 'Search User',
              info: 'Displaying _START_ to _END_ of _TOTAL_ entries'
          },
          order:[1,'desc'],
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
          ],
          initComplete: function () {
          }
      });

});