@extends('layouts.app')

@section('page-title', 'AI Studio')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">AI Studio</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Video Preview --}}
<div class="card mb-4">
    <div class="card-header"><strong>🎬 Video Preview</strong></div>
    <div class="card-body text-center">
        <div style="max-width: 720px; margin: auto; position: relative; background: #000; border-radius: 8px; overflow: hidden;">
            @if($videoPath)
                <video id="bgVideo" width="100%" muted loop style="display:block;">
                    <source src="{{ $videoPath }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            @else
                <div id="noVideoMsg" style="padding: 80px 20px; color: #999;">
                    <i class="ti ti-movie" style="font-size: 2.5rem; display: block; margin-bottom: 8px;"></i>
                    No video selected or found.
                </div>
            @endif
            <video id="overlayVideo" muted loop playsinline
                style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:10; display:none; object-fit:contain;">
                <source src="{{ asset('storage/overlays/elite_talking.webm') }}" type="video/webm">
            </video>
        </div>

        {{-- Transport Controls --}}
        <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
            <span id="currentTime" class="text-muted small font-monospace" style="min-width:45px;">0:00</span>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="skipStartBtn" title="Start"><i class="ti ti-player-skip-back"></i></button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="skipBackBtn" title="-5s"><i class="ti ti-rewind"></i></button>
            <button type="button" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" id="playPauseBtn" style="width:42px;height:42px;" title="Play/Pause"><i class="ti ti-player-play" id="playIcon"></i></button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="skipFwdBtn" title="+5s"><i class="ti ti-fast-forward"></i></button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="skipEndBtn" title="End"><i class="ti ti-player-skip-forward"></i></button>
            <span id="totalTime" class="text-muted small font-monospace" style="min-width:45px;">0:00</span>
        </div>
    </div>
</div>

{{-- Timeline --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <strong>🕒 Timeline & Overlay Control</strong>
        <div class="d-flex align-items-center gap-2">
            <small class="text-muted">Overlay starts at:</small>
            <span class="badge bg-primary" id="overlayTimeLabel">0.0s</span>
        </div>
    </div>
    <div class="card-body p-0">
        {{-- Ruler --}}
        <div style="height:26px; background:#f8f9fc; border-bottom:1px solid #e9ecef; display:flex; align-items:flex-end; padding-left:130px; overflow:hidden;" id="ruler"></div>

        {{-- Tracks --}}
        @php
            $tracks = [
                ['label' => 'Background', 'color' => '#6c5ce7', 'icon' => 'ti-movie', 'clip_class' => 'video'],
                ['label' => 'Audio / TTS', 'color' => '#00cec9', 'icon' => 'ti-music', 'clip_class' => 'audio'],
                ['label' => 'Avatar', 'color' => '#e84393', 'icon' => 'ti-user', 'clip_class' => 'avatar'],
                ['label' => 'Overlay', 'color' => '#f39c12', 'icon' => 'ti-sparkles', 'clip_class' => 'overlay'],
            ];
        @endphp

        @foreach($tracks as $track)
        <div style="display:flex; height:42px; border-bottom:1px solid #f1f3f5;">
            <div style="width:130px; flex-shrink:0; display:flex; align-items:center; gap:8px; padding:0 12px; background:#fafbfc; border-right:1px solid #e9ecef;">
                <span style="width:8px; height:8px; border-radius:50%; background:{{ $track['color'] }};"></span>
                <span style="font-size:0.78rem; font-weight:500; color:#495057;">{{ $track['label'] }}</span>
            </div>
            <div style="flex:1; position:relative; overflow:hidden;" class="track-clips">
                @php
                    $clipStyles = [
                        'video' => 'left:0; width:70%; background:linear-gradient(90deg,#6c5ce7,#a29bfe); color:#fff;',
                        'audio' => 'left:0; width:55%; background:linear-gradient(90deg,#00cec9,#55efc4); color:#111;',
                        'avatar' => 'left:10%; width:45%; background:linear-gradient(90deg,#e84393,#fd79a8); color:#fff;',
                        'overlay' => 'left:15%; width:40%; background:linear-gradient(90deg,#f39c12,#fdcb6e); color:#111;',
                    ];
                @endphp
                <div id="clip_{{ $track['clip_class'] }}"
                     style="position:absolute; top:5px; height:32px; border-radius:5px; display:flex; align-items:center; padding:0 10px; font-size:0.7rem; font-weight:600; white-space:nowrap; {{ $clipStyles[$track['clip_class']] }}">
                    {{ $track['label'] }}
                </div>
            </div>
        </div>
        @endforeach

        {{-- Overlay start slider --}}
        <div class="p-3 border-top">
            <div class="row align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 small">Overlay Start Time:</label>
                </div>
                <div class="col">
                    <input type="range" class="form-range" id="overlayStartSlider" min="0" max="60" step="0.1" value="0">
                </div>
                <div class="col-auto">
                    <span class="badge bg-light text-dark border" id="overlayStartVal">0.0s</span>
                </div>
                <div class="col-auto">
                    <button class="btn btn-success btn-sm" id="previewSyncBtn"><i class="ti ti-player-play me-1"></i> Preview Sync</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Studio Controls Form --}}
<div class="card">
    <div class="card-header"><h5 class="mb-0">🎛️ Studio Controls</h5></div>
    <div class="card-body">
        <form action="{{ route('ai_studio.generate') }}" method="POST" enctype="multipart/form-data" id="studioForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Script (Optional if using MP3)</label>
                    <textarea name="tts_text" class="form-control" rows="4">{{ old('tts_text', session('ttsText')) }}</textarea>
                    @error('tts_text') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Select Voice</label>
                    <select name="voice" class="form-select">
                        <option value="">Default</option>
                        @foreach(['en-US-Wavenet-D','en-GB-Wavenet-A','en-AU-Wavenet-B','en-IN-Wavenet-C'] as $voice)
                            <option value="{{ $voice }}" {{ $selectedVoice == $voice ? 'selected' : '' }}>{{ $voice }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Use Existing Audio (MP3)</label>
                    <select name="mp3_audio" class="form-select">
                        <option value="">None</option>
                        @foreach($audios as $audio)
                            <option value="{{ $audio->id }}" {{ optional($selectedAudio)->id == $audio->id ? 'selected' : '' }}>
                                {{ $audio->original_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Select Blender File</label>
                    <select name="blender_id" class="form-select">
                        <option value="">None</option>
                        @foreach($blenders as $blend)
                            <option value="{{ $blend->id }}" {{ optional($selectedBlender)->id == $blend->id ? 'selected' : '' }}>
                                {{ $blend->original_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Upload New Blender File</label>
                    <input type="file" name="blender_file" class="form-control" accept=".glb,.fbx">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Select Background Video</label>
                    <select name="bg_video" class="form-select" id="bgVideoSelect">
                        <option value="">None</option>
                        @foreach($videos as $vid)
                            <option value="{{ $vid->id }}" data-filename="{{ $vid->filename }}" {{ optional($selectedVideo)->id == $vid->id ? 'selected' : '' }}>
                                {{ $vid->original_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Mouth Animation</label>
                    <select name="mouth_mode" class="form-select">
                        <option value="auto" {{ session('mouthMode') === 'auto' ? 'selected' : '' }}>Auto</option>
                        <option value="loop" {{ session('mouthMode') === 'loop' ? 'selected' : '' }}>Loop</option>
                        <option value="none" {{ session('mouthMode') === 'none' ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Overlay Style</label>
                    <select name="overlay_style" class="form-select">
                        <option value="webm" {{ session('overlayStyle') === 'webm' ? 'selected' : '' }}>WebM</option>
                        <option value="3d" {{ session('overlayStyle') === '3d' ? 'selected' : '' }}>3D Canvas</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <button class="btn btn-primary w-100" id="generateBtn"><i class="ti ti-movie me-1"></i> Generate</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bgVideo = document.getElementById('bgVideo');
    const overlayVideo = document.getElementById('overlayVideo');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const playIcon = document.getElementById('playIcon');
    const currentTimeEl = document.getElementById('currentTime');
    const totalTimeEl = document.getElementById('totalTime');
    const overlaySlider = document.getElementById('overlayStartSlider');
    const overlayValEl = document.getElementById('overlayStartVal');
    const overlayLabel = document.getElementById('overlayTimeLabel');

    let isPlaying = false;
    let overlayStartTime = 0;
    let overlayTriggered = false;

    function fmt(s) { if(!s||isNaN(s)) return '0:00'; return Math.floor(s/60)+':'+Math.floor(s%60).toString().padStart(2,'0'); }

    // ── Build ruler ticks ──
    function buildRuler(dur) {
        const el = document.getElementById('ruler');
        el.innerHTML = '';
        const step = 5, count = Math.ceil((dur||30)/step)+1;
        for (let i = 0; i < count; i++) {
            const t = i*step;
            const tick = document.createElement('div');
            tick.style.cssText = 'width:80px; flex-shrink:0; text-align:center; font-size:0.62rem; color:#adb5bd; font-family:monospace; border-left:1px solid #e9ecef; padding-bottom:4px;';
            tick.textContent = Math.floor(t/60)+':'+((t%60).toString().padStart(2,'0'));
            el.appendChild(tick);
        }
    }
    buildRuler(30);

    // ── Video events ──
    if (bgVideo) {
        bgVideo.addEventListener('loadedmetadata', function () {
            totalTimeEl.textContent = fmt(bgVideo.duration);
            overlaySlider.max = Math.floor(bgVideo.duration);
            buildRuler(bgVideo.duration);
        });

        bgVideo.addEventListener('timeupdate', function () {
            currentTimeEl.textContent = fmt(bgVideo.currentTime);
            // Trigger overlay at start time
            if (!overlayTriggered && bgVideo.currentTime >= overlayStartTime) {
                overlayVideo.style.display = 'block';
                overlayVideo.play().catch(function(){});
                overlayTriggered = true;
            }
        });
    }

    // ── Play / Pause ──
    playPauseBtn?.addEventListener('click', function () {
        if (!bgVideo) return;
        if (isPlaying) {
            bgVideo.pause();
            overlayVideo?.pause();
            playIcon.className = 'ti ti-player-play';
        } else {
            bgVideo.play().catch(function(){});
            playIcon.className = 'ti ti-player-pause';
            if (bgVideo.currentTime >= overlayStartTime) {
                overlayVideo.style.display = 'block';
                overlayVideo.play().catch(function(){});
                overlayTriggered = true;
            } else {
                overlayVideo.style.display = 'none';
                overlayTriggered = false;
            }
        }
        isPlaying = !isPlaying;
    });

    document.getElementById('skipStartBtn')?.addEventListener('click', function(){ if(bgVideo){bgVideo.currentTime=0; resetOverlay();} });
    document.getElementById('skipBackBtn')?.addEventListener('click', function(){ if(bgVideo) bgVideo.currentTime=Math.max(0,bgVideo.currentTime-5); });
    document.getElementById('skipFwdBtn')?.addEventListener('click', function(){ if(bgVideo) bgVideo.currentTime=Math.min(bgVideo.duration||0,bgVideo.currentTime+5); });
    document.getElementById('skipEndBtn')?.addEventListener('click', function(){ if(bgVideo) bgVideo.currentTime=bgVideo.duration; });

    function resetOverlay() {
        overlayVideo.style.display = 'none';
        overlayVideo.pause();
        overlayVideo.currentTime = 0;
        overlayTriggered = false;
    }

    // ── Overlay start slider ──
    overlaySlider?.addEventListener('input', function () {
        overlayStartTime = parseFloat(this.value);
        var label = overlayStartTime.toFixed(1) + 's';
        overlayValEl.textContent = label;
        overlayLabel.textContent = label;

        // Move the overlay clip visually
        var clip = document.getElementById('clip_overlay');
        if (clip && bgVideo && bgVideo.duration) {
            var pct = overlayStartTime / bgVideo.duration * 100;
            clip.style.left = pct + '%';
            clip.style.width = Math.max(10, (100 - pct) * 0.6) + '%';
        }
        resetOverlay();
    });

    // ── Preview Sync button ──
    document.getElementById('previewSyncBtn')?.addEventListener('click', function () {
        if (!bgVideo) return;
        bgVideo.currentTime = 0;
        resetOverlay();
        bgVideo.play().catch(function(){});
        playIcon.className = 'ti ti-player-pause';
        isPlaying = true;

        // Delay overlay
        setTimeout(function () {
            overlayVideo.style.display = 'block';
            overlayVideo.play().catch(function(){});
            overlayTriggered = true;
        }, overlayStartTime * 1000);
    });

    // ── Background video switcher (dropdown in form) ──
    document.getElementById('bgVideoSelect')?.addEventListener('change', function () {
        var selected = this.options[this.selectedIndex];
        var filename = selected.getAttribute('data-filename');
        if (filename && bgVideo) {
            var source = bgVideo.querySelector('source');
            if (source) {
                source.src = '/storage/' + filename;
                bgVideo.load();
            }
        }
    });

    // ── Generate form submit ──
    document.getElementById('studioForm')?.addEventListener('submit', function () {
        var btn = document.getElementById('generateBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating...';
    });
});
</script>
@endpush