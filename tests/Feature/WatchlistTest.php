<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Movie;
use App\Models\WatchlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WatchlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_watchlist(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        WatchlistItem::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/watchlist');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'last_page',
                'per_page',
                'total',
            ]);
    }
    public function test_user_can_add_movie_to_watchlist(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $movie = Movie::factory()->create([
            'imdb_id' => 'tt0111161',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/watchlist', [
                'imdb_id' => 'tt0111161',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('watchlist_items', [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
        ]);
    }
    public function test_user_cannot_add_duplicate_movie(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $movie = Movie::factory()->create();

        WatchlistItem::factory()->create([
            'user_id' => $user->id,
            'movie_id' => $movie->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/watchlist', [
                'imdb_id' => $movie->imdb_id,
            ]);

        $response->assertStatus(409);
    }
    public function test_user_can_view_watchlist_item(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $item = WatchlistItem::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/watchlist/{$item->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'movie',
            ]);
    }
    public function test_user_cannot_view_other_users_item(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $token = $other->createToken('auth_token')->plainTextToken;

        $item = WatchlistItem::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/watchlist/{$item->id}");

        $response->assertStatus(404);
    }
    public function test_mark_as_watched_sets_watched_at(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $item = WatchlistItem::factory()->create([
            'user_id' => $user->id,
            'watched_at' => null,
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/watchlist/{$item->id}", [
                'status' => 'watched',
            ])
            ->assertStatus(200);

        $this->assertNotNull($item->fresh()->watched_at);
    }
    public function test_user_can_delete_watchlist_item(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $item = WatchlistItem::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/watchlist/{$item->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('watchlist_items', [
            'id' => $item->id,
        ]);
    }
}
