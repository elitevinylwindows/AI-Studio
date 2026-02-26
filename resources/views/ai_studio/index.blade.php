@extends('layouts.app')

@section('page-title', 'AI Studio')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">AI Studio</li>
  </ol>
</nav>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Space+Mono:wght@400;700&display=swap');

    :root {
        --st-bg: #0a0a0e;
        --st-panel: #131318;
        --st-surface: #1a1a22;
        --st-border: #232330;
        --st-hover: #1e1e2a;
        --st-accent: #6c5ce7;
        --st-accent-glow: rgba(108, 92, 231, 0.25);
        --st-accent-light: #a29bfe;
        --st-success: #00cec9;
        --st-success-glow: rgba(0, 206, 201, 0.2);
        --st-danger: #ff6b6b;
        --st-warning: #feca57;
        --st-text: #eef0f6;
        --st-text-sec: #8b8da3;
        --st-text-muted: #555670;
        --st-gradient: linear-gradient(135deg, #6c5ce7, #a29bfe);
        --st-teal: linear-gradient(135deg, #00cec9, #55efc4);
        --st-pink: linear-gradient(135deg, #e84393, #fd79a8);
        --st-orange: linear-gradient(135deg, #f39c12, #fdcb6e);
        --st-timeline-bg: #0e0e14;
    }

    .ai-studio {
        font-family: 'DM Sans', sans-serif;
        background: var(--st-bg);
        color: var(--st-text);
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* ═══════════════════════════════════
       TOP BAR
       ═══════════════════════════════════ */
    .studio-topbar {
        background: var(--st-panel);
        border-bottom: 1px solid var(--st-border);
        padding: 0 20px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        z-index: 100;
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .topbar-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: -0.02em;
    }

    .topbar-logo .ring {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--st-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        box-shadow: 0 3px 14px var(--st-accent-glow);
    }

    .topbar-divider {
        width: 1px;
        height: 24px;
        background: var(--st-border);
    }

    .project-name {
        font-size: 0.84rem;
        color: var(--st-text-sec);
        padding: 4px 12px;
        border-radius: 6px;
        border: 1px solid transparent;
        cursor: text;
        transition: all 0.15s;
    }

    .project-name:hover {
        border-color: var(--st-border);
        background: var(--st-surface);
    }

    .project-name:focus {
        outline: none;
        border-color: var(--st-accent);
        background: var(--st-surface);
        color: var(--st-text);
    }

    .topbar-center {
        display: flex;
        gap: 2px;
        background: var(--st-bg);
        padding: 3px;
        border-radius: 10px;
    }

    .topbar-tab {
        padding: 6px 16px;
        border-radius: 7px;
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--st-text-sec);
        cursor: pointer;
        border: none;
        background: none;
        transition: all 0.15s;
    }

    .topbar-tab:hover { color: var(--st-text); }

    .topbar-tab.active {
        background: var(--st-accent);
        color: #fff;
        box-shadow: 0 2px 10px var(--st-accent-glow);
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .topbar-btn {
        padding: 7px 16px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: 'DM Sans', sans-serif;
    }

    .topbar-btn.ghost {
        background: var(--st-surface);
        border: 1px solid var(--st-border);
        color: var(--st-text-sec);
    }

    .topbar-btn.ghost:hover {
        border-color: var(--st-accent);
        color: var(--st-text);
    }

    .topbar-btn.primary {
        background: var(--st-gradient);
        color: #fff;
        box-shadow: 0 3px 16px var(--st-accent-glow);
    }

    .topbar-btn.primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 22px var(--st-accent-glow);
    }

    .topbar-btn.export {
        background: var(--st-teal);
        color: #fff;
        box-shadow: 0 3px 16px var(--st-success-glow);
    }

    /* ═══════════════════════════════════
       MAIN LAYOUT: Sidebar + Canvas + Inspector
       ═══════════════════════════════════ */
    .studio-body {
        flex: 1;
        display: flex;
        overflow: hidden;
    }

    /* ── Left Sidebar: Assets ── */
    .sidebar {
        width: 260px;
        background: var(--st-panel);
        border-right: 1px solid var(--st-border);
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        overflow: hidden;
    }

    .sidebar-tabs {
        display: flex;
        border-bottom: 1px solid var(--st-border);
        padding: 0;
    }

    .sidebar-tab {
        flex: 1;
        padding: 12px 0;
        text-align: center;
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--st-text-muted);
        cursor: pointer;
        border: none;
        background: none;
        border-bottom: 2px solid transparent;
        transition: all 0.15s;
    }

    .sidebar-tab:hover { color: var(--st-text-sec); }

    .sidebar-tab.active {
        color: var(--st-accent-light);
        border-bottom-color: var(--st-accent);
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 14px;
        scrollbar-width: thin;
        scrollbar-color: var(--st-border) transparent;
    }

    .sidebar-section-label {
        font-family: 'Space Mono', monospace;
        font-size: 0.62rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--st-text-muted);
        margin: 14px 0 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sidebar-section-label:first-child { margin-top: 0; }

    .sidebar-section-label .dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--st-accent);
    }

    /* Asset Items */
    .asset-item {
        background: var(--st-surface);
        border: 1px solid var(--st-border);
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 6px;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .asset-item:hover {
        border-color: var(--st-accent);
        background: var(--st-hover);
    }

    .asset-item.active {
        border-color: var(--st-accent);
        background: rgba(108, 92, 231, 0.08);
        box-shadow: 0 0 0 2px var(--st-accent-glow);
    }

    .asset-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        color: #fff;
    }

    .asset-icon.video { background: var(--st-gradient); }
    .asset-icon.audio { background: var(--st-teal); }
    .asset-icon.avatar { background: var(--st-pink); }
    .asset-icon.blend { background: var(--st-orange); }

    .asset-info { flex: 1; min-width: 0; }

    .asset-name {
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .asset-meta {
        font-size: 0.68rem;
        color: var(--st-text-muted);
    }

    .asset-add-btn {
        width: 100%;
        padding: 10px;
        border-radius: 10px;
        border: 2px dashed var(--st-border);
        background: none;
        color: var(--st-text-muted);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 8px;
    }

    .asset-add-btn:hover {
        border-color: var(--st-accent);
        color: var(--st-accent-light);
    }

    /* ── Center: Canvas ── */
    .canvas-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .canvas-viewport {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--st-bg);
        position: relative;
        overflow: hidden;
    }

    .canvas-viewport::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 30% 35%, rgba(108, 92, 231, 0.04) 0%, transparent 50%),
            radial-gradient(circle at 70% 65%, rgba(0, 206, 201, 0.03) 0%, transparent 50%);
        pointer-events: none;
    }

    .preview-frame {
        position: relative;
        width: 640px;
        max-width: 90%;
        aspect-ratio: 16 / 9;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
        box-shadow:
            0 20px 60px rgba(0, 0, 0, 0.5),
            0 0 0 1px var(--st-border);
        z-index: 1;
    }

    .preview-frame video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .preview-frame .overlay-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        pointer-events: none;
        z-index: 5;
    }

    .preview-frame .avatar-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 30%;
        z-index: 6;
        pointer-events: none;
        border-radius: 0 0 12px 0;
    }

    /* No video placeholder */
    .no-video-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: var(--st-surface);
    }

    .no-video-placeholder .placeholder-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--st-panel);
        border: 1px solid var(--st-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .no-video-placeholder .placeholder-text {
        font-size: 0.88rem;
        color: var(--st-text-sec);
        font-weight: 500;
    }

    .no-video-placeholder .placeholder-sub {
        font-size: 0.76rem;
        color: var(--st-text-muted);
    }

    /* Canvas bottom toolbar */
    .canvas-toolbar {
        height: 48px;
        background: var(--st-panel);
        border-top: 1px solid var(--st-border);
        border-bottom: 1px solid var(--st-border);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 16px;
        flex-shrink: 0;
    }

    .toolbar-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid var(--st-border);
        background: var(--st-surface);
        color: var(--st-text-sec);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        transition: all 0.15s;
    }

    .toolbar-btn:hover {
        border-color: var(--st-accent);
        color: var(--st-accent);
    }

    .toolbar-btn.play-btn {
        width: 42px;
        height: 42px;
        background: var(--st-gradient);
        border: none;
        color: #fff;
        font-size: 18px;
        box-shadow: 0 3px 16px var(--st-accent-glow);
        border-radius: 50%;
    }

    .toolbar-btn.play-btn:hover {
        transform: scale(1.08);
    }

    .toolbar-time {
        font-family: 'Space Mono', monospace;
        font-size: 0.76rem;
        color: var(--st-text-muted);
        min-width: 56px;
        text-align: center;
    }

    .toolbar-divider {
        width: 1px;
        height: 20px;
        background: var(--st-border);
        margin: 0 4px;
    }

    .toolbar-zoom {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-left: auto;
    }

    .toolbar-zoom input[type="range"] {
        -webkit-appearance: none;
        width: 80px;
        height: 3px;
        background: var(--st-border);
        border-radius: 3px;
        outline: none;
    }

    .toolbar-zoom input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--st-accent);
        cursor: pointer;
    }

    /* ═══════════════════════════════════
       TIMELINE
       ═══════════════════════════════════ */
    .timeline-area {
        height: 200px;
        background: var(--st-timeline-bg);
        border-top: 1px solid var(--st-border);
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        overflow: hidden;
    }

    /* Ruler */
    .timeline-ruler {
        height: 28px;
        background: var(--st-panel);
        border-bottom: 1px solid var(--st-border);
        display: flex;
        align-items: flex-end;
        padding: 0 0 0 180px;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .ruler-ticks {
        display: flex;
        height: 100%;
        align-items: flex-end;
        position: relative;
    }

    .ruler-tick {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 80px;
        flex-shrink: 0;
    }

    .ruler-tick .tick-label {
        font-family: 'Space Mono', monospace;
        font-size: 0.6rem;
        color: var(--st-text-muted);
        margin-bottom: 2px;
    }

    .ruler-tick .tick-line {
        width: 1px;
        height: 8px;
        background: var(--st-border);
    }

    .ruler-tick .tick-line.major {
        height: 12px;
        background: var(--st-text-muted);
    }

    /* Playhead */
    .timeline-playhead {
        position: absolute;
        top: 0;
        left: 180px;
        width: 2px;
        height: 100%;
        background: var(--st-accent);
        z-index: 50;
        pointer-events: none;
        transition: left 0.05s linear;
    }

    .timeline-playhead::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -5px;
        width: 12px;
        height: 12px;
        background: var(--st-accent);
        clip-path: polygon(50% 100%, 0 0, 100% 0);
    }

    /* Track rows */
    .timeline-tracks {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        position: relative;
    }

    .timeline-track {
        display: flex;
        height: 48px;
        border-bottom: 1px solid rgba(35, 35, 48, 0.4);
        position: relative;
    }

    .track-label {
        width: 180px;
        flex-shrink: 0;
        background: var(--st-panel);
        border-right: 1px solid var(--st-border);
        display: flex;
        align-items: center;
        padding: 0 14px;
        gap: 10px;
    }

    .track-icon {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #fff;
        flex-shrink: 0;
    }

    .track-icon.video { background: var(--st-gradient); }
    .track-icon.audio { background: var(--st-teal); }
    .track-icon.avatar { background: var(--st-pink); }
    .track-icon.overlay { background: var(--st-orange); }

    .track-name {
        font-size: 0.76rem;
        font-weight: 500;
        color: var(--st-text-sec);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .track-mute {
        margin-left: auto;
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 1px solid var(--st-border);
        background: none;
        color: var(--st-text-muted);
        font-size: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        flex-shrink: 0;
    }

    .track-mute:hover {
        border-color: var(--st-danger);
        color: var(--st-danger);
    }

    .track-mute.muted {
        background: rgba(255, 107, 107, 0.15);
        border-color: var(--st-danger);
        color: var(--st-danger);
    }

    .track-clips {
        flex: 1;
        position: relative;
        overflow: hidden;
    }

    /* Clip blocks */
    .clip {
        position: absolute;
        top: 6px;
        height: 36px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        padding: 0 10px;
        font-size: 0.7rem;
        font-weight: 600;
        color: #fff;
        cursor: grab;
        user-select: none;
        transition: box-shadow 0.15s;
        overflow: hidden;
        white-space: nowrap;
    }

    .clip:hover {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
    }

    .clip.video-clip {
        background: linear-gradient(90deg, #6c5ce7, #a29bfe);
        left: 0;
        width: 70%;
    }

    .clip.audio-clip {
        background: linear-gradient(90deg, #00cec9, #55efc4);
        color: #0a0a0e;
        left: 0;
        width: 55%;
    }

    .clip.avatar-clip {
        background: linear-gradient(90deg, #e84393, #fd79a8);
        left: 10%;
        width: 45%;
    }

    .clip.overlay-clip {
        background: linear-gradient(90deg, #f39c12, #fdcb6e);
        color: #0a0a0e;
        left: 15%;
        width: 40%;
    }

    .clip .clip-handle {
        position: absolute;
        top: 0;
        width: 6px;
        height: 100%;
        cursor: col-resize;
    }

    .clip .clip-handle.left { left: 0; }
    .clip .clip-handle.right { right: 0; }

    /* ═══════════════════════════════════
       RIGHT INSPECTOR
       ═══════════════════════════════════ */
    .inspector {
        width: 280px;
        background: var(--st-panel);
        border-left: 1px solid var(--st-border);
        flex-shrink: 0;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--st-border) transparent;
    }

    .inspector-section {
        padding: 16px;
        border-bottom: 1px solid var(--st-border);
    }

    .inspector-section:last-child { border-bottom: none; }

    .insp-label {
        font-family: 'Space Mono', monospace;
        font-size: 0.62rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--st-text-muted);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .insp-label .dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
    }

    .insp-label .dot.purple { background: var(--st-accent); }
    .insp-label .dot.teal { background: var(--st-success); }
    .insp-label .dot.pink { background: #e84393; }
    .insp-label .dot.orange { background: #f39c12; }

    .insp-field {
        margin-bottom: 12px;
    }

    .insp-field-label {
        font-size: 0.76rem;
        color: var(--st-text-sec);
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
    }

    .insp-select,
    .insp-input {
        width: 100%;
        background: var(--st-bg);
        border: 1px solid var(--st-border);
        border-radius: 8px;
        padding: 8px 12px;
        color: var(--st-text);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.82rem;
        outline: none;
        transition: all 0.15s;
    }

    .insp-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%238b8da3' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 30px;
        cursor: pointer;
    }

    .insp-select:focus,
    .insp-input:focus {
        border-color: var(--st-accent);
        box-shadow: 0 0 0 3px var(--st-accent-glow);
    }

    .insp-select option {
        background: var(--st-panel);
        color: var(--st-text);
    }

    .insp-textarea {
        width: 100%;
        background: var(--st-bg);
        border: 1px solid var(--st-border);
        border-radius: 8px;
        padding: 8px 12px;
        color: var(--st-text);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.82rem;
        outline: none;
        resize: vertical;
        min-height: 80px;
        line-height: 1.5;
        transition: all 0.15s;
    }

    .insp-textarea:focus {
        border-color: var(--st-accent);
        box-shadow: 0 0 0 3px var(--st-accent-glow);
    }

    .insp-slider-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .insp-slider-label {
        font-size: 0.76rem;
        color: var(--st-text-sec);
        min-width: 70px;
    }

    .insp-slider {
        flex: 1;
    }

    .insp-slider input[type="range"] {
        -webkit-appearance: none;
        width: 100%;
        height: 3px;
        background: var(--st-border);
        border-radius: 3px;
        outline: none;
    }

    .insp-slider input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--st-accent);
        cursor: pointer;
        box-shadow: 0 2px 6px var(--st-accent-glow);
    }

    .insp-slider-val {
        font-family: 'Space Mono', monospace;
        font-size: 0.7rem;
        color: var(--st-accent-light);
        background: var(--st-accent-glow);
        padding: 2px 8px;
        border-radius: 4px;
        min-width: 36px;
        text-align: center;
    }

    .insp-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .insp-action-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .insp-btn {
        width: 100%;
        padding: 10px;
        border-radius: 10px;
        border: none;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .insp-btn.generate {
        background: var(--st-gradient);
        color: #fff;
        box-shadow: 0 4px 18px var(--st-accent-glow);
    }

    .insp-btn.generate:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 24px var(--st-accent-glow);
    }

    .insp-btn.export {
        background: var(--st-bg);
        color: var(--st-success);
        border: 1px solid rgba(0, 206, 201, 0.3);
    }

    .insp-btn.export:hover {
        background: var(--st-success-glow);
        border-color: var(--st-success);
    }

    .insp-btn.secondary {
        background: var(--st-bg);
        border: 1px solid var(--st-border);
        color: var(--st-text-sec);
    }

    .insp-btn.secondary:hover {
        border-color: var(--st-accent);
        color: var(--st-text);
    }

    /* Status */
    .insp-status {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 8px;
        background: var(--st-bg);
        border: 1px solid var(--st-border);
        font-size: 0.76rem;
        color: var(--st-text-sec);
    }

    .insp-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--st-text-muted);
    }

    .insp-status-dot.ready { background: var(--st-success); box-shadow: 0 0 6px var(--st-success-glow); }
    .insp-status-dot.processing { background: var(--st-warning); animation: pulse 1.2s infinite; }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.35; }
    }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--st-border); border-radius: 4px; }

    /* ── Responsive ── */
    @media (max-width: 1100px) {
        .sidebar { display: none; }
        .inspector { width: 240px; }
    }

    @media (max-width: 768px) {
        .inspector { display: none; }
        .studio-topbar { padding: 0 12px; }
        .timeline-area { height: 150px; }
    }
</style>
@endpush

@section('content')
<div class="ai-studio">

    {{-- ═══ TOP BAR ═══ --}}
    <div class="studio-topbar">
        <div class="topbar-left">
            <div class="topbar-logo">
                <span class="ring">🎬</span>
                <span>AI Studio</span>
            </div>
            <div class="topbar-divider"></div>
            <span class="project-name" contenteditable="true" spellcheck="false">Untitled Project</span>
        </div>

        <div class="topbar-center">
            <button class="topbar-tab active" data-tab="edit">Edit</button>
            <button class="topbar-tab" data-tab="audio">Audio</button>
            <button class="topbar-tab" data-tab="effects">Effects</button>
        </div>

        <div class="topbar-right">
            <button type="button" class="topbar-btn ghost" id="undoBtn">↩</button>
            <button type="button" class="topbar-btn ghost" id="redoBtn">↪</button>
            <button type="button" class="topbar-btn export" id="exportBtn">⬇ Export</button>
        </div>
    </div>

    <div class="studio-body">

        {{-- ═══ LEFT SIDEBAR: Assets ═══ --}}
        <div class="sidebar">
            <div class="sidebar-tabs">
                <button class="sidebar-tab active" data-panel="assets">📁 Assets</button>
                <button class="sidebar-tab" data-panel="layers">📑 Layers</button>
            </div>

            <div class="sidebar-content" id="sidebarContent">

                {{-- Videos --}}
                <div class="sidebar-section-label"><span class="dot"></span> Background Videos</div>
                @foreach($videos as $vid)
                <div class="asset-item {{ optional($selectedVideo)->id == $vid->id ? 'active' : '' }}"
                     data-type="video"
                     data-id="{{ $vid->id }}"
                     data-filename="{{ $vid->filename }}">
                    <div class="asset-icon video">🎬</div>
                    <div class="asset-info">
                        <div class="asset-name">{{ $vid->original_name }}</div>
                        <div class="asset-meta">Video</div>
                    </div>
                </div>
                @endforeach

                {{-- Audio --}}
                <div class="sidebar-section-label"><span class="dot"></span> Audio Files</div>
                @foreach($audios as $audio)
                <div class="asset-item {{ optional($selectedAudio)->id == $audio->id ? 'active' : '' }}"
                     data-type="audio"
                     data-id="{{ $audio->id }}">
                    <div class="asset-icon audio">🎵</div>
                    <div class="asset-info">
                        <div class="asset-name">{{ $audio->original_name }}</div>
                        <div class="asset-meta">Audio · MP3</div>
                    </div>
                </div>
                @endforeach

                {{-- Blender --}}
                <div class="sidebar-section-label"><span class="dot"></span> 3D Assets</div>
                @foreach($blenders as $blend)
                <div class="asset-item {{ optional($selectedBlender)->id == $blend->id ? 'active' : '' }}"
                     data-type="blender"
                     data-id="{{ $blend->id }}">
                    <div class="asset-icon blend">🧊</div>
                    <div class="asset-info">
                        <div class="asset-name">{{ $blend->original_name }}</div>
                        <div class="asset-meta">3D Model</div>
                    </div>
                </div>
                @endforeach
                <button type="button" class="asset-add-btn" id="uploadBlenderBtn">+ Upload 3D File</button>

                <div class="sidebar-section-label"><span class="dot"></span> Avatars</div>
                <button type="button" class="asset-add-btn" onclick="window.location='{{ route('avatar.index') }}'">
                    👤 Browse Avatars
                </button>

            </div>
        </div>

        {{-- ═══ CENTER: Canvas + Toolbar + Timeline ═══ --}}
        <div class="canvas-area">

            {{-- Preview --}}
            <div class="canvas-viewport">
                <div class="preview-frame" id="previewFrame">
                    @if($videoPath)
                    <video id="bgVideo" muted loop>
                        <source src="{{ $videoPath }}" type="video/mp4">
                    </video>
                    @else
                    <div class="no-video-placeholder" id="noVideoPlaceholder">
                        <div class="placeholder-icon">🎬</div>
                        <div class="placeholder-text">No video selected</div>
                        <div class="placeholder-sub">Choose a background video from the sidebar</div>
                    </div>
                    @endif

                    <video id="overlayVideo" class="overlay-video" muted loop playsinline style="display:none;">
                        <source src="{{ asset('storage/overlays/elite_talking.webm') }}" type="video/webm">
                    </video>
                </div>
            </div>

            {{-- Transport Toolbar --}}
            <div class="canvas-toolbar">
                <span class="toolbar-time" id="currentTime">0:00.0</span>

                <div class="toolbar-divider"></div>

                <button type="button" class="toolbar-btn" id="skipStartBtn" title="Go to start">⏮</button>
                <button type="button" class="toolbar-btn" id="skipBackBtn" title="Back 5s">⏪</button>
                <button type="button" class="toolbar-btn play-btn" id="playPauseBtn" title="Play / Pause">▶</button>
                <button type="button" class="toolbar-btn" id="skipFwdBtn" title="Forward 5s">⏩</button>
                <button type="button" class="toolbar-btn" id="skipEndBtn" title="Go to end">⏭</button>

                <div class="toolbar-divider"></div>

                <span class="toolbar-time" id="totalTime">0:00.0</span>

                <div class="toolbar-zoom">
                    <span style="font-size:11px;color:var(--st-text-muted);">🔍</span>
                    <input type="range" id="zoomSlider" min="0.5" max="3" step="0.1" value="1" title="Timeline zoom">
                </div>
            </div>

            {{-- Timeline --}}
            <div class="timeline-area" id="timelineArea">
                {{-- Ruler --}}
                <div class="timeline-ruler" id="timelineRuler">
                    <div class="ruler-ticks" id="rulerTicks"></div>
                    <div class="timeline-playhead" id="playhead"></div>
                </div>

                {{-- Tracks --}}
                <div class="timeline-tracks" id="timelineTracks">
                    {{-- Video Track --}}
                    <div class="timeline-track">
                        <div class="track-label">
                            <span class="track-icon video">🎬</span>
                            <span class="track-name">Background</span>
                            <button type="button" class="track-mute" title="Mute">M</button>
                        </div>
                        <div class="track-clips">
                            <div class="clip video-clip" id="clipVideo">
                                <span class="clip-handle left"></span>
                                Background Video
                                <span class="clip-handle right"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Audio Track --}}
                    <div class="timeline-track">
                        <div class="track-label">
                            <span class="track-icon audio">🎵</span>
                            <span class="track-name">Audio / TTS</span>
                            <button type="button" class="track-mute" title="Mute">M</button>
                        </div>
                        <div class="track-clips">
                            <div class="clip audio-clip" id="clipAudio">
                                <span class="clip-handle left"></span>
                                Audio
                                <span class="clip-handle right"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Avatar Track --}}
                    <div class="timeline-track">
                        <div class="track-label">
                            <span class="track-icon avatar">👤</span>
                            <span class="track-name">Avatar</span>
                            <button type="button" class="track-mute" title="Mute">M</button>
                        </div>
                        <div class="track-clips">
                            <div class="clip avatar-clip" id="clipAvatar">
                                <span class="clip-handle left"></span>
                                Avatar Overlay
                                <span class="clip-handle right"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Overlay Track --}}
                    <div class="timeline-track">
                        <div class="track-label">
                            <span class="track-icon overlay">✨</span>
                            <span class="track-name">Overlay</span>
                            <button type="button" class="track-mute" title="Mute">M</button>
                        </div>
                        <div class="track-clips">
                            <div class="clip overlay-clip" id="clipOverlay">
                                <span class="clip-handle left"></span>
                                WebM Overlay
                                <span class="clip-handle right"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ RIGHT INSPECTOR ═══ --}}
        <div class="inspector">
            <form id="studioForm" action="{{ route('ai_studio.generate') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Script --}}
                <div class="inspector-section">
                    <div class="insp-label"><span class="dot teal"></span> Script</div>
                    <div class="insp-field">
                        <label class="insp-field-label">TTS Text</label>
                        <textarea name="tts_text" class="insp-textarea" placeholder="Type your script or leave blank to use existing audio...">{{ old('tts_text', session('ttsText')) }}</textarea>
                    </div>
                    <div class="insp-field">
                        <label class="insp-field-label">Voice</label>
                        <select name="voice" class="insp-select">
                            <option value="">Default</option>
                            @foreach(['en-US-Wavenet-D','en-GB-Wavenet-A','en-AU-Wavenet-B','en-IN-Wavenet-C'] as $voice)
                            <option value="{{ $voice }}" {{ $selectedVoice == $voice ? 'selected' : '' }}>{{ $voice }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="insp-field">
                        <label class="insp-field-label">Existing Audio</label>
                        <select name="mp3_audio" class="insp-select">
                            <option value="">None (use TTS)</option>
                            @foreach($audios as $audio)
                            <option value="{{ $audio->id }}" {{ optional($selectedAudio)->id == $audio->id ? 'selected' : '' }}>
                                {{ $audio->original_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Visual --}}
                <div class="inspector-section">
                    <div class="insp-label"><span class="dot purple"></span> Visual</div>
                    <div class="insp-field">
                        <label class="insp-field-label">Background Video</label>
                        <select name="bg_video" class="insp-select" id="inspBgVideo">
                            <option value="">None</option>
                            @foreach($videos as $vid)
                            <option value="{{ $vid->id }}" data-filename="{{ $vid->filename }}" {{ optional($selectedVideo)->id == $vid->id ? 'selected' : '' }}>
                                {{ $vid->original_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="insp-field">
                        <label class="insp-field-label">3D Model</label>
                        <select name="blender_id" class="insp-select">
                            <option value="">None</option>
                            @foreach($blenders as $blend)
                            <option value="{{ $blend->id }}" {{ optional($selectedBlender)->id == $blend->id ? 'selected' : '' }}>
                                {{ $blend->original_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="insp-field">
                        <label class="insp-field-label">Upload 3D File</label>
                        <input type="file" name="blender_file" class="insp-input" accept=".glb,.fbx" style="padding:6px 10px;">
                    </div>
                </div>

                {{-- Animation --}}
                <div class="inspector-section">
                    <div class="insp-label"><span class="dot orange"></span> Animation</div>
                    <div class="insp-row">
                        <div class="insp-field">
                            <label class="insp-field-label">Mouth</label>
                            <select name="mouth_mode" class="insp-select">
                                <option value="auto" {{ session('mouthMode') === 'auto' ? 'selected' : '' }}>Auto</option>
                                <option value="loop" {{ session('mouthMode') === 'loop' ? 'selected' : '' }}>Loop</option>
                                <option value="none" {{ session('mouthMode') === 'none' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                        <div class="insp-field">
                            <label class="insp-field-label">Overlay</label>
                            <select name="overlay_style" class="insp-select">
                                <option value="webm" {{ session('overlayStyle') === 'webm' ? 'selected' : '' }}>WebM</option>
                                <option value="3d" {{ session('overlayStyle') === '3d' ? 'selected' : '' }}>3D Canvas</option>
                            </select>
                        </div>
                    </div>

                    <div class="insp-slider-row">
                        <span class="insp-slider-label">Overlay Start</span>
                        <div class="insp-slider">
                            <input type="range" id="overlayStartSlider" name="overlay_start" min="0" max="60" step="0.1" value="0">
                        </div>
                        <span class="insp-slider-val" id="overlayStartVal">0.0s</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="inspector-section">
                    <div class="insp-label"><span class="dot purple"></span> Actions</div>
                    <div class="insp-status" id="studioStatus">
                        <span class="insp-status-dot" id="statusDot"></span>
                        <span id="statusText">Ready</span>
                    </div>
                    <div class="insp-action-stack" style="margin-top: 12px;">
                        <button type="submit" class="insp-btn generate" id="generateBtn">
                            ⚡ Generate Video
                        </button>
                        <button type="button" class="insp-btn export" id="exportVideoBtn">
                            ⬇ Export Final
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Refs ──
    const bgVideo       = document.getElementById('bgVideo');
    const overlayVideo  = document.getElementById('overlayVideo');
    const playPauseBtn  = document.getElementById('playPauseBtn');
    const skipStartBtn  = document.getElementById('skipStartBtn');
    const skipBackBtn   = document.getElementById('skipBackBtn');
    const skipFwdBtn    = document.getElementById('skipFwdBtn');
    const skipEndBtn    = document.getElementById('skipEndBtn');
    const currentTimeEl = document.getElementById('currentTime');
    const totalTimeEl   = document.getElementById('totalTime');
    const playhead      = document.getElementById('playhead');
    const overlaySlider = document.getElementById('overlayStartSlider');
    const overlayVal    = document.getElementById('overlayStartVal');
    const inspBgVideo   = document.getElementById('inspBgVideo');

    let isPlaying = false;
    let overlayStartTime = 0;
    let overlayTriggered = false;

    // ── Time format ──
    function fmt(s) {
        if (!s || isNaN(s)) return '0:00.0';
        const m = Math.floor(s / 60);
        const sec = (s % 60).toFixed(1);
        return `${m}:${sec.padStart(4, '0')}`;
    }

    // ── Generate ruler ticks ──
    function buildRuler(duration) {
        const container = document.getElementById('rulerTicks');
        container.innerHTML = '';
        const step = 5; // seconds per tick
        const count = Math.ceil((duration || 30) / step) + 1;
        for (let i = 0; i < count; i++) {
            const tick = document.createElement('div');
            tick.className = 'ruler-tick';
            const t = i * step;
            const m = Math.floor(t / 60);
            const s = t % 60;
            tick.innerHTML = `
                <span class="tick-label">${m}:${s.toString().padStart(2, '0')}</span>
                <span class="tick-line ${i % 2 === 0 ? 'major' : ''}"></span>`;
            container.appendChild(tick);
        }
    }

    // ── Video events ──
    if (bgVideo) {
        bgVideo.addEventListener('loadedmetadata', () => {
            totalTimeEl.textContent = fmt(bgVideo.duration);
            overlaySlider.max = Math.floor(bgVideo.duration);
            buildRuler(bgVideo.duration);
        });

        bgVideo.addEventListener('timeupdate', () => {
            currentTimeEl.textContent = fmt(bgVideo.currentTime);
            // Move playhead
            if (bgVideo.duration) {
                const pct = bgVideo.currentTime / bgVideo.duration;
                const trackWidth = document.querySelector('.track-clips')?.offsetWidth || 600;
                playhead.style.left = (180 + pct * trackWidth) + 'px';
            }
            // Overlay trigger
            if (!overlayTriggered && bgVideo.currentTime >= overlayStartTime) {
                overlayVideo.style.display = 'block';
                overlayVideo.play().catch(() => {});
                overlayTriggered = true;
            }
        });
    }

    // ── Play / Pause ──
    playPauseBtn?.addEventListener('click', () => {
        if (!bgVideo) return;
        if (isPlaying) {
            bgVideo.pause();
            overlayVideo?.pause();
            playPauseBtn.innerHTML = '▶';
        } else {
            bgVideo.play().catch(() => {});
            playPauseBtn.innerHTML = '⏸';
            if (bgVideo.currentTime >= overlayStartTime) {
                overlayVideo.style.display = 'block';
                overlayVideo.play().catch(() => {});
                overlayTriggered = true;
            } else {
                overlayVideo.style.display = 'none';
                overlayTriggered = false;
            }
        }
        isPlaying = !isPlaying;
    });

    skipStartBtn?.addEventListener('click', () => { if (bgVideo) { bgVideo.currentTime = 0; resetOverlay(); } });
    skipBackBtn?.addEventListener('click', () => { if (bgVideo) bgVideo.currentTime = Math.max(0, bgVideo.currentTime - 5); });
    skipFwdBtn?.addEventListener('click', () => { if (bgVideo) bgVideo.currentTime = Math.min(bgVideo.duration, bgVideo.currentTime + 5); });
    skipEndBtn?.addEventListener('click', () => { if (bgVideo) bgVideo.currentTime = bgVideo.duration; });

    function resetOverlay() {
        overlayVideo.style.display = 'none';
        overlayVideo.pause();
        overlayVideo.currentTime = 0;
        overlayTriggered = false;
    }

    // ── Overlay start slider ──
    overlaySlider?.addEventListener('input', function () {
        overlayStartTime = parseFloat(this.value);
        overlayVal.textContent = overlayStartTime.toFixed(1) + 's';
        // Update overlay clip position visually
        const clipOverlay = document.getElementById('clipOverlay');
        if (clipOverlay && bgVideo?.duration) {
            const pct = overlayStartTime / bgVideo.duration * 100;
            clipOverlay.style.left = pct + '%';
            clipOverlay.style.width = Math.max(10, (100 - pct) * 0.6) + '%';
        }
        resetOverlay();
    });

    // ── Background video switcher (sidebar) ──
    document.querySelectorAll('.asset-item[data-type="video"]').forEach(item => {
        item.addEventListener('click', function () {
            document.querySelectorAll('.asset-item[data-type="video"]').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            const filename = this.dataset.filename;
            if (bgVideo && filename) {
                bgVideo.querySelector('source').src = '/storage/' + filename;
                bgVideo.load();
                // Sync inspector dropdown
                inspBgVideo.value = this.dataset.id;
            }
        });
    });

    // ── Inspector video dropdown ──
    inspBgVideo?.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const filename = opt?.getAttribute('data-filename');
        if (bgVideo && filename) {
            bgVideo.querySelector('source').src = '/storage/' + filename;
            bgVideo.load();
        }
        // Sync sidebar
        document.querySelectorAll('.asset-item[data-type="video"]').forEach(i => {
            i.classList.toggle('active', i.dataset.id === this.value);
        });
    });

    // ── Track mute toggle ──
    document.querySelectorAll('.track-mute').forEach(btn => {
        btn.addEventListener('click', function () {
            this.classList.toggle('muted');
        });
    });

    // ── Sidebar tabs ──
    document.querySelectorAll('.sidebar-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // ── Topbar tabs ──
    document.querySelectorAll('.topbar-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.topbar-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // ── Generate form ──
    const studioForm = document.getElementById('studioForm');
    const statusDot = document.getElementById('statusDot');
    const statusText = document.getElementById('statusText');
    const generateBtn = document.getElementById('generateBtn');

    studioForm?.addEventListener('submit', function () {
        statusDot.className = 'insp-status-dot processing';
        statusText.textContent = 'Generating...';
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating...';
    });

    // ── Init ruler ──
    buildRuler(30);

    @if(session('success'))
    statusDot.className = 'insp-status-dot ready';
    statusText.textContent = '{{ session("success") }}';
    @endif
});
</script>
@endpush