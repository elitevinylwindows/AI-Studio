@extends('layouts.app')

@section('page-title', 'Voices')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Voices</h4>
        <small class="text-muted">Click the ✎ to edit a voice title. Press Enter to save.</small>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
        @foreach ($voices as $voice)
        @php
            $langCode = $voice['languageCodes'][0] ?? 'en-US';
            $countryCode = explode('-', $langCode)[1] ?? 'US';
            $voiceName = $voice['name'];
            $displayTitle = $voice['display_title'] ?? $voiceName;
            // Mock details - replace with your actual data
            $details = [
                'Middle-Aged',
                'Friendly',
                'Podcast',
                'Elevenlabs',
                'Mutli...'
            ];
        @endphp

        <div class="col">
            <div class="card voice-card h-100 position-relative border-0 shadow-sm bg-white">
                <!-- Flag -->
                <div class="position-absolute top-0 end-0 p-2">
                    <img src="https://flagcdn.com/h20/{{ strtolower($countryCode) }}.png" alt="Flag" height="16" class="opacity-75">
                </div>

                <!-- Favorite Star -->
                <div class="position-absolute top-0 start-0 p-2">
                    <i class="far fa-star text-warning fs-5 favorite-toggle" style="cursor:pointer;"></i>
                </div>

                <div class="card-body d-flex flex-column text-center p-3">
                    <!-- Round thumbnail -->
                    <div class="mx-auto mb-3 rounded-circle voice-thumb d-flex align-items-center justify-content-center bg-light">
                        <i class="fas fa-user fs-4 text-muted"></i>
                    </div>

                    <!-- Editable Title -->
                    <div class="d-flex justify-content-center align-items-center gap-2 mb-1">
                        <span class="fw-semibold voice-title"
                              contenteditable="false"
                              data-voice="{{ $voiceName }}"
                              data-original="{{ $displayTitle }}"
                              spellcheck="false">{{ $displayTitle }}</span>

                        <button type="button"
                                class="btn btn-sm btn-light border-0 edit-title-btn"
                                data-bs-toggle="tooltip"
                                title="Edit title"
                                aria-label="Edit title">
                            <i class="fas fa-pen"></i>
                        </button>
                    </div>

                    <!-- Voice Details -->
                    <div class="text-muted small mb-3">
                        {{ implode(' · ', $details) }}
                    </div>

                    <!-- Play Button -->
                    <button class="btn btn-outline-primary rounded-pill play-btn mt-auto"
                            data-voice="{{ $voiceName }}"
                            aria-label="Play preview">
                        <i class="fas fa-play me-1"></i> Play
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<audio id="voicePreviewPlayer" controls style="display:none;"></audio>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('styles')
<style>
.voice-card {
    transition: transform .18s ease, box-shadow .18s ease;
    border-radius: 12px !important;
    overflow: hidden;
}
.voice-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
}

/* Round thumbnail */
.voice-thumb {
    width: 64px;
    height: 64px;
    background: #f4f6f8;
    border: 1px solid #e9ecef;
}

/* Play button */
.play-btn {
    padding: 0.35rem 1rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}
.play-btn:hover {
    background-color: var(--bs-primary);
    color: white;
}

/* Editable title UX */
.voice-title[contenteditable="true"] {
    outline: 2px solid rgba(13,110,253,.35);
    border-radius: .375rem;
    padding: 0 .35rem;
    background: #f8fbff;
}
.edit-title-btn {
    opacity: 0;
    transition: opacity 0.2s ease;
}
.voice-card:hover .edit-title-btn,
.voice-title[contenteditable="true"] + .edit-title-btn {
    opacity: 1;
}
.edit-title-btn i { pointer-events: none; }

/* Favorite star */
.favorite-toggle {
    opacity: 0.5;
    transition: all 0.2s ease;
}
.favorite-toggle:hover,
.favorite-toggle.fas {
    opacity: 1;
    transform: scale(1.1);
}

/* Make sure tooltips show above overlay */
.tooltip { z-index: 1085; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bootstrap tooltips
    if (window.bootstrap) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) })
    }

    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Play button handler
    document.querySelectorAll('.play-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const voiceName = this.dataset.voice;
            fetch('{{ route("voices.preview") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({ voice: voiceName })
            })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(data => {
                const audio = document.getElementById('voicePreviewPlayer');
                audio.src = data.url;
                audio.style.display = 'block';
                audio.play();
            })
            .catch(() => alert('Preview failed. Please try again.'));
        });
    });

    // Favorite toggle
    document.querySelectorAll('.favorite-toggle').forEach(star => {
        star.addEventListener('click', function () {
            const isFavorite = this.classList.contains('fas');
            const voiceName = this.closest('.voice-card').querySelector('.voice-title').dataset.voice;
            
            fetch('{{ route("voices.favorite") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    voice: voiceName,
                    favorite: !isFavorite
                })
            })
            .then(r => {
                if (r.ok) {
                    this.classList.toggle('fas');
                    this.classList.toggle('far');
                } else {
                    throw new Error('Failed to update favorite');
                }
            })
            .catch(() => alert('Failed to update favorite status'));
        });
    });

    // Title editing functions
    function cancelEdit(titleEl) {
        titleEl.textContent = titleEl.dataset.original;
        titleEl.setAttribute('contenteditable', 'false');
    }

    function saveTitle(titleEl, buttonEl) {
        const newTitle = titleEl.textContent.trim();
        const original = titleEl.dataset.original;
        const voiceName = titleEl.dataset.voice;

        if (newTitle === original) {
            titleEl.setAttribute('contenteditable', 'false');
            if (buttonEl) {
                buttonEl.innerHTML = '<i class="fas fa-pen"></i>';
                buttonEl.setAttribute('title', 'Edit title');
            }
            return;
        }

        fetch('{{ route("voices.rename") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
            body: JSON.stringify({ voice: voiceName, title: newTitle })
        })
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(data => {
            titleEl.dataset.original = data.title ?? newTitle;
            titleEl.textContent = data.title ?? newTitle;
            titleEl.setAttribute('contenteditable', 'false');
            if (buttonEl) {
                buttonEl.innerHTML = '<i class="fas fa-pen"></i>';
                buttonEl.setAttribute('title', 'Edit title');
            }
        })
        .catch(() => {
            alert('Could not save title. Reverting.');
            cancelEdit(titleEl);
            if (buttonEl) {
                buttonEl.innerHTML = '<i class="fas fa-pen"></i>';
                buttonEl.setAttribute('title', 'Edit title');
            }
        });
    }

    // Edit button handlers
    document.querySelectorAll('.edit-title-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const wrap = this.closest('.card-body');
            const titleEl = wrap.querySelector('.voice-title');
            const isEditing = titleEl.getAttribute('contenteditable') === 'true';

            if (!isEditing) {
                titleEl.setAttribute('contenteditable', 'true');
                titleEl.focus();
                const range = document.createRange();
                range.selectNodeContents(titleEl);
                range.collapse(false);
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);

                this.innerHTML = '<i class="fas fa-check"></i>';
                this.setAttribute('title', 'Save');
            } else {
                saveTitle(titleEl, this);
            }
        });
    });

    // Title field handlers
    document.querySelectorAll('.voice-title').forEach(el => {
        el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const btn = el.closest('.card-body').querySelector('.edit-title-btn');
                saveTitle(el, btn);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEdit(el);
                const btn = el.closest('.card-body').querySelector('.edit-title-btn');
                btn.innerHTML = '<i class="fas fa-pen"></i>';
                btn.setAttribute('title', 'Edit title');
            }
        });

        el.addEventListener('blur', function () {
            if (el.getAttribute('contenteditable') === 'true') {
                const btn = el.closest('.card-body').querySelector('.edit-title-btn');
                saveTitle(el, btn);
            }
        });
    });
});
</script>
@endpush