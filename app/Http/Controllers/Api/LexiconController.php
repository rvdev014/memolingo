<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDictionary;
use App\Models\UserKnownLexicon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LexiconController extends Controller
{
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        $knownTotal = UserKnownLexicon::where('user_id', $user->id)->count();
        $activeCards = UserDictionary::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();
        $masteredTotal = UserDictionary::where('user_id', $user->id)
            ->where('status', 'mastered')
            ->count();

        return response()->json([
            'known_total' => $knownTotal,
            'active_cards' => $activeCards,
            'mastered_total' => $masteredTotal,
        ]);
    }
}