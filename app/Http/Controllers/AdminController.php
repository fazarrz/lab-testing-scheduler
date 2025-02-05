<?php


namespace App\Http\Controllers;

use App\Models\TestSchedule;
use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Get all schedules
        $schedules = TestSchedule::all()->flatMap(function ($schedule) {
            $events = [];
            $startDate = Carbon::parse($schedule->start_time);
            $endDate = Carbon::parse($schedule->end_time);

            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $events[] = [
                    'title' => $schedule->test_name,
                    'start' => $date->format('Y-m-d'),
                    'allDay' => true,
                ];
            }

            return $events;
        });

        // Count schedules by status
        $totalSchedules = Item::count();
        $sedangBerjalan = Item::where('status', 'Sedang Berjalan')->count();
        $selesai = Item::where('status', 'Selesai')->count();
        $tunda = Item::where('status', 'Tunda')->count();

        // Return the view with schedules and status counts
        return view('admin.dashboard', [
            'schedules' => $schedules,
            'totalSchedules' => $totalSchedules,
            'sedangBerjalan' => $sedangBerjalan,
            'selesai' => $selesai,
            'tunda' => $tunda,
        ]);
    }
}
