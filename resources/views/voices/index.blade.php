@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Activated Google TTS Voices</h5>
            <a href="{{ route('voices.sync') }}" class="btn btn-primary">Sync Voices</a>
        </div>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Avatar</th>
                    <th>Vendor</th>
                    <th>Language</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Voice ID</th>
                    <th>Gender</th>
                    <th>Engine</th>
                    <th>Sample</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voices as $voice)
                <tr>
                    <td><span class="badge bg-success">{{ $voice->status }}</span></td>
                    <td><img src="{{ $voice->avatar_url ?? 'https://ui-avatars.com/api/?name='.$voice->voice_name }}" width="40" class="rounded-circle"></td>
                    <td>{{ $voice->vendor }}</td>
                    <td>{{ $voice->language }}</td>
                    <td>{{ $voice->language_code }}</td>
                    <td>{{ $voice->voice_name }}</td>
                    <td>{{ $voice->voice_id }}</td>
                    <td>{{ $voice->gender }}</td>
                    <td><span class="badge bg-primary">{{ $voice->voice_engine }}</span></td>
                    <td>
                        <button class="btn btn-light btn-sm">
                            <i class="ti ti-play"></i>
                        </button>
                    </td>
                    <td>{{ $voice->updated_at }}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-success"><i class="ti ti-check"></i></a>
                        <a href="#" class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></a>
                        <a href="#" class="btn btn-sm btn-danger"><i class="ti ti-x"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
