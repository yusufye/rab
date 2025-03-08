
@extends('layouts/layoutMaster')

@section('title', 'Edit Order')

<style>
    .readonly {
        pointer-events: none;
        background-color: #e9ecef !important;
        color: #6c757d !important;
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>

@section('content')
<form action="{{url('order/'.$order->id.'/update')}}" method="POST" id="form-edit-order">
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

    @endphp


    <div class="row mb-4">
        <div class="col-12">
            <div class="row">
                <div class="col-sm-3">
                    <span class="badge rounded-pill {{ $badgeClass }} m-2 fw-semibold text-center">
                        {{ $order->status }}
                    </span>
                </div>
                
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('/order')}}">{{ __('Order') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Edit Order') }}
                                
                            </li>
                        </ol>
                    </nav>
                    
                    
                    <div class="d-flex align-items-center ms-auto">
                        
                       
                        <button type="button" class="btn btn-secondary ms-2" id="button-edit" title="Simpan Draft"
                        {{ $order->status !== 'DRAFT' ? 'disabled' : '' }}><span class="mdi mdi-content-save"></span></button>

                        <button type="button" class="btn btn-primary ms-2" id="button-to-review" title="Kirim"
                        {{ $order->status !== 'DRAFT' ? 'disabled' : '' }}><span class="mdi mdi-send"></span></button>
                      
                    </div>
                </div>

                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" maxlength="25" class="form-control" placeholder="No. Kontrak" id="contract_number" value="{{old('contract_number',$order->contract_number??'')}}" name="contract_number" />
                                    <label for="contract_number">{{ __('No. Kontrak') }}</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control format-currency" placeholder="Nilai Kontrak" id="contract_price" name="contract_price" value="{{ old('contract_price', isset($order) ? number_format($order->contract_price, 0, ',', '') : '') }}"/>
                                    <label for="contract_price">{{ __('Nilai Kontrak') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control required-field" id="job_number" placeholder="{{ __('Job Number') }}"
                                        name="job_number" aria-label="Name" required value="{{ old('job_number', $order->job_number ?? '') }}" data-required="Job Number">
                                <label for="job_number" class="required">{{ __('No. Number') }}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="title" placeholder="{{ __('Title') }}"
                                    name="title" aria-label="Title" required value="{{ old('title',$order->title??'') }}" data-required="Title">
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
                                    <option value="{{$c->id}}" {{ old('category_id',$order->category_id) == $c->id ? 'selected' : '' }}>{{$c->category_name}}</option>
                                @empty
                                @endforelse
                            </select>
                            <label for="category_id" class="required">{{ __('Kategori') }}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="group" placeholder="{{ __('Grup') }}"
                                    name="group" aria-label="Group" required value="{{ old('group',$order->group??'') }}" data-required="Group">
                                <label for="group" class="required">{{ __('Kelompok') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">                       
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="customer" placeholder="{{ __('Customer') }}"
                                    name="customer" aria-label="Customer" required value="{{ old('customer',$order->customer??'') }}" data-required="Customer">
                                <label for="customer" class="required">{{ __('Pelanggan') }}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" id="study_lab" placeholder="{{ __('Study/Lab') }}"
                                    name="study_lab" aria-label="Study/Lab" required value="{{ old('study_lab',$order->study_lab??'') }}" data-required="Study/Lab">
                                <label for="study_lab" class="required">{{ __('Study/Lab') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">                        
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control required-field" data-required="Date" placeholder="YYYY-MM-DD to YYYY-MM-DD" id="date_range" name="date_range"/>
                                <label for="customer" class="required">{{ __('Tanggal') }}</label>
                            </div>
                        </div>
                        @php
                            $split_to_mak = $order->orderMak->pluck('split_to')->toArray();
                        @endphp
                        <div class="col-6">
                            <div class="form-floating form-floating-outline">                            
                            <select id="division" class="select2 form-select" multiple name="division[]"
                                data-placeholder="{{ __('Select Division') }}" required>
                                <option value="">{{ __('Select Division') }}</option>
                                {{--
                                @forelse($divisions as $div)
                                <option value="{{ $div->id }}" 
                                    {{ in_array($div->id, is_array($selected_divisions) ? $selected_divisions : []) ? 'selected' : '' }}
                                    {{ in_array($div->id, $split_to_mak) ? 'data-disabled-custom="true"' : '' }}>
                                    {{ $div->division_name }}
                                </option>
                                @empty
                                @endforelse
                                --}}
                            </select>
                            <label for="division" class="required">{{ __('Split ke-') }}</label>
                            
                        </div>
                        </div>                        
                    </div>
                    <div class="row">  
                        <div class="col-6">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control format-currency" id="price" placeholder="{{ __('Price') }}"
                                    name="price" aria-label="Price" required value="{{ old('price', isset($order) ? number_format($order->price, 0, ',', '') : '') }}">
                                <label for="price" class="required">{{ __('Nilai Anggaran') }}</label>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-floating form-floating-outline">
                                <select id="job_type" class="select2 form-select" name="job_type"
                                    data-placeholder="{{ __('Select Job Type') }}">
                                    <option value="">{{ __('Select Job Type') }}</option>
                                    <option value="Tunggal" {{ old('job_type', $order->job_type) == 'Tunggal' ? 'selected' : '' }}>Tunggal</option>
                                    <option value="Retail" {{ old('job_type', $order->job_type) == 'Retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="Gabungan" {{ old('job_type', $order->job_type) == 'Gabungan' ? 'selected' : '' }}>Gabungan</option>
                                </select>
                                <label for="job_type">{{ __('Job Type') }}</label>
                            </div>
                        </div>



                            {{-- @forelse($sum_array as $key => $sum)
                                @if($key !== 'split_totals')
                                    @php
                                    $key_label = match ($key) {
                                        'biaya_operasional' => 'Biaya Operasional',
                                        'profit' => 'Profit',
                                    };
                                    @endphp
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline mb-4">
                                            <input type="text" class="form-control format-currency readonly" id="{{$key}}" placeholder="{{ __($key_label) }}" aria-label="{{ __($key_label) }}" value="{{ number_format($sum ?? 0, 0, ',', '') }}">
                                            <label for="{{ __($key) }}">{{ __($key_label) }}</label>
                                        </div>
                                    </div>
                                @endif
                            @empty
                            @endforelse --}}

                        {{-- <div class="col-6">
                            <div class="row">
                            @forelse($sum_array['split_totals'] as $key => $sum)
                                <div class="col-12">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <input type="text" class="form-control format-currency readonly" id="{{$key}}" placeholder="{{ __($key) }}" aria-label="{{ __($key) }}"  value="{{ number_format($sum ?? 0, 0, ',', '') }}">
                                        <label for="{{$key}}">{{ __($key) }}</label>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                            </div>
                        </div> --}}
                    </div>
                </div>

                <div class="card-footer">
                    @livewire('order-summary', ['orderId' => $order->id])
                </div>
            </div>
        </div>
    </div>
</form>

@livewire('order-percentage-calc', ['orderId' => $order->id])

<br>
{{-- Livewire Component --}}
@livewire('order-mak-list', ['orderId' => $order->id])

<!-- reject notes jika ada -->
@if($order->approval_rejected_notes)
   <div class="alert alert-danger">
    {{$order->rejectedBy?->name}}, {{\Carbon\Carbon::parse($order->approval_rejected_datetime)->format('d M Y H:i:s')}}: {{$order->approval_rejected_notes}}
   </div>
 @endif

{{-- <div class="row mb-2">
    <div class="col-12 text-end">
        <button type="button" class="btn btn-warning" id="add-mak">Add Mak</button>
    </div>
</div> --}}

<!-- order mak -->

{{-- @forelse($order_mak as $om)
    <div class="row mb-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row mb-2">
                        <div class="col-6">
                            <h6>{{$om->mak->mak_code}} - {{$om->mak->mak_name}}</h6>
                        </div>
                        <div class="col-6 text-end">
                           <button class="btn btn-sm btn-info add-title" data-order-mak-id="{{$om->id}}" data-mak="{{$om->mak->mak_code}}-{{$om->mak->mak_name}}">Add Title</button>
                           <button id="edit-mak" 
                           data-order-mak-id="{{$om->id}}" 
                           data-mak-id="{{$om->mak->id}}" 
                           data-order-is-split="{{$om->is_split}}" 
                           data-order-split-to="{{$om->split_to}}"
                           class="btn btn-sm btn-success edit-mak">Edit Mak</button>
                           <button data-order-mak-id="{{$om->id}}"  class="btn btn-sm btn-danger delete-mak">Delete Mak</button>
                        </div>
                    </div>   
                    
                 

                    @forelse($om->orderTitle as $title)
                    <div class="row mb-2 mt-2">
                        
                        <div class="col-6">
                                <h6>{{$title->title}}</h6>
                            </div>
                        <div class="col-6 text-end">
                            <button class="btn btn-sm btn-info add-item" 
                            data-order-mak-id="{{$title->id}}" 
                            data-title="{{$title->title}}"
                            data-mak="{{$om->mak->mak_code}}-{{$om->mak->mak_name}}">Add Item</button>
                            <button id="edit-title" 
                            data-order-mak-id="{{$title->order_mak_id}}" 
                            data-order-title-id="{{$title->id}}"
                            data-mak="{{$om->mak->mak_code}}-{{$om->mak->mak_name}}"
                            data-title="{{$title->title}}"
                            class="btn btn-sm btn-success edit-title">Edit Title</button>
                            <button data-order-title-id="{{$title->id}}" class="btn btn-sm btn-danger delete-title">Delete Title</button>
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
                                        <th style="white-space: nowrap;">Actions</th>
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
                                    <td style="white-space: nowrap;">
                                        <button id="edit-item" 
                                        data-order-title-id="{{$orderItem->order_title_id}}"
                                        data-order-item-id="{{$orderItem->id}}" 
                                        data-mak="{{$om->mak->mak_code}}-{{$om->mak->mak_name}}"
                                        data-title="{{$title->title}}"
                                        data-order-item="{{$orderItem->item}}"
                                        data-qty-1="{{$orderItem->qty_1}}"
                                        data-unit-1="{{$orderItem->unit_1}}"
                                        data-unit-1="{{$orderItem->unit_1}}"
                                        data-qty-1="{{$orderItem->qty_1}}"
                                        data-qty-2="{{$orderItem->qty_2}}"
                                        data-unit-2="{{$orderItem->unit_2}}"
                                        data-qty-3="{{$orderItem->qty_3}}"
                                        data-unit-3="{{$orderItem->unit_3}}"
                                        data-total-qty="{{$orderItem->qty_total}}"
                                        data-total-unit="{{$orderItem->qty_unit}}"
                                        data-price-unit="{{ number_format($orderItem->price_unit, 0, ',', '') }}"
                                        data-total-price="{{ number_format($orderItem->total_price, 0, ',', '') }}"
                                        class="btn btn-sm btn-success edit-item">Edit Item</button>
                                        <button data-order-item-id="{{$orderItem->id}}" class="btn btn-sm btn-danger delete-item">Delete Item</button>
                                    </td>

                                </tr>

                                @php
                                    $sumTotalPrice += $orderItem->total_price ?? 0;
                                @endphp

                                @endforeach
                                <tr>
                                    <td colspan="10" class="text-center" style="font-weight: bold">Total</td>
                                    <td style="white-space:nowrap;font-weight: bold">Rp {{ number_format($sumTotalPrice, 0, ',', '.') }}</td>
                                    <td></td>
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

@endforelse --}}
@livewireScripts
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
        window.statusOrder = @json($order->status);

    </script>
    <script type="module" src="{{ asset('assets/js_custom/edit_order.js') }}?v={{ time() }}"></script>
@endsection