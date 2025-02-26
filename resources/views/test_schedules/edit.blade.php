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
            @method('PUT')

            <!-- Main Schedule Information -->
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
                <label for="cc" class="form-label">CC</label>
                <input type="number" class="form-control" id="cc" name="cc" value="{{ old('cc', $schedule->cc) }}" required>
            </div>

            <div class="mb-3">
                <label for="rh" class="form-label">RH</label>
                <input type="number" class="form-control" id="rh" name="rh" value="{{ old('rh', $schedule->rh) }}" required>
            </div>

            <div class="mb-3">
                <label for="voltase" class="form-label">Voltase</label>
                <input type="number" class="form-control" id="voltase" name="voltase" value="{{ old('voltase', $schedule->voltase) }}" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Upload Gambar</label>
                <input type="file" name="image" class="form-control">
                @if ($schedule->image_path)
                    <small>Gambar saat ini:</small><br>
                    <img src="{{ asset('storage/' . $schedule->image_path) }}" alt="Current Image" style="max-width: 200px; margin-top: 10px;">
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

            <!-- Subitem Section -->
            <div class="mb-4">
                <label class="form-label">Subitem</label>
                <div id="subitem-container">
                    @foreach ($schedule->items as $detail)
                        <div class="subitem-group mb-3 p-3 border rounded">
                            <input type="hidden" name="subitems[{{ $loop->index }}][id]" value="{{ $detail->id }}">
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <input type="text" name="subitems[{{ $loop->index }}][nama_subitem]" class="form-control" placeholder="Nama Subitem" value="{{ $detail->nama_subitem }}" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="datetime-local" name="subitems[{{ $loop->index }}][start_time]" class="form-control" value="{{ \Carbon\Carbon::parse($detail->start_time)->format('Y-m-d\TH:i') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="datetime-local" name="subitems[{{ $loop->index }}][end_time]" class="form-control" value="{{ \Carbon\Carbon::parse($detail->end_time)->format('Y-m-d\TH:i') }}" required>
                                </div>
                            </div>

                            <!-- Subitem Images Section -->
                            <div class="mb-2">
                                <label>Upload Gambar Detail</label>
                                <div class="d-flex mb-2">
                                    <button type="button" class="btn btn-primary me-2 openCameraSubitem">Gunakan Kamera</button>
                                    <input type="file" name="subitems[{{ $loop->index }}][image_detail][]" class="form-control" multiple>
                                </div>
                                @if ($detail->image_detail)
                                    <small>Gambar saat ini:</small><br>
                                    @foreach (json_decode($detail->image_detail) as $image)
                                        <img src="{{ asset('storage/' . $image) }}" alt="Subitem Image" style="max-width: 150px; margin-top: 10px; margin-right: 10px;">
                                    @endforeach
                                @endif
                            </div>

                            <!-- Camera Preview Section -->
                            <div class="cameraContainerSubitem mt-3" style="display: none;">
                                <video class="videoSubitem" width="100%" autoplay></video>
                                <button type="button" class="btn btn-secondary mt-2 captureSubitem">Ambil Gambar</button>
                                <button type="button" class="btn btn-danger stopCameraSubitem mt-2">Stop Kamera</button>
                                <canvas class="canvasSubitem" style="display: none;"></canvas>
                                <input type="file" name="subitems[{{ $loop->index }}][captured_image_detail][]" class="form-control" style="display: none;">
                                <div class="capturedImagePreviewContainer d-flex flex-wrap gap-2 mt-2"></div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-danger btn-remove-subitem"><strong>-</strong></button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="add-subitem" class="btn btn-outline-success"><strong>+</strong> Tambah Subitem</button>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                <a href="{{ route('test_schedules.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</main>

<script>
    let currentStreams = [];  // Menyimpan stream kamera untuk setiap subitem

    document.getElementById('add-subitem').addEventListener('click', function () {
        const container = document.getElementById('subitem-container');
        const index = container.children.length;  // Index untuk subitem baru
        const newSubitem = document.createElement('div');
        newSubitem.classList.add('subitem-group', 'mb-3', 'p-3', 'border', 'rounded');
        newSubitem.innerHTML = `
            <input type="text" name="subitems[${index}][nama_subitem]" class="form-control" placeholder="Nama Subitem" required>
            <div class="row mb-2">
                <div class="col-md-4">
                    <input type="datetime-local" name="subitems[${index}][start_time]" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <input type="datetime-local" name="subitems[${index}][end_time]" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <input type="file" name="subitems[${index}][image_detail][]" class="form-control" multiple>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    <label for="cc" class="form-label">CC</label>
                    <input type="number" class="form-control" name="subitems[${index}][cc]" required>
                </div>
                <div class="col-md-4">
                    <label for="rh" class="form-label">RH</label>
                    <input type="number" class="form-control" name="subitems[${index}][rh]" required>
                </div>
                <div class="col-md-4">
                    <label for="voltase" class="form-label">Voltase</label>
                    <input type="number" class="form-control" name="subitems[${index}][voltase]" required>
                </div>
            </div>
            <div class="d-flex mb-2">
                <button type="button" class="btn btn-primary me-2 openCameraSubitem">Gunakan Kamera</button>
            </div>
            <div class="cameraContainerSubitem mt-3" style="display: none;">
                <video class="videoSubitem" width="100%" autoplay></video>
                <button type="button" class="btn btn-secondary mt-2 captureSubitem">Ambil Gambar</button>
                <button type="button" class="btn btn-danger stopCameraSubitem mt-2">Stop Kamera</button>
                <canvas class="canvasSubitem" style="display: none;"></canvas>
                <input type="file" name="subitems[${index}][captured_image_detail][]" class="form-control" style="display: none;">
                <div class="capturedImagePreviewContainer d-flex flex-wrap gap-2 mt-2"></div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-outline-danger btn-remove-subitem"><strong>-</strong></button>
            </div>
        `;
        container.appendChild(newSubitem);
    });

    document.addEventListener('click', function (e) {
        // Menghapus subitem
        if (e.target && e.target.classList.contains('btn-remove-subitem')) {
            const subitemGroup = e.target.closest('.subitem-group');
            const index = Array.from(subitemGroup.parentNode.children).indexOf(subitemGroup);
            
            // Menghentikan kamera saat subitem dihapus
            if (currentStreams[index]) {
                currentStreams[index].getTracks().forEach(track => track.stop());
                currentStreams[index] = null;  // Menghapus stream dari array
            }
            
            subitemGroup.remove();
        }

        // Buka kamera untuk subitem
        if (e.target && e.target.classList.contains('openCameraSubitem')) {
            const subitemGroup = e.target.closest('.subitem-group');
            const video = subitemGroup.querySelector('.videoSubitem');
            const cameraContainer = subitemGroup.querySelector('.cameraContainerSubitem');

            // Jika stream sudah ada, gunakan yang sudah ada
            const index = Array.from(subitemGroup.parentNode.children).indexOf(subitemGroup);
            if (currentStreams[index]) {
                video.srcObject = currentStreams[index];
                cameraContainer.style.display = 'block';
                return;
            }

            // Meminta akses kamera dan mulai streaming video
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    currentStreams[index] = stream;  // Menyimpan stream untuk subitem ini
                    video.srcObject = stream;
                    cameraContainer.style.display = 'block';
                })
                .catch(function (err) {
                    console.error("Error accessing camera: ", err);
                });
        }

        // Stop kamera untuk subitem
        if (e.target && e.target.classList.contains('stopCameraSubitem')) {
            const subitemGroup = e.target.closest('.subitem-group');
            const video = subitemGroup.querySelector('.videoSubitem');
            const cameraContainer = subitemGroup.querySelector('.cameraContainerSubitem');
            const index = Array.from(subitemGroup.parentNode.children).indexOf(subitemGroup);

            // Menghentikan stream kamera
            if (currentStreams[index]) {
                currentStreams[index].getTracks().forEach(track => track.stop());
                currentStreams[index] = null;
            }

            // Menyembunyikan tampilan kamera
            cameraContainer.style.display = 'none';
        }

        // Tangkap gambar dari video stream
        if (e.target && e.target.classList.contains('captureSubitem')) {
            const subitemGroup = e.target.closest('.subitem-group');
            const video = subitemGroup.querySelector('.videoSubitem');
            const canvas = subitemGroup.querySelector('.canvasSubitem');
            const imagePreviewContainer = subitemGroup.querySelector('.capturedImagePreviewContainer');

            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Mengambil gambar dari canvas
            const dataUrl = canvas.toDataURL('image/png');
            const byteString = atob(dataUrl.split(',')[1]);
            const arrayBuffer = new ArrayBuffer(byteString.length);
            const uintArray = new Uint8Array(arrayBuffer);
            for (let i = 0; i < byteString.length; i++) {
                uintArray[i] = byteString.charCodeAt(i);
            }
            const blob = new Blob([uintArray], { type: 'image/png' });
            const file = new File([blob], `captured_image_${Date.now()}.png`, { type: 'image/png' });

            // Menampilkan preview gambar
            const imagePreview = document.createElement('div');
            imagePreview.style.maxWidth = '150px';
            imagePreview.style.marginBottom = '10px';
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = "Captured Image";
            img.classList.add('img-fluid');
            imagePreview.appendChild(img);
            imagePreviewContainer.appendChild(imagePreview);

            // Menambahkan gambar yang diambil ke input file
            const fileInput = subitemGroup.querySelector('input[type="file"][name^="subitems"]');

            const dataTransfer = new DataTransfer();
            for (let i = 0; i < fileInput.files.length; i++) {
                dataTransfer.items.add(fileInput.files[i]);
            }
            dataTransfer.items.add(file); // Menambahkan gambar baru ke input file
            fileInput.files = dataTransfer.files;

            // Hentikan kamera setelah gambar diambil
            if (currentStreams[index]) {
                currentStreams[index].getTracks().forEach(track => track.stop());
                currentStreams[index] = null;  // Hapus stream setelah mengambil gambar
            }
            cameraContainer.style.display = 'none';  // Sembunyikan tampilan kamera
        }
    });
</script>
@endsection