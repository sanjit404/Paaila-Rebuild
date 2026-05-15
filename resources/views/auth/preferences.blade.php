@extends('layouts.app')

@section('title', isset($preferences) ? 'Edit Preferences' : 'What Do You Prefer?')

@php
    $isEditing = request()->routeIs('preferences.edit') || (isset($preferences) && $preferences?->preferences_set);
@endphp

@section('content')
<div style="background: var(--color-bg); min-height: calc(100vh - 70px); padding: var(--space-xl) var(--space-md);">
    <div style="max-width: 720px; margin: 0 auto;">

        <div style="text-align: center; margin-bottom: var(--space-xl);">
            <div style="width: 72px; height: 72px; background: var(--color-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-lg);">
                <i class="fas fa-mountain" style="font-size: 32px; color: white;"></i>
            </div>
            <h1 style="font-size: 26px; font-weight: 700; margin-bottom: var(--space-sm);">
                {{ $isEditing ? 'Update Your Preferences' : 'Welcome, ' . auth()->user()->name . '!' }}
            </h1>
            <p style="color: var(--color-text-light); font-size: 15px; max-width: 480px; margin: 0 auto;">
                {{ $isEditing
                    ? 'Update what kind of treks you enjoy — your recommendations will refresh.'
                    : 'Tell us what excites you. We\'ll show you treks you\'ll actually want to do.' }}
            </p>
            @if(!$isEditing)
            <div style="margin-top: var(--space-md); font-size: 13px; color: var(--color-text-light);">
                <i class="fas fa-clock"></i> Takes about 30 seconds
            </div>
            @endif
        </div>


        <form method="POST"
              action="{{ $isEditing ? route('preferences.update') : route('preferences.store') }}"
              id="preferenceForm">
            @csrf
            @if($isEditing)
                @method('PUT')
            @endif

            <div class="pref-section" style="margin-bottom: var(--space-xl);">
                <div class="pref-section-header">
                    <div class="step-badge">1</div>
                    <div>
                        <h3 class="pref-section-title">What kind of treks do you enjoy?</h3>
                        <p class="pref-section-sub">Select all that interest you</p>
                    </div>
                </div>

                <div style="padding: var(--space-xl);">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: var(--space-md);">
                        @foreach($trekTypes as $type)
                        @php
                            $isChecked = $isEditing
                                && $preferences->trek_types
                                && in_array($type['value'], $preferences->trek_types);
                        @endphp
                        <label class="trek-type-label">
                            <input
                                type="checkbox"
                                name="trek_types[]"
                                value="{{ $type['value'] }}"
                                class="trek-type-input"
                                {{ $isChecked || old('trek_types') && in_array($type['value'], old('trek_types', [])) ? 'checked' : '' }}
                                style="display: none;"
                            >
                            <div class="trek-type-card {{ $isChecked ? 'selected' : '' }}"
                                 style="--type-color: {{ $type['color'] }}; --type-bg: {{ $type['bg'] }};">
                                <div class="trek-type-icon" style="background: {{ $type['bg'] }};">
                                    <i class="fas {{ $type['icon'] }}" style="color: {{ $type['color'] }};"></i>
                                </div>
                                <div class="trek-type-name">{{ $type['label'] }}</div>
                                <div class="trek-type-desc">{{ $type['desc'] }}</div>
                                <div class="trek-type-check">
                                    <i class="fas fa-check-circle" style="color: var(--color-success);"></i>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('trek_types')
                        <div style="color: var(--color-error); font-size: 13px; margin-top: var(--space-sm);">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="pref-section" style="margin-bottom: var(--space-xl);">
                <div class="pref-section-header">
                    <div class="step-badge">2</div>
                    <div>
                        <h3 class="pref-section-title">How challenging do you like it?</h3>
                        <p class="pref-section-sub">Pick one that fits your fitness level</p>
                    </div>
                </div>

                <div style="padding: var(--space-xl);">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: var(--space-md);">
                        @foreach($difficulties as $diff)
                        @php
                            $isSelected = $isEditing
                                ? $preferences->difficulty === $diff['value']
                                : old('difficulty') === $diff['value'];
                        @endphp
                        <label class="single-option-label">
                            <input
                                type="radio"
                                name="difficulty"
                                value="{{ $diff['value'] }}"
                                class="single-input"
                                {{ $isSelected ? 'checked' : '' }}
                                style="display: none;"
                            >
                            <div class="single-card {{ $isSelected ? 'selected' : '' }}">
                                <i class="fas {{ $diff['icon'] }}"
                                   style="font-size: 28px; color: {{ $diff['color'] }}; margin-bottom: var(--space-sm); display: block;"></i>
                                <div style="font-weight: 700; font-size: 15px; margin-bottom: 4px;">{{ $diff['label'] }}</div>
                                <div style="font-size: 12px; color: var(--color-text-light);">{{ $diff['desc'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pref-section" style="margin-bottom: var(--space-xl);">
                <div class="pref-section-header">
                    <div class="step-badge">3</div>
                    <div>
                        <h3 class="pref-section-title">How long do you prefer?</h3>
                        <p class="pref-section-sub">Typical trek duration</p>
                    </div>
                </div>

                <div style="padding: var(--space-xl);">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: var(--space-md);">
                        @foreach($durations as $dur)
                        @php
                            $isSelected = $isEditing
                                ? $preferences->duration === $dur['value']
                                : old('duration') === $dur['value'];
                        @endphp
                        <label class="single-option-label">
                            <input
                                type="radio"
                                name="duration"
                                value="{{ $dur['value'] }}"
                                class="single-input"
                                {{ $isSelected ? 'checked' : '' }}
                                style="display: none;"
                            >
                            <div class="single-card {{ $isSelected ? 'selected' : '' }}">
                                <i class="fas {{ $dur['icon'] }}"
                                   style="font-size: 28px; color: var(--color-primary); margin-bottom: var(--space-sm); display: block;"></i>
                                <div style="font-weight: 700; font-size: 15px; margin-bottom: 4px;">{{ $dur['label'] }}</div>
                                <div style="font-size: 12px; color: var(--color-text-light);">{{ $dur['desc'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pref-section" style="margin-bottom: var(--space-xl);">
                <div class="pref-section-header">
                    <div class="step-badge">4</div>
                    <div>
                        <h3 class="pref-section-title">What is your budget?</h3>
                        <p class="pref-section-sub">Per person, per trek</p>
                    </div>
                </div>

                <div style="padding: var(--space-xl);">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: var(--space-md);">
                        @foreach($budgets as $budget)
                        @php
                            $isSelected = $isEditing
                                ? $preferences->budget === $budget['value']
                                : old('budget') === $budget['value'];
                        @endphp
                        <label class="single-option-label">
                            <input
                                type="radio"
                                name="budget"
                                value="{{ $budget['value'] }}"
                                class="single-input"
                                {{ $isSelected ? 'checked' : '' }}
                                style="display: none;"
                            >
                            <div class="single-card {{ $isSelected ? 'selected' : '' }}">
                                <i class="fas {{ $budget['icon'] }}"
                                   style="font-size: 28px; color: {{ $budget['color'] }}; margin-bottom: var(--space-sm); display: block;"></i>
                                <div style="font-weight: 700; font-size: 15px; margin-bottom: 4px;">{{ $budget['label'] }}</div>
                                <div style="font-size: 12px; color: var(--color-text-light);">{{ $budget['desc'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: var(--space-md);">
                @if(!$isEditing)
                    <a href="{{ route('home') }}"
                       style="flex: 1; text-align: center; padding: var(--space-lg); border: 2px solid #E0E0E0; border-radius: var(--radius-md); color: var(--color-text-light); text-decoration: none; font-weight: 500; font-size: 15px;">
                        Skip for now
                    </a>
                @else
                    <a href="{{ route('home') }}"
                       class="btn btn-secondary btn-lg" style="flex: 1;">
                        Cancel
                    </a>
                @endif

                <button type="submit" class="btn btn-cta btn-lg" style="flex: 3;">
                    <i class="fas fa-check-circle"></i>
                    {{ $isEditing ? 'Update Preferences' : 'Show My Recommendations' }}
                </button>
            </div>

        </form>
    </div>
</div>

@push('styles')
<style>
    .pref-section {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .pref-section-header {
        padding: var(--space-lg) var(--space-xl);
        border-bottom: 1px solid #E0E0E0;
        display: flex;
        align-items: center;
        gap: var(--space-md);
    }

    .step-badge {
        width: 32px; height: 32px;
        background: var(--color-primary);
        color: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 14px;
        flex-shrink: 0;
    }

    .pref-section-title {
        font-size: 17px; font-weight: 700; margin: 0;
        color: var(--color-text);
    }

    .pref-section-sub {
        font-size: 13px; color: var(--color-text-light);
        margin: 4px 0 0;
    }

    .trek-type-label { cursor: pointer; display: block; }

    .trek-type-card {
        padding: var(--space-lg);
        border: 2px solid #E0E0E0;
        border-radius: var(--radius-md);
        text-align: center;
        transition: all 0.15s ease;
        height: 100%;
        position: relative;
        user-select: none;
    }

    .trek-type-card:hover {
        border-color: var(--type-color, var(--color-primary));
        background: var(--type-bg, #F5F5F5);
    }

    .trek-type-card.selected {
        border-color: var(--color-primary);
        background: #F1F8F1;
        box-shadow: 0 2px 8px rgba(27,94,32,0.15);
    }

    .trek-type-icon {
        width: 52px; height: 52px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto var(--space-sm);
        font-size: 22px;
    }

    .trek-type-name {
        font-weight: 700; font-size: 13px;
        margin-bottom: 4px; color: var(--color-text);
    }

    .trek-type-desc {
        font-size: 11px; color: var(--color-text-light);
    }

    .trek-type-check {
        position: absolute; top: 8px; right: 8px;
        display: none;
    }

    .trek-type-card.selected .trek-type-check { display: block; }

    .single-option-label { cursor: pointer; display: block; }

    .single-card {
        padding: var(--space-lg);
        border: 2px solid #E0E0E0;
        border-radius: var(--radius-md);
        text-align: center;
        transition: all 0.15s ease;
        user-select: none;
    }

    .single-card:hover {
        border-color: var(--color-primary);
        background: #FAFAFA;
    }

    .single-card.selected {
        border-color: var(--color-primary);
        background: #F1F8F1;
        box-shadow: 0 2px 8px rgba(27,94,32,0.15);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.trek-type-label').forEach(function (label) {
        label.addEventListener('click', function () {
            var input = this.querySelector('.trek-type-input');
            var card  = this.querySelector('.trek-type-card');
            setTimeout(function () {
                card.classList.toggle('selected', input.checked);
            }, 0);
        });
    });
    document.querySelectorAll('.single-option-label').forEach(function (label) {
        label.addEventListener('click', function () {
            var input = this.querySelector('.single-input');
            var name  = input.name;

            document.querySelectorAll('input[name="' + name + '"]').forEach(function (r) {
                r.closest('.single-option-label')
                 .querySelector('.single-card')
                 .classList.remove('selected');
            });

            var card = this.querySelector('.single-card');
            setTimeout(function () { card.classList.add('selected'); }, 0);
        });
    });
});
</script>
@endpush
@endsection