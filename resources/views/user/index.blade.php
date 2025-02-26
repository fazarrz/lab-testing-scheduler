@extends('layouts.app')

@section('title', 'Daftar Akun Engineer')

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
    <h1 class="mt-4">Akun Engineer</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Daftar Engineer</li>
    </ol>

    <!-- Tombol Create dan Export -->
    <div class="mb-4 d-flex justify-content-between">
        <a href="{{ route('user.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Engineer
        </a>
    </div>

    <!-- Tabel Jadwal Pengujian -->
    <div class="card">
        
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered" id="scheduleTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Engineer</th>
                        <th>Email Engineer</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('user.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
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

    function confirmDelete() {
        return confirm("Apakah Anda yakin ingin menghapus akun ini?");
    }
</script>
@endsection
