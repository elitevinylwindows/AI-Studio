@extends('layouts.app')

@section('page-title', $avatar->name)

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('avatar.index') }}">Avatars</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $avatar->name }}</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-body text-center">

          {{-- Avatar image --}}
          <div class="position-relative d-inline-block mb-4">
            <img id="avatarImage" src="{{ $avatar->image_url }}" alt="{{ $avatar->name }}"
                 class="rounded-3 shadow" style="max-width:100%; max-height:500px; object-fit:contain;">

            {{-- Processing overlay --}}
            @if($avatar->status === 'processing')
            <div id="processingOverlay"
                 class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center rounded-3"
                 style="background:rgba(0,0,0,0.65);">
              <div class="spinner-border text-light mb-3" style="width:3rem; height:3rem;" role="status">
                <span class="visually-hidden">Transforming...</span>
              </div>
              <p class="text-white fw-semibold mb-1" id="statusText">Transforming to {{ ucfirst($avatar->style) }}...</p>
              <p class="text-white-50 small mb-0">This may take 15–30 seconds</p>
            </div>
            @endif
          </div>

          <h4 class="mb-1">{{ $avatar->name }}</h4>

          <div class="d-flex justify-content-center gap-2 mb-3">
            <span class="badge bg-primary rounded-pill">{{ ucfirst($avatar->style) }}</span>
            @if($avatar->gender)
            <span class="badge bg-secondary rounded-pill">{{ ucfirst($avatar->gender) }}</span>
            @endif
            <span class="badge bg-{{ $avatar->status === 'active' ? 'success' : ($avatar->status === 'processing' ? 'warning' : 'danger') }} rounded-pill"
                  id="statusBadge">
              {{ ucfirst($avatar->status) }}
            </span>
          </div>

          @if(!empty($avatar->tags))
          <div class="mb-3">
            @foreach($avatar->tags as $tag)
            <span class="badge bg-light text-dark border me-1">{{ $tag }}</span>
            @endforeach
          </div>
          @endif

          {{-- Error message area --}}
          <div id="errorAlert" class="alert alert-danger d-none mb-3"></div>

          {{-- Retry button (shown on failure) --}}
          <button id="retryBtn" class="btn btn-outline-primary d-none mb-3">
            <i class="ti ti-refresh"></i> Retry Transformation
          </button>

          <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('avatar.index') }}" class="btn btn-outline-secondary">
              <i class="ti ti-arrow-left"></i> Back
            </a>
            <a href="{{ route('avatar.edit', $avatar) }}" class="btn btn-primary">
              <i class="ti ti-edit"></i> Edit
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const avatarId     = {{ $avatar->id }};
  const status       = '{{ $avatar->status }}';
  const transformUrl = '{{ route("avatar.transform", $avatar) }}';
  const csrfToken    = '{{ csrf_token() }}';
  const indexUrl     = '{{ route("avatar.index") }}';

  const overlay    = document.getElementById('processingOverlay');
  const avatarImg  = document.getElementById('avatarImage');
  const statusText = document.getElementById('statusText');
  const statusBadge = document.getElementById('statusBadge');
  const errorAlert = document.getElementById('errorAlert');
  const retryBtn   = document.getElementById('retryBtn');

  function startTransform() {
    // Show overlay if hidden
    if (overlay) {
      overlay.classList.remove('d-none');
      overlay.style.display = '';
    }
    errorAlert.classList.add('d-none');
    retryBtn.classList.add('d-none');

    fetch(transformUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
      if (ok && (data.status === 'ok' || data.status === 'already_done')) {
        // Success — update image and remove overlay
        if (data.image_url) {
          avatarImg.src = data.image_url;
        }
        if (overlay) overlay.remove();
        statusBadge.textContent = 'Active';
        statusBadge.className = 'badge bg-success rounded-pill';

        // Auto-redirect to index after a moment
        setTimeout(() => {
          window.location.href = indexUrl + '?success=Avatar+transformed+successfully';
        }, 1500);
      } else {
        // Error
        showError(data.error || 'Transformation failed. Please try again.');
      }
    })
    .catch(err => {
      showError('Network error: ' + err.message);
    });
  }

  function showError(msg) {
    if (overlay) overlay.style.display = 'none';
    errorAlert.textContent = msg;
    errorAlert.classList.remove('d-none');
    retryBtn.classList.remove('d-none');
    statusBadge.textContent = 'Failed';
    statusBadge.className = 'badge bg-danger rounded-pill';
  }

  // Retry button
  retryBtn.addEventListener('click', startTransform);

  // Auto-trigger if avatar is in processing state
  if (status === 'processing') {
    startTransform();
  }
});
</script>
@endpush
