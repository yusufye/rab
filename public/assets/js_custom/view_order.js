

  
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

    //   button reject
    $("#button-reject").on("click", function () {

            Swal.fire({
                title: 'Are you sure you want to Reject?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Reject',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((value) => {
                if (value.isConfirmed) {
                    
                    var formData = {
                        order_id: orderId,
                        status: 'DRAFT',
                    };
        
                    $.ajax({
                        url: "/order/update_status/submit",
                        type: "POST",
                        data: formData,
                        dataType: "json",
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.msg,
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                }).then(() => {
                                    window.location.href = '/order'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.msg, 
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                            }
                        },
                        error: function (xhr) {
                            toastr.error('Something went wrong!', 'Error');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        
        
    });

    //   button relase
    $("#button-release").on("click", function () {        
        $('#modal-notes-approval').modal('show');
    });

    $("#button-save-title").on("click", function () {

        var reviewed_notes = $('#reviewed_notes').val();

        $('#modal-notes-approval').modal('hide');

            Swal.fire({
                title: 'Are you sure you want to Release?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Relase',
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((value) => {
                if (value.isConfirmed) {
                   
                    $("#button-save-title").prop('disabled',true);
                    
                    var formData = {
                        order_id: orderId,
                        status: 'REVIEWED',
                        reviewed_notes: reviewed_notes,
                    };
        
                    $.ajax({
                        url: "/order/update_status/submit",
                        type: "POST",
                        data: formData,
                        dataType: "json",
                        success: function (response) {
                            $("#button-save-title").prop('disabled',false);
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.msg,
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                }).then(() => {
                                    window.location.href = '/order'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.msg, 
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                            }
                        },
                        error: function (xhr) {
                            toastr.error('Something went wrong!', 'Error');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        
    });

     //   button reject
     $("#button-approve").on("click", function () {

        Swal.fire({
            title: 'Are you sure you want to Approve?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Approve',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((value) => {
            if (value.isConfirmed) {
                
                var formData = {
                    order_id: orderId,
                    status: 'APPROVED',
                };
    
                $.ajax({
                    url: "/order/update_status/submit",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.msg,
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            }).then(() => {
                                window.location.href = '/order'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.msg, 
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                        }
                    },
                    error: function (xhr) {
                        toastr.error('Something went wrong!', 'Error');
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    
    
    });

    // check item
    $('.check-item').on('click',function(){

        var oderItemId = $(this).data('order-item-id');

        $.ajax({
            url: "/order/get_cheklist/"+oderItemId,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $('#modal-checklist-item').modal('show');
                    $('#order_item_id').val(oderItemId);
                    $('#repeater-checklist').html('');
                    response.data.forEach(item => {
                        addRowChecklist(item.amount, item.checklist_number);
                    });

                   
                }
            },
            error: function (xhr) {
                toastr.error('Something went wrong!', 'Error');
                console.error(xhr.responseText);
            }
        });
    })

    // save checklist item
    $('#button-save-checklist').on('click', function () {
        var isValid = true;
        var checklistData = [];
        var orderItemId = $('#order_item_id').val();
    
        // Loop semua input checklist yang ada
        $('#repeater-checklist .row').each(function () {
            var checklist_number = $(this).find('[name="checklist_number[]"]').val().trim();
            var amount = $(this).find('[name="amount[]"]').val().replace(/\D/g, ''); 
    
            // Validasi: Checklist number tidak boleh kosong
            if (checklist_number === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Checklist Number tidak boleh kosong!',
                    customClass: { confirmButton: 'btn btn-success' }
                });
                isValid = false;
                return false; 
            }

            if (amount === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Amount tidak boleh kosong!',
                    customClass: { confirmButton: 'btn btn-success' }
                });
                isValid = false;
                return false; 
            }
    
            checklistData.push({
                checklist_number: checklist_number,
                amount: amount
            });
        });

        if (checklistData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Anda harus menambahkan setidaknya satu checklist!',
                customClass: { confirmButton: 'btn btn-warning' }
            });
            return;
        }
    
        // Jika ada data yang tidak valid, hentikan eksekusi
        if (!isValid) return;

        $('#button-save-checklist').prop('disabled',true);
            // Kirim data ke server menggunakan AJAX
            $.ajax({
                url: "/order/save_checklist/"+orderItemId,
                type: "POST",
                data: {               
                    checklist: checklistData
                },
                success: function (response) {
                    if (response.success) {
                        $('#modal-checklist-item').modal('hide');                        

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.msg,
                            customClass: {
                            confirmButton: 'btn btn-success'
                            }
                        }).then(() => {        
                            console.log('ini keupdate')
                            window.Livewire.dispatch('refreshOrderViewMak');
                            window.Livewire.dispatch('refreshOrderSummary');
                            window.addEventListener('forceRerender', () => {
                                location.reload();
                            });
                            
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.msg,
                            customClass: { confirmButton: 'btn btn-success' }
                        });
                    }

                    $('#button-save-checklist').prop('disabled',false);

                },
                error: function (xhr) {
                    toastr.error('Gagal menyimpan checklist.');
                    console.error(xhr.responseText);
                }
            });
     });
    
    
     // client people contact
     let rowNumCheckList = 0;
     const repeaterChecklist = document.getElementById('repeater-checklist');
     const addItemChecklist = document.getElementById('add-item-checklist');
     
     function addRowChecklist(amount = "", checklistNumber = "") {
       rowNumCheckList++;
         const newRow = document.createElement('div');
         newRow.setAttribute('data-repeater-item-contact', '');
         newRow.id = `row-checklist${rowNumCheckList}`;
         newRow.innerHTML = `
             <div class="row">
                 <div class="mb-3 col-lg-5 col-12 mb-0"> 
                     <div class="form-floating form-floating-outline">
                         <input type="text" name="checklist_number[]" class="form-control" placeholder="Checklist Number" id="checklist_number${rowNumCheckList}" maxlength="50" value="${checklistNumber}">
                         <label for="checklist_number${rowNumCheckList}">Checklist Number</label>
                     </div>
                 </div>
              
                 <div class="mb-3 col-lg-5 col-12 mb-0">
                     <div class="form-floating form-floating-outline">
                         <input type="text" name="amount[]" class="form-control format-currency" 
                         placeholder="Amount" id="amount${rowNumCheckList}" value="${amount ? Math.floor(amount) : ''}">
                         <label for="amount${rowNumCheckList}">Amount</label>
                     </div>
                 </div>
              
                 <div class="mb-3 col-lg-2 col-12 mb-0">
                     <button type="button" class="btn btn-outline-danger" onclick="deleteChecklist(${rowNumCheckList})">
                     <i class="mdi mdi-delete-alert me-1"></i>
                      </button>
                 </div>
             </div>
         `;
     
         repeaterChecklist.appendChild(newRow);
           
           const amountInput = newRow.querySelector('.format-currency');
           amountInput.addEventListener('input', function () {
               this.value = formatRupiah(this.value, 'Rp ');
           });
     
           if (amountInput.value) {
               amountInput.value = formatRupiah(amountInput.value, 'Rp ');
           }
     
     }
     
     function deleteChecklist(rowId) {
       const row = document.getElementById(`row-checklist${rowId}`);
       if (row) {
           row.remove();
       }
     }
     
     window.deleteChecklist = deleteChecklist;
     
     addItemChecklist.addEventListener('click', function() {
       addRowChecklist();
     });
     
     
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
    
});

