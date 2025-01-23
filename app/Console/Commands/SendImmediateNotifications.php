<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TestSchedule;

class SendImmediateNotifications extends Command
{
    protected $signature = 'notifications:send-immediate';

    public function handle() {
        $schedules = TestSchedule::where('end_time', '>', now()->addDay()->startOfDay())
            ->where('end_time', '<=', now()->addDay()->endOfDay())
            ->get();


            

        if ($schedules->isEmpty()) {
            \Log::info('Tidak ada jadwal yang ditemukan untuk notifikasi.');
            return;
        }

        foreach ($schedules as $schedule) {
            $this->sendNotification($schedule);
        }

        \Log::info('Notifikasi telah dikirim untuk ' . $schedules->count() . ' jadwal.');
    }

    protected function sendNotification($schedule) {
        $message = "Reminder: Pengujian '{$schedule->test_name}' akan selesai segera!";
        
        $chat_id = '668275227';
        $token = '7664846855:AAHMxYYmdkPs0b21nrT9fy1EvMQho-7xago';
        $url = "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&text=" . urlencode($message);

        // Mengirim notifikasi
        $response = @file_get_contents($url); // Menggunakan @ untuk menghindari warning

        if ($response === false) {
            \Log::error("Gagal mengirim notifikasi untuk '{$schedule->test_name}'");
        }
    }
}
