@extends('layouts/layoutMaster')

@section('title', 'Edit Order')

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
            'DRAFT' => 'bg-label-dark',
            'TO REVIEW' => 'bg-label-warning',
            'RELEASED' => 'bg-label-info',
            'APPROVED' => 'bg-label-success',
            'CLOSED' => 'bg-label-danger',
            default => 'bg-label-secondary',
        };

        $selected_divisions = old('division', $divisions_id);

    @endphp


    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('View Order') }}</h5>
                   <div class="badge  rounded-pill {{ $badgeClass }}">
                        {{ $order->status }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <table class="table">
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">Job Number</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->job_number??'-'}}</td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">Category</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->category ? $order->category->category_name : '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">Customer</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->customer??'-'}}</td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">Date</td>
                                    <td style="width: 5%;">:</td>
                                    <td>From: {{\Carbon\Carbon::parse($order->date_from)->format('d-M-Y')}} - To: {{\Carbon\Carbon::parse($order->date_to)->format('d-M-Y')}}</td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">Split Price</td>
                                    <td style="width: 5%;">:</td>
                                    <td> {{ $order->split_price ? 'Rp ' . number_format((float) $order->split_price, 0, ',', '.') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <table class="table">
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">Title</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->title??'-'}}</td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">Group</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->group ??'-'}}</td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">Studay/Lab</td>
                                    <td style="width: 5%;">:</td>
                                    <td>{{$order->study_lab??'-'}}</td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">Price</td>
                                    <td style="width: 5%;">:</td>
                                    <td>
                                        {{ $order->price ? 'Rp ' . number_format((float) $order->price, 0, ',', '.') : '-' }}
                                    </td>

                                </tr>
                                @if($order->split_price != 0 && $divisions_by_order_header->isNotEmpty())
                                <tr>
                                    <td style="white-space: nowrap; width: 10%;">Division</td>
                                    <td style="width: 5%;">:</td>
                                    <td>
                                            {{ $divisions_by_order_header->pluck('division_name')->implode(', ') }}
                                    </td>

                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                 
                </div>
            </div>
        </div>
    </div>




<!-- order mak -->

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

@include('/modals/orders/add_mak_modal')
@include('/modals/orders/add_title_modal')
@include('/modals/orders/add_item_modal')
@endsection

@php
    $dateFrom = old('date_from', isset($order) ? \Carbon\Carbon::parse($order->date_from)->format('d-M-Y') : now());
    $dateTo = old('date_to', isset($order) ? \Carbon\Carbon::parse($order->date_to)->format('d-M-Y') : now());
@endphp

@section('page-script')
    <script type="module">
        window.dateFrom = @json($dateFrom);
        window.dateTo = @json($dateTo);
        window.orderId = @json($order->id);

    </script>
    <script type="module" src="{{ asset('assets/js_custom/edit_order.js') }}?v={{ time() }}"></script>
@endsection