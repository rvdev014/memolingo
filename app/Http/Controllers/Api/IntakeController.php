<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IngestedText;
use App\Models\MemolingoWord;
use App\Models\TextToken;
use App\Models\UserDictionary;
use App\Models\UserKnownLexicon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IntakeController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|min:10',
            'language_code' => 'string|in:en',
            'mode' => 'required|in:manual,auto',
            'keep_full' => 'boolean'
        ]);

        $user = Auth::user();
        $text = $request->input('text');
        $mode = $request->input('mode');
        $languageCode = $request->input('language_code', 'en');
        $keepFull = $request->input('keep_full', false);

        $ingestedText = IngestedText::create([
            'user_id' => $user->id,
            'text_excerpt' => substr($text, 0, 200),
            'full_text' => $keepFull ? $text : null,
            'keep_full' => $keepFull,
        ]);

        $tokens = $this->tokenizeText($text);
        $tokenModels = [];

        if ($mode === 'manual') {
            foreach ($tokens as $token) {
                $word = $this->findOrCreateWord($token, $languageCode);
                $tokenModel = TextToken::create([
                    'ingested_text_id' => $ingestedText->id,
                    'token_raw' => $token,
                    'word_id' => $word->id,
                    'is_unknown' => !$this->isWordKnown($user->id, $word->id)
                ]);
                $tokenModel->load('word');
                $tokenModels[] = $tokenModel;
            }

            return response()->json([
                'mode' => 'manual',
                'tokens' => $tokenModels,
                'ingested_text_id' => $ingestedText->id
            ]);
        } else {
            $unknownWords = [];
            foreach ($tokens as $token) {
                $word = $this->findOrCreateWord($token, $languageCode);
                TextToken::create([
                    'ingested_text_id' => $ingestedText->id,
                    'token_raw' => $token,
                    'word_id' => $word->id,
                    'is_unknown' => !$this->isWordKnown($user->id, $word->id)
                ]);

                if (!$this->isWordKnown($user->id, $word->id) && 
                    !$this->isWordInDictionary($user->id, $word->id)) {
                    $unknownWords[] = $word;
                }
            }

            $uniqueUnknownWords = collect($unknownWords)
                ->unique('id')
                ->values()
                ->toArray();

            return response()->json([
                'mode' => 'auto',
                'candidates' => $uniqueUnknownWords,
                'ingested_text_id' => $ingestedText->id
            ]);
        }
    }

    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'selected_word_ids' => 'required|array',
            'selected_word_ids.*' => 'integer|exists:memolingo_words,id'
        ]);

        $user = Auth::user();
        $selectedWordIds = $request->input('selected_word_ids');

        $lastIngestedText = IngestedText::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$lastIngestedText) {
            return response()->json(['error' => 'No recent text intake found'], 404);
        }

        $tokens = TextToken::where('ingested_text_id', $lastIngestedText->id)->get();

        DB::transaction(function () use ($user, $selectedWordIds, $tokens) {
            foreach ($tokens as $token) {
                if (in_array($token->word_id, $selectedWordIds)) {
                    UserDictionary::firstOrCreate([
                        'user_id' => $user->id,
                        'word_id' => $token->word_id,
                    ], [
                        'progress_percent' => 0,
                        'ease' => 2.50,
                        'interval_days' => 1,
                        'due_at' => now(),
                        'status' => 'active'
                    ]);
                } else {
                    UserKnownLexicon::firstOrCreate([
                        'user_id' => $user->id,
                        'word_id' => $token->word_id,
                    ], [
                        'added_at' => now()
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Words processed successfully']);
    }

    private function tokenizeText(string $text): array
    {
        $text = strtolower(preg_replace('/[^\w\s]/', '', $text));
        return array_filter(array_unique(explode(' ', $text)));
    }

    private function findOrCreateWord(string $token, string $languageCode): MemolingoWord
    {
        return MemolingoWord::firstOrCreate([
            'language_code' => $languageCode,
            'lemma' => $token,
        ], [
            'base_form' => $token,
        ]);
    }

    private function isWordKnown(int $userId, int $wordId): bool
    {
        return UserKnownLexicon::where('user_id', $userId)
            ->where('word_id', $wordId)
            ->exists();
    }

    private function isWordInDictionary(int $userId, int $wordId): bool
    {
        return UserDictionary::where('user_id', $userId)
            ->where('word_id', $wordId)
            ->exists();
    }
}