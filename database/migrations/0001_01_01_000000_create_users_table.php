<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration //Membuat kelas anonim yang mewarisi Migration, digunakan untuk mendefinisikan struktur tabel database.
{
    /**
     * Run the migrations.
     */
    public function up(): void 
    //Fungsi up()Digunakan untuk membuat tabel di database.
    {
        Schema::create('users', function (Blueprint $table) { //Fungsi: Menyimpan data pengguna (user).
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) { //Fungsi: Menyimpan token untuk reset password.
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {  //Membuat tabel sessions,Menyimpan data sesi pengguna yang login
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void   //Fungsi down() Digunakan untuk menghapus tabel jika migration dibatalkan.
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        //Fungsi: Menghapus tabel users, password_reset_tokens, dan sessions.
    }
};
