# ACL (Access Control List) - Sistema de Controle de Acesso

## Visão Geral

O sistema ACL implementado fornece um controle de acesso robusto e escalável para o we-move-backend. Ele é baseado nos recursos nativos de autorização do Laravel (Gates e Policies) e oferece controle granular sobre o que cada tipo de usuário pode fazer no sistema.

## Arquitetura

### 1. Permission Enum
Localização: `Modules/User/app/Enums/Permission.php`

Define todas as permissões disponíveis no sistema:

- `ViewUsers` - Visualizar usuários
- `CreateUsers` - Criar usuários
- `UpdateUsers` - Atualizar usuários
- `DeleteUsers` - Deletar usuários
- `UpdateUserType` - Atualizar tipo de usuário
- `CreateAdminUsers` - Criar usuários Admin/Driver
- `CreateSuperAdmin` - Criar Super Administrador

### 2. Mapeamento Permissão-Tipo de Usuário

Cada tipo de usuário tem um conjunto específico de permissões:

#### Super Admin
- Todas as permissões (7 permissões)
- Pode criar outros Super Admins

#### Admin
- 6 permissões (todas exceto CreateSuperAdmin)
- Pode gerenciar usuários, mas não pode criar Super Admins

#### Driver
- Nenhuma permissão de gerenciamento
- Acesso apenas às funcionalidades específicas de motorista

#### Student
- Nenhuma permissão de gerenciamento
- Acesso apenas às funcionalidades específicas de estudante

### 3. User Model - Métodos de Verificação

Localização: `Modules/User/app/Models/User.php`

```php
// Verifica se tem uma permissão específica
$user->hasPermission(Permission::ViewUsers);

// Verifica se tem alguma das permissões
$user->hasAnyPermission([Permission::ViewUsers, Permission::CreateUsers]);

// Verifica se tem todas as permissões
$user->hasAllPermissions([Permission::ViewUsers, Permission::CreateUsers]);

// Retorna todas as permissões do usuário
$permissions = $user->getPermissions();
```

### 4. UserPolicy

Localização: `Modules/User/app/Policies/UserPolicy.php`

Centraliza toda a lógica de autorização relacionada a usuários:

**Métodos disponíveis:**
- `viewAny(User $user)` - Pode listar usuários?
- `view(User $user, User $model)` - Pode ver um usuário específico?
- `create(User $user)` - Pode criar usuários?
- `createUserType(User $user, UserType $type)` - Pode criar usuário de um tipo específico?
- `update(User $user, User $model)` - Pode atualizar um usuário?
- `updateUserType(User $user, User $model, UserType $newType)` - Pode alterar tipo de usuário?
- `delete(User $user, User $model)` - Pode deletar um usuário?
- `restore(User $user, User $model)` - Pode restaurar um usuário?
- `forceDelete(User $user, User $model)` - Pode deletar permanentemente?

**Regras importantes:**
- Usuários podem ver e atualizar seu próprio perfil
- Usuários não podem alterar seu próprio tipo
- Super Admins não podem ter seu tipo alterado
- Super Admins não podem ser deletados
- Apenas Super Admins podem fazer force delete

### 5. Middleware CheckPermission

Localização: `Modules/User/app/Http/Middleware/CheckPermission.php`

Permite verificar permissões em nível de rota.

**Uso:**
```php
// Em routes/api.php
Route::middleware(['auth:sanctum', 'permission:view-users'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});

// Múltiplas permissões (OR - qualquer uma delas)
Route::middleware(['permission:view-users,create-users'])->group(function () {
    // ...
});
```

## Uso Prático

### Em Controllers

```php
use Modules\User\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Usando policy
        if (!$user->can('viewAny', User::class)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }
        
        // Seu código aqui
    }
    
    public function updateType(Request $request, int $userId)
    {
        $user = User::findOrFail($userId);
        $newType = UserType::from($request->input('user_type'));
        
        // Policy com parâmetros
        if (!$request->user()->can('updateUserType', [$user, $newType])) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }
        
        // Seu código aqui
    }
}
```

### Em Form Requests

```php
class CreateUserByAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $requestedType = UserType::from($this->input('user_type'));
        
        return $user && $user->can('createUserType', [User::class, $requestedType]);
    }
}
```

### Em Blade Views (se aplicável)

```blade
@can('viewAny', App\Models\User::class)
    <a href="{{ route('users.index') }}">Ver Usuários</a>
@endcan

@can('create', App\Models\User::class)
    <a href="{{ route('users.create') }}">Criar Usuário</a>
@endcan
```

### Verificação Direta de Permissões

```php
use Modules\User\Enums\Permission;

$user = auth()->user();

// Verificação simples
if ($user->hasPermission(Permission::ViewUsers)) {
    // Pode visualizar usuários
}

// Verificação múltipla (OR)
if ($user->hasAnyPermission([Permission::ViewUsers, Permission::CreateUsers])) {
    // Tem pelo menos uma das permissões
}

// Verificação múltipla (AND)
if ($user->hasAllPermissions([Permission::ViewUsers, Permission::CreateUsers])) {
    // Tem todas as permissões
}
```

## Testes

### Estrutura de Testes

1. **PermissionTest** - Testa o enum Permission
   - Valores das permissões
   - Mapeamento de permissões por tipo de usuário
   - Labels das permissões

2. **UserPermissionTest** - Testa métodos de permissão no User model
   - `hasPermission()`
   - `hasAnyPermission()`
   - `hasAllPermissions()`
   - `getPermissions()`

3. **UserPolicyTest** - Testa a UserPolicy
   - Todos os métodos de autorização
   - Casos extremos e regras especiais

### Executando Testes

```bash
# Todos os testes de ACL
php artisan test --testsuite=Modules --filter="Permission|UserPolicy"

# Testes específicos
php artisan test Modules/User/tests/Unit/PermissionTest.php
php artisan test Modules/User/tests/Unit/UserPermissionTest.php
php artisan test Modules/User/tests/Unit/UserPolicyTest.php
```

## Adicionando Novas Permissões

### Passo 1: Adicionar ao Permission Enum

```php
// Modules/User/app/Enums/Permission.php
enum Permission: string
{
    // ... permissões existentes
    case ManageVehicles = 'manage-vehicles';
}
```

### Passo 2: Atualizar Mapeamento

```php
public static function forUserType(UserType $userType): array
{
    return match ($userType) {
        UserType::SuperAdmin => [
            // ... permissões existentes
            self::ManageVehicles,
        ],
        UserType::Admin => [
            // ... permissões existentes
            self::ManageVehicles,
        ],
        UserType::Driver => [
            self::ManageVehicles, // Drivers podem gerenciar veículos
        ],
        UserType::Student => [],
    };
}
```

### Passo 3: Adicionar Label

```php
public function label(): string
{
    return match ($this) {
        // ... labels existentes
        self::ManageVehicles => 'Gerenciar veículos',
    };
}
```

### Passo 4: Criar ou Atualizar Policy

```php
// Modules/Vehicle/app/Policies/VehiclePolicy.php
class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ManageVehicles);
    }
    
    // ... outros métodos
}
```

### Passo 5: Adicionar Testes

```php
public function test_driver_can_manage_vehicles(): void
{
    $driver = User::factory()->create(['user_type' => UserType::Driver]);
    
    $this->assertTrue($driver->hasPermission(Permission::ManageVehicles));
}
```

## Boas Práticas

1. **Sempre use Policies** para lógica de autorização complexa
2. **Use middleware** para proteção em nível de rota
3. **Use Form Requests** para autorização em requisições
4. **Evite verificações inline** nos controllers quando possível
5. **Adicione testes** para cada nova permissão ou policy
6. **Documente** mudanças no sistema de permissões

## Troubleshooting

### Policy não está funcionando

1. Verifique se a policy está registrada no `UserServiceProvider`
2. Confirme que está usando o namespace correto do model
3. Limpe o cache: `php artisan optimize:clear`

### Middleware não está funcionando

1. Verifique se o middleware está registrado em `bootstrap/app.php`
2. Confirme que o alias está correto nas rotas
3. Verifique se o usuário está autenticado

### Permissão não está sendo concedida

1. Verifique o mapeamento em `Permission::forUserType()`
2. Confirme o tipo de usuário no banco de dados
3. Use `$user->getPermissions()` para debug

## Recursos Adicionais

- [Laravel Authorization](https://laravel.com/docs/authorization)
- [Laravel Policies](https://laravel.com/docs/authorization#creating-policies)
- [Laravel Gates](https://laravel.com/docs/authorization#gates)
- [Laravel Middleware](https://laravel.com/docs/middleware)
