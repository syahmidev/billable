@extends('errors.layout', ['title' => '403 Forbidden'])

@section('content')
    <div class="code">403</div>
    <h1 class="title">Access denied</h1>
    <p class="desc">You don't have permission to view this page.</p>
    <a href="/" class="btn">Go home</a>
@endsection
