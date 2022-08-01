@extends('errors::layout')

@section('title', '500 Server Error')
@section('code', '500')
@section('message', 'Jika anda melihat halaman ini, harap refresh browser.')
@section('action')
  <a href="{{ route('dashboard') }}" class="text-xs"> Refresh</a>
@endsection