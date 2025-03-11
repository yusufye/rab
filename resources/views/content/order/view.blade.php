@extends('layouts/layoutMaster')

@section('title', 'View Order')

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

    @php
        $badgeClass = match ($order->status) {
            'DRAFT'     => 'bg-secondary',
            'TO REVIEW' => 'bg-warning',
            'REVIEWED'  => 'bg-label-warning',
            'RELEASED'  => 'bg-info',
            'APPROVED'  => 'bg-primary',
            'REVISED'   => 'bg-dark',
            'CLOSED'    => 'bg-dark',
            default     => 'bg-secondary',
        };

        $selected_divisions = old('division', $divisions_id);

        $user = auth()->user();

        $isDisabled = match (true) {
            ($user->hasRole('head_reviewer') && $order?->status != 'TO REVIEW') => true,
            ($user->hasRole('approval_satu') && $order?->status != 'REVIEWED') => true,
            ($user->hasRole('approval_dua') && !($order?->status == 'APPROVED' && $order?->approval_step == 1)) => true,
            ($user->hasRole('approval_tiga') && !($order?->status == 'APPROVED' && $order?->approval_step == 2)) => true,
            default => false,
        };
   
    @endphp


    <div class="row mb-4">
        <div class="col-12">
            @if ($order->reviewed_notes!='')
                <div class="alert alert-success">
                    {!! nl2br($order->reviewed_notes) !!}
                </div>
                @endif
                <!-- reject notes jika ada -->
                @if($order->approval_rejected_notes)
                <div class="alert alert-danger">
                {{$order->rejectedBy?->name}}, {{\Carbon\Carbon::parse($order->approval_rejected_datetime)->format('d M Y H:i:s')}}: {{$order->approval_rejected_notes}}
                </div>
                @endif
            <div class="row">
                <div class="col-sm-3">
                <a href="#" class="btn-status-order" data-order-id="{{$order->id}}">
                        <span class="badge rounded-pill {{ $badgeClass }} m-2 fw-semibold text-center">
                            {{ $order->status }}
                        </span>                        
                    </a>
                </div>
                
            </div>
            <div class="col-md-12 col-xl-12">
                
                
            <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('/order')}}">{{ __('Order') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('View Order') }}
                                
                            </li>
                        </ol>
                    </nav>
                    
                    <div class="d-flex align-items-center ms-auto">
                    
                        @if(!auth()->user()->hasAnyRole(['admin','reviewer','checker','Super_admin']))
                        <button type="button" class="btn btn-danger ms-2" id="button-reject" {{$isDisabled ? 'disabled' : ''}}>Reject</button>
                        
                            @if(auth()->user()->hasRole('head_reviewer'))
                                <button type="button" class="btn btn-primary ms-2" id="button-release" @disabled($isDisabled)>Release</button>
                            @endif

                            @if(!auth()->user()->hasRole('head_reviewer'))
                                <button type="button" class="btn btn-primary ms-2" id="button-approve" @disabled($isDisabled)>Approve</button>

                            @endif
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <table class="table">                                
                                 <tr>
                                    <td style="white-space: nowrap; width: 10%;">No. Kontrak</td>
                                    <td style="width: 5%;">:</td>
                                    <td>
                                        {{$order->contract_number??'' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('Nilai Kontrak') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>
                                        {{ $order->contract_price ? 'Rp ' . number_format((float) $order->contract_price, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('Judul') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->title??'-'}}</td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('Kategori') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->category ? $order->category->category_name : '-'}}</td>
                                </tr>
                                
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('Tanggal') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>From: {{\Carbon\Carbon::parse($order->date_from)->format('d-M-Y')}} - To: {{\Carbon\Carbon::parse($order->date_to)->format('d-M-Y')}}</td>
                                </tr>
                                
                                
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('Split ke-') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>
                                        @if(!empty($divisions_by_order_header))
                                            {{ $divisions_by_order_header->pluck('division_name')->implode(', ') }}
                                        @endif
                                    </td>

                                </tr>
                                
                                {{--
                                @forelse($sum_array as $key => $sum)
                                <tr>
                                @if($key !== 'split_totals')
                                    @php
                                    $key_label = match ($key) {
                                        'biaya_operasional' => 'Biaya Operasional',
                                        'profit' => 'Profit',
                                    };
                                    @endphp                                  
                                        
                                    <td style="white-space: nowrap; width: 10%;"> {{$key_label}}</td>
                                    <td style="width: 5%;">:</td>
                                    <td> {{ 'Rp ' . number_format((float) $sum??'', 0, ',', '.') }}</td>
                                  
                                @endif
                                </tr>
                                @empty
                                @endforelse 
                                --}}
                               
                            </table>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <table class="table">                                       
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('Judul Kontrak') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->study_lab??'-'}}</td>
                                </tr>

                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('Pelanggan') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->customer??'-'}}</td>
                                </tr>                              
                               

                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('No. Job') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->job_number??'-'}}</td>
                                </tr>

                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('Kelompok') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->group ??'-'}}</td>
                                </tr>
                                
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('Nilai Job') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>
                                        {{ $order->price ? 'Rp ' . number_format((float) $order->price, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                           

                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">{{ __('Job Type') }}</td>
                                    <td style="width: 5%;">:</td>
                                    <td>
                                        {{$order->job_type??'' }}
                                    </td>
                                </tr>                                
                               

                                {{--
                                @forelse($sum_array['split_totals'] as $key => $sum)
                                <tr>
                                     <td style="white-space: nowrap; width: 10%;"> {{$key}}</td>
                                    <td style="width: 5%;">:</td>
                                    <td> {{ 'Rp ' . number_format((float) $sum??'', 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                @endforelse 
                                --}}
                            </table>
                        </div>
                    </div>
                 
                </div>
                <div class="card-footer">
                    @livewire('order-summary', ['orderId' => $order->id])
                </div>
            </div>
        </div>
    </div>
    </div>
    
    @livewire('order-percentage-calc', ['orderId' => $order->id])
    
    <br>
    {{-- Livewire Component --}}
    @livewire('order-mak-view-list', ['orderId' => $order->id])

<!-- order mak -->
{{--
@forelse($order_mak as $om)
    <div class="row mb-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row mb-2">
                        <div class="col-6">
                            <h6>{{$om->mak->mak_code}} - {{$om->mak->mak_name}}</h6>
                        </div>
                    </div>   
                    
                 

                    @forelse($om->orderTitle as $title)
                    <div class="row mb-2 mt-2">
                        
                        <div class="col-6">
                                <h6>{{$title->title}}</h6>
                            </div>
                          
                        @if($title->orderItem->isNotEmpty())
                            <div class="col-12 mt-2 mb-2 table-responsive">

                            <table class="table table-bordered datatables-orders">
                                    <thead>
                                        <th style="white-space: nowrap;">Item</th>
                                        <th style="white-space: nowrap;">Qty 1</th>
                                        <th style="white-space: nowrap;">Unit 1</th>
                                        <th style="white-space: nowrap;">Qty 2</th>
                                        <th style="white-space: nowrap;">Unit 2</th>
                                        <th style="white-space: nowrap;">Qty 3</th>
                                        <th style="white-space: nowrap;">Unit 3</th>
                                        <th style="white-space: nowrap;">Total Qty</th>
                                        <th style="white-space: nowrap;">Total Unit</th>
                                        <th style="white-space: nowrap;">Price Unit</th>
                                        <th style="white-space: nowrap;">Total Price</th>
                                    </thead>
                              
                                
                            <tbody>
                                @php
                                    $sumTotalPrice = 0;
                                @endphp
                        @foreach($title->orderItem as $orderItem)

                                <tr>
                                    <td style="white-space: nowrap;">{{$orderItem->item}}</td>
                                    <td style="white-space: nowrap;">{{$orderItem->qty_1??''}}</td>
                                    <td style="white-space: nowrap;">{{$orderItem->unit_1??''}}</td>
                                    <td style="white-space: nowrap;">{{$orderItem->qty_2??''}}</td>
                                    <td style="white-space: nowrap;">{{$orderItem->unit_2??''}}</td>
                                    <td style="white-space: nowrap;">{{$orderItem->qty_3??''}}</td>
                                    <td style="white-space: nowrap;">{{$orderItem->unit_3??''}}</td>
                                    <td style="white-space: nowrap;">{{$orderItem->qty_total??''}}</td>
                                    <td style="white-space: nowrap;">{{$orderItem->qty_unit??''}}</td>
                                    <td style="white-space: nowrap;">Rp {{ number_format($orderItem->price_unit ?? 0, 0, ',', '.') }}</td>
                                    <td style="white-space: nowrap;">Rp {{ number_format($orderItem->total_price ?? 0, 0, ',', '.') }}</td>
                                  

                                </tr>

                                @php
                                    $sumTotalPrice += $orderItem->total_price ?? 0;
                                @endphp

                                @endforeach
                                <tr>
                                    <td colspan="10" class="text-center" style="font-weight: bold;">Total</td>
                                    <td style="white-space:nowrap;font-weight: bold">Rp {{ number_format($sumTotalPrice, 0, ',', '.') }}</td>
                                </tr>
                                
                            </tbody>
                        </table>
                        </div>


                        @endif
                        </div>
                       
                    @empty

                    @endforelse
                </div>
            </div>
        </div>
    </div>
@empty

@endforelse
--}}




<!-- Add notes Modal -->
<div class="modal fade" id="modal-notes-approval" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">    
    <div class="modal-content">
            <div class="modal-header">
                  <h4 class="modal-title">Release Notes</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <div class="modal-body">
                <div class="row">
                    
                <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                        <textarea id="reviewed_notes" name="reviewed_notes" class="form-control">{{ old('reviewed_notes', $order->reviewed_notes ?? '') }}</textarea>
                        <label for="reviewed_notes" class="required">{{ __('Release Notes') }}</label>
                    </div>
                </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="button-save-title">Save</button>
                </div>
        </div>
    </div>
  </div>
</div>

<!-- Add notes Modal -->
<div class="modal fade" id="modal-checklist-item" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">    
    <div class="modal-content">
            <div class="modal-header">
                  <h4 class="modal-title">No. PPB/PPJ</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <div class="modal-body">
                <div class="row">
                    
                <div class="col">
                            <input type="hidden" id="order_item_id" readonly>
                            <div data-repeater-checklist="group-a" id="repeater-checklist">

                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-item-checklist">
                                <i class="mdi mdi-plus me-1"></i>
                            </button>
                </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="button-save-checklist">Save</button>
                </div>
        </div>
    </div>
  </div>
</div>

<!-- Add rejected notes Modal -->
<div class="modal fade" id="modal-notes-rejected" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">    
    <div class="modal-content">
            <div class="modal-header">
                  <h4 class="modal-title">Rejected Notes</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

        <div class="modal-body">
                <div class="row">
                    
                <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                        <textarea id="approval_rejected_notes" name="approval_rejected_notes" class="form-control"></textarea>
                        <label for="approval_rejected_notes" class="required">{{ __('Rejected Notes') }}</label>
                    </div>
                </div>

                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="btn-rejected-notes">Save</button>
                </div>

        </div>

    </div>

  </div>

</div>

@livewireScripts

@endsection

@section('page-script')

    <script type="module">
        window.orderId = @json($order->id);
    </script>
    <script type="module" src="{{ asset('assets/js_custom/view_order.js') }}?v={{ time() }}"></script>
@endsection
