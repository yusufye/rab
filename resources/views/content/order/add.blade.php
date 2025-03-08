@extends('layouts/layoutMaster')

@section('title', 'Add Order')

@section('content')
<form action="{{url('order/submit')}}" method="POST" id="form-create-order">
    @csrf

    <div class="alert alert-danger" style="display: none;" id="alert-create-order"></div>

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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('/order')}}">{{ __('Order') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Add Order') }}
                                
                            </li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center ms-auto">
                        <button type="button" class="btn btn-primary" id="button-add" title="Simpan Draft"><span class="mdi mdi-content-save"></span></button>
                    </div>
                    </div>
                <div class="card-body">
                <div class="row">
                        <div class="col-6">
                            <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" maxlength="25" class="form-control" placeholder="No. Kontrak" id="contract_number" name="contract_number" />
                                    <label for="contract_number">{{ __('No. Kontrak') }}</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control format-currency" placeholder="Nilai Kontrak" id="contract_price" name="contract_price" />
                                    <label for="contract_price">{{ __('Nilai Kontrak') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="job_number" placeholder="{{ __('Job Number') }}"
                                    name="job_number" aria-label="Name" required value="{{ old('job_number') }}" data-required="Job Number">
                                <label for="job_number" class="required">{{ __('No. Job') }}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="title" placeholder="{{ __('Title') }}"
                                    name="title" aria-label="Title" required value="{{ old('title') }}" data-required="Title">
                                <label for="title" class="required">{{ __('Judul') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                            <select id="category_id" class="select2 form-select required-field" data-required="Category" name="category_id"
                                data-placeholder="{{ __('Select Category') }}" required>
                                <option value="">{{ __('Select Category') }}</option>
                                    @forelse($categorys as $c)
                                    <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->category_name }}
                                    </option>
                                    @empty
                                @endforelse
                            </select>
                            <label for="category_id" class="required">{{ __('Kategori') }}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="group" placeholder="{{ __('Group') }}"
                                    name="group" aria-label="Group" required value="{{ old('group') }}" data-required="Group">
                                <label for="group" class="required">{{ __('Kelompok') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">                       
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="customer" placeholder="{{ __('Customer') }}"
                                    name="customer" aria-label="Customer" required value="{{ old('customer') }}" data-required="Customer">
                                <label for="customer" class="required">{{ __('Pelanggan') }}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="study_lab" placeholder="{{ __('Study/Lab') }}"
                                    name="study_lab" aria-label="Study/Lab" required value="{{ old('study_lab') }}" data-required="Study/Lab">
                                <label for="study_lab" class="required">{{ __('Study/Lab') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">                        
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" data-required="Date" placeholder="YYYY-MM-DD to YYYY-MM-DD" id="date_range" name="date_range" />
                                <label for="customer" class="required">{{ __('Tanggal') }}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control format-currency" id="price" placeholder="{{ __('Price') }}"
                                    name="price" aria-label="Price" required value="{{ old('price') }}">
                                <label for="price" class="required">{{ __('Nilai Anggaran') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">       
                        <div class="col-6">
                            <div class="form-floating form-floating-outline mb-4">
                            <select id="division" class="select2 form-select" multiple name="division[]"
                                data-placeholder="{{ __('Select Division') }}" required>
                                <option value="">{{ __('Select Division') }}</option>
                                @forelse($divisions as $div)
                                    <option value="{{$div->id}}">{{$div->division_name}}</option>
                                @empty
                                @endforelse
                            </select>
                            <label for="division" class="required">{{ __('Split Ke-') }}</label>
                        </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline">
                            <select id="job_type" class="select2 form-select" name="job_type"
                                data-placeholder="{{ __('Select Job Type') }}">
                                    <option value="">{{ __('Select Job Type') }}</option>
                                    <option value="Tunggal">Tunggal</option>
                                    <option value="Retail">Retail</option>
                                    <option value="Gabungan">Gabungan</option>
                            </select>
                            <label for="job_type">{{ __('Job Type') }}</label>
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
    <script type="module" src="{{ asset('assets/js_custom/create_order.js') }}?v={{ time() }}"></script>
@endsection