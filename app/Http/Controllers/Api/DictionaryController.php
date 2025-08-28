<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDictionary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DictionaryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = UserDictionary::where('user_id', $user->id)->with('word');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('q')) {
            $searchTerm = $request->input('q');
            $query->whereHas('word', function ($q) use ($searchTerm) {
                $q->where('lemma', 'like', "%{$searchTerm}%")
                  ->orWhere('base_form', 'like', "%{$searchTerm}%");
            });
        }

        $words = $query->orderBy('due_at', 'asc')
            ->paginate($request->input('per_page', 15));

        return response()->json($words);
    }

    public function startLearning(Request $request): JsonResponse
    {
        $user = Auth::user();
        $force = $request->query('force', false);
        
        $query = UserDictionary::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('word');

        if (!$force) {
            $query->where('due_at', '<=', now());
        }

        $cards = $query->limit(20)->get();

        return response()->json(['cards' => $cards]);
    }

    public function submitAnswer(Request $request): JsonResponse
    {
        $request->validate([
            'card_id' => 'required|integer|exists:user_dictionary,id',
            'answer' => 'required|in:bad,ok,mastered'
        ]);

        $user = Auth::user();
        $cardId = $request->input('card_id');
        $answer = $request->input('answer');

        $card = UserDictionary::where('id', $cardId)
            ->where('user_id', $user->id)
            ->first();

        if (!$card) {
            return response()->json(['error' => 'Card not found'], 404);
        }

        $card->updateSRS($answer);

        if ($answer === 'mastered') {
            $user->userKnownLexicon()->firstOrCreate([
                'word_id' => $card->word_id,
            ], [
                'added_at' => now()
            ]);
        }

        return response()->json(['message' => 'Answer submitted successfully']);
    }
}