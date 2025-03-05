@extends('layouts/layoutMaster')

@section('title', 'Edit User')

@section('content')
<form action="{{url('user/'.$user->id.'/update')}}" method="POST" id="form-create-user">
    @csrf

    <div class="alert alert-danger" style="display: none;" id="alert-create-users"></div>

    @if (session()->has('success'))
        <div class="formSuccessSubmit" data-message="{{ session('success') }}"></div>
    @endif
    @if (session()->has('failed'))
        <div class="formFailedSubmit" data-message="{{ session('failed') }}"></div>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                            <a href="{{url('/user')}}">User</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Edit') }}</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center ms-auto">
                        <button type="button" class="btn btn-primary" id="button-add" title="Simpan"><span class="mdi mdi-content-save"></span></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-floating form-floating-outline">
                                <select id="division_id" class="select2 form-select required-field" name="division_id"
                                    data-placeholder="{{ __('Select Division') }}" required data-required="Division">
                                    <option value="">{{ __('Select Division') }}</option>
                                    @forelse($divisions as $div)
                                        <option value="{{$div->id}}" {{ old('division_id',$user->division_id) == $div->id ? 'selected' : '' }}>{{$div->division_name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            <label for="division" class="required">{{ __('Division') }}</label>
                        </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="nip" placeholder="{{ __('NIP') }}"
                                    name="nip" aria-label="NIP" required value="{{ old('nip',$user->nip ?? '') }}" data-required="NIP" maxlength="50">
                                <label for="nip" class="required">{{ __('NIP') }}</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control required-field" id="name" placeholder="{{ __('Name') }}"
                                name="name" aria-label="name" required value="{{ old('name',$user->name ?? '') }}" data-required="Name" maxlength="255">
                            <label for="name" class="required">{{ __('Name') }}</label>
                        </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="email" class="form-control required-field" id="email" placeholder="{{ __('Email') }}"
                                    name="email" aria-label="Email" required value="{{ old('email',$user->email ?? '') }}" data-required="NIP" maxlength="255">
                                <label for="email" class="required">{{ __('Email') }}</label>
                            </div>
                        </div>
                        <div class="col-6">
                        <div class="form-floating form-floating-outline mb-4">
                                <select id="role_id" class="select2 form-select required-field" name="role_id"
                                    data-placeholder="{{ __('Select Roles') }}" required data-required="Roles">
                                    <option value="">{{ __('Select Roles') }}</option>
                                    @forelse($roles as $r)
                                        <option value="{{ $r->id }}" 
                                            {{ (isset($user) && $user->roles->contains('id', $r->id)) || old('role_id') == $r->id ? 'selected' : '' }}>
                                            {{ $r->name }}
                                        </option>
                                    @empty
                                    @endforelse
                                </select>
                                <label for="role_id" class="required">{{ __('Role') }}</label>
                            </div>

                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</form>


@endsection

@section('page-script')
    <script type="module" src="{{ asset('assets/js_custom/create_user.js') }}?v={{ time() }}"></script>
@endsection