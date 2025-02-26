<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function fetchNotifications()
    {
        // Read the content of the notifications.txt file
        $notificationContent = Storage::disk('public')->get('notifications.txt');
        
        // Return the content as a response
        return response($notificationContent);
    }

    public function countNotifications()
    {
        // Read the content of the notifications.txt file
        $notificationContent = Storage::disk('public')->get('notifications.txt');
        
        // Split the content by new lines (assuming each notification is on a new line)
        $notifications = explode("\n", $notificationContent);
        
        // Filter out any empty lines or non-notification content
        $notifications = array_filter($notifications, function($line) {
            return !empty(trim($line)); // Remove empty or whitespace-only lines
        });
        
        // Count the number of notifications
        $notificationCount = count($notifications);

        // Return the notification count as a response (you can adjust this depending on your use case)
        return response()->json(['count' => $notificationCount]);
    }

    public function deleteAllNotifications()
    {
        // Check if the file exists before attempting to clear it
        if (Storage::disk('public')->exists('notifications.txt')) {
            // Clear the content of the notifications.txt file
            Storage::disk('public')->put('notifications.txt', '');

            // Return a success response
            return response()->json(['success' => true, 'message' => 'Semua notifikasi berhasil dihapus.']);
        } else {
            // Return an error response if the file doesn't exist
            return response()->json(['success' => false, 'message' => 'Tidak ada notifikasi.']);
        }
    }
}
