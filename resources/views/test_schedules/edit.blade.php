@extends('layouts.app')

@section('title', 'Edit Jadwal Pengujian')

@section('content')
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Jadwal Pengujian</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('test_schedules.index') }}">Jadwal Pengujian</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('test_schedules.update', $schedule->id) }}" method="POST" enctype="multipart/form-data" class="p-4 bg-white border rounded">
            @csrf
            @method('POST') <!-- Menggunakan metode PUT -->
            <div class="mb-3">
                <label for="test_name" class="form-label">Nama Pengujian</label>
                <input type="text" class="form-control" id="test_name" name="test_name" value="{{ $schedule->test_name }}" required>
            </div>
            <div class="mb-3">
                <label for="start_time" class="form-label">Waktu Mulai</label>
                <input type="datetime-local" class="form-control" id="start_time" name="start_time" 
                    value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="mb-3">
                <label for="end_time" class="form-label">Waktu Selesai</label>
                <input type="datetime-local" class="form-control" id="end_time" name="end_time" 
                    value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload Gambar</label>
                <input type="file" name="image" class="form-control">
                @if ($schedule->image_path)
                    <small>Gambar saat ini: <a href="{{ asset('storage/' . $schedule->image_path) }}" target="_blank">Lihat</a></small>
                @endif
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status Uji</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Sedang Berjalan" {{ $schedule->status == 'Sedang Berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="Selesai" {{ $schedule->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Tunda" {{ $schedule->status == 'Tunda' ? 'selected' : '' }}>Tunda</option>
                </select>
            </div>

            <!-- Bagian Subitem -->
            <div class="mb-3">
                <label class="form-label">Subitem</label>
                <div id="subitem-container">
                    @foreach ($schedule->items as $detail)
                        <div class="d-flex align-items-center mb-2 subitem-group">
                            <input type="hidden" name="subitems[{{ $loop->index }}][id]" value="{{ $detail->id }}">
                            <input type="text" name="subitems[{{ $loop->index }}][nama_subitem]" class="form-control me-2" 
                                placeholder="Nama Subitem" value="{{ $detail->nama_subitem }}" required>
                            <input type="datetime-local" name="subitems[{{ $loop->index }}][start_time]" class="form-control me-2"
                                value="{{ \Carbon\Carbon::parse($detail->start_time)->format('Y-m-d\TH:i') }}" required>
                            <input type="datetime-local" name="subitems[{{ $loop->index }}][end_time]" class="form-control me-2"
                                value="{{ \Carbon\Carbon::parse($detail->end_time)->format('Y-m-d\TH:i') }}" required>
                            <input type="file" name="subitems[{{ $loop->index }}][image_detail]" class="form-control me-2">
                            @if ($detail->image_detail)
                                <small><a href="{{ asset('storage/' . $detail->image_detail) }}" target="_blank">Lihat Gambar</a></small>
                            @endif
                            <select name="subitems[{{ $loop->index }}][status]" class="form-control me-2" required>
                                <option value="Sedang Berjalan" {{ $detail->status == 'Sedang Berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                                <option value="Selesai" {{ $detail->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Tunda" {{ $detail->status == 'Tunda' ? 'selected' : '' }}>Tunda</option>
                            </select>
                            <button type="button" class="btn btn-outline-danger btn-remove-subitem"><strong>-</strong></button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-subitem" class="btn btn-outline-success"><strong>+</strong></button>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                <a href="{{ route('test_schedules.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</main>

<script>
    document.getElementById('add-subitem').addEventListener('click', function () {
        const container = document.getElementById('subitem-container');
        const index = container.children.length; // Index untuk subitem baru
        const newSubitem = document.createElement('div');
        newSubitem.classList.add('d-flex', 'align-items-center', 'mb-2', 'subitem-group');
        newSubitem.innerHTML = `
            <input type="text" name="subitems[${index}][nama_subitem]" class="form-control me-2" placeholder="Nama Subitem" required>
            <input type="datetime-local" name="subitems[${index}][start_time]" class="form-control me-2" required>
            <input type="datetime-local" name="subitems[${index}][end_time]" class="form-control me-2" required>
            <input type="file" name="subitems[${index}][image_detail]" class="form-control me-2">
            <select name="subitems[${index}][status]" class="form-control me-2" required>
                <option value="Sedang Berjalan" selected>Sedang Berjalan</option>
                <option value="Selesai">Selesai</option>
                <option value="Tunda">Tunda</option>
            </select>
            <button type="button" class="btn btn-outline-danger btn-remove-subitem"><strong>-</strong></button>
        `;
        container.appendChild(newSubitem);
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('btn-remove-subitem')) {
            e.target.closest('.subitem-group').remove();
        }
    });
</script>
@endsection
