@extends('layouts.app')

@section('title', 'Tambah Jadwal Pengujian')

@section('content')
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Tambah Jadwal Pengujian</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('test_schedules.index') }}">Jadwal Pengujian</a></li>
            <li class="breadcrumb-item active">Tambah</li>
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
        <form action="{{ route('test_schedules.store') }}" method="POST" enctype="multipart/form-data" class="p-4 bg-white border rounded">
            @csrf
            <div class="mb-3">
                <label for="test_name" class="form-label">Nama Pengujian</label>
                <input type="text" class="form-control" name="test_name" id="test_name" required>
            </div>
            <div class="mb-3">
                <label for="start_time" class="form-label">Waktu Mulai</label>
                <input type="datetime-local" class="form-control" name="start_time" id="start_time" required>
            </div>
            <div class="mb-3">
                <label for="end_time" class="form-label">Waktu Selesai</label>
                <input type="datetime-local" class="form-control" name="end_time" id="end_time" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Upload Gambar</label>
                <input type="file" name="image" class="form-control">
            </div>

            <!-- Section untuk Subitem -->
            <div class="mb-3">
                <label class="form-label">Subitem</label>
                <div id="subitem-container">
                    <div class="border p-3 mb-2 subitem-group">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <input type="text" name="subitems[nama_subitem][]" class="form-control" placeholder="Nama Subitem" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <input type="datetime-local" name="subitems[start_time][]" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <input type="datetime-local" name="subitems[end_time][]" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Upload Gambar Detail</label>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-primary me-2 openCameraSubitem">Gunakan Kamera</button>
                                    <input type="file" name="subitems[image_detail][]" class="form-control">
                                </div>
                                <div class="cameraContainerSubitem mt-3" style="display: none;">
                                    <video class="videoSubitem" width="100%" autoplay></video>
                                    <button type="button" class="btn btn-secondary mt-2 captureSubitem">Ambil Gambar</button>
                                    <canvas class="canvasSubitem" style="display: none;"></canvas>
                                    <input type="hidden" name="subitems[captured_image_detail][]" class="captured_image_subitem">
                                </div>
                                <div class="capsturedImagePreview" style="display: none; max-width: 100%; margin-bottom: 10px;">
                                    <img src="" alt="Captured Image" class="img-fluid" />
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button type="button" class="btn btn-outline-danger btn-remove-subitem"><strong>-</strong></button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-subitem" class="btn btn-outline-success"><strong>+</strong></button>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">Simpan</button>
                <a href="{{ route('test_schedules.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</main>

<style>
    .cameraContainerSubitem {
        display: flex;
        align-items: center;
    }

    .capturedImagePreview img {
        max-width: 200px;
        margin-bottom: 10px;
    }
</style>

<script>
    document.getElementById('add-subitem').addEventListener('click', function() {
        const container = document.getElementById('subitem-container');
        const newSubitem = document.createElement('div');
        newSubitem.classList.add('border', 'p-3', 'mb-2', 'subitem-group');
        newSubitem.innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-2">
                    <!-- Gambar akan muncul di sini -->
                    <div class="capturedImagePreview" style="display: none; max-width: 100%; margin-bottom: 10px;">
                        <img src="" alt="Captured Image" class="img-fluid" />
                    </div>
                    <input type="text" name="subitems[nama_subitem][]" class="form-control" placeholder="Nama Subitem" required>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="datetime-local" name="subitems[start_time][]" class="form-control" required>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="datetime-local" name="subitems[end_time][]" class="form-control" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label>Upload Gambar Detail</label>
                    <div class="d-flex">
                        <button type="button" class="btn btn-primary me-2 openCameraSubitem">Gunakan Kamera</button>
                        <input type="file" name="subitems[image_detail][]" class="form-control">
                    </div>
                    <div class="cameraContainerSubitem mt-3" style="display: none;">
                        <video class="videoSubitem" width="100%" autoplay></video>
                        <button type="button" class="btn btn-secondary mt-2 captureSubitem">Ambil Gambar</button>
                        <canvas class="canvasSubitem" style="display: none;"></canvas>
                        <input type="hidden" name="subitems[captured_image_detail][]" class="captured_image_subitem">
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <button type="button" class="btn btn-outline-danger btn-remove-subitem"><strong>-</strong></button>
                </div>
            </div>
        `;
        container.appendChild(newSubitem);

        // Apply event listener for remove button for dynamically added subitems
        newSubitem.querySelector('.btn-remove-subitem').addEventListener('click', function() {
            newSubitem.remove();
        });
    });

    document.addEventListener('click', function(e) {
        // Open camera for subitem
        if (e.target && e.target.classList.contains('openCameraSubitem')) {
            const subitemGroup = e.target.closest('.subitem-group');
            const video = subitemGroup.querySelector('.videoSubitem');
            const cameraContainer = subitemGroup.querySelector('.cameraContainerSubitem');

            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    video.srcObject = stream;
                    cameraContainer.style.display = 'block';
                })
                .catch(function (err) {
                    console.error("Error accessing camera: ", err);
                });
        }

        // Capture image from live camera
        if (e.target && e.target.classList.contains('captureSubitem')) {
            const subitemGroup = e.target.closest('.subitem-group');
            const video = subitemGroup.querySelector('.videoSubitem');
            const canvas = subitemGroup.querySelector('.canvasSubitem');
            const capturedImageInput = subitemGroup.querySelector('.captured_image_subitem');
            const imagePreview = subitemGroup.querySelector('.capturedImagePreview');

            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            video.srcObject.getTracks().forEach(track => track.stop()); // Stop video stream after capture

            // Menampilkan hasil gambar yang diambil
            const dataUrl = canvas.toDataURL('image/png');
            capturedImageInput.value = dataUrl; // Store captured image in hidden input
            imagePreview.querySelector('img').src = dataUrl; // Tampilkan gambar hasil capture
            imagePreview.style.display = 'block'; // Tampilkan gambar hasil capture
            subitemGroup.querySelector('.cameraContainerSubitem').style.display = 'none'; // Hide camera container
        }
    });

    // Ensure the remove button works for dynamically added subitems
    document.querySelectorAll('.btn-remove-subitem').forEach(button => {
        button.addEventListener('click', function() {
            button.closest('.subitem-group').remove();
        });
    });
</script>
@endsection
