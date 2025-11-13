<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            // Teknisinya adalah user dengan role "teknisi"
            $table->foreignId('technician_id')
                  ->nullable()                  // biar data lama tidak error
                  ->after('location_id')        // posisinya boleh kamu pindah
                  ->constrained('users')        // FK ke tabel users(id)
                  ->nullOnDelete();             // kalau user dihapus, set NULL
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            // balikkan perubahan saat rollback
            $table->dropConstrainedForeignId('technician_id');
        });
    }
};
