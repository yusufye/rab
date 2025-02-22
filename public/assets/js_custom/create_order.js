
  
$(document).ready(function() {

    
    $('.formSuccessSubmit').each(function () {
        var message = $(this).data('message');
        if (message) {
          Swal.fire({
            icon: 'success',
            title: `Sukses`,
            text: `${message}.`,
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        }
    });

    $('.formFailedSubmit').each(function () {
        var message = $(this).data('message');
        if (message) {
          Swal.fire({
            icon: 'danger',
            title: `Gagal`,
            text: `${message}.`,
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        }
    });

    $('#date_range').flatpickr({
        monthSelectorType: "static",
        mode: "range",
        defaultDate: [new Date(), new Date()],
        dateFormat: "d-M-Y",
    });

    $("#button-add").on("click", function () {

        var isValid = true;

        if (!validateRequiredFields()) {
            isValid = false;
        }
       
        if(isValid){
            Swal.fire({
                title: 'Are you sure?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Save',
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((value) => {
                if (value.isConfirmed) {
                    $('#form-create-order').trigger('submit');
                }
            });
            
        }
    });
    
    
    
    
    

});

function validateRequiredFields() {
    var isValid = true;
    var errorMessages = [];

    $(".required-field").each(function () {
        var id_field = $(this).attr("id");
        if ($(this).val().trim() === "") {
            var message = $(this).data("required") + " tidak boleh kosong";
            errorMessages.push(message);

            $("#" + id_field).addClass("is-invalid"); 
            $("#" + id_field).next(".invalid-feedback").show(); 

            isValid = false;
        } else {
            $("#" + id_field).removeClass("is-invalid"); 
            $("#" + id_field).next(".invalid-feedback").hide(); 
        }
    });

    if (!isValid) {
        $("#alert-create-order").html(errorMessages.join("<br>")).show();
    } else {
        $("#alert-create-order").hide(); 
    }

    return isValid;
}
