@extends('layouts.app')

@section('page-title', 'Voice Studio')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Space+Mono:wght@400;700&display=swap');

    :root {
        --studio-bg: #0c0c10;
        --panel-bg: #16161d;
        --panel-border: #232330;
        --panel-hover: #1e1e2a;
        --accent: #6c5ce7;
        --accent-glow: rgba(108, 92, 231, 0.25);
        --accent-light: #a29bfe;
        --success: #00cec9;
        --success-glow: rgba(0, 206, 201, 0.2);
        --danger: #ff6b6b;
        --danger-glow: rgba(255, 107, 107, 0.2);
        --warning: #feca57;
        --text-primary: #eef0f6;
        --text-secondary: #8b8da3;
        --text-muted: #555670;
        --gradient-1: linear-gradient(135deg, #6c5ce7, #a29bfe);
        --gradient-2: linear-gradient(135deg, #00cec9, #55efc4);
        --gradient-3: linear-gradient(135deg, #fd79a8, #e84393);
    }

    .voice-studio {
        font-family: 'DM Sans', sans-serif;
        background: var(--studio-bg);
        min-height: 100vh;
        color: var(--text-primary);
        padding: 0;
    }

    /* ── Top Bar ── */
    .studio-topbar {
        background: var(--panel-bg);
        border-bottom: 1px solid var(--panel-border);
        padding: 14px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
        backdrop-filter: blur(20px);
    }

    .studio-topbar .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: -0.02em;
    }

    .studio-topbar .logo .icon-ring {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--gradient-1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 4px 20px var(--accent-glow);
    }

    .studio-topbar .nav-pills {
        display: flex;
        gap: 4px;
        background: var(--studio-bg);
        padding: 4px;
        border-radius: 12px;
    }

    .studio-topbar .nav-pill {
        padding: 8px 18px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: none;
    }

    .studio-topbar .nav-pill:hover {
        color: var(--text-primary);
        background: var(--panel-hover);
    }

    .studio-topbar .nav-pill.active {
        background: var(--accent);
        color: #fff;
        box-shadow: 0 2px 12px var(--accent-glow);
    }

    /* ── Main Layout ── */
    .studio-grid {
        display: grid;
        grid-template-columns: 340px 1fr 320px;
        gap: 0;
        min-height: calc(100vh - 65px);
    }

    /* ── Left Panel: Script & Voice ── */
    .panel {
        background: var(--panel-bg);
        border-right: 1px solid var(--panel-border);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--panel-border) transparent;
    }

    .panel:last-child {
        border-right: none;
        border-left: 1px solid var(--panel-border);
    }

    .panel-section {
        padding: 20px;
        border-bottom: 1px solid var(--panel-border);
    }

    .panel-section:last-child {
        border-bottom: none;
    }

    .section-label {
        font-family: 'Space Mono', monospace;
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--text-muted);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-label .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--accent);
    }

    /* ── Form Controls ── */
    .form-input,
    .form-textarea,
    .form-select-custom {
        width: 100%;
        background: var(--studio-bg);
        border: 1px solid var(--panel-border);
        border-radius: 10px;
        padding: 11px 14px;
        color: var(--text-primary);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        transition: all 0.2s;
        outline: none;
    }

    .form-input:focus,
    .form-textarea:focus,
    .form-select-custom:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .form-textarea {
        resize: vertical;
        min-height: 140px;
        line-height: 1.65;
    }

    .form-select-custom {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%238b8da3' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
    }

    .form-select-custom option {
        background: var(--panel-bg);
        color: var(--text-primary);
    }

    .input-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }

    .input-group-inline {
        margin-bottom: 14px;
    }

    .char-count {
        font-family: 'Space Mono', monospace;
        font-size: 0.7rem;
        color: var(--text-muted);
        text-align: right;
        margin-top: 6px;
    }

    .char-count.warn {
        color: var(--warning);
    }

    .char-count.danger {
        color: var(--danger);
    }

    /* ── Voice Card ── */
    .voice-card {
        background: var(--studio-bg);
        border: 1px solid var(--panel-border);
        border-radius: 12px;
        padding: 14px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
    }

    .voice-card:hover {
        border-color: var(--accent);
        background: var(--panel-hover);
    }

    .voice-card.selected {
        border-color: var(--accent);
        background: rgba(108, 92, 231, 0.08);
        box-shadow: 0 0 0 2px var(--accent-glow);
    }

    .voice-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .voice-avatar.male { background: linear-gradient(135deg, #0984e3, #6c5ce7); }
    .voice-avatar.female { background: linear-gradient(135deg, #e84393, #fd79a8); }
    .voice-avatar.neutral { background: linear-gradient(135deg, #00cec9, #55efc4); }

    .voice-info {
        flex: 1;
        min-width: 0;
    }

    .voice-name {
        font-weight: 600;
        font-size: 0.88rem;
        margin-bottom: 2px;
    }

    .voice-meta {
        font-size: 0.72rem;
        color: var(--text-muted);
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .voice-play-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid var(--panel-border);
        background: var(--panel-bg);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .voice-play-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-glow);
    }

    /* ── Emotion / Style Tags ── */
    .tag-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .emotion-tag {
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid var(--panel-border);
        background: var(--studio-bg);
        color: var(--text-secondary);
        font-size: 0.78rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        user-select: none;
    }

    .emotion-tag:hover {
        border-color: var(--accent);
        color: var(--text-primary);
    }

    .emotion-tag.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        box-shadow: 0 2px 10px var(--accent-glow);
    }

    /* ── Slider Controls ── */
    .slider-group {
        margin-bottom: 16px;
    }

    .slider-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .slider-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .slider-value {
        font-family: 'Space Mono', monospace;
        font-size: 0.72rem;
        color: var(--accent-light);
        background: var(--accent-glow);
        padding: 2px 10px;
        border-radius: 6px;
    }

    input[type="range"] {
        -webkit-appearance: none;
        width: 100%;
        height: 4px;
        background: var(--panel-border);
        border-radius: 4px;
        outline: none;
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--accent);
        cursor: pointer;
        box-shadow: 0 2px 8px var(--accent-glow);
        transition: transform 0.15s;
    }

    input[type="range"]::-webkit-slider-thumb:hover {
        transform: scale(1.2);
    }

    /* ── Center Stage: Preview ── */
    .stage {
        display: flex;
        flex-direction: column;
        background: var(--studio-bg);
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
            radial-gradient(circle at 30% 40%, rgba(108, 92, 231, 0.06) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(0, 206, 201, 0.04) 0%, transparent 50%);
        pointer-events: none;
    }

    .avatar-placeholder {
        width: 280px;
        height: 320px;
        border-radius: 24px;
        border: 2px dashed var(--panel-border);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        z-index: 1;
        background: rgba(22, 22, 29, 0.5);
        backdrop-filter: blur(10px);
    }

    .avatar-placeholder:hover {
        border-color: var(--accent);
        background: rgba(108, 92, 231, 0.05);
        transform: translateY(-2px);
    }

    .avatar-placeholder .icon-big {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--gradient-1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        box-shadow: 0 8px 32px var(--accent-glow);
    }

    .avatar-placeholder .hint-text {
        font-size: 0.88rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .avatar-placeholder .hint-sub {
        font-size: 0.72rem;
        color: var(--text-muted);
        max-width: 200px;
        text-align: center;
        line-height: 1.5;
    }

    .avatar-preview-img {
        width: 280px;
        height: 320px;
        border-radius: 24px;
        object-fit: cover;
        border: 2px solid var(--panel-border);
        box-shadow: 0 12px 48px rgba(0,0,0,0.4);
        position: relative;
        z-index: 1;
    }

    /* ── Stage Bottom: Waveform & Controls ── */
    .stage-controls {
        background: var(--panel-bg);
        border-top: 1px solid var(--panel-border);
        padding: 20px 28px;
    }

    .waveform-container {
        width: 100%;
        height: 56px;
        background: var(--studio-bg);
        border-radius: 12px;
        margin-bottom: 16px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .waveform-bars {
        display: flex;
        align-items: center;
        gap: 2px;
        height: 100%;
        padding: 8px 12px;
    }

    .waveform-bar {
        width: 3px;
        background: var(--accent);
        border-radius: 2px;
        opacity: 0.35;
        transition: height 0.1s, opacity 0.2s;
    }

    .waveform-bar.active {
        opacity: 1;
    }

    .waveform-placeholder {
        font-size: 0.78rem;
        color: var(--text-muted);
        font-family: 'Space Mono', monospace;
    }

    .transport-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .transport-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid var(--panel-border);
        background: var(--studio-bg);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.2s;
    }

    .transport-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
    }

    .transport-btn.play {
        width: 54px;
        height: 54px;
        background: var(--gradient-1);
        border: none;
        color: #fff;
        font-size: 22px;
        box-shadow: 0 4px 24px var(--accent-glow);
    }

    .transport-btn.play:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 32px var(--accent-glow);
    }

    .time-display {
        font-family: 'Space Mono', monospace;
        font-size: 0.78rem;
        color: var(--text-muted);
        min-width: 80px;
    }

    .time-display.right {
        text-align: right;
    }

    /* ── Right Panel: Settings ── */
    .action-btn {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: none;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .action-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .btn-generate {
        background: var(--gradient-1);
        color: #fff;
        box-shadow: 0 4px 20px var(--accent-glow);
    }

    .btn-generate:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 6px 28px var(--accent-glow);
    }

    .btn-save {
        background: var(--studio-bg);
        color: var(--success);
        border: 1px solid rgba(0, 206, 201, 0.3);
    }

    .btn-save:hover:not(:disabled) {
        background: var(--success-glow);
        border-color: var(--success);
    }

    .btn-delete {
        background: var(--studio-bg);
        color: var(--danger);
        border: 1px solid rgba(255, 107, 107, 0.3);
    }

    .btn-delete:hover:not(:disabled) {
        background: var(--danger-glow);
        border-color: var(--danger);
    }

    .btn-secondary {
        background: var(--studio-bg);
        color: var(--text-secondary);
        border: 1px solid var(--panel-border);
    }

    .btn-secondary:hover:not(:disabled) {
        border-color: var(--accent);
        color: var(--text-primary);
    }

    .actions-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* ── Language Chips ── */
    .lang-chip-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
    }

    .lang-chip {
        padding: 8px 10px;
        border-radius: 10px;
        border: 1px solid var(--panel-border);
        background: var(--studio-bg);
        color: var(--text-secondary);
        font-size: 0.78rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        user-select: none;
    }

    .lang-chip:hover {
        border-color: var(--accent);
        color: var(--text-primary);
    }

    .lang-chip.selected {
        background: rgba(108, 92, 231, 0.12);
        border-color: var(--accent);
        color: var(--accent-light);
    }

    .lang-chip .flag {
        margin-right: 4px;
    }

    /* ── SSML Sentence Blocks ── */
    .sentence-block {
        background: var(--studio-bg);
        border: 1px solid var(--panel-border);
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 8px;
        transition: all 0.2s;
    }

    .sentence-block:hover {
        border-color: var(--panel-hover);
    }

    .sentence-block .sentence-text {
        font-size: 0.84rem;
        color: var(--text-primary);
        margin-bottom: 8px;
        line-height: 1.5;
    }

    .sentence-block .sentence-controls {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .mini-tag {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.68rem;
        font-weight: 600;
        border: 1px solid var(--panel-border);
        background: var(--panel-bg);
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.15s;
    }

    .mini-tag:hover {
        border-color: var(--accent);
        color: var(--accent-light);
    }

    .mini-tag.assigned {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }

    /* ── Status Indicator ── */
    .status-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 10px;
        background: var(--studio-bg);
        border: 1px solid var(--panel-border);
        font-size: 0.78rem;
        color: var(--text-secondary);
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--text-muted);
    }

    .status-dot.ready { background: var(--success); box-shadow: 0 0 8px var(--success-glow); }
    .status-dot.processing { background: var(--warning); animation: pulse 1s infinite; }
    .status-dot.error { background: var(--danger); }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-in {
        animation: fadeInUp 0.4s ease forwards;
    }

    /* ── Responsive ── */
    @media (max-width: 1200px) {
        .studio-grid {
            grid-template-columns: 1fr;
        }
        .stage { min-height: 400px; }
    }

    /* ── Hidden audio el ── */
    #ttsPlayer { display: none; }

    /* ── Scrollbar ── */
    .panel::-webkit-scrollbar { width: 4px; }
    .panel::-webkit-scrollbar-track { background: transparent; }
    .panel::-webkit-scrollbar-thumb { background: var(--panel-border); border-radius: 4px; }
</style>

<div class="voice-studio">
    {{-- ── Top Bar ── --}}
    <div class="studio-topbar">
        <div class="logo">
            <div class="icon-ring">🎙</div>
            <span>Voice Studio</span>
        </div>
        <div class="nav-pills">
            <button class="nav-pill active" data-tab="generate">Generate</button>
            <button class="nav-pill" data-tab="library">Library</button>
            <button class="nav-pill" data-tab="avatars">Avatars</button>
        </div>
        <div style="width: 120px;"></div>
    </div>

    <form id="ttsForm" action="{{ route('text_to_speech.generate') }}" method="POST">
        @csrf

        <div class="studio-grid">
            {{-- ══════════════════════════════════════════════
                 LEFT PANEL: Script & Voice Selection
                 ══════════════════════════════════════════════ --}}
            <div class="panel">
                {{-- Script Input --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Script</div>

                    <div class="input-group-inline">
                        <label class="input-label" for="audio_name">File Name</label>
                        <input type="text" name="audio_name" id="audio_name" class="form-input" placeholder="e.g. welcome_message_v2">
                    </div>

                    <div class="input-group-inline">
                        <label class="input-label" for="text">Script Text</label>
                        <textarea name="text" id="text" class="form-textarea" placeholder="Type or paste your script here. Each sentence will become a controllable block..." required></textarea>
                        <div class="char-count" id="charCount">0 / 5,000</div>
                    </div>
                </div>

                {{-- Language --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Language</div>
                    <input type="hidden" name="language" id="language" value="en-US">
                    <div class="lang-chip-grid">
                        <div class="lang-chip selected" data-lang="en-US"><span class="flag">🇺🇸</span> English</div>
                        <div class="lang-chip" data-lang="en-GB"><span class="flag">🇬🇧</span> British</div>
                        <div class="lang-chip" data-lang="es-MX"><span class="flag">🇲🇽</span> Español</div>
                        <div class="lang-chip" data-lang="es-ES"><span class="flag">🇪🇸</span> Castellano</div>
                        <div class="lang-chip" data-lang="fr-FR"><span class="flag">🇫🇷</span> Français</div>
                        <div class="lang-chip" data-lang="de-DE"><span class="flag">🇩🇪</span> Deutsch</div>
                        <div class="lang-chip" data-lang="pt-BR"><span class="flag">🇧🇷</span> Português</div>
                        <div class="lang-chip" data-lang="ja-JP"><span class="flag">🇯🇵</span> 日本語</div>
                        <div class="lang-chip" data-lang="ko-KR"><span class="flag">🇰🇷</span> 한국어</div>
                        <div class="lang-chip" data-lang="zh-CN"><span class="flag">🇨🇳</span> 中文</div>
                        <div class="lang-chip" data-lang="hi-IN"><span class="flag">🇮🇳</span> हिन्दी</div>
                        <div class="lang-chip" data-lang="ar-XA"><span class="flag">🇸🇦</span> العربية</div>
                    </div>
                </div>

                {{-- Voice Selection --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Voice</div>
                    <input type="hidden" name="voice" id="voice" value="en-US-Wavenet-D">

                    <div id="voiceList">
                        <div class="voice-card selected" data-voice="en-US-Wavenet-D" data-gender="male">
                            <div class="voice-avatar male">🎤</div>
                            <div class="voice-info">
                                <div class="voice-name">Marcus</div>
                                <div class="voice-meta"><span>Male</span> · <span>Wavenet</span> · <span>Deep & Confident</span></div>
                            </div>
                            <button type="button" class="voice-play-btn" title="Preview voice">▶</button>
                        </div>

                        <div class="voice-card" data-voice="en-US-Wavenet-F" data-gender="female">
                            <div class="voice-avatar female">🎤</div>
                            <div class="voice-info">
                                <div class="voice-name">Sophia</div>
                                <div class="voice-meta"><span>Female</span> · <span>Wavenet</span> · <span>Warm & Clear</span></div>
                            </div>
                            <button type="button" class="voice-play-btn" title="Preview voice">▶</button>
                        </div>

                        <div class="voice-card" data-voice="en-US-Wavenet-A" data-gender="male">
                            <div class="voice-avatar male">🎤</div>
                            <div class="voice-info">
                                <div class="voice-name">James</div>
                                <div class="voice-meta"><span>Male</span> · <span>Wavenet</span> · <span>Authoritative</span></div>
                            </div>
                            <button type="button" class="voice-play-btn" title="Preview voice">▶</button>
                        </div>

                        <div class="voice-card" data-voice="en-US-Wavenet-C" data-gender="female">
                            <div class="voice-avatar female">🎤</div>
                            <div class="voice-info">
                                <div class="voice-name">Elena</div>
                                <div class="voice-meta"><span>Female</span> · <span>Wavenet</span> · <span>Energetic</span></div>
                            </div>
                            <button type="button" class="voice-play-btn" title="Preview voice">▶</button>
                        </div>

                        <div class="voice-card" data-voice="en-US-Neural2-D" data-gender="male">
                            <div class="voice-avatar male">🎤</div>
                            <div class="voice-info">
                                <div class="voice-name">Nathan</div>
                                <div class="voice-meta"><span>Male</span> · <span>Neural2</span> · <span>Natural</span></div>
                            </div>
                            <button type="button" class="voice-play-btn" title="Preview voice">▶</button>
                        </div>

                        <div class="voice-card" data-voice="es-MX-Wavenet-A" data-gender="female">
                            <div class="voice-avatar female">🎤</div>
                            <div class="voice-info">
                                <div class="voice-name">Valentina</div>
                                <div class="voice-meta"><span>Femenina</span> · <span>Wavenet</span> · <span>Cálida</span></div>
                            </div>
                            <button type="button" class="voice-play-btn" title="Preview voice">▶</button>
                        </div>

                        <div class="voice-card" data-voice="es-MX-Wavenet-B" data-gender="male">
                            <div class="voice-avatar male">🎤</div>
                            <div class="voice-info">
                                <div class="voice-name">Diego</div>
                                <div class="voice-meta"><span>Masculino</span> · <span>Wavenet</span> · <span>Profesional</span></div>
                            </div>
                            <button type="button" class="voice-play-btn" title="Preview voice">▶</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════
                 CENTER STAGE: Preview & Waveform
                 ══════════════════════════════════════════════ --}}
            <div class="stage">
                <div class="stage-canvas" id="stageCanvas">
                    <div class="avatar-placeholder" id="avatarDropzone">
                        <div class="icon-big">👤</div>
                        <div class="hint-text">Choose an Avatar</div>
                        <div class="hint-sub">Upload an image or select an AI avatar to lip-sync with your audio</div>
                    </div>
                    <img id="avatarPreviewImg" class="avatar-preview-img" style="display:none;" alt="Avatar Preview">
                </div>

                <div class="stage-controls">
                    {{-- Waveform --}}
                    <div class="waveform-container" id="waveformContainer">
                        <div class="waveform-placeholder" id="waveformPlaceholder">Generate audio to see waveform</div>
                        <div class="waveform-bars" id="waveformBars" style="display:none;"></div>
                    </div>

                    {{-- Transport --}}
                    <div class="transport-controls">
                        <span class="time-display" id="timeElapsed">0:00</span>
                        <button type="button" class="transport-btn" id="skipBackBtn" title="Rewind">⏪</button>
                        <button type="button" class="transport-btn play" id="playPauseBtn" title="Play/Pause">▶</button>
                        <button type="button" class="transport-btn" id="skipFwdBtn" title="Forward">⏩</button>
                        <span class="time-display right" id="timeDuration">0:00</span>
                    </div>
                </div>

                {{-- Hidden audio --}}
                <audio id="ttsPlayer"></audio>
            </div>

            {{-- ══════════════════════════════════════════════
                 RIGHT PANEL: Emotion, Tuning, Actions
                 ══════════════════════════════════════════════ --}}
            <div class="panel">
                {{-- Emotion / Style --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Emotion & Style</div>
                    <input type="hidden" name="emotion" id="emotionInput" value="neutral">
                    <div class="tag-grid" id="emotionGrid">
                        <span class="emotion-tag active" data-emotion="neutral">😐 Neutral</span>
                        <span class="emotion-tag" data-emotion="happy">😊 Happy</span>
                        <span class="emotion-tag" data-emotion="sad">😢 Sad</span>
                        <span class="emotion-tag" data-emotion="excited">🤩 Excited</span>
                        <span class="emotion-tag" data-emotion="angry">😠 Angry</span>
                        <span class="emotion-tag" data-emotion="calm">😌 Calm</span>
                        <span class="emotion-tag" data-emotion="whisper">🤫 Whisper</span>
                        <span class="emotion-tag" data-emotion="serious">🧐 Serious</span>
                        <span class="emotion-tag" data-emotion="friendly">🤗 Friendly</span>
                        <span class="emotion-tag" data-emotion="empathetic">💛 Empathetic</span>
                    </div>
                </div>

                {{-- Voice Tuning --}}
                <div class="panel-section">
                    <div class="section-label"><span class="dot"></span> Voice Tuning</div>

                    <div class="slider-group">
                        <div class="slider-header">
                            <span class="slider-label">Speed</span>
                            <span class="slider-value" id="speedValue">1.0x</span>
                        </div>
                        <input type="range" name="speed" id="speed" min="0.5" max="2.0" step="0.1" value="1.0">
                    </div>

                    <div class="slider-group">
                        <div class="slider-header">
                            <span class="slider-label">Pitch</span>
                            <span class="slider-value" id="pitchValue">0</span>
                        </div>
                        <input type="range" name="pitch" id="pitch" min="-10" max="10" step="1" value="0">
                    </div>

                    <div class="slider-group">
                        <div class="slider-header">
                            <span class="slider-label">Volume Gain</span>
                            <span class="slider-value" id="volumeValue">0 dB</span>
                        </div>
                        <input type="range" name="volume_gain" id="volume_gain" min="-6" max="6" step="1" value="0">
                    </div>

                    <div class="slider-group">
                        <div class="slider-header">
                            <span class="slider-label">Emphasis</span>
                            <span class="slider-value" id="emphasisValue">Medium</span>
                        </div>
                        <input type="range" name="emphasis" id="emphasis" min="0" max="3" step="1" value="1">
                    </div>
                </div>

                {{-- Sentence Blocks (SSML) --}}
                <div class="panel-section" id="sentenceSection" style="display: none;">
                    <div class="section-label"><span class="dot"></span> Sentence Controls</div>
                    <div id="sentenceBlocks"></div>
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
                    <div class="actions-stack">
                        <button type="button" id="generateBtn" class="action-btn btn-generate">
                            <span>⚡</span> Generate Audio
                        </button>
                        <button type="button" id="saveBtn" class="action-btn btn-save" disabled>
                            <span>💾</span> Save to File Manager
                        </button>
                        <button type="button" id="deleteBtn" class="action-btn btn-delete" disabled>
                            <span>🗑</span> Discard Preview
                        </button>
                        <button type="button" id="downloadBtn" class="action-btn btn-secondary" disabled>
                            <span>⬇</span> Download MP3
                        </button>
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

    // ── State ──
    let previewUrl = null;
    let isPlaying = false;

    // ── DOM Refs ──
    const form         = document.getElementById('ttsForm');
    const player       = document.getElementById('ttsPlayer');
    const generateBtn  = document.getElementById('generateBtn');
    const deleteBtn    = document.getElementById('deleteBtn');
    const saveBtn      = document.getElementById('saveBtn');
    const downloadBtn  = document.getElementById('downloadBtn');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const skipBackBtn  = document.getElementById('skipBackBtn');
    const skipFwdBtn   = document.getElementById('skipFwdBtn');
    const textArea     = document.getElementById('text');
    const charCount    = document.getElementById('charCount');
    const statusDot    = document.getElementById('statusDot');
    const statusText   = document.getElementById('statusText');
    const waveformBars = document.getElementById('waveformBars');
    const waveformPlaceholder = document.getElementById('waveformPlaceholder');
    const timeElapsed  = document.getElementById('timeElapsed');
    const timeDuration = document.getElementById('timeDuration');

    // ── Character Count ──
    textArea?.addEventListener('input', function () {
        const len = this.value.length;
        charCount.textContent = `${len.toLocaleString()} / 5,000`;
        charCount.className = 'char-count' + (len > 4500 ? ' danger' : len > 3500 ? ' warn' : '');
        buildSentenceBlocks(this.value);
    });

    // ── Language Chips ──
    document.querySelectorAll('.lang-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.lang-chip').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('language').value = this.dataset.lang;
        });
    });

    // ── Voice Cards ──
    document.querySelectorAll('.voice-card').forEach(card => {
        card.addEventListener('click', function (e) {
            if (e.target.closest('.voice-play-btn')) return;
            document.querySelectorAll('.voice-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('voice').value = this.dataset.voice;
        });
    });

    // ── Emotion Tags ──
    document.querySelectorAll('.emotion-tag').forEach(tag => {
        tag.addEventListener('click', function () {
            document.querySelectorAll('.emotion-tag').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('emotionInput').value = this.dataset.emotion;
        });
    });

    // ── Sliders ──
    document.getElementById('speed')?.addEventListener('input', function () {
        document.getElementById('speedValue').textContent = parseFloat(this.value).toFixed(1) + 'x';
    });
    document.getElementById('pitch')?.addEventListener('input', function () {
        document.getElementById('pitchValue').textContent = this.value;
    });
    document.getElementById('volume_gain')?.addEventListener('input', function () {
        document.getElementById('volumeValue').textContent = this.value + ' dB';
    });
    const emphasisLabels = ['None', 'Medium', 'Strong', 'Max'];
    document.getElementById('emphasis')?.addEventListener('input', function () {
        document.getElementById('emphasisValue').textContent = emphasisLabels[this.value] || 'Medium';
    });

    // ── Sentence Block Builder ──
    function buildSentenceBlocks(text) {
        const container = document.getElementById('sentenceBlocks');
        const section = document.getElementById('sentenceSection');
        const sentences = text.match(/[^.!?]+[.!?]+/g) || [];

        if (sentences.length < 2) {
            section.style.display = 'none';
            return;
        }

        section.style.display = 'block';
        container.innerHTML = '';

        sentences.forEach((s, i) => {
            const block = document.createElement('div');
            block.className = 'sentence-block fade-in';
            block.style.animationDelay = `${i * 0.05}s`;
            block.innerHTML = `
                <div class="sentence-text">${s.trim()}</div>
                <div class="sentence-controls">
                    <span class="mini-tag" data-idx="${i}" data-emotion="happy">😊</span>
                    <span class="mini-tag" data-idx="${i}" data-emotion="sad">😢</span>
                    <span class="mini-tag" data-idx="${i}" data-emotion="excited">🤩</span>
                    <span class="mini-tag" data-idx="${i}" data-emotion="whisper">🤫</span>
                    <span class="mini-tag" data-idx="${i}" data-emotion="serious">🧐</span>
                    <span class="mini-tag" data-idx="${i}" data-emotion="pause">⏸ +Pause</span>
                </div>
            `;
            container.appendChild(block);
        });

        container.querySelectorAll('.mini-tag').forEach(tag => {
            tag.addEventListener('click', function () {
                const siblings = this.parentNode.querySelectorAll(`.mini-tag[data-emotion="${this.dataset.emotion}"]`);
                this.classList.toggle('assigned');
            });
        });
    }

    // ── Status helpers ──
    function setStatus(state, text) {
        statusDot.className = 'status-dot ' + state;
        statusText.textContent = text;
    }

    // ── Generate Waveform Bars ──
    function generateWaveform() {
        waveformBars.innerHTML = '';
        const count = 80;
        for (let i = 0; i < count; i++) {
            const bar = document.createElement('div');
            bar.className = 'waveform-bar';
            const h = Math.random() * 30 + 6;
            bar.style.height = h + 'px';
            waveformBars.appendChild(bar);
        }
        waveformBars.style.display = 'flex';
        waveformPlaceholder.style.display = 'none';
    }

    // ── Animate Waveform ──
    let waveInterval = null;
    function startWaveAnimation() {
        const bars = waveformBars.querySelectorAll('.waveform-bar');
        waveInterval = setInterval(() => {
            bars.forEach(bar => {
                bar.style.height = (Math.random() * 34 + 4) + 'px';
                bar.classList.toggle('active', Math.random() > 0.3);
            });
        }, 120);
    }
    function stopWaveAnimation() {
        clearInterval(waveInterval);
        waveformBars.querySelectorAll('.waveform-bar').forEach(b => b.classList.remove('active'));
    }

    // ── Time formatting ──
    function fmtTime(sec) {
        const m = Math.floor(sec / 60);
        const s = Math.floor(sec % 60);
        return `${m}:${s.toString().padStart(2, '0')}`;
    }

    // ── Player Events ──
    player.addEventListener('timeupdate', () => {
        timeElapsed.textContent = fmtTime(player.currentTime);
    });
    player.addEventListener('loadedmetadata', () => {
        timeDuration.textContent = fmtTime(player.duration);
    });
    player.addEventListener('play', () => {
        isPlaying = true;
        playPauseBtn.innerHTML = '⏸';
        startWaveAnimation();
    });
    player.addEventListener('pause', () => {
        isPlaying = false;
        playPauseBtn.innerHTML = '▶';
        stopWaveAnimation();
    });
    player.addEventListener('ended', () => {
        isPlaying = false;
        playPauseBtn.innerHTML = '▶';
        stopWaveAnimation();
    });

    // ── Transport Controls ──
    playPauseBtn?.addEventListener('click', () => {
        if (!previewUrl) return;
        if (isPlaying) player.pause();
        else player.play().catch(e => console.warn(e));
    });
    skipBackBtn?.addEventListener('click', () => { player.currentTime = Math.max(0, player.currentTime - 5); });
    skipFwdBtn?.addEventListener('click', () => { player.currentTime = Math.min(player.duration, player.currentTime + 5); });

    // ── Generate Preview ──
    generateBtn?.addEventListener('click', function () {
        const textVal = textArea.value.trim();
        if (!textVal) { alert('Please enter script text.'); return; }

        generateBtn.disabled = true;
        generateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generating...';
        setStatus('processing', 'Generating audio...');

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) throw new Error(data.error);

            previewUrl = data.url;
            player.src = previewUrl;
            player.load();

            generateWaveform();
            player.play().catch(e => console.warn('Autoplay blocked:', e));

            deleteBtn.disabled = false;
            saveBtn.disabled = false;
            downloadBtn.disabled = false;

            setStatus('ready', 'Audio ready');
            generateBtn.innerHTML = '<span>⚡</span> Regenerate';
            generateBtn.disabled = false;
        })
        .catch(err => {
            console.error(err);
            setStatus('error', 'Generation failed');
            alert('Error generating audio: ' + (err.message || 'Unknown error'));
            generateBtn.innerHTML = '<span>⚡</span> Generate Audio';
            generateBtn.disabled = false;
        });
    });

    // ── Delete Preview ──
    deleteBtn?.addEventListener('click', function () {
        player.pause();
        player.src = '';
        previewUrl = null;

        waveformBars.style.display = 'none';
        waveformPlaceholder.style.display = 'block';
        waveformBars.innerHTML = '';

        deleteBtn.disabled = true;
        saveBtn.disabled = true;
        downloadBtn.disabled = true;
        timeElapsed.textContent = '0:00';
        timeDuration.textContent = '0:00';
        playPauseBtn.innerHTML = '▶';
        generateBtn.innerHTML = '<span>⚡</span> Generate Audio';

        setStatus('', 'Ready');
        stopWaveAnimation();
    });

    // ── Save to File Manager ──
    saveBtn?.addEventListener('click', function () {
        if (!previewUrl) return;

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';
        setStatus('processing', 'Saving to file manager...');

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
        .then(res => res.json())
        .then(res => {
            setStatus('ready', 'Saved successfully');
            saveBtn.innerHTML = '<span>✅</span> Saved!';
            setTimeout(() => {
                saveBtn.innerHTML = '<span>💾</span> Save to File Manager';
                saveBtn.disabled = false;
            }, 2000);
        })
        .catch(err => {
            setStatus('error', 'Save failed');
            saveBtn.innerHTML = '<span>💾</span> Save to File Manager';
            saveBtn.disabled = false;
            alert('Failed to save to File Manager.');
        });
    });

    // ── Download ──
    downloadBtn?.addEventListener('click', function () {
        if (!previewUrl) return;
        const a = document.createElement('a');
        a.href = previewUrl;
        a.download = (document.getElementById('audio_name').value || 'voice_studio_audio') + '.mp3';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    // ── Avatar Upload ──
    const avatarDropzone = document.getElementById('avatarDropzone');
    const avatarImg = document.getElementById('avatarPreviewImg');

    avatarDropzone?.addEventListener('click', function () {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => {
                avatarImg.src = ev.target.result;
                avatarImg.style.display = 'block';
                avatarDropzone.style.display = 'none';
            };
            reader.readAsDataURL(file);
        };
        input.click();
    });

    avatarImg?.addEventListener('click', function () {
        avatarImg.style.display = 'none';
        avatarDropzone.style.display = 'flex';
    });

    // ── Nav Pills (tab stubs) ──
    document.querySelectorAll('.nav-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.nav-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            // Future: switch tabs for Library & Avatars
        });
    });
});
</script>
@endpush