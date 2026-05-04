@extends('errors.layout', ['title' => '404 Not Found'])

@section('content')
    <div class="code">404</div>
    <h1 class="title">Page not found</h1>
    <p class="desc">The page you're looking for doesn't exist or has been moved.</p>
    <a href="/" class="btn">Go home</a>
@endsection
