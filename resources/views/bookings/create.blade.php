@extends('layouts.app')

@section('title', 'Book Trek - ' . $package->name)

@section('content')
<section class="section" style="background: var(--color-bg);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="margin-bottom: var(--space-lg);">
                <a href="{{ route('tours.show', $package) }}" style="color: var(--color-text-light); text-decoration: none; font-size: 14px;">
                    <i class="fas fa-arrow-left"></i> Back to Trek Details
                </a>
            </div>

            <div class="card" style="margin-bottom: var(--space-xl); background: linear-gradient(135deg, var(--color-primary) 0%, #2E7D32 100%); color: white; border: none;">
                <div class="card-body" style="padding: var(--space-xl);">
                    <h2 style="font-size: 28px; font-weight: 700; color: white; margin-bottom: var(--space-md);">
                        {{ $package->name }}
                    </h2>
                    <p style="color: rgba(255,255,255,0.9); margin-bottom: var(--space-lg);">
                        {{ $package->description }}
                    </p>
                    
                    <div class="flex gap-lg" style="flex-wrap: wrap; margin-bottom: var(--space-lg); font-size: 14px;">
                        <div class="flex" style="align-items: center; gap: var(--space-sm);">
                            <i class="fas fa-calendar"></i>
                            <span>{{ $package->duration_days }} {{ Str::plural('Day', $package->duration_days) }}</span>
                        </div>
                        <div class="flex" style="align-items: center; gap: var(--space-sm);">
                            <i class="fas fa-users"></i>
                            <span>Max {{ $package->max_participants }} People</span>
                        </div>
                        <div class="flex" style="align-items: center; gap: var(--space-sm);">
                            <i class="fas fa-signal"></i>
                            <span style="text-transform: capitalize;">{{ $package->difficulty_level }}</span>
                        </div>
                    </div>

                    <div style="border-top: 1px solid rgba(255,255,255,0.3); padding-top: var(--space-lg); display: flex; align-items: baseline; gap: var(--space-md);">
                        <div style="font-size: 36px; font-weight: 700;">Rs. {{ number_format($package->price, 0) }}</div>
                        <div style="opacity: 0.9;">per person</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body" style="padding: var(--space-xl);">
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-xl); color: var(--color-text);">
                        <i class="fas fa-ticket-alt"></i> Complete Your Booking
                    </h3>

                    @if($errors->any())
                        <div class="alert alert-error" style="margin-bottom: var(--space-xl);">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                <strong>Please fix the following errors:</strong>
                                <ul style="margin-top: 8px; padding-left: 20px;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
                        @csrf
                        <input type="hidden" name="tour_package_id" value="{{ $package->id }}">

                        <div class="form-group">
                            <label class="form-label">
                                Number of Trekkers <span class="required">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="participants" 
                                id="participants"
                                class="form-input" 
                                min="1" 
                                max="{{ $package->max_participants }}"
                                value="{{ old('participants', 1) }}"
                                required
                                onchange="updateTotal()"
                            >
                            <div class="form-helper">Maximum {{ $package->max_participants }} trekkers per group</div>
                            @error('participants')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Trek Date <span class="required">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="tour_date" 
                                id="tour_date"
                                class="form-input" 
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                value="{{ old('tour_date') }}"
                                required
                            >
                            <div class="form-helper">Select a date at least 1 day in advance</div>
                            @error('tour_date')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Payment Method <span class="required">*</span>
                            </label>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-md);">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="esewa" {{ old('payment_method') == 'esewa' ? 'checked' : '' }} required>
                                    <div class="option-content">
                                        <i class="fas fa-wallet" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                                        <span>eSewa</span>
                                    </div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="khalti" {{ old('payment_method') == 'khalti' ? 'checked' : '' }}>
                                    <div class="option-content">
                                        <i class="fas fa-mobile-alt" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                                        <span>Khalti</span>
                                    </div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="stripe" {{ old('payment_method') == 'stripe' ? 'checked' : '' }}>
                                    <div class="option-content">
                                        <i class="fab fa-cc-stripe" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                                        <span>Card Payment</span>
                                    </div>
                                </label>
                            </div>

                            @error('payment_method')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="padding: var(--space-xl); background: #E8F5E9; border-radius: var(--radius-md); border: 2px solid var(--color-primary); margin: var(--space-xl) 0;">
                            <div class="flex-between" style="margin-bottom: var(--space-md); font-size: 14px; color: var(--color-text-light);">
                                <span>Price per person</span>
                                <span id="pricePerPerson">Rs. {{ number_format($package->price, 0) }}</span>
                            </div>
                            <div class="flex-between" style="margin-bottom: var(--space-lg); font-size: 14px; color: var(--color-text-light);">
                                <span>Number of trekkers</span>
                                <span id="participantCount">1</span>
                            </div>
                            <div class="flex-between" style="padding-top: var(--space-md); border-top: 2px solid var(--color-primary);">
                                <span style="font-size: 18px; font-weight: 700;">Total Amount</span>
                                <span id="totalAmount" style="font-size: 28px; font-weight: 700; color: var(--color-primary);">Rs. {{ number_format($package->price, 0) }}</span>
                            </div>
                        </div>

                        <div class="flex gap-md">
                            <a href="{{ route('tours.show', $package) }}" class="btn btn-secondary btn-lg" style="flex: 1;">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-cta btn-lg" style="flex: 2;">
                                <i class="fas fa-check-circle"></i> Proceed to Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .payment-option {
        cursor: pointer;
        display: block;
    }

    .payment-option input[type="radio"] {
        display: none;
    }

    .option-content {
        padding: var(--space-lg);
        border: 2px solid #E0E0E0;
        border-radius: var(--radius-md);
        text-align: center;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .payment-option:hover .option-content {
        border-color: var(--color-primary);
        background: #F5F5F5;
    }

    .payment-option input:checked + .option-content {
        border-color: var(--color-primary);
        background: #E8F5E9;
        color: var(--color-primary);
    }
</style>
@endpush

@push('scripts')
<script>
    const pricePerPerson = {{ $package->price }};

    function updateTotal() {
        const participants = parseInt(document.getElementById('participants').value) || 1;
        const total = participants * pricePerPerson;
        
        document.getElementById('participantCount').textContent = participants;
        document.getElementById('totalAmount').textContent = 'Rs. ' + total.toLocaleString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateTotal();
    });
</script>
@endpush
@endsection