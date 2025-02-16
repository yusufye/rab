@extends('layouts/layoutMaster')

@section('title', 'Order')

@section('content')

<table class="table table-bordered datatables-orders">
        <thead>
            <tr>
                <th>Job Number</th>
                <th>Status</th>
                <th>Customer</th>
                <th>Nilai Kontrak</th>
                <th>Biaya Operasional</th>
                <th>Profit</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
</table>

@endsection

@section('page-script')
<script type="module" src="{{ asset('assets/js_custom/index_order.js') }}?v={{ time() }}"></script>
@endsection