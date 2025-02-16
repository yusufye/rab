<!-- BEGIN: Vendor JS-->

@vite([
'resources/assets/vendor/libs/jquery/jquery.js',
'resources/assets/vendor/libs/popper/popper.js',
'resources/assets/vendor/js/bootstrap.js',
'resources/assets/vendor/libs/node-waves/node-waves.js',
'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
'resources/assets/vendor/libs/hammer/hammer.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/js/menu.js'])

@yield('vendor-script')
<!-- END: Page Vendor JS-->
 <script type="module" src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
 <script type="module" src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
 <script type="module" src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
 <script type="module" src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
 <script type="module" src="{{asset('assets/vendor/libs/toastr/toastr.js') }}"></script>

 <script type="module" src="{{ asset('assets/js_custom/main_custom.js') }}?v={{ time() }}"></script>
<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/main.js'])

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->

@if (Session::has('success'))
    <script type="module">
        toastr.options.progressBar = true;
        toastr.options.showMethod = 'slideDown';
        toastr.options.hideMethod = 'slideUp';
        toastr.info("{{ Session::get('success') }}")
    </script>
@endif

@if (Session::has('failed'))
    <script type="module">
        toastr.options.progressBar = true;
        toastr.options.showMethod = 'slideDown';
        toastr.options.hideMethod = 'slideUp';
        toastr.options.escapeHtml = false;
        var errorMessage = @json(Session::get('failed'));
        toastr.error(errorMessage)
    </script>
@endif
