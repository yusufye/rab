@extends('layouts/layoutMaster')

@section('title', 'Add MAK')

@section('content')
<form action="{{url('mak/submit')}}" method="POST" id="form-create-mak">
    @csrf

    <div class="alert alert-danger" style="display: none;" id="alert-create-mak"></div>

    @if (session()->has('success'))
        <div class="formSuccessSubmit" data-message="{{ session('success') }}"></div>
    @endif
    @if (session()->has('failed'))
        <div class="formFailedSubmit" data-message="{{ session('failed') }}">    </div>
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
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                            <a href="{{url('/mak')}}">MAK</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Add') }}</li>
                        </ol>
                    </nav>
                    </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="number" class="form-control required-field" id="mak_code" placeholder="{{ __('MAK Code') }}"
                                    name="mak_code" aria-label="Code" required value="{{ old('mak_code') }}" data-required="MAK Code">
                                <label for="mak_code" class="required">{{ __('MAK Code') }}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="mak_name" placeholder="{{ __('MAK Name') }}"
                                    name="mak_name" aria-label="Name" required value="{{ old('mak_name') }}" data-required="MAK Name">
                                <label for="mak_name" class="required">{{ __('MAK Name') }}</label>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="card-footer text-end">
                    <button type="button" class="btn btn-primary" id="button-add">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>


@endsection

@section('page-script')
    <script type="module" src="{{ asset('assets/js_custom/create_mak.js') }}?v={{ time() }}"></script>
@endsection