@extends('layouts/layoutMaster')

@section('title', 'MAK')

@section('content')

<div class="card">
<div class="card-datatable table-responsive">
    <table class="table datatables-orders dtr-column">
        <thead class="table-primary">
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
</table>
</div>
</div>

@endsection

@section('page-script')
<script type="module" src="{{ asset('assets/js_custom/index_mak.js') }}?v={{ time() }}"></script>
@endsection