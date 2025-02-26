@extends('layouts.app')

@section('title', 'Detail Jadwal Pengujian')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Jadwal Pengujian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            <a href="{{ route('test_schedules.index') }}">Daftar Jadwal</a>
        </li>
        <li class="breadcrumb-item active">Detail Jadwal</li>
    </ol>

    <div class="card">
        <div class="card-header">
            <h5>{{ $schedule->test_name }}</h5>
        </div>
        <div class="card-body">
            <p><strong>Waktu Mulai:</strong> {{ $schedule->start_time }}</p>
            <p><strong>Waktu Selesai:</strong> {{ $schedule->end_time }}</p>
            <p><strong>Status:</strong> <span class="badge bg-warning">{{ $schedule->status }}</span></p>
            <p><strong>Gambar:</strong></p>
            @if ($schedule->image_path)
                <img src="{{ asset('storage/' . $schedule->image_path) }}" alt="Gambar Jadwal" style="max-width: 100%; height: auto;">
            @else
                <p>Tidak ada gambar</p>
            @endif

            <p><strong>CC:</strong> {{ $schedule->cc ?? 'Tidak Tersedia' }}</p>
            <p><strong>RH:</strong> {{ $schedule->rh ?? 'Tidak Tersedia' }}</p>
            <p><strong>Voltase:</strong> {{ $schedule->voltase ?? 'Tidak Tersedia' }}</p>

            <h5 class="mt-4">Subitems</h5>
            @foreach ($schedule->items as $item)
                <div class="card mb-3">
                    <div class="card-body">
                        <h6>{{ $item->nama_subitem }}</h6>
                        <p><strong>Waktu Mulai:</strong> {{ $item->start_time }}</p>
                        <p><strong>Waktu Selesai:</strong> {{ $item->end_time }}</p>
                        <p><strong>Orang:</strong> {{ $item->user->name }}</p>
                        <p><strong>Status:</strong> {{ $item->status }}</p>

                         <!-- Menampilkan cc, rh, dan voltase pada item -->
                         <p><strong>CC:</strong> {{ $item->cc ?? 'Tidak Tersedia' }}</p>
                         <p><strong>RH:</strong> {{ $item->rh ?? 'Tidak Tersedia' }}</p>
                         <p><strong>Voltase:</strong> {{ $item->voltase ?? 'Tidak Tersedia' }}</p>

                        <p><strong>Gambar Subitem:</strong></p>
                        @if ($item->image_detail)
                            @php
                                $imagePaths = json_decode($item->image_detail); // Mengubah JSON menjadi array
                            @endphp

                            @foreach ($imagePaths as $imagePath)
                                <img src="{{ asset('storage/' . $imagePath) }}" alt="Gambar Subitem" style="max-width: 100px; height: auto; margin-right: 5px;">
                            @endforeach
                        @else
                            <p>Tidak ada gambar</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
