<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
//Fungsi: Mengimpor ProfileUpdateRequest, yaitu request khusus untuk validasi data pembaruan profil.
//Gunanya: Memastikan bahwa data yang dikirim pengguna sesuai dengan aturan sebelum disimpan.
use Illuminate\Contracts\Auth\MustVerifyEmail;
//Fungsi: Interface untuk pengguna yang perlu verifikasi email.
//Gunanya: Mengecek apakah pengguna yang sedang login harus memverifikasi email sebelum menggunakan fitur tertentu.
use Illuminate\Http\RedirectResponse;
//Fungsi: Memberikan tipe pengembalian (return type) pada method yang melakukan redirect (update() dan destroy()).
//Gunanya: Memastikan method mengembalikan response redirect yang benar
use Illuminate\Http\Request;
//Fungsi: Menggunakan Request untuk menangkap data dari form atau API yang dikirim oleh pengguna.
//Gunanya:
//Mengambil data dari request ($request->user(), $request->input('name'), dll.).
//Melakukan validasi input data ($request->validate
use Illuminate\Support\Facades\Auth;
//ungsi: Menggunakan facade Auth untuk mengelola autentikasi pengguna.
//Gunanya:
//Logout pengguna → Auth::logout();
//Cek user yang sedang login → Auth::user();
use Illuminate\Support\Facades\Redirect;
//ungsi: Menggunakan facade Redirect untuk melakukan redirect ke halaman lain.
//Gunanya:
//Redirect setelah update profil → Redirect::route('profile.edit');
//Redirect ke halaman utama setelah hapus akun → Redirect::to('/');
use Inertia\Inertia;
// Fungsi: Menggunakan Inertia.js untuk merender halaman di frontend.
//Gunanya: Mengembalikan halaman Vue.js dengan data yang diperlukan
use Inertia\Response;
//Fungsi: Menentukan tipe pengembalian (return type) untuk response dari Inertia.
//Gunanya: Memastikan method edit() mengembalikan response yang sesuai (Response).



class ProfileController extends Controller
//Controller ini digunakan untuk mengelola profil pengguna, termasuk menampilkan, memperbarui, dan menghapus akun pengguna.
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    //Fungsi: Menampilkan halaman edit profil.
    //Request $request → Menangkap data request dari pengguna.
    //Response → Menentukan bahwa fungsi ini mengembalikan response dari Inertia
    {
        return Inertia::render('Profile/Edit', [
        //erender halaman Profile/Edit.vue di frontend menggunakan Inertia.js.    
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            //mustVerifyEmail → Mengecek apakah pengguna perlu verifikasi email
            'status' => session('status'),
            //status → Mengambil pesan status dari session, misalnya notifikasi sukses atau error.

        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    //ungsi: Memproses pembaruan profil pengguna.
    //ProfileUpdateRequest $request → Request khusus untuk validasi input data profil.
    //RedirectResponse → Fungsi ini akan mengembalikan redirect response.

    {
        $request->user()->fill($request->validated());
        //Mengisi data pengguna dengan data valid dari form yang dikirim.

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
            //Cek apakah email pengguna berubah (isDirty('email')).
            //Jika email berubah, maka status verifikasi email di-reset (null), agar pengguna perlu verifikasi ulang.
        }

        $request->user()->save();
        //Menyimpan perubahan profil ke database.

        return Redirect::route('profile.edit');
        //Mengalihkan pengguna kembali ke halaman edit profil setelah berhasil diperbarui.
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    //Fungsi: Menghapus akun pengguna.
    //Request $request → Menangkap data request dari pengguna.
    //RedirectResponse → Mengembalikan response berupa redirect.
    {
        $request->validate([
            'password' => ['required', 'current_password'],
            //  Memastikan pengguna memasukkan password sebelum menghapus akun.
            // current_password → Laravel akan otomatis memeriksa apakah password benar.
        ]);

        $user = $request->user();
        //Mendapatkan data pengguna yang sedang login.

        Auth::logout();
        //Keluar dari akun (logout) sebelum menghapus data pengguna.

        $user->delete();
        //Menghapus akun pengguna dari database.

        $request->session()->invalidate();
        //Menghapus sesi pengguna agar tidak bisa login lagi setelah akun dihapus.
        $request->session()->regenerateToken();
        //Mencegah serangan CSRF dengan meregenerasi token sesi baru.

        return Redirect::to('/');
        //Mengalihkan pengguna ke halaman utama (/) setelah akun dihapus.

    }
}
