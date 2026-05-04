@extends('layouts.app')

@section('title', 'Create Package - Admin')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-plus"></i> Create New Package</h1>
        <a href="{{ route('admin.packages') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.packages.store') }}">
            @csrf

            <div class="form-section">
                <h3>Basic Information</h3>

                <div class="form-group">
                    <label for="name">Package Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea name="description" id="description" rows="4" required>{{ old('description') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (Rs.) *</label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="duration_days">Duration (Days) *</label>
                        <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', 1) }}" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="difficulty_level">Difficulty *</label>
                        <select name="difficulty_level" id="difficulty_level" required>
                            <option value="easy">Easy</option>
                            <option value="moderate">Moderate</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="max_participants">Max Participants *</label>
                        <input type="number" name="max_participants" id="max_participants" value="{{ old('max_participants', 10) }}" min="1" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Start Location</h3>

                <div class="form-group">
                    <label for="start_location_name">Location Name *</label>
                    <input type="text" name="start_location_name" id="start_location_name" value="{{ old('start_location_name') }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_lat">Latitude *</label>
                        <input type="number" name="start_lat" id="start_lat" value="{{ old('start_lat') }}" step="0.000001" required>
                    </div>

                    <div class="form-group">
                        <label for="start_lng">Longitude *</label>
                        <input type="number" name="start_lng" id="start_lng" value="{{ old('start_lng') }}" step="0.000001" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>End Location</h3>

                <div class="form-group">
                    <label for="end_location_name">Location Name *</label>
                    <input type="text" name="end_location_name" id="end_location_name" value="{{ old('end_location_name') }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="end_lat">Latitude *</label>
                        <input type="number" name="end_lat" id="end_lat" value="{{ old('end_lat') }}" step="0.000001" required>
                    </div>

                    <div class="form-group">
                        <label for="end_lng">Longitude *</label>
                        <input type="number" name="end_lng" id="end_lng" value="{{ old('end_lng') }}" step="0.000001" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" checked>
                        Activate package immediately
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Package
                </button>
                <a href="{{ route('admin.packages') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .form-card {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        max-width: 800px;
    }

    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .form-section h3 {
        margin-bottom: 1.5rem;
        color: #667eea;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #667eea;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        padding-top: 1rem;
    }

    .form-actions .btn {
        flex: 1;
    }

    input[type="checkbox"] {
        width: auto;
        margin-right: 0.5rem;
    }
</style>
@endpush
@endsection