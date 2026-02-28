@extends('layouts.app')

@section('page-title', 'Talking Head Videos')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Talking Heads</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="container py-4">

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="mb-0">Talking Head Videos</h1>
    <a href="{{ route('talking-head.create') }}" class="btn btn-primary">
      <i class="ti ti-plus me-1"></i> Generate New
    </a>
  </div>

  @if($videos->isEmpty())
  <div class="card shadow-sm border-0">
    <div class="card-body text-center py-5">
      <i class="ti ti-video-off d-block mb-3" style="font-size:48px; color:#9ca3af;"></i>
      <h5>No talking head videos yet</h5>
      <p class="text-muted mb-3">Create your first talking avatar video</p>
      <a href="{{ route('talking-head.create') }}" class="btn btn-primary">
        <i class="ti ti-sparkles me-1"></i> Generate Video
      </a>
    </div>
  </div>
  @else
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    @foreach($videos as $vid)
    <div class="col">
      <div class="card shadow-sm border-0 h-100">
        {{-- Video preview or avatar thumbnail --}}
        <div class="ratio ratio-16x9 bg-dark rounded-top position-relative">
          @if($vid->status === 'completed' && $vid->video_public_url)
            <video src="{{ $vid->video_public_url }}" class="rounded-top" style="object-fit:contain;" muted preload="metadata"></video>
            <div class="position-absolute top-50 start-50 translate-middle">
              <button class="btn btn-light rounded-circle shadow play-btn" data-video-id="{{ $vid->id }}">
                <i class="ti ti-player-play"></i>
              </button>
            </div>
          @elseif($vid->avatar)
            <img src="{{ $vid->avatar->thumbnail_url }}" class="rounded-top" style="object-fit:cover;">
            <div class="position-absolute top-50 start-50 translate-middle">
              @if($vid->status === 'processing')
              <div class="spinner-border text-light"></div>
              @else
              <span class="badge bg-danger px-3 py-2">Failed</span>
              @endif
            </div>
          @endif
        </div>

        <div class="card-body">
          <h6 class="fw-semibold mb-1 text-truncate">{{ $vid->title ?: 'Untitled' }}</h6>
          <p class="text-muted small mb-2 text-truncate">{{ Str::limit($vid->script, 80) }}</p>
          <div class="d-flex align-items-center justify-content-between">
            <span class="badge bg-{{ $vid->status === 'completed' ? 'success' : ($vid->status === 'processing' ? 'warning' : 'danger') }} rounded-pill">
              {{ ucfirst($vid->status) }}
            </span>
            <div class="d-flex gap-1">
              @if($vid->status === 'completed' && $vid->video_public_url)
              <a href="{{ $vid->video_public_url }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Download">
                <i class="ti ti-download"></i>
              </a>
              @endif
              <form action="{{ route('talking-head.destroy', $vid) }}" method="POST"
                    onsubmit="return confirm('Delete this video?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" title="Delete">
                  <i class="ti ti-trash"></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.play-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const card = this.closest('.ratio');
    const video = card.querySelector('video');
    if (video) {
      video.muted = false;
      video.controls = true;
      video.play();
      this.remove();
    }
  });
});
</script>
@endpush
