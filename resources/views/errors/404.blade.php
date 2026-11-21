@extends('layouts.public')
@section('title', 'Page not found')
@section('content')
    <section class="section">
        <div class="container notfound">
            <p class="eyebrow">Error 404</p>
            <h1 class="section__title">Page not found.</h1>
            <p class="section__lead">The page you are looking for does not exist or is no longer available.</p>
            <a href="{{ route('home') }}" class="btn btn--dark">Back to Home <x-icon name="arrow-right" /></a>
        </div>
    </section>
@endsection
