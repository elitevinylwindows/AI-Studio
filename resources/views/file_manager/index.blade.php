@extends('layouts.app')

@section('page-title', 'File Manager')

@section('content')
<div class="container mt-4">
    <h4>File Manager</h4>

    <form action="{{ route('file_manager.upload') }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="row g-2">
            <div class="col-md-4">
                <input type="file" name="file" class="form-control" required>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select" required>
                    <option value="">Select Category</option>
                    <option value="audio">Audio</option>
                    <option value="video">Video</option>
                    <option value="blender">Blender</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100">Upload</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="fileTabs">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#audios">Audios</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#videos">Videos</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#blender">Blender Files</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="audios">
                    <ul class="list-group">
                        @foreach ($audios as $file)
                            <li class="list-group-item d-flex justify-content-between">
                                {{ $file->original_name }}
                                <a href="{{ asset('storage/' . $file->filename) }}" target="_blank">Download</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="tab-pane fade" id="videos">
                    <ul class="list-group">
                        @foreach ($videos as $file)
                            <li class="list-group-item d-flex justify-content-between">
                                {{ $file->original_name }}
                                <a href="{{ asset('storage/' . $file->filename) }}" target="_blank">Download</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="tab-pane fade" id="blender">
                    <ul class="list-group">
                        @foreach ($blenders as $file)
                            <li class="list-group-item d-flex justify-content-between">
                                {{ $file->original_name }}
                                <a href="{{ asset('storage/' . $file->filename) }}" target="_blank">Download</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
