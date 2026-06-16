<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleActivity;
use App\Services\CartrackActivityService;

class VehicleActivityController extends Controller
{
    protected $activityService;

    public function __construct(CartrackActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    // Menampilkan halaman dashboard aktivitas beserta data yang ada di DB
    public function index(Request $request)
    {
        // Jika parameter 'date' tidak ada, maka default ke H-1 (kemarin)
        $selectedDate = $request->get('date', now()->subDay()->toDateString());
        
        // Menampilkan data berdasarkan filter tanggal di view dengan pagination 10
        $activities = VehicleActivity::where('activity_date', $selectedDate)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();

        return view('cartrack.activity', compact('activities', 'selectedDate'));
    }

    // Memicu sinkronisasi data dari tombol di Blade View
    public function sync(Request $request)
    {
        $request->validate([
            'sync_date' => 'required|date|before_or_equal:today',
        ], [
            'sync_date.required' => 'Tanggal wajib dipilih.',
            'sync_date.before_or_equal' => 'Tidak bisa menarik data aktivitas untuk tanggal yang akan datang.'
        ]);

        $date = $request->input('sync_date');
        
        // Memanggil service
        $result = $this->activityService->syncActivityByDate($date);

        if ($result['status']) {
            return redirect()->route('cartrack.activity.index', ['date' => $date])
                             ->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
