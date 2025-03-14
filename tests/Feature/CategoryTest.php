<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function test_store(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $response = $this->postJson(route('category.store'), $payload = $this->getPayload());
        $response->assertOk();
        $response->assertJson(['message' => 'success']);

        $this->checkDatabase($payload);
    }

    public function test_store_with_parent(): void
    {
        $parent = $this->user->categories()->create(['name' => 'Parent category']);

        $this->actingAs($this->user, 'sanctum');
        $response = $this->postJson(
            route('category.store'),
            $payload = array_merge(
                $this->getPayload(),
                ['parent_id' => $parent->id]
            )
        );
        $response->assertOk();
        $response->assertJson(['message' => 'success']);

        $this->checkDatabase($payload);
    }

    public function test_store_with_wrong_parent(): void
    {
        $anotherUser = $this->createUser();
        $wrongParent = $anotherUser->categories()->create(['name' => 'Parent category']);

        $this->actingAs($this->user, 'sanctum');
        $response = $this->postJson(
            route('category.store'),
            $payload = array_merge($this->getPayload(), [
                'parent_id' => $wrongParent->id
            ])
        );
        $response->assertJsonValidationErrorFor('parent_id');

        $this->assertDatabaseMissing('categories', ['name' => $payload['name']]);
    }

    public function test_delete(): void
    {
        $category = $this->user->categories()->create(['name' => 'Test category']);

        $this->actingAs($this->user, 'sanctum');
        $response = $this->deleteJson(route('category.delete', ['categoryId' => $category->id]));
        $response->assertOk();
        $response->assertJson(['message' => 'deleted']);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_delete_another_user_category(): void
    {
        $anotherUser = $this->createUser();
        $category = $anotherUser->categories()->create(['name' => 'Test category']);

        $this->actingAs($this->user, 'sanctum');
        $response = $this->deleteJson(route('category.delete', ['categoryId' => $category->id]));
        $this->assertMessage('Category not found', $response);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    protected function getPayload(): array
    {
        return [
            'name' => 'Test category',
            'description' => 'Test description',
            'is_favorite' => true,
            'parent_id' => null,
        ];
    }

    protected function checkDatabase(array $payload): void
    {
        $this->assertDatabaseHas('categories', [
            'name' => $payload['name'],
            'description' => $payload['description'],
            'is_favorite' => $payload['is_favorite'],
            'parent_id' => $payload['parent_id'],
        ]);
    }
}
