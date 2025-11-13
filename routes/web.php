<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Support\Role;
use App\Models\MaintenanceSchedule;

// Landing (public)
use App\Livewire\Landing\HomePage;

// Admin
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Clients\Index as AdminClients;
use App\Livewire\Admin\Locations\Index as AdminLocations;
use App\Livewire\Admin\Units\Index as AdminUnits;
use App\Livewire\Admin\Schedules\Index as AdminSchedules;
use App\Livewire\Admin\Technicians\Index as AdminTechnicians;
use App\Livewire\Admin\Reports\Index as AdminReports;
use App\Livewire\Admin\Users\Index as AdminUsers;
use App\Livewire\Admin\Settings\Index as AdminSettings;
use App\Livewire\Admin\Register\Index as AdminRegister;
use App\Livewire\Admin\TechLeaves\Index as AdminTechLeaves;
// Teknisi
use App\Livewire\Teknisi\Dashboard as TeknisiDashboard;
use App\Livewire\Teknisi\Tasks\Index as TeknisiTasks;
use App\Livewire\Teknisi\Leave\Index as TechLeaveIndex;

// Client
use App\Livewire\Client\Dashboard as ClientDashboard;
use App\Livewire\Client\Units\Index     as ClientUnits;
use App\Livewire\Client\Schedules\Index as ClientSchedules;
use App\Livewire\Client\Reports\Index   as ClientReports;
use App\Livewire\Client\Feedback\Index  as ClientFeedback;
use App\Livewire\Client\Requests\Index  as ClientRequests;
use App\Livewire\Client\Complaints\Index as ClientComplaints;
// --------------------------------------
// Public
// --------------------------------------
Route::get('/', HomePage::class)->name('home');

// Auth scaffolding (login/register/forgot/etc)
require __DIR__.'/auth.php';

if (app()->environment('local')) {
    Route::get('/force-logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home')->with('ok', 'Dipaksa logout.');
    });
}
// --------------------------------------
// Signed links untuk aksi klien (tanpa auth)
// --------------------------------------
Route::middleware(['signed','throttle:6,1'])->group(function () {

    Route::get('/schedule/{schedule}/client/confirm', function (MaintenanceSchedule $schedule) {
        $schedule->update([
            'client_response'    => 'confirmed',
            'client_response_at' => now(),
        ]);
        return view('client.schedule-response', [
            'title'   => 'Terima kasih!',
            'message' => 'Jadwal telah dikonfirmasi. Sampai jumpa di hari H.',
        ]);
    })->name('schedule.client.confirm');

    Route::get('/schedule/{schedule}/client/cancel', function (MaintenanceSchedule $schedule) {
        $schedule->update([
            'client_response'    => 'cancelled_by_client',
            'client_response_at' => now(),
            'status'             => 'dibatalkan_oleh_klien',
        ]);
        return view('client.schedule-response', [
            'title'   => 'Jadwal Dibatalkan',
            'message' => 'Permintaan pembatalan diterima. Admin akan menindaklanjuti.',
        ]);
    })->name('schedule.client.cancel');

    Route::get('/schedule/{schedule}/client/reschedule', function (MaintenanceSchedule $schedule) {
        return view('client.schedule-reschedule-form', compact('schedule'));
    })->name('schedule.client.reschedule.form');

    Route::post('/schedule/{schedule}/client/reschedule', function (Request $req, MaintenanceSchedule $schedule) {
        $data = $req->validate([
            'requested_date' => ['required','date','after:now'],
            'note'           => ['nullable','string','max:500'],
        ]);

        $schedule->update([
            'client_response'       => 'reschedule_requested',
            'client_response_at'    => now(),
            'client_requested_date' => $data['requested_date'],
            'client_response_note'  => $data['note'] ?? null,
            'status'                => 'menunggu_persetujuan',
        ]);

        return view('client.schedule-response', [
            'title'   => 'Permintaan Penjadwalan Ulang Terkirim',
            'message' => 'Admin akan meninjau dan mengonfirmasi tanggal baru Anda.',
        ]);
    })->name('schedule.client.reschedule.submit');
});

// --------------------------------------
// Authenticated area
// --------------------------------------
Route::middleware(['auth','verified'])->group(function () {

    // Redirect pasca login sesuai role
    Route::get('/redirect', function () {
        $u = auth()->user();
        return match ($u->role) {
            Role::ADMIN   => redirect()->route('admin.dashboard'),
            Role::TEKNISI => redirect()->route('teknisi.dashboard'),
            default       => redirect()->route('client.dashboard'),
        };
    })->name('redirect');

    // ------------------ ADMIN ------------------
    Route::middleware(['role:'.Role::ADMIN])
        ->prefix('admin')->name('admin.')
        ->group(function () {
            Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

            Route::get('/clients',      AdminClients::class)->name('clients');
            Route::get('/locations',    AdminLocations::class)->name('locations');
            Route::get('/units',        AdminUnits::class)->name('units');
            Route::get('/schedules',    AdminSchedules::class)->name('schedules');
            Route::get('/technicians',  AdminTechnicians::class)->name('technicians');
            Route::get('/requests', \App\Livewire\Admin\Requests\Index::class)->name('requests');
            Route::get('/tech-leaves', AdminTechLeaves::class)->name('tech-leaves');


            // laporan admin (verifikasi dsb)
            Route::get('/reports',      AdminReports::class)->name('reports');

            Route::get('/users',        AdminUsers::class)->name('users');
            Route::get('/settings',     AdminSettings::class)->name('settings');
            Route::get('/register-account', AdminRegister::class)->name('register');
        });

    // ------------------ TEKNISI ------------------
   Route::middleware(['auth','verified','role:'.\App\Support\Role::TEKNISI])
    ->prefix('teknisi')->name('teknisi.')
    ->group(function () {
        Route::get('/dashboard', \App\Livewire\Teknisi\Dashboard::class)->name('dashboard');
        Route::get('/tasks',     \App\Livewire\Teknisi\Tasks\Index::class)->name('tasks');
        Route::get('/reports',   \App\Livewire\Teknisi\Reports\Index::class)->name('reports');
        Route::get('/history',   \App\Livewire\Teknisi\History\Index::class)->name('history');
        Route::get('/profile',   \App\Livewire\Teknisi\Profile\Index::class)->name('profile');
         Route::get('/leave', TechLeaveIndex::class)->name('leave');
    });

    // ------------------ CLIENT ------------------
   Route::middleware(['role:'.\App\Support\Role::CLIENT])
    ->prefix('client')->name('client.')
    ->group(function () {
        Route::get('/dashboard', \App\Livewire\Client\Dashboard::class)->name('dashboard');
        Route::get('/units',      \App\Livewire\Client\Units\Index::class)->name('units');
        Route::get('/schedules',  \App\Livewire\Client\Schedules\Index::class)->name('schedules');
        Route::get('/reports',    \App\Livewire\Client\Reports\Index::class)->name('reports');
        Route::get('/feedback',   \App\Livewire\Client\Feedback\Index::class)->name('feedback');
        Route::get('/requests',   \App\Livewire\Client\Requests\Index::class)->name('requests');
        Route::get('/complaints', \App\Livewire\Client\Complaints\Index::class)->name('complaints');
    });
});

// --------------------------------------
// Dev helper (opsional – untuk debug saja)
// --------------------------------------
Route::get('/force-logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return 'Logged out. Sekarang buka /login';
});
