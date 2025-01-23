<!DOCTYPE html>
<html>
<head>
    <title>Daftar Jadwal Pengujian</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        img {
            width: 100px; /* Ukuran gambar */
            height: auto;
        }
        .header-logo {
            max-width: 150px;
            height: auto;
        }
        .container {
            text-align: center;
        }
        h2 {
            margin-top: 20px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Logo -->
        <div>
            <img src="{{ public_path('storage/avatar/ftth.png') }}" class="header-logo" style="width: 130px; height: 90px; border-radius: 50%;">
        </div>
        <h1>DAFTAR JADWAL PENGUJIAN</h1>
        <h2>Sistem Informasi Penjadwalan Laboratorium Optic</h2>
    </div>
    
    <hr>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pengujian</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Status</th>
                <th>Gambar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($schedules as $index => $schedule)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $schedule->test_name }}</td>
                    <td>{{ $schedule->start_time }}</td>
                    <td>{{ $schedule->end_time }}</td>
                    <td>{{ $schedule->status }}</td>
                    <td>
                        @if ($schedule->image_path)
                            <img src="{{ public_path('storage/' . $schedule->image_path) }}" alt="Gambar">
                        @else
                            Tidak ada gambar
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
