<?php

namespace App\Http\Controllers;

use App\Models\TestSchedule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TestSchedulesExport;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index() {
        
        $schedules = TestSchedule::with('items')->get();
        //$this->checkForNotifications();
        return view('schedule_archive.index', compact('schedules'));
    }

    public function export()
    {
        return Excel::download(new TestSchedulesExport, 'jadwal_pengujian.xlsx');
    }

    public function exportPDF()
    {
        $schedules = TestSchedule::all();

        // Generate PDF dari view dengan data jadwal pengujian
        $pdf = Pdf::loadView('test_schedules.pdf', compact('schedules'));
        return $pdf->download('jadwal_pengujian.pdf');
    }
}
