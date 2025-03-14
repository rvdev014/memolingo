<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    public function store(Request $request): JsonResponse
    {
        $userId = auth('sanctum')->id();
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'is_favorite' => 'nullable|boolean',
            'parent_id' => 'nullable|integer|exists:categories,id,user_id,' . $userId,
        ]);
        $this->categoryService->store($userId, $validated);

        return response()->json(['message' => 'success']);
    }

    public function delete($categoryId): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail(auth('sanctum')->id());

        if ($user->categories()->where('id', $categoryId)->doesntExist()) {
            throw new BadRequestException('Category not found');
        }

        $this->categoryService->delete($user, $categoryId);

        return response()->json(['message' => 'deleted']);
    }
}
