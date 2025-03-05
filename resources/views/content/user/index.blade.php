@extends('layouts/layoutMaster')

@section('title', 'User')

@section('content')

<div class="card">
<div class="card-datatable table-responsive">
<table class="datatables-users table">
    <thead class="table-light">
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Role') }}</th>
            <th>{{ __('Active') }}</th>
            <th>{{ __('Actions') }}</th>
            <th style="display: none;"></th>
        </tr>
    </thead>
</table>
</div>
</div>

@endsection

@section('page-script')
<script type="module" src="{{ asset('assets/js_custom/index_user.js') }}?v={{ time() }}"></script>
@endsection