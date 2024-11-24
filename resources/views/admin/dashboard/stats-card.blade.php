<div class="card bg-{{ $color ?? 'primary' }} text-white mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">
                    {{ $title }}
                </div>
                <div class="h3 mb-0 font-weight-bold">
                    {{ $value }}
                </div>
                @if(isset($increase) && isset($period))
                <div class="mt-2 text-sm">
                    <i class="fas fa-arrow-up"></i> {{ $increase }} {{ $period }}
                </div>
                @endif
            </div>
            <div>
                <i class="fas fa-{{ $icon ?? 'chart-line' }} fa-2x opacity-75"></i>
            </div>
        </div>
    </div>
    @if(isset($link))
    <div class="card-footer d-flex align-items-center justify-content-between">
        <div class="small text-white">
            <i class="fas fa-angle-right"></i>
        </div>
    </div>
    @endif
</div>