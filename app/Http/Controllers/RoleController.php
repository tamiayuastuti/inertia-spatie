<?php

namespace App\Http\Controllers;
//Menentukan namespace tempat controller ini berada.
use Illuminate\Http\Request;
//Mengimpor class Request untuk menangani input dari pengguna.
use Illuminate\Routing\Controllers\HasMiddleware;
//Menggunakan middleware di dalam controller.
use Illuminate\Routing\Controllers\Middleware;
//Middleware digunakan untuk melindungi route, misalnya hanya admin yang bisa mengakses fitur ini.
use Spatie\Permission\Models\Role;
//Mengimpor model Role dan Permission dari package Spatie.
//Role digunakan untuk mengelola peran pengguna (misalnya: admin, editor, user).
use Spatie\Permission\Models\Permission;
//Permission digunakan untuk mengatur izin spesifik (misalnya: create-post, delete-user).


class RoleController extends Controller implements HasMiddleware // Implement Middleware Spatie
//Membuat RoleController sebagai turunan dari Controller.
//Mengimplementasikan HasMiddleware, yang berarti controller ini menggunakan middleware untuk membatasi akses.
{
    public static function middleware()
    {
        return [
            new Middleware('permission:roles index', only: ['index']),
            //permission:roles index → Hanya pengguna dengan izin "roles index" yang bisa mengakses index()
            new Middleware('permission:roles create', only: ['create', 'store']),
            //permission:roles create → Hanya pengguna dengan izin "roles create" yang bisa membuat role (create(), store()).
            new Middleware('permission:roles edit', only: ['edit', 'update']),
            //permission:roles edit → Hanya pengguna dengan izin "roles edit" yang bisa mengedit role (edit(), update()).
            new Middleware('permission:roles delete', only: ['destroy']),
            //ermission:roles delete → Hanya pengguna dengan izin "roles delete" yang bisa menghapus role (destroy()).
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    //Fungsi: Menampilkan daftar roles berdasarkan request dari pengguna.
    {
        // get roles
        $roles = Role::select('id', 'name')
        //Role::select('id', 'name') → Mengambil ID dan nama role dari database
            ->with('permissions:id,name')
            //>with('permissions:id,name') → Mengambil data izin (permissions) yang terkait dengan role.
            ->when($request->search,fn($search) => $search->where('name', 'like', '%'.$request->search.'%'))
            //->when($request->search, fn($search) => $search->where('name', 'like', '%'.$request->search.'%'))
            ->latest() //Mengurutkan dari yang terbaru.
            ->paginate(6); // Menampilkan 6 data per halaman.

        // render view
        return inertia('Roles/Index', ['roles' => $roles,'filters' => $request->only(['search'])]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // get permissions
        // $permissions = Permission::all();
        $data = Permission::orderBy('name')->pluck('name', 'id');
        $collection = collect($data);
        $permissions = $collection->groupBy(function ($item, $key) {
            // Memecah string menjadi array kata-kata
            $words = explode(' ', $item);

            // Mengambil kata pertama
            return $words[0];
        });
        // return $permissions;
        // render view
        return inertia('Roles/Create', ['permissions' => $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // validate request
         $request->validate([
            'name' => 'required|min:3|max:255|unique:roles',
            'selectedPermissions' => 'required|array|min:1',
        ]);

        // create new role data
        $role = Role::create(['name' => $request->name]);

        // give permissions to role
        $role->givePermissionTo($request->selectedPermissions);

        // render view
        return to_route('roles.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // get permissions
        $data = Permission::orderBy('name')->pluck('name', 'id');
        $collection = collect($data);
        $permissions = $collection->groupBy(function ($item, $key) {
            // Memecah string menjadi array kata-kata
            $words = explode(' ', $item);

            // Mengambil kata pertama
            return $words[0];
        });

        // load permissions
        $role->load('permissions');

        // render view
        return inertia('Roles/Edit', ['role' => $role, 'permissions' => $permissions]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // validate request
        $request->validate([
            'name' => 'required|min:3|max:255|unique:roles,name,'.$role->id,
            'selectedPermissions' => 'required|array|min:1',
        ]);

        // update role data
        $role->update(['name' => $request->name]);

        // give permissions to role
        $role->syncPermissions($request->selectedPermissions);

        // render view
        return to_route('roles.index');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // delete role data
        $role->delete();

        // render view
        return back();
    }
}