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

    .table_header{
        background-color: #cfd4d1;
        text-align: center;
        font-weight: bold;
    }
    .grand_total{
        background-color: #e3e6e4;
        font-weight: bold;
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
                    
                @foreach ($sumPerDiv as $division => $amount)
                    <strong>{{ $division }}:</strong> Rp {{ number_format($amount, 0, ',', '.') }}<br>
                @endforeach
                    
                </td>
                <td><strong>Biaya Operasional</strong></td>
                <td>:</td>
                <td>Rp {{ number_format($sumItem, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td><strong>Profit</strong></td>
                <td>:</td>
                <td>Rp {{ number_format($order->price-$sumItem, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <table class="">
            <thead class="table_header">
                <tr>
                    <th>No</th>
                    <th>Uraian</th>
                    <th>Jumlah (%)</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_pct=0;
                    $total_calc=0;
                @endphp
                @foreach($getPercentage as $index => $percentage)
                @php
                    $calc=$profit * ($percentage->percentage / 100);
                @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="detail-item">{!! nl2br($percentage->title) !!}}</td>
                        <td>{{ $percentage->percentage }}%</td>
                        <td class="detail-price">
                            {{ number_format($calc, 2) }}
                        </td>
                    </tr>
                    @php
                        $total_pct+=$percentage->percentage;
                        $total_calc+=$calc;
                    @endphp
                @endforeach
            </tbody>
            <tfoot class="grand_total">
                <tr>
                    <td colspan="2">Total</td>
                    <td>{{$total_pct}}%</td>
                    <td class="detail-price">{{ number_format($total_calc, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <table>
            <thead>
                <tr class="table_header">
                    <th>MAK</th>
                    <th>Title</th>
                    <th>Total(Rp)</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach ($orderMaks as $orderMak)

                @foreach ($orderMak->orderTitle as $title)
                    @php $sum_title=0; @endphp
                    @foreach ($title->orderItem as $item)
                        @php
                            $sum_title+=$item->total_price;
                        @endphp
                    @endforeach
                    <tr >
                        @if ( $loop->index==0)
                            <td class="detail-item" rowspan="{{$orderMak->orderTitle->count()}}">
                                <strong>{!! $orderMak->is_split ? $orderMak->division->division_name.'<br>' : '' !!}</strong>
                                <strong>{{ strtoupper($orderMak->mak->mak_code ?? '-').'-'.strtoupper($orderMak->mak->mak_name ?? 'Tanpa MAK') }}</strong>
                            </td>
                        @endif
                        <td>
                            {{ strtoupper($title->title ?? 'Tanpa Judul') }}
                        </td>
                        <td class="detail-price">
                            {{number_format($sum_title, 2)}}
                        </td>
                    </tr>
                       
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
