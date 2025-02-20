@extends('layouts/layoutMaster')

@section('title', 'Edit category')

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
<form action="{{url('category/'.$category->id.'/update')}}" method="POST" id="form-edit-category">
    @csrf


    <div class="alert alert-danger" style="display: none;" id="alert-create-category"></div>

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
                        <a href="{{url('/category')}}">Category</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="category_name" placeholder="{{ __('Category Name') }}"
                                    name="category_name" aria-label="category_name" required value="{{ old('name',$category->category_name??'') }}" data-required="category_name">
                                <label for="category_name" class="required">{{ __('Category Name') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="button" class="btn btn-primary" id="button-edit">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection


@section('page-script')
    <script type="module">
        window.categoryId = @json($category->id);
    </script>
    <script type="module" src="{{ asset('assets/js_custom/edit_category.js') }}?v={{ time() }}"></script>
@endsection