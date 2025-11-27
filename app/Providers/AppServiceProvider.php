<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\TechnicianProfile;
use App\Models\TechnicianLeave;
use App\Models\MaintenanceSchedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ⛔ Jangan jalankan logic otomatis saat CLI (config:cache, migrate, optimize)
        if (app()->runningInConsole()) {
            return;
        }

        $today = now('Asia/Jakarta')->toDateString();

        /*
        |--------------------------------------------------------------------------
        | 1) Auto Aktifkan Teknisi Yang Selesai Cuti 
        |--------------------------------------------------------------------------
        */
        TechnicianProfile::where('status', 'cuti')
            ->with('user.technicianLeaves')
            ->chunk(50, function ($profiles) use ($today) {

                foreach ($profiles as $p) {

                    if (!$p->user) continue;

                    $leaveFinished = $p->user->technicianLeaves()
                        ->approved()
                        ->whereDate('end_date', '<', $today)
                        ->exists();

                    if ($leaveFinished && method_exists($p, 'markAsActive')) {
                        $p->markAsActive();
                    }
                }
            });

        /*
        |--------------------------------------------------------------------------
        | 2) Tandai Teknisi Sedang Bertugas (jadwal hari ini)
        |--------------------------------------------------------------------------
        */
        TechnicianProfile::where('is_active', 1)
            ->with('user')
            ->chunk(50, function ($profiles) use ($today) {

                foreach ($profiles as $p) {

                    if (!$p->user) continue;

                    $hasJobToday = MaintenanceSchedule::where('assigned_user_id', $p->user_id)
                        ->whereDate('scheduled_at', $today)
                        ->whereIn('status', ['menunggu', 'dalam_proses'])
                        ->exists();

                    if ($hasJobToday && method_exists($p, 'markAsBusy')) {
                        $p->markAsBusy();
                    }
                }
            });
    }
}
