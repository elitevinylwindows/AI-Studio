@extends('layouts.app')

@section('page-title', 'Voices')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.0.7/datatables.min.css">
@endpush
<div class="mb-4"> </div>
@section('content')
<div class="card shadow-sm border-0">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Google TTS Voices</h5>
<form method="POST" action="{{ route('voices.sync') }}">
    @csrf
    <button class="btn btn-primary">
      <i class="ti ti-refresh"></i> Sync Voices
    </button>
  </form>
        {{-- <a href="{{ route('voices.sync') }}" class="btn btn-primary">Sync Voices</a> --}}
    </div>

    {{-- Filters Row --}}
    <div class="row g-2 mb-3">
      <div class="col-12 col-md-2">
        <label class="form-label">Vendor</label>
        <select id="filterVendor" class="form-select">
          <option value="">All</option>
          @foreach($vendors as $v)
          <option value="{{ $v }}">{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Language</label>
        <select id="filterLanguage" class="form-select">
          <option value="">All</option>
          @foreach($languages as $l)
          <option value="{{ $l }}">{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-2">
        <label class="form-label">Code</label>
        <select id="filterCode" class="form-select" disabled>
          <option value="">All</option>
        </select>
      </div>
      <div class="col-12 col-md-2">
        <label class="form-label">Gender</label>
        <select id="filterGender" class="form-select">
          <option value="">All</option>
          @foreach($genders as $g)
          @if($g)
          <option value="{{ $g }}">{{ ucfirst(strtolower($g)) }}</option>
          @endif
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Format</label>
        <select id="filterFormat" class="form-select">
          <option value="">All</option>
          @foreach($formats as $f)
          <option value="{{ $f }}">{{ strtoupper($f) }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="table-responsive">
      <table id="voicesTable" class="table table-hover align-middle w-100">
        <thead>
          <tr>
            <th>Vendor</th>
            <th>Language</th>
            <th>Code</th>
            <th>Gender</th>
            <th>Voice</th>
            <th>Preview</th>
            <th>Format</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($voices as $v)
          <tr data-id="{{ $v->id }}"
              data-text="{{ $v->voice_text ?? '' }}"
              data-format="{{ $v->audio_format ?? 'mp3' }}">
            <td class="text-center align-middle">
  <img src="{{ asset('icons/google.png') }}" alt="Google" style="width:20px;height:20px;vertical-align:middle;">
</td> <!--{{ $v->vendor }}-->
            <td>{{ $v->language_full }}</td>
            <td>{{ $v->language_code }}</td>
            <td>{{ ucfirst(strtolower($v->gender ?? '')) }}</td>
            <td>{{ $v->voice_text }}</td>
            <td>
              <button type="button"
  class="btn btn-sm rounded-circle bg-white border text-primary d-inline-flex align-items-center justify-content-center shadow-sm btnPreview"
  style="width:36px;height:36px" title="Preview" aria-label="Preview">
  <i class="ti ti-player-play"></i>
</button>

              @if($v->sample_url)
                <a href="{{ $v->sample_url }}" target="_blank" class="ms-2 small">Last sample</a>
              @endif
            </td>
            <td><span class="badge bg-secondary">{{ strtoupper($v->audio_format ?? 'mp3') }}</span></td>
            <td>
              <button class="btn btn-sm btn-primary btnEdit">
                <i class="ti ti-edit"></i> Edit
              </button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <audio id="audioPlayer" class="mt-3" controls style="display:none;width:100%"></audio>
  </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editVoiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="editVoiceForm">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Voice</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="voiceId">
          <div class="mb-3">
            <label class="form-label">Voice Name</label>
            <textarea class="form-control" id="voiceText" rows="3" placeholder="Type sample text..."></textarea>
            <div class="form-text">Only this text and format are saved on update.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Format</label>
            <select id="audioFormat" class="form-select">
              <option value="mp3">MP3</option>
              <option value="ogg">OGG</option>
              <option value="wav">WAV</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary" id="btnSaveEdit" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/v/bs5/dt-2.0.7/datatables.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const table = new DataTable('#voicesTable', {
    order: [[1, 'asc'], [2, 'asc']],
    pageLength: 25,
    stateSave: true,
  });

  // Filters
  const vendorEl = document.getElementById('filterVendor');
  const langEl   = document.getElementById('filterLanguage');
  const codeEl   = document.getElementById('filterCode');
  const genderEl = document.getElementById('filterGender');
  const formatEl = document.getElementById('filterFormat');

  function applyFilters() {
    table.column(0).search(vendorEl.value || '', true, false);
    table.column(1).search(langEl.value || '', true, false);
    table.column(2).search(codeEl.value || '', true, false);
    table.column(3).search(genderEl.value || '', true, false);
    table.column(6).search((formatEl.value || '').toUpperCase(), true, false);
    table.draw();
  }

  [vendorEl, langEl, genderEl, formatEl].forEach(el => el.addEventListener('change', applyFilters));

  // Dependent "Code" dropdown
  langEl.addEventListener('change', async function () {
    const lang = this.value;
    codeEl.innerHTML = '<option value="">All</option>';
    codeEl.disabled = true;

    if (!lang) {
      applyFilters();
      return;
    }

    try {
      const res = await fetch(`{{ route('voices.codes') }}?language_full=${encodeURIComponent(lang)}`);
      const codes = await res.json();
      codes.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c;
        opt.textContent = c;
        codeEl.appendChild(opt);
      });
      codeEl.disabled = false;
    } catch (e) {
      console.error(e);
    } finally {
      applyFilters();
    }
  });

  codeEl.addEventListener('change', applyFilters);

  // Edit modal
  const modalEl = document.getElementById('editVoiceModal');
  const bsModal = new bootstrap.Modal(modalEl);
  const voiceIdEl = document.getElementById('voiceId');
  const voiceTextEl = document.getElementById('voiceText');
  const audioFormatEl = document.getElementById('audioFormat');

  document.querySelectorAll('#voicesTable .btnEdit').forEach(btn => {
    btn.addEventListener('click', function () {
      const tr = this.closest('tr');
      voiceIdEl.value = tr.dataset.id;
      voiceTextEl.value = tr.dataset.text || '';
      audioFormatEl.value = tr.dataset.format || 'mp3';
      bsModal.show();
    });
  });

  // Save
  document.getElementById('editVoiceForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const id = voiceIdEl.value;

    const res = await fetch(`{{ url('tts/voices') }}/${id}`, {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json'},
      body: JSON.stringify({
        _method: 'PUT',
        voice_text: voiceTextEl.value,
        audio_format: audioFormatEl.value
      })
    });

    if (res.ok) {
      // Update row dataset + badge
      const tr = document.querySelector(`#voicesTable tr[data-id="${id}"]`);
      tr.dataset.text = voiceTextEl.value;
      tr.dataset.format = audioFormatEl.value;
      tr.querySelector('td:nth-child(7) .badge').textContent = audioFormatEl.value.toUpperCase();
      bsModal.hide();
    } else {
      const data = await res.json().catch(()=>({}));
      alert('Save failed: ' + (data.message || res.statusText));
    }
  });

  // Preview
  const audio = document.getElementById('audioPlayer');

  document.querySelectorAll('#voicesTable .btnPreview').forEach(btn => {
    btn.addEventListener('click', async function () {
      const tr = this.closest('tr');
      const id = tr.dataset.id;

      try {
        const res = await fetch(`{{ url('tts/voices') }}/${id}/preview`, {
          method: 'POST',
          headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
        });
        const data = await res.json();
        if (data.url) {
          audio.src = data.url;
          audio.style.display = 'block';
          audio.play().catch(()=>{});
        } else {
          alert('No preview URL returned.');
        }
      } catch (e) {
        console.error(e);
        alert('Preview failed.');
      }
    });
  });

});
</script>
@endpush
