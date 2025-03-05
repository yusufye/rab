
        <table>
            <tr>
                <td><strong>Job Number </strong></td>
                <td>{{ $order->job_number }}</td>
                <td><strong>Title </strong></td>
                <td>{{ $order->title }}</td>
            </tr>
            <tr>
                <td><strong>Category </strong></td>
                <td>{{ $order->category->category_name ?? 'Unknown' }}</td>
                <td><strong>Group</strong></td>
                <td>{{ $order->group }}</td>
            </tr>
            <tr>
                <td><strong>Customer </strong></td>
                <td>{{ $order->customer }}</td>
                <td><strong>Study/Lab </strong></td>
                <td>{{ $order->study_lab }}</td>
            </tr>
            <tr>
                <td><strong>Date </strong></td>
                <td>{{ \Carbon\Carbon::parse($order->start_date)->format('d-M-Y')}} - {{ \Carbon\Carbon::parse($order->end_date)->format('d-M-Y') }}</td>
                <td><strong>Price </strong></td>
                <td>Rp {{ number_format($order->price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Split </strong></td>
                <td>
                    
                @foreach ($sumPerDiv as $division => $amount)
                    <strong>{{ $division }}</strong> Rp {{ number_format($amount, 0, ',', '.') }}<br>
                @endforeach
                    
                </td>
                <td><strong>Biaya Operasional </strong></td>
                <td>Rp {{ number_format($sumItem, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><strong>Profit </strong></td>
                <td>Rp {{ number_format($order->price-$sumItem, 0, ',', '.') }}</td>
            </tr>
        </table>

        <table>
            <thead>
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
                        <td>{{ $percentage->title }}</td>
                        <td>{{ $percentage->percentage }}%</td>
                        <td>
                            {{ number_format($calc, 2) }}
                        </td>
                    </tr>
                    @php
                        $total_pct+=$percentage->percentage;
                        $total_calc+=$calc;
                    @endphp
                @endforeach
                
                
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td>{{$total_pct}}%</td>
                    <td>{{ number_format($total_calc, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Uraian</th>
                    <th colspan="6">Rincian Satuan</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Unit Biaya(Rp)</th>
                    <th>Total(Rp)</th>
                </tr>
            </thead>
        <tbody>
            
            @foreach ($orderMaks as $orderMak)
            @php
                $sum_mak=0;
            @endphp
                <tr>
                    <td colspan="12" style="text-align: left;" class="text-start">
                        <strong>{!! $orderMak->is_split ? $orderMak->division->division_name.'<br>' : '' !!}</strong>
                        <strong>{{ strtoupper($orderMak->mak->mak_code ?? '') }} - {{ strtoupper($orderMak->mak->mak_name ?? 'Tanpa MAK') }}</strong>
                    </td>
                </tr>
                @php
                    $no=1;
                @endphp
                @foreach ($orderMak->orderTitle as $title)
                    <tr>
                        
                        <td>
                            {{$no}}
                            @php $no++; @endphp
                        </td>
                        <td colspan="11">
                            {{ strtoupper($title->title ?? 'Tanpa Judul') }}
                        </td>
                    </tr>

                    @foreach ($title->orderItem as $item)
                        <tr>
                            <td></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $item->item ?? 'Tanpa Item' }}</td>
                            <td>{{ $item->qty_1 ?? '0' }}</td>
                            <td>{{ $item->unit_1 ?? '-' }}</td>
                            <td>{{ $item->qty_2 ?? '0' }}</td>
                            <td>{{ $item->unit_2 ?? '-' }}</td>
                            <td>{{ $item->qty_3 ?? '0' }}</td>
                            <td>{{ $item->unit_3 ?? '-' }}</td>
                            <td>{{ $item->qty_total ?? '0' }}</td>
                            <td>{{ $item->qty_unit ?? '0' }}</td>
                            <td>Rp {{ number_format($item->price_unit ?? 0, 2) }}</td>
                            <td>Rp {{ number_format($item->total_price ?? 0, 2) }}</td>
                            @php
                                $sum_mak+=$item->total_price;
                            @endphp
                        </tr>

                    @endforeach
                @endforeach
                <tr>
                    <td colspan="11">
                        Total
                    </td>
                    <td>
                        Rp {{ number_format($sum_mak, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        </table>
