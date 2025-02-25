<div>
    <div class="d-flex justify-content-between mb-3">
        <button id="toggleAllBtn" class="btn btn-secondary" onclick="toggleAll()">
            {{-- <span id="toggleAllIcon" class="mdi mdi-unfold-more-horizontal" title="Expand All"></span> --}}
            <span id="toggleAllIcon" class="mdi mdi-unfold-less-horizontal" title="Collapse All"></span>
        </button>
        <button type="button" class="btn btn-primary" id="add-mak" title="Add Mak"><span class="mdi mdi-view-grid-plus"></span></button>
    </div>

    @foreach ($orderMaks as $orderMak)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center {{ $orderMak->is_split?'bg-secondary':'' }}">
                <div class="d-flex align-items-center">
                    <button class="btn btn-light btn-xs me-2 toggle-btn" onclick="toggleAccordion({{ $orderMak->id }})">
                        <span id="icon-{{ $orderMak->id }}" class="mdi mdi-chevron-down"></span>
                    </button>
                    @php
                        $split_to='';
                        if ($orderMak->is_split == 1) {
                            $split_to= ('<div class="m-0 badge rounded-pill bg-label-primary text-center">'.$orderMak->division->division_name.'</div>' ?? '');
                        }
                    @endphp
                    {!!$split_to!!}
                    <h5 class="m-1 badge rounded-pill bg-label-dark text-center">{{ strtoupper($orderMak->mak->mak_name ?? 'Tanpa MAK') }}</h5>
                    <div class="m-0 badge rounded-pill bg-label-primary text-center">Rp {{ number_format($this->getTotalPriceForMak($orderMak), 2) }}</div>
                </div>
                <div>
                    <button class="btn btn-sm btn-primary add-title" data-order-mak-id="{{$orderMak->id}}" data-mak="{{$orderMak->mak->mak_code}}-{{$orderMak->mak->mak_name}}" title="Add Title"><span class="mdi mdi-playlist-plus"></span></button>
                           <button id="edit-mak" 
                           data-order-mak-id="{{$orderMak->id}}" 
                           data-mak-id="{{$orderMak->mak->id}}" 
                           data-order-is-split="{{$orderMak->is_split}}" 
                           data-order-split-to="{{$orderMak->split_to}}"
                           class="btn btn-sm btn-success edit-mak" title="Edit Mak"><span class="mdi mdi-view-dashboard-edit"></span>
                    </button>
                    <button data-order-mak-id="{{$orderMak->id}}"  class="btn btn-sm btn-danger delete-mak" title="Delete Mak"><span class="mdi mdi-table-column-remove"></span></button>
                </div>
            </div>
            <div class="card-body card-body-detail table-responsive" id="accordion-{{ $orderMak->id }}" >
                @foreach ($orderMak->orderTitle as $title)
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="m-0 badge rounded-pill bg-label-dark text-center">{{ strtoupper($title->title ?? 'Tanpa Judul') }}</h6>
                        <div>
                            <button class="btn btn-sm btn-primary add-item" 
                            data-order-mak-id="{{$title->id}}" 
                            data-title="{{$title->title}}"
                            data-mak="{{$orderMak->mak->mak_code}}-{{$orderMak->mak->mak_name}}" title="Add Item"><span class="mdi mdi-table-large-plus"></span></button>
                            <button id="edit-title" 
                            data-order-mak-id="{{$title->order_mak_id}}" 
                            data-order-title-id="{{$title->id}}"
                            data-mak="{{$orderMak->mak->mak_code}}-{{$orderMak->mak->mak_name}}"
                            data-title="{{$title->title}}"
                            class="btn btn-sm btn-success edit-title" title="Edit Title"><span class="mdi mdi-playlist-edit"></span></button>
                            <button data-order-title-id="{{$title->id}}" class="btn btn-sm btn-danger delete-title" title="Delete Title"><span class="mdi mdi-playlist-remove"></span></button>
                        </div>
                    </div>
                    <table class="table table-info table-striped table-bordered mb-5">
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
                                <th>Actions</th>
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
                                    <td>
                                        <button id="edit-item" 
                                        data-order-title-id="{{$item->order_title_id}}"
                                        data-order-item-id="{{$item->id}}" 
                                        data-mak="{{$orderMak->mak->mak_code}}-{{$orderMak->mak->mak_name}}"
                                        data-title="{{$title->title}}"
                                        data-order-item="{{$item->item}}"
                                        data-qty-1="{{$item->qty_1}}"
                                        data-unit-1="{{$item->unit_1}}"
                                        data-unit-1="{{$item->unit_1}}"
                                        data-qty-1="{{$item->qty_1}}"
                                        data-qty-2="{{$item->qty_2}}"
                                        data-unit-2="{{$item->unit_2}}"
                                        data-qty-3="{{$item->qty_3}}"
                                        data-unit-3="{{$item->unit_3}}"
                                        data-total-qty="{{$item->qty_total}}"
                                        data-total-unit="{{$item->qty_unit}}"
                                        data-price-unit="{{ number_format($item->price_unit, 0, ',', '') }}"
                                        data-total-price="{{ number_format($item->total_price, 0, ',', '') }}"
                                        class="btn btn-sm btn-success edit-item" title="Edit Item"><span class="mdi mdi-table-edit"></span></button>
                                        <button data-order-item-id="{{$item->id}}" class="btn btn-sm btn-danger delete-item" title="Delete Item"><span class="mdi mdi-table-row-remove"></span></button>
                                    </td>
                                </tr>
                                @php
                                    $sumTotalPrice += $item->total_price ?? 0;
                                @endphp
                            @endforeach
                            <tfoot>
                                <tr>
                                    <td colspan="10" class="text-center" style="font-weight: bold">Total</td>
                                    <td style="white-space:nowrap;font-weight: bold" colspan="2">Rp {{ number_format($sumTotalPrice, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<script>
    let isAllExpanded = true;

    function toggleAccordion(id) {
        let accordion = document.getElementById('accordion-' + id);
        let icon = document.getElementById('icon-' + id);

        if (accordion.style.display === 'none') {
            accordion.style.display = 'block';
            icon.classList.remove('mdi-chevron-down');
            icon.classList.add('mdi-chevron-up');
        } else {
            accordion.style.display = 'none';
            icon.classList.remove('mdi-chevron-up');
            icon.classList.add('mdi-chevron-down');
        }
    }

    function toggleAll() {
        let allAccordions = document.querySelectorAll('.card-body-detail');
        let allIcons = document.querySelectorAll('.toggle-btn span');
        let button = document.getElementById('toggleAllBtn');
        let buttonIcon = document.getElementById('toggleAllIcon');

        if (isAllExpanded) {
            allAccordions.forEach(el => el.style.display = 'none');
            allIcons.forEach(el => {
                el.classList.remove('mdi-chevron-up');
                el.classList.add('mdi-chevron-down');
            });
            button.innerHTML = '<span id="toggleAllIcon" class="mdi mdi-unfold-more-horizontal" title="Expand All"></span>';
        } else {
            allAccordions.forEach(el => el.style.display = 'block');
            allIcons.forEach(el => {
                el.classList.remove('mdi-chevron-down');
                el.classList.add('mdi-chevron-up');
            });
            button.innerHTML = '<span id="toggleAllIcon" class="mdi mdi-unfold-less-horizontal" title="Collapse All"></span>';
        }

        isAllExpanded = !isAllExpanded;
    }
    
</script>
