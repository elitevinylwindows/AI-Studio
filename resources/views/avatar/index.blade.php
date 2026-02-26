@extends('layouts.app')

@section('page-title', 'Avatars')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Avatars</li>
  </ol>
</nav>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Space+Mono:wght@400;700&display=swap');

    :root {
        --av-bg: #0c0c10;
        --av-panel: #16161d;
        --av-border: #232330;
        --av-hover: #1e1e2a;
        --av-accent: #6c5ce7;
        --av-accent-glow: rgba(108, 92, 231, 0.25);
        --av-accent-light: #a29bfe;
        --av-success: #00cec9;
        --av-success-glow: rgba(0, 206, 201, 0.2);
        --av-danger: #ff6b6b;
        --av-warning: #feca57;
        --av-text: #eef0f6;
        --av-text-sec: #8b8da3;
        --av-text-muted: #555670;
        --av-gradient: linear-gradient(135deg, #6c5ce7, #a29bfe);
        --av-pink: linear-gradient(135deg, #e84393, #fd79a8);
        --av-teal: linear-gradient(135deg, #00cec9, #55efc4);
    }

    .avatar-page {
        font-family: 'DM Sans', sans-serif;
        background: var(--av-bg);
        min-height: 100vh;
        color: var(--av-text);
        padding-bottom: 80px;
    }

    /* ── Header ── */
    .av-header {
        padding: 28px 36px 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .av-header-left h1 {
        font-size: 1.6rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .av-header-left h1 .icon-ring {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: var(--av-gradient);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 20px var(--av-accent-glow);
    }

    .av-header-left .subtitle {
        font-size: 0.85rem;
        color: var(--av-text-sec);
        margin-left: 52px;
    }

    .av-header-right {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .av-stat {
        background: var(--av-panel);
        border: 1px solid var(--av-border);
        border-radius: 12px;
        padding: 10px 18px;
        text-align: center;
        min-width: 80px;
    }

    .av-stat .num {
        font-family: 'Space Mono', monospace;
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--av-accent-light);
    }

    .av-stat .label {
        font-size: 0.68rem;
        color: var(--av-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .btn-purchase {
        background: var(--av-pink);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 11px 22px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
        box-shadow: 0 4px 20px rgba(232, 67, 147, 0.25);
    }

    .btn-purchase:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 28px rgba(232, 67, 147, 0.35);
        color: #fff;
    }

    /* ── Section Headers ── */
    .section-bar {
        margin: 28px 36px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .section-title {
        font-family: 'Space Mono', monospace;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--av-text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--av-accent);
    }

    .section-count {
        font-family: 'Space Mono', monospace;
        font-size: 0.72rem;
        color: var(--av-text-muted);
        background: var(--av-panel);
        border: 1px solid var(--av-border);
        border-radius: 8px;
        padding: 4px 12px;
    }

    /* ── Card Grid ── */
    .av-grid {
        margin: 0 36px;
        display: grid;
        gap: 16px;
    }

    .av-grid.my-grid {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    }

    .av-grid.public-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }

    /* ── Create Card ── */
    .create-card {
        background: var(--av-panel);
        border: 2px dashed var(--av-border);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 32px 16px;
        cursor: pointer;
        transition: all 0.25s;
        text-decoration: none;
        min-height: 240px;
    }

    .create-card:hover {
        border-color: var(--av-accent);
        background: rgba(108, 92, 231, 0.05);
        transform: translateY(-3px);
    }

    .create-card .create-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--av-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: #fff;
        margin-bottom: 14px;
        box-shadow: 0 6px 24px var(--av-accent-glow);
        transition: transform 0.2s;
    }

    .create-card:hover .create-icon {
        transform: scale(1.1);
    }

    .create-card .create-label {
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--av-text);
        margin-bottom: 4px;
    }

    .create-card .create-sub {
        font-size: 0.74rem;
        color: var(--av-text-muted);
        text-align: center;
    }

    /* ── Avatar Card ── */
    .av-card {
        background: var(--av-panel);
        border: 1px solid var(--av-border);
        border-radius: 16px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.25s;
        position: relative;
    }

    .av-card:hover {
        border-color: rgba(108, 92, 231, 0.4);
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35);
    }

    .av-card-img {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        display: block;
        background: var(--av-bg);
    }

    .av-card.landscape .av-card-img {
        aspect-ratio: 16 / 10;
    }

    .av-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(0deg, rgba(12, 12, 16, 0.85) 0%, transparent 50%);
        opacity: 0;
        transition: opacity 0.25s;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 16px;
    }

    .av-card:hover .av-card-overlay {
        opacity: 1;
    }

    .av-card-overlay .overlay-actions {
        display: flex;
        gap: 8px;
        margin-bottom: 8px;
    }

    .overlay-btn {
        padding: 7px 16px;
        border-radius: 8px;
        border: none;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.76rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .overlay-btn.primary {
        background: var(--av-gradient);
        color: #fff;
        box-shadow: 0 3px 12px var(--av-accent-glow);
    }

    .overlay-btn.primary:hover { transform: scale(1.04); }

    .overlay-btn.ghost {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        backdrop-filter: blur(6px);
    }

    .overlay-btn.ghost:hover { background: rgba(255, 255, 255, 0.2); }

    /* Bookmark */
    .av-bookmark {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(22, 22, 29, 0.6);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
        color: var(--av-text-sec);
        z-index: 2;
    }

    .av-bookmark:hover {
        background: rgba(22, 22, 29, 0.85);
        border-color: var(--av-warning);
        color: var(--av-warning);
    }

    .av-bookmark.saved {
        background: rgba(254, 202, 87, 0.2);
        border-color: var(--av-warning);
        color: var(--av-warning);
    }

    /* Card Info */
    .av-card-info {
        padding: 12px 14px;
    }

    .av-card-name {
        font-weight: 600;
        font-size: 0.86rem;
        color: var(--av-text);
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .av-card-meta {
        font-size: 0.72rem;
        color: var(--av-text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .av-card-tags {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        margin-top: 6px;
    }

    .av-tag {
        font-size: 0.66rem;
        padding: 2px 8px;
        border-radius: 6px;
        background: var(--av-bg);
        border: 1px solid var(--av-border);
        color: var(--av-text-muted);
        font-weight: 500;
    }

    /* Looks badge */
    .looks-badge {
        font-family: 'Space Mono', monospace;
        font-size: 0.64rem;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(108, 92, 231, 0.12);
        border: 1px solid rgba(108, 92, 231, 0.25);
        color: var(--av-accent-light);
    }

    /* ── Filters Bar ── */
    .av-filters {
        margin: 0 36px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-chip {
        padding: 7px 18px;
        border-radius: 20px;
        border: 1px solid var(--av-border);
        background: var(--av-panel);
        color: var(--av-text-sec);
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        user-select: none;
        text-decoration: none;
    }

    .filter-chip:hover {
        border-color: var(--av-accent);
        color: var(--av-text);
    }

    .filter-chip.active {
        background: var(--av-accent);
        border-color: var(--av-accent);
        color: #fff;
        box-shadow: 0 2px 12px var(--av-accent-glow);
    }

    .av-search-wrap {
        flex: 1;
        min-width: 200px;
        max-width: 360px;
        position: relative;
    }

    .av-search {
        width: 100%;
        background: var(--av-bg);
        border: 1px solid var(--av-border);
        border-radius: 12px;
        padding: 9px 14px 9px 38px;
        color: var(--av-text);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        outline: none;
        transition: all 0.2s;
    }

    .av-search:focus {
        border-color: var(--av-accent);
        box-shadow: 0 0 0 3px var(--av-accent-glow);
    }

    .av-search-icon {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--av-text-muted);
        font-size: 14px;
        pointer-events: none;
    }

    /* ── View Toggle ── */
    .view-toggle {
        display: flex;
        background: var(--av-bg);
        border: 1px solid var(--av-border);
        border-radius: 10px;
        overflow: hidden;
    }

    .view-toggle-btn {
        padding: 8px 14px;
        border: none;
        background: none;
        color: var(--av-text-muted);
        cursor: pointer;
        transition: all 0.15s;
        font-size: 14px;
    }

    .view-toggle-btn:hover { color: var(--av-text-sec); }

    .view-toggle-btn.active {
        background: var(--av-accent);
        color: #fff;
    }

    /* ── Empty State ── */
    .av-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
    }

    .av-empty .empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--av-panel);
        border: 1px solid var(--av-border);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 16px;
    }

    .av-empty h3 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .av-empty p {
        font-size: 0.84rem;
        color: var(--av-text-muted);
    }

    /* ── Detail Modal ── */
    .modal-dark .modal-content {
        background: var(--av-panel);
        border: 1px solid var(--av-border);
        border-radius: 20px;
        color: var(--av-text);
        overflow: hidden;
    }

    .modal-dark .modal-header {
        border-bottom: 1px solid var(--av-border);
        padding: 18px 24px;
    }

    .modal-dark .modal-title {
        font-weight: 700;
        font-size: 1.1rem;
    }

    .modal-dark .btn-close {
        filter: invert(1) brightness(0.7);
    }

    .modal-dark .modal-body {
        padding: 0;
    }

    .modal-dark .modal-footer {
        border-top: 1px solid var(--av-border);
        padding: 16px 24px;
    }

    .detail-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 400px;
    }

    .detail-img-wrap {
        background: var(--av-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .detail-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .detail-info {
        padding: 28px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .detail-name {
        font-size: 1.3rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .detail-meta-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .detail-badge {
        font-size: 0.76rem;
        padding: 5px 14px;
        border-radius: 8px;
        font-weight: 500;
    }

    .detail-badge.gender {
        background: rgba(108, 92, 231, 0.12);
        border: 1px solid rgba(108, 92, 231, 0.25);
        color: var(--av-accent-light);
    }

    .detail-badge.style {
        background: rgba(0, 206, 201, 0.12);
        border: 1px solid rgba(0, 206, 201, 0.25);
        color: var(--av-success);
    }

    .detail-badge.type {
        background: var(--av-bg);
        border: 1px solid var(--av-border);
        color: var(--av-text-sec);
    }

    .detail-section-label {
        font-family: 'Space Mono', monospace;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--av-text-muted);
        margin-bottom: 8px;
    }

    .detail-looks-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    .detail-look-thumb {
        aspect-ratio: 1;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid var(--av-border);
        cursor: pointer;
        transition: all 0.2s;
    }

    .detail-look-thumb:hover {
        border-color: var(--av-accent);
        transform: scale(1.03);
    }

    .detail-look-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .detail-actions {
        margin-top: auto;
        display: flex;
        gap: 10px;
    }

    .btn-use-avatar {
        flex: 1;
        background: var(--av-gradient);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 20px var(--av-accent-glow);
    }

    .btn-use-avatar:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 28px var(--av-accent-glow);
    }

    .btn-detail-ghost {
        background: var(--av-bg);
        border: 1px solid var(--av-border);
        color: var(--av-text-sec);
        border-radius: 12px;
        padding: 12px 18px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
    }

    .btn-detail-ghost:hover {
        border-color: var(--av-accent);
        color: var(--av-text);
    }

    /* ── Animations ── */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .av-card, .create-card {
        animation: fadeInUp 0.4s ease forwards;
        opacity: 0;
    }

    /* stagger children */
    .av-grid > :nth-child(1) { animation-delay: 0s; }
    .av-grid > :nth-child(2) { animation-delay: 0.04s; }
    .av-grid > :nth-child(3) { animation-delay: 0.08s; }
    .av-grid > :nth-child(4) { animation-delay: 0.12s; }
    .av-grid > :nth-child(5) { animation-delay: 0.16s; }
    .av-grid > :nth-child(6) { animation-delay: 0.20s; }
    .av-grid > :nth-child(7) { animation-delay: 0.24s; }
    .av-grid > :nth-child(8) { animation-delay: 0.28s; }
    .av-grid > :nth-child(9) { animation-delay: 0.32s; }
    .av-grid > :nth-child(10) { animation-delay: 0.36s; }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--av-border); border-radius: 4px; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .av-header, .section-bar, .av-filters { margin-left: 16px; margin-right: 16px; }
        .av-grid { margin-left: 16px; margin-right: 16px; }
        .av-grid.my-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
        .av-grid.public-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
        .detail-layout { grid-template-columns: 1fr; }
        .detail-img-wrap { max-height: 250px; }
    }
</style>
@endpush

@section('content')
<div class="avatar-page">

    {{-- ── Header ── --}}
    <div class="av-header">
        <div class="av-header-left">
            <h1>
                <span class="icon-ring">👤</span>
                Avatars
            </h1>
            <div class="subtitle">Create, browse, and manage your AI presenters</div>
        </div>
        <div class="av-header-right">
            <div class="av-stat">
                <div class="num">{{ count($myAvatars) }}</div>
                <div class="label">My Avatars</div>
            </div>
            <div class="av-stat">
                <div class="num">{{ count($publicAvatars) }}</div>
                <div class="label">Public</div>
            </div>
            <a href="#" class="btn-purchase">
                ✦ Get More Avatars
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MY AVATARS
         ══════════════════════════════════════════ --}}
    <div class="section-bar">
        <div class="section-title"><span class="dot"></span> My Avatars</div>
        <span class="section-count">{{ count($myAvatars) }} avatar{{ count($myAvatars) !== 1 ? 's' : '' }}</span>
    </div>

    <div class="av-grid my-grid" style="margin-bottom: 8px;">
        {{-- Create New --}}
        <a href="#" class="create-card" id="createAvatarBtn">
            <div class="create-icon">+</div>
            <div class="create-label">Create Avatar</div>
            <div class="create-sub">Upload a photo or generate with AI</div>
        </a>

        @foreach($myAvatars as $av)
        <div class="av-card"
             data-id="{{ $av['id'] ?? '' }}"
             data-name="{{ $av['name'] }}"
             data-image="{{ $av['image'] ?? '' }}"
             data-looks="{{ $av['looks'] ?? 0 }}"
             data-type="my">
            <img src="{{ $av['image'] ?? 'https://placehold.co/400x400/16161d/555670?text=Avatar' }}"
                 class="av-card-img"
                 alt="{{ $av['name'] }}"
                 loading="lazy">
            <button type="button" class="av-bookmark {{ !empty($av['bookmarked']) ? 'saved' : '' }}" data-id="{{ $av['id'] ?? '' }}" title="Bookmark">
                {{ !empty($av['bookmarked']) ? '★' : '☆' }}
            </button>
            <div class="av-card-overlay">
                <div class="overlay-actions">
                    <button type="button" class="overlay-btn primary btn-use-in-studio" data-id="{{ $av['id'] ?? '' }}">⚡ Use</button>
                    <button type="button" class="overlay-btn ghost btn-view-detail" data-id="{{ $av['id'] ?? '' }}">Details</button>
                </div>
            </div>
            <div class="av-card-info">
                <div class="av-card-name">{{ $av['name'] }}</div>
                <div class="av-card-meta">
                    <span class="looks-badge">{{ $av['looks'] ?? 0 }} look{{ ($av['looks'] ?? 0) === 1 ? '' : 's' }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════
         PUBLIC AVATARS
         ══════════════════════════════════════════ --}}
    <div class="section-bar" style="margin-top: 36px;">
        <div class="section-title"><span class="dot"></span> Public Avatars</div>
        <div class="d-flex align-items-center gap-10" style="gap:10px;">
            <span class="section-count">{{ count($publicAvatars) }} avatar{{ count($publicAvatars) !== 1 ? 's' : '' }}</span>
            <div class="view-toggle">
                <button type="button" class="view-toggle-btn active" data-view="grid" title="Grid view">▦</button>
                <button type="button" class="view-toggle-btn" data-view="list" title="Large tiles">▣</button>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="av-filters">
        <span class="filter-chip active" data-filter="all">All</span>
        <span class="filter-chip" data-filter="professional">Professional</span>
        <span class="filter-chip" data-filter="lifestyle">Lifestyle</span>
        <span class="filter-chip" data-filter="ugc">UGC</span>
        <span class="filter-chip" data-filter="ai-generated">AI-Generated</span>
        <span class="filter-chip" data-filter="community">Community</span>
        <span class="filter-chip" data-filter="favorites">★ Favorites</span>

        <div class="av-search-wrap">
            <span class="av-search-icon">🔍</span>
            <form method="GET" action="{{ route('avatar.index') }}" id="avatarSearchForm">
                <input type="text" name="q" class="av-search" placeholder="Search avatars..." value="{{ $q }}" id="avatarSearch">
            </form>
        </div>
    </div>

    {{-- Public Grid --}}
    <div class="av-grid public-grid" id="publicGrid">
        @forelse($publicAvatars as $av)
        <div class="av-card landscape"
             data-id="{{ $av['id'] ?? '' }}"
             data-name="{{ $av['name'] }}"
             data-image="{{ $av['image'] ?? '' }}"
             data-tags="{{ implode(',', $av['tags'] ?? []) }}"
             data-type="public">
            <img src="{{ $av['image'] ?? 'https://placehold.co/640x400/16161d/555670?text=Avatar' }}"
                 class="av-card-img"
                 alt="{{ $av['name'] }}"
                 loading="lazy">
            <button type="button" class="av-bookmark {{ !empty($av['bookmarked']) ? 'saved' : '' }}" data-id="{{ $av['id'] ?? '' }}" title="Bookmark">
                {{ !empty($av['bookmarked']) ? '★' : '☆' }}
            </button>
            <div class="av-card-overlay">
                <div class="overlay-actions">
                    <button type="button" class="overlay-btn primary btn-use-in-studio" data-id="{{ $av['id'] ?? '' }}">⚡ Use in Studio</button>
                    <button type="button" class="overlay-btn ghost btn-view-detail" data-id="{{ $av['id'] ?? '' }}">Details</button>
                </div>
            </div>
            <div class="av-card-info">
                <div class="av-card-name">{{ $av['name'] }}</div>
                @if(!empty($av['tags']))
                <div class="av-card-tags">
                    @foreach(array_slice($av['tags'], 0, 3) as $tag)
                    <span class="av-tag">{{ $tag }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="av-empty">
            <div class="empty-icon">🔍</div>
            <h3>No avatars found</h3>
            <p>Try adjusting your search or filters</p>
        </div>
        @endforelse
    </div>

</div>

{{-- ── Avatar Detail Modal ── --}}
<div class="modal fade modal-dark" id="avatarDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalTitle">Avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="detail-layout">
                    <div class="detail-img-wrap">
                        <img id="detailImage" src="" alt="">
                    </div>
                    <div class="detail-info">
                        <div>
                            <div class="detail-name" id="detailName">—</div>
                            <div class="detail-meta-row" id="detailMeta" style="margin-top: 10px;"></div>
                        </div>

                        <div>
                            <div class="detail-section-label">Available Looks</div>
                            <div class="detail-looks-grid" id="detailLooks">
                                <div style="grid-column: 1/-1; color: var(--av-text-muted); font-size: 0.82rem;">No additional looks</div>
                            </div>
                        </div>

                        <div class="detail-actions">
                            <button type="button" class="btn-use-avatar" id="detailUseBtn">⚡ Use in Studio</button>
                            <button type="button" class="btn-detail-ghost" data-bs-dismiss="modal">Close</button>
                        </div>
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

    // ── Filter Chips ──
    const chips = document.querySelectorAll('.filter-chip');
    const publicCards = document.querySelectorAll('.av-grid.public-grid .av-card');

    chips.forEach(chip => {
        chip.addEventListener('click', function () {
            chips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;

            publicCards.forEach(card => {
                const tags = (card.dataset.tags || '').toLowerCase();
                const isFav = card.querySelector('.av-bookmark.saved');

                if (filter === 'all') {
                    card.style.display = '';
                } else if (filter === 'favorites') {
                    card.style.display = isFav ? '' : 'none';
                } else {
                    card.style.display = tags.includes(filter) ? '' : 'none';
                }
            });
        });
    });

    // ── Search (live filter) ──
    const searchInput = document.getElementById('avatarSearch');
    const searchForm = document.getElementById('avatarSearchForm');

    // Prevent form submission for live filtering; keep it for fallback
    searchInput?.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        publicCards.forEach(card => {
            const name = (card.dataset.name || '').toLowerCase();
            const tags = (card.dataset.tags || '').toLowerCase();
            card.style.display = (!q || name.includes(q) || tags.includes(q)) ? '' : 'none';
        });
    });

    // ── View Toggle ──
    const toggleBtns = document.querySelectorAll('.view-toggle-btn');
    const publicGrid = document.getElementById('publicGrid');

    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            toggleBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            if (this.dataset.view === 'list') {
                publicGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
                publicGrid.querySelectorAll('.av-card').forEach(c => c.classList.add('landscape'));
            } else {
                publicGrid.style.gridTemplateColumns = '';
                publicGrid.querySelectorAll('.av-card').forEach(c => c.classList.remove('landscape'));
            }
        });
    });

    // ── Bookmark ──
    document.querySelectorAll('.av-bookmark').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            this.classList.toggle('saved');
            this.textContent = this.classList.contains('saved') ? '★' : '☆';
            // TODO: AJAX call to save bookmark state
        });
    });

    // ── Detail Modal ──
    const detailModal = document.getElementById('avatarDetailModal');
    const bsDetailModal = typeof bootstrap !== 'undefined' ? new bootstrap.Modal(detailModal) : null;

    document.querySelectorAll('.btn-view-detail').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const card = this.closest('.av-card');
            if (!card) return;

            const name = card.dataset.name || '—';
            const image = card.dataset.image || '';
            const tags = (card.dataset.tags || '').split(',').filter(Boolean);
            const looks = parseInt(card.dataset.looks || '0');

            document.getElementById('detailModalTitle').textContent = name;
            document.getElementById('detailName').textContent = name;
            document.getElementById('detailImage').src = image || 'https://placehold.co/600x600/16161d/555670?text=Avatar';

            // Meta badges
            const metaWrap = document.getElementById('detailMeta');
            metaWrap.innerHTML = '';
            if (card.dataset.type === 'my') {
                metaWrap.innerHTML += `<span class="detail-badge gender">My Avatar</span>`;
                metaWrap.innerHTML += `<span class="detail-badge style">${looks} look${looks !== 1 ? 's' : ''}</span>`;
            }
            tags.forEach(t => {
                metaWrap.innerHTML += `<span class="detail-badge type">${t.trim()}</span>`;
            });

            // Looks grid (placeholder)
            const looksGrid = document.getElementById('detailLooks');
            if (looks > 0) {
                looksGrid.innerHTML = '';
                for (let i = 0; i < Math.min(looks, 6); i++) {
                    looksGrid.innerHTML += `
                        <div class="detail-look-thumb">
                            <img src="${image || 'https://placehold.co/200x200/16161d/555670?text=Look'}" alt="Look ${i + 1}">
                        </div>`;
                }
            } else {
                looksGrid.innerHTML = '<div style="grid-column:1/-1;color:var(--av-text-muted);font-size:0.82rem;">No additional looks available</div>';
            }

            if (bsDetailModal) bsDetailModal.show();
        });
    });

    // ── Use in Studio ──
    document.querySelectorAll('.btn-use-in-studio').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const card = this.closest('.av-card');
            const id = card?.dataset.id || this.dataset.id;
            // TODO: redirect or pass avatar to studio
            window.location.href = `{{ route('text_to_speech.index') }}?avatar_id=${id}`;
        });
    });

    document.getElementById('detailUseBtn')?.addEventListener('click', function () {
        if (bsDetailModal) bsDetailModal.hide();
        // TODO: redirect with avatar context
        window.location.href = `{{ route('text_to_speech.index') }}`;
    });

    // ── Card click → detail ──
    document.querySelectorAll('.av-card').forEach(card => {
        card.addEventListener('click', function (e) {
            if (e.target.closest('.av-bookmark') || e.target.closest('.overlay-btn')) return;
            const detailBtn = this.querySelector('.btn-view-detail');
            if (detailBtn) detailBtn.click();
        });
    });

});
</script>
@endpush