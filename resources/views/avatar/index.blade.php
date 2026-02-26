@extends('layouts.app')

@section('page-title', 'Avatar')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">Avatar</h1>
        <a href="#" class="btn btn-primary">
            Purchase More Avatars
        </a>
    </div>

    {{-- My Avatars Section --}}
    <div class="mb-4">
        <h2 class="h5 mb-3">My Avatars</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-3">
            {{-- Create New Card --}}
            <div class="col">
                <a href="#" class="card h-100 border-2 border-dashed text-decoration-none create-card">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div class="create-dot d-flex align-items-center justify-content-center mb-2">+</div>
                        <div class="text-muted">Create New Avatar</div>
                    </div>
                </a>
            </div>

            @foreach($myAvatars as $av)
            <div class="col">
                <div class="card h-100 shadow-sm avatar-card">
                    <div class="ratio ratio-1x1 bg-light position-relative">
                        <img src="{{ $av['image'] ?? 'https://placehold.co/600x600?text=Avatar' }}" 
                             class="object-fit-cover" 
                             alt="{{ $av['name'] }}">
                        <div class="position-absolute top-0 end-0 p-2">
                            <button class="btn btn-sm btn-light rounded-circle">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-2">
                        <div class="fw-semibold small text-truncate">{{ $av['name'] }}</div>
                        <div class="text-muted small">{{ $av['looks'] }} look{{ $av['looks'] === 1 ? '' : 's' }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Public Avatars Section --}}
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 mb-0">Public Avatars</h2>
            <div class="d-flex align-items-center gap-2">
                <div class="btn-group" role="group">
                    <button class="btn btn-outline-secondary btn-sm active" title="Grid">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" title="List">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Quick Filters --}}
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="#" class="badge text-bg-primary rounded-pill px-3 py-2">All</a>
            <a href="#" class="badge text-bg-light rounded-pill px-3 py-2 border">Professional</a>
            <a href="#" class="badge text-bg-light rounded-pill px-3 py-2 border">Lifestyle</a>
            <a href="#" class="badge text-bg-light rounded-pill px-3 py-2 border">UGC</a>
            <a href="#" class="badge text-bg-light rounded-pill px-3 py-2 border">AI-generated</a>
            <a href="#" class="badge text-bg-light rounded-pill px-3 py-2 border">Community</a>
            <a href="#" class="badge text-bg-light rounded-pill px-3 py-2 border">Favorites</a>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('avatar.index') }}" class="mb-4">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search avatars..." value="{{ $q }}">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        {{-- Public Avatars Grid --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            @forelse($publicAvatars as $av)
            <div class="col">
                <div class="card h-100 shadow-sm avatar-card">
                    <div class="ratio ratio-16x9 bg-light position-relative">
                        <img src="{{ $av['image'] }}" class="object-fit-cover" alt="{{ $av['name'] }}">
                        <div class="position-absolute top-0 end-0 p-2">
                            <button class="btn btn-sm btn-light rounded-circle">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-3">
                        <div class="fw-semibold mb-1 text-truncate">{{ $av['name'] }}</div>
                        @if(!empty($av['tags']))
                            <div class="small text-muted">{{ implode(' • ', $av['tags']) }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-light border text-center py-4">
                    <i class="fas fa-image fa-2x mb-3 text-muted"></i>
                    <h5 class="mb-1">No avatars found</h5>
                    <p class="text-muted mb-0">Try adjusting your search or filters</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.create-card {
    border: 2px dashed #d0d5dd !important;
    background: #fafafa;
    transition: all 0.2s ease;
    border-radius: 12px;
}
.create-card:hover {
    background: #f5f7fa;
    border-color: #b9c0cf !important;
    transform: translateY(-2px);
}
.create-dot {
    width: 40px; 
    height: 40px; 
    border-radius: 50%;
    border: 1px solid #d0d5dd;
    font-size: 22px; 
    line-height: 1;
    transition: all 0.2s ease;
}
.create-card:hover .create-dot {
    transform: scale(1.1);
}

.avatar-card {
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
}
.avatar-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08) !important;
}

.badge.rounded-pill {
    font-weight: 500;
    transition: all 0.2s ease;
}
.badge.rounded-pill:hover {
    transform: translateY(-1px);
}

.object-fit-cover { 
    object-fit: cover; 
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle between grid and list view
    const viewButtons = document.querySelectorAll('.btn-group button');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            // Add logic here to switch views
        });
    });

    // Bookmark functionality
    document.querySelectorAll('.fa-bookmark').forEach(icon => {
        icon.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('far');
            this.classList.toggle('fas');
            this.classList.toggle('text-warning');
            // Add AJAX call to save favorite status
        });
    });

    // Filter chips
    document.querySelectorAll('.badge.rounded-pill').forEach(chip => {
        chip.addEventListener('click', function(e) {
            if (!this.classList.contains('text-bg-primary')) {
                e.preventDefault();
                document.querySelectorAll('.badge.rounded-pill').forEach(c => {
                    c.classList.remove('text-bg-primary');
                    c.classList.add('text-bg-light', 'border');
                });
                this.classList.add('text-bg-primary');
                this.classList.remove('text-bg-light', 'border');
                // Add logic to filter avatars
            }
        });
    });
});
</script>
@endpush