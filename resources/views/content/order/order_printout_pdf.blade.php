<style>
    .header-section table {
        border-collapse: collapse;
        width: 100%;
        font-size: 12px;
        margin: 10px 0;
    }

    .header-section td {
        padding-bottom: 5px;
        vertical-align: text-top;
        font-weight: bold;
    }

    .header-section tr.gap-tr td {
    padding-top: 10px;
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
    

    .qrcode-section {
    text-align: center; /* Pusatkan konten dalam div */
}

.qrcode-section table {
    border-collapse: collapse;
    font-size: 12px;
    margin: 10px auto; /* Auto untuk horizontal centering */
    padding: 5px;
    page-break-before: auto;
}

.qrcode-section td {
    border: 1px solid black;
    padding: 5px;
    text-align: center; /* Pusatkan konten dalam div */

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

    <table width="100%">
        <tr>
            <td width="60" style="padding-right: 15px;">
                <img src="{{ public_path('assets/img/lemigas/logo.png') }}" alt="Logo" width="80">
            </td>
            <td>
                <h3 style="font-size: 14px; font-weight: bold; margin: 0;">
                    KEMENTERIAN ENERGI DAN SUMBER DAYA MINERAL - REPUBLIK INDONESIA
                </h3>
                <p style="font-size: 14px; margin: 3px 0 0 0;">
                    DIREKTORAT JENDERAL MINYAK DAN GAS BUMI
                </p>
                <p style="font-size: 14px; margin: 3px 0 0 0;">
                    BALAI BESAR PENGUJIAN MINYAK DAN GAS BUMI
                </p>
                <p style="font-size: 14px; margin: 3px 0 0 0;">
                    LEMIGAS
                </p>
            </td>
        </tr>
    </table>
    <hr style="border: 1px solid black; margin-bottom: 2px;">
    <hr style="border: 1px solid black; margin-top: 0;">
<!-- Header Section -->
    <div class="header-section">
        <table>
            <tr>
                <td>Divisi</td>
                <td>{{ $order->createdBy->division->division_name }}</td>
            </tr>
            <tr>
                <td rowspan="3">Kontrak</td>
                <td>{{ $order->contract_number }}</td>
            </tr>
            <tr>
                <td>{{ $order->study_lab }}</td>
            </tr>
            <tr>
                <td>{{ number_format($order->contract_price, 0, ',', '.') }} RUPIAH</td>
            </tr>

            <tr class="gap-tr">
                <td>Pelanggan</td>
                <td>{{ $order->customer }}</td>
            </tr>

            <tr class="gap-tr">
                <td rowspan="3">Job</td>
                <td>{{ $order->job_number }}</td>
            </tr>
            <tr>
                <td>{{ $order->title }}</td>
            </tr>
            <tr>
                <td>{{ number_format($order->price, 0, ',', '.') }} RUPIAH</td>
            </tr>

            <tr class="gap-tr">
                <td>DURASI</td>
                <td>{{ \Carbon\Carbon::parse($order->start_date)->format('M Y')}} s.d. {{ \Carbon\Carbon::parse($order->end_date)->format('M Y') }}</td>
            </tr>

            
            <tr class="gap-tr">
                <td>Kelompok</td>
                <td>{{ $order->group }}</td>
            </tr>
            
            
            {{-- <tr>
                
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
            </tr> --}}
        </table>
    </div>

    <div class="detail-section">
        {{-- <table class="">
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
        </table> --}}

        <table>
            <thead>
                <tr class="table_header">
                    <th>MAK</th>
                    <th>Uraian</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                
                @php $sum_all=0; @endphp
                @foreach ($orderMaks as $orderMak)

                @foreach ($orderMak->orderTitle as $title)
                    @php $sum_title=0; @endphp
                    @foreach ($title->orderItem as $item)
                        @php
                            $sum_title+=$item->total_price;
                            $sum_all+=$item->total_price;
                        @endphp
                    @endforeach
                    <tr >
                        @if ( $loop->index==0)
                            <td class="detail-item" rowspan="{{$orderMak->orderTitle->count()}}">
                                {!! $orderMak->is_split ? $orderMak->division->division_name.'<br>' : '' !!}
                                {{ strtoupper($orderMak->mak->mak_code ?? '-').': '.strtoupper($orderMak->mak->mak_name ?? 'Tanpa MAK') }}
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
            <tfoot>
                <tr>
                    <td>
                        <strong>Profit: {{number_format(($order->price-$sum_all)/$order->price*100,2)}} %</strong>
                    </td>
                    <td style="border-right: none;font-weight: bold;">
                        Total Operasional (Rp):
                    </td>
                    <td style="border-left: none; text-align: right; bold;">
                        {{number_format($sum_all, 2)}}
                    </td>
                </tr>
            </tfoot>
        </table>
        
    </div>

    <!-- QR Code Section (Paling Bawah) -->
    <div class="qrcode-section">
        <table>
            <tr>
                <td colspan="2" style="border-right:none;border-top:none;border-left:none;font-weight: bold;">Dievaluasi</td>
            </tr>
            <tr>
                <td style="width: 300px;"><strong>KOORDINATOR PENYIAPAN DAN SARANA PENGUJIAN </strong></td>
                <td style="width: 300px;"><strong>KEPALA BAGIAN UMUM</strong></td>
            </tr>

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
               
            </tr>
           
        </table>

        <br>

        <table>
            <tr>
                <td style="border-right:none;border-top:none;border-left:none;font-weight: bold;">Disetujui</td>
            </tr>
            <tr>
                <td style="width: 300px;"><strong>PIMPINAN BLU</strong></td>
            </tr>

            <tr>
                <td>
                    @if($approver_1)
                        <img src="{{ $approver_3 }}" width="100">
                    @endif
                </td>
               
            </tr>
           
        </table>
    </div>

    
</div>
