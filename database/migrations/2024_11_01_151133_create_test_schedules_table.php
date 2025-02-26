<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Tabel test_schedules
        Schema::create('test_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('test_name');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->enum('status', ['Sedang Berjalan', 'Selesai', 'Tunda'])->default('Sedang Berjalan');
        });

        // Tabel test_schedules_detail
        Schema::create('test_schedules_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Tambahkan user_id untuk referensi ke tabel users
            $table->unsignedBigInteger('test_schedule_id');
            $table->string('nama_subitem');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->text('description');
            $table->string('image_detail')->nullable();
            $table->enum('status', ['Selesai', 'Sedang Berjalan', 'Tunda'])->default('Sedang Berjalan');
            $table->timestamps();
            $table->foreign('test_schedule_id')->references('id')->on('test_schedules')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // // Tabel test_schedule_archives
        // Schema::create('test_schedule_archives', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('test_schedule_id');
        //     $table->unsignedBigInteger('test_schedule_detail_id');
        //     $table->timestamps();

        //     $table->foreign('test_schedule_id')->references('id')->on('test_schedules')->onDelete('cascade');
        //     $table->foreign('test_schedule_detail_id')->references('id')->on('test_schedules_detail')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('test_schedule_archives');
        Schema::dropIfExists('test_schedules_detail');
        Schema::dropIfExists('test_schedules');
    }
};
