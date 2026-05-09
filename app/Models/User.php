<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUPERVISOR = 'supervisor';
    public const ROLE_STAFF = 'staff';
    public const ROLE_VIEWER = 'viewer';

    /**
     * @return array<string, string>
     */
    public static function roleOptions(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_SUPERVISOR => 'Supervisor',
            self::ROLE_STAFF => 'Operator Gudang',
            self::ROLE_VIEWER => 'Viewer',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function roleDescriptions(): array
    {
        return [
            self::ROLE_ADMIN => 'Akses penuh termasuk user management.',
            self::ROLE_SUPERVISOR => 'Memantau stok dan menyetujui stock opname.',
            self::ROLE_STAFF => 'Menjalankan mutasi stok dan stock opname.',
            self::ROLE_VIEWER => 'Akses baca dashboard dan data operasional.',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canApproveStockOpnames(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPERVISOR], true);
    }

    public function canManageMasterData(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPERVISOR], true);
    }

    public function canOperateStock(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPERVISOR, self::ROLE_STAFF], true);
    }

    public function roleLabel(): string
    {
        return self::roleOptions()[$this->role] ?? str($this->role)->headline()->toString();
    }

    public function roleBadgeClass(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'bg-blue-100 text-blue-800',
            self::ROLE_SUPERVISOR => 'bg-emerald-100 text-emerald-800',
            self::ROLE_VIEWER => 'bg-amber-100 text-amber-800',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
