<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_technician_leaves_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('technician_leaves', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->date('start_date');
            $t->date('end_date');
            $t->string('reason', 255)->nullable();
            $t->string('proof_path')->nullable(); // bukti (foto / pdf)
            $t->enum('status', ['pending','approved','rejected'])->default('pending');
            $t->timestamp('decided_at')->nullable();
            $t->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('technician_leaves'); }
};
