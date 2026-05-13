@extends('layouts.app')

@section('title', 'Create Package - Admin')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>
            <i class="fas fa-plus"></i>
            Create New Tour Package
        </h1>

        <a href="{{ route('admin.packages') }}" style="text-decoration:none; color:var(--text-primary-light);">
            <i class="fas fa-arrow-left"></i>
            Back
        </a>
    </div>
    <div class="form-card">

        <form method="POST" action="{{ route('admin.packages.store') }}">
            @csrf

            <div class="form-section">

                <h3>Basic Information</h3>

                <div class="form-group">
                    <label>Package Name *</label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Description *</label>

                    <textarea
                        name="description"
                        rows="5"
                        required
                    >{{ old('description') }}</textarea>
                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label>Trek Type *</label>

                        <select name="trek_type" required>
                            <option value="">Select Trek Type</option>

                            <option value="nature">Nature</option>
                            <option value="historical">Historical</option>
                            <option value="cultural">Cultural</option>
                            <option value="adventure">Adventure</option>
                            <option value="spiritual">Spiritual</option>
                            <option value="scenic">Scenic</option>
                            <option value="wildlife">Wildlife</option>
                            <option value="village">Village</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Region *</label>

                        <input
                            type="text"
                            name="region"
                            value="{{ old('region') }}"
                            placeholder="Pokhara, Mustang, Langtang..."
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Difficulty *</label>

                        <select name="difficulty_level" required>
                            <option value="easy">Easy</option>
                            <option value="moderate">Moderate</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label>Price (Rs.) *</label>

                        <input
                            type="number"
                            name="price"
                            min="0"
                            step="0.01"
                            value="{{ old('price') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Duration (Days) *</label>

                        <input
                            type="number"
                            name="duration_days"
                            min="1"
                            value="{{ old('duration_days', 1) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Max Participants *</label>

                        <input
                            type="number"
                            name="max_participants"
                            min="1"
                            value="{{ old('max_participants', 10) }}"
                            required
                        >
                    </div>

                </div>

            </div>

            <div class="form-section">

                <h3>Tags & Seasons</h3>

                <div class="form-group">
                    <label>Tags</label>

                    <input
                        type="text"
                        name="tags"
                        value="{{ old('tags') }}"
                        placeholder="forest, wildlife, sunrise, heritage"
                    >

                    <small>
                        Comma separated values
                    </small>
                </div>

                <div class="form-group">
                    <label>Available Seasons *</label>

                    <div class="checkbox-grid">

                        @php
                            $seasons = ['spring', 'summer', 'autumn', 'winter'];
                        @endphp

                        @foreach($seasons as $season)

                            <label class="checkbox-item">

                                <input
                                    type="checkbox"
                                    name="season[]"
                                    value="{{ $season }}"
                                >

                                {{ ucfirst($season) }}

                            </label>

                        @endforeach

                    </div>
                </div>

            </div>

            <div class="form-section">

                <h3>Package Image</h3>

                <div class="form-group">

                    <label>Image URL *</label>

                    <input
                        type="text"
                        name="image"
                        value="{{ old('image') }}"
                        placeholder="https://example.com/image.jpg"
                        required
                    >
                </div>

            </div>

            <div class="form-section">

                <h3>Start Location</h3>

                <div class="form-group">
                    <label>Location Name *</label>

                    <input
                        type="text"
                        name="start_location_name"
                        value="{{ old('start_location_name') }}"
                        required
                    >
                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label>Latitude *</label>

                        <input
                            type="number"
                            name="start_lat"
                            step="0.000001"
                            value="{{ old('start_lat') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Longitude *</label>

                        <input
                            type="number"
                            name="start_lng"
                            step="0.000001"
                            value="{{ old('start_lng') }}"
                            required
                        >
                    </div>

                </div>

            </div>

            <div class="form-section">

                <h3>End Location</h3>

                <div class="form-group">
                    <label>Location Name *</label>

                    <input
                        type="text"
                        name="end_location_name"
                        value="{{ old('end_location_name') }}"
                        required
                    >
                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label>Latitude *</label>

                        <input
                            type="number"
                            name="end_lat"
                            step="0.000001"
                            value="{{ old('end_lat') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Longitude *</label>

                        <input
                            type="number"
                            name="end_lng"
                            step="0.000001"
                            value="{{ old('end_lng') }}"
                            required
                        >
                    </div>

                </div>

            </div>

            <div class="form-section">

                <div class="checkpoint-header">

                    <h3>Checkpoints</h3>

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="addCheckpoint()"
                    >
                        <i class="fas fa-plus"></i>
                        Add Checkpoint
                    </button>

                </div>

                <div id="checkpoints-container"></div>

            </div>

            {{-- ACTIVE --}}
            <div class="form-section">

                <label class="checkbox-item">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        checked
                    >

                    Activate package immediately

                </label>

            </div>

            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Create Package
                </button>

                <a href="{{ route('admin.packages') }}" class="btn btn-secondary">
                    Cancel
                </a>

            </div>

        </form>

    </div>
</div>

@push('styles')
<style>

.container {
    max-width: 1200px;
    margin: auto;
}

.form-card {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h3 {
    color: var(--color-primary-dark);
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: .5rem;
    font-weight: 600;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: .85rem;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 1rem;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--color-primary-dark);
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
}

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: .75rem;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: .5rem;
}

.checkpoint-card {
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    background: #fafafa;
}

.checkpoint-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.fact-card {
    background: white;
    border: 1px dashed #ccc;
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1rem;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-primary {
    background: var(--color-primary-dark);
    color: white;
}

.btn {
    border: none;
    padding: .75rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
}

</style>
@endpush

@push('scripts')
<script>

let checkpointIndex = 0;

function addCheckpoint() {

    const html = `
        <div class="checkpoint-card">

            <h4>Checkpoint</h4>

            <div class="form-row">

                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="checkpoints[\${checkpointIndex}][name]" required>
                </div>

                <div class="form-group">
                    <label>Order *</label>
                    <input type="number" name="checkpoints[\${checkpointIndex}][order]" required>
                </div>

            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="checkpoints[\${checkpointIndex}][description]" rows="3"></textarea>
            </div>

            <div class="form-row">

                <div class="form-group">
                    <label>Latitude *</label>
                    <input type="number" step="0.000001" name="checkpoints[\${checkpointIndex}][latitude]" required>
                </div>

                <div class="form-group">
                    <label>Longitude *</label>
                    <input type="number" step="0.000001" name="checkpoints[\${checkpointIndex}][longitude]" required>
                </div>

                <div class="form-group">
                    <label>Radius *</label>
                    <input type="number" name="checkpoints[\${checkpointIndex}][radius]" value="100">
                </div>

                <div class="form-group">
                    <label>Estimated Time *</label>
                    <input type="number" name="checkpoints[\${checkpointIndex}][estimated_time_from_previous]" value="0">
                </div>

            </div>

            <div class="form-group">
                <label>Image URL</label>
                <input type="text" name="checkpoints[\${checkpointIndex}][image]">
            </div>

        </div>
    `;

    document
        .getElementById('checkpoints-container')
        .insertAdjacentHTML('beforeend', html);

    checkpointIndex++;
}

</script>
@endpush

@endsection