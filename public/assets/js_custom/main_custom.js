$(document).ready(function() {
    var select2 = $('.select2');

    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            select2Focus($this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                dropdownParent: $this.parent()
            });
        });
    }

    $('.format-currency').each(function () {
        const rawValue = $(this).val();
        if (rawValue) {
            $(this).val(formatRupiah(rawValue, 'Rp '));
        }
    });   
    
    $('.format-currency').on('input', function () {
        const rawValue = $(this).val(); 
        $(this).val(formatRupiah(rawValue, 'Rp '));
    });
});

 
 
 
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

