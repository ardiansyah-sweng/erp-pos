@extends('layouts.app')

@section('title', 'Schedule')

@section('content')
<form action="{{ route('jalankan-schedule') }}" method="post" class="p-6">
    @csrf
    <button type="submit" class="rounded-xl bg-cyan-400 px-6 py-3 font-semibold text-slate-950 hover:bg-cyan-300">Jalankan Schedule</button>
</form>
@endsection
