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
        --vb-text: #eef0f6;
        --vb-text-sec: #8b8da3;
        --vb-text-muted: #555670;
    }

    .voice-bank {
        font-family: 'DM Sans', sans-serif;
        background: var(--vb-bg);
        min-height: 100vh;
        color: var(--vb-text);
        padding-bottom: 60px;
    }

    /* ── Header ── */
    .vb-header {
        padding: 28px 32px 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .vb-title {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 4px;
    }

    .vb-title .ring {
        width: 38px; height: 38px; border-radius: 10px;
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 18px;
        box-shadow: 0 4px 18px var(--vb-accent-glow);
    }

    .vb-subtitle { font-size: 0.84rem; color: var(--vb-text-sec); margin-left: 50px; }

    .vb-stats { display: flex; gap: 10px; align-items: center; }

    .vb-stat {
        background: var(--vb-panel); border: 1px solid var(--vb-border);
        border-radius: 10px; padding: 8px 16px; text-align: center; min-width: 80px;
    }

    .vb-stat .n { font-family: 'Space Mono', monospace; font-size: 1.1rem; font-weight: 700; color: var(--vb-accent-light); }
    .vb-stat .l { font-size: 0.66rem; color: var(--vb-text-muted); text-transform: uppercase; letter-spacing: 0.06em; }

    .btn-sync {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe); color: #fff; border: none;
        border-radius: 10px; padding: 10px 20px;
        font-family: 'DM Sans', sans-serif; font-size: 0.84rem; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
        transition: all 0.15s; box-shadow: 0 4px 16px var(--vb-accent-glow);
    }

    .btn-sync:hover { transform: translateY(-1px); box-shadow: 0 6px 22px var(--vb-accent-glow); }

    /* ── Filters ── */
    .vb-filters {
        margin: 20px 32px 0; background: var(--vb-panel);
        border: 1px solid var(--vb-border); border-radius: 14px;
        padding: 16px 20px; display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;
    }

    .fg { flex: 1; min-width: 130px; }

    .fg-label {
        display: block; font-family: 'Space Mono', monospace;
        font-size: 0.62rem; text-transform: uppercase; letter-spacing: 0.1em;
        color: var(--vb-text-muted); margin-bottom: 5px;
    }

    .fg-select, .fg-search {
        width: 100%; background: var(--vb-bg); border: 1px solid var(--vb-border);
        border-radius: 8px; padding: 8px 12px; color: var(--vb-text);
        font-family: 'DM Sans', sans-serif; font-size: 0.82rem; outline: none; transition: all 0.15s;
    }

    .fg-select { appearance: none; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%238b8da3' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 10px center; padding-right: 28px;
    }

    .fg-select option { background: var(--vb-panel); color: var(--vb-text); }

    .fg-select:focus, .fg-search:focus { border-color: var(--vb-accent); box-shadow: 0 0 0 3px var(--vb-accent-glow); }

    .fg-search {
        padding-left: 34px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23555670' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: 10px center;
    }

    .fg-clear {
        background: var(--vb-bg); border: 1px solid var(--vb-border); border-radius: 8px;
        padding: 8px 14px; color: var(--vb-text-sec); font-size: 0.8rem;
        cursor: pointer; transition: all 0.15s; white-space: nowrap; font-family: 'DM Sans', sans-serif;
    }
    .fg-clear:hover { border-color: var(--vb-danger); color: var(--vb-danger); }

    /* ── Active pills ── */
    .active-pills { margin: 10px 32px 0; display: flex; gap: 6px; flex-wrap: wrap; }

    .pill {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(108,92,231,0.1); border: 1px solid rgba(108,92,231,0.3);
        border-radius: 7px; padding: 3px 10px; font-size: 0.74rem; color: var(--vb-accent-light);
        animation: fadeIn 0.2s ease;
    }

    .pill .x { cursor: pointer; opacity: 0.6; font-size: 13px; }
    .pill .x:hover { opacity: 1; }

    @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

    /* ── Table Wrapper ── */
    .vb-table-wrap {
        margin: 16px 32px 0; background: var(--vb-panel);
        border: 1px solid var(--vb-border); border-radius: 14px; overflow: hidden;
    }

    /* DataTables overrides */
    .vb-table-wrap .dataTables_wrapper .dataTables_length,
    .vb-table-wrap .dataTables_wrapper .dataTables_filter { display: none; }

    .vb-table-wrap .dataTables_wrapper .dataTables_info {
        color: var(--vb-text-muted); font-size: 0.76rem; padding: 10px 20px;
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_paginate { padding: 10px 20px; }

    .vb-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: var(--vb-bg) !important; border: 1px solid var(--vb-border) !important;
        border-radius: 7px !important; color: var(--vb-text-sec) !important;
        font-size: 0.78rem; padding: 5px 10px !important; margin: 0 2px; transition: all 0.15s;
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--vb-hover) !important; border-color: var(--vb-accent) !important; color: var(--vb-text) !important;
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--vb-accent) !important; border-color: var(--vb-accent) !important;
        color: #fff !important; box-shadow: 0 2px 10px var(--vb-accent-glow);
    }

    .vb-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { opacity: 0.3; }

    #voicesTable { width: 100% !important; border-collapse: separate; border-spacing: 0; }

    #voicesTable thead th {
        background: var(--vb-bg); color: var(--vb-text-muted);
        font-family: 'Space Mono', monospace; font-size: 0.66rem;
        text-transform: uppercase; letter-spacing: 0.1em;
        padding: 12px 14px; border-bottom: 1px solid var(--vb-border); font-weight: 400; white-space: nowrap;
    }
    #voicesTable thead th:first-child { padding-left: 20px; }
    #voicesTable thead th:last-child { padding-right: 20px; }

    #voicesTable tbody tr { transition: background 0.12s; }
    #voicesTable tbody tr:hover { background: var(--vb-hover); }
    #voicesTable tbody tr.playing-row { background: rgba(108,92,231,0.06); }

    #voicesTable tbody td {
        padding: 10px 14px; border-bottom: 1px solid rgba(35,35,48,0.5);
        color: var(--vb-text); font-size: 0.84rem; vertical-align: middle;
    }
    #voicesTable tbody td:first-child { padding-left: 20px; }
    #voicesTable tbody td:last-child { padding-right: 20px; }
    #voicesTable tbody tr:last-child td { border-bottom: none; }

    /* Cell styles */
    .vendor-badge {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--vb-bg); border: 1px solid var(--vb-border); border-radius: 7px;
        padding: 4px 10px 4px 7px; font-size: 0.76rem; font-weight: 500;
    }
    .vendor-badge img { width: 15px; height: 15px; border-radius: 3px; }

    .lang-cell { display: flex; align-items: center; gap: 7px; }
    .lang-flag { font-size: 1.05rem; }
    .lang-name { font-weight: 500; font-size: 0.82rem; }
    .lang-code { font-family: 'Space Mono', monospace; font-size: 0.68rem; color: var(--vb-text-muted); }

    .gender-dot { display: inline-flex; align-items: center; gap: 5px; font-size: 0.82rem; }
    .gender-dot::before { content: ''; width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
    .gender-dot.male::before { background: #0984e3; box-shadow: 0 0 5px rgba(9,132,227,0.4); }
    .gender-dot.female::before { background: #e84393; box-shadow: 0 0 5px rgba(232,67,147,0.4); }
    .gender-dot.neutral::before { background: #636e72; }

    .voice-cell { font-weight: 600; }
    .voice-cell .sub { display: block; font-size: 0.68rem; font-weight: 400; color: var(--vb-text-muted); font-family: 'Space Mono', monospace; margin-top: 1px; }

    .fmt-badge {
        font-family: 'Space Mono', monospace; font-size: 0.66rem; padding: 3px 9px;
        border-radius: 5px; background: var(--vb-bg); border: 1px solid var(--vb-border);
        color: var(--vb-text-sec); text-transform: uppercase; letter-spacing: 0.05em;
    }

    .prev-btn {
        width: 34px; height: 34px; border-radius: 50%;
        border: 1px solid var(--vb-border); background: var(--vb-bg);
        color: var(--vb-text-sec); display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 13px; transition: all 0.15s;
    }
    .prev-btn:hover { border-color: var(--vb-accent); color: var(--vb-accent); background: var(--vb-accent-glow); }
    .prev-btn.playing { background: var(--vb-accent); border-color: var(--vb-accent); color: #fff; box-shadow: 0 0 14px var(--vb-accent-glow); }

    .edit-btn {
        background: var(--vb-bg); border: 1px solid var(--vb-border); border-radius: 7px;
        padding: 5px 12px; color: var(--vb-text-sec); font-family: 'DM Sans', sans-serif;
        font-size: 0.76rem; font-weight: 500; cursor: pointer; transition: all 0.15s;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .edit-btn:hover { border-color: var(--vb-accent); color: var(--vb-accent-light); }

    /* ── Inline Player ── */
    .inline-player {
        margin: 0 32px; background: var(--vb-panel);
        border: 1px solid var(--vb-border); border-radius: 0 0 14px 14px; border-top: none;
        padding: 12px 20px; display: none; align-items: center; gap: 14px;
    }
    .inline-player.visible { display: flex; }

    .ip-info { display: flex; align-items: center; gap: 9px; flex: 0 0 auto; }
    .ip-avatar { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; color: #fff; }
    .ip-name { font-weight: 600; font-size: 0.82rem; }
    .ip-meta { font-size: 0.7rem; color: var(--vb-text-muted); }

    .ip-bar { flex: 1; height: 3px; background: var(--vb-border); border-radius: 3px; overflow: hidden; cursor: pointer; }
    .ip-fill { height: 100%; background: var(--vb-accent); border-radius: 3px; width: 0%; transition: width 0.1s linear; }

    .ip-time { font-family: 'Space Mono', monospace; font-size: 0.7rem; color: var(--vb-text-muted); white-space: nowrap; }

    .ip-close {
        width: 26px; height: 26px; border-radius: 50%;
        border: 1px solid var(--vb-border); background: none;
        color: var(--vb-text-muted); display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 13px; transition: all 0.15s;
    }
    .ip-close:hover { border-color: var(--vb-danger); color: var(--vb-danger); }

    /* ── Edit Modal ── */
    .modal-dark .modal-content {
        background: var(--vb-panel); border: 1px solid var(--vb-border);
        border-radius: 16px; color: var(--vb-text);
    }
    .modal-dark .modal-header { border-bottom: 1px solid var(--vb-border); padding: 18px 22px; }
    .modal-dark .modal-title { font-weight: 700; font-size: 1.05rem; }
    .modal-dark .btn-close { filter: invert(1) brightness(0.7); }
    .modal-dark .modal-body { padding: 22px; }
    .modal-dark .modal-footer { border-top: 1px solid var(--vb-border); padding: 14px 22px; }

    .modal-dark .form-label { font-size: 0.78rem; font-weight: 500; color: var(--vb-text-sec); margin-bottom: 5px; }

    .modal-dark .form-control,
    .modal-dark .form-select {
        background: var(--vb-bg); border: 1px solid var(--vb-border); border-radius: 9px;
        color: var(--vb-text); padding: 9px 12px; font-family: 'DM Sans', sans-serif; font-size: 0.86rem;
    }
    .modal-dark .form-control:focus, .modal-dark .form-select:focus {
        border-color: var(--vb-accent); box-shadow: 0 0 0 3px var(--vb-accent-glow);
    }
    .modal-dark .form-text { color: var(--vb-text-muted); font-size: 0.72rem; }

    .modal-btn-cancel {
        background: var(--vb-bg); border: 1px solid var(--vb-border); color: var(--vb-text-sec);
        border-radius: 9px; padding: 8px 18px; font-family: 'DM Sans', sans-serif; font-weight: 500; cursor: pointer;
    }
    .modal-btn-save {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe); border: none; color: #fff;
        border-radius: 9px; padding: 8px 22px; font-family: 'DM Sans', sans-serif; font-weight: 600; cursor: pointer;
        box-shadow: 0 3px 14px var(--vb-accent-glow);
    }

    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--vb-border); border-radius: 4px; }

    @media (max-width: 768px) {
        .vb-header, .vb-filters, .vb-table-wrap, .inline-player, .active-pills { margin-left: 12px; margin-right: 12px; }
        .vb-filters { flex-direction: column; }
        .fg { min-width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="voice-bank">

    {{-- ── Header ── --}}
    <div class="vb-header">
        <div>
            <h1 class="vb-title"><span class="ring">🎙</span> Voice Bank</h1>
            <div class="vb-subtitle">Browse, preview, and manage all synced TTS voices</div>
        </div>
        <div class="vb-stats">
            <div class="vb-stat">
                <div class="n">{{ $voices->count() }}</div>
                <div class="l">Voices</div>
            </div>
            <div class="vb-stat">
                <div class="n">{{ $languages->count() }}</div>
                <div class="l">Languages</div>
            </div>
            <form method="POST" action="{{ route('voices.sync') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-sync">↻ Sync Voices</button>
            </form>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="vb-filters">
        <div class="fg" style="flex:1.5">
            <label class="fg-label">Search</label>
            <input type="text" id="fSearch" class="fg-search" placeholder="Search voices...">
        </div>
        <div class="fg">
            <label class="fg-label">Vendor</label>
            <select id="fVendor" class="fg-select">
                <option value="">All</option>
                @foreach($vendors as $v)<option value="{{ $v }}">{{ $v }}</option>@endforeach
            </select>
        </div>
        <div class="fg">
            <label class="fg-label">Language</label>
            <select id="fLang" class="fg-select">
                <option value="">All</option>
                @foreach($languages as $l)<option value="{{ $l }}">{{ $l }}</option>@endforeach
            </select>
        </div>
        <div class="fg">
            <label class="fg-label">Code</label>
            <select id="fCode" class="fg-select" disabled>
                <option value="">All</option>
            </select>
        </div>
        <div class="fg">
            <label class="fg-label">Gender</label>
            <select id="fGender" class="fg-select">
                <option value="">All</option>
                @foreach($genders as $g)
                    @if($g)<option value="{{ $g }}">{{ ucfirst(strtolower($g)) }}</option>@endif
                @endforeach
            </select>
        </div>
        <div class="fg">
            <label class="fg-label">Format</label>
            <select id="fFormat" class="fg-select">
                <option value="">All</option>
                @foreach($formats as $f)<option value="{{ $f }}">{{ strtoupper($f) }}</option>@endforeach
            </select>
        </div>
        <div class="fg" style="flex:0 0 auto;">
            <label class="fg-label">&nbsp;</label>
            <button type="button" id="fClear" class="fg-clear">✕ Clear</button>
        </div>
    </div>

    <div class="active-pills" id="pills"></div>

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
                        <th style="width:60px;text-align:center;">Play</th>
                        <th>Format</th>
                        <th style="width:70px;text-align:center;">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $flagMap = [
                            'en-US'=>'🇺🇸','en-GB'=>'🇬🇧','en-AU'=>'🇦🇺','en-IN'=>'🇮🇳',
                            'es-MX'=>'🇲🇽','es-ES'=>'🇪🇸','fr-FR'=>'🇫🇷','fr-CA'=>'🇨🇦',
                            'de-DE'=>'🇩🇪','pt-BR'=>'🇧🇷','pt-PT'=>'🇵🇹','ja-JP'=>'🇯🇵',
                            'ko-KR'=>'🇰🇷','zh-CN'=>'🇨🇳','zh-TW'=>'🇹🇼','hi-IN'=>'🇮🇳',
                            'ar-XA'=>'🇸🇦','it-IT'=>'🇮🇹','nl-NL'=>'🇳🇱','ru-RU'=>'🇷🇺',
                            'pl-PL'=>'🇵🇱','sv-SE'=>'🇸🇪','tr-TR'=>'🇹🇷','th-TH'=>'🇹🇭',
                            'vi-VN'=>'🇻🇳','id-ID'=>'🇮🇩','fil-PH'=>'🇵🇭','uk-UA'=>'🇺🇦',
                            'cs-CZ'=>'🇨🇿','el-GR'=>'🇬🇷','he-IL'=>'🇮🇱','af-ZA'=>'🇿🇦',
                        ];
                    @endphp
                    @foreach($voices as $v)
                    @php
                        $g = strtolower($v->gender ?? 'neutral');
                        $flag = $flagMap[$v->language_code] ?? '🌐';
                    @endphp
                    <tr data-id="{{ $v->id }}"
                        data-text="{{ $v->voice_text ?? '' }}"
                        data-format="{{ $v->audio_format ?? 'mp3' }}"
                        data-gender="{{ $g }}">
                        <td>
                            <span class="vendor-badge">
                                <img src="{{ asset('public/icons/google.png') }}" alt="">
                                {{ $v->vendor }}
                            </span>
                        </td>
                        <td>
                            <div class="lang-cell">
                                <span class="lang-flag">{{ $flag }}</span>
                                <div>
                                    <div class="lang-name">{{ $v->language_full }}</div>
                                    <div class="lang-code">{{ $v->language_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="gender-dot {{ $g }}">{{ ucfirst($g) }}</span></td>
                        <td>
                            <div class="voice-cell">
                                {{ $v->voice_text ?: $v->voice_name }}
                                <span class="sub">{{ $v->voice_name }}</span>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="prev-btn btnPreview" title="Preview">▶</button>
                        </td>
                        <td><span class="fmt-badge">{{ strtoupper($v->audio_format ?? 'mp3') }}</span></td>
                        <td style="text-align:center;">
                            <button type="button" class="edit-btn btnEdit">✎</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Inline Player --}}
    <div class="inline-player" id="inlinePlayer">
        <div class="ip-info">
            <div class="ip-avatar" id="ipAvatar" style="background:linear-gradient(135deg,#0984e3,#6c5ce7);">🎤</div>
            <div>
                <div class="ip-name" id="ipName">—</div>
                <div class="ip-meta" id="ipMeta">—</div>
            </div>
        </div>
        <div class="ip-bar" id="ipBar"><div class="ip-fill" id="ipFill"></div></div>
        <div class="ip-time"><span id="ipTime">0:00</span> / <span id="ipDur">0:00</span></div>
        <button type="button" class="ip-close" id="ipClose">✕</button>
    </div>

    <audio id="audioPlayer"></audio>
</div>

{{-- Edit Modal --}}
<div class="modal fade modal-dark" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">✎ Edit Voice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId">
                    <div class="mb-3">
                        <label class="form-label">Display Name / Sample Text</label>
                        <textarea class="form-control" id="editText" rows="3" placeholder="Sample text for this voice..."></textarea>
                        <div class="form-text">This text is used for preview and display.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Output Format</label>
                        <select id="editFormat" class="form-select">
                            <option value="mp3">MP3</option>
                            <option value="ogg">OGG</option>
                            <option value="wav">WAV</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="modal-btn-save" id="editSaveBtn">Save Changes</button>
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

    /* ── DataTable ── */
    const table = new DataTable('#voicesTable', {
        order: [[1, 'asc']],
        pageLength: 25,
        stateSave: true,
        language: {
            info: 'Showing _START_ – _END_ of _TOTAL_ voices',
            infoEmpty: 'No voices found',
            infoFiltered: '(filtered from _MAX_)',
            emptyTable: 'No voices synced yet. Click "Sync Voices" to fetch from Google.',
            zeroRecords: 'No matching voices',
        }
    });

    /* ── Filter refs ── */
    const fSearch = document.getElementById('fSearch');
    const fVendor = document.getElementById('fVendor');
    const fLang   = document.getElementById('fLang');
    const fCode   = document.getElementById('fCode');
    const fGender = document.getElementById('fGender');
    const fFormat = document.getElementById('fFormat');
    const fClear  = document.getElementById('fClear');
    const pills   = document.getElementById('pills');

    function applyFilters() {
        table.search(fSearch.value || '');
        table.column(0).search(fVendor.value || '', true, false);
        table.column(1).search(fCode.value || fLang.value || '', true, false);
        table.column(2).search(fGender.value || '', true, false);
        table.column(5).search((fFormat.value || '').toUpperCase(), true, false);
        table.draw();
        renderPills();
    }

    function renderPills() {
        const active = [];
        if (fSearch.value) active.push({ k: 'search', l: `"${fSearch.value}"`, fn: () => { fSearch.value = ''; } });
        if (fVendor.value) active.push({ k: 'vendor', l: fVendor.value, fn: () => { fVendor.value = ''; } });
        if (fLang.value) active.push({ k: 'lang', l: fLang.value, fn: () => { fLang.value = ''; fCode.innerHTML = '<option value="">All</option>'; fCode.disabled = true; } });
        if (fCode.value) active.push({ k: 'code', l: fCode.value, fn: () => { fCode.value = ''; } });
        if (fGender.value) active.push({ k: 'gender', l: fGender.value, fn: () => { fGender.value = ''; } });
        if (fFormat.value) active.push({ k: 'format', l: fFormat.value.toUpperCase(), fn: () => { fFormat.value = ''; } });

        pills.innerHTML = '';
        active.forEach(a => {
            const el = document.createElement('span');
            el.className = 'pill';
            el.innerHTML = `${a.l} <span class="x">✕</span>`;
            el.querySelector('.x').addEventListener('click', () => { a.fn(); applyFilters(); });
            pills.appendChild(el);
        });
    }

    fSearch.addEventListener('input', applyFilters);
    [fVendor, fGender, fFormat].forEach(el => el.addEventListener('change', applyFilters));

    /* Language → Code dependency (calls VoiceController@codes) */
    fLang.addEventListener('change', async function () {
        fCode.innerHTML = '<option value="">All</option>';
        fCode.disabled = true;
        if (this.value) {
            try {
                const res = await fetch(`{{ route('voices.codes') }}?language_full=${encodeURIComponent(this.value)}`);
                const codes = await res.json();
                codes.forEach(c => {
                    const o = document.createElement('option');
                    o.value = c; o.textContent = c;
                    fCode.appendChild(o);
                });
                fCode.disabled = false;
            } catch (e) { console.error(e); }
        }
        applyFilters();
    });

    fCode.addEventListener('change', applyFilters);

    fClear.addEventListener('click', () => {
        fSearch.value = ''; fVendor.value = ''; fLang.value = '';
        fCode.innerHTML = '<option value="">All</option>'; fCode.disabled = true;
        fGender.value = ''; fFormat.value = '';
        applyFilters();
    });

    /* ── Audio Player ── */
    const audio     = document.getElementById('audioPlayer');
    const ipEl      = document.getElementById('inlinePlayer');
    const ipName    = document.getElementById('ipName');
    const ipMeta    = document.getElementById('ipMeta');
    const ipAvatar  = document.getElementById('ipAvatar');
    const ipTime    = document.getElementById('ipTime');
    const ipDur     = document.getElementById('ipDur');
    const ipFill    = document.getElementById('ipFill');
    const ipBar     = document.getElementById('ipBar');
    const ipClose   = document.getElementById('ipClose');
    let curBtn      = null;

    function fmt(s) { return `${Math.floor(s/60)}:${Math.floor(s%60).toString().padStart(2,'0')}`; }

    audio.addEventListener('timeupdate', () => {
        ipTime.textContent = fmt(audio.currentTime);
        if (audio.duration) ipFill.style.width = (audio.currentTime / audio.duration * 100) + '%';
    });
    audio.addEventListener('loadedmetadata', () => { ipDur.textContent = fmt(audio.duration); });
    audio.addEventListener('ended', () => {
        if (curBtn) { curBtn.classList.remove('playing'); curBtn.textContent = '▶'; curBtn.closest('tr')?.classList.remove('playing-row'); }
        curBtn = null; ipFill.style.width = '0%';
    });

    ipBar.addEventListener('click', function (e) {
        if (!audio.duration) return;
        audio.currentTime = (e.clientX - this.getBoundingClientRect().left) / this.offsetWidth * audio.duration;
    });

    ipClose.addEventListener('click', () => {
        audio.pause(); audio.src = '';
        ipEl.classList.remove('visible');
        if (curBtn) { curBtn.classList.remove('playing'); curBtn.textContent = '▶'; curBtn.closest('tr')?.classList.remove('playing-row'); }
        curBtn = null;
    });

    /* ── Preview (delegated — survives DataTable pagination) ── */
    document.getElementById('voicesTable').addEventListener('click', async function (e) {
        const btn = e.target.closest('.btnPreview');
        if (!btn) return;

        const tr = btn.closest('tr');
        const id = tr.dataset.id;
        const gender = tr.dataset.gender || 'neutral';

        if (curBtn && curBtn !== btn) {
            curBtn.classList.remove('playing'); curBtn.textContent = '▶';
            curBtn.closest('tr')?.classList.remove('playing-row');
        }

        btn.classList.add('playing'); btn.textContent = '⏳'; tr.classList.add('playing-row');
        curBtn = btn;

        const name = tr.querySelector('.voice-cell')?.textContent.trim().split('\n')[0] || '—';
        const lang = tr.querySelector('.lang-name')?.textContent || '';
        const code = tr.querySelector('.lang-code')?.textContent || '';
        ipName.textContent = name;
        ipMeta.textContent = `${lang} · ${code}`;

        const grads = { male: 'linear-gradient(135deg,#0984e3,#6c5ce7)', female: 'linear-gradient(135deg,#e84393,#fd79a8)', neutral: 'linear-gradient(135deg,#636e72,#b2bec3)' };
        ipAvatar.style.background = grads[gender] || grads.neutral;

        ipFill.style.width = '0%'; ipTime.textContent = '0:00'; ipDur.textContent = '0:00';
        ipEl.classList.add('visible');

        try {
            /* Calls VoiceController@preview */
            const res = await fetch(`{{ url('tts/voices') }}/${id}/preview`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (data.url) {
                audio.src = data.url;
                audio.play().catch(() => {});
                btn.textContent = '⏸';
            } else {
                throw new Error(data.error || 'No URL');
            }
        } catch (err) {
            console.error(err);
            btn.classList.remove('playing'); btn.textContent = '▶'; tr.classList.remove('playing-row');
            ipEl.classList.remove('visible');
            alert('Preview failed: ' + (err.message || 'Unknown error'));
        }
    });

    /* ── Edit (delegated) — calls VoiceController@update ── */
    const editModal = document.getElementById('editModal');
    const bsModal   = typeof bootstrap !== 'undefined' ? new bootstrap.Modal(editModal) : null;
    const editId    = document.getElementById('editId');
    const editText  = document.getElementById('editText');
    const editFmt   = document.getElementById('editFormat');

    document.getElementById('voicesTable').addEventListener('click', function (e) {
        const btn = e.target.closest('.btnEdit');
        if (!btn) return;
        const tr = btn.closest('tr');
        editId.value = tr.dataset.id;
        editText.value = tr.dataset.text || '';
        editFmt.value = tr.dataset.format || 'mp3';
        if (bsModal) bsModal.show();
    });

    document.getElementById('editForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = editId.value;
        const saveBtn = document.getElementById('editSaveBtn');
        saveBtn.textContent = 'Saving...'; saveBtn.disabled = true;

        try {
            const res = await fetch(`{{ url('tts/voices') }}/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ _method: 'PUT', voice_text: editText.value, audio_format: editFmt.value })
            });

            if (res.ok) {
                const tr = document.querySelector(`#voicesTable tr[data-id="${id}"]`);
                if (tr) {
                    tr.dataset.text = editText.value;
                    tr.dataset.format = editFmt.value;
                    const badge = tr.querySelector('.fmt-badge');
                    if (badge) badge.textContent = editFmt.value.toUpperCase();
                }
                if (bsModal) bsModal.hide();
            } else {
                const data = await res.json().catch(() => ({}));
                alert('Save failed: ' + (data.message || res.statusText));
            }
        } catch { alert('Network error.'); }
        finally { saveBtn.textContent = 'Save Changes'; saveBtn.disabled = false; }
    });

});
</script>
@endpush