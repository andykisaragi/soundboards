@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Search Results</h1>
        <p>Found <strong>{{ $data['count'] ?? 0 }}</strong> sounds.</p>

        <a href="{{ route('freesound.index') }}" class="btn btn-outline-secondary mb-3">← New search</a>

        <div class="row">
            @forelse($sounds as $sound)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        @if(isset($sound['images']['waveform_m']))
                            <img src="{{ $sound['images']['waveform_m'] }}" class="card-img-top" alt="Waveform">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $sound['name'] ?? 'Untitled' }}</h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    by {{ $sound['username'] ?? 'Unknown' }}
                                    @if(isset($sound['duration']))
                                        · {{ round($sound['duration'], 1) }}s
                                    @endif
                                </small>
                            </p>
                            @if(isset($sound['tags']))
                                <p>
                                    @foreach($sound['tags'] as $tag)
                                        <span class="badge bg-secondary">{{ $tag }}</span>
                                    @endforeach
                                </p>
                            @endif
                            @if(isset($sound['avg_rating']))
                                <p>⭐ {{ number_format($sound['avg_rating'], 1) }}/5</p>
                            @endif
                            <a href="{{ route('freesound.sound.show', $sound['id']) }}"
                               class="btn btn-sm btn-primary">Details</a>
                            @if(isset($sound['previews']['preview-lq-mp3']))
                                <audio controls class="w-100 mt-2">
                                    <source src="{{ $sound['previews']['preview-lq-mp3'] }}" type="audio/mpeg">
                                </audio>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No sounds found. Try a different query!</div>
                </div>
            @endforelse
        </div>

        {{ $sounds->links() }}
    </div>
@endsection
