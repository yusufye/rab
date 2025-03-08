<table class="table" border="1">
    <thead>
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Uraian</th>
            @foreach ($revisions as $rev)
                <th colspan="10">Rev {{ $rev }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($revisions as $rev)
                
                {{-- <th>Qty 1</th> <th>Qty 1</th> <th>Unit 1</th> <th>Qty 2</th> <th>Unit 2</th> <th>Qty 3</th> <th>Unit 3</th> --}}
                <th colspan="6">Rincian Satuan</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Unit Biaya(Rp)</th>
                <th>Total(Rp)</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $jobNumber => $orderGroup)
    <tr>
        <td colspan="{{ count($revisions) * 11 + 1 }}"><strong>{{ $jobNumber }}</strong></td>
    </tr>

    @foreach ($orderGroup->pluck('orderMak')->flatten()->groupBy(fn($mak) => json_encode([
        'mak_name' => $mak->mak->mak_name,
        'division' => $mak->is_split ? ($mak->division->division_name ?? 'Tanpa Divisi') : 'Tidak Terbagi'
    ])) as $groupKey => $ordersByMak)

        @php
            $groupKeys    = json_decode($groupKey, true);
            $makName      = $groupKeys['mak_name'];
            $divisionInfo = $groupKeys['division'];
            
            // Menyimpan total per MAK per revisi
            $sum_mak_per_rev = array_fill_keys($revisions->toArray(), 0);
        @endphp

        {{-- Header MAK --}}
        <tr class="mak_header">
            <td colspan="{{ count($revisions) * 12 + 1 }}">
                {!! $divisionInfo !== 'Tidak Terbagi' ? "(Split ke: $divisionInfo)<br>" : '' !!}
                <strong>{{ $makName }}</strong>
            </td>
        </tr>

        @php $no = 0; @endphp
        @foreach ($ordersByMak->pluck('orderTitle')->flatten()->groupBy('title') as $title => $orderTitles)
            @php
                $title_ids=$orderTitles->pluck('id')->toArray();
            @endphp
            @php $no++; @endphp
            <tr class="title_header">
                <td>{{ $no }}</td>
                <td colspan="{{ count($revisions) * 10 + 1 }}">&nbsp;&nbsp;&nbsp;{{ $title ?? 'Tanpa Judul' }}</td>
            </tr>

            @foreach ($orderTitles->pluck('orderItem')->flatten()->groupBy('item') as $itemName => $orderItems)
                <tr>
                    <td></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $itemName ?? 'Tanpa Item' }}</td>

                    @foreach ($revisions as $rev)
                        @php
                            $revData = $orderGroup->where('rev', $rev)->pluck('orderMak')
                                ->flatten()->pluck('orderTitle')
                                ->flatten()->pluck('orderItem')
                                ->flatten()->where('item', $itemName)
                                ->where('order_title_id', $title_ids[$rev])
                                ->first();

                            $total_price = optional($revData)->total_price ?? 0;
                            $sum_mak_per_rev[$rev] += $total_price;
                        @endphp

                        <td class="item_rincian">{{ optional($revData)->qty_1 ?? '-' }}</td>
                        <td class="item_rincian">{{ optional($revData)->unit_1 ?? '-' }}</td>
                        <td class="item_rincian">{{ optional($revData)->qty_2 ?? '-' }}</td>
                        <td class="item_rincian">{{ optional($revData)->unit_2 ?? '-' }}</td>
                        <td class="item_rincian">{{ optional($revData)->qty_3 ?? '-' }}</td>
                        <td class="item_rincian">{{ optional($revData)->unit_3 ?? '-' }}</td>
                        <td>{{ optional($revData)->qty_total ?? '-' }}</td>
                        <td>{{ optional($revData)->qty_unit ?? '-' }}</td>
                        <td>{{ number_format(optional($revData)->price_unit ?? 0, 2) }}</td>
                        <td>{{ number_format($total_price, 2) }}</td>
                    @endforeach
                </tr>
            @endforeach
        @endforeach

        {{-- Total Per MAK Per Revisi --}}
        <tr>
            <td colspan="2"><strong>Total {{ $makName }}</strong></td>
            @foreach ($revisions as $rev)
                <td colspan="9"></td>
                <td><strong>{{ number_format($sum_mak_per_rev[$rev], 2) }}</strong></td>
            @endforeach
        </tr>

    @endforeach
@endforeach

    </tbody>
</table>
