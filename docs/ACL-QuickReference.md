# ACL - Quick Reference

## Verificar Permissões

```php
use Modules\User\Enums\Permission;

$user = auth()->user();

// Uma permissão
$user->hasPermission(Permission::ViewUsers);

// Qualquer permissão (OR)
$user->hasAnyPermission([Permission::ViewUsers, Permission::CreateUsers]);

// Todas permissões (AND)
$user->hasAllPermissions([Permission::ViewUsers, Permission::CreateUsers]);

// Listar todas
$permissions = $user->getPermissions();
```

## Usar Policies

```php
// Em controllers
if ($user->can('viewAny', User::class)) {
    // ...
}

// Com parâmetros
if ($user->can('updateUserType', [$targetUser, $newType])) {
    // ...
}

// Em Form Requests
public function authorize(): bool
{
    return $this->user()->can('create', User::class);
}
```

## Middleware em Rotas

```php
// Uma permissão
Route::middleware(['permission:view-users'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});

// Múltiplas permissões (OR)
Route::middleware(['permission:view-users,create-users'])->get('/users', /*...*/);
```

## Permissões Disponíveis

| Permissão | SuperAdmin | Admin | Driver | Student |
|-----------|-----------|-------|--------|---------|
| view-users | ✅ | ✅ | ❌ | ❌ |
| create-users | ✅ | ✅ | ❌ | ❌ |
| update-users | ✅ | ✅ | ❌ | ❌ |
| delete-users | ✅ | ✅ | ❌ | ❌ |
| update-user-type | ✅ | ✅ | ❌ | ❌ |
| create-admin-users | ✅ | ✅ | ❌ | ❌ |
| create-super-admin | ✅ | ❌ | ❌ | ❌ |

## Regras Especiais

- ✅ Usuários podem ver/atualizar seu próprio perfil
- ❌ Usuários não podem alterar seu próprio tipo
- ❌ Super Admins não podem ter tipo alterado
- ❌ Super Admins não podem ser deletados
- ✅ Apenas Super Admins podem fazer force delete

## Ver Documentação Completa

[docs/ACL.md](./ACL.md)
