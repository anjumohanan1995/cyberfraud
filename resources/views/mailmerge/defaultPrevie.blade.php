<!-- resources/views/mailmerge/preview.blade.php -->

@extends('layouts.app') {{-- Assuming you have a layout --}}

@section('content')
    <div class="container">
        <h2>Mail Merge Preview</h2>

        {{-- Display your preview data --}}
        <div>
            {{-- Example of displaying data --}}
            <p>ID: {{ $data['id'] }}</p>
            <p>Selected Option: {{ $data['option'] }}</p>
            <p>Somthing went wrong</p>
            {{-- <p>Acknowledgement No: {{ $data['acknowledgement_no'] }}</p> --}}
            {{-- Add more fields as needed --}}
        </div>
    </div>
@endsection
