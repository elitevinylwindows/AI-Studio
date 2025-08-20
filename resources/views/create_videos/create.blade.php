{{-- resources/views/create_videos/create.blade.php --}}

{{-- Trigger button (can be placed anywhere) --}}
<a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createVideoModal">
    Create Video
</a>

{{-- Create Video Modal --}}
<div class="modal fade" id="createVideoModal" tabindex="-1" aria-labelledby="createVideoLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold" id="createVideoLabel">Create Video</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body pt-0">
        <div class="row g-0">
          {{-- Left Menu --}}
          <div class="col-12 col-md-3">
            <div class="cv-rail rounded-3 p-2">
              <ul class="list-unstyled m-0">
                <li><button class="cv-item active" data-pane="scratch">Start from scratch</button></li>
                <li><button class="cv-item" data-pane="translate">Translate a video</button></li>
                <li><button class="cv-item" data-pane="template">Use a template</button></li>
                <li><button class="cv-item" data-pane="ppt">Upload a PPT/PDF</button></li>
                <li><button class="cv-item" data-pane="script">Generate script with AI</button></li>
                <li><button class="cv-item" data-pane="pdfvideo">PDF to video (Beta)</button></li>
              </ul>
            </div>
          </div>

          {{-- Right Content --}}
          <div class="col-12 col-md-9 ps-md-4 mt-4 mt-md-0">
            {{-- Pane: Scratch --}}
            <div class="cv-pane" id="pane-scratch">
              <h5 class="fw-semibold mb-1">Start from scratch</h5>
              <div class="text-muted mb-4">Which orientation do you want to create a video in?</div>

              <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-6">
                  <div class="cv-card text-center">
                    <div class="cv-box ratio ratio-9x16 d-flex align-items-center justify-content-center">
                      <i class="fa-regular fa-circle-play fs-3"></i>
                    </div>
                    <a href="{{ route('videos.create', ['orientation' => 'portrait']) }}" class="btn btn-primary mt-3">
                      Create portrait video
                    </a>
                  </div>
                </div>

                <div class="col-12 col-lg-6">
                  <div class="cv-card text-center">
                    <div class="cv-box ratio ratio-16x9 d-flex align-items-center justify-content-center">
                      <i class="fa-regular fa-circle-play fs-3"></i>
                    </div>
                    <a href="{{ route('videos.create', ['orientation' => 'landscape']) }}" class="btn btn-primary mt-3">
                      Create landscape video
                    </a>
                  </div>
                </div>
              </div>
            </div>

            {{-- Other Panes --}}
            <div class="cv-pane d-none" id="pane-translate">
              <h5 class="fw-semibold mb-1">Translate a video</h5>
              <p class="text-muted">Upload a video and choose the target language.</p>
            </div>

            <div class="cv-pane d-none" id="pane-template">
              <h5 class="fw-semibold mb-1">Use a template</h5>
              <p class="text-muted">Browse templates for promos, explainers, and more.</p>
            </div>

            <div class="cv-pane d-none" id="pane-ppt">
              <h5 class="fw-semibold mb-1">Upload a PPT/PDF</h5>
              <p class="text-muted">We’ll turn your slides into scenes automatically.</p>
            </div>

            <div class="cv-pane d-none" id="pane-script">
              <h5 class="fw-semibold mb-1">Generate script with AI</h5>
              <p class="text-muted">Start with a prompt; we’ll create a script and scenes.</p>
            </div>

            <div class="cv-pane d-none" id="pane-pdfvideo">
              <h5 class="fw-semibold mb-1">PDF to video <span class="badge bg-secondary">Beta</span></h5>
              <p class="text-muted">Experimental: convert a PDF into a storyboarded video.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
.cv-rail {
    background: linear-gradient(180deg, #f8fafc, #ffffff);
    border: 1px solid #eef1f4;
}
.cv-item {
    width: 100%;
    text-align: left;
    padding: .65rem .75rem;
    border: 0;
    background: transparent;
    border-radius: .5rem;
    font-weight: 500;
    color: #364152;
}
.cv-item:hover { background: #f3f6fa; }
.cv-item.active { background: #ecebff; color: #4f46e5; }
.cv-box {
    border: 2px dashed #c9cfe0;
    border-radius: .75rem;
    background: #f6f7fb;
    color: #777;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('#createVideoModal .cv-item').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      document.querySelectorAll('#createVideoModal .cv-item').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');

      const pane = btn.getAttribute('data-pane');
      document.querySelectorAll('#createVideoModal .cv-pane').forEach(p=>p.classList.add('d-none'));
      document.getElementById('pane-'+pane).classList.remove('d-none');
    });
  });
});
</script>
@endpush
