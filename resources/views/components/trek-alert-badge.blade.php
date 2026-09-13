@props(['count' => 0])

@if($count > 0)
<span class="trek-alert-badge" title="Active safety alert for this trek">
    <i class="fas fa-triangle-exclamation fa-beat-fade"></i>Attention
</span>
@endif

@once
@push('styles')
<style>
.trek-alert-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--color-primary-dark);
    color: white;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 8px rgba(110, 211, 47, 0.4);
    animation: trek-alert-pulse 1.6s infinite;
}

@keyframes trek-alert-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(126, 211, 47, 0.5); }
    50%      { box-shadow: 0 0 0 6px rgba(211,47,47,0); }
}
</style>
@endpush
@endonce