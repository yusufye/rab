@extends('layouts/layoutMaster')

@section('title', 'Edit mak')

<style>
    .readonly {
        pointer-events: none; 
        background-color: #e9ecef;
        color: #6c757d;
        opacity: 0.7; 
        cursor: not-allowed; 
    }
</style>

@section('content')
<form action="{{url('mak/'.$mak->id.'/update')}}" method="POST" id="form-edit-mak">
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


    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                        <a href="{{url('/mak')}}">MAK</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center ms-auto">
                    <button type="button" class="btn btn-primary" id="button-edit" title="Simpan"><span class="mdi mdi-content-save"></span></button>
                </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                    <input type="number" class="form-control required-field" id="mak_code" placeholder="{{ __('MAK Code') }}"
                                        name="mak_code" aria-label="Code" required value="{{ old('mak_code', $mak->mak_code ?? '') }}" data-required="Job Number">
                                <label for="mak_code" class="required">{{ __('MAK Code') }}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="mak_name" placeholder="{{ __('MAK Name') }}"
                                    name="mak_name" aria-label="mak_name" required value="{{ old('name',$mak->mak_name??'') }}" data-required="mak_name">
                                <label for="mak_name" class="required">{{ __('MAK Name') }}</label>
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
    <script type="module">
        window.makId = @json($mak->id);
    </script>
    <script type="module" src="{{ asset('assets/js_custom/edit_mak.js') }}?v={{ time() }}"></script>
@endsection