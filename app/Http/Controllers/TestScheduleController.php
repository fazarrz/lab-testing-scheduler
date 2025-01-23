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
    ]);

    // Menyimpan file jika ada
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
    if ($request->has('subitems')) {
        foreach ($request->input('subitems')['nama_subitem'] as $key => $namaSubitem) {
            $subitemImagePath = null;

            if (!empty($request->input('subitems')['captured_image_detail'][$key])) {
                $imageData = $request->input('subitems')['captured_image_detail'][$key];
                $image = str_replace('data:image/png;base64,', '', $imageData);
                $image = str_replace(' ', '+', $image);
                $subitemImagePath = 'images/details/' . uniqid() . '.png';
                Storage::disk('public')->put($subitemImagePath, base64_decode($image));
            } elseif (isset($request->file('subitems')['image_detail'][$key])) {
                $subitemImagePath = $request->file('subitems')['image_detail'][$key]->store('images/details', 'public');
            }

            Item::create([
                'test_schedule_id' => $schedule->id,
                'user_id' => auth()->user()->id,
                'nama_subitem' => $namaSubitem,
                'start_time' => $request->input('subitems')['start_time'][$key],
                'end_time' => $request->input('subitems')['end_time'][$key],
                'image_detail' => $subitemImagePath,
            ]);
        }
    }

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
        \Log::info($schedule);
        return view('test_schedules.edit', compact('schedule'));
    }

    // Perbarui jadwal pengujian beserta subitems
    public function update(Request $request, $id)
    {
        \Log::info($request->all());

        $request->validate([
            'test_name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'subitems.*.nama_subitem' => 'required|string|max:255',
            'subitems.*.start_time' => 'required|date',
            'subitems.*.end_time' => 'required|date|after:subitems.*.start_time',
            'subitems.*.image_detail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $schedule = TestSchedule::findOrFail($id);

        // Menghapus gambar lama jika ada dan menggantinya dengan gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari storage jika ada
            if ($schedule->image_path && Storage::exists('public/' . $schedule->image_path)) {
                Storage::delete('public/' . $schedule->image_path);
            }
            // Simpan gambar baru
            $schedule->image_path = $request->file('image')->store('images', 'public');
        }

        $schedule->update([
            'test_name' => $request->input('test_name'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'status' => $request->input('status'),
        ]);

        if ($request->has('subitems')) {
            $existingSubitems = $schedule->items->keyBy('id');
            $updatedSubitemIds = [];

            foreach ($request->input('subitems') as $key => $subitem) {
                $subitemId = $subitem['id'] ?? null;

                $subitemData = [
                    'nama_subitem' => $subitem['nama_subitem'],
                    'start_time' => $subitem['start_time'],
                    'end_time' => $subitem['end_time'],
                    'status' => $subitem['status'],
                    'test_schedule_id' => $schedule->id,   
                    
                ];

                if (!$subitemId) {
                    $subitemData['user_id'] = auth()->user()->id;
                }

                // Jika gambar detail subitem diubah, hapus gambar lama
                if (isset($request->file('subitems')[$key]['image_detail'])) {
                    // Hapus gambar lama jika ada
                    if (isset($existingSubitems[$subitemId]) && $existingSubitems[$subitemId]->image_detail) {
                        Storage::delete('public/' . $existingSubitems[$subitemId]->image_detail);
                    }

                    // Simpan gambar baru untuk subitem
                    $subitemData['image_detail'] = $request->file('subitems')[$key]['image_detail']->store('images/details', 'public');
                }

                if ($subitemId) {
                    // Update subitem yang sudah ada
                    $existingSubitem = $existingSubitems->get($subitemId);
                    if ($existingSubitem) {
                        $existingSubitem->update($subitemData);
                        $updatedSubitemIds[] = $subitemId;
                    }
                } else {
                    // Buat subitem baru
                    Item::create($subitemData);
                }
            }

            // Menghapus subitem yang tidak diperbarui
            $subitemsToDelete = $existingSubitems->whereNotIn('id', $updatedSubitemIds);
            foreach ($subitemsToDelete as $subitem) {
                // Hapus gambar detail subitem jika ada
                if ($subitem->image_detail) {
                    Storage::delete('public/' . $subitem->image_detail);
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
            if ($subitem->image_detail && Storage::exists('public/' . $subitem->image_detail)) {
                Storage::delete('public/' . $subitem->image_detail);
            }
        }

        // Hapus jadwal dan subitem terkait
        $schedule->delete();

        return redirect()->route('test_schedules.index')->with('success', 'Jadwal dan gambar terkait berhasil dihapus.');
    }

    // Fungsi untuk mengirim notifikasi Telegram
    // public function sendTelegramNotification($chat_id, $message)
    // {
    //     $token = '7664846855:AAHMxYYmdkPs0b21nrT9fy1EvMQho-7xago'; // token bot
    //     $url = "https://api.telegram.org/bot{$token}/sendMessage";

    //     $response = Http::post($url, [
    //         'chat_id' => $chat_id,
    //         'text' => $message,
    //     ]);

    //     // Cek jika pengiriman berhasil
    //     if (!$response->successful()) {
    //         // Tampilkan pesan kesalahan jika gagal
    //         // dd($response->body());
    //     }
    // }

    // Fungsi untuk memeriksa jadwal dan mengirimkan notifikasi jika mendekati waktu selesai
    // public function checkForNotifications()
    // {
    //     $schedules = TestSchedule::whereBetween('end_time', [now()->addDay()->startOfDay(), now()->addDay()->endOfDay()])->get();

    //     foreach ($schedules as $schedule) {
    //         $message = "Jadwal pengujian '{$schedule->test_name}' akan berakhir dalam satu hari lagi!";
    //         $chat_id = '668275227';
    //         $this->sendTelegramNotification($chat_id, $message);
    //     }
    // }
}
