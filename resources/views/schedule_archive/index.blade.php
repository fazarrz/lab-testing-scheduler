@extends('layouts.app')

@section('title', 'Daftar Jadwal Pengujian')

@section('content')
<style>
    /* Tema Finder dengan Unsur Telkom */
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: #f4f5f7;
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
    }
    .card-header {
        background-color: #d91d2a;
        color: white;
        font-weight: bold;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }
    .table-container {
        background-color: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }
    .table th {
        background-color: #f7f7f9;
        color: #333;
        font-weight: bold;
        text-align: center;
        padding: 16px;
        border-bottom: 2px solid #d91d2a;
    }
    .table td {
        text-align: center;
        padding: 12px;
        vertical-align: middle;
        border-top: 1px solid #e0e0e0;
    }
    .table td img {
        border-radius: 6px;
        max-width: 100px;
        height: auto;
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Archive</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Daftar Archive</li>
    </ol>

    <!-- Tombol Create dan Export -->
    <div class="mb-4 d-flex justify-content-end">

        <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-export"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('test_schedules.export') }}">Export Excel</a></li>
                <li><a class="dropdown-item" href="{{ route('schedule_archive.export_pdf') }}">Export PDF</a></li>
            </ul>
        </div>
    </div>

    <!-- Tabel Jadwal Pengujian -->
    <div class="card">
        
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered" id="scheduleTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pengujian</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Gambar</th>
                        <th>Status Uji</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schedules as $index => $schedule)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $schedule->test_name }}</td>
                            <td>{{ $schedule->start_time }}</td>
                            <td>{{ $schedule->end_time }}</td>
                            <td>
                                @if ($schedule->image_path)
                                    <img src="{{ asset('storage/' . $schedule->image_path) }}" alt="Gambar">
                                @else
                                    Tidak ada gambar
                                @endif
                            </td>
                            <td>
                                    <span class="badge bg-warning">{{$schedule->status}}</span>
                                
                            </td>
                            <td>
                                <!-- <a href="{{ route('test_schedules.edit', $schedule->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a> -->
                                <a href="{{ route('test_schedules.show', $schedule->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <!-- <form action="{{ route('test_schedules.destroy', $schedule->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form> -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#scheduleTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true
        });
    });
</script>
@endsection
