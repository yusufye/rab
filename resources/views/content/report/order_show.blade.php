<table class="table" border="1">
    <thead>
        <tr>
            <th>Job Number / MAK Name / Order Title / Order Item</th>
            @foreach ($revisions as $rev)
                <th colspan="9">Rev {{ $rev }}</th>
            @endforeach
        </tr>
        <tr>
            <th></th>
            @foreach ($revisions as $rev)
                <th>Qty 1</th>
                <th>Unit 1</th>
                <th>Qty 2</th>
                <th>Unit 2</th>
                <th>Qty 3</th>
                <th>Unit 3</th>
                <th>Total Qty</th>
                <th>Price Unit</th>
                <th>Total Price</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $jobNumber => $orderGroup)
            {{-- Job Number Header --}}
            <tr>
                <td colspan="{{ count($revisions) * 9 + 1 }}"><strong>{{ $jobNumber }}</strong></td>
            </tr>
    
            @foreach ($orderGroup->pluck('orderMak')->flatten()->groupBy('mak.mak_name') as $makName => $ordersByMak)
                <tr>
                    <td><strong>{{ $makName ?? 'Tanpa MAK' }}</strong></td>
                </tr>
    
                @foreach ($ordersByMak->pluck('orderTitle')->flatten()->groupBy('title') as $title => $orderTitles)
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;{{ $title ?? 'Tanpa Judul' }}</td>
                    </tr>
    
                    @foreach ($orderTitles->pluck('orderItem')->flatten()->groupBy('item') as $itemName => $orderItems)
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $itemName ?? 'Tanpa Item' }}</td>
    
                            @foreach ($revisions as $rev)
                                @php
                                    $revData = $orderGroup->where('rev', $rev)->pluck('orderMak')
                                        ->flatten()->pluck('orderTitle')
                                        ->flatten()->pluck('orderItem')
                                        ->flatten()->where('item', $itemName)
                                        ->first();
                                @endphp
                                <td>{{ optional($revData)->qty_1 ?? '-' }}</td>
                                <td>{{ optional($revData)->unit_1 ?? '-' }}</td>
                                <td>{{ optional($revData)->qty_2 ?? '-' }}</td>
                                <td>{{ optional($revData)->unit_2 ?? '-' }}</td>
                                <td>{{ optional($revData)->qty_3 ?? '-' }}</td>
                                <td>{{ optional($revData)->unit_3 ?? '-' }}</td>
                                <td>{{ optional($revData)->qty_total ?? '-' }}</td>
                                <td>{{ number_format(optional($revData)->price_unit ?? 0, 2) }}</td>
                                <td>{{ number_format(optional($revData)->total_price ?? 0, 2) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        @endforeach
    </tbody>
    
</table>
