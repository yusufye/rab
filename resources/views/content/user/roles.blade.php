@extends('layouts/layoutMaster')

@section('title', 'Roles')

@section('content')

    @if (session()->has('success')) 
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
    @endif

    @if (session()->has('failed'))
        <div class="alert alert-danger">
            {{ session('failed') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif




   <div class="row">
        <div class="col-12">       
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-1">{{ __('Roles List') }}</h4>
                    <p class="mb-4">
                        {{ __('A role provided access to predefined menus and features so that depending on assigned role an administrator can have access to what user needs.') }}
                    </p>
                </div>
                <div class="card-body">
                    <!-- Role cards -->
                        <div class="row g-4">
                            @foreach ($roles as $role)
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                 <p>{{ __('Total :total user', ['total' => $role->users()->count()]) }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-end">
                                                <div class="role-heading">
                                                    <h4 class="mb-1 text-body">{{ $role->name }} @if($role->client_name) - {{ $role->client_name }} @endif</h4>
                                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#editRoleModal"
                                                        data-role-id="{{ $role->id }}"
                                                        class="role-edit-modal @if (!$update_roles_and_permission) btn-disabled-permission @endif"><span>
                                                            Edit Role
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="card h-100">
                                    <div class="row h-100">
                                        <div class="col-5">
                                            <div class="d-flex align-items-end h-100 justify-content-center">
                                                <img src="{{ asset('assets/img/illustrations/add-new-role-illustration.png') }}"
                                                    class="img-fluid" alt="Image" width="70">
                                            </div>
                                        </div>
                                        <div class="col-7">
                                            <div class="card-body text-sm-end text-center ps-sm-0">
                                                <button data-bs-target="#addRoleModal" data-bs-toggle="modal"
                                                    class="btn btn-primary mb-3 text-nowrap add-new-role @if (!$create_roles_and_permission) btn-disabled-permission @endif">
                                                    {{ __('Add Role') }}
                                                </button>
                                                <p class="mb-0">{{ __('Add role, if it does not exist') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h4 class="fw-medium mb-1 mt-5">{{ __('Total users with their roles') }}</h4>
                            <p class="mb-0 mt-1">{{ __('Find all of your company’s administrator accounts and their associate roles') }}.</p>

                            <div class="col-12">
                                <!-- Role Table -->
                                <div class="card">
                                    <div class="card-datatable table-responsive">
                                        <table class="datatables-users table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>{{ __('Name') }}</th>
                                                    <th>{{ __('Email') }}</th>
                                                    <th>{{ __('Role') }}</th>
                                                    <th>{{ __('Active') }}</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <!--/ Role Table -->
                            </div>
                        </div>
                        <!--/ Role cards -->
                </div>
            </div>
        </div>
   </div>

  
   <!-- Add Role Modal -->
   @include('modals/role/modal-edit-role')
   @include('modals/role/modal-add-role')
    <!-- / Add Role Modal -->
    
@endsection

@section('page-script')
    <script type="module" src="{{asset('assets/js_custom/roles.js')}}?v={{ time() }}"></script>
@endsection