<?php

namespace Modules\User\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasOne};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Operation\Models\Student;
use Modules\User\Enums\{Permission, UserType};

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
        'gender',
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

    public function genderText(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => match ($attributes['gender']) {
                'M' => 'Masculino',
                'F' => 'Feminino',
                'O' => 'Outro',
                default => 'Não informado',
            },
        );
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

    /**
     * Verifica se o usuário tem uma permissão específica.
     */
    public function hasPermission(Permission $permission): bool
    {
        $userPermissions = Permission::forUserType($this->user_type);

        return in_array($permission, $userPermissions);
    }

    /**
     * Verifica se o usuário tem alguma das permissões especificadas.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o usuário tem todas as permissões especificadas.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retorna todas as permissões do usuário.
     */
    public function getPermissions(): array
    {
        return Permission::forUserType($this->user_type);
    }

    /**
     * Rotas vinculadas ao usuário (motorista).
     */
    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\Logistics\Models\Route::class, 'user_routes')
            ->withTimestamps();
    }

    /**
     * Viagens do motorista.
     */
    public function trips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\Modules\Logistics\Models\Trip::class, 'driver_id');
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }
}
