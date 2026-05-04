@extends('layouts.app')

@section('title', 'Manage Trek Packages')

@section('content')
<section style="background: var(--color-bg); min-height: calc(100vh - 70px);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <!-- Header -->
        <div class="flex-between" style="margin-bottom: var(--space-xl); flex-wrap: wrap; gap: var(--space-md);">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; margin-bottom: var(--space-sm);">Trek Packages</h1>
                <p style="color: var(--color-text-light); margin: 0;">Manage your trek offerings</p>
            </div>
            <a href="{{ route('admin.packages.create') }}" class="btn btn-cta btn-lg">
                <i class="fas fa-plus-circle"></i>
                Create New Trek
            </a>
        </div>

        <!-- Filter & Search -->
        <div class="card" style="margin-bottom: var(--space-xl);">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.packages') }}" class="flex gap-md" style="flex-wrap: wrap; align-items: end;">
                    <div class="form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
                        <label class="form-label">Search</label>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-input" 
                            placeholder="Search by name or location..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    
                    <div class="form-group" style="min-width: 150px; margin-bottom: 0;">
                        <label class="form-label">Difficulty</label>
                        <select name="difficulty" class="form-select">
                            <option value="">All Levels</option>
                            <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="moderate" {{ request('difficulty') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>

                    <div class="form-group" style="min-width: 150px; margin-bottom: 0;">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filter
                    </button>

                    @if(request()->hasAny(['search', 'difficulty', 'status']))
                        <a href="{{ route('admin.packages') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-4" style="margin-bottom: var(--space-xl);">
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 32px; font-weight: 700; color: var(--color-primary); margin-bottom: var(--space-xs);">
                        {{ $packages->total() }}
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-light);">Total Packages</div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 32px; font-weight: 700; color: var(--color-success); margin-bottom: var(--space-xs);">
                        {{ $packages->where('is_active', true)->count() }}
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-light);">Active</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 32px; font-weight: 700; color: var(--color-warning); margin-bottom: var(--space-xs);">
                        {{ $packages->sum(fn($p) => $p->bookings->count()) }}
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-light);">Total Bookings</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: var(--color-success); margin-bottom: var(--space-xs);">
                        Rs. {{ number_format($packages->sum(fn($p) => $p->bookings->where('status', '!=', 'cancelled')->sum('total_amount')), 0) }}
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-light);">Revenue</div>
                </div>
            </div>
        </div>

        @if($packages->count() > 0)
            <!-- Packages List -->
            <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
                @foreach($packages as $package)
                    <div class="card">
                        <div class="card-body">
                            <div class="flex-between" style="gap: var(--space-lg); flex-wrap: wrap;">
                                <!-- Package Info -->
                                <div style="flex: 1; min-width: 300px;">
                                    <div class="flex-between" style="margin-bottom: var(--space-md);">
                                        <h3 style="font-size: 18px; font-weight: 700; margin: 0;">
                                            {{ $package->name }}
                                        </h3>
                                        
                                        <!-- Active Toggle -->
                                        <form method="POST" action="{{ route('admin.packages.toggle-status', $package) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                class="badge {{ $package->is_active ? 'badge-success' : '' }}" 
                                                style="border: none; cursor: pointer; padding: 6px 12px; {{ !$package->is_active ? 'background: #E0E0E0; color: #666;' : '' }}"
                                            >
                                                <i class="fas fa-{{ $package->is_active ? 'check-circle' : 'times-circle' }}"></i>
                                                {{ $package->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </div>

                                    <p style="color: var(--color-text-light); margin-bottom: var(--space-md); font-size: 14px;">
                                        {{ Str::limit($package->description, 100) }}
                                    </p>

                                    <!-- Package Stats -->
                                    <div class="flex gap-lg" style="flex-wrap: wrap; font-size: 13px; color: var(--color-text-light);">
                                        <div class="flex" style="gap: var(--space-xs); align-items: center;">
                                            @if($package->difficulty_level === 'easy')
                                                <span class="badge badge-success">Easy</span>
                                            @elseif($package->difficulty_level === 'moderate')
                                                <span class="badge badge-warning">Moderate</span>
                                            @else
                                                <span class="badge badge-error">Hard</span>
                                            @endif
                                        </div>
                                        <div class="flex" style="gap: var(--space-xs); align-items: center;">
                                            <i class="fas fa-calendar" style="color: var(--color-primary);"></i>
                                            <span>{{ $package->duration_days }} days</span>
                                        </div>
                                        <div class="flex" style="gap: var(--space-xs); align-items: center;">
                                            <i class="fas fa-map-marker-alt" style="color: var(--color-primary);"></i>
                                            <span>{{ $package->checkpoints->count() }} checkpoints</span>
                                        </div>
                                        <div class="flex" style="gap: var(--space-xs); align-items: center;">
                                            <i class="fas fa-users" style="color: var(--color-primary);"></i>
                                            <span>Max {{ $package->max_participants }}</span>
                                        </div>
                                    </div>

                                    <!-- Analytics -->
                                    <div style="margin-top: var(--space-md); padding-top: var(--space-md); border-top: 1px solid #E0E0E0;">
                                        <div class="flex gap-lg" style="font-size: 13px;">
                                            <div>
                                                <span style="color: var(--color-text-light);">Bookings:</span>
                                                <strong style="color: var(--color-primary); margin-left: 4px;">{{ $package->bookings->count() }}</strong>
                                            </div>
                                            <div>
                                                <span style="color: var(--color-text-light);">Revenue:</span>
                                                <strong style="color: var(--color-success); margin-left: 4px;">
                                                    Rs. {{ number_format($package->bookings->where('status', '!=', 'cancelled')->sum('total_amount'), 0) }}
                                                </strong>
                                            </div>
                                            <div>
                                                <span style="color: var(--color-text-light);">Avg Rating:</span>
                                                <strong style="color: var(--color-warning); margin-left: 4px;">
                                                    <i class="fas fa-star"></i> N/A
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price & Actions -->
                                <div style="text-align: right; display: flex; flex-direction: column; justify-content: space-between; min-width: 200px;">
                                    <div style="margin-bottom: var(--space-lg);">
                                        <div style="font-size: 12px; color: var(--color-text-light); margin-bottom: 4px;">Price</div>
                                        <div style="font-size: 28px; font-weight: 700; color: var(--color-primary);">
                                            Rs. {{ number_format($package->price, 0) }}
                                        </div>
                                    </div>

                                    <div style="display: flex; flex-direction: column; gap: var(--space-sm);">
                                        <a href="{{ route('tours.show', $package) }}" class="btn btn-secondary btn-sm" target="_blank">
                                            <i class="fas fa-eye"></i>
                                            View Live
                                        </a>
                                        <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </a>
                                        <button 
                                            onclick="duplicatePackage({{ $package->id }})" 
                                            class="btn btn-secondary btn-sm"
                                        >
                                            <i class="fas fa-copy"></i>
                                            Duplicate
                                        </button>
                                        <button 
                                            onclick="deletePackage({{ $package->id }})" 
                                            class="btn btn-sm" 
                                            style="background: #FFEBEE; color: var(--color-error);"
                                        >
                                            <i class="fas fa-trash"></i>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-top: var(--space-xl);">
                {{ $packages->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-2xl);">
                    <i class="fas fa-box-open" style="font-size: 64px; color: #E0E0E0; margin-bottom: var(--space-lg);"></i>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-sm);">No Trek Packages Found</h3>
                    <p style="color: var(--color-text-light); margin-bottom: var(--space-xl);">
                        Create your first trek package to get started
                    </p>
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-cta btn-lg">
                        <i class="fas fa-plus-circle"></i>
                        Create Trek Package
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center; padding: var(--space-lg);">
    <div style="background: white; border-radius: var(--radius-lg); max-width: 500px; width: 100%;">
        <div style="padding: var(--space-xl);">
            <div style="text-align: center; margin-bottom: var(--space-lg);">
                <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: var(--color-error); margin-bottom: var(--space-md);"></i>
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-sm);">Delete Trek Package?</h2>
                <p style="color: var(--color-text-light); margin: 0;">This action cannot be undone. All checkpoints and associated data will be deleted.</p>
            </div>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-md">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary btn-lg" style="flex: 1;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-lg" style="flex: 1; background: var(--color-error); color: white;">
                        Delete Package
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deletePackage(id) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/packages/${id}`;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    function duplicatePackage(id) {
        if (confirm('Create a copy of this trek package?')) {
            fetch(`/admin/packages/${id}/duplicate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = `/admin/packages/${data.package_id}/edit`;
                } else {
                    alert('Failed to duplicate package');
                }
            })
            .catch(() => alert('Error occurred'));
        }
    }
</script>
@endpush
@endsection