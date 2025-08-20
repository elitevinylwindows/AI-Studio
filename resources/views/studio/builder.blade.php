@extends('layouts.app')

@section('page-title', 'Studio')

@section('content')
<div class="container-fluid px-0" x-data="studioApp({{ $project->id }}, @js($project->title), @js($state) )">
  {{-- Top bar --}}
  <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-dark text-white">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('dashboard') }}" class="text-white-50 text-decoration-none">&larr;</a>
      <div contenteditable="true" class="fw-semibold" x-text="title" @blur="title = $event.target.textContent.trim()"></div>
      <span class="text-success ms-2" x-show="saved">Saved</span>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-sm btn-outline-light" @click="preview()">Preview</button>
      <button class="btn btn-sm btn-outline-light" @click="share()">Share</button>
      <button class="btn btn-sm btn-primary" @click="exportVideo()">Export</button>
    </div>
  </div>

  <div class="d-flex" style="min-height: calc(100vh - 56px);">
    {{-- Left rail --}}
    <div class="bg-body-tertiary border-end" style="width: 320px;">
      <div class="p-3">
        <div class="btn-group w-100 mb-3" role="group">
          <button class="btn btn-outline-secondary" :class="tab==='script' && 'active'" @click="tab='script'">Script</button>
          <button class="btn btn-outline-secondary" :class="tab==='avatar' && 'active'" @click="tab='avatar'">Avatar</button>
          <button class="btn btn-outline-secondary" :class="tab==='assets' && 'active'" @click="tab='assets'">Assets</button>
        </div>

        {{-- Script tab --}}
        <div x-show="tab==='script'">
          <label class="form-label">Scene Script</label>
          <textarea class="form-control" rows="5" x-model="currentScene().script" placeholder="Type your script..."></textarea>
          <label class="form-label mt-3">Voice (Google TTS name)</label>
          <input type="text" class="form-control" x-model="currentScene().voice" placeholder="e.g. en-US-Wavenet-D">
          <div class="mt-3 d-grid">
            <button class="btn btn-primary" @click="generateTTS()">Generate Voice</button>
          </div>
        </div>

        {{-- Avatar tab --}}
        <div x-show="tab==='avatar'">
          <div class="mb-2 small text-muted">Upload a portrait to use for lip‑sync (Wav2Lip later).</div>
          <input type="file" class="form-control" accept=".png,.jpg,.jpeg"
                 @change="uploadFile($event,'avatar_image')">
          <div class="mt-3" x-show="currentScene().avatar_image">
            <img :src="fileUrl(currentScene().avatar_image)" class="img-fluid rounded border">
          </div>
        </div>

        {{-- Assets tab --}}
        <div x-show="tab==='assets'">
          <label class="form-label">Background Video</label>
          <input type="file" class="form-control" accept=".mp4,.webm"
                 @change="uploadFile($event,'bg_video')">
          <div class="mt-3" x-show="currentScene().bg_video">
            <video :src="fileUrl(currentScene().bg_video)" class="w-100 rounded border" controls></video>
          </div>
        </div>
      </div>
    </div>

   {{-- CENTER PLAYER --}}
<div class="p-3">
  <div class="bg-black rounded-3 position-relative mx-auto" style="max-width:960px; aspect-ratio:16/9;">
    
    {{-- Background video (use <source> and x-ref to force reload) --}}
    <video x-ref="playerBg"
           x-show="currentScene().bg_video"
           class="w-100 h-100" style="object-fit:cover;" controls muted>
      <source :src="fileUrl(currentScene().bg_video)" type="video/mp4">
    </video>

    {{-- Fallback message when no bg video selected --}}
    <div x-show="!currentScene().bg_video"
         class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50">
      No background video selected.
    </div>

    {{-- Avatar overlay (shows as soon as we have a path) --}}
    <img x-show="currentScene().avatar_image"
         :src="fileUrl(currentScene().avatar_image)"
         class="position-absolute border rounded-circle"
         style="bottom:24px; left:24px; width:120px; height:120px; object-fit:cover;">

    {{-- Caption strip (preview of script) --}}
    <div class="position-absolute text-white px-3 py-1 rounded"
         style="bottom:16px; left:50%; transform:translateX(-50%); background:rgba(0,0,0,.45);"
         x-text="currentScene().script"></div>
  </div>
</div>


      {{-- Timeline --}}
      <div class="p-2 border-top bg-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="fw-semibold">Timeline</div>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" @click="addScene()">+ New Scene</button>
            <button class="btn btn-sm btn-outline-danger" @click="removeScene()" x-show="scenes.length>1">Delete Scene</button>
            <button class="btn btn-sm btn-outline-primary" @click="save()">Save</button>
          </div>
        </div>

        <div class="d-flex gap-2 overflow-auto pb-2">
          <template x-for="(s,idx) in scenes" :key="s.id">
            <div class="border rounded-3 p-2 bg-white" :class="idx===activeIndex && 'border-primary'"
                 style="min-width: 180px; cursor:pointer;" @click="activeIndex=idx">
              <div class="small text-muted">Scene <span x-text="idx+1"></span></div>
              <div class="ratio ratio-16x9 bg-light rounded mb-2">
                <template x-if="s.bg_video">
                  <video :src="fileUrl(s.bg_video)" muted></video>
                </template>
              </div>
              <div class="small text-truncate" x-text="(s.script||'No script')"></div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>

  {{-- hidden audio element for previewing TTS --}}
  <audio id="sceneAudio" preload="auto"></audio>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
  body { background: #0e0f13; }               /* dark app chrome */
  .bg-body, .bg-body-tertiary { background: #14161e !important; color: #eaeef7; }
  .border, .border-top, .border-bottom { border-color: #232738 !important; }
  .card, .bg-white { background: #1a1d27 !important; color: #dce2f1; }
  .form-control, .form-select, .btn-outline-secondary { background: #0f1117; color:#dce2f1; border-color:#262b3e; }
  .btn-primary { background: #6b61ff; border-color:#6b61ff; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function studioApp(projectId, initialTitle, initialState){
  return {
    projectId,
    title: initialTitle,
    saved: false,
    tab: 'script',
    scenes: initialState.scenes?.length ? initialState.scenes : [{
      id: crypto.randomUUID(), script: '', voice: '', bg_video: null, avatar_image: null, audio: null, config:{}, duration: 0
    }],
    activeIndex: 0,

    currentScene(){ return this.scenes[this.activeIndex]; },
    fileUrl(path){ return path ? ('/storage/' + path) : ''; },

    addScene(){
      this.scenes.push({ id: crypto.randomUUID(), script:'', voice:'', bg_video:null, avatar_image:null, audio:null, config:{}, duration:0 });
      this.activeIndex = this.scenes.length - 1;
    },
    removeScene(){
      if(this.scenes.length<=1) return;
      this.scenes.splice(this.activeIndex,1);
      this.activeIndex = Math.max(0, this.activeIndex-1);
    },

    async uploadFile(ev, type){
  const file = ev.target.files[0];
  if(!file) return;

  const fd = new FormData();
  fd.append('type', type);                     // 'bg_video' or 'avatar_image'
  fd.append('file', file);

  const res = await fetch('{{ route("studio.upload") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: fd
  });
  const data = await res.json();
  console.log('Upload result:', type, data);   // DEBUG

  if(!data.ok){
    alert('Upload failed'); 
    return;
  }

  // Save storage path to scene
  this.currentScene()[type] = data.path;
  this.saved = false;

  // If it's the video, reload the <video> element so the source is picked up
  if(type === 'bg_video'){
    this.$nextTick(()=>{
      if(this.$refs.playerBg){
        // update <source> src manually in case the browser cached it
        const srcEl = this.$refs.playerBg.querySelector('source');
        if(srcEl) srcEl.src = this.fileUrl(this.currentScene().bg_video);
        this.$refs.playerBg.load();
      }
    });
  }
},


    async generateTTS(){
      // Reuse your existing endpoint for TTS (text_to_speech.generate)
      const s = this.currentScene();
      if(!(s.script && s.voice)){ alert('Enter script and voice (e.g., en-US-Wavenet-D)'); return; }

      const fd = new FormData();
      fd.append('_token','{{ csrf_token() }}');
      fd.append('text', s.script);
      fd.append('language', s.voice.slice(0,5));
      fd.append('voice', s.voice);

      const res = await fetch('{{ route("text_to_speech.generate") }}', { method:'POST', body: fd });
      const data = await res.json();
      if(data.url){
        // Convert asset URL back to storage path for saving inside state
        const rel = data.url.split('/storage/')[1];
        s.audio = rel ? rel : null;

        // Preview audio
        const a = document.getElementById('sceneAudio');
        a.src = data.url; a.play();
        this.saved = false;
      } else {
        alert('TTS failed');
      }
    },

    async save(){
      const payload = {
        project_id: this.projectId,
        title: this.title || 'Untitled Project',
        state: { scenes: this.scenes, language: 'en', fps: 30, canvas:{w:1280,h:720} }
      };
      const res = await fetch('{{ route("studio.save") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      this.saved = !!data.ok;
      setTimeout(()=>this.saved=false, 2000);
    },

    preview(){
      // Simple: play bg video + audio of current scene
      const v = document.getElementById('playerBg');
      const a = document.getElementById('sceneAudio');
      const s = this.currentScene();
      if(s.bg_video) v.src = this.fileUrl(s.bg_video);
      if(s.audio)   a.src = this.fileUrl(s.audio);
      v.play(); a.play();
    },

    async exportVideo(){
      const res = await fetch('{{ route("studio.export") }}', {
        method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
      });
      const data = await res.json();
      alert(data.message || 'Export requested.');
      if(data.url) window.open(data.url, '_blank');
    }
  }
}
</script>
@endpush
