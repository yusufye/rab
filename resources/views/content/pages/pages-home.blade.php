@extends('layouts/layoutMaster')

@section('title', 'Home')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
<div class="row g-6">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-primary h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2">
              <div class="avatar me-4">
                <span class="avatar-initial rounded-3 bg-label-primary"><i class="mdi mdi-cart mdi-24px"></i></span>
              </div>
              <h4 class="mb-0">{{$order->whereNotIn('status',['REVISED'])->count()}}</h4>
            </div>
            <h6 class="mb-0 fw-normal">Order</h6>
            <p class="mb-0">
              <small class="text-muted">keseluruhan</small>
            </p>
          </div>
        </div>
      </div>
    
      @php
        $need_process=0;
        $has_process=0;
        $has_process_title="";
        if (Auth::user()->hasRole('reviewer')||Auth::user()->hasRole('head_reviewer')) {
            $need_process=$order->where('status','TO REVIEW')->count();
            $has_process=$order->where('status','REVIEWED')->count();
            $has_process_title='telah anda release';
        }elseif (Auth::user()->hasRole('approval_satu')) {
            $need_process=$order->where('status','REVIEWED')->count();
            $has_process=$order->where('status','APPROVED')->where('approval_step',1)->count();
            $has_process_title='telah anda approve dan menunggu approval dari approver ke-2';
        }elseif (Auth::user()->hasRole('approval_dua')) {
            $need_process=$order->where('status','APPROVED')->where('approval_step',1)->count();
            $has_process=$order->where('status','APPROVED')->where('approval_step',2)->count();
            $has_process_title='telah anda approve dan menunggu approval dari approver ke-3';
        }elseif (Auth::user()->hasRole('approval_tiga')) {
            $need_process=$order->where('status','APPROVED')->where('approval_step',2)->count();
            $has_process=$order->where('status','APPROVED')->where('approval_step',3)->count();
            $has_process_title='telah anda approve dan siap di checklist';
        }elseif (Auth::user()->hasRole('checker')) {
            $need_process=$order->where('status','APPROVED')->where('approval_step',3)->count();
            $has_process=$order_checked;
            $has_process_title='telah anda checklist';
        }elseif (Auth::user()->hasRole('admin')) {
            $need_process=$order->whereIn('status',['DRAFT'])->count();
            $has_process=$order->where('status','TO REVIEW')->count();
            $has_process_title='telah anda kirim dan menunggu review';
        }
      @endphp

      <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2">
              <div class="avatar me-4">
                <span class="avatar-initial rounded-3 bg-label-warning"><i class="mdi mdi-file-alert mdi-24px"></i></span>
              </div>
              <h4 class="mb-0">{{$need_process}}</h4>
            </div>
            <h6 class="mb-0 fw-normal">Order</h6>
            <p class="mb-0">
                <small class="text-muted">
                    @if (Auth::user()->hasRole('admin'))
                    (DRAFT) yang perlu anda proses
                    @elseif (Auth::user()->hasRole('checker'))
                    yang perlu anda checklist
                    @else
                    yang perlu anda approve
                    @endif
                </small>
            </p>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2">
              <div class="avatar me-4">
                <span class="avatar-initial rounded-3 bg-label-success"><i class="mdi mdi-file-check mdi-24px"></i></span>
              </div>
              <h4 class="mb-0">{{$has_process}}</h4>
            </div>
            <h6 class="mb-0 fw-normal">Order</h6>
            <p class="mb-0">
              <small class="text-muted">{{$has_process_title}}</small>
            </p>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-danger h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2">
              <div class="avatar me-4">
                <span class="avatar-initial rounded-3 bg-label-danger"><i class="mdi mdi-cancel mdi-24px"></i></span>
              </div>
              <h4 class="mb-0">{{$order->where('status','CANCELLED')->count()}}</h4>
            </div>
            <h6 class="mb-0 fw-normal">Order</h6>
            <p class="mb-0">
              <small class="text-muted">yang dibatalkan</small>
            </p>
          </div>
        </div>
      </div>
    
</div>
</div>


@endsection
