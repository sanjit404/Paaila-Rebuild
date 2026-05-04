@extends('layouts.app')

@section('title', 'Manage Posts')

@section('content')
<section style="background: var(--color-bg); min-height: calc(100vh - 70px);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <!-- Header -->
        <div class="flex-between" style="margin-bottom: var(--space-xl); flex-wrap: wrap; gap: var(--space-md);">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; margin-bottom: var(--space-sm);">Manage Posts</h1>
                <p style="color: var(--color-text-light); margin: 0;">Control feed content and news</p>
            </div>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-cta btn-lg">
                <i class="fas fa-plus-circle"></i>
                Create New Post
            </a>
        </div>

        <!-- Filters -->
        <div class="card" style="margin-bottom: var(--space-xl);">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.posts') }}" class="flex gap-md" style="flex-wrap: wrap; align-items: end;">
                    <div class="form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
                        <label class="form-label">Search</label>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-input" 
                            placeholder="Search by title..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    
                    <div class="form-group" style="min-width: 150px; margin-bottom: 0;">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="news" {{ request('type') == 'news' ? 'selected' : '' }}>News</option>
                            <option value="offer" {{ request('type') == 'offer' ? 'selected' : '' }}>Offer</option>
                            <option value="trek" {{ request('type') == 'trek' ? 'selected' : '' }}>Trek</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>

                    @if(request()->hasAny(['search', 'type']))
                        <a href="{{ route('admin.posts') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-4" style="margin-bottom: var(--space-xl);">
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: var(--color-text);">{{ $stats['total'] }}</div>
                    <div style="font-size: 12px; color: var(--color-text-light);">Total Posts</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: #1976D2;">{{ $stats['news'] }}</div>
                    <div style="font-size: 12px; color: var(--color-text-light);">News</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: var(--color-warning);">{{ $stats['offers'] }}</div>
                    <div style="font-size: 12px; color: var(--color-text-light);">Offers</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: var(--color-primary);">{{ $stats['treks'] }}</div>
                    <div style="font-size: 12px; color: var(--color-text-light);">Treks</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: var(--color-accent);">{{ $stats['highlighted'] }}</div>
                    <div style="font-size: 12px; color: var(--color-text-light);">Highlighted</div>
                </div>
            </div>
        </div>

        @if($posts->count() > 0)
            <!-- Posts Table -->
            <div class="card">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #E0E0E0; background: #F5F5F5;">
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600;">Image</th>
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600;">Title</th>
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600;">Type</th>
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600;">Engagement</th>
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600;">Status</th>
                                <th style="padding: var(--space-md); text-align: right; font-size: 13px; font-weight: 600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                                <tr style="border-bottom: 1px solid #E0E0E0;">
                                    <td style="padding: var(--space-md);">
                                        @if($post->image)
                                            <img src="{{ $post->image }}" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: var(--radius-md);">
                                        @else
                                            <div style="width: 60px; height: 60px; background: #E0E0E0; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image" style="color: #999;"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: var(--space-md);">
                                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">{{ Str::limit($post->title, 50) }}</div>
                                        <div style="font-size: 12px; color: var(--color-text-light);">{{ $post->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td style="padding: var(--space-md);">
                                        @if($post->type === 'news')
                                            <span class="badge" style="background: #E3F2FD; color: #1976D2;">News</span>
                                        @elseif($post->type === 'offer')
                                            <span class="badge badge-warning">Offer</span>
                                        @else
                                            <span class="badge badge-primary">Trek</span>
                                        @endif
                                    </td>
                                    <td style="padding: var(--space-md); font-size: 13px;">
                                        <div class="flex" style="gap: var(--space-md);">
                                            <span><i class="fas fa-heart" style="color: #E53935;"></i> {{ $post->likes_count }}</span>
                                            <span><i class="fas fa-star" style="color: #FFA000;"></i> {{ number_format($post->rating_avg, 1) }}</span>
                                        </div>
                                    </td>
                                    <td style="padding: var(--space-md);">
                                        @if($post->is_highlighted)
                                            <span class="badge" style="background: #FFF3E0; color: var(--color-warning);">
                                                <i class="fas fa-star"></i> Featured
                                            </span>
                                        @else
                                            <span style="font-size: 12px; color: var(--color-text-light);">Regular</span>
                                        @endif
                                    </td>
                                    <td style="padding: var(--space-md); text-align: right;">
                                        <div class="flex gap-xs" style="justify-content: flex-end;">
                                            <a href="{{ route('feed.show', $post) }}" class="btn btn-secondary btn-sm" title="View" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-primary btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deletePost({{ $post->id }})" class="btn btn-sm" style="background: #FFEBEE; color: var(--color-error);" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div style="margin-top: var(--space-xl);">
                {{ $posts->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-2xl);">
                    <i class="fas fa-newspaper" style="font-size: 64px; color: #E0E0E0; margin-bottom: var(--space-lg);"></i>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-sm);">No Posts Yet</h3>
                    <p style="color: var(--color-text-light); margin-bottom: var(--space-xl);">Create your first post to get started</p>
                    <a href="{{ route('admin.posts.create') }}" class="btn btn-cta btn-lg">
                        <i class="fas fa-plus-circle"></i>
                        Create First Post
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete Modal -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center; padding: var(--space-lg);">
    <div style="background: white; border-radius: var(--radius-lg); max-width: 500px; width: 100%;">
        <div style="padding: var(--space-xl);">
            <div style="text-align: center; margin-bottom: var(--space-lg);">
                <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: var(--color-error); margin-bottom: var(--space-md);"></i>
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-sm);">Delete Post?</h2>
                <p style="color: var(--color-text-light); margin: 0;">This action cannot be undone.</p>
            </div>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-md">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary btn-lg" style="flex: 1;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-lg" style="flex: 1; background: var(--color-error); color: white;">
                        Delete Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deletePost(id) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/posts/${id}`;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
</script>
@endpush
@endsection