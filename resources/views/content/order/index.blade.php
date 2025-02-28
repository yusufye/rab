@extends('layouts/layoutMaster')

@section('title', 'Order')

<style>
    .btn-disabled {
    pointer-events: none;
    opacity: 0.5;
    cursor: default;
}

</style>

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <div class="row">
                <div class="col-4">
                    <div class="form-floating form-floating-outline">
                    <select id="status" class="select2 form-select" multiple name="status[]"
                        data-placeholder="{{ __('Select Status') }}" required>
                        <option value="">{{ __('Select Status') }}</option>
                        <option value="DRAFT">DRAFT</option>
                        <option value="TO REVIEW">TO REVIEW</option>
                        <option value="REVIEWED">REVIEWED</option>
                        <option value="APPROVED">APPROVED</option>
                        <option value="CLOSED">CLOSED</option>
                        <option value="REVISED">REVISED</option>

                    </select>
                    <label for="status">{{ __('Status') }}</label>
                    </div>
                </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <table class="table datatables-orders">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center" style="display: none;"></th>
                                <th class="text-start">Job Number</th>
                                <th class="text-center">Status</th>
                                <th class="text-start">Customer</th>
                                <th class="text-end">Nilai Anggaran</th>
                                <th class="text-end">Biaya Operasional</th>
                                <th class="text-end">Profit</th>
                                <th class="text-center">Actions</th>
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