@extends('layouts.app')

@section('page-title', 'Generate Talking Head')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('talking-head.index') }}">Talking Heads</a></li>
    <li class="breadcrumb-item active">Generate</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
          <h5 class="mb-0"><i class="ti ti-video me-2"></i>Generate Talking Head Video</h5>
        </div>
        <div class="card-body">

          {{-- Step indicator --}}
          <div class="d-flex gap-3 mb-4">
            <span class="badge bg-primary rounded-pill px-3 py-2 step-badge active" id="stepBadge1">1. Choose Avatar</span>
            <span class="badge bg-light text-dark rounded-pill px-3 py-2 step-badge" id="stepBadge2">2. Write Script</span>
            <span class="badge bg-light text-dark rounded-pill px-3 py-2 step-badge" id="stepBadge3">3. Generate</span>
          </div>

          {{-- STEP 1: Choose Avatar --}}
          <div id="step1">
            <h6 class="fw-semibold mb-3">Select an Avatar</h6>
            @if($avatars->isEmpty())
              <div class="alert alert-warning">
                You don't have any avatars yet.
                <a href="{{ route('avatar.create') }}" class="alert-link">Create one first</a>.
              </div>
            @else
              <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3 mb-4">
                @foreach($avatars as $av)
                <div class="col">
                  <div class="card avatar-pick-card h-100 {{ $selectedAvatar == $av->id ? 'selected' : '' }}"
                       data-id="{{ $av->id }}"
                       data-name="{{ $av->name }}"
                       data-image="{{ $av->image_url }}"
                       style="cursor:pointer;">
                    <div class="ratio ratio-1x1 bg-light">
                      <img src="{{ $av->thumbnail_url }}" class="object-fit-cover rounded-top" alt="{{ $av->name }}">
                    </div>
                    <div class="p-2 text-center">
                      <span class="small fw-semibold text-truncate d-block">{{ $av->name }}</span>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              <div class="text-end">
                <button class="btn btn-primary" id="btnToStep2" disabled>
                  Next: Write Script <i class="ti ti-arrow-right ms-1"></i>
                </button>
              </div>
            @endif
          </div>

          {{-- STEP 2: Script + Voice --}}
          <div id="step2" class="d-none">
            <div class="row">
              <div class="col-md-4 mb-3">
                <div class="card bg-light border-0">
                  <div class="card-body text-center">
                    <img id="selectedAvatarImg" src="" class="rounded-3 mb-2" style="max-height:200px; max-width:100%; object-fit:contain;">
                    <div class="fw-semibold" id="selectedAvatarName"></div>
                    <button class="btn btn-sm btn-outline-secondary mt-2" id="btnBackToStep1">
                      <i class="ti ti-arrow-left"></i> Change
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                {{-- Title --}}
                <div class="mb-3">
                  <label class="form-label fw-semibold">Video Title</label>
                  <input type="text" id="videoTitle" class="form-control" placeholder="e.g. Product Introduction">
                </div>

                {{-- Script --}}
                <div class="mb-3">
                  <label class="form-label fw-semibold">Script <span class="text-danger">*</span></label>
                  <textarea id="scriptText" class="form-control" rows="5"
                            placeholder="Type what the avatar should say..."
                            maxlength="2000" required></textarea>
                  <div class="form-text"><span id="charCount">0</span>/2000 characters</div>
                </div>

                {{-- Language --}}
                <div class="mb-3">
                  <label class="form-label fw-semibold">Language</label>
                  <select id="languageSelect" class="form-select">
                    @foreach($languages as $lang)
                    <option value="{{ $lang->language_code }}">{{ $lang->language_full }} ({{ $lang->language_code }})</option>
                    @endforeach
                  </select>
                </div>

                {{-- Voice --}}
                <div class="mb-3">
                  <label class="form-label fw-semibold">Voice <span class="text-danger">*</span></label>
                  <select id="voiceSelect" class="form-select">
                    <option value="">Loading voices...</option>
                  </select>
                </div>

                <div class="text-end">
                  <button class="btn btn-primary" id="btnGenerate">
                    <i class="ti ti-sparkles me-1"></i> Generate Video
                  </button>
                </div>
              </div>
            </div>
          </div>

          {{-- STEP 3: Processing / Result --}}
          <div id="step3" class="d-none">
            <div class="text-center py-5">
              {{-- Processing state --}}
              <div id="processingState">
                <div class="spinner-border text-primary mb-3" style="width:3rem; height:3rem;"></div>
                <h5 id="genStatusText">Generating TTS audio...</h5>
                <p class="text-muted">This may take 1–3 minutes. Please keep this page open.</p>
                <div class="progress mx-auto" style="max-width:400px; height:6px;">
                  <div id="genProgress" class="progress-bar progress-bar-striped progress-bar-animated" style="width:10%"></div>
                </div>
              </div>

              {{-- Success state --}}
              <div id="successState" class="d-none">
                <div class="mb-3">
                  <i class="ti ti-circle-check text-success" style="font-size:64px;"></i>
                </div>
                <h5 class="mb-3">Your talking head video is ready!</h5>
                <video id="resultVideo" controls class="rounded-3 shadow mb-3" style="max-width:100%; max-height:500px;"></video>
                <div class="d-flex justify-content-center gap-2 mt-3">
                  <a href="{{ route('talking-head.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-list"></i> All Videos
                  </a>
                  <a href="{{ route('talking-head.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Create Another
                  </a>
                </div>
              </div>

              {{-- Error state --}}
              <div id="errorState" class="d-none">
                <div class="mb-3">
                  <i class="ti ti-alert-circle text-danger" style="font-size:64px;"></i>
                </div>
                <h5 class="mb-2">Generation failed</h5>
                <p class="text-danger" id="errorMsg"></p>
                <button class="btn btn-outline-primary" id="btnRetry">
                  <i class="ti ti-refresh"></i> Try Again
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.avatar-pick-card {
  border: 2px solid transparent;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.2s ease;
}
.avatar-pick-card:hover {
  border-color: #a5b4fc;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.avatar-pick-card.selected {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
}
.step-badge {
  font-size: 13px;
  transition: all 0.2s;
}
.step-badge.active {
  background-color: #6366f1 !important;
  color: #fff !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const csrfToken = '{{ csrf_token() }}';
  let selectedAvatarId = {{ $selectedAvatar ?: 'null' }};
  let talkingHeadId = null;

  // Elements
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  const step3 = document.getElementById('step3');
  const badges = [
    document.getElementById('stepBadge1'),
    document.getElementById('stepBadge2'),
    document.getElementById('stepBadge3')
  ];

  function showStep(n) {
    [step1, step2, step3].forEach((s, i) => {
      s.classList.toggle('d-none', i !== n - 1);
      badges[i].classList.toggle('active', i <= n - 1);
      if (i <= n - 1) {
        badges[i].classList.remove('bg-light', 'text-dark');
        badges[i].classList.add('bg-primary');
      }
    });
  }

  // ── Step 1: Avatar selection ──
  document.querySelectorAll('.avatar-pick-card').forEach(card => {
    card.addEventListener('click', function () {
      document.querySelectorAll('.avatar-pick-card').forEach(c => c.classList.remove('selected'));
      this.classList.add('selected');
      selectedAvatarId = this.dataset.id;
      document.getElementById('selectedAvatarImg').src = this.dataset.image;
      document.getElementById('selectedAvatarName').textContent = this.dataset.name;
      document.getElementById('btnToStep2').disabled = false;
    });

    // Pre-select
    if (card.dataset.id == selectedAvatarId) card.click();
  });

  document.getElementById('btnToStep2')?.addEventListener('click', () => {
    showStep(2);
    loadVoices();
  });

  document.getElementById('btnBackToStep1')?.addEventListener('click', () => showStep(1));

  // ── Step 2: Voice loading ──
  const langSelect  = document.getElementById('languageSelect');
  const voiceSelect = document.getElementById('voiceSelect');
  const scriptText  = document.getElementById('scriptText');
  const charCount   = document.getElementById('charCount');

  scriptText.addEventListener('input', () => {
    charCount.textContent = scriptText.value.length;
  });

  function loadVoices() {
    const code = langSelect.value;
    voiceSelect.innerHTML = '<option value="">Loading...</option>';

    fetch(`{{ route('text_to_speech.voices') }}?code=${encodeURIComponent(code)}`)
      .then(r => r.json())
      .then(voices => {
        voiceSelect.innerHTML = '';
        if (voices.length === 0) {
          voiceSelect.innerHTML = '<option value="">No voices available</option>';
          return;
        }
        voices.forEach(v => {
          const opt = document.createElement('option');
          opt.value = v.voice_name;
          opt.textContent = `${v.voice_text || v.voice_name} (${v.gender || 'Unknown'})`;
          voiceSelect.appendChild(opt);
        });
      })
      .catch(() => {
        voiceSelect.innerHTML = '<option value="">Error loading voices</option>';
      });
  }

  langSelect.addEventListener('change', loadVoices);

  // ── Step 3: Generate ──
  document.getElementById('btnGenerate')?.addEventListener('click', startGeneration);
  document.getElementById('btnRetry')?.addEventListener('click', () => showStep(2));

  async function startGeneration() {
    const script = scriptText.value.trim();
    const voice  = voiceSelect.value;
    const lang   = langSelect.value;
    const title  = document.getElementById('videoTitle').value;

    if (!script) { alert('Please write a script.'); return; }
    if (!voice)  { alert('Please select a voice.'); return; }

    showStep(3);
    updateProgress('Generating TTS audio...', 10);

    try {
      // 1. Create the talking head (generates TTS)
      const storeRes = await fetch('{{ route("talking-head.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
          avatar_id: selectedAvatarId,
          script: script,
          voice: voice,
          language: lang,
          title: title,
        })
      });
      const storeData = await storeRes.json();

      if (!storeRes.ok || storeData.status !== 'ok') {
        throw new Error(storeData.error || storeData.message || 'Failed to create talking head.');
      }

      talkingHeadId = storeData.id;
      updateProgress('Audio ready. Starting video generation...', 30);

      // 2. Kick off Replicate generation
      const genRes = await fetch(`/talking-head/${talkingHeadId}/generate`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      });
      const genData = await genRes.json();

      if (genData.status === 'completed') {
        showSuccess(genData.video_url);
        return;
      }

      if (genData.status === 'error') {
        throw new Error(genData.error || 'Generation failed.');
      }

      // 3. Poll for completion
      updateProgress('Video is being generated by AI...', 50);
      pollStatus();

    } catch (err) {
      showError(err.message);
    }
  }

  function pollStatus() {
    if (!talkingHeadId) return;

    const interval = setInterval(async () => {
      try {
        const res = await fetch(`/talking-head/${talkingHeadId}/status`, {
          headers: { 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (data.status === 'completed') {
          clearInterval(interval);
          showSuccess(data.video_url);
        } else if (data.status === 'failed') {
          clearInterval(interval);
          showError(data.error || 'Video generation failed.');
        } else {
          // Increment progress bar slowly
          const bar = document.getElementById('genProgress');
          const w = parseInt(bar.style.width) || 50;
          if (w < 90) bar.style.width = (w + 2) + '%';
        }
      } catch (e) {
        // Keep polling on network errors
      }
    }, 5000); // poll every 5 seconds
  }

  function updateProgress(text, pct) {
    document.getElementById('genStatusText').textContent = text;
    document.getElementById('genProgress').style.width = pct + '%';
  }

  function showSuccess(videoUrl) {
    document.getElementById('processingState').classList.add('d-none');
    document.getElementById('errorState').classList.add('d-none');
    document.getElementById('successState').classList.remove('d-none');
    const video = document.getElementById('resultVideo');
    video.src = videoUrl;
    video.load();
  }

  function showError(msg) {
    document.getElementById('processingState').classList.add('d-none');
    document.getElementById('successState').classList.add('d-none');
    document.getElementById('errorState').classList.remove('d-none');
    document.getElementById('errorMsg').textContent = msg;
  }
});
</script>
@endpush
