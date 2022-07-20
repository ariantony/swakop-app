@extends('errors::layout')

@section('title', '403 Forbidden')
@section('code', '403')
@section('message', 'Anda tidak memiliki akses ke halaman ini')
@section('action')
  <a href="{{ route('dashboard') }}" class="text-xs"> Silahkan kembali ke dashboard</a>
@endsection