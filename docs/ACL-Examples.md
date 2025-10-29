# ACL System - Usage Examples

## Example 1: Check if user can view other users

```php
use Modules\User\Enums\Permission;

// Method 1: Using permissions directly
$user = auth()->user();
if ($user->hasPermission(Permission::ViewUsers)) {
    // User can view other users
    $users = User::paginate();
}

// Method 2: Using policy
if ($user->can('viewAny', User::class)) {
    // User can view other users
    $users = User::paginate();
}

// Method 3: Using middleware in routes
Route::middleware(['auth:sanctum', 'permission:view-users'])
    ->get('/api/v1/users', [UserController::class, 'index']);
```

## Example 2: Check if user can create a specific type of user

```php
use Modules\User\Enums\UserType;

$currentUser = auth()->user();
$newUserType = UserType::Admin;

// Using policy (recommended)
if ($currentUser->can('createUserType', [User::class, $newUserType])) {
    // Can create this type of user
    $user = User::create([
        'user_type' => $newUserType,
        // ... other fields
    ]);
}
```

## Example 3: Form Request with ACL

```php
namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;

class CreateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $requestedType = UserType::from($this->input('user_type'));
        
        // Check if user can create this type
        return $user && $user->can('createUserType', [User::class, $requestedType]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'user_type' => ['required', 'in:admin,driver'],
        ];
    }
}
```

## Example 4: Controller with ACL

```php
namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Modules\User\Models\User;
use Modules\User\Enums\UserType;

class UserController extends Controller
{
    public function updateUserType(Request $request, int $userId)
    {
        $currentUser = $request->user();
        $targetUser = User::findOrFail($userId);
        $newType = UserType::from($request->input('user_type'));
        
        // Check authorization using policy
        if (!$currentUser->can('updateUserType', [$targetUser, $newType])) {
            return response()->json([
                'message' => 'Acesso negado.'
            ], 403);
        }
        
        $targetUser->update(['user_type' => $newType]);
        
        return response()->json([
            'message' => 'Tipo de usuário atualizado com sucesso.',
            'data' => $targetUser
        ]);
    }
}
```

## Example 5: Multiple permissions check

```php
use Modules\User\Enums\Permission;

$user = auth()->user();

// Check if user has ANY of these permissions
if ($user->hasAnyPermission([Permission::ViewUsers, Permission::CreateUsers])) {
    // User has at least one of these permissions
}

// Check if user has ALL of these permissions
if ($user->hasAllPermissions([Permission::ViewUsers, Permission::CreateUsers])) {
    // User has all of these permissions
}
```

## Example 6: Route group with permission middleware

```php
// routes/api.php

use Modules\User\Http\Controllers\UserController;

// All routes in this group require 'view-users' permission
Route::middleware(['auth:sanctum', 'permission:view-users'])->group(function () {
    Route::get('/api/v1/users', [UserController::class, 'index']);
    Route::get('/api/v1/users/{id}', [UserController::class, 'show']);
});

// This route requires EITHER 'create-users' OR 'update-users' permission
Route::middleware(['auth:sanctum', 'permission:create-users,update-users'])
    ->post('/api/v1/users', [UserController::class, 'store']);
```

## Example 7: Checking permissions in API Resource

```php
namespace Modules\User\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Enums\Permission;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $currentUser = $request->user();
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'user_type' => $this->user_type,
            
            // Include sensitive data only if user has permission
            'permissions' => $currentUser->hasPermission(Permission::ViewUsers) 
                ? $this->getPermissions() 
                : null,
                
            // Show actions user can perform
            'can' => [
                'update' => $currentUser->can('update', $this->resource),
                'delete' => $currentUser->can('delete', $this->resource),
                'update_type' => $currentUser->can('updateUserType', [$this->resource, $this->user_type]),
            ],
        ];
    }
}
```

## Example 8: Testing ACL

```php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class UserAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users()
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        
        $this->actingAs($admin, 'sanctum');
        
        $response = $this->getJson('/api/v1/users');
        
        $response->assertStatus(200);
    }

    public function test_student_cannot_view_users()
    {
        $student = User::factory()->create(['user_type' => UserType::Student]);
        
        $this->actingAs($student, 'sanctum');
        
        $response = $this->getJson('/api/v1/users');
        
        $response->assertStatus(403);
    }
}
```

## Common Scenarios

### Scenario 1: Dashboard with different views per user type

```php
public function dashboard(Request $request)
{
    $user = $request->user();
    
    return match(true) {
        $user->hasPermission(Permission::ViewUsers) => view('admin.dashboard'),
        $user->user_type === UserType::Driver => view('driver.dashboard'),
        $user->user_type === UserType::Student => view('student.dashboard'),
        default => view('guest.dashboard'),
    };
}
```

### Scenario 2: API with different data based on permissions

```php
public function getStatistics(Request $request)
{
    $user = $request->user();
    $data = [];
    
    if ($user->hasPermission(Permission::ViewUsers)) {
        $data['total_users'] = User::count();
        $data['users_by_type'] = User::groupBy('user_type')
            ->selectRaw('user_type, count(*) as count')
            ->get();
    }
    
    // Everyone gets their own stats
    $data['my_stats'] = [
        'created_at' => $user->created_at,
        'last_login' => $user->last_login_at,
    ];
    
    return response()->json($data);
}
```

### Scenario 3: Conditional form fields

```blade
<form action="{{ route('users.store') }}" method="POST">
    @csrf
    
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    
    @can('createUserType', [App\Models\User::class, App\Enums\UserType::Admin])
        <select name="user_type">
            <option value="student">Student</option>
            <option value="driver">Driver</option>
            <option value="admin">Admin</option>
        </select>
    @else
        <input type="hidden" name="user_type" value="student">
    @endcan
    
    <button type="submit">Create User</button>
</form>
```
