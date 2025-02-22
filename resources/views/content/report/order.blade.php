@extends('layouts/layoutMaster')

@section('title', 'Order Report')
@section('content')
<form action="{{url('report/show')}}" method="POST" id="form-report">
    @csrf

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Order Report') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                            <div class="col">
                                <div class="form-floating form-floating-outline mb-4">
                                    <select id="category_id" class="select2 form-select" name="division"
                                        data-placeholder="{{ __('Select Division') }}">
                                        <option value="">{{ __('Select Division') }}</option>
                                        @forelse($divisions as $row)
                                            <option value="{{$row->id}}">{{$row->division_name}}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                    <label for="category_id" class="required">{{ __('Division') }}</label>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-floating form-floating-outline mb-4">
                                    <select id="category_id" class="select2 form-select required-field" data-required="order" multiple name="order[]"
                                        data-placeholder="{{ __('Select order') }}" required>
                                        <option value="">{{ __('Select order') }}</option>
                                        @forelse($order as $job_number)
                                            <option value="{{$job_number}}">{{$job_number}}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                    <label for="category_id" class="required">{{ __('order') }}</label>
                                </div>
                            </div>
                            
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">View</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('page-script')
@endsection