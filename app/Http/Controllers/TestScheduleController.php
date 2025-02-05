<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestSchedule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class TestScheduleController extends Controller
{

    // Tampilkan daftar jadwal pengujian
    public function index()
    {
        // Update status test_schedules to 'Selesai' if end_time has passed
        TestSchedule::where('end_time', '<', now())->update(['status' => 'Selesai']);
    
        // Update status items to 'Selesai' if end_time has passed
        Item::where('end_time', '<', now())->update(['status' => 'Selesai']);
    
        $schedules = TestSchedule::with(['items.user'])->get();
        return view('test_schedules.index', compact('schedules'));
    }
    
    // Tampilkan form untuk membuat jadwal pengujian

    public function create()
    {
        return view('test_schedules.create');
    }

    public function store(Request $request)
    {
        Log::info($request->all());
    
        // Validasi request
        $request->validate([
            'test_name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'subitems' => 'array',
            'subitems.nama_subitem.*' => 'required|string|max:255',
            'subitems.start_time.*' => 'required|date',
            'subitems.end_time.*' => 'required|date|after:subitems.start_time.*',
            'subitems.image_detail.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'subitems.captured_image_detail.*' => 'nullable|file|mimes:jpeg,png', // Menangani file yang diunggah
        ]);
    
        // Menyimpan file utama jika ada
        $imagePath = $request->file('image') ? $request->file('image')->store('images', 'public') : null;
        $status = 'Sedang Berjalan';
    
        // Membuat entri baru di tabel test_schedules
        $schedule = TestSchedule::create([
            'test_name' => $request->input('test_name'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'image_path' => $imagePath,
            'status' => $status,
        ]);
    
        // Menyimpan subitems ke tabel test_schedules_detail
        $subitemsDetails = '';  // Menyimpan detail subitems untuk pesan Telegram
        if ($request->has('subitems') && !empty($request->input('subitems')['nama_subitem'])) {
            foreach ($request->input('subitems')['nama_subitem'] as $key => $namaSubitem) {
                $subitemImagePaths = [];  // Menyimpan array path gambar
    
                // Menangani captured_image_detail (mengunggah banyak file)
                if ($request->hasFile('subitems.captured_image_detail')) {
                    $capturedImages = $request->file('subitems.captured_image_detail'); // Array file
    
                    // Loop melalui semua gambar yang diunggah dan simpan secara individual
                    foreach ($capturedImages as $capturedImage) {
                        // Menyimpan setiap gambar dengan nama unik
                        $subitemImagePaths[] = $capturedImage->store('images/details', 'public');
                    }
                }
    
                // Menangani image_detail (file tunggal)
                if ($request->hasFile('subitems.image_detail')) {
                    $imageDetails = $request->file('subitems.image_detail');
    
                    // Loop melalui semua file image_detail dan simpan mereka
                    foreach ($imageDetails as $imageDetail) {
                        $subitemImagePaths[] = $imageDetail->store('images/details', 'public');
                    }
                }
    
                // Jika ada gambar yang berhasil diupload, simpan array path-nya
                $subitemImagePaths = json_encode($subitemImagePaths); // Menyimpan path dalam format JSON
    
                // Menyimpan data subitem
                $item = Item::create([
                    'test_schedule_id' => $schedule->id,
                    'user_id' => auth()->user()->id,
                    'nama_subitem' => $namaSubitem,
                    'start_time' => $request->input('subitems')['start_time'][$key],
                    'end_time' => $request->input('subitems')['end_time'][$key],
                    'image_detail' => $subitemImagePaths,  // Menyimpan path gambar dalam bentuk JSON
                ]);
    
                // Menambahkan detail subitem ke dalam pesan
                $subitemsDetails .= "\n- Subitem: {$namaSubitem}\n";
                $subitemsDetails .= "  Waktu Mulai: {$request->input('subitems')['start_time'][$key]}\n";
                $subitemsDetails .= "  Waktu Selesai: {$request->input('subitems')['end_time'][$key]}\n";
            }
        }
    
        // Kirim notifikasi ke Telegram
        $message = "Jadwal Tes Baru telah dibuat:\n";
        $message .= "Nama Tes: {$schedule->test_name}\n";
        $message .= "Waktu Mulai: {$schedule->start_time}\n";
        $message .= "Waktu Selesai: {$schedule->end_time}\n";
        $message .= "Status: {$schedule->status}\n";
        $message .= "Jumlah Subitems: " . (count($request->input('subitems')['nama_subitem'] ?? [])) . "\n";
    
        // Jika ada subitem, tampilkan detailnya
        if (!empty($subitemsDetails)) {
            $message .= "Detail Subitems:{$subitemsDetails}";
        }
    
        // Ganti dengan chat_id yang sesuai, bisa diset di .env
        $chat_id = env('TELEGRAM_CHAT_ID');
        $this->sendTelegramNotification($chat_id, $message);
    
        return redirect()->route('test_schedules.index')->with('success', 'Jadwal berhasil dibuat.');
    }
    


    


    public function show($id)
    {
        $schedule = TestSchedule::with('items')->findOrFail($id); // Load the schedule with its items
        return view('test_schedules.show', compact('schedule')); // Pass the schedule to the view
    }

    // Edit jadwal pengujian
    public function edit($id)
    {
        $schedule = TestSchedule::with('items')->findOrFail($id); // Include subitems
        return view('test_schedules.edit', compact('schedule'));
    }

    public function update(Request $request, $id)
    {
        \Log::info($request->all());
    
        $request->validate([
            'test_name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'subitems' => 'array',
            'subitems.*.nama_subitem' => 'required|string|max:255',
            'subitems.*.start_time' => 'required|date',
            'subitems.*.end_time' => 'required|date|after:subitems.*.start_time',
            'subitems.*.image_detail.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Image array validation
            'subitems.*.captured_image_detail.*' => 'nullable|file|mimes:jpeg,png',
        ]);
    
        $schedule = TestSchedule::findOrFail($id);
    
        // Handle main image update
        if ($request->hasFile('image')) {
            if ($schedule->image_path && Storage::exists('public/' . $schedule->image_path)) {
                Storage::delete('public/' . $schedule->image_path);
            }
            $schedule->image_path = $request->file('image')->store('images', 'public');
        }
    
        // Update the schedule data
        // $updatedSchedule = false;
        // if ($schedule->start_time != $request->input('start_time') || $schedule->end_time != $request->input('end_time')) {
        //     $updatedSchedule = true;
        //     // Send notification if schedule's start_time or end_time is updated
        //     $this->sendTelegramNotification(env('TELEGRAM_CHAT_ID'), "Jadwal Tes diperbarui:\nNama Tes: {$schedule->test_name}\nWaktu Mulai: {$request->input('start_time')}\nWaktu Selesai: {$request->input('end_time')}");
        // }
    
        $schedule->update([
            'test_name' => $request->input('test_name'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'status' => $request->input('status'),
        ]);
    
        // Update subitems
        if ($request->has('subitems')) {
            $existingSubitems = $schedule->items->keyBy('id');

            \Log::info($existingSubitems);
            $updatedSubitemIds = [];
    
            foreach ($request->input('subitems') as $key => $subitem) {
                $subitemId = $subitem['id'] ?? null;
                $subitemData = [
                    'user_id' => auth()->user()->id,
                    'nama_subitem' => $subitem['nama_subitem'],
                    'start_time' => $subitem['start_time'],
                    'end_time' => $subitem['end_time'],
                    'test_schedule_id' => $schedule->id,
                ];
    
                // Handle multiple image details (if any)
                $subitemImagePaths = [];
    
                // Handle multiple captured images (if any)
                if ($request->hasFile("subitems.$key.captured_image_detail")) {
                    $capturedImages = $request->file("subitems.$key.captured_image_detail");
    
                    // Check if multiple files are provided
                    if (is_array($capturedImages)) {
                        foreach ($capturedImages as $capturedImage) {
                            // Store each captured image in the correct folder
                            $subitemImagePaths[] = $capturedImage->store('images/details', 'public');
                        }
                    } else {
                        // If a single image is uploaded, store it
                        $subitemImagePaths[] = $capturedImages->store('images/details', 'public');
                    }
                }
    
                // Handle multiple image details (if any)
                if ($request->hasFile("subitems.$key.image_detail")) {
                    $imageDetails = $request->file("subitems.$key.image_detail");
    
                    // Check if multiple files are provided
                    if (is_array($imageDetails)) {
                        foreach ($imageDetails as $imageDetail) {
                            // Store each image detail in the correct folder
                            $subitemImagePaths[] = $imageDetail->store('images/details', 'public');
                        }
                    } else {
                        // If a single image is uploaded, store it
                        $subitemImagePaths[] = $imageDetails->store('images/details', 'public');
                    }
                }
    
                // Log the image paths for debugging
                \Log::info("Data gambar baru saat menambahkan subitem baru: " . json_encode($subitemImagePaths));
    
                // Save the paths as an array (without JSON encoding)
                if (!empty($subitemImagePaths)) {
                    $subitemData['image_detail'] = json_encode($subitemImagePaths); // Encode as JSON
                }
    
                // If there's a subitem ID, update the existing subitem
                if ($subitemId) {
                    $existingSubitem = $existingSubitems->get($subitemId);
                    if ($existingSubitem) {

                        \Log::info("existingSubitem waktu" . $existingSubitem->start_time);
                        \Log::info("input start_time: " . $subitem['start_time']);
                        $sendNotification = false;
                        if ($existingSubitem->start_time != $subitem['start_time'] || $existingSubitem->end_time != $subitem['end_time']) {
                            // Only send notification if start_time or end_time have changed
                            $sendNotification = true;
                        }
    
                        if (!empty($subitemImagePaths)) {
                            if ($existingSubitem->image_detail) {
                                // Decode the JSON string into an array
                                $imagePaths = json_decode($existingSubitem->image_detail, true);
    
                                // Ensure it's an array before proceeding
                                if (is_array($imagePaths)) {
                                    foreach ($imagePaths as $path) {
                                        Storage::delete('public/' . $path); // Delete each image
                                    }
                                }
                            }
                        }
    
                        // Update the subitem data
                        $existingSubitem->update($subitemData);

                        if ($sendNotification) {
                            $this->sendTelegramNotification(env('TELEGRAM_CHAT_ID'), "Subitem diperbarui:\nSubitem: {$subitem['nama_subitem']}\nWaktu Mulai: {$subitem['start_time']}\nWaktu Selesai: {$subitem['end_time']}");
                        }
    
                        $updatedSubitemIds[] = $subitemId;
    
                       
                    }
                } else {
                    // If no subitem ID, create a new subitem
                    if (!empty($subitemImagePaths)) {
                        $subitemData['image_detail'] = json_encode($subitemImagePaths); // Encode images as JSON if there are any
                    }
                    Item::create($subitemData);
    
                    // Send notification if a new subitem is added
                    $this->sendTelegramNotification(env('TELEGRAM_CHAT_ID'), "Subitem baru ditambahkan:\nSubitem: {$subitem['nama_subitem']}\nWaktu Mulai: {$subitem['start_time']}\nWaktu Selesai: {$subitem['end_time']}");
                }
            }
    
            // Delete subitems that weren't updated
            $subitemsToDelete = $existingSubitems->whereNotIn('id', $updatedSubitemIds);
            foreach ($subitemsToDelete as $subitem) {
                if ($subitem->image_detail) {
                    $imagePaths = json_decode($subitem->image_detail, true);
                    foreach ($imagePaths as $path) {
                        Storage::delete('public/' . $path);
                    }
                }
                $subitem->delete();
            }
        }
    
        return redirect()->route('test_schedules.index')->with('success', 'Jadwal dan subitem berhasil diperbarui.');
    }
    

    // Menghapus jadwal pengujian
    public function destroy($id)
    {
        $schedule = TestSchedule::findOrFail($id);
        
        // Hapus file gambar utama jika ada
        if ($schedule->image_path && Storage::exists('public/' . $schedule->image_path)) {
            Storage::delete('public/' . $schedule->image_path);
        }
        
        // Hapus subitem terkait jika ada gambar pada subitem
        foreach ($schedule->items as $subitem) {
            if ($subitem->image_detail) {
                // Dekode image_detail yang disimpan dalam format JSON
                $imagePaths = json_decode($subitem->image_detail, true);
    
                // Pastikan imagePaths adalah array
                if (is_array($imagePaths)) {
                    // Hapus setiap gambar dalam array image_detail
                    foreach ($imagePaths as $path) {
                        // Cek dan hapus file gambar jika ada
                        if (Storage::exists('public/' . $path)) {
                            Storage::delete('public/' . $path);
                        }
                    }
                }
            }
        }
    
        // Hapus jadwal dan subitem terkait
        $schedule->delete();
        
        return redirect()->route('test_schedules.index')->with('success', 'Jadwal dan gambar terkait berhasil dihapus.');
    }
    

    public function sendTelegramNotification($chat_id, $message)
    {
        $token = '7664846855:AAHMxYYmdkPs0b21nrT9fy1EvMQho-7xago'; // token bot
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $response = Http::post($url, [
            'chat_id' => $chat_id,
            'text' => $message,
        ]);

        // Cek jika pengiriman berhasil
        if (!$response->successful()) {
            // Tampilkan pesan kesalahan jika gagal
            // dd($response->body());
        }
    }

    public function checkForNotifications()
    {
        $schedules = TestSchedule::whereBetween('end_time', [now()->addDay()->startOfDay(), now()->addDay()->endOfDay()])->get();

        foreach ($schedules as $schedule) {
            $message = "Jadwal pengujian '{$schedule->test_name}' akan berakhir dalam satu hari lagi!";
            $chat_id = '668275227';
            $this->sendTelegramNotification($chat_id, $message);
        }
    }
}
