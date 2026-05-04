@extends('layouts.app')

@section('title', 'Edit Package - Admin')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-edit"></i> Edit Package</h1>
            <p>{{ $package->name }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('tours.show', $package) }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-eye"></i> Preview
            </a>
            <a href="{{ route('admin.packages') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="edit-layout">
        <!-- Package Details -->
        <div class="edit-main">
            <div class="form-card">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Package Details</h3>
                </div>

                <form method="POST" action="{{ route('admin.packages.update', $package) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Package Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $package->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea name="description" id="description" rows="4" required>{{ old('description', $package->description) }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Price (Rs.) *</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $package->price) }}" min="0" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="duration_days">Duration (Days) *</label>
                            <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', $package->duration_days) }}" min="1" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="difficulty_level">Difficulty *</label>
                            <select name="difficulty_level" id="difficulty_level" required>
                                <option value="easy" {{ $package->difficulty_level == 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="moderate" {{ $package->difficulty_level == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                <option value="hard" {{ $package->difficulty_level == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="max_participants">Max Participants *</label>
                            <input type="number" name="max_participants" id="max_participants" value="{{ old('max_participants', $package->max_participants) }}" min="1" required>
                        </div>
                    </div>

                    <div class="form-section-title">Start Location</div>

                    <div class="form-group">
                        <label for="start_location_name">Location Name *</label>
                        <input type="text" name="start_location_name" id="start_location_name" value="{{ old('start_location_name', $package->start_location_name) }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_lat">Latitude *</label>
                            <input type="number" name="start_lat" id="start_lat" value="{{ old('start_lat', $package->start_lat) }}" step="0.000001" required>
                        </div>

                        <div class="form-group">
                            <label for="start_lng">Longitude *</label>
                            <input type="number" name="start_lng" id="start_lng" value="{{ old('start_lng', $package->start_lng) }}" step="0.000001" required>
                        </div>
                    </div>

                    <div class="form-section-title">End Location</div>

                    <div class="form-group">
                        <label for="end_location_name">Location Name *</label>
                        <input type="text" name="end_location_name" id="end_location_name" value="{{ old('end_location_name', $package->end_location_name) }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="end_lat">Latitude *</label>
                            <input type="number" name="end_lat" id="end_lat" value="{{ old('end_lat', $package->end_lat) }}" step="0.000001" required>
                        </div>

                        <div class="form-group">
                            <label for="end_lng">Longitude *</label>
                            <input type="number" name="end_lng" id="end_lng" value="{{ old('end_lng', $package->end_lng) }}" step="0.000001" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" value="1" {{ $package->is_active ? 'checked' : '' }}>
                            Package is active
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Package
                        </button>
                    </div>
                </form>
            </div>

            <!-- Checkpoints -->
            <div class="form-card">
                <div class="card-header">
                    <h3><i class="fas fa-map-marker-alt"></i> Checkpoints</h3>
                    <button type="button" class="btn btn-primary btn-sm" onclick="toggleAddCheckpoint()">
                        <i class="fas fa-plus"></i> Add Checkpoint
                    </button>
                </div>

                <!-- Add Checkpoint Form -->
                <div id="addCheckpointForm" style="display: none;">
                    <form method="POST" action="{{ route('admin.checkpoints.add', $package) }}" class="checkpoint-form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label>Checkpoint Name *</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Latitude *</label>
                                <input type="number" name="latitude" step="0.000001" required>
                            </div>
                            <div class="form-group">
                                <label>Longitude *</label>
                                <input type="number" name="longitude" step="0.000001" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Detection Radius (meters)</label>
                                <input type="number" name="detection_radius" value="100" min="10">
                            </div>
                            <div class="form-group">
                                <label>Time from Previous (mins)</label>
                                <input type="number" name="estimated_time_from_previous" min="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="short_description" rows="2"></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-sm">Add Checkpoint</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAddCheckpoint()">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Checkpoints List -->
                @if($package->checkpoints->count() > 0)
                    <div class="checkpoints-list">
                        @foreach($package->checkpoints->sortBy('order') as $checkpoint)
                            <div class="checkpoint-item">
                                <div class="checkpoint-header">
                                    <div class="checkpoint-number">{{ $checkpoint->order }}</div>
                                    <div class="checkpoint-info">
                                        <h4>{{ $checkpoint->name }}</h4>
                                        <p>{{ $checkpoint->short_description }}</p>
                                        <small>Lat: {{ $checkpoint->latitude }}, Lng: {{ $checkpoint->longitude }} | Radius: {{ $checkpoint->detection_radius }}m</small>
                                    </div>
                                    <div class="checkpoint-actions">
                                        <button type="button" class="btn-icon" onclick="toggleFacts({{ $checkpoint->id }})" title="Facts">
                                            <i class="fas fa-lightbulb"></i> {{ $checkpoint->facts->count() }}
                                        </button>
                                        <form method="POST" action="{{ route('admin.checkpoints.delete', $checkpoint) }}" style="display: inline;" onsubmit="return confirm('Delete this checkpoint?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Facts for this checkpoint -->
                                <div id="facts-{{ $checkpoint->id }}" class="facts-section" style="display: none;">
                                    <div class="facts-header">
                                        <h5><i class="fas fa-lightbulb"></i> Educational Facts</h5>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="toggleAddFact({{ $checkpoint->id }})">
                                            <i class="fas fa-plus"></i> Add Fact
                                        </button>
                                    </div>

                                    <!-- Add Fact Form -->
                                    <div id="addFactForm-{{ $checkpoint->id }}" style="display: none;">
                                        <form method="POST" action="{{ route('admin.facts.add', $checkpoint) }}" class="fact-form">
                                            @csrf
                                            <div class="form-group">
                                                <label>Title *</label>
                                                <input type="text" name="title" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Content *</label>
                                                <textarea name="content" rows="3" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Type *</label>
                                                <select name="type" required>
                                                    <option value="history">History</option>
                                                    <option value="culture">Culture</option>
                                                    <option value="safety">Safety</option>
                                                    <option value="info">Information</option>
                                                </select>
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary btn-sm">Add Fact</button>
                                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAddFact({{ $checkpoint->id }})">Cancel</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Facts List -->
                                    @if($checkpoint->facts->count() > 0)
                                        <div class="facts-list">
                                            @foreach($checkpoint->facts->sortBy('order') as $fact)
                                                <div class="fact-item">
                                                    <div class="fact-type-badge type-{{ $fact->type }}">
                                                        <i class="{{ $fact->icon_class }}"></i>
                                                        {{ ucfirst($fact->type) }}
                                                    </div>
                                                    <div class="fact-content">
                                                        <h6>{{ $fact->title }}</h6>
                                                        <p>{{ $fact->content }}</p>
                                                    </div>
                                                    <form method="POST" action="{{ route('admin.facts.delete', $fact) }}" style="display: inline;" onsubmit="return confirm('Delete this fact?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-icon btn-danger btn-sm" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">No facts added yet</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No checkpoints added yet. Add checkpoints to create the tour route.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="edit-sidebar">
            <!-- Danger Zone -->
            <div class="danger-zone">
                <h4><i class="fas fa-exclamation-triangle"></i> Danger Zone</h4>
                <p>Once you delete a package, there is no going back.</p>
                <form method="POST" action="{{ route('admin.packages.delete', $package) }}" onsubmit="return confirm('Are you sure? This will delete the package and all its checkpoints!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-trash"></i> Delete Package
                    </button>
                </form>
            </div>

            <!-- Quick Info -->
            <div class="info-box">
                <h4>Package Info</h4>
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <span>Created {{ $package->created_at->diffForHumans() }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-edit"></i>
                    <span>Updated {{ $package->updated_at->diffForHumans() }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $package->checkpoints->count() }} Checkpoints</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-ticket-alt"></i>
                    <span>{{ $package->bookings->count() }} Bookings</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .edit-layout {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 2rem;
    }

    .form-card {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
    }

    .card-header h3 {
        margin: 0;
        color: #333;
    }

    .form-section-title {
        font-weight: 600;
        color: #667eea;
        margin: 2rem 0 1rem 0;
        padding-top: 1.5rem;
        border-top: 1px solid #e0e0e0;
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
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .checkpoint-form,
    .fact-form {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .checkpoints-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .checkpoint-item {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .checkpoint-item:hover {
        border-color: #667eea;
    }

    .checkpoint-header {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .checkpoint-number {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .checkpoint-info {
        flex: 1;
    }

    .checkpoint-info h4 {
        margin: 0 0 0.5rem 0;
        color: #333;
    }

    .checkpoint-info p {
        margin: 0 0 0.5rem 0;
        color: #666;
    }

    .checkpoint-info small {
        color: #999;
        font-size: 0.85rem;
    }

    .checkpoint-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: none;
        background: #667eea;
        color: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-icon:hover {
        background: #764ba2;
        transform: scale(1.1);
    }

    .btn-icon.btn-danger {
        background: #dc3545;
    }

    .btn-icon.btn-danger:hover {
        background: #c82333;
    }

    .facts-section {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e0e0e0;
    }

    .facts-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .facts-header h5 {
        margin: 0;
        color: #667eea;
    }

    .facts-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .fact-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .fact-type-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .type-history {
        background: #d1ecf1;
        color: #0c5460;
    }

    .type-culture {
        background: #f8d7da;
        color: #721c24;
    }

    .type-safety {
        background: #fff3cd;
        color: #856404;
    }

    .type-info {
        background: #d4edda;
        color: #155724;
    }

    .fact-content {
        flex: 1;
    }

    .fact-content h6 {
        margin: 0 0 0.5rem 0;
        color: #333;
    }

    .fact-content p {
        margin: 0;
        color: #666;
        font-size: 0.95rem;
    }

    .edit-sidebar {
        position: sticky;
        top: 2rem;
        height: fit-content;
    }

    .danger-zone {
        background: #fff5f5;
        border: 2px solid #feb2b2;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .danger-zone h4 {
        color: #c53030;
        margin: 0 0 0.5rem 0;
    }

    .danger-zone p {
        color: #742a2a;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .info-box {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .info-box h4 {
        margin: 0 0 1rem 0;
        color: #333;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e0e0e0;
        color: #666;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-item i {
        color: #667eea;
        width: 20px;
    }

    .text-muted {
        color: #999;
        font-style: italic;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .btn-block {
        width: 100%;
    }

    input[type="checkbox"] {
        width: auto;
        margin-right: 0.5rem;
    }

    @media (max-width: 1200px) {
        .edit-layout {
            grid-template-columns: 1fr;
        }

        .edit-sidebar {
            position: static;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleAddCheckpoint() {
        const form = document.getElementById('addCheckpointForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    function toggleFacts(checkpointId) {
        const facts = document.getElementById('facts-' + checkpointId);
        facts.style.display = facts.style.display === 'none' ? 'block' : 'none';
    }

    function toggleAddFact(checkpointId) {
        const form = document.getElementById('addFactForm-' + checkpointId);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
</script>
@endpush
@endsection