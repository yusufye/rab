<div class="row">
    <!-- Total Items (Non-Split) -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Biaya Operasional</h5>
                <p class="fw-bold">Rp {{ number_format($totalItem, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Profit Calculation -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Profit</h5>
                <p class="fw-bold">Rp {{ number_format($profit, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Total Split Items (by Division) -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Split</h5>
                <ul>
                    @foreach ($totalSplitItems as $division => $amount)
                        <li><strong>{{ $division }}:</strong> Rp {{ number_format($amount, 0, ',', '.') }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
