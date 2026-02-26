@extends('layouts.app')

@section('page-title', 'AI Studio')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">AI Studio</li>
@endsection

@push('styles')
<style>
    /* ── Studio Layout ── */
    .studio-layout {
        display: grid;
        grid-template-columns: 240px 1fr 280px;
        gap: 16px;
        min-height: 600px;
    }

    @media (max-width: 1200px) {
        .studio-layout { grid-template-columns: 1fr; }
        .studio-sidebar { display: none; }
        .studio-inspector { order: 3; }
    }

    /* ── Sidebar: Assets ── */
    .studio-sidebar .card { height: 100%; }

    .asset-section-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 16px 4px;
    }

    .asset-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 16px;
        cursor: pointer;
        transition: background 0.12s;
        border-left: 3px solid transparent;
    }

    .asset-item:hover { background: #f8f9fc; }

    .asset-item.active {
        background: #f3f0ff;
        border-left-color: #6c5ce7;
    }

    .asset-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #fff;
        flex-shrink: 0;
    }

    .asset-icon.video { background: linear-gradient(135deg, #6c5ce7, #a29bfe); }
    .asset-icon.audio { background: linear-gradient(135deg, #00cec9, #55efc4); }
    .asset-icon.model { background: linear-gradient(135deg, #f39c12, #fdcb6e); }
    .asset-icon.avatar { background: linear-gradient(135deg, #e84393, #fd79a8); }

    .asset-name { font-size: 0.82rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .asset-meta { font-size: 0.7rem; color: #adb5bd; }

    .asset-add-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 16px;
        margin: 4px 12px 12px;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        background: none;
        color: #6c757d;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.15s;
        width: calc(100% - 24px);
    }

    .asset-add-btn:hover { border-color: #6c5ce7; color: #6c5ce7; }

    /* ── Center: Preview + Timeline ── */
    .studio-center { display: flex; flex-direction: column; gap: 16px; }

    /* Preview card */
    .preview-frame {
        position: relative;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 16/9;
    }

    .preview-frame video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-frame .overlay-video {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: contain;
        pointer-events: none;
        z-index: 5;
    }

    .no-video-msg {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #adb5bd;
        background: #1a1a22;
    }

    .no-video-msg .icon { font-size: 2rem; }
    .no-video-msg .text { font-size: 0.88rem; }
    .no-video-msg .sub { font-size: 0.76rem; color: #6c757d; }

    /* Transport bar */
    .transport-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 8px 0;
    }

    .transport-time {
        font-family: monospace;
        font-size: 0.78rem;
        color: #6c757d;
        min-width: 50px;
        text-align: center;
    }

    .transport-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #495057;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.15s;
    }

    .transport-btn:hover { border-color: #6c5ce7; color: #6c5ce7; }

    .transport-btn.play-main {
        width: 42px;
        height: 42px;
        background: #6c5ce7;
        border-color: #6c5ce7;
        color: #fff;
        font-size: 16px;
        box-shadow: 0 2px 10px rgba(108,92,231,0.3);
    }

    .transport-btn.play-main:hover { background: #5a4bd1; transform: scale(1.05); }

    /* Timeline */
    .timeline-card .card-body { padding: 0; overflow: hidden; }

    .timeline-ruler {
        height: 26px;
        background: #f8f9fc;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: flex-end;
        padding-left: 140px;
        overflow: hidden;
    }

    .ruler-tick {
        width: 80px;
        flex-shrink: 0;
        text-align: center;
        font-size: 0.62rem;
        color: #adb5bd;
        font-family: monospace;
        border-left: 1px solid #e9ecef;
        padding-bottom: 4px;
    }

    .timeline-track {
        display: flex;
        height: 44px;
        border-bottom: 1px solid #f1f3f5;
    }

    .timeline-track:last-child { border-bottom: none; }

    .track-label {
        width: 140px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 12px;
        background: #fafbfc;
        border-right: 1px solid #e9ecef;
        font-size: 0.78rem;
        font-weight: 500;
        color: #495057;
    }

    .track-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .track-dot.video { background: #6c5ce7; }
    .track-dot.audio { background: #00cec9; }
    .track-dot.avatar { background: #e84393; }
    .track-dot.overlay { background: #f39c12; }

    .track-clips {
        flex: 1;
        position: relative;
        overflow: hidden;
    }

    .clip-block {
        position: absolute;
        top: 6px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        padding: 0 10px;
        font-size: 0.7rem;
        font-weight: 600;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
    }

    .clip-block.video { background: linear-gradient(90deg, #6c5ce7, #a29bfe); left: 0; width: 70%; }
    .clip-block.audio { background: linear-gradient(90deg, #00cec9, #55efc4); color: #0a0a0e; left: 0; width: 55%; }
    .clip-block.avatar { background: linear-gradient(90deg, #e84393, #fd79a8); left: 10%; width: 45%; }
    .clip-block.overlay { background: linear-gradient(90deg, #f39c12, #fdcb6e); color: #0a0a0e; left: 15%; width: 40%; }

    /* ── Inspector ── */
    .insp-section {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f3f5;
    }

    .insp-section:last-child { border-bottom: none; }

    .insp-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .insp-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .insp-dot.purple { background: #6c5ce7; }
    .insp-dot.teal { background: #00cec9; }
    .insp-dot.orange { background: #f39c12; }

    .insp-field { margin-bottom: 10px; }
    .insp-field-label { font-size: 0.78rem; color: #6c757d; font-weight: 500; margin-bottom: 4px; display: block; }

    .insp-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }

    .slider-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .slider-row label { font-size: 0.78rem; color: #6c757d; min-width: 80px; margin: 0; }

    .slider-row input[type="range"] { flex: 1; }

    .slider-val {
        font-family: monospace;
        font-size: 0.72rem;
        color: #6c5ce7;
        background: #f3f0ff;
        padding: 2px 8px;
        border-radius: 4px;
        min-width: 40px;
        text-align: center;
    }

    .status-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 8px;
        background: #f8f9fc;
        border: 1px solid #e9ecef;
        font-size: 0.78rem;
        color: #6c757d;
    }

    .status-dot { width: 7px; height: 7px; border-radius: 50%; background: #adb5bd; }
    .status-dot.ready { background: #00cec9; }
    .status-dot.processing { background: #feca57; animation: blink 1s infinite; }
    @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.3;} }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="studio-layout">

    {{-- ═══ LEFT SIDEBAR: Assets ═══ --}}
    <div class="studio-sidebar">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom"><h6 class="mb-0"><i class="ti ti-folder me-1"></i> Assets</h6></div>
            <div class="card-body p-0" style="overflow-y:auto; max-height:calc(100vh - 250px);">

                <div class="asset-section-label">Background Videos</div>
                @foreach($videos as $vid)
                <div class="asset-item {{ optional($selectedVideo)->id == $vid->id ? 'active' : '' }}"
                     data-type="video" data-id="{{ $vid->id }}" data-filename="{{ $vid->filename }}">
                    <div class="asset-icon video"><i class="ti ti-movie"></i></div>
                    <div><div class="asset-name">{{ $vid->original_name }}</div><div class="asset-meta">Video</div></div>
                </div>
                @endforeach

                <div class="asset-section-label">Audio Files</div>
                @foreach($audios as $audio)
                <div class="asset-item {{ optional($selectedAudio)->id == $audio->id ? 'active' : '' }}"
                     data-type="audio" data-id="{{ $audio->id }}">
                    <div class="asset-icon audio"><i class="ti ti-music"></i></div>
                    <div><div class="asset-name">{{ $audio->original_name }}</div><div class="asset-meta">Audio · MP3</div></div>
                </div>
                @endforeach

                <div class="asset-section-label">3D Assets</div>
                @foreach($blenders as $blend)
                <div class="asset-item {{ optional($selectedBlender)->id == $blend->id ? 'active' : '' }}"
                     data-type="blender" data-id="{{ $blend->id }}">
                    <div class="asset-icon model"><i class="ti ti-box"></i></div>
                    <div><div class="asset-name">{{ $blend->original_name }}</div><div class="asset-meta">3D Model</div></div>
                </div>
                @endforeach
                <button type="button" class="asset-add-btn" id="uploadBlenderBtn"><i class="ti ti-plus"></i> Upload 3D File</button>

                <div class="asset-section-label">Avatars</div>
                <button type="button" class="asset-add-btn" onclick="window.location='{{ route('avatar.index') }}'"><i class="ti ti-user"></i> Browse Avatars</button>
            </div>
        </div>
    </div>

    {{-- ═══ CENTER: Preview + Timeline ═══ --}}
    <div class="studio-center">
        {{-- Preview --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="preview-frame" id="previewFrame">
                    @if($videoPath)
                    <video id="bgVideo" muted loop>
                        <source src="{{ $videoPath }}" type="video/mp4">
                    </video>
                    @else
                    <div class="no-video-msg" id="noVideoMsg">
                        <div class="icon"><i class="ti ti-movie"></i></div>
                        <div class="text">No video selected</div>
                        <div class="sub">Choose a background video from the sidebar</div>
                    </div>
                    @endif
                    <video id="overlayVideo" class="overlay-video" muted loop playsinline style="display:none;">
                        <source src="{{ asset('storage/overlays/elite_talking.webm') }}" type="video/webm">
                    </video>
                </div>

                {{-- Transport --}}
                <div class="transport-bar">
                    <span class="transport-time" id="currentTime">0:00</span>
                    <button type="button" class="transport-btn" id="skipStartBtn"><i class="ti ti-player-skip-back"></i></button>
                    <button type="button" class="transport-btn" id="skipBackBtn"><i class="ti ti-rewind"></i></button>
                    <button type="button" class="transport-btn play-main" id="playPauseBtn"><i class="ti ti-player-play"></i></button>
                    <button type="button" class="transport-btn" id="skipFwdBtn"><i class="ti ti-fast-forward"></i></button>
                    <button type="button" class="transport-btn" id="skipEndBtn"><i class="ti ti-player-skip-forward"></i></button>
                    <span class="transport-time" id="totalTime">0:00</span>
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="card shadow-sm border-0 timeline-card">
            <div class="card-header bg-white border-bottom py-2 d-flex align-items-center justify-content-between">
                <h6 class="mb-0"><i class="ti ti-clock me-1"></i> Timeline</h6>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted">Zoom</small>
                    <input type="range" id="zoomSlider" min="0.5" max="3" step="0.1" value="1" style="width:80px;">
                </div>
            </div>
            <div class="card-body">
                <div class="timeline-ruler" id="ruler"></div>
                <div class="timeline-track">
                    <div class="track-label"><span class="track-dot video"></span> Background</div>
                    <div class="track-clips"><div class="clip-block video">Background Video</div></div>
                </div>
                <div class="timeline-track">
                    <div class="track-label"><span class="track-dot audio"></span> Audio / TTS</div>
                    <div class="track-clips"><div class="clip-block audio">Audio</div></div>
                </div>
                <div class="timeline-track">
                    <div class="track-label"><span class="track-dot avatar"></span> Avatar</div>
                    <div class="track-clips"><div class="clip-block avatar">Avatar</div></div>
                </div>
                <div class="timeline-track">
                    <div class="track-label"><span class="track-dot overlay"></span> Overlay</div>
                    <div class="track-clips"><div class="clip-block overlay" id="clipOverlay">WebM Overlay</div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ RIGHT: Inspector / Controls ═══ --}}
    <div class="studio-inspector">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom"><h6 class="mb-0"><i class="ti ti-settings me-1"></i> Controls</h6></div>
            <div class="card-body p-0" style="overflow-y:auto; max-height:calc(100vh - 250px);">
                <form id="studioForm" action="{{ route('ai_studio.generate') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Script --}}
                    <div class="insp-section">
                        <div class="insp-label"><span class="insp-dot teal"></span> Script</div>
                        <div class="insp-field">
                            <label class="insp-field-label">TTS Text</label>
                            <textarea name="tts_text" class="form-control form-control-sm" rows="3" placeholder="Script text (optional if using MP3)...">{{ old('tts_text', session('ttsText')) }}</textarea>
                        </div>
                        <div class="insp-field">
                            <label class="insp-field-label">Voice</label>
                            <select name="voice" class="form-select form-select-sm">
                                <option value="">Default</option>
                                @foreach(['en-US-Wavenet-D','en-GB-Wavenet-A','en-AU-Wavenet-B','en-IN-Wavenet-C'] as $voice)
                                <option value="{{ $voice }}" {{ $selectedVoice == $voice ? 'selected' : '' }}>{{ $voice }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="insp-field">
                            <label class="insp-field-label">Existing Audio</label>
                            <select name="mp3_audio" class="form-select form-select-sm">
                                <option value="">None (use TTS)</option>
                                @foreach($audios as $audio)
                                <option value="{{ $audio->id }}" {{ optional($selectedAudio)->id == $audio->id ? 'selected' : '' }}>{{ $audio->original_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Visual --}}
                    <div class="insp-section">
                        <div class="insp-label"><span class="insp-dot purple"></span> Visual</div>
                        <div class="insp-field">
                            <label class="insp-field-label">Background Video</label>
                            <select name="bg_video" class="form-select form-select-sm" id="inspBgVideo">
                                <option value="">None</option>
                                @foreach($videos as $vid)
                                <option value="{{ $vid->id }}" data-filename="{{ $vid->filename }}" {{ optional($selectedVideo)->id == $vid->id ? 'selected' : '' }}>{{ $vid->original_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="insp-field">
                            <label class="insp-field-label">3D Model</label>
                            <select name="blender_id" class="form-select form-select-sm">
                                <option value="">None</option>
                                @foreach($blenders as $blend)
                                <option value="{{ $blend->id }}" {{ optional($selectedBlender)->id == $blend->id ? 'selected' : '' }}>{{ $blend->original_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="insp-field">
                            <label class="insp-field-label">Upload 3D File</label>
                            <input type="file" name="blender_file" class="form-control form-control-sm" accept=".glb,.fbx">
                        </div>
                    </div>

                    {{-- Animation --}}
                    <div class="insp-section">
                        <div class="insp-label"><span class="insp-dot orange"></span> Animation</div>
                        <div class="insp-row">
                            <div class="insp-field">
                                <label class="insp-field-label">Mouth</label>
                                <select name="mouth_mode" class="form-select form-select-sm">
                                    <option value="auto" {{ session('mouthMode')==='auto'?'selected':'' }}>Auto</option>
                                    <option value="loop" {{ session('mouthMode')==='loop'?'selected':'' }}>Loop</option>
                                    <option value="none" {{ session('mouthMode')==='none'?'selected':'' }}>None</option>
                                </select>
                            </div>
                            <div class="insp-field">
                                <label class="insp-field-label">Overlay</label>
                                <select name="overlay_style" class="form-select form-select-sm">
                                    <option value="webm" {{ session('overlayStyle')==='webm'?'selected':'' }}>WebM</option>
                                    <option value="3d" {{ session('overlayStyle')==='3d'?'selected':'' }}>3D Canvas</option>
                                </select>
                            </div>
                        </div>
                        <div class="slider-row">
                            <label>Overlay Start</label>
                            <input type="range" id="overlayStart" name="overlay_start" min="0" max="60" step="0.1" value="0" class="form-range">
                            <span class="slider-val" id="overlayStartVal">0.0s</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="insp-section">
                        <div class="insp-label"><span class="insp-dot purple"></span> Actions</div>
                        <div class="status-bar mb-3" id="statusBar">
                            <span class="status-dot" id="statusDot"></span>
                            <span id="statusText">Ready</span>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="generateBtn"><i class="ti ti-bolt me-1"></i> Generate Video</button>
                            <button type="button" class="btn btn-outline-success" id="exportBtn"><i class="ti ti-download me-1"></i> Export Final</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bgVideo = document.getElementById('bgVideo');
    const overlayVideo = document.getElementById('overlayVideo');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const currentTimeEl = document.getElementById('currentTime');
    const totalTimeEl = document.getElementById('totalTime');
    const overlaySlider = document.getElementById('overlayStart');
    const overlayVal = document.getElementById('overlayStartVal');
    const inspBgVideo = document.getElementById('inspBgVideo');

    let isPlaying = false;
    let overlayStartTime = 0;
    let overlayTriggered = false;

    function fmt(s) { if (!s || isNaN(s)) return '0:00'; return Math.floor(s/60) + ':' + Math.floor(s%60).toString().padStart(2,'0'); }

    // Ruler
    function buildRuler(dur) {
        const el = document.getElementById('ruler');
        el.innerHTML = '';
        const step = 5, count = Math.ceil((dur||30)/step)+1;
        for (let i = 0; i < count; i++) {
            const t = i*step, m = Math.floor(t/60), s = t%60;
            const tick = document.createElement('div');
            tick.className = 'ruler-tick';
            tick.textContent = m+':'+s.toString().padStart(2,'0');
            el.appendChild(tick);
        }
    }
    buildRuler(30);

    if (bgVideo) {
        bgVideo.addEventListener('loadedmetadata', () => { totalTimeEl.textContent = fmt(bgVideo.duration); overlaySlider.max = Math.floor(bgVideo.duration); buildRuler(bgVideo.duration); });
        bgVideo.addEventListener('timeupdate', () => {
            currentTimeEl.textContent = fmt(bgVideo.currentTime);
            if (!overlayTriggered && bgVideo.currentTime >= overlayStartTime) {
                overlayVideo.style.display = 'block';
                overlayVideo.play().catch(()=>{});
                overlayTriggered = true;
            }
        });
    }

    playPauseBtn?.addEventListener('click', () => {
        if (!bgVideo) return;
        if (isPlaying) {
            bgVideo.pause(); overlayVideo?.pause();
            playPauseBtn.innerHTML = '<i class="ti ti-player-play"></i>';
        } else {
            bgVideo.play().catch(()=>{});
            playPauseBtn.innerHTML = '<i class="ti ti-player-pause"></i>';
            if (bgVideo.currentTime >= overlayStartTime) { overlayVideo.style.display='block'; overlayVideo.play().catch(()=>{}); overlayTriggered=true; }
            else { overlayVideo.style.display='none'; overlayTriggered=false; }
        }
        isPlaying = !isPlaying;
    });

    document.getElementById('skipStartBtn')?.addEventListener('click', () => { if(bgVideo){bgVideo.currentTime=0; resetOverlay();} });
    document.getElementById('skipBackBtn')?.addEventListener('click', () => { if(bgVideo) bgVideo.currentTime=Math.max(0,bgVideo.currentTime-5); });
    document.getElementById('skipFwdBtn')?.addEventListener('click', () => { if(bgVideo) bgVideo.currentTime=Math.min(bgVideo.duration,bgVideo.currentTime+5); });
    document.getElementById('skipEndBtn')?.addEventListener('click', () => { if(bgVideo) bgVideo.currentTime=bgVideo.duration; });

    function resetOverlay() { overlayVideo.style.display='none'; overlayVideo.pause(); overlayVideo.currentTime=0; overlayTriggered=false; }

    overlaySlider?.addEventListener('input', function () {
        overlayStartTime = parseFloat(this.value);
        overlayVal.textContent = overlayStartTime.toFixed(1)+'s';
        const clip = document.getElementById('clipOverlay');
        if (clip && bgVideo?.duration) { clip.style.left = (overlayStartTime/bgVideo.duration*100)+'%'; clip.style.width = Math.max(10,(100-parseFloat(clip.style.left))*0.6)+'%'; }
        resetOverlay();
    });

    // Sidebar video click
    document.querySelectorAll('.asset-item[data-type="video"]').forEach(item => {
        item.addEventListener('click', function () {
            document.querySelectorAll('.asset-item[data-type="video"]').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            const fn = this.dataset.filename;
            if (bgVideo && fn) { bgVideo.querySelector('source').src='/storage/'+fn; bgVideo.load(); }
            inspBgVideo.value = this.dataset.id;
        });
    });

    // Inspector video dropdown
    inspBgVideo?.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const fn = opt?.getAttribute('data-filename');
        if (bgVideo && fn) { bgVideo.querySelector('source').src='/storage/'+fn; bgVideo.load(); }
        document.querySelectorAll('.asset-item[data-type="video"]').forEach(i => i.classList.toggle('active', i.dataset.id===this.value));
    });

    // Generate form
    const studioForm = document.getElementById('studioForm');
    studioForm?.addEventListener('submit', function () {
        document.getElementById('statusDot').className = 'status-dot processing';
        document.getElementById('statusText').textContent = 'Generating...';
        const btn = document.getElementById('generateBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating...';
    });

    @if(session('success'))
    document.getElementById('statusDot').className = 'status-dot ready';
    document.getElementById('statusText').textContent = '{{ session("success") }}';
    @endif
});
</script>
@endpush