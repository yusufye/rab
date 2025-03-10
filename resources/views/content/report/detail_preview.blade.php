<style>
    .table table {
        border-collapse: collapse;
        width: 100%;
        font-size: 12px;
        margin: 10px 0;
    }
    .table-custom {
    width: 100%;
    border-collapse: collapse;
    table-layout: auto; /* Lebar kolom menyesuaikan isi */
    font-size: 14px;
    text-align: center;
}

.table-custom thead {
    background-color: #2c3e50;
    color: white;
}

.table-custom th, 
.table-custom td {
    border: 1px solid #ddd;
    padding: 8px;
    white-space: nowrap; /* Mencegah teks terlalu panjang dipecah ke baris baru */
}

.table-custom tbody tr:nth-child(odd) {
    background-color: #f9f9f9; /* Baris selang-seling */
}

.table-custom tbody tr:hover {
    background-color: #f1f1f1; /* Warna saat di-hover */
}

.table-custom td.text-right {
    text-align: right;
}

.table-custom th {
    padding: 12px;
}

.table-custom tfoot {
    font-weight: bold;
    background-color: #ecf0f1;
}


</style>
<div class="table">

    <table class="table-custom" border="1">
        <thead>
            <tr>
                <th>DPM</th>
                <th>Nomor Kontrak</th>
                <th>Nilai Kontrak</th>
                <th>Split Ke DPM</th>
                <th>Nilai Split</th>
                @foreach ($divisions as $division)
                    <th>
                        Nilai Split {{$division->division_name}}
                    </th>
                @endforeach
                <th>Tanggal Mulai Pekerjaan</th>
                <th>Tanggal Akhir Pekerjaan</th>
                <th>Nilai Job</th>
                <th>Profit (Rp)</th>
                <th>Operasional (Rp)</th>
                @foreach ($maks as $mak)
                    <th>
                        {{$mak->mak_code}} - {{$mak->mak_name}}
                    </th>
                @endforeach
                <th>Profit (%)</th>
                <th>Operasional (%)</th>
                <th>Profit Asumsi Juknis (%)</th>
                <th>Profit Asumsi Juknis (Rp)</th>
                <th>Profit Gab (Rp)</th>
                <th>Direct Cost Sblm (Rp)</th>
                <th>Direct Cost Sblm (%)</th>
                <th>Direct Cost Stlh (Rp)</th>
                <th>Direct Cost Stlh (%)</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            @php
                $splitTotals = $order->totalSplitPerDivision();
            @endphp
                <tr>
                    <td>
                        {{$order->createdBy->division->division_name}}
                    </td>
                    <td>
                        {{$order->contract_number}}
                    </td>
                    <td>
                        {{ number_format($order->contract_price, 0, ',', '.') }}
                    </td>
                    <td>
                        {{ $order->splitToDivisions()->pluck('division_name')->implode(', ') }}
                    </td>
                    <td>
                        {{ number_format($splitTotals['total_split'], 2) }}
                    </td>
                    
                    @foreach ($divisions as $division)
                        <td>{{ number_format($splitTotals['split_per_division'][$division->id] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                    <td>
                        {{\Carbon\Carbon::parse($order->date_from)->format('d-M-Y')}}
                    </td>
                    <td>
                        {{\Carbon\Carbon::parse($order->date_to)->format('d-M-Y')}}
                    </td>
                    <td>
                        {{ number_format($order->price, 2) }}
                    </td>
                    <td>
                        @php
                            $profit=$order->price-$order->totalOperational();
                        @endphp
                        {{ number_format($profit, 2) }}
                    </td>
                    <td>{{ number_format($order->totalOperational(), 0, ',', '.') }}</td>
                    @php
                        $makTotals = $order->totalPerMak();
                    @endphp
                    <!-- Tampilkan total per MAK -->
                    @foreach ($maks as $mak)
                        <td>{{ number_format($makTotals[$mak->id] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                    <td>
                        {{ number_format($profit/$order->price*100, 2) }}
                    </td>
                    <td>
                        {{ number_format($order->totalOperational()/$order->price*100, 2) }}
                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>

                    </td>

                    @php
                        $rev0=$order->rev0();
                    @endphp
                    <td class="text-right">
                        {{ number_format($rev0['operational'], 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ $rev0['operational']/$rev0['job_price']*100 }}
                    </td>
                    <td>
                        {{ number_format($order->totalOperational(), 0, ',', '.') }}
                    </td>
                    <td>
                        {{ number_format($order->totalOperational()/$order->price*100, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>