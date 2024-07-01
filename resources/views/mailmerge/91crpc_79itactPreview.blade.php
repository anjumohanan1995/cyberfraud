<!-- resources/views/mailmerge/preview.blade.php -->

@extends('layouts.app')


@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
    <div class="container" style="background-color: #ffffff; padding: 20px;">
        <h2>Preview</h2>

        {{-- Display your preview data --}}
        <form action="{{ route('send-email') }}" method="POST">
            @csrf

            {{-- Sub: Notice U/s 91 CrPC & 79(3)(b) of IT Act --}}
            <p><strong>Sub:</strong> {{ $sub }}</p>

            {{-- Salutation: Team Register name --}}
            <p><strong>Salutation:</strong> {{ $salutation }}</p>

            {{-- Content of the notice --}}
            <p>{!! nl2br(e($compiledContent)) !!}</p>

            {{-- Hidden field for content --}}
            <input type="hidden" name="sub" value="{{ $sub }}">
            <input type="hidden" name="salutation" value="{{ $salutation }}">
            <input type="hidden" name="compiledContent" value="{{ $compiledContent }}">
            <input type="hidden" name="mongo_id" value="{{ $mongo_id }}">

            {{-- Submit button --}}
            <button type="submit" class="btn btn-primary">Send Email</button>
        </form>
    </div>
@endsection
