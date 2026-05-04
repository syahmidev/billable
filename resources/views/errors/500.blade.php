@extends('errors.layout', ['title' => '500 Server Error'])

@section('content')
    <div class="code">500</div>
    <h1 class="title">Something went wrong</h1>
    <p class="desc">An unexpected error occurred on our end. We've been notified and are looking into it.</p>
    <a href="/" class="btn">Go home</a>
@endsection
