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
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
    /*
     * Voice Bank — scoped dark theme.
     * All rules are scoped under .vb so they don't leak into the app shell.
     */
    .vb {
        font-family: 'DM Sans', sans-serif;
    }

    /* ── Header row ── */
    .vb-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .vb-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin: 0 0 2px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #1a1a2e;
    }

    .vb-header h1 .ring {
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

    .vb-header .sub {
        font-size: 0.84rem;
        color: #6b7280;
        margin-left: 46px;
    }

    .vb-stats {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .vb-stat-box {
        background: #f8f9fc;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 8px 16px;
        text-align: center;
        min-width: 72px;
    }

    .vb-stat-box .n {
        font-family: 'Space Mono', monospace;
        font-size: 1.1rem;
        font-weight: 700;
        color: #6c5ce7;
    }

    .vb-stat-box .l {
        font-size: 0.64rem;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .btn-sync {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 9px 20px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
        box-shadow: 0 3px 12px rgba(108, 92, 231, 0.2);
    }

    .btn-sync:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 18px rgba(108, 92, 231, 0.3);
    }

    /* ── Filters card ── */
    .vb-filters-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    .vb-filters-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .vb-fg {
        flex: 1;
        min-width: 130px;
    }

    .vb-fg-label {
        display: block;
        font-family: 'Space Mono', monospace;
        font-size: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #9ca3af;
        margin-bottom: 4px;
    }

    .vb-fg .form-control,
    .vb-fg .form-select {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        border-radius: 8px;
        border-color: #e5e7eb;
    }

    .vb-fg .form-control:focus,
    .vb-fg .form-select:focus {
        border-color: #6c5ce7;
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.15);
    }

    .btn-clear-filters {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 7px 14px;
        font-size: 0.8rem;
        color: #6b7280;
        cursor: pointer;
        white-space: nowrap;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.12s;
    }

    .btn-clear-filters:hover {
        border-color: #ef4444;
        color: #ef4444;
        background: #fef2f2;
    }

    /* Active filter pills */
    .vb-pills {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 12px;
        min-height: 0;
    }

    .vb-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #ede9fe;
        border: 1px solid #c4b5fd;
        border-radius: 7px;
        padding: 3px 10px;
        font-size: 0.72rem;
        color: #6c5ce7;
        font-weight: 500;
        animation: pillIn 0.15s ease;
    }

    .vb-pill .x {
        cursor: pointer;
        opacity: 0.5;
        font-size: 12px;
    }

    .vb-pill .x:hover {
        opacity: 1;
    }

    @keyframes pillIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    /* ── Table card ── */
    .vb-table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    /* Hide DT default search/length (we have our own) */
    .vb-table-card .dataTables_length,
    .vb-table-card .dataTables_filter {
        display: none !important;
    }

    .vb-table-card .dataTables_info {
        font-size: 0.76rem;
        color: #9ca3af;
        padding: 10px 20px;
    }

    .vb-table-card .dataTables_paginate {
        padding: 10px 20px;
    }

    .vb-table-card .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        font-size: 0.78rem;
        padding: 4px 10px !important;
        margin: 0 2px;
    }

    .vb-table-card .dataTables_paginate .paginate_button.current {
        background: #6c5ce7 !important;
        border-color: #6c5ce7 !important;
        color: #fff !important;
    }

    #voicesTable {
        width: 100% !important;
        margin: 0 !important;
    }

    #voicesTable thead th {
        background: #f9fafb;
        font-family: 'Space Mono', monospace;
        font-size: 0.64rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #9ca3af;
        border-bottom: 1px solid #e5e7eb;
        padding: 12px 14px;
        font-weight: 400;
        white-space: nowrap;
    }

    #voicesTable tbody td {
        padding: 10px 14px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
        font-size: 0.86rem;
        color: #374151;
    }

    #voicesTable tbody tr:hover {
        background: #faf8ff;
    }

    #voicesTable tbody tr.playing-row {
        background: #f5f3ff;
    }

    #voicesTable tbody tr:last-child td {
        border-bottom: none;
    }

    /* ── Cell styles ── */
    .vb-vendor {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 0.8rem;
        font-weight: 500;
        color: #374151;
    }

    .vb-vendor img {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    }

    .vb-lang-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .vb-flag {
        font-size: 1.15rem;
        line-height: 1;
    }

    .vb-lang-name {
        font-weight: 500;
        font-size: 0.84rem;
        color: #1f2937;
    }

    .vb-lang-code {
        font-family: 'Space Mono', monospace;
        font-size: 0.68rem;
        color: #9ca3af;
    }

    .vb-gender {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.84rem;
        color: #4b5563;
    }

    .vb-gender::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .vb-gender.male::before {
        background: #3b82f6;
        box-shadow: 0 0 4px rgba(59, 130, 246, 0.4);
    }

    .vb-gender.female::before {
        background: #ec4899;
        box-shadow: 0 0 4px rgba(236, 72, 153, 0.4);
    }

    .vb-gender.neutral::before {
        background: #9ca3af;
    }

    .vb-voice-name {
        font-weight: 600;
        color: #1f2937;
    }

    .vb-voice-name .sub {
        display: block;
        font-size: 0.68rem;
        font-weight: 400;
        color: #9ca3af;
        font-family: 'Space Mono', monospace;
        margin-top: 1px;
    }

    .vb-fmt {
        font-family: 'Space Mono', monospace;
        font-size: 0.66rem;
        padding: 3px 9px;
        border-radius: 5px;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* Preview button */
    .vb-play {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.15s;
    }

    .vb-play:hover {
        border-color: #6c5ce7;
        color: #6c5ce7;
        background: #f5f3ff;
    }

    .vb-play.playing {
        background: #6c5ce7;
        border-color: #6c5ce7;
        color: #fff;
        box-shadow: 0 0 12px rgba(108, 92, 231, 0.3);
        animation: playPulse 1.5s infinite;
    }

    @keyframes playPulse {
        0%, 100% { box-shadow: 0 0 12px rgba(108, 92, 231, 0.3); }
        50% { box-shadow: 0 0 20px rgba(108, 92, 231, 0.5); }
    }

    /* Edit button */
    .vb-edit {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 7px;
        padding: 5px 12px;
        color: #6b7280;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.76rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.12s;
    }

    .vb-edit:hover {
        border-color: #6c5ce7;
        color: #6c5ce7;
        background: #f5f3ff;
    }

    /* ── Inline Player ── */
    .vb-player {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0 0 14px 14px;
        border-top: none;
        margin-top: -1px;
        padding: 12px 20px;
        display: none;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .vb-player.visible {
        display: flex;
    }

    .vb-player .p-info {
        display: flex;
        align-items: center;
        gap: 9px;
        flex: 0 0 auto;
    }

    .vb-player .p-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #fff;
    }

    .vb-player .p-name {
        font-weight: 600;
        font-size: 0.84rem;
        color: #1f2937;
    }

    .vb-player .p-meta {
        font-size: 0.7rem;
        color: #9ca3af;
    }

    .vb-player .p-bar {
        flex: 1;
        height: 4px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
    }

    .vb-player .p-fill {
        height: 100%;
        background: #6c5ce7;
        border-radius: 4px;
        width: 0%;
        transition: width 0.1s linear;
    }

    .vb-player .p-time {
        font-family: 'Space Mono', monospace;
        font-size: 0.7rem;
        color: #9ca3af;
        white-space: nowrap;
    }

    .vb-player .p-close {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
    }

    .vb-player .p-close:hover {
        border-color: #ef4444;
        color: #ef4444;
    }

    /* ── Edit Modal ── */
    #editModal .modal-content {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
    }

    #editModal .modal-header {
        border-bottom: 1px solid #f3f4f6;
    }

    #editModal .modal-footer {
        border-top: 1px solid #f3f4f6;
    }

    .btn-modal-save {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        border: none;
        color: #fff;
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 600;
        box-shadow: 0 2px 10px rgba(108, 92, 231, 0.2);
    }
</style>
@endpush

@section('content')
<div class="vb">

    {{-- Header --}}
    <div class="vb-header">
        <div>
            <h1><span class="ring">🎙</span> Voice Bank</h1>
            <div class="sub">Browse, preview, and manage all synced TTS voices</div>
        </div>
        <div class="vb-stats">
            <div class="vb-stat-box">
                <div class="n">{{ $voices->count() }}</div>
                <div class="l">Voices</div>
            </div>
            <div class="vb-stat-box">
                <div class="n">{{ $languages->count() }}</div>
                <div class="l">Languages</div>
            </div>
            <form method="POST" action="{{ route('voices.sync') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-sync">↻ Sync Voices</button>
            </form>
        </div>
    </div>

    {{-- Filters --}}
    <div class="vb-filters-card">
        <div class="vb-filters-row">
            <div class="vb-fg" style="flex:1.5">
                <label class="vb-fg-label">Search</label>
                <input type="text" id="fSearch" class="form-control" placeholder="Search voices, languages...">
            </div>
            <div class="vb-fg">
                <label class="vb-fg-label">Vendor</label>
                <select id="fVendor" class="form-select">
                    <option value="">All</option>
                    @foreach($vendors as $v)<option value="{{ $v }}">{{ $v }}</option>@endforeach
                </select>
            </div>
            <div class="vb-fg">
                <label class="vb-fg-label">Language</label>
                <select id="fLang" class="form-select">
                    <option value="">All</option>
                    @foreach($languages as $l)<option value="{{ $l }}">{{ $l }}</option>@endforeach
                </select>
            </div>
            <div class="vb-fg">
                <label class="vb-fg-label">Code</label>
                <select id="fCode" class="form-select" disabled>
                    <option value="">All</option>
                </select>
            </div>
            <div class="vb-fg">
                <label class="vb-fg-label">Gender</label>
                <select id="fGender" class="form-select">
                    <option value="">All</option>
                    @foreach($genders as $g)
                        @if($g)<option value="{{ $g }}">{{ ucfirst(strtolower($g)) }}</option>@endif
                    @endforeach
                </select>
            </div>
            <div class="vb-fg">
                <label class="vb-fg-label">Format</label>
                <select id="fFormat" class="form-select">
                    <option value="">All</option>
                    @foreach($formats as $f)<option value="{{ $f }}">{{ strtoupper($f) }}</option>@endforeach
                </select>
            </div>
            <div class="vb-fg" style="flex:0 0 auto;">
                <label class="vb-fg-label">&nbsp;</label>
                <button type="button" id="fClear" class="btn-clear-filters">✕ Clear</button>
            </div>
        </div>
    </div>

    {{-- Active pills --}}
    <div class="vb-pills" id="pills"></div>

    {{-- Table --}}
    <div class="vb-table-card">
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
                            <span class="vb-vendor">
                                <img src="{{ asset('public/icons/google.png') }}" alt="">
                                {{ $v->vendor }}
                            </span>
                        </td>
                        <td>
                            <div class="vb-lang-cell">
                                <span class="vb-flag">{{ $flag }}</span>
                                <div>
                                    <div class="vb-lang-name">{{ $v->language_full }}</div>
                                    <div class="vb-lang-code">{{ $v->language_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="vb-gender {{ $g }}">{{ ucfirst($g) }}</span></td>
                        <td>
                            <div class="vb-voice-name">
                                {{ $v->voice_text ?: $v->voice_name }}
                                <span class="sub">{{ $v->voice_name }}</span>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="vb-play btnPreview" title="Preview">▶</button>
                        </td>
                        <td><span class="vb-fmt">{{ strtoupper($v->audio_format ?? 'mp3') }}</span></td>
                        <td style="text-align:center;">
                            <button type="button" class="vb-edit btnEdit">✎ Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Inline Player --}}
    <div class="vb-player" id="inlinePlayer">
        <div class="p-info">
            <div class="p-avatar" id="pAvatar" style="background:linear-gradient(135deg,#3b82f6,#6c5ce7);">🎤</div>
            <div>
                <div class="p-name" id="pName">—</div>
                <div class="p-meta" id="pMeta">—</div>
            </div>
        </div>
        <div class="p-bar" id="pBar"><div class="p-fill" id="pFill"></div></div>
        <div class="p-time"><span id="pTime">0:00</span> / <span id="pDur">0:00</span></div>
        <button type="button" class="p-close" id="pClose">✕</button>
    </div>

    <audio id="audioPlayer"></audio>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">✎ Edit Voice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId">
                    <div class="mb-3">
                        <label class="form-label">Display Name / Sample Text</label>
                        <textarea class="form-control" id="editText" rows="3" placeholder="Sample text for this voice..."></textarea>
                        <div class="form-text">This text is used for preview generation and display.</div>
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
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-save" id="editSaveBtn">Save Changes</button>
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

    const table = new DataTable('#voicesTable', {
        order: [[1, 'asc']],
        pageLength: 25,
        stateSave: true,
        language: {
            info: 'Showing _START_ – _END_ of _TOTAL_ voices',
            infoEmpty: 'No voices found',
            infoFiltered: '(filtered from _MAX_)',
            emptyTable: 'No voices synced. Click "Sync Voices" to pull from Google.',
            zeroRecords: 'No matching voices',
        }
    });

    const fSearch = document.getElementById('fSearch');
    const fVendor = document.getElementById('fVendor');
    const fLang   = document.getElementById('fLang');
    const fCode   = document.getElementById('fCode');
    const fGender = document.getElementById('fGender');
    const fFormat = document.getElementById('fFormat');
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
        const items = [];
        if (fSearch.value) items.push({ l: `"${fSearch.value}"`, fn: () => { fSearch.value = ''; } });
        if (fVendor.value) items.push({ l: fVendor.value, fn: () => { fVendor.value = ''; } });
        if (fLang.value) items.push({ l: fLang.value, fn: () => { fLang.value = ''; fCode.innerHTML = '<option value="">All</option>'; fCode.disabled = true; } });
        if (fCode.value) items.push({ l: fCode.value, fn: () => { fCode.value = ''; } });
        if (fGender.value) items.push({ l: fGender.value, fn: () => { fGender.value = ''; } });
        if (fFormat.value) items.push({ l: fFormat.value.toUpperCase(), fn: () => { fFormat.value = ''; } });

        pills.innerHTML = '';
        items.forEach(i => {
            const el = document.createElement('span');
            el.className = 'vb-pill';
            el.innerHTML = `${i.l} <span class="x">✕</span>`;
            el.querySelector('.x').addEventListener('click', () => { i.fn(); applyFilters(); });
            pills.appendChild(el);
        });
    }

    fSearch.addEventListener('input', applyFilters);
    [fVendor, fGender, fFormat].forEach(el => el.addEventListener('change', applyFilters));

    fLang.addEventListener('change', async function () {
        fCode.innerHTML = '<option value="">All</option>';
        fCode.disabled = true;
        if (this.value) {
            try {
                const res = await fetch(`{{ route('voices.codes') }}?language_full=${encodeURIComponent(this.value)}`);
                const codes = await res.json();
                codes.forEach(c => { const o = document.createElement('option'); o.value = c; o.textContent = c; fCode.appendChild(o); });
                fCode.disabled = false;
            } catch (e) { console.error(e); }
        }
        applyFilters();
    });
    fCode.addEventListener('change', applyFilters);

    document.getElementById('fClear').addEventListener('click', () => {
        fSearch.value = ''; fVendor.value = ''; fLang.value = '';
        fCode.innerHTML = '<option value="">All</option>'; fCode.disabled = true;
        fGender.value = ''; fFormat.value = '';
        applyFilters();
    });

    // Audio player
    const audio = document.getElementById('audioPlayer');
    const ipEl = document.getElementById('inlinePlayer');
    const pName = document.getElementById('pName');
    const pMeta = document.getElementById('pMeta');
    const pAvatar = document.getElementById('pAvatar');
    const pTime = document.getElementById('pTime');
    const pDur = document.getElementById('pDur');
    const pFill = document.getElementById('pFill');
    const pBar = document.getElementById('pBar');
    let curBtn = null;

    function fmt(s) { return `${Math.floor(s/60)}:${Math.floor(s%60).toString().padStart(2,'0')}`; }

    audio.addEventListener('timeupdate', () => { pTime.textContent = fmt(audio.currentTime); if (audio.duration) pFill.style.width = (audio.currentTime/audio.duration*100)+'%'; });
    audio.addEventListener('loadedmetadata', () => { pDur.textContent = fmt(audio.duration); });
    audio.addEventListener('ended', () => { if (curBtn) { curBtn.classList.remove('playing'); curBtn.textContent = '▶'; curBtn.closest('tr')?.classList.remove('playing-row'); } curBtn = null; pFill.style.width = '0%'; });

    pBar.addEventListener('click', function(e) { if (!audio.duration) return; audio.currentTime = (e.clientX - this.getBoundingClientRect().left)/this.offsetWidth*audio.duration; });

    document.getElementById('pClose').addEventListener('click', () => {
        audio.pause(); audio.src = ''; ipEl.classList.remove('visible');
        if (curBtn) { curBtn.classList.remove('playing'); curBtn.textContent = '▶'; curBtn.closest('tr')?.classList.remove('playing-row'); }
        curBtn = null;
    });

    // Delegated preview
    document.getElementById('voicesTable').addEventListener('click', async function(e) {
        const btn = e.target.closest('.btnPreview');
        if (!btn) return;
        const tr = btn.closest('tr');
        const id = tr.dataset.id;
        const gender = tr.dataset.gender || 'neutral';

        if (curBtn && curBtn !== btn) { curBtn.classList.remove('playing'); curBtn.textContent = '▶'; curBtn.closest('tr')?.classList.remove('playing-row'); }

        btn.classList.add('playing'); btn.textContent = '⏳'; tr.classList.add('playing-row');
        curBtn = btn;

        pName.textContent = tr.querySelector('.vb-voice-name')?.childNodes[0]?.textContent.trim() || '—';
        pMeta.textContent = `${tr.querySelector('.vb-lang-name')?.textContent||''} · ${tr.querySelector('.vb-lang-code')?.textContent||''}`;
        const grads = { male:'linear-gradient(135deg,#3b82f6,#6c5ce7)', female:'linear-gradient(135deg,#ec4899,#f472b6)', neutral:'linear-gradient(135deg,#9ca3af,#d1d5db)' };
        pAvatar.style.background = grads[gender] || grads.neutral;
        pFill.style.width = '0%'; pTime.textContent = '0:00'; pDur.textContent = '0:00';
        ipEl.classList.add('visible');

        try {
            const res = await fetch(`{{ url('tts/voices') }}/${id}/preview`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            if (data.url) { audio.src = data.url; audio.play().catch(()=>{}); btn.textContent = '⏸'; }
            else throw new Error(data.error || 'No URL');
        } catch (err) {
            btn.classList.remove('playing'); btn.textContent = '▶'; tr.classList.remove('playing-row');
            ipEl.classList.remove('visible');
            alert('Preview failed: '+(err.message||'Error'));
        }
    });

    // Delegated edit
    const editModal = document.getElementById('editModal');
    const bsModal = typeof bootstrap !== 'undefined' ? new bootstrap.Modal(editModal) : null;

    document.getElementById('voicesTable').addEventListener('click', function(e) {
        const btn = e.target.closest('.btnEdit');
        if (!btn) return;
        const tr = btn.closest('tr');
        document.getElementById('editId').value = tr.dataset.id;
        document.getElementById('editText').value = tr.dataset.text || '';
        document.getElementById('editFormat').value = tr.dataset.format || 'mp3';
        if (bsModal) bsModal.show();
    });

    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('editId').value;
        const saveBtn = document.getElementById('editSaveBtn');
        saveBtn.textContent = 'Saving...'; saveBtn.disabled = true;
        try {
            const res = await fetch(`{{ url('tts/voices') }}/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ _method: 'PUT', voice_text: document.getElementById('editText').value, audio_format: document.getElementById('editFormat').value })
            });
            if (res.ok) {
                const tr = document.querySelector(`#voicesTable tr[data-id="${id}"]`);
                if (tr) { tr.dataset.text = document.getElementById('editText').value; tr.dataset.format = document.getElementById('editFormat').value; const b = tr.querySelector('.vb-fmt'); if (b) b.textContent = document.getElementById('editFormat').value.toUpperCase(); }
                if (bsModal) bsModal.hide();
            } else { const d = await res.json().catch(()=>({})); alert('Failed: '+(d.message||res.statusText)); }
        } catch { alert('Network error.'); }
        finally { saveBtn.textContent = 'Save Changes'; saveBtn.disabled = false; }
    });
});
</script>
@endpush