<div>
    <div class="d-flex justify-content-between mb-3">
        <button id="toggleAllBtn" class="btn btn-secondary" onclick="toggleAll()">
            <span id="toggleAllIcon" class="mdi mdi-unfold-more-horizontal"></span> Expand All
        </button>
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
            </div>
            <div class="card-body card-body-detail table-responsive" id="accordion-{{ $orderMak->id }}" style="display: none;">
                @foreach ($orderMak->orderTitle as $title)
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="m-0 badge rounded-pill bg-label-dark text-center">{{ strtoupper($title->title ?? 'Tanpa Judul') }}</h6>
                    </div>
                    <table class="table table-info table-striped table-bordered mb-5" style=" width: 100%; 
            border-collapse: collapse; 
            table-layout: auto;">
                        <thead>
                            <tr style="vertical-align: middle;">
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Item</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Qty 1</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Unit 1</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Qty 2</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Unit 2</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Qty 3</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Unit 3</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Total Qty</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Unit</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Price Unit</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Total Price</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Total Checked</th>
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Total Available to Check</th>
                                
                                @if(auth()->user()->hasAnyRole(['Super_admin','checker']))
                                <th style="white-space: nowrap; padding: 8px; text-align: left; font-weight: bold;">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sumTotalPrice = 0;
                            @endphp
                            @foreach ($title->orderItem as $item)
                                @php
                                    $totalCheckedPerItem = $item->orderChecklist->sum('amount');
                                    $totalAvailableToCheckPerItem = ($item->total_price ?? 0) - $totalCheckedPerItem;
                                @endphp
                          
                                <tr>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">{{ $item->item ?? 'Tanpa Item' }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">{{ $item->qty_1 ?? '0' }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">{{ $item->unit_1 ?? '-' }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">{{ $item->qty_2 ?? '0' }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">{{ $item->unit_2 ?? '-' }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">{{ $item->qty_3 ?? '0' }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">{{ $item->unit_3 ?? '-' }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">{{ $item->qty_total ?? '0' }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">{{ $item->unit_total ?? '-' }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">Rp {{ number_format($item->price_unit ?? 0, 2) }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">Rp {{ number_format($item->total_price ?? 0, 2) }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">Rp {{ number_format($totalCheckedPerItem, 2) }}</td>
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px; ">Rp {{ number_format($totalAvailableToCheckPerItem, 2) }}</td>
                                    @if(auth()->user()->hasAnyRole(['Super_admin','checker']))
                                    <td style="white-space: nowrap; padding: 8px; text-align: left; min-width: 50px;">
                                        <button data-order-item-id="{{$item->id}}" class="btn btn-sm btn-success check-item" title="Check Item"><span class="mdi mdi-check-all"></span></button>
                                    </td>
                                    @endif
                                </tr>
                                @php
                                    $sumTotalPrice += $item->total_price ?? 0;
                                @endphp
                            @endforeach
                            <tfoot>
                                <tr>
                                    <td colspan="10" class="text-center" style="font-weight: bold">Total</td>
                                    <td colspan="4" style="white-space:nowrap;font-weight: bold" colspan="2">Rp {{ number_format($sumTotalPrice, 0, ',', '.') }}</td>
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
    let isAllExpanded = false;

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
            button.innerHTML = '<span id="toggleAllIcon" class="mdi mdi-unfold-more-horizontal"></span> Expand All';
        } else {
            allAccordions.forEach(el => el.style.display = 'block');
            allIcons.forEach(el => {
                el.classList.remove('mdi-chevron-down');
                el.classList.add('mdi-chevron-up');
            });
            button.innerHTML = '<span id="toggleAllIcon" class="mdi mdi-unfold-less-horizontal"></span> Collapse All';
        }

        isAllExpanded = !isAllExpanded;
    }
</script>
