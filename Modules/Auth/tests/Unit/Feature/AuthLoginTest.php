<?php

namespace Modules\Auth\Tests\Unit\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase as BaseTestCase;

class AuthLoginTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\UserSeeder']);
    }

    public function test_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'teste@exemplo.com',
            'password' => 'senha123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertEquals('Bearer', $response->json('data.token_type'));
    }

    public function test_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'teste@exemplo.com',
            'password' => 'senhaincorreta',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }

    public function test_login_validation_errors(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'email-invalido',
            'password' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_me_endpoint_with_authenticated_user(): void
    {
        $user = User::where('email', 'teste@exemplo.com')->first();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertEquals($user->id, $response->json('data.id'));
    }

    public function test_me_endpoint_without_authentication(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    public function test_logout_with_authenticated_user(): void
    {
        $user = User::where('email', 'teste@exemplo.com')->first();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout realizado com sucesso.',
            ]);
    }

    public function test_logout_without_authentication(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    public function test_logout_all_with_authenticated_user(): void
    {
        $user = User::where('email', 'teste@exemplo.com')->first();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/logout-all');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout de todos os dispositivos realizado com sucesso.',
            ]);
    }
}
