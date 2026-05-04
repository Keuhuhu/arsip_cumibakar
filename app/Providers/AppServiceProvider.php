<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // Super Admin — bisa melakukan semua hal
        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // User biasa hanya bisa melihat & download
        // Semua gate di bawah ini otomatis return false untuk role 'user'
        // karena mereka tidak disebut di callback

        // Upload & buat dokumen: super_admin saja
        Gate::define('upload-dokumen', function (User $user) {
            return $user->isSuperAdmin();
        });

        // Edit dokumen: super_admin saja
        Gate::define('edit-dokumen', function (User $user) {
            return $user->isSuperAdmin();
        });

        // Hapus dokumen: super_admin saja
        Gate::define('delete-dokumen', function (User $user) {
            return $user->isSuperAdmin();
        });

        // Setujui / ubah status: super_admin saja
        Gate::define('approve-dokumen', function (User $user) {
            return $user->isSuperAdmin();
        });

        // Lihat log aktivitas: super_admin saja
        Gate::define('view-logs', function (User $user) {
            return $user->isSuperAdmin();
        });

        // Manajemen pengguna: super_admin saja
        Gate::define('manage-users', function (User $user) {
            return $user->isSuperAdmin();
        });

        // Backup & restore: super_admin saja
        Gate::define('manage-backup', function (User $user) {
            return $user->isSuperAdmin();
        });
    }
}