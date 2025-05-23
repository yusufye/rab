<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet">

@vite(['resources/assets/vendor/fonts/materialdesignicons.scss',
'resources/assets/vendor/fonts/flag-icons.scss'])
<!-- Core CSS -->
@vite(['resources/assets/vendor/scss'.$configData['rtlSupport'].'/core' .($configData['style'] !== 'light' ? '-' . $configData['style'] : '') .'.scss',
'resources/assets/vendor/scss'.$configData['rtlSupport'].'/' .$configData['theme'] .($configData['style'] !== 'light' ? '-' . $configData['style'] : '') .'.scss',
'resources/assets/css/demo.css'])


<!-- Vendor Styles -->
@vite(['resources/assets/vendor/libs/node-waves/node-waves.scss',
'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss'])
@yield('vendor-style')

{{-- DataTables --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}"/>
<link rel="stylesheet" href="{{asset('assets/vendor/libs/toastr/toastr.css')}}"/>

<!-- Page Styles -->
@yield('page-style')
