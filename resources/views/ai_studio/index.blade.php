@extends('layouts.app')

@section('page-title', 'AI Studio')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Video Preview --}}
    <div class="card mb-4">
        <div class="card-header"><strong>🎬 Background Video Preview</strong></div>
        <div class="card-body text-center">
            <div style="max-width: 720px; margin: auto; position: relative;">
                @if($videoPath)
                    <video id="videoPreview" width="100%" controls autoplay muted loop>
                        <source src="{{ $videoPath }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <p>No video selected or found.</p>
                @endif

                <video id="overlayVideo" autoplay loop muted playsinline
                    style="position:absolute; top:0; left:0; width:100%; pointer-events:none; z-index:10;">
                    <source src="{{ asset('storage/overlays/elite_talking.webm') }}" type="video/webm">
                </video>
            </div>
        </div>
    </div>

    {{-- Studio Form --}}
    <div class="card">
        <div class="card-header"><h5>🎛️ Studio Controls</h5></div>
        <div class="card-body">
            <form action="{{ route('ai_studio.generate') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Script (Optional if using MP3)</label>
                        <textarea name="tts_text" class="form-control" rows="4">{{ old('tts_text', session('ttsText')) }}</textarea>
                        @error('tts_text') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Select Voice</label>
                        <select name="voice" class="form-select">
                            <option value="">Default</option>
                            @foreach(['en-US-Wavenet-D','en-GB-Wavenet-A','en-AU-Wavenet-B','en-IN-Wavenet-C'] as $voice)
                                <option value="{{ $voice }}" {{ $selectedVoice == $voice ? 'selected' : '' }}>{{ $voice }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Use Existing Audio (MP3)</label>
                        <select name="mp3_audio" class="form-select">
                            <option value="">None</option>
                            @foreach($audios as $audio)
                                <option value="{{ $audio->id }}" {{ optional($selectedAudio)->id == $audio->id ? 'selected' : '' }}>
                                    {{ $audio->original_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Select Blender File</label>
                        <select name="blender_id" class="form-select">
                            <option value="">None</option>
                            @foreach($blenders as $blend)
                                <option value="{{ $blend->id }}" {{ optional($selectedBlender)->id == $blend->id ? 'selected' : '' }}>
                                    {{ $blend->original_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Upload New Blender File</label>
                        <input type="file" name="blender_file" class="form-control" accept=".glb,.fbx">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Select Background Video</label>
                        <select name="bg_video" class="form-select" id="bgVideoSelect">
                            <option value="">None</option>
                            @foreach($videos as $vid)
                                <option value="{{ $vid->id }}" data-filename="{{ $vid->filename }}" {{ optional($selectedVideo)->id == $vid->id ? 'selected' : '' }}>
                                    {{ $vid->original_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Mouth Animation</label>
                        <select name="mouth_mode" class="form-select">
                            <option value="auto" {{ session('mouthMode') === 'auto' ? 'selected' : '' }}>Auto</option>
                            <option value="loop" {{ session('mouthMode') === 'loop' ? 'selected' : '' }}>Loop</option>
                            <option value="none" {{ session('mouthMode') === 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Overlay Style</label>
                        <select name="overlay_style" class="form-select">
                            <option value="webm" {{ session('overlayStyle') === 'webm' ? 'selected' : '' }}>WebM</option>
                            <option value="3d" {{ session('overlayStyle') === '3d' ? 'selected' : '' }}>3D Canvas</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end justify-content-end">
                        <button class="btn btn-primary w-100">🎬 Generate</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('bgVideoSelect').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const filename = selected.getAttribute('data-filename');
        if (filename) {
            const video = document.getElementById('videoPreview');
            const source = video.querySelector('source');
            source.src = '/storage/' + filename;
            video.load();
        }
    });
</script>
@endpush
