<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void //Fungsi up() 
    {
        Schema::create('jobs', function (Blueprint $table) { 
        //(Membuat Tabel) Membuat tabel jobs
       //Fungsi:Menyimpan daftar pekerjaan (jobs) yang akan dieksekusi secara asinkron oleh sistem queue.    
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
        ////Membuat tabel job_batches fungsiMenyimpan informasi batch untuk menjalankan banyak pekerjaan secara bersamaan.    
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) { 
        //Membuat tabel failed_jobs   
        //Menyimpan pekerjaan yang gagal agar bisa diperbaiki atau dijalankan ulang.    
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    //Fungsi down() (Menghapus Tabel)
    //Fungsi:Menghapus tabel jobs, job_batches, dan failed_jobs jika migration dibatalkan.

    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
