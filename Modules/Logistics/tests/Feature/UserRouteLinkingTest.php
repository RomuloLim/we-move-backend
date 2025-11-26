<?php

namespace Modules\Logistics\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Modules\Logistics\Models\Route;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class UserRouteLinkingTest extends TestCase
{
    use RefreshDatabase;

    private function userActingAs(UserType $userType): void
    {
        $user = User::factory()->create(['user_type' => $userType->value]);

        Sanctum::actingAs($user);
    }

    public function test_admin_can_link_routes_to_user(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();
        $routes = Route::factory()->count(3)->create();
        $routeIds = $routes->pluck('id')->toArray();

        $response = $this->postJson('/api/v1/user-routes/link', [
            'user_id' => $user->id,
            'route_ids' => $routeIds,
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'Rotas vinculadas com sucesso.']);

        foreach ($routeIds as $routeId) {
            $this->assertDatabaseHas('user_routes', [
                'user_id' => $user->id,
                'route_id' => $routeId,
            ]);
        }
    }

    public function test_admin_can_unlink_routes_from_user(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();
        $routes = Route::factory()->count(3)->create();
        $routeIds = $routes->pluck('id')->toArray();

        // Link routes first
        foreach ($routeIds as $routeId) {
            DB::table('user_routes')->insert([
                'user_id' => $user->id,
                'route_id' => $routeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->deleteJson('/api/v1/user-routes/unlink', [
            'user_id' => $user->id,
            'route_ids' => $routeIds,
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'Rotas desvinculadas com sucesso.']);

        foreach ($routeIds as $routeId) {
            $this->assertDatabaseMissing('user_routes', [
                'user_id' => $user->id,
                'route_id' => $routeId,
            ]);
        }
    }

    public function test_admin_can_list_routes_linked_to_user(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();
        $routes = Route::factory()->count(3)->create();
        $routeIds = $routes->pluck('id')->toArray();

        // Link routes
        foreach ($routeIds as $routeId) {
            DB::table('user_routes')->insert([
                'user_id' => $user->id,
                'route_id' => $routeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->getJson("/api/v1/user-routes/user/{$user->id}");

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'route_name',
                        'description',
                        'linked_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_student_cannot_link_routes_to_user(): void
    {
        $this->userActingAs(UserType::Student);

        $user = User::factory()->create();
        $routes = Route::factory()->count(2)->create();
        $routeIds = $routes->pluck('id')->toArray();

        $response = $this->postJson('/api/v1/user-routes/link', [
            'user_id' => $user->id,
            'route_ids' => $routeIds,
        ]);

        $response->assertForbidden();
    }

    public function test_student_cannot_unlink_routes_from_user(): void
    {
        $this->userActingAs(UserType::Student);

        $user = User::factory()->create();
        $routes = Route::factory()->count(2)->create();
        $routeIds = $routes->pluck('id')->toArray();

        $response = $this->deleteJson('/api/v1/user-routes/unlink', [
            'user_id' => $user->id,
            'route_ids' => $routeIds,
        ]);

        $response->assertForbidden();
    }

    public function test_student_cannot_list_routes_linked_to_user(): void
    {
        $this->userActingAs(UserType::Student);

        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/user-routes/user/{$user->id}");

        $response->assertForbidden();
    }

    public function test_linking_same_route_twice_does_not_duplicate(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();
        $routes = Route::factory()->count(2)->create();
        $routeIds = $routes->pluck('id')->toArray();

        $this->postJson('/api/v1/user-routes/link', [
            'user_id' => $user->id,
            'route_ids' => $routeIds,
        ])->assertOk();

        $response = $this->postJson('/api/v1/user-routes/link', [
            'user_id' => $user->id,
            'route_ids' => $routeIds,
        ]);

        $response->assertOk();

        foreach ($routeIds as $routeId) {
            $count = DB::table('user_routes')
                ->where('user_id', $user->id)
                ->where('route_id', $routeId)
                ->count();

            $this->assertEquals(1, $count);
        }
    }

    public function test_linking_routes_requires_user_id(): void
    {
        $this->userActingAs(UserType::Admin);

        $routes = Route::factory()->count(2)->create();
        $routeIds = $routes->pluck('id')->toArray();

        $response = $this->postJson('/api/v1/user-routes/link', [
            'route_ids' => $routeIds,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id']);
    }

    public function test_linking_routes_requires_route_ids(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/user-routes/link', [
            'user_id' => $user->id,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['route_ids']);
    }

    public function test_linking_routes_requires_at_least_one_route(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/user-routes/link', [
            'user_id' => $user->id,
            'route_ids' => [],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['route_ids']);
    }

    public function test_linking_routes_validates_user_exists(): void
    {
        $this->userActingAs(UserType::Admin);

        $routes = Route::factory()->count(2)->create();
        $routeIds = $routes->pluck('id')->toArray();

        $response = $this->postJson('/api/v1/user-routes/link', [
            'user_id' => 99999,
            'route_ids' => $routeIds,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id']);
    }

    public function test_linking_routes_validates_routes_exist(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/user-routes/link', [
            'user_id' => $user->id,
            'route_ids' => [99999, 88888],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['route_ids.0', 'route_ids.1']);
    }

    public function test_unlinking_non_linked_routes_still_succeeds(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();
        $routes = Route::factory()->count(2)->create();
        $routeIds = $routes->pluck('id')->toArray();

        $response = $this->deleteJson('/api/v1/user-routes/unlink', [
            'user_id' => $user->id,
            'route_ids' => $routeIds,
        ]);

        $response->assertOk();
    }

    public function test_listing_routes_for_user_without_routes_returns_empty(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/user-routes/user/{$user->id}");

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_listing_routes_supports_pagination(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();
        $routes = Route::factory()->count(20)->create();
        $routeIds = $routes->pluck('id')->toArray();

        foreach ($routeIds as $routeId) {
            DB::table('user_routes')->insert([
                'user_id' => $user->id,
                'route_id' => $routeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->getJson("/api/v1/user-routes/user/{$user->id}?per_page=5");

        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data',
                'links',
                'meta' => [
                    'current_page',
                    'total',
                    'per_page',
                ],
            ]);
    }

    public function test_can_link_partial_routes_to_user_with_existing_links(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();
        $existingRoutes = Route::factory()->count(2)->create();
        $newRoutes = Route::factory()->count(2)->create();

        // Link existing routes
        foreach ($existingRoutes as $route) {
            DB::table('user_routes')->insert([
                'user_id' => $user->id,
                'route_id' => $route->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Link new routes
        $response = $this->postJson('/api/v1/user-routes/link', [
            'user_id' => $user->id,
            'route_ids' => $newRoutes->pluck('id')->toArray(),
        ]);

        $response->assertOk();

        $totalLinks = DB::table('user_routes')
            ->where('user_id', $user->id)
            ->count();

        $this->assertEquals(4, $totalLinks);
    }

    public function test_can_unlink_partial_routes_from_user(): void
    {
        $this->userActingAs(UserType::Admin);

        $user = User::factory()->create();
        $routes = Route::factory()->count(4)->create();

        // Link all routes
        foreach ($routes as $route) {
            DB::table('user_routes')->insert([
                'user_id' => $user->id,
                'route_id' => $route->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Unlink only 2 routes
        $routesToUnlink = $routes->take(2)->pluck('id')->toArray();

        $response = $this->deleteJson('/api/v1/user-routes/unlink', [
            'user_id' => $user->id,
            'route_ids' => $routesToUnlink,
        ]);

        $response->assertOk();

        $remainingLinks = DB::table('user_routes')
            ->where('user_id', $user->id)
            ->count();

        $this->assertEquals(2, $remainingLinks);
    }
}
