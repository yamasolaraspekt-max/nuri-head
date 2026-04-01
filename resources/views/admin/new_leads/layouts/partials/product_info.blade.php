<div class="p-3 text-white" style="background-color: #1e3955;">
    <div class="d-flex justify-content-between align-items-start flex-wrap">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 48px; height: 48px;">
                {{ $productList->initial }}
            </div>
            <img src="{{ asset('images/employee/' . $productList->image) }}" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover; margin-left:-12px;">
            <div class="ml-3">
                <strong>{{ $productList->product_name ?? 'Produktname' }}</strong><br>
                <span class="small text-light">
                    {{ $productList->department_name }} - 
                    {{ $productList->phase_section }} - 
                    {{ $productList->interest }}
                </span>
                <div class="progress" style="height: 6px; max-width: 150px;">
                    <div class="progress-bar bg-success" style="width: {{ $overallPercent }}%"></div>
                </div>
                <small>{{ $doneActivities }}/{{ $totalActivities }}</small>
            </div>
        </div>
        <div>
            <strong>Start:</strong> 12.07.2025<br>
            <strong>Ende:</strong> -<br>
            <small>
                <strong>P:</strong> 20 Std. 
                <strong>I:</strong> 10 Std. 
                <strong>D:</strong> 10 Std.
            </small>
        </div>
    </div>
</div>
