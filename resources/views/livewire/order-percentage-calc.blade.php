<div class="card">
<div class="card-header">
<h5>Rincian Porsi Peruntukan</h5>
</div>
<div class="card-body">
    <table class="table table-striped-columns">
        <thead class="table-success">
            <tr class="text-center">
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
                    <td class="text-end">
                        {{ number_format($calc, 2) }}
                    </td>
                </tr>
                @php
                    $total_pct+=$percentage->percentage;
                    $total_calc+=$calc;
                @endphp
            @endforeach
            <tfoot class="table-secondary">
                <tr>
                    <td colspan="2">Total</td>
                    <td>{{$total_pct}}</td>
                    <td class="text-end">{{ number_format($total_calc, 2) }}</td>
                </tr>
            </tfoot>
            
        </tbody>
    </table>
</div>
</div>
