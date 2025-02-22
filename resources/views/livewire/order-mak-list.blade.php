<div>
    <div class="mb-2">
        <button wire:click="$emit('expandAll')" class="btn btn-primary">Expand All</button>
        <button wire:click="$emit('collapseAll')" class="btn btn-secondary">Collapse All</button>
    </div>

    <div id="accordion">
        @foreach ($orderMaks->orderMak as $orderMak)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{ $orderMak->id }}">
                            {{ $orderMak->mak->mak_name }} 
                            @if($orderMak->is_split)
                                (Split ke: {{ $orderMak->division->division_name ?? 'Tanpa Divisi' }})
                            @endif
                        </button>
                    </h5>
                </div>

                <div id="collapse{{ $orderMak->id }}" class="collapse">
                    <div class="card-body">
                        @foreach ($orderMak->orderTitle as $orderTitle)
                            <h6>{{ $orderTitle->title ?? 'Tanpa Judul' }}</h6>
                            <table class="table table-bordered">
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
                                    @foreach ($orderTitle->orderItem as $orderItem)
                                        <tr>
                                            <td>{{ $orderItem->item }}</td>
                                            <td>{{ $orderItem->qty_1 }}</td>
                                            <td>{{ $orderItem->unit_1 }}</td>
                                            <td>{{ $orderItem->qty_2 }}</td>
                                            <td>{{ $orderItem->unit_2 }}</td>
                                            <td>{{ $orderItem->qty_3 }}</td>
                                            <td>{{ $orderItem->unit_3 }}</td>
                                            <td>{{ $orderItem->qty_total }}</td>
                                            <td>{{ $orderItem->qty_unit }}</td>
                                            <td>{{ number_format($orderItem->price_unit, 2) }}</td>
                                            <td>{{ number_format($orderItem->total_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('expandAll', function () {
            document.querySelectorAll('.collapse').forEach(el => el.classList.add('show'));
        });

        Livewire.on('collapseAll', function () {
            document.querySelectorAll('.collapse').forEach(el => el.classList.remove('show'));
        });
    });
</script>
