$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  disableRemoveButton(); 
  loadDivisions();

  // Cegah penghapusan opsi yang disabled dengan Backspace
  $('#division').on('select2:unselecting', function(e) {
      var selectedOption = $(e.params.args.data.element);
      
      if (selectedOption.is('[data-disabled-custom]')) {
          e.preventDefault(); 
      }
  });

  // Jalankan ulang fungsi setiap kali ada perubahan
  $('#division').on('select2:select select2:unselect', function() {
      setTimeout(disableRemoveButton, 1);
  });

  if (statusOrder !== 'DRAFT') {
    $('button:not(#modal_status_order button)')
      .prop('disabled', true)
      .on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
      });
  }

  $('#date_range').flatpickr({
    monthSelectorType: 'static',
    mode: 'range',
    defaultDate: dateFrom && dateTo ? [dateFrom, dateTo] : null,
    dateFormat: 'd-M-Y'
  });

  $('#button-batal').on('click', function () {
    var isValid = true;

    if (!validateRequiredFields()) {
      isValid = false;
    }

    if (isValid) {
      Swal.fire({
        title: '<h4>Apakah anda yakin membatalkan dokumen order ini?</h4>',
        // footer: 'Setelah simpan draft berhasil, anda masih dapat merubah kembali dokumen order ini',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Save',
        customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-danger'
        },
        buttonsStyling: false
      }).then(value => {
        if (value.isConfirmed) {
          var formData = {
            order_id: orderId,
            status: 'CANCELLED'
          };
  
          $.ajax({
            url: '/order/update_status/submit',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
              if (response.success) {
                localStorage.setItem('toastrMessage', response.msg);
                localStorage.setItem('toastrType', 'info'); 

                window.location.href = '/order';
              } else {
                toastrError(response.msg);
              }
            },
            error: function (xhr) {
              toastrError('Something went wrong!');
              console.error(xhr.responseText);
            }
          });
        }
      });
    }
  });

  $('#button-edit').on('click', function () {
    var isValid = true;

    if (!validateRequiredFields()) {
      isValid = false;
    }

    if (isValid) {
      Swal.fire({
        title: '<h4>Apakah anda yakin akan menyimpan dokumen order ini sebagai draft ?</h4>',
        footer: 'Setelah simpan draft berhasil, anda masih dapat merubah kembali dokumen order ini',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Save',
        customClass: {
          confirmButton: 'btn btn-secondary me-3',
          cancelButton: 'btn btn-label-danger'
        },
        buttonsStyling: false
      }).then(value => {
        if (value.isConfirmed) {
          $('#form-edit-order').trigger('submit');
        }
      });
    }
  });

  $('#button-to-review').on('click', function () {
    Swal.fire({
      title: '<h4>Apakah anda yakin akan mengirim dokumen order ini ?</h4>',
      footer: 'Setelah terkirim, dokumen order ini akan diteruskan ke reviewer',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Submit',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-danger'
      },
      buttonsStyling: false
    }).then(value => {
      if (value.isConfirmed) {
        var formData = {
          order_id: orderId,
          status: 'TO REVIEW'
        };

        $.ajax({
          url: '/order/update_status/submit',
          type: 'POST',
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.success) {
              $('#button-save-item').prop('disabled', false);
              $('#add-item-modal').modal('hide');          
              
              localStorage.setItem('toastrMessage', response.msg);
              localStorage.setItem('toastrType', 'info'); 
              window.location.href = '/order';
              
            } else {
              toastr.error(response.msg, 'Error');
            }
            
            
            
          },
          error: function (xhr) {
            $('#button-save-item').prop('disabled', false);
            toastr.error('Something went wrong!', 'Error');
            console.error(xhr.responseText);
          }
        });
      }
    });
  });

  // order mak
  $('#add-mak').on('click', function () {
    $('#add-mak-modal').modal('show');
    $('#order_id').val(orderId);
    $('#type_form').val(1);

    $('#mak').val(-1).trigger('change');
    $('#mak').next('.select2-container').removeClass('is-invalid'); 
    $('#order_mak_id').val('');
    $('#is_split').prop('checked', false);
    $('#split_to').val(-1).trigger('change');

    $('#div_split_to').hide();

    $('#is_split').on('change', function () {
      if ($(this).prop('checked')) {
        $('#div_split_to').show();
      } else {
        $('#div_split_to').hide();
      }
    });
  });

  $(document).on('click', '.edit-mak', function () {
    var orderMakId = $(this).data('order-mak-id');
    $('#mak').next('.select2-container').removeClass('is-invalid'); 

    $.ajax({
      url: '/get_order_mak/'+orderMakId,
      type: 'GET',
      dataType: 'json',
      success: function (response) {
        $('#add-mak-modal').modal('show');
        $('#order_id').val(response.orderMak.order_id??'');
        $('#mak').val(response.orderMak.mak_id??'').trigger('change');
        $('#type_form').val(0);
        $('#order_mak_id').val(response.orderMak.id??'');
        $('#is_split').prop('checked', response.orderMak.is_split === 1);

        if (response.orderMak.is_split == 1) {
          $('#div_split_to').show();
          $('#split_to').val(response.orderMak.split_to).trigger('change');
        } else {
          $('#div_split_to').hide();
          $('#split_to').val(-1).trigger('change');
        }

        $('#is_split').on('change', function () {
          if ($(this).prop('checked')) {
            $('#div_split_to').show();
          } else {
            $('#div_split_to').hide();
            $('#split_to').val(-1).trigger('change');
          }
        });

      },
      error: function (xhr) {
        toastr.error('Something went wrong!', 'Error');
        console.error(xhr.responseText);
      }
    });

 
  });

  $('#button-save-mak').on('click', function () {
    var isValid = true;

    if ($('#mak').val() === '' || $('#mak').val() === null) {
      $('#mak').next('.select2-container').addClass('is-invalid'); 
      toastrError('Mak tidak boleh kosong');
      isValid = false;
    }else{
      $('#mak').next('.select2-container').removeClass('is-invalid');
    }
    

    if (isValid) {
      var formData = {
        order_id: $('#order_id').val(),
        mak: $('#mak').val(),
        is_split: $('#is_split').is(':checked') ? 1 : 0,
        split_to: $('#split_to').val() || [],
        type_form: $('#type_form').val(),
        order_mak_id: $('#order_mak_id').val() ?? null
      };
      $('#button-save-mak').prop('disabled', true);

      $.ajax({
        url: '/order/mak/submit',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
           
          window.Livewire.dispatch('refreshPercentage');
          window.Livewire.dispatch('refreshOrderMak');
          window.Livewire.dispatch('refreshOrderSummary');
          loadDivisions();

          if (response.success) {
            $('#button-save-mak').prop('disabled', false);

            $('#add-mak-modal').modal('hide');        

            toastrSuccess(response.msg);
            
          } else {
            $('#button-save-mak').prop('disabled', false);
            toastrError(response.msg);
          }
        },
        error: function (xhr) {
          $('#button-save-mak').prop('disabled', false);
          toastrError('Something went wrong!');
          console.error(xhr.responseText);
        }
      });
    }
  });
  // order mak

  // order title
  $(document).on('click', '.add-title', function () {
    var orderMakId = $(this).data('order-mak-id');
    var dataMak = $(this).data('mak');
    $('#add-title-modal').modal('show').attr("aria-hidden", "false");
    $('#type_form_title').val(1);
    $('#order_label_mak').html(`Mak: ${dataMak}`);
    $('#order_title_mak_id').val(orderMakId);
    $('#order_title_id').val('');
    $('#order_title').val('');

    $('#order_title').removeClass('is-invalid'); 
  });

  $(document).on('click', '.edit-title', function () {
    var orderTitleId = $(this).data('order-title-id');
    $('#order_title').removeClass('is-invalid'); 

    $.ajax({
      url: '/get_order_title/'+orderTitleId,
      type: 'GET',
      dataType: 'json',
      success: function (response) {
        $('#add-title-modal').modal('show').attr("aria-hidden", "false");
        $('#type_form_title').val(0);
        $('#order_label_mak').html(`Mak: ${response.orderTitle.order_mak?.mak?.mak_name??''}`);
        $('#order_title_mak_id').val(response.orderTitle?.order_mak_id??'');
        $('#order_title_id').val(response.orderTitle?.id??'');
        $('#order_title').val(response.orderTitle?.title??'');

      },
      error: function (xhr) {
        toastr.error('Something went wrong!', 'Error');
        console.error(xhr.responseText);
      }
    });

  });

  $('#button-save-title').on('click', function () {
    var isValid = true;

    if ($('#order_title').val() === '' || $('#order_title').val() === null) {
      $('#order_title').addClass('is-invalid'); 
      toastrError('Title tidak boleh kosong');
      isValid = false;
    }else{
      $('#order_title').removeClass('is-invalid'); 
    }

    if (isValid) {
      var formData = {
        type_form: $('#type_form_title').val(),
        order_mak_id: $('#order_title_mak_id').val(),
        order_title: $('#order_title').val(),
        order_title_id: $('#order_title_id').val()
      };
      $('#button-save-title').prop('disabled', true);

      $.ajax({
        url: '/order/title/submit',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            $('#button-save-title').prop('disabled', false);
            $('#add-title-modal').modal('hide');

            toastrSuccess(response.msg);

            window.Livewire.dispatch('refreshPercentage');
            window.Livewire.dispatch('refreshOrderMak');
            window.Livewire.dispatch('refreshOrderSummary');

          } else {
            toastrError(response.msg);
          }
        },
        error: function (xhr) {
          $('#button-save-title').prop('disabled', false);
          toastrError('Something went wrong!');
          console.error(xhr.responseText);
        }
      });
    }
  });
  // order title

  // order item
  $(document).on('click', '.add-item', function () {
    var orderTitleId = $(this).data('order-mak-id');
    var dataMak = $(this).data('mak');
    var dataTitle = $(this).data('title');

    $('#add-item-modal').modal('show').removeAttr("aria-hidden");;

    $('#order_label_mak_item').html(`Mak: ${dataMak}`);
    $('#order_label_title').html(`Title: ${dataTitle}`);

    $('#order_item').removeClass('is-invalid');
    $('#order_item_qty_1').removeClass('is-invalid');
    $('#order_item_qty_2').removeClass('is-invalid');
    $('#order_item_qty_3').removeClass('is-invalid');
    $('#qty_unit').removeClass('is-invalid');


    $('#type_form_item').val(1);
    $('#order_title_id_item').val(orderTitleId);
    $('#order_item_id').val('');
    $('#order_item').val('');
    $('#order_item_qty_1').val('');
    $('#order_item_unit_1').val('');
    $('#order_item_qty_2').val('');
    $('#order_item_unit_2').val('');
    $('#order_item_qty_3').val('');
    $('#order_item_unit_3').val('');
    $('#qty_total').val('');
    $('#qty_unit').val('');
    $('#price_unit').val('');
    $('#total_price').val('');
  });

  $(document).on('click', '.edit-item', function () {
    var orderItemId = $(this).data('order-item-id');
    $('#order_item').removeClass('is-invalid');
    $('#order_item_qty_1').removeClass('is-invalid');
    $('#order_item_qty_2').removeClass('is-invalid');
    $('#order_item_qty_3').removeClass('is-invalid');
    $('#qty_unit').removeClass('is-invalid');


    $.ajax({
      url: '/get_order_item/'+orderItemId,
      type: 'GET',
      dataType: 'json',
      success: function (response) {
        $('#add-item-modal').modal('show').removeAttr("aria-hidden");;

        $('#order_label_mak_item').html(`Mak: ${response.orderItem?.order_title?.order_mak?.mak?.mak_name??''}`);
        $('#order_label_title').html(`Title: ${response.orderItem?.order_title?.title??''}`);

        $('#type_form_item').val(0);
        $('#order_title_id_item').val(response.orderItem?.title_id??'');
        $('#order_item_id').val(response.orderItem?.id??'');
        $('#order_item').val(response.orderItem?.item??'');
        $('#order_item_qty_1').val(response.orderItem?.qty_1??'');
        $('#order_item_unit_1').val(response.orderItem?.unit_1??'');
        $('#order_item_qty_2').val(response.orderItem?.qty_2??'');
        $('#order_item_unit_2').val(response.orderItem?.unit_2??'');
        $('#order_item_qty_3').val(response.orderItem?.qty_3??'');
        $('#order_item_unit_3').val(response.orderItem?.unit_3??'');
        $('#qty_total').val(response.orderItem?.qty_total??'');
        $('#qty_unit').val(response.orderItem?.qty_unit??'');

        var dataPriceUnit = Math.floor(response.orderItem?.price_unit??'0');
        var dataTotalPrice = Math.floor(response.orderItem?.total_price??'0');
        var totalDataPriceUnitFormated = 'Rp ' + dataPriceUnit.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        var totalDataTotalPrice = 'Rp ' + dataTotalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        $('#price_unit').val(totalDataPriceUnitFormated);
        $('#total_price').val(totalDataTotalPrice);

      },
      error: function (xhr) {
        toastr.error('Something went wrong!', 'Error');
        console.error(xhr.responseText);
      }
    });

   
  });

  $('#order_item_qty_1, #order_item_qty_2, #order_item_qty_3').on('change keyup', updateTotalQty);

  $('#price_unit').on('change keyup', function () {
    // var rawValue = $(this).val();
    // var cleanValue = rawValue.replace(/\D/g, '');

    updateTotalPrice();
  });

  $('#button-save-item').on('click', function () {
    var isValid = true;

    if ($('#order_item').val() === '' || $('#order_item').val() === null) { 
      $('#order_item').addClass('is-invalid');   
      toastrError('Item tidak boleh kosong');
      isValid = false;
    }else{
      $('#order_item').removeClass('is-invalid');   
    }

    var qty1 = $('#order_item_qty_1').val();

    var valQty1 = isValidNumber(qty1);

    if (qty1 !== '' && !valQty1) {
      $('#order_item_qty_1').addClass('is-invalid');   
      toastrError('Qty 1 tidak boleh minus (-)');
      isValid = false;
    }else{
      $('#order_item_qty_1').removeClass('is-invalid');   
    }

    var qty2 = $('#order_item_qty_2').val();

    var valQty2 = isValidNumber(qty2);

    if (qty2 !== '' && !valQty2) {
      $('#order_item_qty_2').addClass('is-invalid');   
      toastrError('Qty 2 tidak boleh minus (-)');
      isValid = false;
    }else{
      $('#order_item_qty_2').removeClass('is-invalid');   
    }

    var qty3 = $('#order_item_qty_3').val();

    var valQty3 = isValidNumber(qty3);

    if (qty3 !== '' && !valQty3) {
      $('#order_item_qty_3').addClass('is-invalid');   
      toastrError('Qty 3 tidak boleh minus (-)');
      isValid = false;
    }else{
      $('#order_item_qty_3').removeClass('is-invalid');   
    }

    if(qty1 === '' && qty2 === '' && qty3 === ''){  
      $('#order_item_qty_1').addClass('is-invalid');   
      $('#order_item_qty_2').addClass('is-invalid');   
      $('#order_item_qty_3').addClass('is-invalid');   
      toastrError('Masukan minimal satu Qty');
      isValid = false;
    }else{
      $('#order_item_qty_1').removeClass('is-invalid');   
      $('#order_item_qty_2').removeClass('is-invalid');   
      $('#order_item_qty_3').removeClass('is-invalid'); 
    }

    if($('#qty_unit').val() === ''){
      $('#qty_unit').addClass('is-invalid');   
      toastrError('Unit tidak boleh kosong');
      isValid = false;
    }else{
      $('#qty_unit').removeClass('is-invalid');     
    }

    if (isValid) {
      var formData = {
        type_form: $('#type_form_item').val(),
        order_item_id: $('#order_item_id').val(),
        order_title_id: $('#order_title_id_item').val(),
        order_item: $('#order_item').val(),
        order_item_qty_1: $('#order_item_qty_1').val(),
        order_item_unit_1: $('#order_item_unit_1').val(),
        order_item_qty_2: $('#order_item_qty_2').val(),
        order_item_unit_2: $('#order_item_unit_2').val(),
        order_item_qty_3: $('#order_item_qty_3').val(),
        order_item_unit_3: $('#order_item_unit_3').val(),
        qty_total: $('#qty_total').val(),
        qty_unit: $('#qty_unit').val(),
        price_unit: $('#price_unit').val(),
        total_price: $('#total_price').val()
      };

      $('#button-save-item').prop('disabled', true);

      $.ajax({
        url: '/order/item/submit',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
          
          window.Livewire.dispatch('refreshPercentage');
          window.Livewire.dispatch('refreshOrderMak');
          window.Livewire.dispatch('refreshOrderSummary');

          if (response.success) {
            $('#button-save-item').prop('disabled', false);
            $('#add-item-modal').modal('hide');          

            toastrSuccess(response.msg);  

          
           
          } else {
            $('#button-save-item').prop('disabled', false);
            toastrError(response.msg);
          }

        },
        error: function (xhr) {
          $('#button-save-item').prop('disabled', false);
          toastrError('Something went wrong!');
          console.error(xhr.responseText);
        }
      });
    }
  });




});

function validateRequiredFields() {
  var isValid = true;
  var errorMessages = [];

  $('.required-field').each(function () {
    var id_field = $(this).attr('id');
    if ($(this).val().trim() === '') {
      var message = $(this).data('required') + ' tidak boleh kosong';
      errorMessages.push(message);

      $('#' + id_field).addClass('is-invalid');
      $('#' + id_field)
        .next('.invalid-feedback')
        .show();

      isValid = false;
    } else {
      $('#' + id_field).removeClass('is-invalid');
      $('#' + id_field)
        .next('.invalid-feedback')
        .hide();
    }
  });

  if (!isValid) {
    $('#alert-create-order').html(errorMessages.join('<br>')).show();
  } else {
    $('#alert-create-order').hide();
  }

  return isValid;
}

// format rupiah
function formatRupiah(value, prefix) {
  const numberString = value.replace(/\D/g, ''); // Hanya ambil angka

  const split = numberString.split(',');

  let sisa = split[0].length % 3;
  let rupiah = split[0].substr(0, sisa);
  const ribuan = split[0].substr(sisa).match(/\d{3}/g);

  if (ribuan) {
    const separator = sisa ? '.' : '';
    rupiah += separator + ribuan.join('.');
  }

  if (split[1]) {
    const decimalPart = split[1].length > 2 ? split[1].substr(0, 2) : split[1];
    return prefix ? prefix + rupiah + ',' + decimalPart : rupiah + ',' + decimalPart;
  }

  return prefix ? prefix + rupiah : rupiah;
}

function updateTotalQty() {
  var qty1 = Number($('#order_item_qty_1').val()) || 0;
  var qty2 = Number($('#order_item_qty_2').val()) || 0;
  var qty3 = Number($('#order_item_qty_3').val()) || 0;

  var filledValues = [qty1, qty2, qty3].filter(qty => qty > 0);

  var qty_total = filledValues.length > 0 ? filledValues.reduce((a, b) => a * b, 1) : 0;

  $('#qty_total').val(qty_total);

  updateTotalPrice(); // Hitung ulang total harga
}

// Event listener agar perubahan langsung terdeteksi
$('#order_item_qty_1, #order_item_qty_2, #order_item_qty_3').on('input', updateTotalQty);


function updateTotalPrice() {
  var qty_total = parseInt($('#qty_total').val()) || 0;
  var price_unit = parseInt($('#price_unit').val().replace(/\D/g, '')) || 0;

  var total_price = qty_total * price_unit;

  var totalFormatted = 'Rp ' + total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  $('#total_price').val(totalFormatted);
}

function isValidNumber(input) {
  return /^[0-9]+$/.test(input);
}

function disableRemoveButton() {
  $('.select2-selection__choice').each(function() {
      var $choice = $(this);
      var choiceTitle = $choice.attr('title').trim();

      $('#division option[data-disabled-custom]').each(function() {
          var disabledOptionText = $(this).text().trim();

          if (choiceTitle === disabledOptionText) {
              $choice.attr('data-disabled-custom', 'true');
              $choice.find('.select2-selection__choice__remove').remove();
          }
      });
  });
}

function loadDivisions() {
  $.ajax({
      url: "/order/"+orderId+"/get_divisions",
      type: "GET",
      dataType: "json",
      success: function(response) {
          let divisions = response.divisions;
          let split_to_mak = response.split_to_mak;
          let selected_divisions = response.selected_divisions.map(String); // Pastikan jadi string

          let selectHTML = `<option value="">Select Division</option>`;

          divisions.forEach(div => {
              let isDisabled = split_to_mak.includes(div.id) ? 'data-disabled-custom="true"' : '';
              let isSelected = selected_divisions.includes(div.id.toString()) ? 'selected' : '';

              selectHTML += `<option value="${div.id}" ${isSelected} ${isDisabled}>${div.division_name}</option>`;
          });

          // Update select dengan opsi terbaru
          $('#division').html(selectHTML);

          // Set kembali nilai yang sudah terpilih
          $('#division').val(selected_divisions).trigger('change');

          // Inisialisasi ulang Select2
          $('#division').select2();

          // Pastikan opsi yang dikunci tidak bisa dihapus
          disableRemoveButton();
      }
  });
}

function toastrSuccess(message){
  toastr.options = {
    progressBar: true,
    showMethod: 'slideDown',
    hideMethod: 'slideUp'
  };

  toastr.success(message);
}

function toastrError(message){
  toastr.options = {
    progressBar: true,
    showMethod: 'slideDown',
    hideMethod: 'slideUp'
  };

  toastr.error(message);
}

window.deleteMak = function(makId) {
  console.log("Menghapus ID:", makId);

  Swal.fire({
      title: 'Konfirmasi Penghapusan?',
      text: 'Data ini dihapus secara permanen.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal',
      customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
  }).then(value => {
      if (value.isConfirmed) {
          $.ajax({
              url: '/order/mak/delete',
              type: 'POST',
              data: { id: makId },
              dataType: 'json',
              success: function (response) {
                  if (response.success) {
                      toastrSuccess(response.msg);
                      window.Livewire.dispatch('refreshPercentage');
                      window.Livewire.dispatch('refreshOrderMak');
                      window.Livewire.dispatch('refreshOrderSummary');
                      loadDivisions();
                  } else {
                      toastrError(response.msg);
                  }
              },
              error: function (xhr) {
                  toastrError('Terjadi kesalahan saat menghapus data!');
                  console.error(xhr.responseText);
              }
          });
      }
  });
}

// Delete Order Title
window.deleteTitle = function (titleId) {
  Swal.fire({
      title: 'Konfirmasi Penghapusan?',
      text: 'Data ini akan dihapus secara permanen.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: 'Batal',
      customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
  }).then(value => {
      if (value.isConfirmed) {
          $.ajax({
              url: '/order/title/delete',
              type: 'POST',
              data: { id: titleId },
              dataType: 'json',
              success: function (response) {
                  if (response.success) {
                      toastrSuccess(response.msg);
                      window.Livewire.dispatch('refreshPercentage');
                      window.Livewire.dispatch('refreshOrderMak');
                      window.Livewire.dispatch('refreshOrderSummary');
                  } else {
                      toastrError(response.msg);
                  }
              },
              error: function (xhr) {
                  toastrError('Terjadi kesalahan saat menghapus data!');
                  console.error(xhr.responseText);
              }
          });
      }
  });
}

// Delete Order Item
window.deleteItem = function (itemId) {
  Swal.fire({
      title: 'Konfirmasi Penghapusan?',
      text: 'Data ini akan dihapus secara permanen.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: 'Batal',
      customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
  }).then(value => {
      if (value.isConfirmed) {
          $.ajax({
              url: '/order/item/delete',
              type: 'POST',
              data: { id: itemId },
              dataType: 'json',
              success: function (response) {
                  if (response.success) {
                      toastrSuccess(response.msg);
                      window.Livewire.dispatch('refreshPercentage');
                      window.Livewire.dispatch('refreshOrderMak');
                      window.Livewire.dispatch('refreshOrderSummary');
                  } else {
                      toastrError(response.msg);
                  }
              },
              error: function (xhr) {
                  toastrError('Terjadi kesalahan saat menghapus data!');
                  console.error(xhr.responseText);
              }
          });
      }
  });
}
