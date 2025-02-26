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
                <label for="cc" class="form-label">CC</label>
                <input type="number" class="form-control" name="cc" id="cc" required>
            </div>
            <div class="mb-3">
                <label for="rh" class="form-label">RH</label>
                <input type="number" class="form-control" name="rh" id="rh" required>
                <p>Maksimal : 500RH</p>
            </div>
            <div class="mb-3">
                <label for="voltase" class="form-label">Voltase</label>
                <input type="number" class="form-control" name="voltase" id="voltase" required>
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
                <div id="subitem-container"></div> <!-- Hapus subitem pertama disini -->
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
    let cameraStreams = [];  // Array untuk menyimpan streams kamera

    document.getElementById('add-subitem').addEventListener('click', function() {
        const container = document.getElementById('subitem-container');
        const newSubitem = document.createElement('div');
        newSubitem.classList.add('border', 'p-3', 'mb-2', 'subitem-group');
        newSubitem.innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" name="subitems[nama_subitem][]" class="form-control" placeholder="Nama Subitem" required>
                </div>
                 <div class="col-md-4 mb-2">
                    <input type="number" name="subitems[cc][]" class="form-control" placeholder="CC" required>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="number" name="subitems[rh][]" class="form-control" placeholder="RH" required>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="number" name="subitems[voltase][]" class="form-control" placeholder="Voltase" required>
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
                        <input type="file" name="subitems[image_detail][]" class="form-control" multiple>
                    </div>
                    <div class="cameraContainerSubitem mt-3" style="display: none;">
                        <video class="videoSubitem" width="100%" autoplay></video>
                        <button type="button" class="btn btn-secondary mt-2 captureSubitem">Ambil Gambar</button>
                        <canvas class="canvasSubitem" style="display: none;"></canvas>
                        <input type="file" name="subitems[captured_image_detail][]" class="form-control" style="display: none;">
                        <div class="capturedImagePreviewContainer" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;"></div>
                        <button type="button" class="btn btn-danger mt-2 stopCameraSubitem" style="display: none;">Stop Kamera</button>
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
            stopCameraForSubitem(newSubitem);  // Stop camera when subitem is removed
            newSubitem.remove();
        });
    });

    document.addEventListener('click', function(e) {
        // Open camera for subitem
        if (e.target && e.target.classList.contains('openCameraSubitem')) {
            const subitemGroup = e.target.closest('.subitem-group');
            const video = subitemGroup.querySelector('.videoSubitem');
            const cameraContainer = subitemGroup.querySelector('.cameraContainerSubitem');
            const stopButton = subitemGroup.querySelector('.stopCameraSubitem');

            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    video.srcObject = stream;
                    cameraContainer.style.display = 'block';
                    stopButton.style.display = 'inline-block'; // Show stop button
                    cameraStreams.push(stream); // Store the stream
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
            const imagePreviewContainer = subitemGroup.querySelector('.capturedImagePreviewContainer');

            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert captured image to a file
            const dataUrl = canvas.toDataURL('image/png');
            const byteString = atob(dataUrl.split(',')[1]);
            const arrayBuffer = new ArrayBuffer(byteString.length);
            const uintArray = new Uint8Array(arrayBuffer);
            for (let i = 0; i < byteString.length; i++) {
                uintArray[i] = byteString.charCodeAt(i);
            }
            const blob = new Blob([uintArray], { type: 'image/png' });
            const file = new File([blob], `captured_image_${Date.now()}.png`, { type: 'image/png' });

            // Create image preview element
            const imagePreview = document.createElement('div');
            imagePreview.style.maxWidth = '200px';
            imagePreview.style.marginBottom = '10px';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = "Captured Image";
            img.classList.add('img-fluid');

            imagePreview.appendChild(img);
            imagePreviewContainer.appendChild(imagePreview); // Append the new image preview

            // Store the file in the hidden input for submission
            const fileInput = subitemGroup.querySelector('input[type="file"][name="subitems[captured_image_detail][]"]');


            const dataTransfer = new DataTransfer();
            for (let i = 0; i < fileInput.files.length; i++) {
                dataTransfer.items.add(fileInput.files[i]);
            }
            dataTransfer.items.add(file); // Add the newly captured image
            fileInput.files = dataTransfer.files; // Assign the updated files list

            console.log(fileInput.files);  // Cek apakah file ada di input
        }

        // Stop the camera stream
        if (e.target && e.target.classList.contains('stopCameraSubitem')) {
            const subitemGroup = e.target.closest('.subitem-group');
            const video = subitemGroup.querySelector('.videoSubitem');
            const stream = video.srcObject;
            const tracks = stream.getTracks();
            tracks.forEach(track => track.stop()); // Stop the video track
            video.srcObject = null; // Clear the video source
            subitemGroup.querySelector('.cameraContainerSubitem').style.display = 'none'; // Hide the camera container
            e.target.style.display = 'none'; // Hide the stop button
        }
    });

    // Function to stop the camera when subitem is removed
    function stopCameraForSubitem(subitem) {
        const video = subitem.querySelector('.videoSubitem');
        const stream = video.srcObject;
        if (stream) {
            const tracks = stream.getTracks();
            tracks.forEach(track => track.stop()); // Stop the video track
            video.srcObject = null; // Clear the video source
        }
    }
</script>
@endsection
