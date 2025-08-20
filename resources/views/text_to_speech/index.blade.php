@extends('layouts.app')

@section('page-title', 'Text to Speech')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-light text-dark">
                    <h5 class="mb-0">Text to Speech Generator</h5>
                </div>
                <div class="card-body">
                    <form id="ttsForm" action="{{ route('text_to_speech.generate') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="audio_name" class="form-label">Audio File Name (Optional)</label>
                            <input type="text" name="audio_name" id="audio_name" class="form-control" placeholder="e.g. welcome_message">
                        </div>

                        <div class="mb-3">
                            <label for="text" class="form-label">Enter Text</label>
                            <textarea name="text" id="text" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="language" class="form-label">Language</label>
                                <select name="language" id="language" class="form-select" required>
                                    <option value="en-US">English (US)</option>
                                    <option value="es-MX">Spanish (Mexico)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="voice" class="form-label">Voice</label>
                                <select name="voice" id="voice" class="form-select" required>
                                    <option value="en-US-Wavenet-D">US Male</option>
                                    <option value="en-US-Wavenet-F">US Female</option>
                                    <option value="es-MX-Wavenet-A">Mexican Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="button" id="previewBtn" class="btn btn-primary flex-fill">Preview</button>
                            <button type="button" id="deleteBtn" class="btn btn-danger flex-fill" disabled>Delete</button>
                            <button type="button" id="saveBtn" class="btn btn-success flex-fill" disabled>Save to File Manager</button>
                        </div>
                    </form>

                    <div class="mt-4" id="audioBox" style="display: none;">
                        <label class="form-label">Preview</label>
                        <audio controls id="ttsPlayer" class="w-100"></audio>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    let previewUrl = null;

    const previewBtn = document.getElementById('previewBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const saveBtn = document.getElementById('saveBtn');
    const player = document.getElementById('ttsPlayer');
    const audioBox = document.getElementById('audioBox');

    previewBtn?.addEventListener('click', function () {
        const form = document.getElementById('ttsForm');
        const formData = new FormData(form);

        previewBtn.disabled = true;
        previewBtn.innerText = 'Generating Preview...';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            previewUrl = data.url;

            player.src = previewUrl;
            player.load();
            player.play().catch(error => console.error('Auto-play failed:', error));

            audioBox.style.display = 'block';
            deleteBtn.disabled = false;
            saveBtn.disabled = false;

            previewBtn.innerText = 'Preview';
            previewBtn.disabled = false;
        })
        .catch(err => {
            alert('Error generating preview.');
            previewBtn.innerText = 'Preview';
            previewBtn.disabled = false;
        });
    });

    deleteBtn?.addEventListener('click', function () {
        player.pause();
        player.src = '';
        previewUrl = null;
        audioBox.style.display = 'none';
        deleteBtn.disabled = true;
        saveBtn.disabled = true;
    });

    saveBtn?.addEventListener('click', function () {
        if (!previewUrl) return;

        fetch('{{ route('file_manager.save_from_tts') }}', {
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
            alert('✅ Saved to File Manager as Audio!');
        })
        .catch(err => {
            alert('❌ Failed to save.');
        });
    });
});
</script>
@endpush
