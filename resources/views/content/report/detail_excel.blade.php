<table>
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
            <th>Nomor Job</th>
            <th>Status Job</th>
            <th>Kategori Job</th>
            <th>Tipe Job</th>
            <th>Nama Job</th>
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
            $splitTotals         = $order->totalSplitPerDivision();
            $profit              = $order->price-$order->totalOperational();
            $category_percentage = 0;
            if (strtolower($order->category->category_name)=='special case') {
                $category_percentage=$profit/$order->price*100;
            }else {
                foreach ($categorys as $category) {
                    if (strtolower($order->category->category_name)==strtolower($category['category_name'])) {
                        $category_percentage=$category['category_percentage'];
                    }
                }
            }
        @endphp
            <tr>
                <td>
                    {{$order->createdBy->division->division_name}}
                </td>
                <td>
                    {{$order->contract_number}}
                </td>
                <td>
                    {{ $order->contract_price }}
                </td>
                <td>
                    {{ $order->splitToDivisions()->pluck('division_name')->implode(', ') }}
                </td>
                <td>
                    {{ $splitTotals['total_split'] }}
                </td>
                
                @foreach ($divisions as $division)
                    <td>{{ $splitTotals['split_per_division'][$division->id] ?? 0 }}</td>
                @endforeach
                <td>
                    {{$order->job_number}}
                </td>
                <td>
                    {{($order->rev==0?'BARU':'REVISI '.$order->rev)}}
                </td>
                <td>
                    {{$order->category->category_name}}
                </td>
                <td>
                    {{$order->job_type}}
                </td>
                <td>
                    {{$order->title}}
                </td>
                <td>
                    {{\Carbon\Carbon::parse($order->date_from)->format('d-M-Y')}}
                </td>
                <td>
                    {{\Carbon\Carbon::parse($order->date_to)->format('d-M-Y')}}
                </td>
                <td>
                    {{ $order->price }}
                </td>
                <td>
                    {{ $profit }}
                </td>
                <td>{{ $order->totalOperational() }}</td>
                @php
                    $makTotals = $order->totalPerMak();
                @endphp
                <!-- Tampilkan total per MAK -->
                @foreach ($maks as $mak)
                    <td>{{ $makTotals[$mak->id] ?? 0 }}</td>
                @endforeach
                <td>
                    {{ $profit/$order->price*100 }}
                </td>
                <td>
                    {{ $order->totalOperational()/$order->price*100 }}
                </td>
                <td>
                    {{$category_percentage}}
                </td>
                <td>
                    {{$category_percentage*$order->price/100}}
                </td>
                <td>
                    {{$profit-($category_percentage*$order->price/100)}}
                </td>

                @php
                    $rev0=$order->rev0();
                @endphp
                <td class="text-right">
                    {{ $rev0['operational'] }}
                </td>
                <td class="text-right">
                    {{ $rev0['operational']/$rev0['job_price']*100 }}
                </td>
                <td>
                    {{ $order->totalOperational() }}
                </td>
                <td>
                    {{ $order->totalOperational()/$order->price*100 }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>