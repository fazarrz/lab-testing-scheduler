<?php

namespace App\Http\Controllers;

use App\Models\TestSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
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

        return view('admin.dashboard', ['schedules' => $schedules]);
    }
}

