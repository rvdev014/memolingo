<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NormalizeController extends Controller
{
    public function normalize(Request $request): JsonResponse
    {
        $request->validate([
            'words' => 'required|array',
            'words.*' => 'string',
            'language_code' => 'string|in:en'
        ]);

        $words = $request->input('words');
        $languageCode = $request->input('language_code', 'en');
        
        $normalized = [];
        
        foreach ($words as $word) {
            $cacheKey = "normalize:{$languageCode}:{$word}";
            
            if (Cache::has($cacheKey)) {
                $normalized[$word] = Cache::get($cacheKey);
            } else {
                $lemma = $this->normalizeWord($word, $languageCode);
                Cache::put($cacheKey, $lemma, 60 * 60 * 24); // Cache for 24 hours
                $normalized[$word] = $lemma;
            }
        }

        return response()->json($normalized);
    }

    private function normalizeWord(string $word, string $languageCode): string
    {
        $word = strtolower(trim($word));
        
        if ($languageCode === 'en') {
            $irregularVerbs = [
                'was' => 'be', 'were' => 'be', 'been' => 'be',
                'had' => 'have', 'has' => 'have',
                'went' => 'go', 'gone' => 'go',
                'did' => 'do', 'done' => 'do',
                'said' => 'say',
                'came' => 'come',
                'took' => 'take', 'taken' => 'take',
                'got' => 'get', 'gotten' => 'get',
                'saw' => 'see', 'seen' => 'see',
                'made' => 'make',
                'knew' => 'know', 'known' => 'know',
                'thought' => 'think',
                'found' => 'find',
                'gave' => 'give', 'given' => 'give',
                'told' => 'tell',
                'became' => 'become',
                'left' => 'leave',
                'felt' => 'feel',
                'brought' => 'bring',
                'began' => 'begin', 'begun' => 'begin',
                'kept' => 'keep',
                'held' => 'hold',
                'wrote' => 'write', 'written' => 'write',
                'stood' => 'stand',
                'heard' => 'hear',
                'let' => 'let',
                'meant' => 'mean',
                'set' => 'set',
                'met' => 'meet',
                'ran' => 'run',
                'moved' => 'move',
                'lived' => 'live',
                'believed' => 'believe',
                'brought' => 'bring',
                'happened' => 'happen',
                'wrote' => 'write',
                'provided' => 'provide',
                'sat' => 'sit',
                'lost' => 'lose',
                'paid' => 'pay',
                'met' => 'meet',
                'included' => 'include',
                'continued' => 'continue',
                'set' => 'set',
                'learned' => 'learn',
                'changed' => 'change',
                'led' => 'lead',
                'understood' => 'understand',
                'watched' => 'watch',
                'followed' => 'follow',
                'stopped' => 'stop',
                'created' => 'create',
                'spoke' => 'speak', 'spoken' => 'speak',
                'read' => 'read',
                'allowed' => 'allow',
                'added' => 'add',
                'spent' => 'spend',
                'grew' => 'grow', 'grown' => 'grow',
                'opened' => 'open',
                'walked' => 'walk',
                'won' => 'win',
                'offered' => 'offer',
                'remembered' => 'remember',
                'loved' => 'love',
                'considered' => 'consider',
                'appeared' => 'appear',
                'bought' => 'buy',
                'waited' => 'wait',
                'served' => 'serve',
                'died' => 'die',
                'sent' => 'send',
                'built' => 'build',
                'stayed' => 'stay',
                'fell' => 'fall', 'fallen' => 'fall',
                'cut' => 'cut',
                'reached' => 'reach',
                'killed' => 'kill',
                'remained' => 'remain',
                'suggested' => 'suggest',
                'raised' => 'raise',
                'passed' => 'pass',
                'sold' => 'sell',
                'required' => 'require',
                'reported' => 'report',
                'decided' => 'decide',
                'pulled' => 'pull'
            ];

            if (isset($irregularVerbs[$word])) {
                return $irregularVerbs[$word];
            }

            if (preg_match('/ies$/', $word)) {
                return preg_replace('/ies$/', 'y', $word);
            }
            
            if (preg_match('/(s|es|ies|ed|ing)$/', $word)) {
                if (preg_match('/ed$/', $word)) {
                    return preg_replace('/ed$/', '', $word);
                }
                if (preg_match('/ing$/', $word)) {
                    $base = preg_replace('/ing$/', '', $word);
                    if (preg_match('/([bcdfghjklmnpqrstvwxyz])\1$/', $base)) {
                        $base = substr($base, 0, -1);
                    }
                    return $base;
                }
                if (preg_match('/es$/', $word)) {
                    return preg_replace('/es$/', '', $word);
                }
                if (preg_match('/s$/', $word)) {
                    return preg_replace('/s$/', '', $word);
                }
            }
        }
        
        return $word;
    }
}