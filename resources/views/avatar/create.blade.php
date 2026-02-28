@extends('layouts.app')

@section('page-title', 'Create Avatar')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('avatar.index') }}">Avatars</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
          <h5 class="mb-0">Create New Avatar</h5>
        </div>

        <div class="card-body">

          {{-- Warning flash (e.g. AI transform fallback) --}}
          @if(session('warning'))
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          @endif

          <form action="{{ route('avatar.store') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
            @csrf

            {{-- Image Upload with Preview --}}
            <div class="mb-4">
              <label class="form-label fw-semibold">Upload Photo <span class="text-danger">*</span></label>
              <div class="upload-zone text-center p-4 rounded-3 border-2 border-dashed position-relative"
                   id="uploadZone" style="cursor:pointer; border-color:#d0d5dd; background:#fafbfc;">
                <div id="previewContainer" class="d-none">
                  <img id="previewImage" src="" alt="Preview"
                       class="rounded-3 mb-3" style="max-height:300px; max-width:100%; object-fit:contain;">
                  <div>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="removeImage">
                      <i class="ti ti-trash"></i> Remove
                    </button>
                  </div>
                </div>
                <div id="uploadPlaceholder">
                  <div class="mb-2">
                    <i class="ti ti-cloud-upload" style="font-size:48px; color:#9ca3af;"></i>
                  </div>
                  <p class="mb-1 fw-semibold">Click or drag & drop your photo here</p>
                  <p class="text-muted small mb-0">JPG, PNG or WebP — max 10 MB</p>
                </div>
                <input type="file" name="image" id="imageInput"
                       accept="image/jpeg,image/png,image/webp"
                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                       style="cursor:pointer;" required>
              </div>
              @error('image')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Avatar Style Picker --}}
            <div class="mb-4">
              <label class="form-label fw-semibold">Avatar Style <span class="text-danger">*</span></label>
              <p class="text-muted small mb-3">Choose how your photo will be transformed</p>
              <div class="row g-3">
                {{-- Realistic --}}
                <div class="col-4">
                  <input type="radio" name="style" value="realistic" id="style_realistic"
                         class="btn-check" {{ old('style', 'realistic') === 'realistic' ? 'checked' : '' }}>
                  <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center p-3 style-card"
                         for="style_realistic">
                    <div class="style-icon mb-2">
                      <i class="ti ti-user" style="font-size:32px;"></i>
                    </div>
                    <span class="fw-semibold">Realistic</span>
                    <span class="text-muted small text-center mt-1">Keep your real photo as-is</span>
                  </label>
                </div>

                {{-- Cartoon --}}
                <div class="col-4">
                  <input type="radio" name="style" value="cartoon" id="style_cartoon"
                         class="btn-check" {{ old('style') === 'cartoon' ? 'checked' : '' }}>
                  <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center p-3 style-card"
                         for="style_cartoon">
                    <div class="style-icon mb-2">
                      <i class="ti ti-brush" style="font-size:32px;"></i>
                    </div>
                    <span class="fw-semibold">Cartoon</span>
                    <span class="text-muted small text-center mt-1">Illustrated avatar style</span>
                  </label>
                </div>

                {{-- 3D --}}
                <div class="col-4">
                  <input type="radio" name="style" value="3d" id="style_3d"
                         class="btn-check" {{ old('style') === '3d' ? 'checked' : '' }}>
                  <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center p-3 style-card"
                         for="style_3d">
                    <div class="style-icon mb-2">
                      <i class="ti ti-3d-cube-sphere" style="font-size:32px;"></i>
                    </div>
                    <span class="fw-semibold">3D Avatar</span>
                    <span class="text-muted small text-center mt-1">3D rendered character</span>
                  </label>
                </div>
              </div>
              @error('style')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Name --}}
            <div class="mb-3">
              <label for="avatarName" class="form-label fw-semibold">Avatar Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="avatarName" class="form-control"
                     placeholder="e.g. Professional Presenter" value="{{ old('name') }}" required maxlength="255">
              @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Gender --}}
            <div class="mb-3">
              <label for="gender" class="form-label fw-semibold">Gender</label>
              <select name="gender" id="gender" class="form-select">
                <option value="">— Select —</option>
                <option value="male"    {{ old('gender') === 'male'    ? 'selected' : '' }}>Male</option>
                <option value="female"  {{ old('gender') === 'female'  ? 'selected' : '' }}>Female</option>
                <option value="neutral" {{ old('gender') === 'neutral' ? 'selected' : '' }}>Neutral</option>
              </select>
            </div>

            {{-- Tags --}}
            <div class="mb-3">
              <label class="form-label fw-semibold">Tags</label>
              <div class="d-flex flex-wrap gap-2">
                @foreach(['Professional', 'Lifestyle', 'UGC', 'AI-generated', 'Community'] as $tag)
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="tags[]"
                         value="{{ $tag }}" id="tag_{{ $tag }}"
                         {{ in_array($tag, old('tags', [])) ? 'checked' : '' }}>
                  <label class="form-check-label" for="tag_{{ $tag }}">{{ $tag }}</label>
                </div>
                @endforeach
              </div>
            </div>

            {{-- Public toggle --}}
            <div class="mb-4">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_public" value="1"
                       id="isPublic" {{ old('is_public') ? 'checked' : '' }}>
                <label class="form-check-label" for="isPublic">Make this avatar publicly visible</label>
              </div>
            </div>

            {{-- AI processing notice --}}
            <div id="aiNotice" class="alert alert-info d-none mb-4">
              <i class="ti ti-sparkles me-1"></i>
              <strong>AI Transformation:</strong> Your photo will be sent to OpenAI for transformation.
              This may take 15–30 seconds. Please be patient after clicking Create.
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-between">
              <a href="{{ route('avatar.index') }}" class="btn btn-outline-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary" id="btnSubmit">
                <i class="ti ti-upload"></i> Create Avatar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.upload-zone {
  transition: all 0.2s ease;
}
.upload-zone:hover,
.upload-zone.drag-over {
  border-color: #6366f1 !important;
  background: #f0f0ff !important;
}
.border-dashed {
  border-style: dashed !important;
}
.style-card {
  border-radius: 12px !important;
  transition: all 0.2s ease;
}
.btn-check:checked + .style-card {
  background: #eef2ff;
  border-color: #6366f1 !important;
  box-shadow: 0 0 0 2px #6366f1;
}
.style-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.style-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-check:checked + .style-card .style-icon {
  background: #c7d2fe;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const input       = document.getElementById('imageInput');
  const zone        = document.getElementById('uploadZone');
  const placeholder = document.getElementById('uploadPlaceholder');
  const container   = document.getElementById('previewContainer');
  const preview     = document.getElementById('previewImage');
  const removeBtn   = document.getElementById('removeImage');
  const aiNotice    = document.getElementById('aiNotice');
  const btnSubmit   = document.getElementById('btnSubmit');
  const form        = document.getElementById('avatarForm');

  function showPreview(file) {
    if (!file || !file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      placeholder.classList.add('d-none');
      container.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  }

  input.addEventListener('change', () => {
    if (input.files.length) showPreview(input.files[0]);
  });

  removeBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    input.value = '';
    container.classList.add('d-none');
    placeholder.classList.remove('d-none');
  });

  // Drag-and-drop
  ['dragenter', 'dragover'].forEach(evt =>
    zone.addEventListener(evt, e => { e.preventDefault(); zone.classList.add('drag-over'); })
  );
  ['dragleave', 'drop'].forEach(evt =>
    zone.addEventListener(evt, e => { e.preventDefault(); zone.classList.remove('drag-over'); })
  );
  zone.addEventListener('drop', e => {
    const dt = e.dataTransfer;
    if (dt.files.length) {
      input.files = dt.files;
      showPreview(dt.files[0]);
    }
  });

  // Show/hide AI notice based on style selection
  document.querySelectorAll('input[name="style"]').forEach(radio => {
    radio.addEventListener('change', function () {
      if (this.value === 'cartoon' || this.value === '3d') {
        aiNotice.classList.remove('d-none');
      } else {
        aiNotice.classList.add('d-none');
      }
    });
  });

  // Prevent double-submit and show loading state
  form.addEventListener('submit', function () {
    const style = document.querySelector('input[name="style"]:checked')?.value;
    btnSubmit.disabled = true;

    if (style === 'cartoon' || style === '3d') {
      btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Transforming with AI...';
    } else {
      btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';
    }
  });
});
</script>
@endpush
