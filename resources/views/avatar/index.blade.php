@extends('layouts.app')

@section('page-title', 'Avatar')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Avatars</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="container py-4">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">Avatars</h1>
        <a href="{{ route('talking-head.create') }}" class="btn btn-primary">
            <i class="ti ti-video me-1"></i> Generate Talking Head
        </a>
    </div>

    {{-- ─── My Avatars ─────────────────────────────────────────── --}}
    <div class="mb-5">
        <h2 class="h5 mb-3">My Avatars</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-3">
            {{-- Create New Card --}}
            <div class="col">
                <a href="{{ route('avatar.create') }}" class="card h-100 border-2 border-dashed text-decoration-none create-card">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height:200px;">
                        <div class="create-dot d-flex align-items-center justify-content-center mb-2">+</div>
                        <div class="text-muted">Create New Avatar</div>
                    </div>
                </a>
            </div>

            @foreach($myAvatars as $av)
            <div class="col">
                <div class="card h-100 shadow-sm avatar-card position-relative">
                    <div class="ratio ratio-1x1 bg-light">
                        <img src="{{ $av->thumbnail_url }}"
                             class="object-fit-cover" alt="{{ $av->name }}">
                    </div>

                    {{-- Hover overlay with actions --}}
                    <div class="avatar-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-end justify-content-center p-2">
                        <div class="d-flex gap-1">
                            <a href="{{ route('avatar.edit', $av) }}" class="btn btn-sm btn-light" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form action="{{ route('avatar.destroy', $av) }}" method="POST"
                                  onsubmit="return confirm('Delete this avatar?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light text-danger" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="p-2">
                        <div class="fw-semibold small text-truncate">{{ $av->name }}</div>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            @if($av->style && $av->style !== 'realistic')
                            <span class="badge bg-{{ $av->style === 'cartoon' ? 'info' : 'purple' }} rounded-pill" style="font-size:10px;">
                                {{ ucfirst($av->style) }}
                            </span>
                            @endif
                            @if($av->gender)
                            <span class="text-muted small">{{ ucfirst($av->gender) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @if($myAvatars->isEmpty())
            <div class="col">
                <div class="text-muted small mt-3">No avatars yet. Upload your first one!</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ─── Public Avatars ─────────────────────────────────────── --}}
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 mb-0">Public Avatars</h2>
        </div>

        {{-- Quick Filters --}}
        <div class="d-flex flex-wrap gap-2 mb-3">
            @foreach($filters as $f)
            <a href="{{ route('avatar.index', ['tag' => $f === 'All' ? '' : $f, 'q' => $q]) }}"
               class="badge rounded-pill px-3 py-2 text-decoration-none
                      {{ $activeTag === $f ? 'text-bg-primary' : 'text-bg-light border' }}">
                {{ $f }}
            </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('avatar.index') }}" class="mb-4">
            @if($activeTag && $activeTag !== 'All')
            <input type="hidden" name="tag" value="{{ $activeTag }}">
            @endif
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search avatars..." value="{{ $q }}">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        {{-- Grid --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            @forelse($publicAvatars as $av)
            <div class="col">
                <div class="card h-100 shadow-sm avatar-card">
                    <div class="ratio ratio-1x1 bg-light">
                        <img src="{{ $av->thumbnail_url }}" class="object-fit-cover" alt="{{ $av->name }}">
                    </div>
                    <div class="p-3">
                        <div class="fw-semibold mb-1 text-truncate">{{ $av->name }}</div>
                        @if(!empty($av->tags))
                            <div class="small text-muted">{{ implode(' · ', $av->tags) }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-light border text-center py-4">
                    <i class="ti ti-photo fa-2x mb-3 text-muted d-block" style="font-size:2rem;"></i>
                    <h5 class="mb-1">No public avatars found</h5>
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
.avatar-overlay {
    background: linear-gradient(transparent 50%, rgba(0,0,0,0.5));
    opacity: 0;
    transition: opacity 0.2s ease;
    border-radius: 12px;
}
.avatar-card:hover .avatar-overlay {
    opacity: 1;
}
.object-fit-cover {
    object-fit: cover;
}
</style>
@endpush
