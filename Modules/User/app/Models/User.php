<?php

namespace Modules\User\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\User\Enums\UserType;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'cpf',
        'rg',
        'phone_contact',
        'profile_picture_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function newFactory()
    {
        return UserFactory::new();
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
            'password' => 'hashed',
            'user_type' => UserType::class,
        ];
    }

    /**
     * Verifica se o usuário pode criar outros usuários do tipo admin/driver.
     */
    public function canCreateAdminUsers(): bool
    {
        return $this->user_type->canCreateAdminUsers();
    }

    /**
     * Verifica se o usuário pode criar usuários.
     */
    public function canCreateUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Verifica se o usuário pode visualizar outros usuários.
     */
    public function canViewUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Verifica se é super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->user_type === UserType::SuperAdmin;
    }

    /**
     * Verifica se é admin ou super admin.
     */
    public function isAdmin(): bool
    {
        return in_array($this->user_type, [UserType::Admin, UserType::SuperAdmin]);
    }
}
