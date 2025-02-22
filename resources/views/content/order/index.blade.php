@extends('layouts/layoutMaster')

@section('title', 'Order')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered datatables-orders">
                        <thead>
                            <tr>
                                <th style="display: none;"></th>
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
        
            </div>
        </div>

    </div>
</div>

@endsection

@section('page-script')

<script type="module">
   window.isAdmin = @json($isAdmin);
   window.isSuperAdmin = @json($isSuperAdmin);
</script>


<script type="module" src="{{ asset('assets/js_custom/index_order.js') }}?v={{ time() }}"></script>
@endsection