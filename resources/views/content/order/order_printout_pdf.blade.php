<style>
    .header-section table {
        border-collapse: collapse;
        width: 100%;
        font-size: 12px;
        margin: 10px 0;
        padding: 5px;
    }

    .header-section td {
        padding: 5px;
    }

    .detail-section table {
        border-collapse: collapse;
        width: 100%;
        font-size: 10px;
        margin: 10px 0;
        padding: 5px;
        text-align: center;
    }

    .detail-title {
        text-align: left;
        background-color: #e3e6e4;
    }
    .detail-mak {
        
        background-color: #cfd4d1;
        text-align: left;
    }
    .detail-item {
        text-align: left;
    }

    .detail-section th, .detail-section td {
        border: 1px solid black;
        padding: 5px;
    }

    .detail-price {
        text-align: right;
    }

    .qrcode-section table {
        border-collapse: collapse;
        width: 100%;
        font-size: 12px;
        margin: 10px 0;
        padding: 5px;
        text-align: center;
        page-break-before: auto;
    }

    .qrcode-section td {
        border: 1px solid black;
        padding: 5px;
    }

    .draft-status::before {
        content: "[DRAFT]";
        position: absolute;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 150px;
        font-weight: bold;
        color: rgba(200, 200, 200, 0.3);
        z-index: 99999;
        white-space: nowrap;
        pointer-events: none;
    }
</style>

<div class="container {{($order->status=='DRAFT'?'draft-status':'')}}">

<!-- Header Section -->
    <div class="header-section">
        <table>
            <tr>
                <td><strong>Job Number</strong></td>
                <td>:</td>
                <td>{{ $order->job_number }}</td>
                <td><strong>Title</strong></td>
                <td>:</td>
                <td>{{ $order->title }}</td>
            </tr>
            <tr>
                <td><strong>Category</strong></td>
                <td>:</td>
                <td>{{ $order->category->category_name ?? 'Unknown' }}</td>
                <td><strong>Group</strong></td>
                <td>:</td>
                <td>{{ $order->group }}</td>
            </tr>
            <tr>
                <td><strong>Customer</strong></td>
                <td>:</td>
                <td>{{ $order->customer }}</td>
                <td><strong>Study/Lab</strong></td>
                <td>:</td>
                <td>{{ $order->study_lab }}</td>
            </tr>
            <tr>
                <td><strong>Date</strong></td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($order->start_date)->format('d-M-Y')}} - {{ \Carbon\Carbon::parse($order->end_date)->format('d-M-Y') }}</td>
                <td><strong>Price</strong></td>
                <td>:</td>
                <td>Rp {{ number_format($order->price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Split</strong></td>
                <td>:</td>
                <td>
                    @php
                    $divisions=[];
                    if (is_array($order->split_to)) {
                        $divisions = \App\Models\Division::whereIn('id', $order->split_to)->pluck('division_name')->toArray();
                    }
                    @endphp
                    {{ implode(', ', $divisions) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <table>
            <thead>
                <tr class="detail-mak">
                    <th>MAK</th>
                    <th>Title</th>
                    <th>Total(Rp)</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach ($orderMaks as $orderMak)
               
                @php
                $sum_title=0;
            @endphp
                        @foreach ($orderMak->orderTitle as $title)
                        
                        <tr >
                            @if ( $loop->index==0)    
                                <td rowspan="{{$orderMak->orderTitle->count()}}">
                                    <strong>{!! $orderMak->is_split ? $orderMak->division->division_name.'<br>' : '' !!}</strong>
                                    <strong>{{ strtoupper($orderMak->mak->mak_name ?? 'Tanpa MAK') }}</strong>
                                </td>
                            @endif
                            <td>
                                {{ strtoupper($title->title ?? 'Tanpa Judul') }}
                            </td>
                            <td class="detail-price">
                                {{$sum_title}}
                            </td>
                        </tr>
                            @foreach ($title->orderItem as $item)
                                @php
                                    $sum_title+=$item->total_price;
                                @endphp
                            @endforeach
                        @endforeach
                    
                
                @endforeach
            </tbody>
        </table>
        
    </div>

    <!-- QR Code Section (Paling Bawah) -->
    <div class="qrcode-section">
        <table>
            <!-- Baris Nama Approver -->
            
            <!-- Baris QR Code -->
            <tr>
                <td>
                    @if($approver_1)
                        <img src="{{ $approver_1 }}" width="100">
                    @endif
                </td>
                <td>
                    @if($approver_2)
                        <img src="{{ $approver_2 }}" width="100">
                    @endif
                </td>
                <td>
                    @if($approver_3)
                        <img src="{{ $approver_3 }}" width="100">
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Approver 1</strong></td>
                <td><strong>Approver 2</strong></td>
                <td><strong>Approver 3</strong></td>
            </tr>
        </table>
    </div>
</div>
