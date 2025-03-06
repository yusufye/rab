@extends('layouts/layoutMaster')

@section('title', 'Order Report')
@section('content')
<form action="{{url('report/show/'.$type)}}" method="POST" id="form-report">
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
                                    <input class="form-control" type="date" id="html5-date-input" name="start_date">
                                    <label for="html5-date-input">Start Date</label>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-floating form-floating-outline mb-4">
                                    <input class="form-control" type="date" id="html5-date-input" name="start_date">
                                    <label for="html5-date-input">End Date</label>
                                </div>
                            </div>
                            
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" name="action" value="view" class="btn btn-primary">View</button>
                    <button type="submit" name="action" value="download" class="btn btn-primary">Download</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('page-script')
@endsection