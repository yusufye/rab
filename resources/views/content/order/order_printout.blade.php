<style>
    
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
        font-size: 10px;

    }
    th {
        background-color: #f2f2f2;
    }
    .container{
        font-size: 10px;
    }
    .header-section, .qrcode-container {
        border: 0px !important;
        padding: 2px;
        margin-bottom: 20px;
    }
    .header-section td {
        border: 0px !important;
        font-size: 10px;
    }
    .detail-section h3 {
        margin: 5px 0;
        page-break-inside: avoid;
    }

    .item-table {
        page-break-inside: avoid;
    }
    .qrcode-section td {
        border: 0 !important;
        text-align: center;
        padding: 5px;
    }
    .qrcode-section{
        page-break-before: always;
    }
</style>
<div class="container">

<!-- Header Section -->
<div class="header-section">
    <table>
        <tr>
            <td>Job Number</td>
            <td>:</td>
            <td>{{ $order->job_number }}</td>
            <td>Title</td>
            <td>:</td>
            <td>{{ $order->title }}</td>
        </tr>
        <tr>
            <td>Category</td>
            <td>:</td>
            <td>{{ $order->category->category_name ?? 'Unknown' }}</td>
            <td>Group</td>
            <td>:</td>
            <td>{{ $order->group }}</td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>:</td>
            <td>{{ $order->customer }}</td>
            <td>Study/Lab</td>
            <td>:</td>
            <td>{{ $order->study_lab }}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($order->start_date)->format('d-M-Y')}} - {{ \Carbon\Carbon::parse($order->end_date)->format('d-M-Y') }}</td>
            <td>Price</td>
            <td>:</td>
            <td>Rp {{ number_format($order->price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Split</td>
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
    @foreach ($orderMaks as $orderMak)
        @if ($orderMak->is_split)
            <div class="division-title">
                {{ $orderMak->is_split ? $orderMak->division->division_name : '-' }}
            </div>
        @endif
        <div class="mak-name">
            <strong>{{ strtoupper($orderMak->mak->mak_name ?? 'Tanpa MAK') }}</strong>
        </div>

        @foreach ($orderMak->orderTitle as $title)
            <div class="title-label">
                {{ strtoupper($title->title ?? 'Tanpa Judul') }}
            </div>

            <table class="item-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty 1</th>
                        <th>Unit 1</th>
                        <th>Qty 2</th>
                        <th>Unit 2</th>
                        <th>Qty 3</th>
                        <th>Unit 3</th>
                        <th>Total Qty</th>
                        <th>Unit</th>
                        <th>Price Unit</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sumTotalPrice = 0;
                    @endphp

                    @foreach ($title->orderItem as $item)
                        <tr>
                            <td>{{ $item->item ?? 'Tanpa Item' }}</td>
                            <td>{{ $item->qty_1 ?? '0' }}</td>
                            <td>{{ $item->unit_1 ?? '-' }}</td>
                            <td>{{ $item->qty_2 ?? '0' }}</td>
                            <td>{{ $item->unit_2 ?? '-' }}</td>
                            <td>{{ $item->qty_3 ?? '0' }}</td>
                            <td>{{ $item->unit_3 ?? '-' }}</td>
                            <td>{{ $item->qty_total ?? '0' }}</td>
                            <td>{{ $item->unit_total ?? '-' }}</td>
                            <td>Rp {{ number_format($item->price_unit ?? 0, 2) }}</td>
                            <td>Rp {{ number_format($item->total_price ?? 0, 2) }}</td>
                        </tr>

                        @php
                            $sumTotalPrice += $item->total_price ?? 0;
                        @endphp
                    @endforeach

                    <tr class="total-row">
                        <td colspan="10">Total</td>
                        <td>Rp {{ number_format($sumTotalPrice, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    @endforeach
</div>

<!-- QR Code Section (Paling Bawah) -->
<div class="qrcode-section">
    <table width="100%">
        <!-- Baris Nama Approver -->
        <tr>
            <td><strong>Approver 1</strong></td>
            <td><strong>Approver 2</strong></td>
            <td><strong>Approver 3</strong></td>
        </tr>
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
    </table>
</div>
</div>
