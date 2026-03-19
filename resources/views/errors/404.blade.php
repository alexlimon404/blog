@extends('layouts.app')

@section('title', 'Page Not Found')
@section('description', 'The page you are looking for could not be found.')

@section('content')
    <div class="text-center py-5">
        <h1 class="display-1 fw-bold text-muted">404</h1>
        <p class="fs-4 mb-4">The page you're looking for doesn't exist or has been moved.</p>
        <div>
            <a href="{{ url('/') }}" class="btn btn-primary me-2">Go to Homepage</a>
        </div>
    </div>
@endsection
