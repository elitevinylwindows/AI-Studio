@extends('layouts.app')
@section('page-title', 'AI Studio Timeline')

@section('content')
<div class="container py-4">
    {{-- Preview Area --}}
    <div class="card mb-4">
        <div class="card-header"><h5>🎥 Preview Video</h5></div>
        <div class="card-body text-center">
            <div class="position-relative" style="max-width: 720px; margin: auto;">
                <video id="bgVideo" controls width="100%">
                    <source src="{{ asset('storage/videos/user_video.mp4') }}" type="video/mp4">
                    Your browser does not support video.
                </video>

                <video id="eliteOverlay" muted loop playsinline
                    style="position: absolute; top: 0; left: 0; width: 100%; pointer-events: none; z-index: 10;">
                    <source src="{{ asset('storage/overlays/elite_talking.webm') }}" type="video/webm">
                </video>
            </div>
        </div>
    </div>

    {{-- Timeline Controls --}}
    <div class="card mb-4">
        <div class="card-header"><h5>🕒 Timeline & Overlay Control</h5></div>
        <div class="card-body">
            <div id="waveform" class="mb-3"></div>

            <label>Mr. Elite Overlay Start Time (seconds):</label>
            <input type="range" id="overlayStart" min="0" max="60" step="0.1" value="0" class="form-range">
            <div>Start Time: <span id="startTimeDisplay">0.0s</span></div>

            <button class="btn btn-success mt-3" onclick="startPreview()">▶ Preview Sync</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/wavesurfer.js"></script>
<script>
    let video = document.getElementById('bgVideo');
    let overlay = document.getElementById('eliteOverlay');
    let startInput = document.getElementById('overlayStart');
    let startTimeDisplay = document.getElementById('startTimeDisplay');

    // Wavesurfer timeline for TTS audio
    let wavesurfer = WaveSurfer.create({
        container: '#waveform',
        waveColor: '#ddd',
        progressColor: '#0d6efd',
        height: 80
    });

    wavesurfer.load("{{ asset('storage/audio/tts_voice.mp3') }}");

    // Show selected start time
    startInput.addEventListener('input', function () {
        startTimeDisplay.innerText = `${this.value}s`;
    });

    function startPreview() {
        video.currentTime = 0;
        overlay.currentTime = 0;
        overlay.style.display = 'none';
        video.play();
        wavesurfer.play();

        // Delay Mr. Elite overlay
        setTimeout(() => {
            overlay.style.display = 'block';
            overlay.play();
        }, parseFloat(startInput.value) * 1000);
    }
</script>
@endpush
