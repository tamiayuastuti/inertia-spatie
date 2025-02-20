<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void //Fungsi up() (Membuat Tabel)
    {
        Schema::create('cache', function (Blueprint $table) { 
        // //Membuat tabel cache Menyimpan data cache (penyimpanan sementara untuk mempercepat akses data).  
            $table->string('key')->primary();                 
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
        //Membuat tabel cache_locks Digunakan untuk mekanisme locking agar cache tidak diakses atau dimodifikasi secara bersamaan oleh proses lain.
   
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void //Fungsi down() (Menghapus Tabel) 
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
        //Menghapus tabel cache dan cache_locks jika migration dibatalkan
    }
};
