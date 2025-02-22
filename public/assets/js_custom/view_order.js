
  
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
    
});