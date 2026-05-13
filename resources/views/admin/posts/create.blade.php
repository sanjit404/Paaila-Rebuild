@extends('layouts.app')

@section('title', 'Create Post')

@section('content')
<section style="background: var(--color-bg); min-height: calc(100vh - 70px);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="margin-bottom: var(--space-xl);">
                <a href="{{ route('admin.posts') }}" style="color: var(--color-text-light); text-decoration: none; font-size: 14px; margin-bottom: var(--space-md); display: inline-block;">
                    <i class="fas fa-arrow-left"></i> Back to Posts
                </a>
                <h1 style="font-size: 28px; font-weight: 700; margin-bottom: var(--space-sm);">Create New Post</h1>
                <p style="color: var(--color-text-light); margin: 0;">Add content to your feed</p>
            </div>

            <div class="card">
                <div class="card-body" style="padding: var(--space-xl);">
                    <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Title <span class="required">*</span></label>
                            <input type="text" name="title" class="form-input" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Content <span class="required">*</span></label>
                            <textarea name="content" class="form-textarea" rows="8" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Type <span class="required">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="news" {{ old('type') == 'news' ? 'selected' : '' }}>News</option>
                                <option value="offer" {{ old('type') == 'offer' ? 'selected' : '' }}>Offer</option>
                                <option value="trek" {{ old('type') == 'trek' ? 'selected' : '' }}>Trek</option>
                            </select>
                            @error('type')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Related Trek Package (Optional)</label>
                            <select name="trek_id" class="form-select">
                                <option value="">None</option>
                                @foreach($trekPackages as $package)
                                    <option value="{{ $package->id }}" {{ old('trek_id') == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('trek_id')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Featured Image</label>
                            <input type="text" name="image" class="form-input" >
                            @error('image')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: var(--space-sm); cursor: pointer;">
                                <input type="checkbox" name="is_highlighted" value="1" {{ old('is_highlighted') ? 'checked' : '' }}>
                                <span>Feature this post (highlighted)</span>
                            </label>
                            <div class="form-helper">Only one post can be highlighted at a time</div>
                        </div>

                        <div class="flex gap-md">
                            <a href="{{ route('admin.posts') }}" class="btn btn-secondary btn-lg" style="flex: 1;">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-cta btn-lg" style="flex: 2;">
                                <i class="fas fa-save"></i>
                                Create Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection