<?php

namespace Modules\Communication\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Modules\Communication\Enums\NoticeType;
use Modules\Communication\Events\NoticeCreated;
use Modules\Communication\Models\Notice;
use Modules\Logistics\Models\Route;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    use RefreshDatabase;

    private function userActingAs(UserType $userType): User
    {
        $user = User::factory()->create(['user_type' => $userType->value]);
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_admin_can_create_general_notice(): void
    {
        Event::fake();
        $user = $this->userActingAs(UserType::Admin);

        $data = [
            'title' => 'Aviso Geral Importante',
            'content' => 'Este é um aviso geral para todos os usuários.',
            'type' => NoticeType::General->value,
        ];

        $response = $this->postJson('/api/v1/notices', $data);

        $response->assertCreated()
            ->assertJsonFragment(['title' => $data['title']])
            ->assertJsonFragment(['type' => NoticeType::General->value]);

        $this->assertDatabaseHas('notices', [
            'author_user_id' => $user->id,
            'title' => $data['title'],
            'type' => NoticeType::General->value,
            'route_id' => null,
        ]);

        Event::assertDispatched(NoticeCreated::class);
    }

    public function test_admin_can_create_route_alert_for_multiple_routes(): void
    {
        Event::fake();
        $user = $this->userActingAs(UserType::Admin);

        $route1 = Route::factory()->create();
        $route2 = Route::factory()->create();

        $data = [
            'title' => 'Alerta de Rota',
            'content' => 'Alteração no trajeto.',
            'type' => NoticeType::RouteAlert->value,
            'route_ids' => [$route1->id, $route2->id],
        ];

        $response = $this->postJson('/api/v1/notices', $data);

        $response->assertCreated();

        $this->assertDatabaseHas('notices', [
            'author_user_id' => $user->id,
            'title' => $data['title'],
            'type' => NoticeType::RouteAlert->value,
            'route_id' => $route1->id,
        ]);

        $this->assertDatabaseHas('notices', [
            'author_user_id' => $user->id,
            'title' => $data['title'],
            'type' => NoticeType::RouteAlert->value,
            'route_id' => $route2->id,
        ]);

        Event::assertDispatched(NoticeCreated::class, 2);
    }

    public function test_route_alert_requires_route_ids(): void
    {
        $this->userActingAs(UserType::Admin);

        $data = [
            'title' => 'Alerta de Rota',
            'content' => 'Conteúdo do alerta.',
            'type' => NoticeType::RouteAlert->value,
        ];

        $response = $this->postJson('/api/v1/notices', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['route_ids']);
    }

    public function test_admin_can_delete_notice(): void
    {
        $user = $this->userActingAs(UserType::Admin);

        $notice = Notice::factory()->create(['author_user_id' => $user->id]);

        $response = $this->deleteJson('/api/v1/notices/' . $notice->id);

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Aviso removido com sucesso.']);

        $this->assertDatabaseMissing('notices', ['id' => $notice->id]);
    }

    public function test_cannot_delete_nonexistent_notice(): void
    {
        $this->userActingAs(UserType::Admin);

        $response = $this->deleteJson('/api/v1/notices/99999');

        $response->assertNotFound();
    }

    public function test_title_is_required(): void
    {
        $this->userActingAs(UserType::Admin);

        $data = [
            'content' => 'Conteúdo do aviso.',
            'type' => NoticeType::General->value,
        ];

        $response = $this->postJson('/api/v1/notices', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_content_is_required(): void
    {
        $this->userActingAs(UserType::Admin);

        $data = [
            'title' => 'Título do aviso',
            'type' => NoticeType::General->value,
        ];

        $response = $this->postJson('/api/v1/notices', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_type_is_required(): void
    {
        $this->userActingAs(UserType::Admin);

        $data = [
            'title' => 'Título do aviso',
            'content' => 'Conteúdo do aviso.',
        ];

        $response = $this->postJson('/api/v1/notices', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['type']);
    }

    public function test_can_list_all_notices(): void
    {
        $this->userActingAs(UserType::Admin);

        Notice::factory()->count(7)->create();

        $response = $this->getJson('/api/v1/notices');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'content', 'type', 'created_at'],
                ],
                'links',
                'meta' => ['current_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonCount(5, 'data');
    }

    public function test_can_list_notices_with_custom_per_page(): void
    {
        $this->userActingAs(UserType::Admin);

        Notice::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/notices?per_page=10');

        $response->assertOk()
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonCount(10, 'data');
    }

    public function test_can_filter_notices_by_route_ids(): void
    {
        $this->userActingAs(UserType::Admin);

        $route1 = Route::factory()->create();
        $route2 = Route::factory()->create();
        $route3 = Route::factory()->create();

        Notice::factory()->count(3)->create(['route_id' => $route1->id, 'type' => NoticeType::RouteAlert]);
        Notice::factory()->count(2)->create(['route_id' => $route2->id, 'type' => NoticeType::RouteAlert]);
        Notice::factory()->count(4)->create(['route_id' => $route3->id, 'type' => NoticeType::RouteAlert]);

        $response = $this->getJson('/api/v1/notices?route_ids[]=' . $route1->id . '&route_ids[]=' . $route2->id);

        $response->assertOk()
            ->assertJsonPath('meta.total', 5);
    }

    public function test_can_filter_notices_by_single_route(): void
    {
        $this->userActingAs(UserType::Admin);

        $route1 = Route::factory()->create();
        $route2 = Route::factory()->create();

        Notice::factory()->count(3)->create(['route_id' => $route1->id, 'type' => NoticeType::RouteAlert]);
        Notice::factory()->count(2)->create(['route_id' => $route2->id, 'type' => NoticeType::RouteAlert]);

        $response = $this->getJson('/api/v1/notices?route_ids[]=' . $route1->id);

        $response->assertOk()
            ->assertJsonPath('meta.total', 3);
    }

    public function test_list_returns_notices_ordered_by_latest(): void
    {
        $this->userActingAs(UserType::Admin);

        $oldNotice = Notice::factory()->create(['title' => 'Aviso Antigo', 'created_at' => now()->subDays(5)]);
        $newNotice = Notice::factory()->create(['title' => 'Aviso Novo', 'created_at' => now()]);

        $response = $this->getJson('/api/v1/notices');

        $response->assertOk();

        $data = $response->json('data');
        $this->assertEquals('Aviso Novo', $data[0]['title']);
    }

    public function test_can_get_unread_notices_for_user(): void
    {
        $user = $this->userActingAs(UserType::Student);

        $notice1 = Notice::factory()->create(['created_at' => now()->subDays(3)]);
        $notice2 = Notice::factory()->create(['created_at' => now()->subDays(2)]);
        $notice3 = Notice::factory()->create(['created_at' => now()->subDay()]);

        // Mark notice2 as read by the user
        $notice2->readByUsers()->attach($user->id, ['read_at' => now()]);

        $response = $this->getJson('/api/v1/notices/unread');

        $response->assertOk()
            ->assertJsonCount(2, 'data');

        $data = $response->json('data');
        // Should be ordered from oldest to newest
        $this->assertEquals($notice1->id, $data[0]['id']);
        $this->assertEquals($notice3->id, $data[1]['id']);
    }

    public function test_unread_notices_are_ordered_from_oldest_to_newest(): void
    {
        $user = $this->userActingAs(UserType::Student);

        $oldest = Notice::factory()->create(['title' => 'Mais Antiga', 'created_at' => now()->subDays(5)]);
        $middle = Notice::factory()->create(['title' => 'Intermediária', 'created_at' => now()->subDays(3)]);
        $newest = Notice::factory()->create(['title' => 'Mais Nova', 'created_at' => now()->subDay()]);

        $response = $this->getJson('/api/v1/notices/unread');

        $response->assertOk();

        $data = $response->json('data');
        $this->assertEquals('Mais Antiga', $data[0]['title']);
        $this->assertEquals('Intermediária', $data[1]['title']);
        $this->assertEquals('Mais Nova', $data[2]['title']);
    }

    public function test_unread_only_shows_notices_not_read_by_user(): void
    {
        $user = $this->userActingAs(UserType::Student);
        $otherUser = User::factory()->create(['user_type' => UserType::Student->value]);

        $notice1 = Notice::factory()->create();
        $notice2 = Notice::factory()->create();
        $notice3 = Notice::factory()->create();

        // Current user has read notice1
        $notice1->readByUsers()->attach($user->id, ['read_at' => now()]);

        // Another user has read notice2 (should not affect the result)
        $notice2->readByUsers()->attach($otherUser->id, ['read_at' => now()]);

        $response = $this->getJson('/api/v1/notices/unread');

        $response->assertOk()
            ->assertJsonCount(2, 'data');

        $data = $response->json('data');
        $returnedIds = array_column($data, 'id');

        $this->assertNotContains($notice1->id, $returnedIds);
        $this->assertContains($notice2->id, $returnedIds);
        $this->assertContains($notice3->id, $returnedIds);
    }

    public function test_unread_respects_pagination(): void
    {
        $user = $this->userActingAs(UserType::Student);

        Notice::factory()->count(10)->create();

        $response = $this->getJson('/api/v1/notices/unread?per_page=3');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.per_page', 3)
            ->assertJsonPath('meta.total', 10);
    }
}
