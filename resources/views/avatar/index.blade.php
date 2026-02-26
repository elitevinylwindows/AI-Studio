@extends('layouts.app')

@section('page-title', 'Avatar')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Avatars</li>
  </ol>
</nav>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
    .av {
        font-family: 'DM Sans', sans-serif;
    }

    /* ── Header ── */
    .av-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .av-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin: 0 0 2px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #1a1a2e;
    }

    .av-header h1 .ring {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        box-shadow: 0 3px 12px rgba(108, 92, 231, 0.25);
    }

    .av-header .sub {
        font-size: 0.84rem;
        color: #6b7280;
        margin-left: 46px;
    }

    .av-stats {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .av-stat-box {
        background: #f8f9fc;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 8px 16px;
        text-align: center;
        min-width: 72px;
    }

    .av-stat-box .n {
        font-family: 'Space Mono', monospace;
        font-size: 1.1rem;
        font-weight: 700;
        color: #6c5ce7;
    }

    .av-stat-box .l {
        font-size: 0.64rem;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .btn-get-more {
        background: linear-gradient(135deg, #e84393, #fd79a8);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 9px 20px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
        box-shadow: 0 3px 12px rgba(232, 67, 147, 0.2);
    }

    .btn-get-more:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 18px rgba(232, 67, 147, 0.3);
        color: #fff;
    }

    /* ── Section label ── */
    .av-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .av-section-title {
        font-family: 'Space Mono', monospace;
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #9ca3af;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .av-section-title .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #6c5ce7;
    }

    .av-section-count {
        font-family: 'Space Mono', monospace;
        font-size: 0.7rem;
        color: #9ca3af;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 3px 10px;
    }

    /* ── Card grid ── */
    .av-grid {
        display: grid;
        gap: 14px;
        margin-bottom: 32px;
    }

    .av-grid.my-grid {
        grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
    }

    .av-grid.pub-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }

    /* ── Create card ── */
    .av-create {
        background: #fff;
        border: 2px dashed #d1d5db;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 28px 16px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        min-height: 220px;
    }

    .av-create:hover {
        border-color: #6c5ce7;
        background: #faf8ff;
        transform: translateY(-2px);
    }

    .av-create .ci {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #fff;
        margin-bottom: 12px;
        box-shadow: 0 4px 16px rgba(108, 92, 231, 0.25);
        transition: transform 0.15s;
    }

    .av-create:hover .ci {
        transform: scale(1.08);
    }

    .av-create .ct {
        font-weight: 600;
        font-size: 0.88rem;
        color: #1f2937;
        margin-bottom: 3px;
    }

    .av-create .cs {
        font-size: 0.72rem;
        color: #9ca3af;
        text-align: center;
    }

    /* ── Avatar card ── */
    .av-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }

    .av-card:hover {
        border-color: #c4b5fd;
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .av-card-img {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        display: block;
        background: #f3f4f6;
    }

    .av-card.landscape .av-card-img {
        aspect-ratio: 16 / 10;
    }

    /* Hover overlay */
    .av-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(0deg, rgba(17, 17, 27, 0.8) 0%, transparent 50%);
        opacity: 0;
        transition: opacity 0.2s;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 14px;
    }

    .av-card:hover .av-card-overlay {
        opacity: 1;
    }

    .av-card-overlay .ov-actions {
        display: flex;
        gap: 6px;
    }

    .ov-btn {
        padding: 6px 14px;
        border-radius: 7px;
        border: none;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.12s;
    }

    .ov-btn.primary {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        color: #fff;
        box-shadow: 0 2px 8px rgba(108, 92, 231, 0.3);
    }

    .ov-btn.ghost {
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
        backdrop-filter: blur(4px);
    }

    /* Bookmark */
    .av-bm {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(0, 0, 0, 0.06);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        color: #9ca3af;
        z-index: 2;
        transition: all 0.15s;
    }

    .av-bm:hover {
        color: #f59e0b;
        border-color: #f59e0b;
    }

    .av-bm.saved {
        background: #fef3c7;
        border-color: #f59e0b;
        color: #f59e0b;
    }

    /* Card info */
    .av-card-info {
        padding: 10px 12px;
    }

    .av-card-name {
        font-weight: 600;
        font-size: 0.84rem;
        color: #1f2937;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .av-card-meta {
        font-size: 0.7rem;
        color: #9ca3af;
    }

    .av-looks {
        font-family: 'Space Mono', monospace;
        font-size: 0.64rem;
        padding: 2px 8px;
        border-radius: 5px;
        background: #ede9fe;
        color: #6c5ce7;
        border: 1px solid #ddd6fe;
    }

    .av-tags {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        margin-top: 5px;
    }

    .av-tag {
        font-size: 0.64rem;
        padding: 2px 7px;
        border-radius: 4px;
        background: #f3f4f6;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }

    /* ── Filters ── */
    .av-filters {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .av-chip {
        padding: 6px 16px;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        font-size: 0.78rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
    }

    .av-chip:hover {
        border-color: #6c5ce7;
        color: #6c5ce7;
    }

    .av-chip.active {
        background: #6c5ce7;
        border-color: #6c5ce7;
        color: #fff;
    }

    .av-search {
        flex: 1;
        min-width: 180px;
        max-width: 320px;
    }

    .av-search input {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 7px 12px 7px 34px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        outline: none;
        transition: all 0.15s;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%239ca3af' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 10px center;
    }

    .av-search input:focus {
        border-color: #6c5ce7;
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.12);
    }

    /* View toggle */
    .av-views {
        display: inline-flex;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    .av-views button {
        padding: 6px 12px;
        border: none;
        background: #fff;
        color: #9ca3af;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.12s;
    }

    .av-views button.active {
        background: #6c5ce7;
        color: #fff;
    }

    /* ── Empty state ── */
    .av-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 50px 20px;
    }

    .av-empty .ei {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #f3f4f6;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 12px;
    }

    .av-empty h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
    }

    .av-empty p {
        font-size: 0.84rem;
        color: #9ca3af;
    }

    /* ── Card animation ── */
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .av-card, .av-create {
        animation: cardIn 0.3s ease forwards;
        opacity: 0;
    }

    .av-grid > :nth-child(1) { animation-delay: 0s; }
    .av-grid > :nth-child(2) { animation-delay: 0.03s; }
    .av-grid > :nth-child(3) { animation-delay: 0.06s; }
    .av-grid > :nth-child(4) { animation-delay: 0.09s; }
    .av-grid > :nth-child(5) { animation-delay: 0.12s; }
    .av-grid > :nth-child(6) { animation-delay: 0.15s; }
    .av-grid > :nth-child(7) { animation-delay: 0.18s; }
    .av-grid > :nth-child(8) { animation-delay: 0.21s; }
</style>
@endpush

@section('content')
<div class="av">

    {{-- Header --}}
    <div class="av-header">
        <div>
            <h1><span class="ring">👤</span> Avatars</h1>
            <div class="sub">Create, browse, and manage your AI presenters</div>
        </div>
        <div class="av-stats">
            <div class="av-stat-box">
                <div class="n">{{ count($myAvatars) }}</div>
                <div class="l">Mine</div>
            </div>
            <div class="av-stat-box">
                <div class="n">{{ count($publicAvatars) }}</div>
                <div class="l">Public</div>
            </div>
            <a href="#" class="btn-get-more">✦ Get More Avatars</a>
        </div>
    </div>

    {{-- ═══ MY AVATARS ═══ --}}
    <div class="av-section">
        <div class="av-section-title"><span class="dot"></span> My Avatars</div>
        <span class="av-section-count">{{ count($myAvatars) }} avatar{{ count($myAvatars) !== 1 ? 's' : '' }}</span>
    </div>

    <div class="av-grid my-grid">
        <a href="#" class="av-create" id="createBtn">
            <div class="ci">+</div>
            <div class="ct">Create Avatar</div>
            <div class="cs">Upload a photo or generate with AI</div>
        </a>

        @foreach($myAvatars as $av)
        <div class="av-card"
             data-id="{{ $av['id'] ?? '' }}"
             data-name="{{ $av['name'] }}"
             data-image="{{ $av['image'] ?? '' }}"
             data-looks="{{ $av['looks'] ?? 0 }}">
            <img src="{{ $av['image'] ?? 'https://placehold.co/400x400?text=Avatar' }}"
                 class="av-card-img" alt="{{ $av['name'] }}" loading="lazy">
            <button type="button" class="av-bm {{ !empty($av['bookmarked']) ? 'saved' : '' }}">
                {{ !empty($av['bookmarked']) ? '★' : '☆' }}
            </button>
            <div class="av-card-overlay">
                <div class="ov-actions">
                    <button type="button" class="ov-btn primary btn-use" data-id="{{ $av['id'] ?? '' }}">⚡ Use</button>
                    <button type="button" class="ov-btn ghost btn-detail" data-id="{{ $av['id'] ?? '' }}">Details</button>
                </div>
            </div>
            <div class="av-card-info">
                <div class="av-card-name">{{ $av['name'] }}</div>
                <div class="av-card-meta">
                    <span class="av-looks">{{ $av['looks'] ?? 0 }} look{{ ($av['looks'] ?? 0) === 1 ? '' : 's' }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══ PUBLIC AVATARS ═══ --}}
    <div class="av-section">
        <div class="av-section-title"><span class="dot"></span> Public Avatars</div>
        <div style="display:flex;align-items:center;gap:8px;">
            <span class="av-section-count">{{ count($publicAvatars) }} avatar{{ count($publicAvatars) !== 1 ? 's' : '' }}</span>
            <div class="av-views">
                <button class="active" data-view="grid" title="Grid">▦</button>
                <button data-view="large" title="Large">▣</button>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="av-filters">
        <span class="av-chip active" data-filter="all">All</span>
        <span class="av-chip" data-filter="professional">Professional</span>
        <span class="av-chip" data-filter="lifestyle">Lifestyle</span>
        <span class="av-chip" data-filter="ugc">UGC</span>
        <span class="av-chip" data-filter="ai-generated">AI-Generated</span>
        <span class="av-chip" data-filter="community">Community</span>
        <span class="av-chip" data-filter="favorites">★ Favorites</span>
        <div class="av-search">
            <input type="text" placeholder="Search avatars..." value="{{ $q }}" id="avSearch">
        </div>
    </div>

    {{-- Public grid --}}
    <div class="av-grid pub-grid" id="pubGrid">
        @forelse($publicAvatars as $av)
        <div class="av-card landscape"
             data-id="{{ $av['id'] ?? '' }}"
             data-name="{{ $av['name'] }}"
             data-image="{{ $av['image'] ?? '' }}"
             data-tags="{{ implode(',', $av['tags'] ?? []) }}">
            <img src="{{ $av['image'] ?? 'https://placehold.co/640x400?text=Avatar' }}"
                 class="av-card-img" alt="{{ $av['name'] }}" loading="lazy">
            <button type="button" class="av-bm {{ !empty($av['bookmarked']) ? 'saved' : '' }}">
                {{ !empty($av['bookmarked']) ? '★' : '☆' }}
            </button>
            <div class="av-card-overlay">
                <div class="ov-actions">
                    <button type="button" class="ov-btn primary btn-use" data-id="{{ $av['id'] ?? '' }}">⚡ Use in Studio</button>
                    <button type="button" class="ov-btn ghost btn-detail" data-id="{{ $av['id'] ?? '' }}">Details</button>
                </div>
            </div>
            <div class="av-card-info">
                <div class="av-card-name">{{ $av['name'] }}</div>
                @if(!empty($av['tags']))
                <div class="av-tags">
                    @foreach(array_slice($av['tags'], 0, 3) as $tag)
                    <span class="av-tag">{{ $tag }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="av-empty">
            <div class="ei">🔍</div>
            <h3>No avatars found</h3>
            <p>Try adjusting your search or filters</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const pubCards = document.querySelectorAll('#pubGrid .av-card');

    // Filter chips
    document.querySelectorAll('.av-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.av-chip').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const f = this.dataset.filter;
            pubCards.forEach(card => {
                const tags = (card.dataset.tags || '').toLowerCase();
                const isFav = card.querySelector('.av-bm.saved');
                if (f === 'all') card.style.display = '';
                else if (f === 'favorites') card.style.display = isFav ? '' : 'none';
                else card.style.display = tags.includes(f) ? '' : 'none';
            });
        });
    });

    // Live search
    document.getElementById('avSearch')?.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        pubCards.forEach(card => {
            const name = (card.dataset.name || '').toLowerCase();
            const tags = (card.dataset.tags || '').toLowerCase();
            card.style.display = (!q || name.includes(q) || tags.includes(q)) ? '' : 'none';
        });
    });

    // View toggle
    const pubGrid = document.getElementById('pubGrid');
    document.querySelectorAll('.av-views button').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.av-views button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            if (this.dataset.view === 'large') {
                pubGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
            } else {
                pubGrid.style.gridTemplateColumns = '';
            }
        });
    });

    // Bookmark toggle
    document.querySelectorAll('.av-bm').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            this.classList.toggle('saved');
            this.textContent = this.classList.contains('saved') ? '★' : '☆';
        });
    });

    // Use in Studio
    document.querySelectorAll('.btn-use').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const id = this.dataset.id;
            window.location.href = `{{ route('text_to_speech.index') }}?avatar_id=${id}`;
        });
    });

    // Card click opens detail (placeholder)
    document.querySelectorAll('.av-card').forEach(card => {
        card.addEventListener('click', function (e) {
            if (e.target.closest('.av-bm') || e.target.closest('.ov-btn')) return;
            const detail = this.querySelector('.btn-detail');
            if (detail) {
                // TODO: open detail modal
                alert('Avatar detail: ' + this.dataset.name);
            }
        });
    });
});
</script>
@endpush