@extends('layouts.app')

@section('page-title', 'Voice Bank')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Voice Bank</li>
  </ol>
</nav>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.0.7/datatables.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Space+Mono:wght@400;700&display=swap');

    :root {
        --vb-bg: #0c0c10;
        --vb-panel: #16161d;
        --vb-border: #232330;
        --vb-hover: #1e1e2a;
        --vb-accent: #6c5ce7;
        --vb-accent-glow: rgba(108, 92, 231, 0.25);
        --vb-accent-light: #a29bfe;
        --vb-success: #00cec9;
        --vb-success-glow: rgba(0, 206, 201, 0.2);
        --vb-danger: #ff6b6b;
        --vb-warning: #feca57;
        --vb-text: #eef0f6;
        --vb-text-sec: #8b8da3;
        --vb-text-muted: #555670;
        --vb-male: linear-gradient(135deg, #0984e3, #6c5ce7);
        --vb-female: linear-gradient(135deg, #e84393, #fd79a8);
        --vb-neutral: linear-gradient(135deg, #636e72, #b2bec3);
    }

    .voice-bank-page {
        font-family: 'DM Sans', sans-serif;
        background: var(--vb-bg);
        min-height: 100vh;
        color: var(--vb-text);
        padding: 0 0 60px 0;
    }

    /* ── Page Header ── */
    .vb-header {
        padding: 28px 32px 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .vb-header-left h1 {
        font-size: 1.6rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .vb-header-left h1 .icon-ring {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 20px var(--vb-accent-glow);
    }

    .vb-header-left .subtitle {
        font-size: 0.85rem;
        color: var(--vb-text-sec);
        margin-left: 52px;
    }

    .vb-header-right {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .vb-stat {
        background: var(--vb-panel);
        border: 1px solid var(--vb-border);
        border-radius: 12px;
        padding: 10px 18px;
        text-align: center;
        min-width: 90px;
    }

    .vb-stat .num {
        font-family: 'Space Mono', monospace;
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--vb-accent-light);
    }

    .vb-stat .label {
        font-size: 0.68rem;
        color: var(--vb-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .btn-sync {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
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
        box-shadow: 0 4px 20px var(--vb-accent-glow);
    }

    .btn-sync:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 28px var(--vb-accent-glow);
    }

    /* ── Filter Bar ── */
    .vb-filters {
        margin: 24px 32px 0;
        background: var(--vb-panel);
        border: 1px solid var(--vb-border);
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 140px;
    }

    .filter-label {
        display: block;
        font-family: 'Space Mono', monospace;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--vb-text-muted);
        margin-bottom: 6px;
    }

    .filter-select {
        width: 100%;
        background: var(--vb-bg);
        border: 1px solid var(--vb-border);
        border-radius: 10px;
        padding: 9px 32px 9px 12px;
        color: var(--vb-text);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%238b8da3' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }

    .filter-select:focus {
        border-color: var(--vb-accent);
        box-shadow: 0 0 0 3px var(--vb-accent-glow);
    }

    .filter-select option {
        background: var(--vb-panel);
        color: var(--vb-text);
    }

    .filter-search {
        width: 100%;
        background: var(--vb-bg);
        border: 1px solid var(--vb-border);
        border-radius: 10px;
        padding: 9px 12px 9px 36px;
        color: var(--vb-text);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        outline: none;
        transition: all 0.2s;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23555670' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 12px center;
    }

    .filter-search:focus {
        border-color: var(--vb-accent);
        box-shadow: 0 0 0 3px var(--vb-accent-glow);
    }

    .filter-clear {
        background: var(--vb-bg);
        border: 1px solid var(--vb-border);
        border-radius: 10px;
        padding: 9px 16px;
        color: var(--vb-text-sec);
        font-size: 0.82rem;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        font-family: 'DM Sans', sans-serif;
    }

    .filter-clear:hover {
        border-color: var(--vb-danger);
        color: var(--vb-danger);
    }

    /* ── Active Filters Pills ── */
    .active-filters {
        margin: 12px 32px 0;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        min-height: 0;
    }

    .active-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(108, 92, 231, 0.12);
        border: 1px solid rgba(108, 92, 231, 0.3);
        border-radius: 8px;
        padding: 4px 12px;
        font-size: 0.76rem;
        color: var(--vb-accent-light);
        animation: fadeIn 0.2s ease;
    }

    .active-filter-pill .remove {
        cursor: pointer;
        opacity: 0.6;
        font-size: 14px;
        line-height: 1;
    }

    .active-filter-pill .remove:hover {
        opacity: 1;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    /* ── Voice Table ── */
    .vb-table-wrap {
        margin: 20px 32px 0;
        background: var(--vb-panel);
        border: 1px solid var(--vb-border);
        border-radius: 16px;
        overflow: hidden;
    }

    /* DataTables Overrides */
    .vb-table-wrap .dataTables_wrapper {
        padding: 0;
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_length,
    .vb-table-wrap .dataTables_wrapper .dataTables_filter {
        display: none;
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_info {
        color: var(--vb-text-muted);
        font-size: 0.78rem;
        padding: 12px 24px;
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_paginate {
        padding: 12px 24px;
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: var(--vb-bg) !important;
        border: 1px solid var(--vb-border) !important;
        border-radius: 8px !important;
        color: var(--vb-text-sec) !important;
        font-size: 0.8rem;
        padding: 6px 12px !important;
        margin: 0 2px;
        transition: all 0.15s;
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--vb-hover) !important;
        border-color: var(--vb-accent) !important;
        color: var(--vb-text) !important;
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--vb-accent) !important;
        border-color: var(--vb-accent) !important;
        color: #fff !important;
        box-shadow: 0 2px 12px var(--vb-accent-glow);
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.3;
    }

    #voicesTable {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
    }

    #voicesTable thead th {
        background: var(--vb-bg);
        color: var(--vb-text-muted);
        font-family: 'Space Mono', monospace;
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 14px 16px;
        border-bottom: 1px solid var(--vb-border);
        font-weight: 400;
        white-space: nowrap;
    }

    #voicesTable thead th:first-child { padding-left: 24px; }
    #voicesTable thead th:last-child { padding-right: 24px; }

    #voicesTable tbody tr {
        transition: background 0.15s;
    }

    #voicesTable tbody tr:hover {
        background: var(--vb-hover);
    }

    #voicesTable tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(35, 35, 48, 0.5);
        color: var(--vb-text);
        font-size: 0.86rem;
        vertical-align: middle;
    }

    #voicesTable tbody td:first-child { padding-left: 24px; }
    #voicesTable tbody td:last-child { padding-right: 24px; }

    #voicesTable tbody tr:last-child td {
        border-bottom: none;
    }

    #voicesTable tbody tr.playing-row {
        background: rgba(108, 92, 231, 0.06);
    }

    /* ── Vendor Badge ── */
    .vendor-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--vb-bg);
        border: 1px solid var(--vb-border);
        border-radius: 8px;
        padding: 5px 12px 5px 8px;
        font-size: 0.78rem;
        font-weight: 500;
    }

    .vendor-badge img {
        width: 16px;
        height: 16px;
        border-radius: 3px;
    }

    /* ── Gender Indicator ── */
    .gender-dot {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.84rem;
    }

    .gender-dot::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .gender-dot.male::before { background: #0984e3; box-shadow: 0 0 6px rgba(9, 132, 227, 0.4); }
    .gender-dot.female::before { background: #e84393; box-shadow: 0 0 6px rgba(232, 67, 147, 0.4); }
    .gender-dot.neutral::before { background: #636e72; }

    /* ── Voice Name Cell ── */
    .voice-name-cell {
        font-weight: 600;
        color: var(--vb-text);
    }

    .voice-name-cell .voice-id-sub {
        display: block;
        font-size: 0.7rem;
        font-weight: 400;
        color: var(--vb-text-muted);
        font-family: 'Space Mono', monospace;
        margin-top: 2px;
    }

    /* ── Language Cell ── */
    .lang-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .lang-flag {
        font-size: 1.1rem;
        line-height: 1;
    }

    .lang-info .lang-name {
        font-weight: 500;
        font-size: 0.84rem;
    }

    .lang-info .lang-code {
        font-family: 'Space Mono', monospace;
        font-size: 0.7rem;
        color: var(--vb-text-muted);
    }

    /* ── Format Badge ── */
    .format-badge {
        font-family: 'Space Mono', monospace;
        font-size: 0.68rem;
        padding: 4px 10px;
        border-radius: 6px;
        background: var(--vb-bg);
        border: 1px solid var(--vb-border);
        color: var(--vb-text-sec);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* ── Preview Button ── */
    .preview-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid var(--vb-border);
        background: var(--vb-bg);
        color: var(--vb-text-sec);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .preview-btn:hover {
        border-color: var(--vb-accent);
        color: var(--vb-accent);
        background: var(--vb-accent-glow);
    }

    .preview-btn.playing {
        background: var(--vb-accent);
        border-color: var(--vb-accent);
        color: #fff;
        box-shadow: 0 0 16px var(--vb-accent-glow);
        animation: pulseBtn 1.5s infinite;
    }

    @keyframes pulseBtn {
        0%, 100% { box-shadow: 0 0 16px var(--vb-accent-glow); }
        50% { box-shadow: 0 0 24px rgba(108, 92, 231, 0.4); }
    }

    /* ── Action Buttons ── */
    .action-btn-sm {
        background: var(--vb-bg);
        border: 1px solid var(--vb-border);
        border-radius: 8px;
        padding: 6px 14px;
        color: var(--vb-text-sec);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .action-btn-sm:hover {
        border-color: var(--vb-accent);
        color: var(--vb-accent-light);
        background: var(--vb-hover);
    }

    /* ── Inline Player Bar ── */
    .inline-player {
        margin: 0 32px;
        background: var(--vb-panel);
        border: 1px solid var(--vb-border);
        border-radius: 0 0 16px 16px;
        border-top: none;
        padding: 14px 24px;
        display: none;
        align-items: center;
        gap: 16px;
        animation: slideDown 0.25s ease;
    }

    .inline-player.visible {
        display: flex;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .inline-player .player-info {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .inline-player .player-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #fff;
    }

    .inline-player .player-name {
        font-weight: 600;
        font-size: 0.84rem;
    }

    .inline-player .player-meta {
        font-size: 0.72rem;
        color: var(--vb-text-muted);
    }

    .inline-player .player-progress {
        flex: 1;
        height: 4px;
        background: var(--vb-border);
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
    }

    .inline-player .player-progress-fill {
        height: 100%;
        background: var(--vb-accent);
        border-radius: 4px;
        width: 0%;
        transition: width 0.1s linear;
    }

    .inline-player .player-time {
        font-family: 'Space Mono', monospace;
        font-size: 0.72rem;
        color: var(--vb-text-muted);
        white-space: nowrap;
    }

    .inline-player .player-close {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid var(--vb-border);
        background: none;
        color: var(--vb-text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.15s;
    }

    .inline-player .player-close:hover {
        border-color: var(--vb-danger);
        color: var(--vb-danger);
    }

    /* ── Edit Modal (Dark) ── */
    .modal-dark .modal-content {
        background: var(--vb-panel);
        border: 1px solid var(--vb-border);
        border-radius: 16px;
        color: var(--vb-text);
    }

    .modal-dark .modal-header {
        border-bottom: 1px solid var(--vb-border);
        padding: 20px 24px;
    }

    .modal-dark .modal-title {
        font-weight: 700;
        font-size: 1.1rem;
    }

    .modal-dark .btn-close {
        filter: invert(1) brightness(0.7);
    }

    .modal-dark .modal-body {
        padding: 24px;
    }

    .modal-dark .modal-footer {
        border-top: 1px solid var(--vb-border);
        padding: 16px 24px;
    }

    .modal-dark .form-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--vb-text-sec);
        margin-bottom: 6px;
    }

    .modal-dark .form-control,
    .modal-dark .form-select {
        background: var(--vb-bg);
        border: 1px solid var(--vb-border);
        border-radius: 10px;
        color: var(--vb-text);
        padding: 10px 14px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
    }

    .modal-dark .form-control:focus,
    .modal-dark .form-select:focus {
        border-color: var(--vb-accent);
        box-shadow: 0 0 0 3px var(--vb-accent-glow);
    }

    .modal-dark .form-text {
        color: var(--vb-text-muted);
        font-size: 0.75rem;
    }

    .modal-dark .btn-modal-cancel {
        background: var(--vb-bg);
        border: 1px solid var(--vb-border);
        color: var(--vb-text-sec);
        border-radius: 10px;
        padding: 9px 20px;
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
    }

    .modal-dark .btn-modal-cancel:hover {
        border-color: var(--vb-text-muted);
        color: var(--vb-text);
    }

    .modal-dark .btn-modal-save {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        border: none;
        color: #fff;
        border-radius: 10px;
        padding: 9px 24px;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        box-shadow: 0 4px 16px var(--vb-accent-glow);
    }

    .modal-dark .btn-modal-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 24px var(--vb-accent-glow);
    }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--vb-border); border-radius: 4px; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .vb-header, .vb-filters, .vb-table-wrap, .inline-player, .active-filters {
            margin-left: 12px;
            margin-right: 12px;
        }
        .vb-filters { flex-direction: column; }
        .filter-group { min-width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="voice-bank-page">

    {{-- ── Header ── --}}
    <div class="vb-header">
        <div class="vb-header-left">
            <h1>
                <span class="icon-ring">🎙</span>
                Voice Bank
            </h1>
            <div class="subtitle">Browse, preview, and manage all available TTS voices</div>
        </div>
        <div class="vb-header-right">
            <div class="vb-stat">
                <div class="num">{{ count($voices) }}</div>
                <div class="label">Voices</div>
            </div>
            <div class="vb-stat">
                <div class="num">{{ count($languages) }}</div>
                <div class="label">Languages</div>
            </div>
            <form method="POST" action="{{ route('voices.sync') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-sync">
                    ↻ Sync Voices
                </button>
            </form>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="vb-filters">
        <div class="filter-group" style="flex: 1.5;">
            <label class="filter-label">Search</label>
            <input type="text" id="filterSearch" class="filter-search" placeholder="Search voices, languages...">
        </div>
        <div class="filter-group">
            <label class="filter-label">Vendor</label>
            <select id="filterVendor" class="filter-select">
                <option value="">All Vendors</option>
                @foreach($vendors as $v)
                <option value="{{ $v }}">{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Language</label>
            <select id="filterLanguage" class="filter-select">
                <option value="">All Languages</option>
                @foreach($languages as $l)
                <option value="{{ $l }}">{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Code</label>
            <select id="filterCode" class="filter-select" disabled>
                <option value="">All Codes</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Gender</label>
            <select id="filterGender" class="filter-select">
                <option value="">All</option>
                @foreach($genders as $g)
                @if($g)
                <option value="{{ $g }}">{{ ucfirst(strtolower($g)) }}</option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Format</label>
            <select id="filterFormat" class="filter-select">
                <option value="">All</option>
                @foreach($formats as $f)
                <option value="{{ $f }}">{{ strtoupper($f) }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="flex: 0 0 auto;">
            <label class="filter-label">&nbsp;</label>
            <button type="button" id="clearFilters" class="filter-clear">✕ Clear</button>
        </div>
    </div>

    {{-- Active Filter Pills --}}
    <div class="active-filters" id="activeFilters"></div>

    {{-- ── Table ── --}}
    <div class="vb-table-wrap">
        <div class="table-responsive">
            <table id="voicesTable" class="table table-hover align-middle w-100 mb-0">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Language</th>
                        <th>Gender</th>
                        <th>Voice</th>
                        <th style="width:70px;text-align:center;">Preview</th>
                        <th>Format</th>
                        <th style="width:80px;text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($voices as $v)
                    @php
                        $genderLower = strtolower($v->gender ?? 'neutral');
                        $langCode = $v->language_code ?? '';
                        $flagMap = [
                            'en-US' => '🇺🇸', 'en-GB' => '🇬🇧', 'en-AU' => '🇦🇺', 'en-IN' => '🇮🇳',
                            'es-MX' => '🇲🇽', 'es-ES' => '🇪🇸', 'fr-FR' => '🇫🇷', 'fr-CA' => '🇨🇦',
                            'de-DE' => '🇩🇪', 'pt-BR' => '🇧🇷', 'pt-PT' => '🇵🇹', 'ja-JP' => '🇯🇵',
                            'ko-KR' => '🇰🇷', 'zh-CN' => '🇨🇳', 'zh-TW' => '🇹🇼', 'hi-IN' => '🇮🇳',
                            'ar-XA' => '🇸🇦', 'it-IT' => '🇮🇹', 'nl-NL' => '🇳🇱', 'ru-RU' => '🇷🇺',
                            'pl-PL' => '🇵🇱', 'sv-SE' => '🇸🇪', 'da-DK' => '🇩🇰', 'nb-NO' => '🇳🇴',
                            'fi-FI' => '🇫🇮', 'tr-TR' => '🇹🇷', 'th-TH' => '🇹🇭', 'vi-VN' => '🇻🇳',
                            'id-ID' => '🇮🇩', 'ms-MY' => '🇲🇾', 'fil-PH' => '🇵🇭', 'uk-UA' => '🇺🇦',
                            'cs-CZ' => '🇨🇿', 'el-GR' => '🇬🇷', 'hu-HU' => '🇭🇺', 'ro-RO' => '🇷🇴',
                            'sk-SK' => '🇸🇰', 'bg-BG' => '🇧🇬', 'ca-ES' => '🇪🇸', 'sr-RS' => '🇷🇸',
                            'hr-HR' => '🇭🇷', 'he-IL' => '🇮🇱', 'af-ZA' => '🇿🇦',
                        ];
                        $flag = $flagMap[$langCode] ?? '🌐';
                    @endphp
                    <tr data-id="{{ $v->id }}"
                        data-text="{{ $v->voice_text ?? '' }}"
                        data-format="{{ $v->audio_format ?? 'mp3' }}"
                        data-gender="{{ $genderLower }}">
                        <td>
                            <span class="vendor-badge">
                                <img src="{{ asset('public/icons/google.png') }}" alt="Google">
                                {{ $v->vendor }}
                            </span>
                        </td>
                        <td>
                            <div class="lang-cell">
                                <span class="lang-flag">{{ $flag }}</span>
                                <div class="lang-info">
                                    <div class="lang-name">{{ $v->language_full }}</div>
                                    <div class="lang-code">{{ $v->language_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="gender-dot {{ $genderLower }}">{{ ucfirst($genderLower) }}</span>
                        </td>
                        <td>
                            <div class="voice-name-cell">
                                {{ $v->voice_text ?: $v->voice_id ?? '—' }}
                                @if($v->voice_id)
                                <span class="voice-id-sub">{{ $v->voice_id }}</span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="preview-btn btnPreview" title="Preview voice" aria-label="Preview">
                                ▶
                            </button>
                        </td>
                        <td>
                            <span class="format-badge">{{ strtoupper($v->audio_format ?? 'mp3') }}</span>
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="action-btn-sm btnEdit" title="Edit">
                                ✎
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Inline Player Bar ── --}}
    <div class="inline-player" id="inlinePlayer">
        <div class="player-info">
            <div class="player-avatar" id="playerAvatar" style="background: var(--vb-male);">🎤</div>
            <div>
                <div class="player-name" id="playerName">—</div>
                <div class="player-meta" id="playerMeta">—</div>
            </div>
        </div>
        <div class="player-progress" id="playerProgress">
            <div class="player-progress-fill" id="playerProgressFill"></div>
        </div>
        <div class="player-time">
            <span id="playerTime">0:00</span> / <span id="playerDuration">0:00</span>
        </div>
        <button type="button" class="player-close" id="playerClose" title="Close">✕</button>
    </div>

    {{-- Hidden Audio --}}
    <audio id="audioPlayer"></audio>
</div>

{{-- ── Edit Modal ── --}}
<div class="modal fade modal-dark" id="editVoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editVoiceForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">✎ Edit Voice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="voiceId">
                    <div class="mb-3">
                        <label class="form-label">Display Name / Sample Text</label>
                        <textarea class="form-control" id="voiceText" rows="3" placeholder="Type sample text for this voice..."></textarea>
                        <div class="form-text">This text and format will be saved on update.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Output Format</label>
                        <select id="audioFormat" class="form-select">
                            <option value="mp3">MP3</option>
                            <option value="ogg">OGG</option>
                            <option value="wav">WAV</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-save">Save Changes</button>
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

    // ── DataTable Init ──
    const table = new DataTable('#voicesTable', {
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        stateSave: true,
        language: {
            info: 'Showing _START_ – _END_ of _TOTAL_ voices',
            infoEmpty: 'No voices found',
            infoFiltered: '(filtered from _MAX_)',
            emptyTable: 'No voices available. Click "Sync Voices" to fetch from Google.',
            zeroRecords: 'No matching voices found',
        }
    });

    // ── Filter Elements ──
    const searchEl  = document.getElementById('filterSearch');
    const vendorEl  = document.getElementById('filterVendor');
    const langEl    = document.getElementById('filterLanguage');
    const codeEl    = document.getElementById('filterCode');
    const genderEl  = document.getElementById('filterGender');
    const formatEl  = document.getElementById('filterFormat');
    const clearBtn  = document.getElementById('clearFilters');
    const pillsWrap = document.getElementById('activeFilters');

    // ── Apply Filters ──
    function applyFilters() {
        table.search(searchEl.value || '');
        table.column(0).search(vendorEl.value || '', true, false);
        table.column(1).search(langEl.value || '', true, false);
        // Code is embedded in language column (lang-code span)
        if (codeEl.value) {
            table.column(1).search(codeEl.value, true, false);
        }
        table.column(2).search(genderEl.value || '', true, false);
        table.column(5).search((formatEl.value || '').toUpperCase(), true, false);
        table.draw();
        renderPills();
    }

    // ── Render Active Filter Pills ──
    function renderPills() {
        const filters = [];
        if (searchEl.value) filters.push({ key: 'search', label: `"${searchEl.value}"`, clear: () => { searchEl.value = ''; } });
        if (vendorEl.value) filters.push({ key: 'vendor', label: vendorEl.value, clear: () => { vendorEl.value = ''; } });
        if (langEl.value) filters.push({ key: 'language', label: langEl.value, clear: () => { langEl.value = ''; codeEl.innerHTML = '<option value="">All Codes</option>'; codeEl.disabled = true; } });
        if (codeEl.value) filters.push({ key: 'code', label: codeEl.value, clear: () => { codeEl.value = ''; } });
        if (genderEl.value) filters.push({ key: 'gender', label: genderEl.value, clear: () => { genderEl.value = ''; } });
        if (formatEl.value) filters.push({ key: 'format', label: formatEl.value.toUpperCase(), clear: () => { formatEl.value = ''; } });

        pillsWrap.innerHTML = '';
        filters.forEach(f => {
            const pill = document.createElement('span');
            pill.className = 'active-filter-pill';
            pill.innerHTML = `${f.label} <span class="remove" data-key="${f.key}">✕</span>`;
            pill.querySelector('.remove').addEventListener('click', () => {
                f.clear();
                applyFilters();
            });
            pillsWrap.appendChild(pill);
        });
    }

    // ── Bind Filter Events ──
    searchEl.addEventListener('input', applyFilters);
    [vendorEl, genderEl, formatEl].forEach(el => el.addEventListener('change', applyFilters));

    // Language → Code dependency
    langEl.addEventListener('change', async function () {
        const lang = this.value;
        codeEl.innerHTML = '<option value="">All Codes</option>';
        codeEl.disabled = true;

        if (lang) {
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
            }
        }
        applyFilters();
    });

    codeEl.addEventListener('change', applyFilters);

    // Clear all
    clearBtn.addEventListener('click', function () {
        searchEl.value = '';
        vendorEl.value = '';
        langEl.value = '';
        codeEl.innerHTML = '<option value="">All Codes</option>';
        codeEl.disabled = true;
        genderEl.value = '';
        formatEl.value = '';
        applyFilters();
    });

    // ── Audio Player ──
    const audio = document.getElementById('audioPlayer');
    const inlinePlayer = document.getElementById('inlinePlayer');
    const playerName = document.getElementById('playerName');
    const playerMeta = document.getElementById('playerMeta');
    const playerAvatar = document.getElementById('playerAvatar');
    const playerTime = document.getElementById('playerTime');
    const playerDuration = document.getElementById('playerDuration');
    const playerProgressFill = document.getElementById('playerProgressFill');
    const playerProgress = document.getElementById('playerProgress');
    const playerClose = document.getElementById('playerClose');
    let currentPlayingBtn = null;

    function fmtTime(s) {
        const m = Math.floor(s / 60);
        const sec = Math.floor(s % 60);
        return `${m}:${sec.toString().padStart(2, '0')}`;
    }

    audio.addEventListener('timeupdate', () => {
        playerTime.textContent = fmtTime(audio.currentTime);
        if (audio.duration) {
            playerProgressFill.style.width = (audio.currentTime / audio.duration * 100) + '%';
        }
    });

    audio.addEventListener('loadedmetadata', () => {
        playerDuration.textContent = fmtTime(audio.duration);
    });

    audio.addEventListener('ended', () => {
        if (currentPlayingBtn) {
            currentPlayingBtn.classList.remove('playing');
            currentPlayingBtn.innerHTML = '▶';
            currentPlayingBtn.closest('tr')?.classList.remove('playing-row');
        }
        currentPlayingBtn = null;
        playerProgressFill.style.width = '0%';
    });

    // Seek on progress click
    playerProgress.addEventListener('click', function (e) {
        if (!audio.duration) return;
        const rect = this.getBoundingClientRect();
        const pct = (e.clientX - rect.left) / rect.width;
        audio.currentTime = pct * audio.duration;
    });

    // Close player
    playerClose.addEventListener('click', function () {
        audio.pause();
        audio.src = '';
        inlinePlayer.classList.remove('visible');
        if (currentPlayingBtn) {
            currentPlayingBtn.classList.remove('playing');
            currentPlayingBtn.innerHTML = '▶';
            currentPlayingBtn.closest('tr')?.classList.remove('playing-row');
        }
        currentPlayingBtn = null;
    });

    // ── Preview Click (Delegated) ──
    document.getElementById('voicesTable').addEventListener('click', async function (e) {
        const btn = e.target.closest('.btnPreview');
        if (!btn) return;

        const tr = btn.closest('tr');
        const id = tr.dataset.id;
        const gender = tr.dataset.gender || 'neutral';

        // Reset previous
        if (currentPlayingBtn && currentPlayingBtn !== btn) {
            currentPlayingBtn.classList.remove('playing');
            currentPlayingBtn.innerHTML = '▶';
            currentPlayingBtn.closest('tr')?.classList.remove('playing-row');
        }

        btn.classList.add('playing');
        btn.innerHTML = '⏳';
        tr.classList.add('playing-row');
        currentPlayingBtn = btn;

        // Show inline player
        const voiceName = tr.querySelector('.voice-name-cell')?.textContent.trim().split('\n')[0] || '—';
        const langText = tr.querySelector('.lang-name')?.textContent || '';
        const langCodeText = tr.querySelector('.lang-code')?.textContent || '';
        playerName.textContent = voiceName;
        playerMeta.textContent = `${langText} · ${langCodeText}`;

        const gradients = { male: 'var(--vb-male)', female: 'var(--vb-female)', neutral: 'var(--vb-neutral)' };
        playerAvatar.style.background = gradients[gender] || gradients.neutral;

        playerProgressFill.style.width = '0%';
        playerTime.textContent = '0:00';
        playerDuration.textContent = '0:00';
        inlinePlayer.classList.add('visible');

        try {
            const res = await fetch(`{{ url('tts/voices') }}/${id}/preview`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (data.url) {
                audio.src = data.url;
                audio.play().catch(() => {});
                btn.innerHTML = '⏸';
            } else {
                throw new Error('No URL');
            }
        } catch (e) {
            console.error(e);
            btn.classList.remove('playing');
            btn.innerHTML = '▶';
            tr.classList.remove('playing-row');
            inlinePlayer.classList.remove('visible');
            alert('Preview failed.');
        }
    });

    // ── Edit Modal ──
    const modalEl = document.getElementById('editVoiceModal');
    const bsModal = new bootstrap.Modal(modalEl);
    const voiceIdEl = document.getElementById('voiceId');
    const voiceTextEl = document.getElementById('voiceText');
    const audioFormatEl = document.getElementById('audioFormat');

    document.getElementById('voicesTable').addEventListener('click', function (e) {
        const btn = e.target.closest('.btnEdit');
        if (!btn) return;

        const tr = btn.closest('tr');
        voiceIdEl.value = tr.dataset.id;
        voiceTextEl.value = tr.dataset.text || '';
        audioFormatEl.value = tr.dataset.format || 'mp3';
        bsModal.show();
    });

    document.getElementById('editVoiceForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = voiceIdEl.value;
        const saveBtn = this.querySelector('.btn-modal-save');

        saveBtn.textContent = 'Saving...';
        saveBtn.disabled = true;

        try {
            const res = await fetch(`{{ url('tts/voices') }}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'PUT',
                    voice_text: voiceTextEl.value,
                    audio_format: audioFormatEl.value
                })
            });

            if (res.ok) {
                const tr = document.querySelector(`#voicesTable tr[data-id="${id}"]`);
                if (tr) {
                    tr.dataset.text = voiceTextEl.value;
                    tr.dataset.format = audioFormatEl.value;
                    const badge = tr.querySelector('.format-badge');
                    if (badge) badge.textContent = audioFormatEl.value.toUpperCase();
                }
                bsModal.hide();
            } else {
                const data = await res.json().catch(() => ({}));
                alert('Save failed: ' + (data.message || res.statusText));
            }
        } catch (err) {
            alert('Network error.');
        } finally {
            saveBtn.textContent = 'Save Changes';
            saveBtn.disabled = false;
        }
    });

});
</script>
@endpush