@extends('layouts.app')

@section('page-title', 'Voice Studio')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Voice Studio</li>
  </ol>
</nav>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Space+Mono:wght@400;700&display=swap');

    :root {
        --vs-bg: #0c0c10;
        --vs-panel: #16161d;
        --vs-surface: #1a1a22;
        --vs-border: #232330;
        --vs-hover: #1e1e2a;
        --vs-accent: #6c5ce7;
        --vs-accent-glow: rgba(108, 92, 231, 0.25);
        --vs-accent-light: #a29bfe;
        --vs-success: #00cec9;
        --vs-success-glow: rgba(0, 206, 201, 0.2);
        --vs-danger: #ff6b6b;
        --vs-danger-glow: rgba(255, 107, 107, 0.15);
        --vs-warning: #feca57;
        --vs-text: #eef0f6;
        --vs-text-sec: #8b8da3;
        --vs-text-muted: #555670;
    }

    .voice-studio {
        font-family: 'DM Sans', sans-serif;
        background: var(--vs-bg);
        min-height: 100vh;
        color: var(--vs-text);
    }

    /* ── Top Bar ── */
    .studio-topbar {
        background: var(--vs-panel);
        border-bottom: 1px solid var(--vs-border);
        padding: 0 24px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .topbar-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 1rem;
    }

    .topbar-logo .ring {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        box-shadow: 0 3px 14px var(--vs-accent-glow);
    }

    /* ── 3-Column Layout ── */
    .studio-grid {
        display: grid;
        grid-template-columns: 320px 1fr 300px;
        min-height: calc(100vh - 52px);
    }

    .panel {
        background: var(--vs-panel);
        border-right: 1px solid var(--vs-border);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--vs-border) transparent;
    }

    .panel:last-child {
        border-right: none;
        border-left: 1px solid var(--vs-border);
    }

    .panel-section {
        padding: 18px;
        border-bottom: 1px solid var(--vs-border);
    }

    .section-label {
        font-family: 'Space Mono', monospace;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--vs-text-muted);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .section-label .dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--vs-accent);
    }

    /* ── Form Controls ── */
    .ctrl-input,
    .ctrl-textarea,
    .ctrl-select {
        width: 100%;
        background: var(--vs-bg);
        border: 1px solid var(--vs-border);
        border-radius: 9px;
        padding: 9px 12px;
        color: var(--vs-text);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .ctrl-input:focus, .ctrl-textarea:focus, .ctrl-select:focus {
        border-color: var(--vs-accent);
        box-shadow: 0 0 0 3px var(--vs-accent-glow);
    }

    .ctrl-textarea {
        resize: vertical;
        min-height: 120px;
        line-height: 1.6;
    }

    .ctrl-select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%238b8da3' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 32px;
    }

    .ctrl-select option { background: var(--vs-panel); color: var(--vs-text); }

    .ctrl-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--vs-text-sec);
        margin-bottom: 5px;
    }

    .ctrl-group { margin-bottom: 12px; }

    .char-count {
        font-family: 'Space Mono', monospace;
        font-size: 0.68rem;
        color: var(--vs-text-muted);
        text-align: right;
        margin-top: 4px;
    }
    .char-count.warn { color: var(--vs-warning); }
    .char-count.over { color: var(--vs-danger); }

    /* ── Voice Cards ── */
    .voice-card {
        background: var(--vs-bg);
        border: 1px solid var(--vs-border);
        border-radius: 10px;
        padding: 11px 13px;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
    }

    .voice-card:hover { border-color: rgba(108,92,231,0.4); background: var(--vs-hover); }

    .voice-card.selected {
        border-color: var(--vs-accent);
        background: rgba(108,92,231,0.08);
        box-shadow: 0 0 0 2px var(--vs-accent-glow);
    }

    .voice-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        color: #fff;
    }

    .voice-avatar.male { background: linear-gradient(135deg, #0984e3, #6c5ce7); }
    .voice-avatar.female { background: linear-gradient(135deg, #e84393, #fd79a8); }
    .voice-avatar.neutral { background: linear-gradient(135deg, #636e72, #b2bec3); }

    .voice-info { flex: 1; min-width: 0; }

    .voice-name {
        font-weight: 600;
        font-size: 0.84rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .voice-meta {
        font-size: 0.7rem;
        color: var(--vs-text-muted);
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .voice-preview-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid var(--vs-border);
        background: var(--vs-panel);
        color: var(--vs-text-sec);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 11px;
        transition: all 0.15s;
        flex-shrink: 0;
    }

    .voice-preview-btn:hover { border-color: var(--vs-accent); color: var(--vs-accent); }

    #voiceList .loading-msg {
        text-align: center;
        padding: 20px;
        font-size: 0.82rem;
        color: var(--vs-text-muted);
    }

    #voiceList .empty-msg {
        text-align: center;
        padding: 24px 12px;
        font-size: 0.82rem;
        color: var(--vs-text-muted);
    }

    /* ── Center Stage ── */
    .stage {
        background: var(--vs-bg);
        display: flex;
        flex-direction: column;
    }

    .stage-canvas {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .stage-canvas::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 30% 40%, rgba(108,92,231,0.05) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(0,206,201,0.03) 0%, transparent 50%);
        pointer-events: none;
    }

    .avatar-zone {
        width: 260px;
        height: 300px;
        border-radius: 20px;
        border: 2px dashed var(--vs-border);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        cursor: pointer;
        transition: all 0.25s;
        position: relative;
        z-index: 1;
        background: rgba(22,22,29,0.5);
        backdrop-filter: blur(8px);
    }

    .avatar-zone:hover {
        border-color: var(--vs-accent);
        background: rgba(108,92,231,0.04);
        transform: translateY(-2px);
    }

    .avatar-zone .zone-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        box-shadow: 0 8px 28px var(--vs-accent-glow);
    }

    .avatar-zone .zone-text { font-size: 0.86rem; color: var(--vs-text-sec); font-weight: 500; }
    .avatar-zone .zone-sub { font-size: 0.72rem; color: var(--vs-text-muted); text-align: center; max-width: 180px; line-height: 1.4; }

    .avatar-preview-img {
        width: 260px;
        height: 300px;
        border-radius: 20px;
        object-fit: cover;
        border: 2px solid var(--vs-border);
        box-shadow: 0 12px 48px rgba(0,0,0,0.4);
        position: relative;
        z-index: 1;
        display: none;
        cursor: pointer;
    }

    /* ── Waveform + Transport ── */
    .stage-controls {
        background: var(--vs-panel);
        border-top: 1px solid var(--vs-border);
        padding: 16px 24px;
    }

    .waveform-box {
        width: 100%;
        height: 48px;
        background: var(--vs-bg);
        border-radius: 10px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .waveform-bars {
        display: flex;
        align-items: center;
        gap: 2px;
        height: 100%;
        padding: 6px 10px;
        display: none;
    }

    .waveform-bar {
        width: 3px;
        background: var(--vs-accent);
        border-radius: 2px;
        opacity: 0.3;
        transition: height 0.1s;
    }

    .waveform-bar.active { opacity: 1; }

    .waveform-placeholder {
        font-size: 0.76rem;
        color: var(--vs-text-muted);
        font-family: 'Space Mono', monospace;
    }

    .transport {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .transport-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1px solid var(--vs-border);
        background: var(--vs-bg);
        color: var(--vs-text-sec);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 15px;
        transition: all 0.15s;
    }

    .transport-btn:hover { border-color: var(--vs-accent); color: var(--vs-accent); }

    .transport-btn.play {
        width: 46px;
        height: 46px;
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        border: none;
        color: #fff;
        font-size: 18px;
        box-shadow: 0 4px 20px var(--vs-accent-glow);
    }

    .transport-btn.play:hover { transform: scale(1.06); }

    .transport-time {
        font-family: 'Space Mono', monospace;
        font-size: 0.74rem;
        color: var(--vs-text-muted);
        min-width: 52px;
    }

    .transport-time.right { text-align: right; }

    /* ── Right Panel ── */
    .slider-group { margin-bottom: 14px; }

    .slider-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .slider-lbl { font-size: 0.78rem; color: var(--vs-text-sec); font-weight: 500; }

    .slider-val {
        font-family: 'Space Mono', monospace;
        font-size: 0.7rem;
        color: var(--vs-accent-light);
        background: var(--vs-accent-glow);
        padding: 2px 8px;
        border-radius: 5px;
    }

    input[type="range"] {
        -webkit-appearance: none;
        width: 100%;
        height: 3px;
        background: var(--vs-border);
        border-radius: 3px;
        outline: none;
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--vs-accent);
        cursor: pointer;
        box-shadow: 0 2px 6px var(--vs-accent-glow);
    }

    /* Emotion Tags */
    .tag-grid { display: flex; flex-wrap: wrap; gap: 5px; }

    .emotion-tag {
        padding: 5px 12px;
        border-radius: 16px;
        border: 1px solid var(--vs-border);
        background: var(--vs-bg);
        color: var(--vs-text-sec);
        font-size: 0.74rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        user-select: none;
    }

    .emotion-tag:hover { border-color: var(--vs-accent); color: var(--vs-text); }

    .emotion-tag.active {
        background: var(--vs-accent);
        border-color: var(--vs-accent);
        color: #fff;
        box-shadow: 0 2px 8px var(--vs-accent-glow);
    }

    /* Action Buttons */
    .action-stack { display: flex; flex-direction: column; gap: 7px; }

    .act-btn {
        width: 100%;
        padding: 10px;
        border-radius: 9px;
        border: none;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    .act-btn:disabled { opacity: 0.35; cursor: not-allowed; }

    .act-btn.generate {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        color: #fff;
        box-shadow: 0 4px 18px var(--vs-accent-glow);
    }

    .act-btn.generate:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 24px var(--vs-accent-glow); }

    .act-btn.save {
        background: var(--vs-bg);
        color: var(--vs-success);
        border: 1px solid rgba(0,206,201,0.3);
    }

    .act-btn.save:hover:not(:disabled) { background: var(--vs-success-glow); border-color: var(--vs-success); }

    .act-btn.delete {
        background: var(--vs-bg);
        color: var(--vs-danger);
        border: 1px solid rgba(255,107,107,0.3);
    }

    .act-btn.delete:hover:not(:disabled) { background: var(--vs-danger-glow); border-color: var(--vs-danger); }

    .act-btn.ghost {
        background: var(--vs-bg);
        color: var(--vs-text-sec);
        border: 1px solid var(--vs-border);
    }

    .act-btn.ghost:hover:not(:disabled) { border-color: var(--vs-accent); color: var(--vs-text); }

    /* Status */
    .status-bar {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        border-radius: 8px;
        background: var(--vs-bg);
        border: 1px solid var(--vs-border);
        font-size: 0.76rem;
        color: var(--vs-text-sec);
    }

    .status-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--vs-text-muted);
    }

    .status-dot.ready { background: var(--vs-success); box-shadow: 0 0 6px var(--vs-success-glow); }
    .status-dot.processing { background: var(--vs-warning); animation: pulse 1.2s infinite; }
    .status-dot.error { background: var(--vs-danger); }

    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.35; } }

    /* Hidden */
    #ttsPlayer { display: none; }

    /* Scrollbar */
    .panel::-webkit-scrollbar { width: 4px; }
    .panel::-webkit-scrollbar-track { background: transparent; }
    .panel::-webkit-scrollbar-thumb { background: var(--vs-border); border-radius: 4px; }

    /* Responsive */
    @media (max-width: 1100px) {
        .studio-grid { grid-template-columns: 1fr; }
        .stage { min-height: 360px; }
    }
</style>
@endpush

@section('content')
<div class="voice-studio">

    {{-- ── Top Bar ── --}}
    <div class="studio-topbar">
        <div class="topbar-logo">
            <span class="ring">🎙</span>
            <span>Voice Studio</span>
        </div>
        <div></div>
    </div>

    <form id="ttsForm" action="{{ route('text_to_speech.generate') }}" method="POST">
        @csrf

        <div class="studio-grid">

            {{-- ═══════════════════════════════════════
                 LEFT PANEL: Script + Language + Voice
                 ═══════════════════════════════════════ --}}
            <div class="panel">

                {{-- Script --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Script</div>

                    <div class="ctrl-group">
                        <label class="ctrl-label" for="audio_name">File Name</label>
                        <input type="text" name="audio_name" id="audio_name" class="ctrl-input" placeholder="e.g. welcome_v2">
                    </div>

                    <div class="ctrl-group">
                        <label class="ctrl-label" for="text">Script Text</label>
                        <textarea name="text" id="text" class="ctrl-textarea" placeholder="Type or paste your script here..." required></textarea>
                        <div class="char-count" id="charCount">0 / 5,000</div>
                    </div>
                </div>

                {{-- Language (from DB) --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Language</div>

                    <div class="ctrl-group">
                        <select name="language" id="languageSelect" class="ctrl-select" required>
                            @foreach($languages as $lang)
                                <option value="{{ $lang->language_code }}"
                                    {{ $lang->language_code === $selectedCode ? 'selected' : '' }}>
                                    {{ $lang->language_full }} ({{ $lang->language_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Voice (from DB, updates via AJAX) --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Voice</div>
                    <input type="hidden" name="voice" id="voiceInput" value="{{ optional($voices->first())->voice_name ?? '' }}">

                    <div id="voiceList">
                        @forelse($voices as $v)
                            @php $g = strtolower($v->gender ?? 'neutral'); @endphp
                            <div class="voice-card {{ $loop->first ? 'selected' : '' }}"
                                 data-voice="{{ $v->voice_name }}"
                                 data-gender="{{ $g }}">
                                <div class="voice-avatar {{ $g }}">🎤</div>
                                <div class="voice-info">
                                    <div class="voice-name">{{ $v->voice_text ?: $v->voice_name }}</div>
                                    <div class="voice-meta">
                                        <span>{{ ucfirst($g) }}</span> ·
                                        <span>{{ strtoupper($v->audio_format ?? 'mp3') }}</span> ·
                                        <span>{{ $v->language_code }}</span>
                                    </div>
                                </div>
                                <button type="button" class="voice-preview-btn" title="Preview">▶</button>
                            </div>
                        @empty
                            <div class="empty-msg">No curated voices for this language.<br>Select another language or visit <a href="{{ route('voices.index') }}" style="color:var(--vs-accent-light)">Voice Bank</a> to set up voices.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 CENTER STAGE: Preview + Waveform
                 ═══════════════════════════════════════ --}}
            <div class="stage">
                <div class="stage-canvas">
                    <div class="avatar-zone" id="avatarZone">
                        <div class="zone-icon">👤</div>
                        <div class="zone-text">Choose an Avatar</div>
                        <div class="zone-sub">Upload an image or pick from your avatar library</div>
                    </div>
                    <img id="avatarImg" class="avatar-preview-img" alt="Avatar">
                </div>

                <div class="stage-controls">
                    <div class="waveform-box">
                        <span class="waveform-placeholder" id="waveformPlaceholder">Generate audio to see waveform</span>
                        <div class="waveform-bars" id="waveformBars"></div>
                    </div>
                    <div class="transport">
                        <span class="transport-time" id="elapsed">0:00</span>
                        <button type="button" class="transport-btn" id="rewindBtn">⏪</button>
                        <button type="button" class="transport-btn play" id="playPauseBtn">▶</button>
                        <button type="button" class="transport-btn" id="ffwdBtn">⏩</button>
                        <span class="transport-time right" id="duration">0:00</span>
                    </div>
                </div>

                <audio id="ttsPlayer"></audio>
            </div>

            {{-- ═══════════════════════════════════════
                 RIGHT PANEL: Tuning + Actions
                 ═══════════════════════════════════════ --}}
            <div class="panel">

                {{-- Emotion --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Emotion & Style</div>
                    <input type="hidden" name="emotion" id="emotionInput" value="neutral">
                    <div class="tag-grid">
                        <span class="emotion-tag active" data-v="neutral">😐 Neutral</span>
                        <span class="emotion-tag" data-v="happy">😊 Happy</span>
                        <span class="emotion-tag" data-v="sad">😢 Sad</span>
                        <span class="emotion-tag" data-v="excited">🤩 Excited</span>
                        <span class="emotion-tag" data-v="angry">😠 Angry</span>
                        <span class="emotion-tag" data-v="calm">😌 Calm</span>
                        <span class="emotion-tag" data-v="whisper">🤫 Whisper</span>
                        <span class="emotion-tag" data-v="serious">🧐 Serious</span>
                        <span class="emotion-tag" data-v="friendly">🤗 Friendly</span>
                    </div>
                </div>

                {{-- Voice Tuning --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Voice Tuning</div>

                    <div class="slider-group">
                        <div class="slider-header">
                            <span class="slider-lbl">Speed</span>
                            <span class="slider-val" id="speedVal">1.0x</span>
                        </div>
                        <input type="range" name="speed" id="speed" min="0.5" max="2.0" step="0.1" value="1.0">
                    </div>

                    <div class="slider-group">
                        <div class="slider-header">
                            <span class="slider-lbl">Pitch</span>
                            <span class="slider-val" id="pitchVal">0</span>
                        </div>
                        <input type="range" name="pitch" id="pitch" min="-10" max="10" step="1" value="0">
                    </div>

                    <div class="slider-group">
                        <div class="slider-header">
                            <span class="slider-lbl">Volume Gain</span>
                            <span class="slider-val" id="volVal">0 dB</span>
                        </div>
                        <input type="range" name="volume_gain" id="volume_gain" min="-6" max="6" step="1" value="0">
                    </div>
                </div>

                {{-- Status --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Status</div>
                    <div class="status-bar" id="statusBar">
                        <span class="status-dot" id="statusDot"></span>
                        <span id="statusText">Ready</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Actions</div>
                    <div class="action-stack">
                        <button type="button" id="generateBtn" class="act-btn generate">⚡ Generate Audio</button>
                        <button type="button" id="saveBtn" class="act-btn save" disabled>💾 Save to File Manager</button>
                        <button type="button" id="deleteBtn" class="act-btn delete" disabled>🗑 Discard</button>
                        <button type="button" id="downloadBtn" class="act-btn ghost" disabled>⬇ Download</button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ── State ── */
    let previewUrl = null;
    let isPlaying = false;

    /* ── Refs ── */
    const form         = document.getElementById('ttsForm');
    const player       = document.getElementById('ttsPlayer');
    const generateBtn  = document.getElementById('generateBtn');
    const deleteBtn    = document.getElementById('deleteBtn');
    const saveBtn      = document.getElementById('saveBtn');
    const downloadBtn  = document.getElementById('downloadBtn');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const rewindBtn    = document.getElementById('rewindBtn');
    const ffwdBtn      = document.getElementById('ffwdBtn');
    const textArea     = document.getElementById('text');
    const charCount    = document.getElementById('charCount');
    const statusDot    = document.getElementById('statusDot');
    const statusText   = document.getElementById('statusText');
    const waveformBars = document.getElementById('waveformBars');
    const waveformPlaceholder = document.getElementById('waveformPlaceholder');
    const elapsed      = document.getElementById('elapsed');
    const duration     = document.getElementById('duration');
    const langSelect   = document.getElementById('languageSelect');
    const voiceInput   = document.getElementById('voiceInput');
    const voiceList    = document.getElementById('voiceList');

    /* ── Char count ── */
    textArea?.addEventListener('input', function () {
        const len = this.value.length;
        charCount.textContent = `${len.toLocaleString()} / 5,000`;
        charCount.className = 'char-count' + (len > 4500 ? ' over' : len > 3500 ? ' warn' : '');
    });

    /* ═══════════════════════════════════════
       LANGUAGE → VOICE (AJAX dependent dropdown)
       ═══════════════════════════════════════ */
    langSelect?.addEventListener('change', function () {
        const code = this.value;
        voiceList.innerHTML = '<div class="loading-msg">Loading voices...</div>';
        voiceInput.value = '';

        fetch(`{{ route('text_to_speech.voices_by_language') }}?code=${encodeURIComponent(code)}`)
            .then(r => r.json())
            .then(voices => {
                voiceList.innerHTML = '';

                if (!voices.length) {
                    voiceList.innerHTML = '<div class="empty-msg">No curated voices for this language.<br>Visit <a href="{{ route("voices.index") }}" style="color:var(--vs-accent-light)">Voice Bank</a> to set up voices.</div>';
                    return;
                }

                voices.forEach((v, i) => {
                    const g = (v.gender || 'neutral').toLowerCase();
                    const card = document.createElement('div');
                    card.className = 'voice-card' + (i === 0 ? ' selected' : '');
                    card.dataset.voice = v.voice_name;
                    card.dataset.gender = g;
                    card.innerHTML = `
                        <div class="voice-avatar ${g}">🎤</div>
                        <div class="voice-info">
                            <div class="voice-name">${v.voice_text || v.voice_name}</div>
                            <div class="voice-meta">
                                <span>${g.charAt(0).toUpperCase() + g.slice(1)}</span> ·
                                <span>${(v.audio_format || 'mp3').toUpperCase()}</span> ·
                                <span>${v.language_code}</span>
                            </div>
                        </div>
                        <button type="button" class="voice-preview-btn" title="Preview">▶</button>`;
                    voiceList.appendChild(card);
                });

                // Auto-select first
                voiceInput.value = voices[0].voice_name;
                bindVoiceCards();
            })
            .catch(() => {
                voiceList.innerHTML = '<div class="empty-msg">Failed to load voices.</div>';
            });
    });

    /* ── Voice card click / preview binding ── */
    function bindVoiceCards() {
        voiceList.querySelectorAll('.voice-card').forEach(card => {
            card.addEventListener('click', function (e) {
                if (e.target.closest('.voice-preview-btn')) return;
                voiceList.querySelectorAll('.voice-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                voiceInput.value = this.dataset.voice;
            });
        });

        voiceList.querySelectorAll('.voice-preview-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const card = this.closest('.voice-card');
                if (!card) return;
                // Quick TTS preview: generate with minimal text
                const previewText = textArea.value.trim().substring(0, 100) || 'Hello, this is a voice preview.';
                this.textContent = '⏳';

                const fd = new FormData();
                fd.append('_token', form.querySelector('[name="_token"]').value);
                fd.append('text', previewText);
                fd.append('language', langSelect.value);
                fd.append('voice', card.dataset.voice);

                fetch(form.action, { method: 'POST', body: fd })
                    .then(r => r.json())
                    .then(data => {
                        if (data.url) {
                            player.src = data.url;
                            player.play().catch(() => {});
                        }
                        this.textContent = '▶';
                    })
                    .catch(() => { this.textContent = '▶'; });
            });
        });
    }

    // Initial binding for server-rendered cards
    bindVoiceCards();

    /* ── Emotion tags ── */
    document.querySelectorAll('.emotion-tag').forEach(tag => {
        tag.addEventListener('click', function () {
            document.querySelectorAll('.emotion-tag').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('emotionInput').value = this.dataset.v;
        });
    });

    /* ── Sliders ── */
    document.getElementById('speed')?.addEventListener('input', function () {
        document.getElementById('speedVal').textContent = parseFloat(this.value).toFixed(1) + 'x';
    });
    document.getElementById('pitch')?.addEventListener('input', function () {
        document.getElementById('pitchVal').textContent = this.value;
    });
    document.getElementById('volume_gain')?.addEventListener('input', function () {
        document.getElementById('volVal').textContent = this.value + ' dB';
    });

    /* ── Helpers ── */
    function setStatus(state, msg) {
        statusDot.className = 'status-dot ' + state;
        statusText.textContent = msg;
    }

    function fmtTime(s) {
        const m = Math.floor(s / 60);
        const sec = Math.floor(s % 60);
        return `${m}:${sec.toString().padStart(2, '0')}`;
    }

    function buildWaveform() {
        waveformBars.innerHTML = '';
        for (let i = 0; i < 70; i++) {
            const b = document.createElement('div');
            b.className = 'waveform-bar';
            b.style.height = (Math.random() * 26 + 4) + 'px';
            waveformBars.appendChild(b);
        }
        waveformBars.style.display = 'flex';
        waveformPlaceholder.style.display = 'none';
    }

    let waveInt = null;
    function startWaveAnim() {
        const bars = waveformBars.querySelectorAll('.waveform-bar');
        waveInt = setInterval(() => {
            bars.forEach(b => {
                b.style.height = (Math.random() * 28 + 4) + 'px';
                b.classList.toggle('active', Math.random() > 0.3);
            });
        }, 120);
    }
    function stopWaveAnim() {
        clearInterval(waveInt);
        waveformBars.querySelectorAll('.waveform-bar').forEach(b => b.classList.remove('active'));
    }

    /* ── Player events ── */
    player.addEventListener('timeupdate', () => { elapsed.textContent = fmtTime(player.currentTime); });
    player.addEventListener('loadedmetadata', () => { duration.textContent = fmtTime(player.duration); });
    player.addEventListener('play', () => { isPlaying = true; playPauseBtn.innerHTML = '⏸'; startWaveAnim(); });
    player.addEventListener('pause', () => { isPlaying = false; playPauseBtn.innerHTML = '▶'; stopWaveAnim(); });
    player.addEventListener('ended', () => { isPlaying = false; playPauseBtn.innerHTML = '▶'; stopWaveAnim(); });

    playPauseBtn?.addEventListener('click', () => {
        if (!previewUrl) return;
        isPlaying ? player.pause() : player.play().catch(() => {});
    });
    rewindBtn?.addEventListener('click', () => { player.currentTime = Math.max(0, player.currentTime - 5); });
    ffwdBtn?.addEventListener('click', () => { player.currentTime = Math.min(player.duration || 0, player.currentTime + 5); });

    /* ═══════════════════════════════════════
       GENERATE — Posts to TextToSpeechController@generate
       Sends: text, language, voice (+ audio_name)
       ═══════════════════════════════════════ */
    generateBtn?.addEventListener('click', function () {
        if (!textArea.value.trim()) return alert('Please enter script text.');
        if (!voiceInput.value) return alert('Please select a voice.');

        generateBtn.disabled = true;
        generateBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating...';
        setStatus('processing', 'Generating audio...');

        const fd = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value },
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) throw new Error(data.error);

            previewUrl = data.url;
            player.src = previewUrl;
            player.load();
            buildWaveform();
            player.play().catch(() => {});

            deleteBtn.disabled = false;
            saveBtn.disabled = false;
            downloadBtn.disabled = false;

            setStatus('ready', 'Audio ready');
            generateBtn.innerHTML = '⚡ Regenerate';
            generateBtn.disabled = false;
        })
        .catch(err => {
            console.error(err);
            setStatus('error', 'Generation failed');
            alert('Error: ' + (err.message || 'Unknown error'));
            generateBtn.innerHTML = '⚡ Generate Audio';
            generateBtn.disabled = false;
        });
    });

    /* ── Delete ── */
    deleteBtn?.addEventListener('click', function () {
        player.pause();
        player.src = '';
        previewUrl = null;
        waveformBars.style.display = 'none';
        waveformBars.innerHTML = '';
        waveformPlaceholder.style.display = '';
        deleteBtn.disabled = true;
        saveBtn.disabled = true;
        downloadBtn.disabled = true;
        elapsed.textContent = '0:00';
        duration.textContent = '0:00';
        playPauseBtn.innerHTML = '▶';
        generateBtn.innerHTML = '⚡ Generate Audio';
        setStatus('', 'Ready');
        stopWaveAnim();
    });

    /* ── Save to File Manager ── */
    saveBtn?.addEventListener('click', function () {
        if (!previewUrl) return;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
        setStatus('processing', 'Saving...');

        fetch('{{ route("file_manager.save_from_tts") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
            },
            body: JSON.stringify({
                url: previewUrl,
                name: document.getElementById('audio_name').value || null
            })
        })
        .then(r => r.json())
        .then(() => {
            setStatus('ready', 'Saved successfully');
            saveBtn.innerHTML = '✅ Saved!';
            setTimeout(() => { saveBtn.innerHTML = '💾 Save to File Manager'; saveBtn.disabled = false; }, 2000);
        })
        .catch(() => {
            setStatus('error', 'Save failed');
            saveBtn.innerHTML = '💾 Save to File Manager';
            saveBtn.disabled = false;
        });
    });

    /* ── Download ── */
    downloadBtn?.addEventListener('click', function () {
        if (!previewUrl) return;
        const a = document.createElement('a');
        a.href = previewUrl;
        a.download = (document.getElementById('audio_name').value || 'voice_studio') + '.mp3';
        document.body.appendChild(a);
        a.click();
        a.remove();
    });

    /* ── Avatar upload ── */
    const avatarZone = document.getElementById('avatarZone');
    const avatarImg = document.getElementById('avatarImg');

    avatarZone?.addEventListener('click', function () {
        const inp = document.createElement('input');
        inp.type = 'file';
        inp.accept = 'image/*';
        inp.onchange = e => {
            const f = e.target.files[0];
            if (!f) return;
            const reader = new FileReader();
            reader.onload = ev => {
                avatarImg.src = ev.target.result;
                avatarImg.style.display = 'block';
                avatarZone.style.display = 'none';
            };
            reader.readAsDataURL(f);
        };
        inp.click();
    });

    avatarImg?.addEventListener('click', function () {
        this.style.display = 'none';
        avatarZone.style.display = '';
    });
});
</script>
@endpush