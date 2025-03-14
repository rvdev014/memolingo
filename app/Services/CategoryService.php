<?php

namespace App\Services;

use App\Models\User;
use App\Models\Category;

class CategoryService
{
    public function store(int $userId, array $validated): void
    {
        Category::query()->create(array_merge($validated, ['user_id' => $userId]));
    }

    public function delete(User $user, int $categoryId): void
    {
        $user->categories()->where('id', $categoryId)->delete();
    }
}
