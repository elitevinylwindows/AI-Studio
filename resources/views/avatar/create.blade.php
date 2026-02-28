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
          <form action="{{ route('avatar.store') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
            @csrf

            {{-- Image Upload with Preview --}}
            <div class="mb-4">
              <label class="form-label fw-semibold">Avatar Image <span class="text-danger">*</span></label>
              <div class="upload-zone text-center p-4 rounded-3 border-2 border-dashed position-relative"
                   id="uploadZone" style="cursor:pointer; border-color:#d0d5dd; background:#fafbfc;">
                {{-- Preview (hidden by default) --}}
                <div id="previewContainer" class="d-none">
                  <img id="previewImage" src="" alt="Preview"
                       class="rounded-3 mb-3" style="max-height:300px; max-width:100%; object-fit:contain;">
                  <div>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="removeImage">
                      <i class="ti ti-trash"></i> Remove
                    </button>
                  </div>
                </div>

                {{-- Placeholder --}}
                <div id="uploadPlaceholder">
                  <div class="mb-2">
                    <i class="ti ti-cloud-upload" style="font-size:48px; color:#9ca3af;"></i>
                  </div>
                  <p class="mb-1 fw-semibold">Click or drag & drop your image here</p>
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

  // Drag-and-drop highlight
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
});
</script>
@endpush
